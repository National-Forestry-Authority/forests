<?php

/**
 * @file
 * Post update functions for the farm_nfa module.
 */

use Drupal\taxonomy\Entity\Term;

/**
 * Save the taxonomy terms for forest_vegetation taxonomy type.
 */
function farm_nfa_post_update_001_add_forest_vegetation_terms(&$sandbox) {
  $vocab_terms = [
    'forest_vegetation' => [
      'Tropical High Forest',
      'Woodland',
      'Bush',
    ],
  ];
  foreach ($vocab_terms as $vocab => $terms) {
    $current_vocab = $vocab;
    // Term names to be added.
    foreach ($terms as $term) {
      $term = Term::create([
        'name' => $term,
        'vid' => $current_vocab,
      ]);
      $term->save();
    }
  }
}

/**
 * Save the taxonomy terms for forest purpose taxonomy type.
 */
function farm_nfa_post_update_002_add_forest_purpose_terms(&$sandbox) {
  $vocab = 'forest_purpose';
  $forest_purpose_terms = [
    'Timber' => [
      'forest_type' => 'plantation',
    ],
    'Poles' => [
      'forest_type' => 'plantation',
    ],
    'Fuel' => [
      'forest_type' => 'plantation',
    ],
    'Processing' => [
      'forest_type' => 'plantation',
    ],
    'Conservation' => [
      'forest_type' => 'natural',
    ],
    'Restoration' => [
      'forest_type' => 'natural',
    ],
  ];
  foreach ($forest_purpose_terms as $term => $term_info) {
    // Term names to be added.
    $current_term = Term::create([
      'name' => $term,
      'vid' => $vocab,
      'forest_type' => $term_info['forest_type'],
    ]);
    $current_term->save();
  }
}
