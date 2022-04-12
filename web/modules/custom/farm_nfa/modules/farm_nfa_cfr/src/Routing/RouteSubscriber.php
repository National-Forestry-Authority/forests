<?php

namespace Drupal\farm_nfa_cfr\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Changes the title for all land assets.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('farm.asset.locations')) {
      $route->setDefault('_title_callback', 'Drupal\farm_nfa_cfr\Controller\GetTitleController::getTitle');
    }
  }

}
