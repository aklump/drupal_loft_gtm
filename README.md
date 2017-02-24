## Summary

Google Tag Manager API Module for Drupal by In the Loft Studios

## Requirements


## Installation

1. Download and unzip this module into your modules directory.
1. Goto Administer > Site Building > Modules and enable this module.
1. Install the [chrome extension](https://chrome.google.com/webstore/detail/tag-assistant-by-google/kejbdjndbnbjgmefkgdddjlbokphdefk?hl=en) for testing.

## Configuration

1. Make sure you have [created your account](https://www.google.com/analytics/tag-manager/get-started/) at google.com.
1. Visit `admin/config/search/loft-gtm` to configure.
1. Read the [quick start guide](https://developers.google.com/tag-manager/quickstart)

## Background Learning

Learn more about Google Tag Manager with these links.
* <https://www.youtube.com/watch?v=3LVza7sGy7g>
* <https://www.google.com/analytics/tag-manager/>

## Themers

By default the code will be spit out using `$page['content']['loft_gtm']`.  This may not place the code where you want it, just after the body tag.

As a second option you can adjust the settings `$conf['loft_gtm_theme_support']` and then do the following where `$loft_gtm` is provided to `html.tpl.php`.

You will want to do something like this to `html.tpl.php`
    
    ...
    </head>
    <body class="<?php print $classes; ?>" <?php print $attributes; ?>>
    <?php print render($loft_gtm) ?>
    <div id="skip-link">
        <a href="#main-content"...

## Developers

The point of [Google Tag Manager](https://www.google.com/analytics/tag-manager/) is to give away control to the Google UI, rather than the Drupal UI, however some tasks remain for the developer to do.  For these tasks read on.

### API

1. Drupal.loftGTM.dataLayer.event
1. `loft_gtm_queue_add()`

### For form submission events

You will add to the queue in the submission hook of a form; the queue will then be emptied on the next page load and the events thus fired.

Here is some example code to send an event based on the contact id of the contact form:

    <?php
    /**
     * Implements hook_form_alter().
     */
    function my_module_form_contact_site_form_alter(&$form, $form_state) {
      // Google Tag Manager
      $form['#submit'][] = '_my_module_form_contact_site_form_submit';
    }
    
    /**
     * Form submission handler
     */
    function _my_module_form_contact_site_form_submit($form, &$form_state) {
      // Google Tag Manager
      $cid = $form_state['values']['cid'];
      $event_label = $form['cid']['#options'][$cid];
      $event = array (
        '#method' => 'push',
        '#params' =>
        array (
          'event' => 'eventTracker',
          'eventCat' => 'Contact',
          'eventAct' => 'Request',
          'eventLbl' => $event_label,
        ),
      );
      loft_gtm_queue_add($event);
    }

### Usage via Javascript

1. Simply call one function:

        Drupal.loftGTM.dataLayer.event(event, category, action, label, value);
    
### Usage via PHP

1. Anytime you want to output a GTM event in HTML you will use `loft_gtm_queue_add()` to add the event.  All events added during processing will be written in html on the next output.
1. Important to note that the queue is cleared via ajax, so repeatedly viewing source will show you a growing queue.  That is inaccurate and only occurs because the ajax is not clearing the queue.

### Development servers

It's probably a good idea to do something like this in a dev only settings file:

    $conf['loft_gtm_enabled'] = false;
    $conf['loft_gtm_logging'] = true;
    //$conf['loft_gtm_code'] = '<!--Google Tag Manager Disabled for Dev-->';

## Contact

* **In the Loft Studios**
* Aaron Klump - Developer
* PO Box 29294 Bellingham, WA 98228-1294
* _aim_: theloft101
* _skype_: intheloftstudios
* _d.o_: aklump
* <http://www.InTheLoftStudios.com>
