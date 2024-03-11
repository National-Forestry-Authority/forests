<?php

declare(strict_types=1);

namespace Drupal\farm_nfa_cfr;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Utility service for CFR asssets.
 */
final class CfrUtilities implements ContainerInjectionInterface {

  /**
   * The current CFR asset.
   *
   * @var int
   */
  protected $asset;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs the service object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->asset = $route_match->getParameter('asset');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Return the plan that the CFR belongs to.
   */
  public function getPlan(): ?PlanInterface {
    $storage = $this->entityTypeManager->getStorage('plan');
    $plan_results = $storage->getQuery()
      ->condition('type', 'natural')
      ->condition('asset.entity.id', $this->asset->id())
      ->accessCheck(FALSE)
      ->execute();
    if ($plan_results) {
      /** @var \Drupal\plan\Entity\PlanInterface $plan */
      $plan = $storage->load(reset($plan_results));
      return $plan;
    }
    return NULL;
  }

}
