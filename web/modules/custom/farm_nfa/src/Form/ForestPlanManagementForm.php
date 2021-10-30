<?php

namespace Drupal\farm_nfa\Form;

/**
 * Forest plan management form.
 *
 * @ingroup farm_nfa
 */
class ForestPlanManagementForm extends ForestPlanBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_management_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {
    return [
        'log_type' => 'activity',
        'form_title' => t('Management'),
      ] + parent::defaultSettings();
  }

}
