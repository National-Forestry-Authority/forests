<?php

namespace Drupal\farm_nfa\Form;

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
    return [
        'log_type' => 'observation',
        'display_log_types' => ['observation'],
        'form_title' => t('Inventory'),
      ] + parent::defaultSettings();
  }

}
