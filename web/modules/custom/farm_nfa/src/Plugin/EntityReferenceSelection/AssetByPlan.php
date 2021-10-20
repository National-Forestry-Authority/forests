<?php

namespace Drupal\farm_nfa\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * Asset by plan ER selection.
 *
 * @EntityReferenceSelection(
 *   id = "farm_nfa_asset_by_plan",
 *   label = @Translation("Asset by plan"),
 *   entity_types = {"asset"},
 *   group = "farm_nfa_asset_by_plan",
 *   weight = 10
 * )
 */
class AssetByPlan extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);
    if ($plan = \Drupal::service('farm_nfa.referer_plan_loader')->load()) {
      $assets = $plan->get('asset')->getValue();
      $query->condition('parent',  array_column($assets, 'target_id'), 'IN');
    }

    return $query;
  }

}
