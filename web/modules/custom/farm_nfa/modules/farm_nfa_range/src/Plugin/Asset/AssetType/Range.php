<?php

namespace Drupal\farm_nfa_range\Plugin\Asset\AssetType;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;

/**
 * Provides the range asset type.
 *
 * @AssetType(
 *   id = "range",
 *   label = @Translation("Range"),
 * )
 */
class Range extends FarmAssetType {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    return $fields;
  }

}
