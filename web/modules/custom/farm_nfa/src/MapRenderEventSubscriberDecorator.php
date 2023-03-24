<?php

namespace Drupal\farm_nfa;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\farm_map\Event\MapRenderEvent;
use Drupal\farm_map\LayerStyleLoaderInterface;
use Drupal\farm_ui_map\EventSubscriber\MapRenderEventSubscriber;

/**
 * Decorates MapRenderEventSubscriber to exclude some routes.
 */
class MapRenderEventSubscriberDecorator extends MapRenderEventSubscriber {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new MapRenderEventSubscriberDecorator.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\farm_map\LayerStyleLoaderInterface $layer_style_loader
   *   The layer style loader service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LayerStyleLoaderInterface $layer_style_loader, RouteMatchInterface $routeMatch) {
    parent::__construct($entity_type_manager, $layer_style_loader);
    $this->routeMatch = $routeMatch;
  }

  /**
   * React to the MapRenderEvent.
   *
   * @param \Drupal\farm_map\Event\MapRenderEvent $event
   *   The MapRenderEvent.
   */
  public function onMapRender(MapRenderEvent $event) {
    parent::onMapRender($event);

    $farm_nfa_routes = [
      'farm_nfa.plan.add_task',
    ];

    if (in_array($this->routeMatch->getRouteName(), $farm_nfa_routes)) {
      $settings[$event->getMapTargetId()]['asset_type_layers']['all_locations'] = [
        'label' => $this->t('All locations'),
        'filters' => [
          'is_location' => 1,
        ],
        'color' => 'grey',
        'zoom' => FALSE,
      ];
      $event->addSettings($settings);
    }
  }

}
