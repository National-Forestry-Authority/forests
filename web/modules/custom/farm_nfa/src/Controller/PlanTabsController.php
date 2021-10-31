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
    $assets = array_column($plan->get('asset')->getValue(), 'target_id');
    $build['logs'] = views_embed_view('plan_logs', 'embed', implode('+', $assets), implode('+', $log_types));
    $build['#attached']['library'][] = 'farm_nfa/off_canvas';
    return $build;
  }

}
