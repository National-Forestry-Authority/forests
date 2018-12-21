<?php
/**
 * @file
 * Farm access hooks implemented by farm nfa module.
 */

/**
 * Implements hook_farm_access_roles().
 */
function farm_nfa_farm_access_roles() {

  // Build a list of roles.
  $roles = array(
    'senior_management' => array(
      'name' => 'Senior Management',
      'access' => array(
        'view' => 'all',
        'edit' => 'all',
      ),
    ),
    'hq_officer' => array(
      'name' => 'HQ Officer',
      'access' => array(
        'view' => 'all',
        'edit' => 'all',
      ),
    ),
    'range_manager' => array(
      'name' => 'Range Manager',
      'access' => array(
        'view' => 'all',
        'edit' => 'all',
      ),
    ),
    'sector_manager' => array(
      'name' => 'Sector Manager',
      'access' => array(
        'view' => 'all',
        'edit' => 'all',
      ),
    ),
    'forest_supervisor' => array(
      'name' => 'Forest Supervisor',
      'access' => array(
        'view' => 'all',
        'edit' => 'all',
      ),
    ),
  );
  return $roles;
}
