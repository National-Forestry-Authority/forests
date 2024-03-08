<?php

namespace Drupal\farm_nfa_natural_forest\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Displays the Plan Sector CFR widget.
 *
 * @FieldWidget(
 *   id = "farm_nfa_natural_forest_plan_sector_cfr",
 *   label = @Translation("NFA: Plan Sector CFR"),
 *   field_types = {
 *     "entity_reference"
 *   },
 *  multiple_values = TRUE,
 * )
 */
class PlanSectorCFR extends OptionsButtonsWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $entity = $items->getEntity();
    $args = [];
    if ($entity->hasField('sector') && !$entity->get('sector')->isEmpty()) {
      $sector = $entity->get('sector')->entity;
      $args = [$sector->id()];
    }
    $user_input = $form_state->getUserInput();
    $triggering_element_name = $user_input['_triggering_element_name'] ?? NULL;
    if ($triggering_element_name) {
      $parts = explode('[', str_replace(']', '', $triggering_element_name));
      $parent_value = NestedArray::getValue($user_input, $parts);
      $parent_id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($parent_value);
      $args = [$parent_id];
    }

    // @TODO the views should be selectable in the widget settings.
    $widget = [
      'parent' => [
        '#title' => $this->t('Sector'),
        '#type' => 'entity_autocomplete',
        '#target_type' => 'asset',
        '#selection_handler' => 'views',
        '#selection_settings' => [
          'view' => [
            'view_name' => 'entity_reference_sectors',
            'display_name' => 'entity_reference',
            'arguments' => [],
          ],
          'match_operator' => 'CONTAINS',
        ],
        '#default_value' => $sector ?? NULL,
        '#disabled' => !$entity->isNew(),
        '#required' => TRUE,
        '#ajax' => [
          'callback' => [static::class, 'updateAsset'],
          'wrapper' => 'fmap-asset-wrapper',
          'event' => 'autocompleteclose',
        ],
      ],
    ];
    $widget['asset'] = parent::formElement($items, $delta, $element, $form, $form_state);
    $widget['asset']['#prefix'] = '<div id="fmap-asset-wrapper">';
    $widget['asset']['#suffix'] = '</div>';

    $options = [];
    $view = Views::getView('entity_reference_cfr_by_sector');
    if ($view instanceof ViewExecutable) {
      $view->setDisplay('entity_reference');
      $view->setArguments($args);
      $view->preExecute();
      $view->execute();
      foreach ($view->result as $result) {
        $options[$result->id] = $result->asset_field_data_name;
      }
    }
    $widget['asset']['#options'] = $options;
    return $widget;
  }

  /**
   * Ajax callback to update the asset field.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The asset field.
   */
  public static function updateAsset(array &$form, FormStateInterface $form_state) {
    return $form['asset']['widget']['asset'];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return $values['asset'] ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getSetting('target_type') == 'asset';
  }

}
