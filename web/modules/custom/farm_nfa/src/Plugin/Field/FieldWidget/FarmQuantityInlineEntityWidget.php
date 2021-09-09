<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\inline_entity_form\Plugin\Field\FieldWidget\InlineEntityFormComplex;
use Drupal\inline_entity_form\Plugin\Field\FieldWidget\InlineEntityFormSimple;
use Drupal\taxonomy\TermInterface;

/**
 * Inline widget for quantity.
 *
 * @FieldWidget(
 *   id = "farm_nfa_inline_entity_form_quantity",
 *   label = @Translation("Quantity IEF widget"),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   multiple_values = true
 * )
 */
class FarmQuantityInlineEntityWidget extends InlineEntityFormComplex {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();
    $defaults += [
      'measure' => '',
      'units' => NULL,
    ];

    return $defaults;
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

    $element['measure'] = [
      '#type' => 'select',
      '#title' => $this->t('Measure.'),
      '#default_value' => $this->getSetting('measure'),
      '#options' => quantity_measure_options(),
    ];
    $units =  $this->getSetting('units');
    $default_value_units = NULL;
    if (!empty($units)) {
      $default_value_units = $this->entityTypeManager->getStorage('taxonomy_term')->load($units);
    }
    $element['units'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Units of the quantity.'),
      '#default_value' => $default_value_units,
      '#target_type' => 'taxonomy_term',
      '#selection_handler' => 'default:taxonomy_term',
      '#selection_settings' => ['target_bundles' => 'unit'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($this->getSetting('measure')) {
      $summary[] = $this->t('Measure: @measure', ['@measure' => $this->getSetting('measure')]);
    }
    if ($this->getSetting('units')) {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($this->getSetting('units'));
      if ($term instanceof TermInterface) {
        $summary[] = $this->t('Units: @units', ['@units' => $term->label()]);
      }
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  protected function getInlineEntityForm($operation, $bundle, $langcode, $delta, array $parents, EntityInterface $entity = NULL) {
    $element = parent::getInlineEntityForm($operation, $bundle, $langcode, $delta, $parents, $entity);

    if ($this->getSetting('measure')) {
      $element['#default_measure'] = $this->getSetting('measure');
    }
    if ($this->getSetting('units')) {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($this->getSetting('units'));
      if ($term instanceof TermInterface) {
        $element['#default_unit'] = $term;
      }
    }

    $element['#widget_type'] = $this->pluginId;

    return $element;
  }

}
