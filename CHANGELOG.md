## 2.0-rc10

1. Deprecated the use of `Drupal.loftGTM.dataLayer`; use `dataLayer` instead.

## 2.0*

1. There is now support for the no-js tag from Google; get it from the GTM account and then add it here admin/config/search/loft-gtm
1. The variable `loft_gtm_injection_mode` is no longer used.
1. Most likely, you need to set up theme support for the variable `$loft_gtm`.  If you are using a custom theme, you probably need to add this to `html.tpl.php`:
    
        <?php print render($loft_gtm) ?>

1. And then add this to settings.php: 

        $conf['loft_gtm_theme_support'] = 1;

1. You must replace `Drupal.loftGTM.push` with `Drupal.loftGTM.dataLayer.event` and change the object argument to scalar arguments
