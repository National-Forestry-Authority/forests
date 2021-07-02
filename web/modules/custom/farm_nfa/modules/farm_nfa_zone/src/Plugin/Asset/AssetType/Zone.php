<?php

namespace Drupal\farm_nfa_zone\Plugin\Asset\AssetType;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;

/**
 * Provides the zone asset type.
 *
 * @AssetType(
 *   id = "zone",
 *   label = @Translation("Zone"),
 * )
 */
class Zone extends FarmAssetType {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    return $fields;
  }

}
