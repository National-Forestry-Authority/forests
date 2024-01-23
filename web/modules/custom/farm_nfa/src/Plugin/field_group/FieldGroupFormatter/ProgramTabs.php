<?php

namespace Drupal\farm_nfa\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\Plugin\field_group\FieldGroupFormatter\Tabs;

/**
 * Plugin implementation of the 'Program Tabs' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "program_tabs",
 *   label = @Translation("Program Tabs"),
 *   description = @Translation("This fieldgroup is a child of a Tabs group and renders its child Program Tabs in its own tabs wrapper."),
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class ProgramTabs extends Tabs {

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $form = parent::settingsForm();
    unset($form['width_breakpoint']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultContextSettings($context) {
    $settings = parent::defaultContextSettings($context);
    unset($settings['width_breakpoint']);
    return $settings;
  }

}
