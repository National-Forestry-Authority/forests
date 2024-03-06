/**
 * @file
 * Adjust Program Tabs and Program Tab field groups.
 **/
(function (Drupal) {
  Drupal.behaviors.programTabs = {
    attach: function attach(context) {
      once('program-tab', '.field-group-program-tab', context).forEach(function (element) {
        // Find the tab link and reduce its font weight to distinguish it from
        // its parent tab.
        var tab_href = element.getAttribute('data-drupal-selector');
        element.closest('.vertical-tabs').querySelector('[href="#' + tab_href + '"] .vertical-tabs__menu-link-content').style.fontWeight = 300;
      });
    }
  };

})(Drupal, drupalSettings);
