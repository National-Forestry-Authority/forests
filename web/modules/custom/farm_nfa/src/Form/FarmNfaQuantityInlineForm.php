<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\quantity\Form\QuantityInlineForm;
use Drupal\taxonomy\Entity\Term;

/**
 * Render the quantity entity in quantity inline entity forms.
 */
class FarmNfaQuantityInlineForm extends QuantityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    if (isset($entity_form['#widget_type']) && $entity_form['#widget_type'] == 'farm_nfa_inline_entity_form_quantity') {
      $default_quantity = $entity_form['#$default_quantity'];
      $delta = $entity_form['#ief_row_delta'];
      $entity_form['#entity']->set('label', $default_quantity[$delta]['label']);
      $entity_form['#entity']->set('measure', $default_quantity[$delta]['measure']);
      $term = Term::load($default_quantity[$delta]['units']);
      $entity_form['#entity']->set('units', $term);

      $entity_form = parent::entityForm($entity_form, $form_state);

      $entity_form['value']['#required'] = TRUE;
      $entity_form['measure']['#disabled'] = TRUE;
      $entity_form['units']['#disabled'] = TRUE;
      $entity_form['label']['#disabled'] = TRUE;
    }
    else {
      $entity_form = parent::entityForm($entity_form, $form_state);
    }

    return $entity_form;
  }

}
