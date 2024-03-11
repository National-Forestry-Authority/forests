<?php

namespace Drupal\farm_nfa\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Deletion form overrides from off canvas.
 */
class FarmNfaContentEntityDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->getRequest()->request->get('_drupal_ajax') != 1) {
      parent::submitForm($form, $form_state);
      return;
    }

    try {
      parent::submitForm($form, $form_state);
    }
    catch (\Exception $e) {
      $this->logger('farm_nfa', $e);
      $this->messenger()->addError($e->getMessage());
    }
  }

}
