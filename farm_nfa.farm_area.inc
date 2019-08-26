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
    'sector' => array(
      'label' => t('Sector'),
      'style' => 'farm_map_style_yellow',
      'weight' => 100,
    ),
    'cfr' => array(
      'label' => t('Central Forest Reserve'),
      'style' => 'farm_map_style_purple',
      'weight' => 90,
    ),
    'compartment' => array(
      'label' => t('Compartment'),
      'style' => 'farm_map_style_purple',
      'weight' => 80,
    ),
    'zone' => array(
      'label' => t('Zone'),
      'style' => 'farm_map_style_purple',
      'weight' => 70,
    ),
    'cfm' => array(
      'label' => t('Collaborative Forest Management'),
      'style' => 'farm_map_style_purple',
      'weight' => 60,
    ),
    'tourism' => array(
      'label' => t('Tourism'),
      'style' => 'farm_map_style_purple',
      'weight' => 50,
    ),
  );
}
