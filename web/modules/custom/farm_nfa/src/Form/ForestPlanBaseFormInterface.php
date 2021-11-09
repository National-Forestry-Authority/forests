<?php

namespace Drupal\farm_nfa\Form;

/**
 * Forest plan base form.
 *
 * @ingroup farm_nfa
 */
interface ForestPlanBaseFormInterface {

  /**
   * Settings per form.
   *
   * @return array
   */
  public static function defaultSettings() : array;

  /**
   * Gets the log type based on the plan.
   *
   * @param \Drupal\plan\Entity\PlanInterface|null $plan
   *  The plan entity or null.
   *
   * @return string
   *  The log type.
   */
  public function getLogType($plan = NULL) : string;

}
