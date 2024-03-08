<?php

namespace Drupal\farm_nfa_natural_forest\Plugin\Block;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a sector block.
 *
 * We need this because bundle computed fields are not exposed to Layout
 * Builder.
 *
 * @see https://www.drupal.org/project/drupal/issues/3034979
 *
 * @Block(
 *   id = "farm_nfa_natural_forest_sector",
 *   admin_label = @Translation("Sector"),
 *   category = @Translation("Farm NFA"),
 * )
 */
final class SectorBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RequestStack $requestStack,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $entity = $this->requestStack->getCurrentRequest()->attributes->get('plan');
    if (!$entity instanceof PlanInterface) {
      return [];
    }

    if (!$entity->hasField('asset') || $entity->get('asset')->isEmpty()) {
      return [];
    }

    $sector = $entity->get('sector')->entity;
    if (!$sector instanceof AssetInterface) {
      return [];
    }
    return [
      '#type' => 'link',
      '#title' => $sector->label(),
      '#url' => $sector->toUrl(),
      '#cache' => [
        'tags' => $sector->getCacheTags(),
        'contexts' => $sector->getCacheContexts(),
      ],
    ];
  }

}
