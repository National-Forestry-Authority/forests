<?php

namespace Drupal\farm_nfa;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\plan\Entity\PlanInterface;

/**
 * Builds the breadcrumbs for assets and plans.
 */
class BreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new BreadcrumbBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $asset = $route_match->getParameter('asset');
    $plan = $route_match->getParameter('plan');
    $view = $route_match->getParameter('view_id');
    $log = $route_match->getParameter('log');
    if ($asset || $plan || $view == 'farm_asset' || $view == 'farm_plan' || $log) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $links = [];
    $asset = $route_match->getParameter('asset');
    $plan = $route_match->getParameter('plan');
    $log = $route_match->getParameter('log');
    $breadcrumb->addCacheContexts(['url.path']);
    $view = $route_match->getParameter('view_id');
    $links[] = Link::createFromRoute('Home', '<front>');
    $current_tab = $this->getCurrentTab();
    if ($asset instanceof EntityInterface || is_numeric($asset)) {
      $asset = is_numeric($asset) ? $this->entityTypeManager->getStorage('asset')->load($asset) : $asset;
      // If the asset parameter is a valid entity.
      $asset_type = $asset->bundle();
      if ($asset_type == 'land') {
        // If the asset type is 'land', further refine it based on land type as
        // land type can be either 'Sector' or 'Range'.
        $asset_type = $asset->get('land_type')->value;
      }
      // @todo Records should link to a Records landing page, not the home page.
      $links[] = Link::createFromRoute('Records', '<front>');
      $links[] = Link::createFromRoute('Locations', 'entity.asset.collection');
      $asset_type = $asset_type == 'cfr' ? strtoupper($asset_type) : ucfirst($asset_type);
      // The breadcrumb on the assets overview page is plural, so we pluralize
      // the fragment on the individual asset page for consistency.
      $links[] = Link::createFromRoute($asset_type . 's', 'farm_nfa.assets.asset', ['asset' => strtolower($asset_type)]);
      if ($current_tab) {
        // If there is a current tab, add it to the breadcrumb as we are a sub
        // route.
        $links[] = Link::createFromRoute($asset->label(), 'entity.asset.canonical', ['asset' => $asset->id()]);
        $links[] = Link::createFromRoute(ucfirst($current_tab), '<none>');
      }
      else {
        // If no current tab, just link to the asset.
        $links[] = Link::createFromRoute($asset->label(), '<none>');
      }
    }
    elseif ($plan instanceof PlanInterface) {
      // If the plan parameter is a valid plan entity.
      $plan_type = $plan->bundle();
      $plan_text = $plan_type == 'natural' ? 'Natural forest' : '';
      $links[] = Link::createFromRoute('Plans', 'entity.plan.collection');
      $links[] = Link::createFromRoute($plan_text, 'farm_nfa.plans.plan', ['plan' => $plan_type]);
      if ($current_tab) {
        // If there is a current tab, add it to the breadcrumb as we are on a
        // sub route.
        $links[] = Link::createFromRoute($plan->label(), 'entity.plan.canonical', ['plan' => $plan->id()]);
        $links[] = Link::createFromRoute(ucfirst($current_tab), '<none>');
      }
      else {
        // If no current tab, just link to the plan.
        $links[] = Link::createFromRoute($plan->label(), '<none>');
      }
    }
    elseif ($view == 'farm_asset') {
      // If the view is related to farm assets.
      $view_type = $route_match->getParameter('arg_0') ? $route_match->getParameter('arg_0') : $route_match->getParameter('display_id');
      $links[] = Link::createFromRoute('Records', '<front>');
      // If there is a specific view type, add it to the breadcrumb.
      if ($view_type && $view_type != 'page') {
        $view_type = $view_type == 'cfr' ? strtoupper($view_type) : ucfirst($view_type);
        $links[] = Link::createFromRoute('Locations', 'entity.asset.collection');
        // The breadcrumb on the assets overview page is plural, so we
        // pluralize the fragment on the individual asset page for consistency.
        $links[] = Link::createFromRoute($view_type . 's', '<none>');
      }
      else {
        // If no specific view type, just link to the 'Locations' page.
        $links[] = Link::createFromRoute('Locations', '<none>');
      }
    }
    elseif ($view == 'farm_plan') {
      // If the view is related to farm plans.
      $plan_type = $route_match->getParameter('arg_0');
      $plan_text = $plan_type == 'natural' ? 'Natural forest' : '';
      $links[] = Link::createFromRoute('Plans', 'entity.plan.collection');
      $links[] = Link::createFromRoute($plan_text, '<none>');
    }
    elseif ($log instanceof EntityInterface) {
      // If the log parameter is a valid entity.
      $task_type = $log->bundle();
      $links[] = Link::createFromRoute('Records', '<front>');
      $links[] = Link::createFromRoute('Tasks', 'farm_nfa.tasks');
      $links[] = Link::createFromRoute(ucfirst($task_type), 'farm_nfa.task_type', ['task_type' => $task_type]);
      $links[] = Link::createFromRoute($log->get('name')->value, '<none>');
    }
    return $breadcrumb->setLinks($links);
  }

  /**
   * Helper function to get the current tab.
   *
   * @return string
   *   The current tab.
   */
  private function getCurrentTab() {
    $current_url = \Drupal::request()->getRequestUri();
    $current_url = rtrim($current_url, '/');
    $url_parts = explode('/', $current_url);
    $last_part = end($url_parts);
    return is_numeric($last_part) ? NULL : $last_part;
  }

}
