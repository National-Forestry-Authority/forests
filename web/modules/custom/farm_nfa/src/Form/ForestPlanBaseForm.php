<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
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

    $form['#title'] = $this->settings['form_title'];
    $form['revision_log_message']['#access'] = FALSE;

    if (isset($form['location'])) {
      foreach (Element::children($form['location']['widget']) as $delta => $widget) {
        if (is_numeric($widget)) {
          $form['location']['widget'][$delta]['target_id']['#selection_handler'] = 'farm_nfa_asset_by_plan';
          $form['location']['widget'][$delta]['target_id']['#selection_settings'] = [
            'target_bundles' => [
              'compartment' => 'compartment'
            ]
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

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxSubmit(&$form, FormStateInterface $form_state) : AjaxResponse {
    $close_dialog = TRUE;
    $response = new AjaxResponse();
    $values = $form_state->getValue('log');
    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = $form['#log'];
    /** @var \Drupal\plan\Entity\PlanInterface $plan */
    $plan = $form['#plan'];
    $plan_assets = array_column($plan->get('asset')->getValue(), 'target_id');
    $log_assets = array_column($log->get('asset')->getValue(), 'target_id');
    $asset = reset($plan_assets);

    $locations = array_filter(array_column($values['location'], 'target_id'));
    $original_locations =  array_column($log->get('location')->getValue(), 'target_id');
    $deleted_locations =  array_diff($original_locations, $locations);

    try {
      if (empty($plan)) {
        throw new \Exception($this->t('Cannot save a task without a plan.'));
      }

      foreach ($values as $value_name => $value) {
        if ($log->hasField($value_name)) {
          $log->set($value_name, $value);
        }
      }
      $assets = array_diff(array_unique(array_merge($plan_assets, $log_assets ,$locations)), $deleted_locations);
      $log->set('asset', $assets);

      $violations = $log->validate();
      if ($violations->count() == 0) {
        $saved_status = $log->save();
        if (!in_array($saved_status, [SAVED_NEW, SAVED_UPDATED])) {
          throw new \Exception($this->t('Task cannot be saved.'));
        }
        $route = farm_nfa_plan_route_log_types($plan, $log);
        $log_types = $route->getDefault('log_types');
        $view = views_embed_view('plan_logs', 'embed', $asset, implode('+', $log_types));
        $response->addCommand(new ReplaceCommand('.view-plan-logs', $view));
        $form['#attached']['library'][] = 'farm_nfa/off_canvas';
        $response->setAttachments($form['#attached']);
        $response->addCommand(new MessageCommand($this->t('The task %name has been saved.', ['%name' => $log->label()]), NULL, ['type' => 'status']));
      }
      else {
        $close_dialog = FALSE;
        foreach ($violations as $violation) {
          $response->addCommand(new MessageCommand($violation->getMessage(), NULL, ['type' => 'error']));
        }
      }
    }
    catch (\Exception $e) {
      $close_dialog = FALSE;
      $response->addCommand(new MessageCommand($this->t('There was an error saving the task.'), NULL, ['type' => 'warning']));
      watchdog_exception('forest_nfa', $e);
    }
    finally {
      if ($close_dialog) {
        $response->addCommand(new CloseDialogCommand('#drupal-off-canvas'));
      }
    }

    return $response;
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
    $log_values = $form_state->getValues();

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
