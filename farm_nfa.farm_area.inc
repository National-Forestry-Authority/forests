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
    'range' => array(
      'label' => t('Range'),
      'style' => t('farm_map_style_red'),
      'weight' => 110,
    ),
    'sector' => array(
      'label' => t('Sector'),
      'style' => 'farm_map_style_yellow',
      'weight' => 100,
    ),
    'cfr_natural' => array(
      'label' => t('Central Forest Reserve - Natural Forest'),
      'style' => 'farm_map_style_dark_green',
      'weight' => 90,
    ),
    'cfr_plantation' => array(
      'label' => t('Central Forest Reserve - Plantation'),
      'style' => 'farm_map_style_purple',
      'weight' => 90,
    ),
    'cfr_urban' => array(
      'label' => t('Central Forest Reserve - Urban Forest'),
      'style' => 'farm_map_style_blue',
      'weight' => 90,
    ),
    'compartment' => array(
      'label' => t('Compartment'),
      'style' => 'farm_map_style_green',
      'weight' => 80,
    ),
    'zone' => array(
      'label' => t('Zone'),
      'style' => 'farm_map_style_red',
      'weight' => 70,
    ),
    'cfm' => array(
      'label' => t('Collaborative Forest Management'),
      'style' => 'farm_map_style_orange',
      'weight' => 60,
    ),
    'tourism' => array(
      'label' => t('Tourism'),
      'style' => 'farm_map_style_blue',
      'weight' => 50,
    ),
    'survey' => array(
      'label' => t('Surveyed boundary'),
      'style' => 'farm_map_style_red',
      'weight' => 40,
    ),
    'nursery' => array(
      'label' => t('Nursery bed'),
      'style' => 'farm_map_style_green',
      'weight' => 30,
    ),
    'observations' => array(
      'label' => t('Field observations'),
      'style' => 'farm_map_style_blue',
      'weight' => 20,
    ),
  );
}
