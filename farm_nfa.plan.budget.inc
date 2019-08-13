<?php

/**
 * @file
 * NFA forest plan budget form.
 */

/**
 * Forest plan budget form.
 */
function farm_nfa_forest_plan_budget_form($form, &$form_state, $plan) {

  // Start an empty form array.
  $form = array(
    '#tree' => TRUE,
  );

  // Set the page title.
  drupal_set_title('Budget');

  // Add CSS.
  drupal_add_css(drupal_get_path('module', 'farm_nfa') . '/css/farm_nfa.budget.css');

  // Add JS.
  drupal_add_js(drupal_get_path('module', 'farm_nfa') . '/js/farm_nfa.budget.js');

  // Add the plan to the form values, for use in form submission.
  $form['plan'] = array(
    '#type' => 'value',
    '#value' => $plan,
  );

  // Create a fieldset for expenses.
  $form['expense'] = array(
    '#type' => 'fieldset',
    '#title' => t('Expenses'),
    '#description' => t('Record expenses in the table below. Expense lines will be automatically added for each activity that is created on the Planting, Management, and Harvest tabs.'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#theme' => 'farm_nfa_forest_plan_budget_fieldset',
  );

  // Add the budget items sub-form for expenses.
  $form['expense'] += farm_nfa_forest_plan_budget_items_form($plan->id, 'expense');

  // Projected cost and income fields.
  $projection_query = 'SELECT * FROM {farm_nfa_budget_projection} WHERE plan_id = :plan_id AND type = :type';
  $projections = array(
    'expense' => '',
    'income' => '',
  );
  foreach ($projections as $type => &$projection) {
    $args = array(
      ':plan_id' => $plan->id,
      ':type' => $type,
    );
    $row = db_query($projection_query, $args)->fetch();
    if (!empty($row->value)) {
      $projection = $row->value;
    }
  }
  $form['projections'] = array(
    '#type' => 'fieldset',
    '#title' => t('Projections'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['projections']['expense'] = array(
    '#type' => 'textfield',
    '#title' => t('Projected cost'),
    '#default_value' => $projections['expense'],
    '#element_validate' => array('element_validate_number'),
  );
  $form['projections']['income'] = array(
    '#type' => 'textfield',
    '#title' => t('Projected income'),
    '#default_value' => $projections['income'],
    '#element_validate' => array('element_validate_number'),
  );

  // Submit button.
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );

  // Return the form.
  return $form;
}

/**
 * Forest plan budget items sub-form.
 *
 * @param $plan_id
 *   The plan ID.
 * @param $type
 *   The budget item type (income or expense).
 *
 * @return array
 *   Returns a form array for use inside another form.
 */
function farm_nfa_forest_plan_budget_items_form($plan_id, $type) {

  // Start a form array.
  $form = array();

  // Define the date format used throughout the form.
  $date_format = 'Y-m-d';

  // Load expense budget items from the database.
  $budget_items = farm_nfa_budget_items($plan_id, $type);

  // Add one blank one for creating new items.
  $new_item = array(
    'id' => NULL,
  );
  $budget_items[] = $new_item;

  // Iterate through the budget items and create form fields.
  foreach ($budget_items as $key => $item) {

    // Budget item ID (hidden).
    $form['items'][$key]['id'] = array(
      '#type' => 'hidden',
      '#value' => !empty($item['id']) ? $item['id'] : NULL,
    );

    // Plan ID (hidden).
    $form['items'][$key]['plan_id'] = array(
      '#type' => 'hidden',
      '#value' => $plan_id,
    );

    // Description.
    $form['items'][$key]['description'] = array(
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['description']) ? $item['description'] : '',
    );

    // Type.
    $form['items'][$key]['type'] = array(
      '#type' => 'value',
      '#value' => !empty($item['type']) ? $item['type'] : '',
    );

    // Category.
    $form['items'][$key]['category'] = array(
      '#type' => 'textfield',
      '#title' => t('Category'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['category']) ? $item['category'] : '',
    );

    // Unit cost.
    $form['items'][$key]['unit_cost'] = array(
      '#type' => 'textfield',
      '#title' => t('Unit cost'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['unit_cost']) ? $item['unit_cost'] : '',
      '#element_validate' => array('element_validate_number'),
      '#attributes' => array(
        'class' => array('cost'),
      ),
    );

    // Quantity.
    $form['items'][$key]['quantity'] = array(
      '#type' => 'textfield',
      '#title' => t('Quantity'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['quantity']) ? $item['quantity'] : '',
      '#element_validate' => array('element_validate_number'),
      '#attributes' => array(
        'class' => array('qty'),
      ),
    );

    // Total cost.
    $form['items'][$key]['total_cost'] = array(
      '#type' => 'textfield',
      '#title' => t('Total cost'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['total_cost']) ? $item['total_cost'] : '',
      '#element_validate' => array('element_validate_number'),
      '#attributes' => array(
        'class' => array('total'),
      ),
    );

    // Date required.
    $form['items'][$key]['date_required'] = array(
      '#type' => 'date_popup',
      '#title' => t('Date required'),
      '#title_display' => 'invisible',
      '#date_format' => $date_format,
      '#date_label_position' => 'within',
      '#date_year_range' => '-3:+10',
      '#default_value' => !empty($item['date_required']) ? date($date_format, $item['date_required']) : '',
    );

    // Date created.
    $form['items'][$key]['date_created'] = array(
      '#type' => 'value',
      '#value' => !empty($item['date_created']) ? $item['date_created'] : '',
    );

    // Date updated.
    $form['items'][$key]['date_updated'] = array(
      '#type' => 'value',
      '#value' => !empty($item['date_updated']) ? $item['date_updated'] : '',
    );

    // User ID.
    $form['items'][$key]['user_id'] = array(
      '#type' => 'value',
      '#value' => !empty($item['user_id']) ? $item['user_id'] : NULL,
    );

    // Log ID.
    $form['items'][$key]['log_id'] = array(
      '#type' => 'value',
      '#value' => !empty($item['log_id']) ? $item['log_id'] : NULL,
    );
  }

  // Return the form.
  return $form;
}

/**
 * Forest plan budget form validation.
 */
function farm_nfa_forest_plan_budget_form_validate($form, &$form_state) {

  // Iterate through the budget items.
  foreach ($form_state['values']['expense']['items'] as $delta => $item) {

    // If any of the fields are filled, all of them are required.
    $required = FALSE;
    $fields = array(
      'description',
      'category',
      'unit_cost',
      'quantity',
      'total_cost',
      'date_required',
    );
    foreach ($fields as $field) {
      if (!empty($item[$field])) {
        $required = TRUE;
        break;
      }
    }
    if ($required) {
      foreach ($fields as $field) {
        if (empty($item[$field]) && $item[$field] != 0) {
          form_set_error('expense][items][' . $delta . '][' . $field, t('Missing required fields.'));
        }
      }
    }
  }
}

/**
 * Forest plan budget form submit.
 */
function farm_nfa_forest_plan_budget_form_submit($form, &$form_state) {

  // If the plan ID is not set, bail.
  if (empty($form_state['values']['plan']->id)) {
    return;
  }
  $plan_id = $form_state['values']['plan']->id;

  // Save the projected cost and income.
  db_query('DELETE FROM {farm_nfa_budget_projection} WHERE plan_id = :plan_id', array(':plan_id' => $plan_id));
  $types = array(
    'expense',
    'income',
  );
  foreach ($types as $type) {
    if (!empty($form_state['values']['projections'][$type])) {
      $projection = array(
        'plan_id' => $plan_id,
        'type' => $type,
        'value' => $form_state['values']['projections'][$type],
      );
      drupal_write_record('farm_nfa_budget_projection', $projection);
    }
  }

  // Iterate through the budget items.
  foreach ($form_state['values']['expense']['items'] as $delta => $item) {

    // If this is a new item (no ID), and it doesn't have a description, skip
    // it. The validation function above will check to make sure other fields
    // are set, so we just need to check one.
    if (empty($item['id']) && empty($item['description'])) {
      continue;
    }

    // Set the type to expense.
    $item['type'] = 'expense';

    // If date_created is not set, set it to the current time.
    if (empty($item['date_created'])) {
      $item['date_created'] = REQUEST_TIME;
    }

    // We will set the date_updated field to the current timestamp if it is
    // empty, or if any of the item's fields were updated.
    $updated = FALSE;
    if (empty($item['date_updated'])) {
      $updated = TRUE;
    }
    else {
      $fields = array(
        'description',
        'category',
        'unit_cost',
        'quantity',
        'total_cost',
        'date_required',
      );
      foreach ($fields as $field) {
        if ($item[$field] != $form['expense']['items'][$delta][$field]['#default_value']) {
          $updated = TRUE;
          break;
        }
      }
    }
    if ($updated) {
      $item['date_updated'] = REQUEST_TIME;
    }

    // Convert date_required to a timestamp.
    if (!empty($item['date_required'])) {
      $item['date_required'] = strtotime($item['date_required']);
    }

    // If a user ID is not set, set it to the current user.
    if (empty($item['user_id'])) {
      global $user;
      $item['user_id'] = $user->uid;
    }

    // If the item has an ID, we will update it. Otherwise, we will insert.
    $keys = array();
    if (!empty($item['id'])) {
      $keys = array('id');
    }
    drupal_write_record('farm_nfa_budget_item', $item, $keys);
  }
}

/**
 * Forest plan budget items fieldset theme function.
 */
function theme_farm_nfa_forest_plan_budget_fieldset(&$vars) {
  $fieldset = $vars['fieldset'];
  $output = '';

  // Define the date format used throughout the form.
  $date_format = 'Y-m-d';

  // Render the budget item fields in a table.
  $header = array(
    t('Log'),
    t('Description'),
    t('Category'),
    t('Unit cost'),
    t('Quantity'),
    t('Total cost'),
    t('Date required'),
    t('Added'),
    t('Updated'),
    t('Author'),
    t('Remove'),
  );
  $rows = array();
  if (!empty($fieldset['items'])) {
    foreach (element_children($fieldset['items']) as $item_id) {

      // Alias the item variable.
      $item =& $fieldset['items'][$item_id];

      // Get the author's name, if available.
      $author_name = '';
      if (!empty($item['user_id']['#value'])) {
        $author = user_load($item['user_id']['#value']);
        if (!empty($author)) {
          $author_name = entity_label('user', $author);
        }
      }

      // Create a link to the log, if available.
      $log_link = '';
      if (!empty($item['log_id']['#value'])) {
        $log = log_load($item['log_id']['#value']);
        if (!empty($log)) {
          $log_uri = entity_uri('log', $log);
          $log_link = l($log->id, $log_uri['path']);
        }
      }

      // Create a link for removing the item, if it was saved to the database.
      // If the item is linked to a log, then it can only be deleted by deleting
      // the log.
      $remove_link = '';
      if (!empty($item['id']['#value']) && empty($item['log_id']['#value'])) {
        $remove_link = l('Remove', 'farm/plan/' . $item['plan_id']['#value'] . '/budget/' . $item['id']['#value'] . '/remove');
      }

      // Assemble the row.
      $rows[] = array(
        'data' => array(
          $log_link,
          drupal_render($item['description']),
          drupal_render($item['category']),
          drupal_render($item['unit_cost']),
          drupal_render($item['quantity']),
          drupal_render($item['total_cost']),
          drupal_render($item['date_required']),
          !empty($item['date_created']['#value']) ? date($date_format, $item['date_created']['#value']) : '',
          !empty($item['date_updated']['#value']) ? date($date_format, $item['date_updated']['#value']) : '',
          $author_name,
          $remove_link,
        ),
      );
    }
  }
  $output .= theme('table', array(
    'header' => $header,
    'rows' => $rows,
    'empty' => t('No budget items have been added to this plan.'),
  ));

  // Add a container for the grand budget total (calculated via JS).
  $output .= '<div id="budget-total"></div>';

  // Render any remaining form elements.
  $output .= drupal_render_children($fieldset);

  return $output;
}

/**
 * Form for removing a budget item the plan.
 */
function farm_nfa_forest_plan_budget_remove_form($form, &$form_state, $plan, $item_id) {

  // Load the item from the database.
  $item = db_query('SELECT * FROM {farm_nfa_budget_item} WHERE id = :item_id', array(':item_id' => $item_id))->fetchAssoc();

  // If the plan or item is empty, show page not found.
  if (empty($plan) || empty($item)) {
    drupal_not_found();
    drupal_exit();
  }

  // Store the plan ID and item ID for use in the submit function.
  $form['plan_id'] = array(
    '#type' => 'value',
    '#value' => $plan->id,
  );
  $form['item_id'] = array(
    '#type' => 'value',
    '#value' => $item_id,
  );

  // Build a return path.
  $plan_uri = entity_uri('farm_plan', $plan);
  $path = $plan_uri['path'] . '/';

  // Build and return a confirmation form.
  return confirm_form($form,
    t('Are you sure you want to delete the budget item %item_name from the plan %plan_name?', array('%item_name' => $item['description'], '%plan_name' => $plan->name)),
    $path,
    t('This cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );
}

/**
 * Submit handler for the budget item removal form.
 */
function farm_nfa_forest_plan_budget_remove_form_submit($form, &$form_state) {

  // If a plan ID and item ID are not available, bail.
  if (empty($form_state['values']['plan_id']) || empty($form_state['values']['item_id'])) {
    return;
  }

  // Delete the record from the {farm_nfa_budget} table.
  db_delete('farm_nfa_budget_item')
    ->condition('id', $form_state['values']['item_id'])
    ->execute();

  // Redirect to the budget form.
  $form_state['redirect'] = 'farm/plan/' . $form_state['values']['plan_id'] . '/budget';
}
