/**
 * @file
 * The main javascript file for the loft_gtm module
 *
 * @ingroup loft_gtm
 * @{
 */
(function ($) {
  Drupal.loftGTM = Drupal.loftGTM || {};

  /**
   * Retrieve and execute queue records by id
   *
   * @param array ids
   */
  Drupal.loftGTM.process = function(ids) {
    var settings = Drupal.settings;
    $.post(settings.loftGTM.url + '/queue/process/ajax', {
      ids: ids,
      token: settings.loftGTM.token,
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
  Drupal.loftGTM.clear = function(ids) {
    var settings = Drupal.settings;
    $.post(settings.loftGTM.url + '/queue/clear/ajax', {
      ids: ids,
      token: settings.loftGTM.token,
    });
  };

  /**
  * Core behavior for loft_gtm.
  */
  Drupal.behaviors.loftGTM = Drupal.behaviors.loftGTM || {};
  Drupal.behaviors.loftGTM.attach = function (context, settings) {

    if (typeof settings.loftGTM.token === undefined) {
      throw "missing loftGTM token.";
    }

    // Process the queue
    if (settings.loftGTM.ids.length) {
      Drupal.loftGTM[settings.loftGTM.method](settings.loftGTM.ids);
    }
  };

  /**
  * @} End of "defgroup loft_gtm".
  */

})(jQuery);
