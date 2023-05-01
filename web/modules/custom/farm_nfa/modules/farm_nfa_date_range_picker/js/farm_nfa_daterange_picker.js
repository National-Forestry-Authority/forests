(function($) {
  Drupal.behaviors.daterangepicker = {
    attach: function(context, settings) {      
      $('input.daterangepicker').each(function(index, element) {
        const daterangepickerCss = document.createElement('link');
        daterangepickerCss.href = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css';
        daterangepickerCss.rel = 'stylesheet';
        document.getElementsByTagName('head')[0].appendChild(daterangepickerCss);

        // Enable date range for all inputs with the given class.
        $(this).daterangepicker({
          initialText: settings.daterangepicker.initialText,
          applyButtonText: settings.daterangepicker.applyButtonText,
          clearButtonText: settings.daterangepicker.clearButtonText,
          cancelButtonText: settings.daterangepicker.cancelButtonText,
          rangeSplitter: settings.daterangepicker.rangeSplitter,
          dateFormat: settings.daterangepicker.dateFormat,
          altFormat: settings.daterangepicker.altFormat,
          datepickerOptions : {
            numberOfMonths: settings.daterangepicker.datepickerOptions.numberOfMonths
          }
        });

        // Calculate if we need to set a default value.
        if (settings.daterangepicker.defaultValue instanceof Object) {
          var range = {};
          range.start = new Date(settings.daterangepicker.defaultValue.start);
          if (settings.daterangepicker.defaultValue.end.length > 0) {
            range.end = new Date(settings.daterangepicker.defaultValue.end);
          }
          $(this).daterangepicker('setRange', range);
        }
      });
    }
  };
})(jQuery);
