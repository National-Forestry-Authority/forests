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
    'zone' => array(
      'label' => t('Zone'),
      'style' => 'farm_map_style_purple',
      'weight' => 80,
    ),
    'cfm' => array(
      'label' => t('Collaborative Forest Management'),
      'style' => 'farm_map_style_purple',
      'weight' => 70,
    ),
    'tourism' => array(
      'label' => t('Tourism'),
      'style' => 'farm_map_style_purple',
      'weight' => 60,
    ),
  );
}
