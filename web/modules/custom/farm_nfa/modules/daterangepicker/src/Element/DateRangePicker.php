<?php

namespace Drupal\daterangepicker\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a daterangepicker render element.
 *
 * @RenderElement("daterangepicker")
 */
class DateRangePicker extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);

    return [
      '#theme' => 'daterangepicker',
      '#pre_render' => [
        [$class, 'preRenderDatePicker'],
      ],
      '#title' => NULL,
      '#src_type' => NULL,
      '#src' => NULL,
      '#attributes' => NULL,
      '#attached' => [
        'library' => [
          'daterangepicker/moment',
          'daterangepicker/jquery_ui_daterange_picker',
          'daterangepicker/daterange_picker',
        ],
      ],
    ];
  }

/**
 * Pre-render callback for the daterangepicker render array.
 *
 * @param array $element
 *   A renderable array containing a #DateRangePickerOptions property.
 *
 * @return array
 *   A renderable array representing the date range picker.
 */
  public static function preRenderDatePicker(array $element) {
    $element['#attached']['drupalSettings'] = [
      'daterangepicker' => $element['#DateRangePickerOptions'],
    ];
    return $element;
  }
}
