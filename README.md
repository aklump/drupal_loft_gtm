##Summary
Google Tag Manager API Moduel for Drupal by In the Loft Studios

##Requirements


##Installation
1. Download and unzip this module into your modules directory.
1. Goto Administer > Site Building > Modules and enable this module.

##Configuration
1. Visit `admin/config/search/loft-gtm` to configure.

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

## Usage via Javascript
1. Simply call one function:

        Drupal.loftGTM.push(event, category, action, label, value);
    
## Usage via PHP
1. Anytime you want to output a GTM event in HTML you will use `loft_gtm_queue_add()` to add the event.  All events added during processing will be written in html on the next output.
1. Important to note that the queue is cleared via ajax, so repeatedly viewing source will show you a growing queue.  That is inaccurate and only occurs because the ajax is not clearing the queue.

##API
1. Drupal.loftGTM.push
1. `loft_gtm_queue_add()`

##Contact
* **In the Loft Studios**
* Aaron Klump - Developer
* PO Box 29294 Bellingham, WA 98228-1294
* _aim_: theloft101
* _skype_: intheloftstudios
* _d.o_: aklump
* <http://www.InTheLoftStudios.com>
