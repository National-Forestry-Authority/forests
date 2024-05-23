<?php

/**
 * @file
 * Post update functions for the farm_nfa module.
 */

/**
 * Implements hook_removed_post_updates().
 */
function farm_nfa_removed_post_updates() {
  return [
    'farm_nfa_post_update_001_add_forest_vegetation_terms' => '10.0.0',
    'farm_nfa_post_update_002_add_forest_purpose_terms' => '10.0.0',
  ];
}

/**
 * Change layout of plan page and add image gallery
 */
function farm_nfa_post_update_10002(&$sandbox) {
  farm_nfa_update_helper(10002);
}
