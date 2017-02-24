<?php
/**
 * @file
 * Provides administration functions for the loft_gtm module.
 *
 * @ingroup loft_gtm
 * @{
 */

/**
 * Form builder. Configure my_module.
 *
 * @ingroup forms
 * @see     system_settings_form()
 *
 * @param $form
 * @param $form_state
 *
 * @return mixed
 */
function loft_gtm_admin_settings($form, &$form_state)
{
    $code0 = variable_get('loft_gtm_code', LOFT_GTM_CODE);
    $code1 = variable_get('loft_gtm_code_noscript', LOFT_GTM_CODE_NOSCRIPT);
    $description = t('Enter your <a href="!codeurl">code from Google here</a>. To obtain your code or register for an account visit <a href="!url">Google.com</a>.', array(
        '!url'     => url('http://www.google.com/tagmanager/'),
        '!codeurl' => url('https://developers.google.com/tag-manager/quickstart'),
    ));

    $form['init'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Google Tag Manager Code Snippets'),
        '#description' => $description,
        '#collapsible' => true,
        '#collapsed'   => !empty($code0 . $code1),
    );

    $form['init']['loft_gtm_theme_support'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('My theme supports this module (using <code>$loft_gtm</code>).'),
        '#default_value' => variable_get('loft_gtm_theme_support', false),
    );

    $form['init']['loft_gtm_code'] = array(
        '#type'          => 'textarea',
        '#title'         => t('Box One (Paste this code as high in the &lt;head> of the page as possible)'),
        '#default_value' => $code0,
        '#required'      => true,
        '#rows'          => 8,
        '#resizable'     => true,
    );

    $form['init']['loft_gtm_code_noscript'] = array(
        '#type'          => 'textarea',
        '#title'         => t('Box Two (Additionally, paste this code immediately after the opening &lt;body> tag)'),
        '#description'   => $description,
        '#default_value' => $code1,
        '#required'      => true,
        '#rows'          => 5,
        '#resizable'     => true,
    );

    $form['loft_gtm_enabled'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Enable Google event reporting'),
        '#default_value' => variable_get('loft_gtm_enabled', LOFT_GTM_ENABLED),
    );

    $form['loft_gtm_logging'] = array(
        '#type'          => 'checkbox',
        '#title'         => t('Enable Google event logging (dev only)'),
        '#default_value' => variable_get('loft_gtm_logging', LOFT_GTM_LOGGING),
    );

    $form['#validate'][] = 'loft_gtm_admin_settings_validate';

    return system_settings_form($form);
}

/**
 * Form validation handler for loft_gtm_admin_settings_validate().
 */
function loft_gtm_admin_settings_validate($form, &$form_state)
{
    $code = $form_state['values']['loft_gtm_code'];
    $passed = 0;
    $passed += (int) stripos($code, 'googletagmanager.com') !== false;
    $passed += (int) strpos($code, 'DataLayer') !== false;
    if ($passed === 0) {
        form_set_error('loft_gtm_code', t('Your code seems to be missing key parts to make it valid; please check your code and re-submit.'));
    }
}

/** @} */ //end of group loft_gtm1
