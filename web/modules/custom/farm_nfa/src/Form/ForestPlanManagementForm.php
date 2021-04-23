<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\log\Entity\Log;

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
        'display_log_types' => ['activity', 'input'],
        'form_title' => t('Management'),
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $log_id = $this->request->query->get('log');
    if (!empty($log_id) && is_numeric($log_id)) {
      /** @var \Drupal\log\Entity\LogInterface $log */
      $log = Log::load($log_id);
    }
    $form += parent::buildForm($form, $form_state);
    $form['log']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Task type'),
      '#options' => [
        'activity' => $this->t('Activity'),
        'input' => $this->t('Input'),
      ],
      '#default_value' => !empty($log) ? $log->bundle() : '',
      '#disabled' => !empty($log),
      '#weight' => -99,
      '#parents' => ['log', 'type'],
    ];
    return $form;
  }

}
