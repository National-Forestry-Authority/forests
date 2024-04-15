<?php

namespace Drupal\farm_nfa;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\plan\Entity\Plan;

/**
 * Checks access for displaying plan permissions route.
 */
class PlanPermissionsRouteChecker implements AccessInterface {

  /**
   * Checks access for displaying plan permissions route.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param string $plan
   *   The plan entity.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Returns allowed if the user has the correct permissions.
   */
  public function access(AccountInterface $account, Plan $plan) {
    $user_ids = [];
    $roles = $account->getRoles();
    $all_permissions = [];

    // Check if the field 'user' exists on the Plan entity and is not empty.
    if ($plan->hasField('user') && !$plan->get('user')->isEmpty()) {
      $users = $plan->get('user')->referencedEntities();
      foreach ($users as $user) {
        $user_ids[] = $user->id();
      }
    }
    $has_administer_permissions = $account->hasPermission('administer plans');
    $has_grant_edit_permissions = $account->hasPermission('grant edit on plans to other users');
    return AccessResult::allowedIf($has_grant_edit_permissions || $has_administer_permissions);
  }

}
