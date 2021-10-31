<?php

namespace Drupal\farm_nfa_planting\Plugin\Log\LogType;

use Drupal\farm_entity\Plugin\Log\LogType\FarmLogType;

/**
 * Provides the planting log type.
 *
 * @LogType(
 *   id = "planting",
 *   label = @Translation("Planting"),
 * )
 */
class Planting extends FarmLogType {}
