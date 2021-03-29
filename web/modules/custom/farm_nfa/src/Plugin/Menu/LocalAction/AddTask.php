<?php

namespace Drupal\farm_nfa\Plugin\Menu\LocalAction;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Menu\LocalActionDefault;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a local task that opens an offcanvas form.
 */
class AddTask extends LocalActionDefault {
  /**
   * {@inheritdoc}
   */
  public function getOptions(RouteMatchInterface $route_match) {
    $options = parent::getOptions($route_match);

    $options['attributes']['class'][] = 'use-ajax';
    $options['attributes']['data-dialog-type'] = 'dialog';
    $options['attributes']['data-dialog-renderer'] = 'off_canvas';
    $options['attributes']['data-dialog-options'] = Json::encode([
      'width' => '50%',
    ]);
    return $options;
  }

}
