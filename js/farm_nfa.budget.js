(function ($) {
  Drupal.behaviors.farm_nfa_forest_budget = {
    attach: function (context, settings) {
      var form_id = 'farm-nfa-forest-plan-budget-form';
      $('#' + form_id + ' .cost', context).on('keyup', function() {
        var row = $(this).closest('tr');
        Drupal.behaviors.farm_nfa_forest_budget.calculateRowTotal(row);
        Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal();
      });
      $('#' + form_id + ' .qty', context).on('keyup', function() {
        var row = $(this).closest('tr');
        Drupal.behaviors.farm_nfa_forest_budget.calculateRowTotal(row);
        Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal();
      });
    },
    calculateRowTotal: function(row) {
      var cost = $('.cost', row).val();
      var qty = $('.qty', row).val();
      var total = cost * qty;
      if (total === total) {
        $('.total', row).val(total);
      }
    }
  };
}(jQuery));
