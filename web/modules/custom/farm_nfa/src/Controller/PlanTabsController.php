<?php

namespace Drupal\farm_nfa\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the plan tabs.
 */
class PlanTabsController extends ControllerBase implements ContainerInjectionInterface {
  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new PlanTabsController object.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   *   The current route match.
   */
  public function __construct(CurrentRouteMatch $currentRouteMatch) {
    $this->currentRouteMatch = $currentRouteMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(PlanInterface $plan = NULL, $log_types = []) {
    $build = [];
    if ($this->currentRouteMatch->getRouteName() === 'entity.plan.maps') {
      $build['maps'] = views_embed_view('plan_blocks', 'maps', $plan->id());
    }
    else {
      if (in_array('activity', $log_types)) {
        $display = 'embed_plan_level';
      }
      else {
        $display = 'embed';
      }
      $build['logs'] = views_embed_view('plan_logs', $display, $plan->id(), implode('+', $log_types));
    }
    return $build;
  }

}
