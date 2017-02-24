<?php
/**
 * @file
 * queue-related functions
 *
 * @ingroup loft_gtm
 * @{
 */

/**
 * Ajax handler for queue ajax functions
 *
 * Ops are: clear, get, process
 */
function _loft_gtm_ajax_handler_queue()
{
    $args = func_get_args();
    $op = array_shift($args);
    $type = array_pop($args);
    if ($type != 'ajax') {
        return MENU_NOT_FOUND;
    }
    $ids = isset($_POST['ids']) ? json_decode($_POST['ids']) : array();
    switch ($op) {
        case 'clear':
            loft_gtm_queue_clear($ids);
            break;
        case 'get':
            $queue = loft_gtm_queue_add();
            $json = array();
            foreach ($queue as $value) {
                $json[] = array(
                    'method' => $value['#method'],
                    'params' => $value['#params'],
                );
            }
            drupal_json_output(array_values($json));
            break;
        case 'process':
            if (($queue = loft_gtm_queue_add())
                && ($queue = array_intersect_key($queue, array_flip($ids)))
            ) {
                loft_gtm_queue_clear(array_keys($queue));
            }
            $json = array();
            foreach ($queue as $value) {
                $json[] = array(
                    'method' => $value['#method'],
                    'params' => $value['#params'],
                );
            }
            drupal_json_output(array_values($json));
            break;
    }
    drupal_exit();
}

/**
 * Remove one or more items from the queue by id
 *
 * @param string|array $ids
 *   An id or array of ids to remove
 *
 * @return array
 *   The remaining queue after removing $ids
 *
 * @tested
 */
function loft_gtm_queue_clear($ids)
{
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    if (empty($_SESSION['loft_gtm']['queue'])) {
        $_SESSION['loft_gtm']['queue'] = array();
    }
    $_SESSION['loft_gtm']['queue'] = array_diff_key($_SESSION['loft_gtm']['queue'], array_flip($ids));

    return $_SESSION['loft_gtm']['queue'];
}

/** @} */ //end of group: loft_gtm
