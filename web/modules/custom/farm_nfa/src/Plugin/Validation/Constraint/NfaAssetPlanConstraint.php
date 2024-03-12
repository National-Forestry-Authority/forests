<?php

namespace Drupal\farm_nfa\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks if an asset belongs to just one plan that is not archived.
 *
 * @Constraint(
 *   id = "NfaAssetPlan",
 *   label = @Translation("Ensures an asset belongs to just one plan that is not archived", context = "Validation"),
 * )
 */
class NfaAssetPlanConstraint extends Constraint {

  public string $message = 'A @entity_type with @field_name %value already exists.';

  /**
   * Returns the name of the class that validates this constraint.
   *
   * @return string
   */
  public function validatedBy() {
    return '\Drupal\farm_nfa\Plugin\Validation\Constraint\NfaAssetPlanConstraintValidator';
  }

}
