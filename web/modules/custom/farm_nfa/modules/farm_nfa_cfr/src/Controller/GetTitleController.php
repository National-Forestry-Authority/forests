<?php

namespace Drupal\farm_nfa_cfr\Controller;

use Drupal\asset\Entity\AssetInterface;
use Drupal\Core\Controller\ControllerBase;

/**
 * Defines GetTitleController class.
 *
 * @see \Drupal\farm_ui_location\Controller\AssetReorderController::getTitle()
 *
 */
class GetTitleController extends ControllerBase {

  /**
   * Generate the page title.
   *
   * @param \Drupal\asset\Entity\AssetInterface|null $asset
   *   Optionally specify the parent asset that this page is being built for.
   *
   * @return string
   *   Returns the translated page title.
   */
  public function getTitle(AssetInterface $asset = NULL) {
    if (!empty($asset)) {
      return $this->t('Sectors & CFRs in %location', ['%location' => $asset->label()]);
    }
    return $this->t('Sectors & CFRs');
  }

}
