<?php

namespace Drupal\farm_nfa\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Farm NFA map plan.
 *
 * @Block(
 *   id = "farm_nfa_plan_map_block",
 *   admin_label = @Translation("Farm NFA map for plan page"),
 *   category = @Translation("Farm NFA")
 * )
 */
class FarmNfaPlanMap extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'farm_map',
      '#map_type' => 'farm_nfa_plan_locations',
      '#map_settings' => [
        'plan' => \Drupal::routeMatch()->getRawParameter('plan')
      ],
    ];
  }

}
