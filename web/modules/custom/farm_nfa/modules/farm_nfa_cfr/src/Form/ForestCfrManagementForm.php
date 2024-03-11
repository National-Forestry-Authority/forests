<?php

namespace Drupal\farm_nfa_cfr\Form;

use Drupal\Core\Form\FormStateInterface;

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

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // @todo Add the program/activity dropdowns.
    $form['cfr']['#ajax'] = [
      'wrapper' => 'activity-select-wrapper',
      'callback' => '::activityOptions',
      'event' => 'autocompleteclose change',
    ];
    $form['activity'] = [
      '#type' => 'select',
      '#title' => $this->t('Program / Activity'),
      '#weight' => 1,
      '#prefix' => '<div id="activity-select-wrapper">',
      '#suffix' => '</div>',
//      '#states' => [
//        'enabled' => [
//          [':input[name="cfr[0][target_id]"]' => ['filled' => TRUE]],
//        ],
//      ],
//      '#ajax' => [
//        'wrapper' => 'subactivity-select-wrapper',
//        'callback' => '::subactivityOptions',
//      ],
    ];

    $form['subactivity'] = [
      '#type' => 'select',
      '#title' => $this->t('Sub-activity'),
      '#weight' => 1,
      '#prefix' => '<div id="subactivity-select-wrapper">',
      '#suffix' => '</div>',
//      '#states' => [
//        'disabled' => [
//          ['select[name="activity"]' => ['value' => 0]],
//        ],
//      ],
    ];
    return $form;
  }

  /**
   * AJAX callback for loading activity summaries.
   *
   * @return array
   *   The select element to replace.
   */
  public function activityOptions(array &$form, FormStateInterface $form_state) {
    $selected_cfr = $form_state['values']['cfr'];

    // @todo Replace this with actual data retrieval logic.
    $activities = [1 => 'Activity 1', 2 => 'Activity 2'];

    // Populate the child field options.
    $form['activity']['#options'] = $activities;

    // Return the updated child field.
    return $form['activity'];
  }

  /**
   * AJAX callback for loading activity summaries.
   *
   * @return array
   *   The select element to replace.
   */
  public function subactivityOptions(array &$form, FormStateInterface $form_state) {
    return [
      0 => $this->t('- None -'),
      1 => $this->t('One'),
      2 => $this->t('Two'),
      3 => $this->t('Three'),
    ];
  }

}
