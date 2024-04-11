<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldWidget;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsWidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Program/activity & sub-activity field widget.
 *
 * @FieldWidget(
 *   id = "farm_nfa_subactivity",
 *   label = @Translation("NFA: Program/Activity"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class SubactivityWidget extends OptionsWidgetBase implements TrustedCallbackInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a SubactivityWidget object.
   *
   * @param string $plugin_id
   *   The plugin id.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param mixed $field_definition
   *   The field definition.
   * @param array $settings
   *   The settings array.
   * @param array $third_party_settings
   *   The third party settings array.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Get the selected sub-activity from the triggering element.
    $user_input = $form_state->getUserInput();
    $triggering_element_name = $user_input['_triggering_element_name'] ?? NULL;
    if ($triggering_element_name === 'sub_activity[0][activity]') {
      $parts = explode('[', str_replace(']', '', $triggering_element_name));
      $selected_activity = NestedArray::getValue($user_input, $parts);
    }
    else {
      // Get the stored sub-activity and calculate the select activity and
      // sub-activity.
      $log = $items->getEntity();
      if ($log->hasField('sub_activity')) {
        $subactivity = $log->sub_activity->value;
        if (!empty($subactivity)) {
          $parts = explode(':', $subactivity);
          $selected_activity = $parts[1];
        }
      }
    }

    // Load the activities from either the asset or the plan entity, depending
    // on which tab we are on.
    $entity = $form_state->getFormObject()->getEntity();

    $wrapper_id = 'subactivity-wrapper';
    $widget['activity'] = [
      '#type' => 'select',
      '#title' => $this->t('Activity'),
      '#options' => $this->getActivityOptions($entity),
      '#empty_option' => $this->t('- None -'),
      '#default_value' => $selected_activity ?? NULL,
      '#ajax' => [
        'callback' => [static::class, 'updateSubActivityOptions'],
        'wrapper' => $wrapper_id,
      ],
    ];
    $widget['sub_activity'] = parent::formElement($items, $delta, $element, $form, $form_state);
    $widget['sub_activity']['#type'] = 'select';
    $widget['sub_activity']['#empty_option'] = $this->t('- None -');
    $widget['sub_activity']['#limit_validation_errors'] = [];
    $widget['sub_activity']['#prefix'] = '<div id="' . $wrapper_id . '">';
    $widget['sub_activity']['#suffix'] = '</div>';


    $options = [];
    if (!empty($selected_activity)) {
      $options = $this->getSubActivityOptions($selected_activity, $entity);
      $widget['sub_activity']['#default_value'] = $subactivity ?? NULL;
    }
    $widget['sub_activity']['#options'] = $options;

    return $widget;
  }

  /**
   * Helper function to get activity options.
   */
  public function getActivityOptions(EntityInterface $entity) {
    $form_display_stub = $entity instanceof AssetInterface ? 'asset.cfr.' : 'plan.natural.';
    $form_display_id = $form_display_stub . 'default';
    $form_display = $this->entityTypeManager->getStorage('entity_form_display')->load($form_display_id);
    $field_groups = $form_display->getThirdPartySettings('field_group');

    $options = [];
    foreach ($field_groups as $field_group) {
      // @todo this should be configurable on the widget.
      if ($field_group['format_type'] == 'program_tab') {
        $programs = [];
        foreach ($field_group['children'] as $program) {
          $program_field = $this->entityTypeManager->getStorage('field_config')->load($form_display_stub . $program);
          $programs[$program] = $program_field->label();
        }
        $options[$field_group['label']] = $programs;
      }
    }

    return $options;
  }

  /**
   * Helper function to get sub-activity options.
   *
   * @param string $activity
   *   The selected activity.
   * @param \Drupal\asset\Entity\AssetInterface $asset
   *   The asset entity.
   *
   * @return array
   *   The sub-activity options.
   */
  public function getSubActivityOptions(string $activity, EntityInterface $entity): array {
    $sub_activities = [];

    if (!$entity->hasField($activity) || $entity->get($activity)->isEmpty()) {
      return [];
    }

    $summaries = $entity->get($activity)->getValue();
    foreach ($summaries as $delta => $summary) {
      // The option key has to uniquely identify the CFR, the sub-activity
      // and the sub-activity delta.
      $key = $entity->id() . ":$activity:$delta";
      $sub_activities[$key] = $summary['summary'];
      // @todo If we allow editors to re-order the sub-activities the delta would
      // refer to the wrong value. We have to disable re-ordering on the program
      // widget.
    }

    return $sub_activities;
  }

  /**
   * Ajax callback to update sub-activity options.
   */
  public static function updateSubActivityOptions(array &$form, FormStateInterface $form_state) {
    $parents = $form_state->getTriggeringElement()['#array_parents'];
    array_pop($parents);
    $parents[] = 'sub_activity';
    return NestedArray::getValue($form, $parents);
  }

  /**
   * {@inheritdoc}
   */
  public static function validateElement(array $element, FormStateInterface $form_state) {
    // When changing the activity, the existing sub-activity is no longer valid.
    // We have to clear the error so the form can be submitted.
    if ($form_state->hasAnyErrors()) {
      foreach ($form_state->getErrors() as $error) {
        if (preg_match('/The submitted value .* element is not allowed./', $error->__toString())) {
          $form_state->clearErrors();
          break;
        }
      }
    }

    if ($element['#required'] && $element['#value'] == '_none') {
      if (isset($element['#required_error'])) {
        $form_state->setError($element, $element['#required_error']);
      }
      else {
        $form_state->setError($element, new TranslatableMarkup('@name field is required.', ['@name' => $element['#title']]));
      }
    }

    // Massage submitted form values.
    // Drupal\Core\Field\WidgetBase::submit() expects values as
    // an array of values keyed by delta first, then by column, while our
    // widgets return the opposite.
    if (is_array($element['#value'])) {
      $values = array_values($element['#value']);
    }
    else {
      $values = [$element['#value']];
    }

    // Filter out the 'none' option. Use a strict comparison, because
    // 0 == 'any string'.
    $index = array_search('_none', $values, TRUE);
    if ($index !== FALSE) {
      unset($values[$index]);
    }

    // Transpose selections from field => delta to delta => field.
    $items = [];
    foreach ($values as $value) {
      $items[] = [$element['#key_column'] => $value];
    }
    $form_state->setValueForElement($element, $items);

    // Massage form values doesn't get called on this type of widget, so we
    // implement it manually.
    // Get the field name out of the #name of the element.
    $field_name = explode('[', $element['#name'])[0];
    $form_state->setValue($field_name, $items);
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRenderOptions'];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getName() == 'sub_activity';
  }

}
