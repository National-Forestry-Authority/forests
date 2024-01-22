<?php

namespace Drupal\farm_nfa\Plugin\field_group\FieldGroupFormatter;

use Drupal\field_group\Plugin\field_group\FieldGroupFormatter\Tab;


/**
 * Plugin implementation of the 'tab' formatter.
 *
 * @FieldGroupFormatter(
 *   id = "program_tab",
 *   label = @Translation("Program Tab"),
 *   description = @Translation("This fieldgroup renders the content as a tab."),
 *   format_types = {
 *     "open",
 *     "closed",
 *   },
 *   supported_contexts = {
 *     "form",
 *     "view",
 *   }
 * )
 */
class ProgramTab extends Tab {

  /**
   * {@inheritdoc}
   */
  public function process(&$element, $processed_object) {
    parent::process($element, $processed_object);

    // Move the Program Tab group from its Program Tabs parent to its Tabs
    // ancestor.
    $parent_tabs_name = $element['#group'];
    $parent_tabs = $processed_object['#fieldgroups'][$parent_tabs_name];
    $grandparent_tab_name = $parent_tabs->parent_name;
    $grandparent_tab = $processed_object['#fieldgroups'][$grandparent_tab_name];
    $element['#group'] = $grandparent_tab->parent_name;
    // @todo figure out way to calculate the correct weight to position the tabs
    // in the same order that they are configured with.
    $element['#weight'] = $grandparent_tab->weight;
    $element['#attached']['library'][] = 'farm_nfa/farm_nfa_program_tabs';
  }

}
