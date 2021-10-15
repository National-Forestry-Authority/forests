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
    parent::onMapRender($event);

    $farm_nfa_routes = [
      'farm_nfa.plan.add_task'
    ];

    // @TODO inject this.
    if (in_array(\Drupal::routeMatch()->getRouteName(), $farm_nfa_routes)) {
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
