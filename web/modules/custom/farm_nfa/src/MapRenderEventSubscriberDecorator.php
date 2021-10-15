<?php

namespace Drupal\farm_nfa;

use Drupal\farm_map\Event\MapRenderEvent;
use Drupal\farm_ui_map\EventSubscriber\MapRenderEventSubscriber;

/**
 * Decorates MapRenderEventSubscriber to exclude some routes.
 */
class MapRenderEventSubscriberDecorator extends MapRenderEventSubscriber {

  /**
   * React to the MapRenderEvent.
   *
   * @param \Drupal\farm_map\Event\MapRenderEvent $event
   *   The MapRenderEvent.
   */
  public function onMapRender(MapRenderEvent $event) {
    $excluded_routes = [
      'farm_nfa.plan.add_task'
    ];

    if (!in_array(\Drupal::routeMatch()->getRouteName(), $excluded_routes)) {
      parent::onMapRender($event);
    }
  }

}
