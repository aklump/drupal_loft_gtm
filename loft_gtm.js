/**
 * @file
 * The main javascript file for loft_gtm
 *
 * @ingroup loft_gtm
 * @{
 */
var dataLayer = dataLayer || false;
(function ($, Drupal, googleDataLayer) {
  "use strict";

  if (googleDataLayer === false) {
    return;
  }

  /**
   * A wrapper for google's dataLayer integrates drupal controls and logging.
   */
  function dataLayer(logger, settings, googleDataLayer) {
    this.log = logger;
    this.settings = settings;
    this.googleDataLayer = googleDataLayer;

    this.index = [];
    this.index.instances = [];
  }

  /**
   * Perform a dataLayer push.
   * @param data
   * @param uuid
   * @returns {dataLayer}
   */
  dataLayer.prototype.push = function (data, uuid) {

    var json = JSON.stringify(data);

    // Validation checking
    if (!this.settings.enabled) {
      throw 'loftGTM.enabled === false, prevented push ' + json;
    }

    if (!this.googleDataLayer) {
      throw "Missing dependency 'dataLayer'; cannot push " + json;
    }

    var key = typeof(uuid) === "string" ? uuid : JSON.stringify(data);
    if (!uuid || !this.index.instances[key]) {
      this.googleDataLayer.push(data);
      this.index.instances[key] = true;
      this.log('Google tag manager, dataLayer.push executed.');
      this.log(data)
    }
    else {
      this.log('Duplicate push blocked for ' + json);
    }

    return this;
  };

  /**
   * Shortcut method to push events
   * @param category
   * @param action
   * @param label optional
   * @param value optional
   * @param event Optional, defaults to eventTracker
   * @param uuid
   * @returns {dataLayer}
   */
  dataLayer.prototype.event = function (category, action, label, value, event, uuid) {
    return this.push({
      "event"   : event || this.settings.event,
      "eventCat": category,
      "eventAct": action,
      "eventLbl": label,
      "eventVal": value,
    }, uuid);
  };

  Drupal.loftGTM = {};

  Drupal.loftGTM.log = function (message) {
    if (Drupal.settings.loftGTM.logging) {
      console.log(message);
    }
  };

  /**
   * Expose this to the world.
   * @type {dataLayer}
   */
  Drupal.loftGTM.dataLayer = new dataLayer(Drupal.loftGTM.log, Drupal.settings.loftGTM, googleDataLayer);

  /**
   * Push an event to Google tag manager.
   *
   * @param event string|object  If you want to send the entire object as the first argument, you may do so, in which
   *   case the other arguments are ignored.  Otherwise send each argument to build the object.
   * @param category
   * @param action
   * @param label
   * @param value
   *
   * @deprecated Will be removed in future versions.  Convert to Drupal.loftGTM.dataLayer.event() instead.
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

      return this.dataLayer.event(category, action, label, value, event);
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
})
(jQuery, Drupal, dataLayer);
