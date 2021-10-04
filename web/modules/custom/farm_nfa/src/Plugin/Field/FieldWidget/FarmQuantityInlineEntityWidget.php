<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Plugin\Field\FieldWidget\InlineEntityFormComplex;
use Drupal\quantity\Entity\Quantity;
use Drupal\quantity\Entity\QuantityInterface;

/**
 * Inline widget for quantity.
 *
 * @FieldWidget(
 *   id = "farm_nfa_inline_entity_form_quantity",
 *   label = @Translation("Quantity IEF widget"),
 *   field_types = {
 *     "entity_reference",
 *     "entity_reference_revisions",
 *   },
 *   multiple_values = true
 * )
 */
class FarmQuantityInlineEntityWidget extends InlineEntityFormComplex {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $parent_settings = parent::defaultSettings();
    unset($parent_settings['revision']);
    unset($parent_settings['collapsible']);
    unset($parent_settings['collapsed']);
    unset($parent_settings['allow_duplicate']);
    return [
        'quantity' => [],
      ] + $parent_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $element['allow_new']['#access'] = FALSE;
    $element['allow_existing']['#access'] = FALSE;
    $element['match_operator']['#access'] = FALSE;
    $element['form_mode']['#access'] = FALSE;
    $element['revision']['#access'] = FALSE;
    $element['collapsible']['#access'] = FALSE;
    $element['allow_duplicate']['#access'] = FALSE;
    $element['override_labels']['#access'] = FALSE;
    $element['collapsed']['#access'] = FALSE;
    $element['label_singular']['#access'] = FALSE;
    $element['label_plural']['#access'] = FALSE;

    $quantity_settings =  $this->getSetting('quantity');
    foreach ($quantity_settings as $delta => $quantity_setting) {
      if (!empty($quantity_setting['units'])) {
        $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($quantity_setting['units']);
        if ($term) {
          $quantity_settings[$delta]['units'] = $term;
        }
      }
    }

