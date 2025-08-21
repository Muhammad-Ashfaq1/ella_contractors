<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper functions for Ella Contractors module - minimal version
 */

/**
 * Get module version
 */
function get_ella_contractors_version()
{
    return '1.0.0';
}

/**
 * Check if contractor has permission
 */
function has_contractor_permission($permission, $contractor_id = null)
{
    if (is_admin()) {
        return true;
    }
    
    return has_permission('ella_contractors', '', $permission);
}
