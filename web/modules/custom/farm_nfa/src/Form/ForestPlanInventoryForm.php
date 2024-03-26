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
    // @TODO This array depends heavily in the plan type, so we need to pass it
    // along or make these forms some sort of plugin based on the plan type.
    return [
        'log_type' => [
          'natural' => 'natural_inventory',
        ],
        'form_title' => t('Inventory'),
      ] + parent::defaultSettings();
  }

}
