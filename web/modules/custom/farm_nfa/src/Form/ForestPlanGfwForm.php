<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Forest plan gfw form.
 *
 * @ingroup farm_nfa
 */
class ForestPlanGfwForm extends FormBase {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructs a new ForestPlanGfwForm.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function __construct(RouteMatchInterface $routeMatch, Request $request) {
    $this->routeMatch = $routeMatch;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'farm_nfa_forest_budget_plan_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Set the form title.
    $form['#title'] = $this->t('GFW');
    $node = $this->routeMatch->getParameter('asset');
    $assetType = '';
    if($node) {
      $assetType = $node->bundle();
    }

    $form['range'] = [
      '#type' => 'daterangepicker',
      '#prefix' => $assetType != 'land' ? '<div>' :'<div class="gfw-hidden">',
      '#suffix' => '</div>',
      '#DateRangePickerOptions' => [
        'initial_text' => $this -> t('Select date range...'),
        'apply_button_text' =>  $this -> t('Apply'),
        'clear_button_text' =>  $this -> t('Clear'),
        'cancel_button_text' =>  $this -> t('Cancel'),
        'range_splitter' => ' - ',
        'date_format' => 'd M, yy',
        // This needs to be a format recognised by javascript Date.parse method.
        'alt_format' => 'yy-mm-dd',
        'date_picker_options' => [
          'number_of_months' => 2,
        ],
      ],
    ];

    $dates = $this->getCurrentAndLastMonthDates();
    $form['date_range_message'] = [
      '#type' => 'markup',
      '#prefix' =>  $assetType != 'land' ? '<div class="gfw-hidden">' :'<div class="date-range-text">',
      '#suffix' => '</div>',
      '#markup' => $this->t("Showing alerts from <span class='date'>'". $dates['startDate'] ."'</span> to <span class='date'>'" .$dates['currentDate']. "'</span> To see data for more dates please choose a lower level asset (for example CFR)"),
    ];

    $form['gfw_map'] = [
      '#type' => 'farm_map',
      '#map_type' => 'farm_nfa_plan_locations',
      '#map_settings' => [
        'plan' => $this->routeMatch->getRawParameter('plan'),
        'asset' => $this->routeMatch->getRawParameter('asset'),
        'host' => $this->request->getHost(),
        'asset_type' => $assetType,
        'base_query' => 'SELECT latitude,longitude FROM results',
      ],
      '#attached' => [
        'library' => [
          'farm_nfa/behavior_farm_nfa_gfw_layers',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

   /**
  * {@inheritdoc}
  */
  public function getCurrentAndLastMonthDates() {
    $currentDate = new \DateTime();
    $oneMonthAgo = new \DateTime();
    $oneMonthAgo->modify('-1 month');

    return [
      'currentDate' => $currentDate->format('Y-m-d'),
      'startDate' => $oneMonthAgo->format('Y-m-d')
    ];
  }
}
