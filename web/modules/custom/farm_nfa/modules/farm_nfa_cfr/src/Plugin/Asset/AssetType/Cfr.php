<?php

namespace Drupal\farm_nfa_cfr\Plugin\Asset\AssetType;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;

/**
 * Provides the cfr asset type.
 *
 * @AssetType(
 *   id = "cfr",
 *   label = @Translation("CFR"),
 * )
 */
class Cfr extends FarmAssetType {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    return $fields;
  }

}
