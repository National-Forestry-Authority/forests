<?php

namespace Drupal\observation_quick_form\Plugin\QuickForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;
use Drupal\farm_quick\Traits\QuickLogTrait;

/**
 * Observation log quick form.
 *
 * @QuickForm(
 *   id = "observation_quick_form",
 *   label = @Translation("Observation Quick Form"),
 *   description = @Translation("Use this form to record observations."),
 *   helpText = @Translation("This form will create a new observation log."),
 *   permissions = {
 *     "create log content",
 *   }
 * )
 */
class Observation extends QuickFormBase {

  use QuickLogTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $id = NULL) {

    // Observation quick form.
    $form['coup_number'] = [
      '#type' => 'number',
      '#title' => $this->t('Coup Number'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Draft an observation log from the user-submitted data.
    $coup_number = $form_state->getValue('coup_number');
    $log = [
      'name' => $this->t('Observation log'),
      'type' => 'observation',
      'coup_number' => $coup_number,
    ];

    // Create the log.
    $this->createLog($log);
  }
}