    // @TODO Should we be disabling editing existing config if there's any
    // quantity that references this bundle?
    $element['quantity'] = [
      '#type' => 'element_multiple',
      '#title' => $this->t('Quantity'),
      '#add_more_input' => FALSE,
      '#add_more_button_label' => $this->t('Add more'),
      '#min_items' => 0,
      '#empty_items' => 0,
      '#element' => [
        'label' => [
          '#type' => 'textfield',
          '#title' => $this->t('Label'),
        ],
        'measure' => [
          '#type' => 'select',
          '#title' => $this->t('Measure.'),
          '#options' => quantity_measure_options(),
        ],
        'units' => [
          '#type' => 'entity_autocomplete',
          '#title' => $this->t('Units of the quantity.'),
          '#target_type' => 'taxonomy_term',
          '#selection_settings' => ['target_bundles' => ['unit']],
        ],
      ],
      '#default_value' => $quantity_settings,
    ];


    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $quantity_settings =  $this->getSetting('quantity');
    foreach ($quantity_settings as $quantity_setting) {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($quantity_setting['units']);
      if ($term) {
        $summary[] = $this->t('@label: Measure @measure, Units: @units',
          [
            '@label' => $quantity_setting['label'],
            '@measure' => $quantity_setting['measure'],
            '@units' => $term->label(),
          ]
        );
      }
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function getInlineEntityForm($operation, $bundle, $langcode, $delta, array $parents, EntityInterface $entity = NULL) {
    $element = parent::getInlineEntityForm($operation, $bundle, $langcode, $delta, $parents, $entity);

    if ($this->getSetting('quantity')) {
      $element['#$default_quantity'] = $this->getSetting('quantity');
    }
    $element['#widget_type'] = $this->pluginId;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $settings = $this->getSettings();
    $target_type = $this->getFieldSetting('target_type');
    // Get the entity type labels for the UI strings.
    $labels = $this->getEntityTypeLabels();

    // Build a parents array for this element's values in the form.
    $parents = array_merge($element['#field_parents'], [
      $items->getName(),
      'form',
    ]);

    // Assign a unique identifier to each IEF widget.
    // Since $parents can get quite long, hashing ensures that every id has
    // a consistent and relatively short length while maintaining uniqueness.
    $this->setIefId($this->makeIefId($parents));

    // Get the langcode of the parent entity.
    $parent_langcode = $items->getEntity()->language()->getId();

    // Determine the wrapper ID for the entire element.
    $wrapper = 'inline-entity-form-' . $this->getIefId();

    $element = [
        '#type' => $this->getSetting('collapsible') ? 'details' : 'fieldset',
        '#tree' => TRUE,
        '#description' => $this->fieldDefinition->getDescription(),
        '#prefix' => '<div id="' . $wrapper . '">',
        '#suffix' => '</div>',
        '#ief_id' => $this->getIefId(),
        '#ief_root' => TRUE,
        '#translating' => $this->isTranslating($form_state),
        '#field_title' => $this->fieldDefinition->getLabel(),
        '#after_build' => [
          [get_class($this), 'removeTranslatabilityClue'],
        ],
      ] + $element;
    if ($element['#type'] == 'details') {
      // If there's user input, keep the details open. Otherwise, use settings.
      $element['#open'] = $form_state->getUserInput() ?: !$this->getSetting('collapsed');
    }

    $this->prepareFormState($form_state, $items, $element['#translating']);
    $entities = $form_state->get(['inline_entity_form', $this->getIefId(), 'entities']);
    /** @var \Drupal\Core\Entity\EntityInterface $parent_entity */
    $parent_entity = $items->getParent()->getValue();
    $entities = $this->mergeQuantityEntities($entities, $parent_entity);
    $form_state->set(['inline_entity_form', $this->getIefId(), 'entities'], $entities);

    // Prepare cardinality information.
    $entities_count = count($entities);
    $cardinality = count($this->getSetting('quantity'));
    $cardinality_reached = ($cardinality > 0 && $entities_count == $cardinality);

    // Build the "Multiple value" widget.
    // TODO - does this belong in #element_validate?
    $element['#element_validate'][] = [get_class($this), 'updateRowWeights'];
    // Add the required element marker & validation.
    if ($element['#required']) {
      $element['#element_validate'][] = [get_class($this), 'requiredField'];
    }

    $element['entities'] = [
      '#tree' => TRUE,
      '#theme' => 'inline_entity_form_entity_table',
      '#entity_type' => $target_type,
    ];

    // Get the fields that should be displayed in the table.
    $target_bundles = $this->getTargetBundles();
    $fields = $this->inlineFormHandler->getTableFields($target_bundles);
    $context = [
      'parent_entity_type' => $this->fieldDefinition->getTargetEntityTypeId(),
      'parent_bundle' => $this->fieldDefinition->getTargetBundle(),
      'field_name' => $this->fieldDefinition->getName(),
      'entity_type' => $target_type,
      'allowed_bundles' => $target_bundles,
    ];
    $this->moduleHandler->alter('inline_entity_form_table_fields', $fields, $context);
    $element['entities']['#table_fields'] = $fields;

    $weight_delta = max(ceil($entities_count * 1.2), 50);
    foreach ($entities as $key => $value) {
      // Data used by inline-entity-form-entity-table.html.twig.
      // @see template_preprocess_inline_entity_form_entity_table()
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      $entity = $value['entity'];
      $element['entities'][$key]['#label'] = $entity->get('label')->value;
      $element['entities'][$key]['#entity'] = $entity;
      $element['entities'][$key]['#needs_save'] = $value['needs_save'];

      // Handle row weights.
      $element['entities'][$key]['#weight'] = $value['weight'];

      // First check to see if this entity should be displayed as a form.
      if (!empty($value['form'])) {
        $element['entities'][$key]['title'] = [];
        $element['entities'][$key]['delta'] = [
          '#type' => 'value',
          '#value' => $value['weight'],
        ];

        // Add the appropriate form.
        if (in_array($value['form'], ['edit', 'duplicate'])) {
          $element['entities'][$key]['form'] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['ief-form', 'ief-form-row']],
            'inline_entity_form' => $this->getInlineEntityForm(
              $value['form'],
              $entity->bundle(),
              $parent_langcode,
              $key,
              array_merge($parents, ['inline_entity_form', 'entities', $key, 'form']),
              $value['form'] == 'edit' ? $entity : $entity->createDuplicate()
            ),
          ];

          $element['entities'][$key]['form']['inline_entity_form']['#process'] = [
            ['\Drupal\inline_entity_form\Element\InlineEntityForm', 'processEntityForm'],
            [get_class($this), 'addIefSubmitCallbacks'],
            [get_class($this), 'buildEntityFormActions'],
          ];
        }
      }
      else {
        $row = &$element['entities'][$key];
        $row['title'] = [];
        $row['delta'] = [
          '#type' => 'weight',
          '#delta' => $weight_delta,
          '#default_value' => $value['weight'],
          '#attributes' => ['class' => ['ief-entity-delta']],
        ];
        // Add an actions container with edit and delete buttons for the entity.
        $row['actions'] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['ief-entity-operations']],
        ];

