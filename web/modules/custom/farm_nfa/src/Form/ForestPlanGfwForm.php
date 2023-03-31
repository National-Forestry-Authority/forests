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

    $form['gfw_map'] = [
      '#type' => 'farm_map',
      '#map_type' => 'farm_nfa_plan_locations',
      '#map_settings' => [
        'plan' => $this->routeMatch->getRawParameter('plan'),
        'host' => $this->request->getHost(),
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

}
