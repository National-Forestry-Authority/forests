<?php

namespace Drupal\farm_nfa\EventSubscriber;

use Drupal\asset\Event\AssetEvent;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * An event subscriber for the Asset:Update event.
 */
class FarmNfaAssetPresaveEventSubscriber implements EventSubscriberInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new FarmNfaMapActivitiesEventSubscriber.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match.
   */
  public function __construct(RouteMatchInterface $routeMatch) {
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      AssetEvent::PRESAVE => 'onAssetPresave',
    ];
  }

  /**
   * React to the AssetEvent::PRESAVE event.
   *
   * @param \Drupal\asset\Event\AssetEvent $event
   *   The AssetEvent.
   */
  public function onAssetPresave(AssetEvent $event): void {
    // If the update is from a JSON:API request, do not allow the cfr_global_id
    // field to be overwritten.
    $defaults = $this->routeMatch->getRouteObject()->getDefaults();
    if (!empty($defaults['_is_jsonapi']) && !empty($defaults['resource_type'])) {
      $asset = $event->asset;
      if ($asset->bundle() === 'cfr') {
        $original = $asset->original;
        $asset->cfr_global_id->setValue($original->cfr_global_id->value);
      }
    }
  }

}
