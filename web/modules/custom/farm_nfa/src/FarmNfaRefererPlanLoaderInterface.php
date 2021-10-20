<?php

namespace Drupal\farm_nfa;

interface FarmNfaRefererPlanLoaderInterface {

  /**
   * Returns a plan from the HTTP_REFERERS.
   *
   * @return \Drupal\plan\Entity\PlanInterface|bool
   */
  public function load();

}
