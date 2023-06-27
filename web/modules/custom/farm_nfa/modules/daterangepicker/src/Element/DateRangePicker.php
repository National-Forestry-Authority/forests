<?php

namespace Drupal\daterangepicker\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a farm_map render element.
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
      '#pre_render' => [
        [$class, 'preRenderDatePicker'],
      ],
      '#theme' => 'daterangepicker',
    ];
  }
   /**
   * Pre-render callback for the map render array.
   *
   * @param array $element
   *   A renderable array containing a #DateRangePicker_options property.
   *
   * @return array
   *   A renderable array representing the date range picker.
   */
  public static function preRenderDatePicker(array $element) {
    $element['#attached'] = [
      'library' => [
        'daterangepicker/jquery',
        'daterangepicker/moment',
        'daterangepicker/jquery_ui',
        'daterangepicker/jquery_ui_daterange_picker',
        'daterangepicker/daterange_picker',
      ],
        'drupalSettings' => [
            'daterangepicker' => $element['#DateRangePicker_options']
        ],
    ];
    return $element;
  }
}
