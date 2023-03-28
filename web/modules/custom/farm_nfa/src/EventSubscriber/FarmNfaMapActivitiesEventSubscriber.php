<?php

namespace Drupal\farm_nfa\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\farm_location\AssetLocationInterface;
use Drupal\farm_map\Event\MapRenderEvent;
use Drupal\farm_nfa\FarmNfaRefererPlanLoaderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * An event subscriber for the MapRenderEvent.
 */
class FarmNfaMapActivitiesEventSubscriber implements EventSubscriberInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The asset location service.
   *
   * @var \Drupal\farm_location\AssetLocationInterface
   */
  protected $assetLocation;

  /**
   * The service that loads a plan from HTTP_REFERERS.
   *
   * @var \Drupal\farm_nfa\FarmNfaRefererPlanLoaderInterface
   */
  protected $planLoader;

  /**
   * Constructs a new FarmNfaMapActivitiesEventSubscriber.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   * @param \Drupal\farm_location\AssetLocationInterface $asset_location
   *   The asset location service.
   * @param \Drupal\farm_nfa\FarmNfaRefererPlanLoaderInterface $plan_loader
   *   The plan loader service.
   */
  public function __construct(RouteMatchInterface $routeMatch, AssetLocationInterface $asset_location, FarmNfaRefererPlanLoaderInterface $plan_loader) {
    $this->routeMatch = $routeMatch;
    $this->assetLocation = $asset_location;
    $this->planLoader = $plan_loader;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      MapRenderEvent::EVENT_NAME => 'onMapRender',
    ];
  }

  /**
   * React to the MapRenderEvent.
   *
   * @param \Drupal\farm_map\Event\MapRenderEvent $event
   *   The MapRenderEvent.
   */
  public function onMapRender(MapRenderEvent $event) {
    $map_id = $event->getmapType()->id();
    $geometries = [];
    if ($map_id == 'geofield_widget') {
      $routes = [
        'farm_nfa.plan.add_inventory',
        'farm_nfa.plan.add_task',
        'farm_nfa.plan.add_harvest',
      ];
      if (in_array($this->routeMatch->getRouteName(), $routes)) {
        if ($plan = $this->planLoader->load()) {
          $assets = $plan->get('asset')->referencedEntities();
          foreach ($assets as $asset) {
            $geometries[] = $this->assetLocation->getGeometry($asset);
          }
        }
      }
    }

    if (!empty($geometries)) {
      $event->addBehavior('farm_nfa_map_activities');
      $settings[$event->getMapTargetId()]['farm_nfa_map_activities']['geometries'] = [
        'value' => $geometries,
      ];
      $event->addSettings($settings);
    }
  }

}
