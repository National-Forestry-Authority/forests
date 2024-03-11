<?php

namespace Drupal\farm_nfa_cfr\Form;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\farm_nfa\Form\ForestPlanBaseForm;
use Drupal\log\Entity\Log;
use Drupal\quantity\Entity\QuantityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Forest CFR base form.
 *
 * @ingroup farm_nfa
 */
abstract class ForestCfrBaseForm extends ForestPlanBaseForm {

  /**
   * The CFR asset.
   *
   * @var \Drupal\asset\Entity\AssetInterface
   */
  protected $asset;

  /**
   * ForestPlanBaseForm constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(Request $request, RouteProviderInterface $route_provider, RouteMatchInterface $route_match) {
    parent::__construct($request, $route_provider, $route_match);
    $this->asset = $route_match->getParameter('asset');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the plan that the CFR asset is in.
    $cfr = typed_entity_repository_manager()->wrap($this->asset);
    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $cfr->getPlan();
    if (!empty($plan)) {
      $form['#plan'] = $plan;
    }

    $log_id = $this->request->query->get('log');
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

    // Pre-populate the CFR field and make it readonly.
    $form['cfr']['widget'][0]['target_id']['#default_value'] = $this->getRouteMatch()->getParameter('asset');
    $form['cfr']['widget']['#disabled'] = TRUE;

    $form['#title'] = $this->settings['form_title'];
    $form['revision_log_message']['#access'] = FALSE;

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
      $view = views_embed_view('cfr_logs', 'embed', $plan->id(), implode('+', $log_types), $this->asset->id());
      $response->addCommand(new ReplaceCommand('.view-cfr-logs', $view));
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
   * Returns the entity being used by this form.
   */
  public function getEntity() {
    return $this->asset;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    Parent::submitForm($form, $form_state);

    // The activities are loaded from the CFR, not thew plan.
    // @todo consider showing the widget to admin users so they can change a
    // task from plan level to CFR level.
    $form['#log']->set('plan_level', FALSE);
  }
}
