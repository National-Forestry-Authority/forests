<?php

namespace Drupal\farm_nfa\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\farm_map\Event\MapRenderEvent;
use Drupal\farm_map\layerStyleLoaderInterface;
use Drupal\plan\Entity\Plan;
use Drupal\plan\Entity\PlanInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * An event subscriber for the MapRenderEvent.
 */
class FarmNfaMapActivitiesEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * Asset types.
   *
   * @var \Drupal\asset\Entity\AssetTypeInterface[]
   */
  protected $assetTypes;

  /**
   * The layer style loader service.
   *
   * @var \Drupal\farm_map\layerStyleLoader
   */
  protected $layerStyleLoader;

  /**
   * MapRenderEventSubscriber Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\farm_map\layerStyleLoaderInterface $layer_style_loader
   *   The layer style loader service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, layerStyleLoaderInterface $layer_style_loader) {
    $this->assetTypes = $entity_type_manager->getStorage('asset_type')->loadMultiple();
    $this->layerStyleLoader = $layer_style_loader;
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
      // @TODO Inject this
      if (\Drupal::routeMatch()->getRouteName()== 'farm_nfa.plan.add_task') {
        $plan_id = $this->getPlanId();
        if (!empty($plan_id) && is_numeric($plan_id)) {
          $plan = Plan::load($plan_id);
          if ($plan instanceof PlanInterface) {
            $assets = $plan->get('asset')->referencedEntities();
            foreach ($assets as $asset) {
              $geometries[] = \Drupal::service('asset.location')->getGeometry($asset);
            }
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

  /**
   * Get the plan id from the URL referer.
   *
   * Because the form is displayed on a
   *
   * @return mixed|string|null
   *  Plan id.
   */
  protected function getPlanId() {
    // @TODO Inject this
    $referer = \Drupal::requestStack()->getCurrentRequest()->server->get('HTTP_REFERER');
    $referer_path = parse_url($referer, PHP_URL_PATH);
    $referer_partial_path = substr($referer_path, strlen(base_path()));
    $referer_args = explode('/', $referer_partial_path);
    return isset($referer_args[1]) ? $referer_args[1] : NULL;
  }

}
