<?php

namespace Drupal\farm_nfa_natural_forest\Plugin\Plan\PlanType;

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
      'description' => $this->t('Select the CFRs that this plan pertains to. If the desired compartment does not yet exist in the system, a new record must be created for it. This can be done via the <a href="@add">Add Compartment</a> form.', ['@add' => '/asset/add/compartment']),
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
