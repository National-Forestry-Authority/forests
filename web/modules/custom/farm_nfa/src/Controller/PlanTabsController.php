<?php

namespace Drupal\farm_nfa\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the plan tabs.
 */
class PlanTabsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function build(PlanInterface $plan = NULL) {
    $build = [];
    $assets = array_column($plan->get('asset')->getValue(), 'target_id');
    $asset = reset($assets);
    $build['logs'] = views_embed_view('plan_logs', 'embed', $asset);
    $build['#attached']['library'][] = 'farm_nfa/off_canvas';
    return $build;
  }

}
