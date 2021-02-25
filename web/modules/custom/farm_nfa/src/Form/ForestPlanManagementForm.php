<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Forest plan management form.
 *
 * @ingroup farm_nfa
 */
class ForestPlanManagementForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_management_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Set the form title.
    $form['#title'] = $this->t('Management');

    $form['edit'] = [
      '#type' => 'details',
      '#title' => t('Add/edit management task'),
      '#description' => t('Use this form to create a new management task. Or select a task below to edit it here.')
    ];

    $form['edit']['name'] = [
      '#type' => 'textfield',
      '#title' => t('Task name'),
    ];

    $form['edit']['date'] = [
      '#type' => 'datetime',
      '#title' => t('Date'),
    ];

    $form['edit']['notes'] = [
      '#type' => 'textarea',
      '#title' => t('Notes'),
    ];

    $form['edit']['working_circle'] = [
      '#type' => 'select',
      '#title' => t('Working circle'),
      '#options' => [
        'Conservation',
        'Partnerships & community livelihoods',
        'Production',
        'Research and education',
        'Tourism',
      ],
    ];

    $form['edit']['done'] = [
      '#type' => 'checkbox',
      '#title' => t('This task is done'),
    ];

    $form['list'] = [
      '#type' => 'markup',
      '#markup' => '(placeholder: View of management tasks - load into form above for quick editing)',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
