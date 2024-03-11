<?php

namespace Drupal\farm_nfa_cfr\Controller;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\farm_nfa_cfr\CfrUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns the content of the CFR management tab.
 */
class ReserveTabsController extends ControllerBase {

  /**
   * The CFR utilities service object.
   *
   * @var \Drupal\farm_nfa_cfr\CfrUtilities
   */
  protected $utilities;

  /**
   * ReserveTabsController constructor.
   *
   * @param \Drupal\farm_nfa_cfr\CfrUtilities $utilities
   *   The CFR utilities service object.
   */
  public function __construct(CfrUtilities $utilities) {
    $this->utilities = $utilities;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('farm_nfa_cfr.utilities'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(AssetInterface $asset = NULL, $log_types = []) {
    $build = [];

    $plan = $this->utilities->getPlan();
    $build['logs'] = views_embed_view('cfr_logs', 'embed', $plan->id(), implode('+', $log_types), $asset->id());
    return $build;
  }

}
