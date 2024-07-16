<?php

namespace Drupal\farm_nfa;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\plan\Entity\PlanInterface;

/**
 * Breadcrumb builder class for generating breadcrumbs.
 *
 * Builds breadcrumbs within the context of the abv_app Drupal package.
 *
 * @package Drupal\abv_app
 */
class BreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $asset = $route_match->getParameter('asset');
    $plan = $route_match->getParameter('plan');
    $view = $route_match->getParameter('view_id');
    if ($asset || $plan || $view == 'farm_asset' || $view == 'farm_plan') {
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
    $breadcrumb->addCacheContexts(['url.path']);
    $view = $route_match->getParameter('view_id');
    $links[] = Link::createFromRoute('Home', '<front>');
    $current_tab = $this->getCurrentTab();
    if ($asset instanceof EntityInterface) {
      $asset_type = $asset->bundle();
      if ($asset_type == 'land') {
        $asset_type = $asset->get('land_type')->value;
      }
      $links[] = Link::createFromRoute('Records', '<front>');
      $links[] = Link::createFromRoute('Locations', 'entity.asset.collection');
      $asset_type = $asset_type == 'cfr' ? strtoupper($asset_type) : ucfirst($asset_type);
      $links[] = Link::createFromRoute($asset_type, 'farm_nfa.assets.asset', ['asset' => strtolower($asset_type)]);
      if ($current_tab) {
        $links[] = Link::createFromRoute($asset->label(), 'entity.asset.canonical', ['asset' => $asset->id()]);
        $links[] = Link::createFromRoute(strtoupper($current_tab), '<none>');
      }
      else {
        $links[] = Link::createFromRoute($asset->label(), '<none>');
      }
    }
    elseif ($plan instanceof PlanInterface) {
      $plan_type = $plan->bundle();
      $plan_text = $plan_type == 'natural' ? 'Natural forest' : 'Plantation';
      $links[] = Link::createFromRoute('Plans', 'entity.plan.collection');
      $links[] = Link::createFromRoute($plan_text, 'farm_nfa.plans.plan', ['plan' => $plan_type]);
      if ($current_tab) {
        $links[] = Link::createFromRoute($plan->label(), 'entity.plan.canonical', ['plan' => $plan->id()]);
        $links[] = Link::createFromRoute(strtoupper($current_tab), '<none>');
      }
      else {
        $links[] = Link::createFromRoute($plan->label(), '<none>');
      }
    }
    elseif ($view == 'farm_asset') {
      $view_type = $route_match->getParameter('arg_0') ? $route_match->getParameter('arg_0') : $route_match->getParameter('display_id');
      $links[] = Link::createFromRoute('Records', '<front>');
      $links[] = Link::createFromRoute('Locations', 'entity.asset.collection');
      if ($view_type) {
        $view_type = $view_type == 'cfr' ? strtoupper($view_type) : ucfirst($view_type);
        $links[] = Link::createFromRoute($view_type . 's', '<none>');
      }
    }
    elseif ($view == 'farm_plan') {
      $plan_type = $route_match->getParameter('arg_0');
      $plan_text = $plan_type == 'natural' ? 'Natural forest' : 'Plantation';
      $links[] = Link::createFromRoute('Plans', 'entity.plan.collection');
      $links[] = Link::createFromRoute($plan_text, '<none>');
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
