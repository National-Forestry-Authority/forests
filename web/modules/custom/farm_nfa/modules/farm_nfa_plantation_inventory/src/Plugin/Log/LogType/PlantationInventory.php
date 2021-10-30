<?php

namespace Drupal\farm_nfa_plantation_inventory\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;

/**
 * Provides the plantation inventory log type.
 *
 * @LogType(
 *   id = "plantation_inventory",
 *   label = @Translation("Plantation inventory"),
 * )
 */
class PlantationInventory extends FarmLogType {}
