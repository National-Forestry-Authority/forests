<?php

namespace Drupal\farm_nfa;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\plan\Entity\Plan;

/**
 * Checks access for displaying plan edit route.
 */
class PlanEditRouteChecker implements AccessInterface {

  /**
   * Checks access for displaying plan edit route.
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
    $has_grant_edit_permissions = $account->hasPermission('grant edit on plans to other users');
    $is_allowed_user = in_array($account->id(), $user_ids);
    $can_access_edit_plan_permissions = ($has_grant_edit_permissions || $is_allowed_user);
    return AccessResult::allowedIf($can_access_edit_plan_permissions);
  }

}
