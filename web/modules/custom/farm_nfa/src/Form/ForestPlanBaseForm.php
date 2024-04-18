<?php

namespace Drupal\farm_nfa\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\log\Entity\Log;
use Drupal\plan\Entity\Plan;
use Drupal\quantity\Entity\QuantityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Forest plan base form.
 *
 * @ingroup farm_nfa
 */
abstract class ForestPlanBaseForm extends FormBase implements ForestPlanBaseFormInterface {

  use DependencySerializationTrait {
    __sleep as traitSleep;
    __wakeup as traitWakeup;
  }
  use AjaxFormHelperTrait;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The plan entity.
   *
   * @var \Drupal\plan\Entity\PlanInterface
   */
  protected $plan;

  /**
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Form settings.
   *
   * @var array
   */
  protected $settings;

  /**
   * Implements the magic __sleep() method to avoid serializing the request.
   */
  public function __sleep() {
    $keys = $this->traitSleep();
    return array_diff($keys, [
      // Remove request from serialization as it's throwing an ugly exception:
      // "Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed in serialize()"
      // @see https://www.drupal.org/project/drupal/issues/2647812
      'request',
    ]);
  }

  /**
   * Implements the magic __wakeup() method to reload the request.
   */
  public function __wakeup() {
    if (!isset($this->request)) {
      $this->request = \Drupal::request();
    }
    $this->traitWakeup();
  }

