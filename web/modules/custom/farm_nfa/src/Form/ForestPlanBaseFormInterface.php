<?php

namespace Drupal\farm_nfa\Form;


use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\log\Entity\LogInterface;
use Drupal\plan\Entity\PlanInterface;

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

  /**
   * Ajax submit of values.
   *
   * @param $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Response based on the save results.
   */
  public function ajaxSubmit(&$form, FormStateInterface $form_state) : AjaxResponse;

}
