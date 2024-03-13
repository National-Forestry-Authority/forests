<?php

namespace Drupal\farm_nfa\entity;

use Drupal\asset\Entity\Asset;
use Drupal\plan\Entity\PlanInterface;

/**
 * Override the FarmOS Asset entity for NFA.
 */
class NfaAsset extends Asset {

  /**
   * Return the plan that the CFR belongs to.
   */
  public function getPlan(): ?PlanInterface {
    $storage = \Drupal::service('entity_type.manager')->getStorage('plan');
    $plan_results = $storage->getQuery()
      ->condition('type', 'natural')
      ->condition('asset.entity.id', $this->id())
      ->accessCheck()
      ->execute();
    if ($plan_results) {
      /** @var \Drupal\plan\Entity\PlanInterface $plan */
      $plan = $storage->load(reset($plan_results));
      return $plan;
    }
    return NULL;
  }

}
