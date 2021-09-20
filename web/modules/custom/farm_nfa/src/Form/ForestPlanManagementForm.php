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
        'display_log_types' => ['activity'],
        'form_title' => t('Management'),
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form += parent::buildForm($form, $form_state);

    $default_value = '';
    $log_id = $this->request->query->get('log');
    if (!empty($log_id) && is_numeric($log_id)) {
      /** @var \Drupal\log\Entity\LogInterface $log */
      $log = Log::load($log_id);
      if ($log->get('compartment')->referencedEntities()) {
        $default_value = 'compartment';
      }
      if ($log->get('geometry')->value) {
        $default_value = 'map';
      }
      if ($log->get('file')->referencedEntities()) {
        $default_value = 'kml';
      }
    }
    $form['log']['location_widget'] = [
      '#type' => 'select',
      '#title' => $this->t('Location selector'),
      '#description' => $this->t('Input the location - the more specific and accurate the location, the better.'),
      '#options' => [
        'compartment' => $this->t('Compartments'),
        'kml' => $this->t('KML Upload'),
        'map' => $this->t('Draw a map'),
      ],
      '#default_value' => '',
    ];

    $form['log']['compartment']['#states'] = [
      'visible' => [
        [
          [':input[name="location_widget"]' => ['value' => 'compartment']],
        ],
      ],
    ];
    $form['log']['geometry']['#states'] = [
      'visible' => [
        [
          [':input[name="location_widget"]' => ['value' => 'map']],
        ],
      ],
    ];
    $form['log']['file']['#states'] = [
      'visible' => [
        [
          [':input[name="location_widget"]' => ['value' => 'kml']],
        ],
      ],
    ];
    return $form;
  }

}
