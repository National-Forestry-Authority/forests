<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\log\Entity\Log;
use Drupal\plan\Entity\Plan;
use Drupal\plan\Entity\PlanInterface;
use Drupal\quantity\Entity\QuantityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Forest plan base form.
 *
 * @ingroup farm_nfa
 */
abstract class ForestPlanBaseForm extends FormBase implements ForestPlanBaseFormInterface {

  /**
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Form settings,
   *
   * @var array
   */
  protected $settings;

  /**
   * ForestPlanBaseForm constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request.
   */
  public function __construct(Request $request) {
    $this->request = $request;
    $this->settings = static::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')->getCurrentRequest(),
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
      $form['#log'] = $log = Log::load($log_id);
    }

    if (empty($log)) {
      $log = Log::create([
        // @TODO Should we add a log_type_callback instead?
        'type' => $this->getLogType($form['#plan']),
      ]);
    }

    // Build the form in 'plan' form display mode.
    $form['log'] = [
      '#parents' => ['log'],
    ];
    $form_display = EntityFormDisplay::collectRenderDisplay($log, 'plan');
    $form_display->buildForm($log, $form['log'], $form_state);

    $form['#title'] = $this->settings['form_title'];
    $form['log']['revision_log_message']['#access'] = FALSE;

    if (isset($form['log']['location'])) {
      foreach (Element::children($form['log']['location']['widget']) as $delta => $widget) {
        if (is_numeric($widget)) {
          $form['log']['location']['widget'][$delta]['target_id']['#selection_handler'] = 'farm_nfa_asset_by_plan';
          $form['log']['location']['widget'][$delta]['target_id']['#selection_settings'] = [
            'target_bundles' => [
              'compartment' => 'compartment'
            ]
          ];
        }
      }
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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxSubmit(&$form, FormStateInterface $form_state) : AjaxResponse {
    $response = new AjaxResponse();
    $values = $form_state->getValue('log');
    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];
    $assets = array_column($plan->get('asset')->getValue(), 'target_id');
    $asset = reset($assets);

    try {
      $log = $this->saveTask($plan, $assets, $values, $log);
      $view = views_embed_view('plan_logs', 'embed', $asset, implode('+', $this->settings['display_log_types']));
      $response->addCommand(new ReplaceCommand('.view-plan-logs', $view));
      $form['#attached']['library'][] = 'farm_nfa/off_canvas';
      $response->setAttachments($form['#attached']);
      $response->addCommand(new MessageCommand($this->t('The task %name has been saved.', ['%name' => $log->label()]), NULL, ['type' => 'status']));
    }
    catch (\Exception $e) {
      $response->addCommand(new MessageCommand($this->t('There was an error saving the task.'), NULL, ['type' => 'warning']));
      watchdog_exception('forest_nfa', $e);
    }
    finally {
      $response->addCommand(new CloseDialogCommand('#drupal-off-canvas'));
    }

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function saveTask(PlanInterface $plan, array $assets, array $values, $log = FALSE) {
    if ($log) {
      foreach ($values as $value_name => $value) {
        // @TODO Do we need to add a preprocess plugin?
        $log->set($value_name, $value);
      }
    }
    else {
      if (empty($plan)) {
        throw new \Exception($this->t('Cannot save a task without a plan.'));
      }
      $log = Log::create($values + [
        'type' => $this->getLogType($plan),
        'asset' => $assets,
      ]);
    }
    if (!$log->validate()) {
      throw new \Exception($this->t('Task cannot be saved.'));
    }
    $saved_status = $log->save();
    if (in_array($saved_status, [SAVED_NEW, SAVED_UPDATED])) {
      return $log;
    }
    else {
      throw new \Exception($this->t('Task cannot be saved.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // We are using the submitForm method to process the values that do not
    // "fit" exactly from the log form display to the entity save, at this
    // point in time is the date fields and the quantity, that uses a complex
    // IEF widget.
    $form_state->cleanValues();
    $log_values = $form_state->getValue('log');

    // Ensure timestamp is saved correctly.
    foreach ($log_values as &$value) {
      if (isset($value[0]['value']) && $value[0]['value'] instanceof DrupalDateTime) {
        $value[0]['value'] = $value[0]['value']->getTimestamp();
      }
    }

    // Build the quantities array from the inline_entity_form "magic" value.
    // At this point the Quantity entity has been saved already so to avoid
    // saving it again, we need to get the id and assign it to the quantity
    // reference array.
    // @see \Drupal\inline_entity_form\WidgetSubmit::doSubmit()
    foreach ($form_state->get('inline_entity_form') as $ief) {
      $field_name = $ief['instance']->getName();
      $log_values[$field_name] = [];
      foreach ($ief['entities'] as $ief_value) {
        if ($ief_value['entity'] instanceof QuantityInterface) {
          $log_values[$field_name][] = $ief_value['entity']->id();
        }
      }
    }

    $form_state->setValue('log', $log_values);
  }

}
