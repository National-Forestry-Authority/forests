<?php

namespace Drupal\farm_nfa_cfr\Form;

/**
 * Forest CFR management form.
 *
 * @ingroup farm_nfa
 */
class ForestCfrManagementForm extends ForestCfrBaseForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_management_cfr_form';
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
