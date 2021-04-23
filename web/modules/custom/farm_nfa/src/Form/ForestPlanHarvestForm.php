<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Forest plan harvest form.
 *
 * @ingroup farm_nfa
 */
class ForestPlanHarvestForm extends ForestPlanBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_harvest_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {
    return [
      'log_type' => 'harvest',
      'display_log_types' => ['harvest'],
      'form_title' => t('Harvest'),
    ] + parent::defaultSettings();
  }

}
