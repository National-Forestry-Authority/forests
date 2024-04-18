<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'farm_nfa_program' field widget.
 *
 * @FieldWidget(
 *   id = "farm_nfa_program",
 *   label = @Translation("Program"),
 *   field_types = {"farm_nfa_program"},
 * )
 */
class ProgramWidget extends WidgetBase {

  protected function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state) {
    $elements = parent::formMultipleElements($items, $form, $form_state);

    if ($elements) {
      $elements['#theme'] = 'field_multiple_value_without_order_form';
    }

    return $elements;
  }
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['summary'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Summary'),
      '#default_value' => isset($items[$delta]->summary) ? $items[$delta]->summary : NULL,
    ];

    $element['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => isset($items[$delta]->description) ? $items[$delta]->description : NULL,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'farm-nfa-program-elements';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    return isset($violation->arrayPropertyPath[0]) ? $element[$violation->arrayPropertyPath[0]] : $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      if ($value['summary'] === '') {
        $values[$delta]['summary'] = NULL;
      }
      if ($value['description'] === '') {
        $values[$delta]['description'] = NULL;
      }
    }
    return $values;
  }

}
