<?php

/**
 * Implements hook_loft_gtm_is_active().
 */
function HOOK_loft_gtm_is_active_alter(&$is_active)
{
    // Disable this on admin pages.
    $is_active = path_is_admin(current_path()) ? false : $is_active;
}
