<?php

/**
 * @file
 * Farm NFA views hooks.
 */

/**
 * Implements hook_views_data_alter().
 */
function farm_nfa_views_data_alter(&$data) {

  // Describe the {farm_nfa_forest_plan_type} table to Views.
  $data['farm_nfa_forest_plan_type']['table']['group'] = t('NFA forest plan type');

  // Create an implicit relationships with the {farm_plan} table.
  $data['farm_nfa_forest_plan_type']['table']['join'] = array(
    'farm_plan' => array(
      'left_field' => 'id',
      'field' => 'plan_id',
    ),
  );

  // Describe the plan type column so that it can be used as a filter.
  $data['farm_nfa_forest_plan_type']['type'] = array(
    'title' => t('Forest plan type'),
    'help' => t('The type of forest plan (natural/plantation)'),
    'filter' => array(
      'handler' => 'views_handler_filter_string',
    ),
  );
}
