<?php

/**
 * @file
 * NFA dashboard.
 */

/**
 * NFA dashboard page callback.
 */
function farm_nfa_dashboard() {

  // Start a build array.
  $build = array();

  // Header.
  $build['header']  = array(
    '#type' => 'markup',
    '#markup' => '<h2>National Forestry Authority Forest Management Information System</h2>',
  );

  // Map.
  $build['map'] = farm_map_build('farm_areas', TRUE, t('Areas (click to expand/collapse)'));

  // Left and right columns.
  $build['left'] = array(
    '#type' => 'container',
    '#prefix' => '<div class="col-md-6">',
    '#suffix' => '</div>',
  );
  $build['right'] = array(
    '#type' => 'container',
    '#prefix' => '<div class="col-md-6">',
    '#suffix' => '</div>',
  );

  // Create a list of links to sector FAMPS.
  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', 'taxonomy_term');
  $query->entityCondition('bundle', 'farm_areas');
  $query->fieldCondition('field_farm_area_type', 'value', 'sector');
  $result = $query->execute();
  $sectors = array();
  if (isset($result['taxonomy_term'])) {
    $sector_ids = array_keys($result['taxonomy_term']);
    $sectors = entity_load('taxonomy_term', $sector_ids);
  }
  $famps = '';
  if (!empty($sectors)) {
    $famps_items = array();
    foreach ($sectors as $sector) {
      $entity_label = entity_label('taxonomy_term', $sector);
      $url = 'fmis/famps/' . $sector->tid;
      $famps_items[] = l($entity_label, $url);
    }
    if (!empty($famps_items)) {
      $famps = theme('item_list', array('items' => $famps_items));
    }
  }

  // Natural forest planning.
  $build['left']['natural'] = array(
    '#type' => 'markup',
    '#markup' => '<h3 class="text-center">' . t('Natural Forest Management') . '</h3><p>' . t('Select a Forest Area Management Plan Sector below to see plans within that sector.') . '</p>' . $famps,
  );

  // Create a list of links to plantation plans.
  $result = db_query('SELECT p.id FROM {farm_plan} p LEFT JOIN {farm_nfa_forest_plan_type} t ON p.id = t.plan_id WHERE t.type = :type', array(':type' => 'plantation'));
  $plantation_plan_links = array();
  foreach ($result as $row) {
    if (!empty($row->id)) {
      $plan = farm_plan_load($row->id);
      if (!empty($plan)) {
        $plan_label = entity_label('farm_plan', $plan);
        $plan_uri = entity_uri('farm_plan', $plan);
        $plantation_plan_links[] = l($plan_label, $plan_uri['path']);
      }
    }
  }
  $plantation_plans = '';
  if (!empty($plantation_plan_links)) {
    $plantation_plans = theme('item_list', array('items' => $plantation_plan_links));
  }

  // Plantation forest planning.
  $build['right']['plantation'] = array(
    '#type' => 'markup',
    '#markup' => '<h3 class="text-center">' . t('Plantation Management') . '</h3><p>' . t('Select a Forest Plantation Plan below to manage activities within it.') . '</p>' . $plantation_plans,
  );

  // Return the build array.
  return $build;
}
