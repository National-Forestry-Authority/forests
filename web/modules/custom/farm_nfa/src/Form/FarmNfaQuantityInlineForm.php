<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\quantity\Form\QuantityInlineForm;

/**
 * Render the quantity entity in quantity inline entity forms.
 */
class FarmNfaQuantityInlineForm extends QuantityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function entityForm(array $entity_form, FormStateInterface $form_state) {
    if (isset($entity_form['#widget_type']) && $entity_form['#widget_type'] == 'farm_nfa_inline_entity_form_quantity') {
      $default_measure = $entity_form['#default_measure'];
      $default_unit = $entity_form['#default_unit'];
      $entity_form['#entity']->set('measure', $default_measure);
      $entity_form['#entity']->set('units', $default_unit);
      $quantity_label = $default_unit->label();
      $entity_form['#entity']->set('label', "$default_measure / $quantity_label");

      $entity_form = parent::entityForm($entity_form, $form_state);

      $entity_form['measure']['#access'] = FALSE;
      $entity_form['units']['#access'] = FALSE;
      $entity_form['label']['#access'] = FALSE;
    }
    else {
      $entity_form = parent::entityForm($entity_form, $form_state);
    }

    return $entity_form;
  }

}
