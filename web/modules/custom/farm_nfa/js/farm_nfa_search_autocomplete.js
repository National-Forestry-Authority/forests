/**
 * @file
 * Drupal autocomplete override for Farm NFA.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  // As a safety precaution, bail if the Drupal Core autocomplete framework is
  // not present.
  if (!Drupal.autocomplete) {
    return;
  }

  /**
   * Override the autocomplete behaviour to do a quickjump instead.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the autocomplete behaviors.
   */
  Drupal.behaviors.farmNfaSearchAutocomplete = {
    attach: function (context, settings) {
      // Find our field with autocomplete settings
      $(context)
        .find('.ui-autocomplete-input[data-farm-nfa-autocomplete-search]')
        .each(function () {
          let uiAutocomplete = $(this).data('ui-autocomplete');
          if (!uiAutocomplete) {
            return;
          }
          let $element = uiAutocomplete.menu.element;
          $element.addClass('farm-nfa-autocomplete-search');

          // Override the select callback to do a quickjump.
          uiAutocomplete.options.select = function (event, ui) {
            if (ui.item.url) {
              location.href = ui.item.url;
              return false;
            }
          };
        });
    }
  };

})(jQuery, Drupal, drupalSettings);
