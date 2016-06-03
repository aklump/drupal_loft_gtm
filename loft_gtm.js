/**
 * @file
 * The main javascript file for loft_gtm
 *
 * @ingroup loft_gtm
 * @{
 */
(function ($, Drupal, window, document, undefined) {
  "use strict";

  Drupal.loftGTM = {};

  Drupal.loftGTM.log = function (message) {
    if (Drupal.settings.loftGTM.logging) {
      console.log(message);
    }
  }

  /**
   * Push an event to Google tag manager.
   *
   * @param event string|object  If you want to send the entire object as the first argument, you may do so, in which
   *   case the other arguments are ignored.  Otherwise send each argument to build the object.
   * @param category
   * @param action
   * @param label
   * @param value
   */
  Drupal.loftGTM.push = function (event, category, action, label, value) {
    var _    = this,
        args = event;

    try {
      if (typeof event !== 'object') {
        args = {
          'event'   : event,
          'eventCat': category,
          'eventAct': action,
          'eventLbl': label,
          'eventVal': value
        }
      }

      if (!Drupal.settings.loftGTM.enabled) {
        throw 'loftGTM.enabled === false, prevented push ' + JSON.stringify(args);
      }

      if (typeof dataLayer === 'undefined') {
        throw "Missing dependency 'dataLayer'; cannot push " + JSON.stringify(args);
      }

      _.log('Google tag manager, dataLayer.push executed.');
      _.log(args)
      dataLayer.push(args);
    }
    catch (error) {
      if (Drupal.settings.loftGTM.logging) {
        _.log(error);
      }
      else {
        throw error;
      }

    }
  }


  /**
   * Retrieve and execute queue records by id
   *
   * @param array ids
   */
  Drupal.loftGTM.process = function (ids) {
    var settings = Drupal.settings;
    $.post(settings.loftGTM.url + '/queue/process/ajax', {
      ids  : ids,
      token: settings.loftGTM.token,
    }, function (data) {
      for (var i in data) {
        if (data[i].method) {
          DataLayer[data[i].method](data[i].params);
        }
        else {
          DataLayer = data[i].params;
        }
      }
    });
  };

  /**
   * Clear queue records by id
   *
   * @param array ids
   */
  Drupal.loftGTM.clear = function (ids) {
    var settings = Drupal.settings;
    $.post(settings.loftGTM.url + '/queue/clear/ajax', {
      ids  : ids,
      token: settings.loftGTM.token,
    });
  };

  /**
   * Core behavior for loft_gtm.
   */
  Drupal.behaviors.loftGTM = {};
  Drupal.behaviors.loftGTM.attach = function (context, settings) {

    if (typeof settings.loftGTM.token === undefined) {
      throw "missing loftGTM token.";
    }

    // Process the queue
    if (settings.loftGTM.ids) {
      Drupal.loftGTM[settings.loftGTM.method](settings.loftGTM.ids);
    }
  };

})
(jQuery, Drupal, window, document, undefined);
