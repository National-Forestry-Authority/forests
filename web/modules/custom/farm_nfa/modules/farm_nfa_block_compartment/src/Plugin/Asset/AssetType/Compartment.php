<?php

namespace Drupal\farm_nfa_block_compartment\Plugin\Asset\AssetType;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\farm_entity\Plugin\Asset\AssetType\FarmAssetType;

/**
 * Provides the cfr asset type.
 *
 * @AssetType(
 *   id = "compartment",
 *   label = @Translation("Compartment"),
 * )
 */
class Compartment extends FarmAssetType {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    return $fields;
  }

}
