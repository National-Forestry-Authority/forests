<?php

namespace Drupal\farm_nfa\Plugin\Field\FieldFormatter;

use Drupal\farm_map\Plugin\Field\FieldFormatter\GeofieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'map_with_date_picker' formatter.
 *
 * @FieldFormatter(
 *   id = "map_with_date_picker",
 *   label = @Translation("farmOS Map with Date Picker"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class MapWithDatepickerFormatter extends GeofieldFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Call the parent class's implementation.
    $element = parent::viewElements($items, $langcode);

    $element['datepicker'] = [
      '#type' => 'daterangepicker',
      '#prefix' => '<div class="daterange-picker">',
      '#suffix' => '</div>',
      '#DateRangePickerOptions' => [
        'initial_text' => $this->t('Select date range...'),
        'apply_button_text' => $this->t('Apply'),
        'clear_button_text' => $this->t('Clear'),
        'cancel_button_text' => $this->t('Cancel'),
        'range_splitter' => ' - ',
        'date_format' => 'd M, yy',
        // This needs to be a format recognised by javascript Date.parse method.
        'alt_format' => 'yy-mm-dd',
        'date_picker_options' => [
          'number_of_months' => 2,
        ],
      ],
    ];

    $element['datepicker_help'] = [
      '#type' => 'markup',
      '#markup' => t('Click to select the date range'),
      '#prefix' => '<div class="daterange-picker-help">',
      '#suffix' => '</div>',
    ];

    $element_wrapper = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['map-with-datepicker'],
      ],
      $element,
    ];

    return $element_wrapper;
  }

}
