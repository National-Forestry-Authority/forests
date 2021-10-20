<?php

namespace Drupal\farm_nfa;

use Drupal\plan\Entity\Plan;
use Drupal\plan\Entity\PlanInterface;

/**
 * Loader of Plans from HTTP_REFERER.
 */
class FarmNfaRefererPlanLoader implements FarmNfaRefererPlanLoaderInterface {

  /**
   * {@inheritdoc}
   */
  public function getParameterFromReferer($num) {
    // @TODO Inject this
    $referer = \Drupal::requestStack()->getCurrentRequest()->server->get('HTTP_REFERER');
    $referer_path = parse_url($referer, PHP_URL_PATH);
    $referer_partial_path = substr($referer_path, strlen(base_path()));
    $referer_args = explode('/', $referer_partial_path);
    return isset($referer_args[$num]) ? $referer_args[$num] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $plan_id = $this->getParameterFromReferer(1);
    if (!empty($plan_id) && is_numeric($plan_id)) {
      $plan = Plan::load($plan_id);
      if ($plan instanceof PlanInterface) {
        return $plan;
      }
    }

    return FALSE;
  }

}
