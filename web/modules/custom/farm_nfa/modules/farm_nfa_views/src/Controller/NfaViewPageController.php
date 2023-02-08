<?php

namespace Drupal\farm_nfa_views\Controller;

use Drupal\views\Routing\ViewPageController;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Overrides selected FarmOS views.
 */
class NfaViewPageController extends ViewPageController {

  /**
   * {@inheritdoc}
   */
  public function handle($view_id, $display_id, RouteMatchInterface $route_match) {
    $config = \Drupal::config('farm_nfa_views.settings');
    if ($routes = $config->get('overridden_routes')) {
      $list = array_filter(preg_split("/(\r\n|\n|\r)/", $config->get('overridden_routes')));
      foreach ($list as $route_pair) {
        $matches = [];
        if (preg_match('/(.*)\|(.*)/', $route_pair, $matches)) {
          $view_id = trim($matches[2]);
        }
      }
    }
    return parent::handle($view_id, $display_id, $route_match);
  }

}
