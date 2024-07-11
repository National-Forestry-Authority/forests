<?php

namespace Drupal\farm_nfa\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Alter routes for the farm_nfa module.
 *
 * @ingroup farm_nfa
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    foreach ($collection as $name => $route) {
      // Alter JSON:API routes to check for 'access jsonapi routes' permission.
      $defaults = $route->getDefaults();
      if (!empty($defaults['_is_jsonapi']) && !empty($defaults['resource_type'])) {
        $route->setRequirement('_permission', 'access jsonapi routes');

        // Remove DELETE and POST methods from JSON:API routes.
        $methods = $route->getMethods();
        if (in_array('DELETE', $methods) || in_array('POST', $methods)) {
          // We never want to delete or post data.
          $collection->remove($name);
        }
      }
    }
  }
}
