<?php

namespace Drupal\farm_nfa_views\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Override FarmOS views.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new NodeAdminRouteSubscriber.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Get the routes that will be overridden by Farm NFA views.
    $config = $this->configFactory->get('farm_nfa_views.settings');
    if ($routes = $config->get('overridden_routes')) {
      $list = array_filter(preg_split("/(\r\n|\n|\r)/", $routes));
      foreach ($list as $route_pair) {
        $matches = [];
        if (preg_match('/(.*)\|(.*)/', $route_pair, $matches)) {
          $original = trim($matches[1]);
          if ($route = $collection->get($original)) {
            // Alter the view page controller of overridden routes.
            $route->setDefault('_controller', '\Drupal\farm_nfa_views\Controller\NfaViewPageController::handle');
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Change the weight of the route so our controller takes priority over
    // any other ViewPageController.
    $events[RoutingEvents::ALTER][] = ['onAlterRoutes', -500];

    return $events;
  }

}
