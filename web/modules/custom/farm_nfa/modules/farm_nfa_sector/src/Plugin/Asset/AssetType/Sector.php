<?php

namespace Drupal\farm_nfa_sector\Plugin\Asset\AssetType;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;

/**
 * Provides the sector asset type.
 *
 * @AssetType(
 *   id = "sector",
 *   label = @Translation("Sector"),
 * )
 */
class Sector extends FarmAssetType {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    return $fields;
  }

}
