/**
 * @file
 * The main javascript file for the loft_gtm module
 *
 * @ingroup loft_gtm
 * @{
 */
(function ($) {
  Drupal.gopGTM = Drupal.gopGTM || {};

  /**
   * Retrieve and execute queue records by id
   *
   * @param array ids
   */
  Drupal.gopGTM.process = function(ids) {
    var settings = Drupal.settings;
    $.post(settings.gopGTM.url + '/queue/process/ajax', {
      ids: ids,
      token: settings.gopGTM.token,
    }, function(data) {
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
  Drupal.gopGTM.clear = function(ids) {
    var settings = Drupal.settings;
    $.post(settings.gopGTM.url + '/queue/clear/ajax', {
      ids: ids,
      token: settings.gopGTM.token,
    });
  };

  /**
  * Core behavior for loft_gtm.
  */
  Drupal.behaviors.gopGTM = Drupal.behaviors.gopGTM || {};
  Drupal.behaviors.gopGTM.attach = function (context, settings) {

    // Process the queue
    if (settings.gopGTM.ids.length) {
      Drupal.gopGTM[settings.gopGTM.method](settings.gopGTM.ids);
    }
  };

  /**
  * @} End of "defgroup loft_gtm".
  */

})(jQuery);
