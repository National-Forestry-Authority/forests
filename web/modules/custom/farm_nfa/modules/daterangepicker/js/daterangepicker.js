(function($) {
  Drupal.behaviors.daterangepicker = {
    attach: function (context, settings) {
      $('input.daterangepicker').each(function (index, element) {
        // Enable date range for all inputs with the given class.
        $(this).daterangepicker({
          initialText: settings.daterangepicker.initial_text,
          applyButtonText: settings.daterangepicker.apply_button_text,
          clearButtonText: settings.daterangepicker.clear_button_Text,
          cancelButtonText: settings.daterangepicker.cancel_button_text,
          rangeSplitter: settings.daterangepicker.range_splitter,
          dateFormat: settings.daterangepicker.date_format,
          altFormat: settings.daterangepicker.alt_format,
          datepickerOptions : {
            numberOfMonths: settings.daterangepicker.date_picker_options.number_of_months
          }
        });

        // Calculate if we need to set a default value.
        if (settings.daterangepicker.default_value instanceof Object) {
          var range = {};
          range.start = new Date(settings.daterangepicker.default_value.start);
          if (settings.daterangepicker.default_value.end.length > 0) {
            range.end = new Date(settings.daterangepicker.default_value.end);
          }
          $(this).daterangepicker('setRange', range);
        }

      });
    }
  };
})(jQuery);
