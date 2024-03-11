<?php

namespace Drupal\farm_nfa_cfr\Controller;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns the content of the CFR management tab.
 */
class CfrTabsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function build(AssetInterface $asset = NULL, $log_types = []) {
    $build = [];
    $cfr = typed_entity_repository_manager()->wrap($asset);
    $plan = $cfr->getPlan();
    if ($plan) {
      $build['logs'] = views_embed_view('cfr_logs', 'embed', $plan->id(), implode('+', $log_types), $asset->id());
    }
    else {
      $build = ['#markup' => $this->t('@cfr CFR is not assigned to a Forest Management Plan. Assign the CFR to a <a href="/plans/natural">Plan</a> so that management tasks can be added.', ['@cfr' => $asset->label()])];
    }

    return $build;
  }

}
