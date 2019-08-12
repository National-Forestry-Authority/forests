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
      $('#' + form_id + ' .total', context).on('keyup', function() {
        Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal();
      });
      this.calculateGrandTotal();
    },
    calculateRowTotal: function(row) {
      var cost = $('.cost', row).val();
      var qty = $('.qty', row).val();
      var total = cost * qty;
      if (total === total) {
        $('.total', row).val(total);
      }
    },
    calculateGrandTotal: function() {
      var form_id = 'farm-nfa-forest-plan-budget-form';
      var total = 0;
      $('#' + form_id + ' table tr').each(function() {
        var rowTotal = parseFloat($(this).find('input.total').val());
        if (!isNaN(rowTotal)) {
          total += rowTotal;
        }
      });
      if (!isNaN(total)) {
        $('#budget-total').html('<strong>Budget Total</strong>: ' + total);
      }
      else {
        $('#budget-total').html('');
      }
    }
  };
}(jQuery));
