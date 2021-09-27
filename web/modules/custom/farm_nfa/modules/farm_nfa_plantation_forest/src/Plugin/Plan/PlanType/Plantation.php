<?php

namespace Drupal\farm_nfa_plantation_forest\Plugin\Plan\PlanType;

use Drupal\farm_entity\Plugin\Plan\PlanType\FarmPlanType;

/**
 * Provides the forest plantation plan type.
 *
 * @PlanType(
 *   id = "plantation",
 *   label = @Translation("Forest plantation"),
 * )
 */
class Plantation extends FarmPlanType {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    // Assets in the plan.
    $options = [
      'type' => 'entity_reference',
      'label' => $this->t('CFRs'),
      'description' => $this->t('Select the CFRs that this plan pertains to.'),
      'target_type' => 'asset',
      'target_bundle' => 'cfr',
      'multiple' => TRUE,
      'required' => TRUE,
      'weight' => [
        'form' => -50,
        'view' => -10,
      ],
    ];
    $fields['asset'] = $this->farmFieldFactory->bundleFieldDefinition($options);

    return $fields;
  }

}
