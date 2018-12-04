<?php

/**
 * @file
 * Farm Area hooks implemented by the Farm Forest module.
 */

/**
 * Implements hook_farm_area_type_info().
 */
function farm_nfa_farm_area_type_info() {
  return array(
    'cfr' => array(
      'label' => t('Central Forest Reserve'),
      'style' => 'farm_map_style_purple',
      'weight' => 100,
    ),
  );
}
