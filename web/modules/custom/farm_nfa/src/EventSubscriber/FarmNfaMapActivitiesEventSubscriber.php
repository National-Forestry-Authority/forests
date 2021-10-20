<?php

namespace Drupal\farm_nfa\EventSubscriber;

use Drupal\farm_map\Event\MapRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * An event subscriber for the MapRenderEvent.
 */
class FarmNfaMapActivitiesEventSubscriber implements EventSubscriberInterface {

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
      // @TODO Inject this
      if (in_array(\Drupal::routeMatch()->getRouteName(), $routes)) {
        if ($plan = \Drupal::service('farm_nfa.referer_plan_loader')->load()) {
          $assets = $plan->get('asset')->referencedEntities();
          foreach ($assets as $asset) {
            // @TODO: Inject this.
            $geometries[] = \Drupal::service('asset.location')->getGeometry($asset);
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
