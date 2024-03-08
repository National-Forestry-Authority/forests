<?php

namespace Drupal\farm_nfa;

use Drupal\Core\Field\BaseFieldDefinition;

/**
 * A custom field storage definition class.
 *
 * For convenience, we extend from BaseFieldDefinition although this should not
 * implement FieldDefinitionInterface.
 *
 * @see https://www.drupal.org/project/drupal/issues/2981047
 */
class FieldStorageDefinition extends BaseFieldDefinition {

  /**
   * {@inheritdoc}
   */
  public function isBaseField() {
    return FALSE;
  }

}
