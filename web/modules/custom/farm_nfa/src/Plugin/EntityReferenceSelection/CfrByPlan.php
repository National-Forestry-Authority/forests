<?php

namespace Drupal\farm_nfa\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\farm_nfa\FarmNfaRefererPlanLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Asset by plan ER selection.
 *
 * @EntityReferenceSelection(
 *   id = "farm_nfa_cfr_by_plan",
 *   label = @Translation("CFR by plan"),
 *   entity_types = {"asset"},
 *   group = "farm_nfa_cfr_by_plan",
 *   weight = 10
 * )
 */
class CfrByPlan extends DefaultSelection {

  /**
   * The service that loads a plan from HTTP_REFERERS.
   *
   * @var \Drupal\farm_nfa\FarmNfaRefererPlanLoaderInterface
   */
  protected $planLoader;

  /**
   * Constructs a new FarmNfaMapActivitiesEventSubscriber.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info service.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\farm_nfa\FarmNfaRefererPlanLoaderInterface $plan_loader
   *   The plan loader service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, AccountInterface $current_user, EntityFieldManagerInterface $entity_field_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, EntityRepositoryInterface $entity_repository, FarmNfaRefererPlanLoaderInterface $plan_loader) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $module_handler, $current_user, $entity_field_manager, $entity_type_bundle_info, $entity_repository);
    $this->planLoader = $plan_loader;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('current_user'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity.repository'),
      $container->get('farm_nfa.referer_plan_loader')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);
    if ($plan = $this->planLoader->load()) {
      // Get the CFR assets in the current plan entity.
      $cfrs = $plan->get('asset')->getValue();

      // Add a condition to the query to only select CFR assets in the current plan entity.
      $query->condition('id', array_column($cfrs, 'target_id'), 'IN');    }

    return $query;
  }

}
