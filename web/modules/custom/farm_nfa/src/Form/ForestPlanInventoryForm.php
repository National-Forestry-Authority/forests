<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\plan\Entity\PlanInterface;

/**
 * Forest plan inventory form.
 *
 * @ingroup farm_nfa
 */
class ForestPlanInventoryForm extends ForestPlanBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_inventory_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {
    // @TODO This array depends heavily in the plan type, so we need to pass it
    // along or make these forms some sort of plugin based on the plan type.
    return [
        'log_type' => [
          'forest' => 'observation',
          'natural_forest' => 'observation',
          'plantation' => 'plantation_inventory',
        ],
        'display_log_types' => ['observation', 'plantation_inventory'],
        'form_title' => t('Inventory'),
      ] + parent::defaultSettings();
  }

    /**
   * {@inheritdoc}
   */
  public function saveTask(PlanInterface $plan, array $assets, array $values, $log = FALSE) {
    //@TODO inject this.
    $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $terms = $term_storage->loadByProperties([
      'vid' => 'working_circle',
      'name' => 'Production',
      'langcode' => 'en',
    ]);
    $term = reset($terms);
    $values['working_circle'] = [0 => ['target_id' => $term->id()]];
    return parent::saveTask($plan,$assets, $values, $log);
  }

}
