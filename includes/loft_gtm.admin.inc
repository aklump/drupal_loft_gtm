<?php
/**
 * @file
 * Administration page callbacks for the loft_gtm module.
 *
 * @ingroup loft_gtm
 * @{
 */

/**
 * Form builder. Configure my_module.
 *
 * @ingroup forms
 * @see system_settings_form()
 */
function loft_gtm_admin_settings($form, &$form_state) {
  $code = variable_get('loft_gtm_code', LOFT_GTM_CODE);
  $form['init'] = array(
    '#type' => 'fieldset',
    '#title' => t('General Settings'),
    '#collapsible' => TRUE,
    '#collapsed' => !empty($code),
  );
  $form['init']['loft_gtm_code'] = array(
    '#type' => 'textarea',
    '#title' => t('Google Tag Manager Code'),
    '#description' => t('Enter your code from Google here. To obtain your code or register for an account visit <a href="@url">Google.com</a>.', array(
      '@url' => url('http://www.google.com/tagmanager/'),
    )),
    '#default_value' => $code,
    '#required' => TRUE,
    '#rows' => 10,
    '#resizable' => TRUE,
  );

  $form['loft_gtm_injection_mode'] = array(
    '#type' => 'radios',
    '#title' => t('DataLayer Processing'),
    '#default_value' => variable_get('loft_gtm_injection_mode', LOFT_GTM_INJECTION_MODE),
    '#options' => array(
      1 => t('Written in the HTML'),
      2 => t('Processed via AJAX')
    ),
  );

  $form['#validate'][] = 'loft_gtm_admin_settings_validate';

  return system_settings_form($form);
}

/**
 * Form validation handler for loft_gtm_admin_settings_validate().
 */
function loft_gtm_admin_settings_validate($form, &$form_state) {
  $code = $form_state['values']['loft_gtm_code'];
  $passed = 0;
  $passed += (int) stripos($code, 'googletagmanager.com') !== FALSE;
  $passed += (int) strpos($code, 'DataLayer') !== FALSE;
  if ($passed === 0) {
    form_set_error('loft_gtm_code', t('Your code seems to be missing key parts to make it valid; please check your code and re-submit.'));
  }
}

/** @} */ //end of group loft_gtm1
