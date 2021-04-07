<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\log\Entity\Log;
use Drupal\log\Entity\LogType;
use Drupal\plan\Entity\Plan;

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
    $plan_id = \Drupal::routeMatch()->getRawParameter('plan');
    $log_id = \Drupal::request()->query->get('log');
    $workflow_manager = \Drupal::service('plugin.manager.workflow');

    $form['#plan'] = $form['#log'] = FALSE;
    if (!empty($plan_id) && is_numeric($plan_id)) {
      $form['#plan'] = Plan::load($plan_id);
    }
    if (!empty($log_id) && is_numeric($log_id)) {
      /** @var \Drupal\log\Entity\LogInterface $log */
      $form['#log'] = $log = Log::load($log_id);
    }

    // Set the form title.
    $form['#title'] = $this->t('Management');

    $form['log_type'] = [
      '#type' => 'select',
      '#title' => t('Task type'),
      '#options' => [
        'activity' => t('Activity'),
        'input' => t('Input'),
      ],
      '#default_value' => !empty($log) ? $log->bundle() : '',
      '#disabled' => !empty($log),
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => t('Task name'),
      '#default_value' => !empty($log) ? $log->label() : '',
    ];

    $form['date'] = [
      '#type' => 'datetime',
      '#title' => t('Date'),
      '#default_value' => !empty($log) ? DrupalDateTime::createFromTimestamp($log->get('timestamp')->value) : DrupalDateTime::createFromTimestamp(\Drupal::time()->getRequestTime()),
    ];

    $form['notes'] = [
      '#type' => 'textarea',
      '#title' => t('Notes'),
      '#default_value' => !empty($log) ? $log->get('notes')->value : '',
    ];

    $taxonomy_term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $working_circle_ids = $taxonomy_term_storage->getQuery()->condition('vid', 'working_circle')->execute();
    $working_circles = $taxonomy_term_storage->loadMultiple($working_circle_ids);
    $working_circle_options = [];
    foreach ($working_circles as $working_circle) {
      $working_circle_options[$working_circle->id()] = $working_circle->label();
    }
    $form['working_circle'] = [
      '#title' => t('Working circle'),
      '#type' => 'select',
      '#options' => $working_circle_options,
      '#multiple' => TRUE,
      '#default_value' => array_column($log->get('working_circle')->getValue(), 'target_id'),
    ];

    $status_options = [];
    if (!empty($log) && $log->hasField('status') && !$log->get('status')->isEmpty()) {
      /** @var \Drupal\state_machine\Plugin\Workflow\WorkflowInterface $workflow */
      $workflow = $workflow_manager->createInstance(Log::getWorkflowId($log));
      $transitions = $workflow->getPossibleTransitions(
        $log->get('status')->value
      );
      $current_state = $workflow->getState($log->get('status')->value);
      $status_options[$current_state->getId()] = $current_state->getLabel();
      foreach ($transitions as $transition) {
        $status_options[$transition->getId()] = $transition->getLabel();
      }
    }
    else {
      // @TODO the workflow needs to be loaded automatically depending on the
      // log type selected. Assume Activity for the time being.
      /** @var \Drupal\log\Entity\LogTypeInterface $default_bundle */
      $default_bundle = LogType::load('activity');
      /** @var \Drupal\state_machine\Plugin\Workflow\WorkflowInterface $workflow */
      $workflow = $workflow_manager->createInstance($default_bundle->getWorkflowId());
      $states = $workflow->getStates();
      foreach ($states as $state) {
        $status_options[$state->getId()] = $state->getLabel();
      }
    }
    $form['status'] = [
      '#type' => 'select',
      '#title' => t('Status of the task'),
      '#options' => $status_options,
      '#default_value' => !empty($log) ? $log->get('status')->value : '',
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => '::saveTask',
      ],
      '#attributes' => [
        'class' => [
          'button',
          'button--primary',
        ],
      ],
    ];

    return $form;
  }

  /**
   * Saves the log(task).
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function saveTask(&$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $values = $form_state->getValues();

    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];
    $assets = array_column($plan->get('asset')->getValue(), 'target_id');
    $asset = reset($assets);

    try {
      if ($log) {
        $log->set('name', $values['name']);
        $log->set('notes', $values['notes']);
        $log->set('status', $values['status']);
        $log->set('timestamp', $values['date']->getTimestamp());
        $log->set('working_circle', $values['working_circle']);
      }
      else {
        if (empty($plan)) {
          throw new \Exception($this->t('Cannot save a task without a plan.'));
        }
        $log = Log::create(
          [
            'name' => $values['name'],
            'type' => $values['log_type'],
            'notes' => $values['notes'],
            'status' => $values['status'],
            'timestamp' => $values['date']->getTimestamp(),
            'asset' => $assets,
          ]
        );
      }
      if (!$log->validate()) {
        throw new \Exception($this->t('Task cannot be saved.'));
      }
      $saved_status = $log->save();

      if (in_array($saved_status, [SAVED_NEW, SAVED_UPDATED])) {
        $view = views_embed_view('plan_logs', 'embed', $asset);
        $response->addCommand(new ReplaceCommand('.view-plan-logs', $view));
        $form['#attached']['library'][] = 'farm_nfa/off_canvas';
        $response->setAttachments($form['#attached']);
        $response->addCommand(new MessageCommand($this->t('The task has been saved.'), NULL, ['type' => 'status']));
      }
    } catch (\Exception $e) {
      $response->addCommand(new MessageCommand($this->t('There was an error saving the task.'), NULL, ['type' => 'warning']));
      watchdog_exception('forest_nfa', $e);
    } finally {
      $response->addCommand(new CloseDialogCommand('#drupal-off-canvas'));
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Do nothing.
  }

}
