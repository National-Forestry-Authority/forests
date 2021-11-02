<?php

namespace Drupal\farm_nfa_planting\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\farm_nfa\Form\ForestPlanBaseForm;

/**
 * Forest plan planting form.
 *
 * @ingroup farm_nfa_planting
 */
class ForestPlanPlantingForm extends ForestPlanBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_planting_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {
    return [
        'log_type' => 'planting',
        'form_title' => t('Planting'),
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    if (!$log->isNew() && isset($form['quantity'])) {
      foreach ($log->get('quantity')->referencedEntities() as $quantity) {
        if ($quantity->get('units')->entity->get('name')->value == 'trees') {
          $total_trees = $quantity->get('value')->value;
        }
        if ($quantity->get('units')->entity->get('name')->value == 'hectares') {
          $hectares = $quantity->get('value')->value;
        }
      }
      if ($total_trees && $hectares) {
        $form['seedlings_per_hectare'] = [
          '#type' => 'markup',
          '#markup' => $this->t('Number of seedlings per hectares: %value', ['%value' => round($total_trees / $hectares, 2)]),
          '#weight' => $form['quantity']['#weight'] + 1,
        ];
      }
    }

    // Only forests available to reference as assets.
    $form['asset']['widget']['actions']['bundle']['#options'] = ['forest' => $this->t('Forest')];
    $form['asset']['widget']['actions']['bundle']['#access'] = FALSE;

    $form['asset']['widget']['actions']['ief_add']['#access'] = empty(Element::children($form['asset']['widget']['entities']));

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    if ($log->bundle() == 'planting') {
      // Planting logs always are movements ones.
      // @see https://github.com/mstenta/farm_nfa/issues/92#issuecomment-956176947
      $log_values = $form_state->getValue('log');
      $log_values['is_movement'] = TRUE;
      $form_state->setValue('log', $log_values);
    }
  }

}
