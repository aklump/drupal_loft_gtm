<?php
/**
 * @file
 * Administration page callbacks for the loft_gtm_dev module.
 *
 * @ingroup loft_gtm_dev
 * @{
 */

/**
 * Form builder for the loft_gtm_dev_parser_form form.
 *
 * Parses json into a php array for hook_loft_gtm_forms()
 *
 * @param array $form
 * @param array &$form_state
 *
 * @see     loft_gtm_dev_parser_form_validate()
 * @see     loft_gtm_dev_parser_form_submit()
 * @ingroup forms
 */
function loft_gtm_dev_parser_form($form, &$form_state) {

  $form['#regex'] = '(?:<script type="text\/javascript">\s*)?dataLayer\.(push)\((.*)\)(?:;?\s*<\/script>)?';
  $form['#regex_data_layer'] = '()dataLayer\s*=\s*(\[{.*}\])';

  if (isset($form_state['storage'])) {
    $form['json_result'] = array(
      '#type'        => 'textarea',
      '#title'       => t('Result'),
      '#description' => t('Add to the appropriate hook_form_submit().'),
      '#value'       => $form_state['storage']['output'],
      '#rows'        => 20,
      '#resizable'   => TRUE,
    );
  }

  $form['input'] = array(
    '#type'          => 'textarea',
    '#title'         => t('Enter snippet'),
    '#default_value' => isset($form_state['storage']['input']) ? $form_state['storage']['input'] : '',
    '#required'      => TRUE,
    '#rows'          => 5,
    '#resizable'     => TRUE,
  );

  //$form['callback_params'] = array(
  //  '#type' => 'textfield',
  //  '#description' => t('Enter a list of method parameter keys that should be configured for callbacks'),
  //  '#title' => t('Callback Parameters'),
  //  '#default_value' => '',
  //);

  $form['target_form'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Form ID'),
    '#default_value' => isset($form_state['storage']['target_form']) ? $form_state['storage']['target_form'] : 'my_form',
    '#required'      => TRUE,
  );

  $form['loft_gtm_dev_target_module'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Target Module'),
    '#default_value' => variable_get('loft_gtm_dev_target_module', ''),
    '#required'      => TRUE,
  );

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => t('Parse Now'),
  );

  return $form;
}

/**
 * Form validation handler for loft_gtm_dev_parser_form().
 *
 * @see loft_gtm_dev_parser_form()
 * @see loft_gtm_dev_parser_form_submit()
 */
function loft_gtm_dev_parser_form_validate($form, &$form_state) {
  $values = $form_state['values'];
  if (!preg_match('/' . $form['#regex'] . '/s', $values['input'])
    && !preg_match('/' . $form['#regex_data_layer'] . '/s', $values['input'])
  ) {
    form_set_error('input', t('Invalid input'));
  }
}

/**
 * Form submission handler for loft_gtm_dev_parser_form().
 *
 * @see loft_gtm_dev_parser_form()
 * @see loft_gtm_dev_parser_form_validate()
 */
function loft_gtm_dev_parser_form_submit($form, &$form_state) {
  $values = $form_state['values'];

  variable_set('loft_gtm_dev_target_module', $values['loft_gtm_dev_target_module']);

  $form_state['rebuild'] = TRUE;
  if (preg_match('/' . $form['#regex'] . '/si', $values['input'], $parsed)) {
    $regex = '#regex';
  }
  elseif (preg_match('/' . $form['#regex_data_layer'] . '/si', $values['input'], $parsed)) {
    $regex = '#regex_data_layer';
  }

  array_shift($parsed);
  $parsed[1] = str_replace("'", '"', $parsed[1]);
  $parsed[1] = json_decode($parsed[1], TRUE);
  $parsed = array_combine(array('#method', '#params'), $parsed);
  $callback_params = isset($values['callback_params']) ? explode(',', $values['callback_params']) : array();

  foreach ($parsed['#params'] as $key => $value) {
    if (in_array($key, $callback_params)) {
      $parsed['#params'][$key] = array(
        'callback'           => 't',
        'callback arguments' => array($value),
      );
    }
  }

  $_SESSION['loft_gtm_dev']['test_event'] = $parsed = var_export($parsed, TRUE);

  $form = $form_state['values']['target_form'];
  $module = $values['loft_gtm_dev_target_module'];
  $parsed = <<<EOD
/**
 * Implements hook_form_alter().
 */
function {$module}_form_{$form}_alter(&\$form, \$form_state, \$form_id) {
  \$form['#submit'][] = '_{$module}_{$form}_submit';
}

/**
 * Form submission handler
 */
function _{$module}_{$form}_submit(\$form, &\$form_state) {
  // Google Tag Manager
  \$event = {$parsed};
  loft_gtm_queue_add(\$event);
}
EOD;

  $form_state['storage'] = array(
    'input'  => $form_state['values']['input'],
    'output' => $parsed,
  );
}

/**
 * Form builder for the loft_gtm_dev_event_test_form form.
 *
 * Test fire an event
 *
 * @param array $form
 * @param array &$form_state
 *
 * @see     loft_gtm_dev_event_test_form_validate()
 * @see     loft_gtm_dev_event_test_form_submit()
 * @ingroup forms
 */
function loft_gtm_dev_event_test_form($form, &$form_state) {

  $form['event'] = array(
    '#type'          => 'textarea',
    '#title'         => t('Enter the event code array'),
    '#default_value' => isset($_SESSION['loft_gtm_dev']['test_event']) ? $_SESSION['loft_gtm_dev']['test_event'] : '',
    '#required'      => TRUE,
    '#rows'          => 10,
    '#resizable'     => TRUE,
  );

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => t('Fire Event'),
  );

  return $form;
}

/**
 * Form submission handler for loft_gtm_dev_event_test_form().
 *
 * @see loft_gtm_dev_event_test_form()
 * @see loft_gtm_dev_event_test_form_validate()
 */
function loft_gtm_dev_event_test_form_submit($form, &$form_state) {
  $php = "\$event = " . $form_state['values']['event'] . ';';
  eval($php);
  loft_gtm_queue_add($event);
  drupal_set_message(t('Event has been fired; check page source.'));
}



/** @} */ //end of group loft_gtm_dev
