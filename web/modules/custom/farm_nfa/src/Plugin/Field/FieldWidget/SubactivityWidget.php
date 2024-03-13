<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the Program/activity & sub-activity field widget.
 *
 * @FieldWidget(
 *   id = "farm_nfa_subactivity",
 *   label = @Translation("NFA: Program/activity"),
 *   field_types = {"string"},
 * )
 */
class SubactivityWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['subactivity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Summary'),
      '#default_value' => isset($items[$delta]->summary) ? $items[$delta]->summary : NULL,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'farm-nfa-program-elements';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return $values['subactivity'] ?? [];
  }

}
