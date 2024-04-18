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
    if (in_array('activity', $log_types)) {
      $display = 'embed_plan_level';
    }
    else {
      $display = 'embed';
    }
    $build['logs'] = views_embed_view('plan_logs', $display, $plan->id(), implode('+', $log_types));
    return $build;
  }

}
