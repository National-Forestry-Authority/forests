<?php

namespace Drupal\farm_nfa_cfr;

use Drupal\asset\Entity\Asset;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;

class CfrTaskAccessCheck implements AccessInterface {

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param Asset $asset
   *   The CFR to check.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, Asset $asset) {
    $cfr = typed_entity_repository_manager()->wrap($asset);
    $plan = $cfr->getPlan();
    if ($plan) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

}
