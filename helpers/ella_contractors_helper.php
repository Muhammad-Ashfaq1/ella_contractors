<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper functions for Ella Contractors module
 */

/**
 * Get contractor status badge
 */
function get_contractor_status_badge($status)
{
    $badges = [
        'active' => '<span class="label label-success">Active</span>',
        'inactive' => '<span class="label label-default">Inactive</span>',
        'pending' => '<span class="label label-warning">Pending</span>',
        'blacklisted' => '<span class="label label-danger">Blacklisted</span>'
    ];
    
    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . ucfirst($status) . '</span>';
}

/**
 * Get contract status badge
 */
function get_contract_status_badge($status)
{
    $badges = [
        'draft' => '<span class="label label-default">Draft</span>',
        'active' => '<span class="label label-success">Active</span>',
        'completed' => '<span class="label label-primary">Completed</span>',
        'terminated' => '<span class="label label-danger">Terminated</span>',
        'expired' => '<span class="label label-warning">Expired</span>'
    ];
    
    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . ucfirst($status) . '</span>';
}

/**
 * Get payment status badge
 */
function get_payment_status_badge($status)
{
    $badges = [
        'pending' => '<span class="label label-warning">Pending</span>',
        'completed' => '<span class="label label-success">Completed</span>',
        'failed' => '<span class="label label-danger">Failed</span>',
        'cancelled' => '<span class="label label-default">Cancelled</span>'
    ];
    
    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . ucfirst($status) . '</span>';
}

/**
 * Get project status badge
 */
function get_project_status_badge($status)
{
    $badges = [
        'planning' => '<span class="label label-info">Planning</span>',
        'in_progress' => '<span class="label label-primary">In Progress</span>',
        'on_hold' => '<span class="label label-warning">On Hold</span>',
        'completed' => '<span class="label label-success">Completed</span>',
        'cancelled' => '<span class="label label-danger">Cancelled</span>'
    ];
    
    return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . ucfirst($status) . '</span>';
}

/**
 * Format currency
 */
function format_contractor_currency($amount, $currency = null)
{
    if (!$currency) {
        $currency = get_base_currency()->name;
    }
    
    return app_format_money($amount, $currency);
}

/**
 * Get contractor avatar
 */
function get_contractor_avatar($contractor)
{
    if (!empty($contractor->profile_image)) {
        return base_url('uploads/contractors/' . $contractor->profile_image);
    }
    
    // Generate initials avatar
    $initials = '';
    $name_parts = explode(' ', $contractor->company_name);
    foreach ($name_parts as $part) {
        $initials .= substr($part, 0, 1);
    }
    
    $colors = ['#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', '#8c564b'];
    $color_index = ord($initials[0]) % count($colors);
    $bg_color = $colors[$color_index];
    
    return 'data:image/svg+xml;base64,' . base64_encode('
        <svg width="40" height="40" xmlns="http://www.w3.org/2000/svg">
            <rect width="40" height="40" fill="' . $bg_color . '"/>
            <text x="20" y="25" font-family="Arial, sans-serif" font-size="14" 
                  fill="white" text-anchor="middle" dominant-baseline="middle">' . 
                  strtoupper(substr($initials, 0, 2)) . '</text>
        </svg>
    ');
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

/**
 * Get contractor dropdown options
 */
function get_contractor_dropdown_options($selected = null, $include_empty = true)
{
    $CI = &get_instance();
    $CI->load->model('ella_contractors/ella_contractors_model');
    
    $contractors = $CI->ella_contractors_model->get_active_contractors();
    
    $options = '';
    
    if ($include_empty) {
        $options .= '<option value="">Select Contractor</option>';
    }
    
    foreach ($contractors as $contractor) {
        $selected_attr = ($selected == $contractor->id) ? 'selected' : '';
        $options .= '<option value="' . $contractor->id . '" ' . $selected_attr . '>' . 
                   $contractor->company_name . '</option>';
    }
    
    return $options;
}

/**
 * Calculate contract value
 */
function calculate_contract_value($contract)
{
    $total = 0;
    
    if (!empty($contract->hourly_rate) && !empty($contract->estimated_hours)) {
        $total = $contract->hourly_rate * $contract->estimated_hours;
    } elseif (!empty($contract->fixed_amount)) {
        $total = $contract->fixed_amount;
    }
    
    return $total;
}

/**
 * Get contract progress percentage
 */
function get_contract_progress($contract)
{
    if ($contract->status == 'completed') {
        return 100;
    }
    
    if (empty($contract->start_date) || empty($contract->end_date)) {
        return 0;
    }
    
    $start = strtotime($contract->start_date);
    $end = strtotime($contract->end_date);
    $now = time();
    
    if ($now < $start) {
        return 0;
    }
    
    if ($now > $end) {
        return 100;
    }
    
    $total_duration = $end - $start;
    $elapsed_duration = $now - $start;
    
    return round(($elapsed_duration / $total_duration) * 100);
}

/**
 * Send contractor notification
 */
function send_contractor_notification($contractor_id, $subject, $message, $type = 'email')
{
    $CI = &get_instance();
    $CI->load->model('ella_contractors/ella_contractors_model');
    
    $contractor = $CI->ella_contractors_model->get_contractor($contractor_id);
    
    if (!$contractor) {
        return false;
    }
    
    if ($type == 'email' && !empty($contractor->email)) {
        $CI->load->library('email');
        
        $CI->email->from(get_option('smtp_email'), get_option('companyname'));
        $CI->email->to($contractor->email);
        $CI->email->subject($subject);
        $CI->email->message($message);
        
        return $CI->email->send();
    }
    
    return false;
}

/**
 * Log contractor activity
 */
function log_contractor_activity($contractor_id, $activity, $description = '')
{
    $CI = &get_instance();
    
    $data = [
        'contractor_id' => $contractor_id,
        'activity' => $activity,
        'description' => $description,
        'staff_id' => get_staff_user_id(),
        'date_created' => date('Y-m-d H:i:s')
    ];
    
    $CI->db->insert('tblella_contractor_activity', $data);
}

/**
 * Get module version
 */
function get_ella_contractors_version()
{
    return '1.0.0';
}
