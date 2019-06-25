<?php

/**
 * @file
 * NFA forest plantation plan budget form.
 */

/**
 * Plantation plan budget form.
 */
function farm_nfa_plantation_plan_budget_form($form, &$form_state, $plan) {

  // Start an empty form array.
  $form = array(
    '#tree' => TRUE,
  );

  // Set the page title.
  drupal_set_title('Budget');

  // Define the date format used throughout the form.
  $date_format = 'Y-m-d';

  // Add the plan to the form values, for use in form submission.
  $form['plan'] = array(
    '#type' => 'value',
    '#value' => $plan,
  );

  // Load budget items from the database.
  $budget_items = farm_nfa_budget_items($plan->id);

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
      '#value' => $plan->id,
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
      '#type' => 'textfield',
      '#title' => t('Unit type'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['type']) ? $item['type'] : '',
    );

    // Unit cost.
    $form['items'][$key]['unit_cost'] = array(
      '#type' => 'textfield',
      '#title' => t('Unit cost'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['unit_cost']) ? $item['unit_cost'] : '',
      '#element_validate' => array('element_validate_number'),
    );

    // Quantity.
    $form['items'][$key]['quantity'] = array(
      '#type' => 'textfield',
      '#title' => t('Quantity'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['quantity']) ? $item['quantity'] : '',
      '#element_validate' => array('element_validate_number'),
    );

    // Total cost.
    $form['items'][$key]['total_cost'] = array(
      '#type' => 'textfield',
      '#title' => t('Total cost'),
      '#title_display' => 'invisible',
      '#default_value' => !empty($item['total_cost']) ? $item['total_cost'] : '',
      '#element_validate' => array('element_validate_number'),
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
  }

  // Submit button.
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );

  // Return the form.
  return $form;
}

/**
 * Plantation plan budget form validation.
 */
function farm_nfa_plantation_plan_budget_form_validate($form, &$form_state) {

  // Iterate through the budget items.
  foreach ($form_state['values']['items'] as $delta => $item) {

    // If any of the fields are filled, all of them are required.
    $required = FALSE;
    $fields = array(
      'description',
      'type',
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
        if (empty($item[$field])) {
          form_set_error('items][' . $delta . '][' . $field, t('Missing required fields.'));
        }
      }
    }
  }
}

/**
 * Plantation plan budget form submit.
 */
function farm_nfa_plantation_plan_budget_form_submit($form, &$form_state) {

  // Iterate through the budget items.
  foreach ($form_state['values']['items'] as $delta => $item) {

    // If this is a new item (no ID), and it doesn't have a description, skip
    // it. The validation function above will check to make sure other fields
    // are set, so we just need to check one.
    if (empty($item['id']) && empty($item['description'])) {
      continue;
    }

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
        'type',
        'unit_cost',
        'quantity',
        'total_cost',
        'date_required',
      );
      foreach ($fields as $field) {
        if ($item[$field] != $form['items'][$delta][$field]['#default_value']) {
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
    drupal_write_record('farm_nfa_budget', $item, $keys);
  }
}

/**
 * Plantation plan budget theme function.
 */
function theme_farm_nfa_plantation_plan_budget_form(&$vars) {
  $form = $vars['form'];
  $output = '';

  // Define the date format used throughout the form.
  $date_format = 'Y-m-d';

  // Add CSS.
  drupal_add_css(drupal_get_path('module', 'farm_nfa') . '/css/farm_nfa.budget.css');

  // Get the plan object.
  $plan = $form['plan']['#value'];

  // Render the budget item fields in a table.
  $header = array(
    t('Description'),
    t('Unit type'),
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
  if (!empty($form['items'])) {
    foreach (element_children($form['items']) as $item_id) {

      // Alias the item variable.
      $item =& $form['items'][$item_id];

      // Get the author's name, if available.
      $author_name = '';
      if (!empty($item['user_id']['#value'])) {
        $author = user_load($item['user_id']['#value']);
        if (!empty($author)) {
          $author_name = entity_label('user', $author);
        }
      }

      // Create a link for removing the item, if it was saved to the database.
      $remove_link = '';
      if (!empty($item['id']['#value'])) {
        $remove_link = l('Remove', 'farm/plan/' . $plan->id . '/budget/' . $item['id']['#value'] . '/remove');
      }

      // Assemble the row.
      $rows[] = array(
        'data' => array(
          drupal_render($item['description']),
          drupal_render($item['type']),
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

  // Render any remaining form elements.
  $output .= drupal_render_children($form);

  return $output;
}

/**
 * Form for removing a budget item the plan.
 */
function farm_nfa_plantation_plan_budget_remove_form($form, &$form_state, $plan, $item_id) {

  // Load the item from the database.
  $item = db_query('SELECT * FROM {farm_nfa_budget} WHERE id = :item_id', array(':item_id' => $item_id))->fetchAssoc();

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
function farm_nfa_plantation_plan_budget_remove_form_submit($form, &$form_state) {

  // If a plan ID and item ID are not available, bail.
  if (empty($form_state['values']['plan_id']) || empty($form_state['values']['item_id'])) {
    return;
  }

  // Delete the record from the {farm_nfa_budget} table.
  db_delete('farm_nfa_budget')
    ->condition('id', $form_state['values']['item_id'])
    ->execute();

  // Redirect to the budget form.
  $form_state['redirect'] = 'farm/plan/' . $form_state['values']['plan_id'] . '/budget';
}
