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

}
