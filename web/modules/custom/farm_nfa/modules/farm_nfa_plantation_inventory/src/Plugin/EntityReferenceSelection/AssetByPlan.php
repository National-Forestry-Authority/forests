<?php

namespace Drupal\farm_nfa_plantation_inventory\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\plan\Entity\Plan;
use Drupal\plan\Entity\PlanInterface;

/**
 * Asset by plan ER selection.
 *
 * @EntityReferenceSelection(
 *   id = "farm_nfa_plantation_inventory",
 *   label = @Translation("Asset by plan"),
 *   entity_types = {"asset"},
 *   group = "farm_nfa_plantation_inventory",
 *   weight = 10
 * )
 */
class AssetByPlan extends DefaultSelection {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);

    $plan_id = $this->getPlanId();
    if (!empty($plan_id) && is_numeric($plan_id)) {
      $plan = Plan::load($plan_id);
      if ($plan instanceof PlanInterface) {
        $assets = $plan->get('asset')->getValue();
        $query->condition('parent',  array_column($assets, 'target_id'), 'IN');
      }
    }

    return $query;
  }

  /**
   * Get the plan id from the URL referer.
   *
   * Because the form is displayed on a
   *
   * @return mixed|string|null
   *  Plan id.
   */
  protected function getPlanId() {
    $referer = \Drupal::requestStack()->getCurrentRequest()->server->get('HTTP_REFERER');
    $referer_path = parse_url($referer, PHP_URL_PATH);
    $referer_partial_path = substr($referer_path, strlen(base_path()));
    $referer_args = explode('/', $referer_partial_path);
    return isset($referer_args[1]) ? $referer_args[1] : NULL;
  }

}
