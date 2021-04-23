<?php

namespace Drupal\farm_nfa\Form;


use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;

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
   * Saves the log(task).
   *
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function saveTask(&$form, FormStateInterface $form_state) : AjaxResponse;

}
