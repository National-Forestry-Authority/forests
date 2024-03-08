<?php

/**
 * @file
 * Post updates for natural forest plan type.
 */

use Drush\Drush;

/**
 * Refresh the natural forest plan type field configuration.
 */
function farm_nfa_natural_forest_post_update_312() {
  $config_path = \Drupal::service('extension.path.resolver')->getPath('module', 'farm_nfa_natural_forest') . "/deployments/issue_312";
  $process = Drush::drush(Drush::aliasManager()->getSelf(), 'config-import', ['--partial'], ['source' => $config_path]);
  $process->run();
}
