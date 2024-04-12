<?php

namespace Drupal\farm_nfa_cfr\WrappedEntities;

use Drupal\plan\Entity\PlanInterface;
use Drupal\typed_entity\WrappedEntities\WrappedEntityBase;

/**
 * The wrapped entity for the CFR asset type.
 */
class Cfr extends WrappedEntityBase {

  /**
   * Return the plan that the CFR belongs to.
   */
  public function getPlan(): ?PlanInterface {
    $storage = \Drupal::service('entity_type.manager')->getStorage('plan');
    $plan_results = $storage->getQuery()
      ->condition('type', 'natural')
      ->condition('asset.entity.id', $this->entity->id())
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