        // Make sure entity_access is not checked for unsaved entities.
        $entity_id = $entity->id();
        if (empty($entity_id) || $entity->access('update')) {
          $row['actions']['ief_entity_edit'] = [
            '#type' => 'submit',
            '#value' => (empty($entity_id) || strlen($entity->get('value')->value) === 0) ? $this->t('Add') : $this->t('Edit'),
            '#name' => 'ief-' . $this->getIefId() . '-entity-edit-' . $key,
            '#limit_validation_errors' => [],
            '#ajax' => [
              'callback' => 'inline_entity_form_get_element',
              'wrapper' => $wrapper,
            ],
            '#submit' => ['inline_entity_form_open_row_form'],
            '#ief_row_delta' => $key,
            '#ief_row_form' => 'edit',
          ];
        }
      }
    }

    // When in translation, the widget only supports editing (translating)
    // already added entities, so there's no need to show the rest.
    if ($element['#translating']) {
      if (empty($entities)) {
        // There are no entities available for translation, hide the widget.
        $element['#access'] = FALSE;
      }
      return $element;
    }

    if ($cardinality > 1) {
      // Add a visual cue of cardinality count.
      $message = $this->t('You have added @entities_count out of @cardinality_count allowed @label.', [
        '@entities_count' => $entities_count,
        '@cardinality_count' => $cardinality,
        '@label' => $labels['plural'],
      ]);
      $element['cardinality_count'] = [
        '#markup' => '<div class="ief-cardinality-count">' . $message . '</div>',
      ];
    }
    // Do not return the rest of the form if cardinality count has been reached.
    if ($cardinality_reached) {
      return $element;
    }

    $create_bundles = $this->getCreateBundles();
    $create_bundles_count = count($create_bundles);
    $allow_new = $settings['allow_new'] && !empty($create_bundles);
    $hide_cancel = FALSE;
    // If the field is required and empty try to open one of the forms.
    if (empty($entities) && $this->fieldDefinition->isRequired()) {
      if ($settings['allow_existing'] && !$allow_new) {
        $form_state->set(['inline_entity_form', $this->getIefId(), 'form'], 'ief_add_existing');
        $hide_cancel = TRUE;
      }
      elseif ($create_bundles_count == 1 && $allow_new && !$settings['allow_existing']) {
        $bundle = reset($target_bundles);

        // The parent entity type and bundle must not be the same as the inline
        // entity type and bundle, to prevent recursion.
        $parent_entity_type = $this->fieldDefinition->getTargetEntityTypeId();
        $parent_bundle = $this->fieldDefinition->getTargetBundle();
        if ($parent_entity_type != $target_type || $parent_bundle != $bundle) {
          $form_state->set(['inline_entity_form', $this->getIefId(), 'form'], 'add');
          $form_state->set(['inline_entity_form', $this->getIefId(), 'form settings'], [
            'bundle' => $bundle,
          ]);
          $hide_cancel = TRUE;
        }
      }
    }

    // If no form is open, show buttons that open one.
    $open_form = $form_state->get(['inline_entity_form', $this->getIefId(), 'form']);

    if (empty($open_form)) {
      $element['actions'] = [
        '#attributes' => ['class' => ['container-inline']],
        '#type' => 'container',
        '#weight' => 100,
      ];

      // The user is allowed to create an entity of at least one bundle.
      if ($allow_new) {
        // Let the user select the bundle, if multiple are available.
        if ($create_bundles_count > 1) {
          $bundles = [];
          foreach ($this->entityTypeBundleInfo->getBundleInfo($target_type) as $bundle_name => $bundle_info) {
            if (in_array($bundle_name, $create_bundles)) {
              $bundles[$bundle_name] = $bundle_info['label'];
            }
          }
          asort($bundles);

          $element['actions']['bundle'] = [
            '#type' => 'select',
            '#options' => $bundles,
          ];
        }
        else {
          $element['actions']['bundle'] = [
            '#type' => 'value',
            '#value' => reset($create_bundles),
          ];
        }
      }
    }
    else {
      // Make a delta key bigger than all existing ones, without assuming that
      // the keys are strictly consecutive.
      $new_key = $entities ? max(array_keys($entities)) + 1 : 0;
      // There's a form open, show it.
      if ($form_state->get(['inline_entity_form', $this->getIefId(), 'form']) == 'add') {
        $element['form'] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => ['ief-form', 'ief-form-bottom']],
          'inline_entity_form' => $this->getInlineEntityForm(
            'add',
            $this->determineBundle($form_state),
            $parent_langcode,
            $new_key,
            array_merge($parents, [$new_key])
          ),
        ];
        $element['form']['inline_entity_form']['#process'] = [
          ['\Drupal\inline_entity_form\Element\InlineEntityForm', 'processEntityForm'],
          [get_class($this), 'addIefSubmitCallbacks'],
          [get_class($this), 'buildEntityFormActions'],
        ];
      }

      // Pre-opened forms can't be closed in order to force the user to
      // add / reference an entity.
      if ($hide_cancel) {
        if ($open_form == 'add') {
          $process_element = &$element['form']['inline_entity_form'];
        }
        $process_element['#process'][] = [get_class($this), 'hideCancel'];
      }
    }

    return $element;
  }

  /**
   * Merges existing entities with the default quantities from configuration.
   *
   * @param \Drupal\quantity\Entity\QuantityInterface[] $entities
   *   Array of existing quantities.
   * @param \Drupal\Core\Entity\EntityInterface $parent_entity
   *   Parent entity.
   *
   * @return \Drupal\quantity\Entity\QuantityInterface[]
   *   Array of entities.
   */
  protected function mergeQuantityEntities(array $entities, EntityInterface $parent_entity) {
    $create_bundles = $this->getCreateBundles();
    $bundle = reset($create_bundles);
    foreach ($this->getSetting('quantity') as $delta => $quantity) {
      $qty_entity = Quantity::create(['type' => $bundle] + $quantity);
      $default_entities[] = [
        'entity' => $qty_entity,
        'needs_save' => TRUE,
        'weight' => 0,
      ];
    }
    // If the parent is new OR the entities are empty, return the default ones
    // as there's nothing to merge.
    if ($parent_entity->isNew() || empty($entities)) {
      return $entities + $default_entities;
    }

    $max_weight = max(array_column($default_entities, 'weight'));
    // For the edit worflow, add the new quantities at the end of the table.
    foreach ($default_entities as $default_entity) {
      if (!$this->isDefaultQuantityInExistingQuantities($default_entity, $entities)) {
        $default_entity['weight'] = $max_weight;
        $entities[] = $default_entity;
        $max_weight++;
      }
    }

    return $entities;
  }

  /**
   * Best guess to see if the default quantity is already in the existing ones.
   *
   * @param array $default_entity
   *   Array in the IEF structure that includes the default quantity entity.
   * @param array $entities
   *  Array in the IEF structure that includes the existing quantity entities.
   *
   * @return bool
   *   TRUE if the quantity is present in the existing ones, FALSE, otherwise.
   */
  protected function isDefaultQuantityInExistingQuantities(array $default_entity, array $entities) {
    foreach ($entities as $entity) {
      if (!empty($default_entity['entity']) && !empty($entity['entity']) &&
          $default_entity['entity'] instanceof QuantityInterface &&
          $entity['entity'] instanceof QuantityInterface &&
          $default_entity['entity']->get('label')->value === $entity['entity']->get('label')->value &&
          $default_entity['entity']->get('measure')->value === $entity['entity']->get('measure')->value &&
          $default_entity['entity']->get('units')->value === $entity['entity']->get('units')->value) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    if (!in_array($field_definition->getType(),['entity_reference', 'entity_reference_revisions'])) {
      return FALSE;
    }
    $settings = $field_definition->getItemDefinition()->getSettings();
    if ($settings['target_type'] == 'quantity') {
      return TRUE;
    }
    return FALSE;
  }

}
