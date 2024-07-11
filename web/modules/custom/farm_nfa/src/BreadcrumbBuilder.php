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
    $route = $route_match->getRouteObject();
    $asset = $route_match->getParameter('asset');
    $plan = $route_match->getParameter('plan');
    if ($asset instanceof EntityInterface || $plan instanceof PlanInterface) {
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
    $links[] = Link::createFromRoute('Home', '<front>');
    if ($asset instanceof EntityInterface) {
      $asset_type = $asset->bundle();
      $links[] = Link::createFromRoute('Records', '<front>');
      $links[] = Link::createFromRoute('Locations', 'entity.asset.collection');
      $asset_type = $asset_type == 'cfr' ? strtoupper($asset_type) : ucfirst($asset_type);
      $links[] = Link::createFromRoute($asset_type, 'farm_nfa.assets.asset', ['asset' => strtolower($asset_type)]);
      $links[] = Link::createFromRoute($asset->label(), 'entity.asset.canonical', ['asset' => $asset->id()]);
    }
    elseif ($plan instanceof PlanInterface) {
      $plan_type = $plan->bundle();
      $plan_text = $plan_type == 'natural' ? 'Natural forest' : 'Plantation';
      $links[] = Link::createFromRoute('Plans', 'entity.plan.collection');
      $links[] = Link::createFromRoute($plan_text, 'farm_nfa.plans.plan', ['plan' => $plan_type]);
      $links[] = Link::createFromRoute($plan->label(), 'entity.plan.canonical', ['plan' => $plan->id()]);
    }
    return $breadcrumb->setLinks($links);
  }

}
