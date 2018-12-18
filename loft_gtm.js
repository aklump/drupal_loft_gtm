/**
 * @file
 * The main javascript file for loft_gtm
 *
 * @ingroup loft_gtm
 * @{
 */
var dataLayer = dataLayer || null;

// It's possible that dataLayer is not defined, such as when the admin theme is
// active.
(function(Drupal, dataLayer) {
  'use strict';

  // @link https://developers.google.com/tag-manager/devguide
  // Do nothing if there is no dataLayer (Google's dataLayer)
  if (!dataLayer) {
    return;
  }

  /**
   * Keep an index of UUIDs that were already pushed.
   * @type {Array}
   */
  var pushed = [];

  /**
   * An object that can be used to interact with dataLayer (shortcuts, etc).
   *
   * @type {{settings, dataLayer: boolean, push: push, event: event, log: log}}
   * @see Drupal.loftGTM.dataLayer
   */
  var reporter = {

    settings: Drupal.settings.loftGTM,

    /**
     * Shortcut method to push events into dataLayer
     *
     * @param category
     * @param action
     * @param label optional
     * @param value optional
     * @param event Optional, defaults to eventTracker
     * @param uuid
     * @returns {dataLayer}
     */
    event: function(category, action, label, value, event, uuid) {
      return this.push({
        'event': event || this.settings.event,
        'eventCat': category,
        'eventAct': action,
        'eventLbl': label,
        'eventVal': value
      }, uuid);
    },


    /**
     * This becomes dataLayer.push with augmented features (see monkey-patch).
     *
     * @param data
     * @param uuid
     * @returns {*}
     */
    push: function(data, uuid) {
      var json = JSON.stringify(data);

      // Validation checking
      if (!this.settings.enabled) {
        this.log('loftGTM.enabled === false, prevented push ' + json);
        return;
      }

      if (!dataLayer) {
        this.log('Missing dependency \'dataLayer\'; cannot push ' + json);
        return;
      }

      var flatUuid = typeof (uuid) === 'string' ? uuid : JSON.stringify(data);
      if (!uuid || !pushed[flatUuid]) {
        pushed[flatUuid] = true;
        this.log('Google tag manager, dataLayer.push executed.');
        this.log(data);

        return dataLayer.push(data);
      }
      this.log('Duplicate push blocked for ' + json);
    },

    /**
     * Log messages
     *
     * @param message
     */
    log: function(message) {
      if (!this.settings.logging) {
        return;
      }

      console.log(message);
    }
  };

  //
  //
  // Expose reporter as a global object.
  //
  Drupal.loftGTM = {
    dataLayer: reporter
  };

})(Drupal, dataLayer);
