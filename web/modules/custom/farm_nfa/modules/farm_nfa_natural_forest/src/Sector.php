<?php

namespace Drupal\farm_nfa_natural_forest;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\plan\Entity\PlanInterface;

/**
 * Calculates the sector of a plan.
 */
class Sector extends EntityReferenceFieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {
    $delta = 0;
    /** @var \Drupal\plan\Entity\PlanInterface $entity */
    $entity = $this->getEntity();
    $sector = $this->getSector($entity);
    if ($sector instanceof AssetInterface) {
      $this->list[$delta] = $this->createItem($delta, $sector);
    }
  }

  /**
   * Gets the sector for a plan.
   *
   * @param \Drupal\plan\Entity\PlanInterface $plan
   *   The plan.
   *
   * @return \Drupal\asset\Entity\AssetInterface|false
   *   The sector entity, or FALSE if not found.
   */
  protected function getSector(PlanInterface $plan): AssetInterface|bool {
    foreach ($plan->get('asset') as $item) {
      $cfr = $item->entity;
      if ($cfr->bundle() === 'cfr') {
        $sector = $this->getParentSector($cfr);
        if ($sector instanceof AssetInterface) {
          return $sector;
        }
      }
    }
    return FALSE;
  }

  /**
   * Gets the parent sector of an asset recursively.
   *
   * @param \Drupal\asset\Entity\AssetInterface $asset
   *   The asset.
   *
   * @return \Drupal\asset\Entity\AssetInterface|false
   *   The parent sector entity, or FALSE if not found.
   */
  protected function getParentSector(AssetInterface $asset): AssetInterface|bool {
    $parents = $asset->get('parent')->referencedEntities();
    if (empty($parents)) {
      return FALSE;
    }
    foreach ($parents as $parent) {
      if ($parent->hasField('land_type') && !$parent->get('land_type')->isEmpty()) {
        if ($parent->get('land_type')->value === 'sector') {
          return $parent;
        }
      }
      return $this->getParentSector($parent);
    }

    return FALSE;
  }

}
