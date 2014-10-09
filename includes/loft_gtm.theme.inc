<?php
/**
 * @file
 * Theme functions
 *
 * @ingroup loft_gtm
 * @{
 */

/**
 * Formats a datalayer js snippet.
 *
 * @param array $vars
 * - event:
 *   - #method: string
 *   - #params: array
 *
 * @return string
 *
 * @ingroup themeable
 */
function theme_loft_gtm_datalayer($vars) {
  $event = $vars['event'];

  $output = '';
  $output .= 'if (typeof dataLayer !== "undefined") {';
  if (!empty($event['#method'])) {
    $output .= 'dataLayer.' . $event['#method'] . '(' . json_encode($event['#params']) . ');';
  }
  else {
    $output .= 'dataLayer = ' . json_encode($event['#params']) . ';';
  }
  $output .= '}';

  return $output;
}
