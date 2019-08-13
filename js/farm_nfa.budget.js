(function ($) {
  Drupal.behaviors.farm_nfa_forest_budget = {
    attach: function (context, settings) {
      var form_id = 'farm-nfa-forest-plan-budget-form';
      var expense_table = $('#' + form_id + ' table.expense', context);
      this.attachTableBehaviors(expense_table, 'expense');
      var income_table = $('#' + form_id + ' table.income', context);
      this.attachTableBehaviors(income_table, 'income');
    },
    attachTableBehaviors: function(table, type) {
      $('.value', table).on('keyup', function() {
        var row = $(this).closest('tr');
        var table = row.closest('table');
        Drupal.behaviors.farm_nfa_forest_budget.calculateRowTotal(row);
        Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal(table, type);
      });
      $('.qty', table).on('keyup', function() {
        var row = $(this).closest('tr');
        var table = row.closest('table');
        Drupal.behaviors.farm_nfa_forest_budget.calculateRowTotal(row);
        Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal(table, type);
      });
      $('.total', table).on('keyup', function() {
        var table = row.closest('table');
        Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal(table, type);
      });
      Drupal.behaviors.farm_nfa_forest_budget.calculateGrandTotal(table, type);
    },
    calculateRowTotal: function(row) {
      var value = $('.value', row).val();
      var qty = $('.qty', row).val();
      var total = value * qty;
      if (total === total) {
        $('.total', row).val(total);
      }
    },
    calculateGrandTotal: function(table, type) {
      var total = 0;
      $('tr', table).each(function() {
        var rowTotal = parseFloat($(this).find('input.total').val());
        if (!isNaN(rowTotal)) {
          total += rowTotal;
        }
      });
      if (!isNaN(total)) {
        $('.budget-' + type + '-total').html('<strong>Total</strong>: ' + total);
      }
      else {
        $('.budget-' + type + '-total').html('');
      }
    }
  };
}(jQuery));
