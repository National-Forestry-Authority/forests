<?php

namespace Drupal\farm_nfa\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\plan\Entity\PlanInterface;

/**
 * Returns the plan tabs.
 */
class PlanTabsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function build(PlanInterface $plan = NULL, $log_types = []) {
    $build = [];
    $build['logs'] = views_embed_view('plan_logs', 'embed', $plan->id(), implode('+', $log_types));
    return $build;
  }

}
