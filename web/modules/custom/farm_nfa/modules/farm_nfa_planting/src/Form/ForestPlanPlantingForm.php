<?php

namespace Drupal\farm_nfa_planting\Form;

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

}
