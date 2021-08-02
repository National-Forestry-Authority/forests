<?php

namespace Drupal\farm_nfa\Plugin\Plan\PlanType;

use Drupal\farm_entity\Plugin\Plan\PlanType\FarmPlanType;

/**
 * Provides the natural forest plan type.
 *
 * @PlanType(
 *   id = "natural",
 *   label = @Translation("Natural forest"),
 * )
 */
class Natural extends FarmPlanType {

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
    $fields['cfr'] = $this->farmFieldFactory->bundleFieldDefinition($options);

    return $fields;
  }

}