  /**
   * ForestPlanBaseForm constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request.
   */
  public function __construct(Request $request, RouteProviderInterface $route_provider, RouteMatchInterface $route_match) {
    $this->request = $request;
    $this->settings = static::defaultSettings();
    $this->routeProvider = $route_provider;
    $this->plan = $route_match->getParameter('plan');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('router.route_provider'),
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() : array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLogType($plan = NULL) : string {
    return is_array($this->settings['log_type']) ? $this->settings['log_type'][$plan->bundle()] : $this->settings['log_type'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $plan_id = $this->getRouteMatch()->getRawParameter('plan');
    $log_id = $this->request->query->get('log');

    $form['#plan'] = $form['#log'] = FALSE;
    if (!empty($plan_id) && is_numeric($plan_id)) {
      $form['#plan'] = Plan::load($plan_id);
    }
    if (!empty($log_id) && is_numeric($log_id)) {
      /** @var \Drupal\log\Entity\LogInterface $log */
      $log = Log::load($log_id);
    }

    if (empty($log)) {
      $log = Log::create([
        'type' => $this->getLogType($form['#plan']),
      ]);
    }
    $form['#log'] = $log;

    // Build the form in 'plan' form display mode.
    $form_display = EntityFormDisplay::collectRenderDisplay($log, 'plan');
    $form_display->buildForm($log, $form, $form_state);

    if (!$log->isNew() && $log->hasField('cfr') && !$log->get('cfr')->isEmpty()) {
      // Disable the CFR widget if we're editing an existing log that has a CFR.
      $form['cfr']['widget']['#disabled'] = FALSE;
    }

    if ($form['#plan'] && !$form['#plan']->access('update')) {
      foreach (Element::children($form) as $key) {
        $form[$key]['#disabled'] = TRUE;
      }
    }
    $form['#title'] = $this->settings['form_title'];
    $form['revision_log_message']['#access'] = FALSE;

    if (isset($form['location'])) {
      foreach (Element::children($form['location']['widget']) as $delta => $widget) {
        if (is_numeric($widget)) {
          $form['location']['widget'][$delta]['target_id']['#selection_handler'] = 'farm_nfa_asset_by_plan';
          $form['location']['widget'][$delta]['target_id']['#selection_settings'] = [
            'target_bundles' => [
              'compartment' => 'compartment',
            ],
          ];
        }
      }

      $form['location']['widget']['#description'] = $this->t('If the compartment you are looking for does not appear in your search, the compartment has not yet been created in the system. Please ask the system administrator to create the compartment for you or if you have privileges, please create it via <a href="@href" target="_blank" rel="noopener noreferrer">Add Compartment</a>. Once the compartment has been created, you can continue.', ['@href' => '/asset/add/compartment']);
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#ajax' => [
        'callback' => '::ajaxSubmit',
      ],
      '#attributes' => [
        'class' => [
          'button',
          'button--primary',
        ],
      ],
    ];

    // data-drupal-selector needs to be the same among all ajax requests, so we
    // bypass the bug below by forcing the id as this form is going to be shown
    // once in a given page.
    // @see https://www.drupal.org/node/2897377
    $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function successfulAjaxSubmit($form, FormStateInterface $form_state) : AjaxResponse {
    $response = new AjaxResponse();
    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];

    try {
      $saved_status = $log->save();
      if (!in_array($saved_status, [SAVED_NEW, SAVED_UPDATED])) {
        throw new \Exception($this->t('Task cannot be saved.'));
      }
      $route = $this->routeProvider->getRouteByName(farm_nfa_entity_route_name_by_log_type($plan, $log));
      $log_types = $route->getDefault('log_types');

      // Save the log in the plan, if it's not there already.
      if ($plan->hasField('log')) {
        $existing_logs = array_column($plan->get('log')->getValue(), 'target_id');
        if (!in_array($log->id(), $existing_logs)) {
          $plan->get('log')->appendItem($log);
          $plan->save();
        }
      }
      $view = views_embed_view('plan_logs', 'embed', $plan->id(), implode('+', $log_types));
      $response->addCommand(new ReplaceCommand('.view-plan-logs', $view));
      $response->setAttachments($form['#attached']);
      $response->addCommand(new MessageCommand($this->t('The task %name has been saved.', ['%name' => $log->label()]), NULL, ['type' => 'status'], TRUE));
    }
    catch (\Exception $e) {
      $response->addCommand(new MessageCommand($this->t('There was an error saving the task.'), NULL, ['type' => 'warning'], TRUE));
      $this->logger('forest_nfa')->error($e->getMessage());
    }
    finally {
      $this->messenger()->deleteAll();
      $response->addCommand(new CloseDialogCommand('#drupal-off-canvas'));
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];

    if (empty($plan)) {
      $form_state->setError($form, $this->t('Cannot save a task without a plan.'));
    }

    $form_state->cleanValues();
    $log_values = $form_state->getValues();
    // Ensure timestamp is saved correctly.
    foreach ($log_values as $value_name => $value) {
      if ($log->hasField($value_name)) {
        // Ensure timestamp is saved correctly.
        if (isset($value[0]['value']) && $value[0]['value'] instanceof DrupalDateTime) {
          $value[0]['value'] = $value[0]['value']->getTimestamp();
        }
        $log->set($value_name, $value);
      }
    }

    $violations = $log->validate();
    foreach ($violations as $violation) {
      $form_state->setError($form, $violation->getMessage());
    }

    $form['#log'] = $log;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    // We are using the submitForm method to process the values that do not
    // "fit" exactly from the log form display to the entity save that uses
    // a complex IEF widget.
    // Build the quantities and assets array from the inline_entity_form "magic"
    // value. At this point the Quantity entity has been saved already so to
    // avoid saving it again, we need to get the id and assign it to the
    // quantity reference array (same goes for assets).
    // @see \Drupal\inline_entity_form\WidgetSubmit::doSubmit()
    $data = [];
    $ief_form = $form_state->get('inline_entity_form');
    if ($ief_form) {
      foreach ($form_state->get('inline_entity_form') as $ief) {
        if ($ief['instance'] instanceof FieldDefinitionInterface) {
          $field_name = $ief['instance']->getName();
          $data[$field_name] = [];
          foreach ($ief['entities'] as $ief_value) {
            if ($ief_value['entity'] instanceof QuantityInterface || $ief_value['entity'] instanceof AssetInterface) {
              $data[$field_name][] = $ief_value['entity']->id();
            }
          }
        }
      }
    }

    foreach ($data as $field_name => $datum) {
      $log->set($field_name, $datum);
    }

    // The activities are loaded from the plan rather than the CFR.
    // @todo consider showing the widget to admin users so they can change a
    // task from plan level to CFR level.
    if ($log->hasField('plan_level')) {
      $log->set('plan_level', TRUE);
    }

    $form['#log'] = $log;
  }

  /**
   * Returns the entity being used by this form.
   */
  public function getEntity() {
    return $this->plan;
  }

}
