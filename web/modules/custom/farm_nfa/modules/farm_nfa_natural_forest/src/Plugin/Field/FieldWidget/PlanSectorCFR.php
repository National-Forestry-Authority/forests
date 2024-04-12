<?php

namespace Drupal\farm_nfa_natural_forest\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
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
class PlanSectorCFR extends OptionsButtonsWidget implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $entity = $items->getEntity();
    $args = [];
    if ($entity->hasField('sector') && !$entity->get('sector')->isEmpty()) {
      // Load the sector entity. Can't use $entity->sector->entity because it
      // is not always available with ajax callbacks.
      // @todo inject the entity type manager.
      $sector = \Drupal::entityTypeManager()->getStorage('asset')->load($entity->get('sector')->target_id);
      $args = [$sector->id()];
    }

    $user_input = $form_state->getUserInput();
    $triggering_element_name = $user_input['_triggering_element_name'] ?? NULL;
    if ($triggering_element_name == 'asset[sector][parent]') {
      $parts = explode('[', str_replace(']', '', $triggering_element_name));
      $parent_value = NestedArray::getValue($user_input, $parts);
      $parent_id = EntityAutocomplete::extractEntityIdFromAutocompleteInput($parent_value);
      $args = [$parent_id];
    }

    // @todo the views should be selectable in the widget settings.
    $widget['sector'] = [
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

    // When adding ajax with pre_render, the #title and #description are not
    // rendered on ajax refresh even if they're present on the render array.
    // This is a workaround that moves the #title and #description to #prefix.
    $widget['asset']['#prefix'] = '<div id="fmap-asset-wrapper">';
    $widget['asset']['#prefix'] .= '<legend class="fieldset__legend fieldset__legend--composite fieldset__legend--visible">
      <span class="fieldset__label fieldset__label--group js-form-required form-required">' . $widget['asset']['#title'] . '</span>
    </legend>';
    $widget['asset']['#prefix'] .= '<p class="fieldset__description">' . $widget['asset']['#description'] . '</p>';
    unset($widget['asset']['#title']);
    unset($widget['asset']['#description']);
    $widget['asset']['#suffix'] = '</div>';

    $options = [];
    // @todo the views should be selectable in the widget settings.
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
    if (!empty($options)) {
      $class = get_class($this);
      $widget['asset']['#pre_render'][] = [$class, 'preRenderOptions'];
      $widget['asset']['#nfa_disabled_options'] = $this->getAssetsInUse($args);
    }

    return $widget;
  }

  /**
   * Get the assets in use.
   *
   * @param array $args
   *   The arguments.
   *
   * @return array
   *   The assets in use.
   */
  protected function getAssetsInUse(array $args): array {
    // @todo the views should be selectable in the widget settings.
    $view = Views::getView('assets_in_use');
    if ($view instanceof ViewExecutable) {
      $view->setDisplay('default');
      $view->setArguments($args);
      $view->preExecute();
      $view->execute();
      return array_column($view->result, 'asset_field_data_id');
    }

    return [];
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

  /**
   * Pre-render callback for the form element.
   *
   * @param array $element
   *   The form element.
   */
  public static function preRenderOptions(array $element): array {
    foreach ($element['#nfa_disabled_options'] as $id) {
      if (!$element[$id]['#default_value']) {
        $element[$id]['#attributes']['disabled'] = 'disabled';
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRenderOptions'];
  }

}
