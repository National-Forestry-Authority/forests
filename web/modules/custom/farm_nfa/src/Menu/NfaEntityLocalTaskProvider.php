<?php

namespace Drupal\farm_nfa\Menu;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity\Menu\DefaultEntityLocalTaskProvider;

/**
 * Provides a set of tasks to view, edit and duplicate an entity.
 */
class NfaEntityLocalTaskProvider extends DefaultEntityLocalTaskProvider {

  /**
   * {@inheritdoc}
   */
  public function buildLocalTasks(EntityTypeInterface $entity_type) {
    $tasks = parent::buildLocalTasks($entity_type);

    // Remove the duplicate canonical task, as it doesn't seem to be possible to
    // display subtasks in the one coming from the link template, we need to
    // keep the one from farm_nfa.links.task.yml.
    unset($tasks['entity.plan.canonical']);
    return $tasks;
  }

}
