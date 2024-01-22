<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'farm_nfa_program_default' formatter.
 *
 * @FieldFormatter(
 *   id = "farm_nfa_program_default",
 *   label = @Translation("Default"),
 *   field_types = {"farm_nfa_program"}
 * )
 */
class ProgramDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {

      if ($item->summary) {
        $element[$delta]['summary'] = [
          '#type' => 'item',
          '#title' => $this->t('Summary'),
          '#markup' => $item->summary,
        ];
      }

      if ($item->description) {
        $element[$delta]['description'] = [
          '#type' => 'item',
          '#title' => $this->t('Description'),
          '#markup' => $item->description,
        ];
      }

    }

    return $element;
  }

}
