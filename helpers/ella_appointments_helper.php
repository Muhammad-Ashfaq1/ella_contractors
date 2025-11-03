<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ella Appointments Helper
 * 
 * Helper functions for appointment data retrieval and management
 * Following the same patterns as staff_helper.php and other Perfex CRM helpers
 * 
 * @package EllaContractors
 * @version 1.0.0
 */

if (!function_exists('get_appointments_by_lead')) {
    /**
     * Get all appointments for a specific lead
     * Similar to get_lead_detail() in staff_helper.php
     * 
     * @param int $lead_id Lead ID
     * @return array Array of appointment records
     */
    function get_appointments_by_lead($lead_id)
    {
        $CI = &get_instance();
        $appointments = array();
        
        $CI->db->select('*');
        // $CI->db->where('rel_type', 'lead');
        // $CI->db->where('rel_id', $lead_id);
        $CI->db->where('contact_id', $lead_id);
        $CI->db->order_by('date', 'DESC');
        $query = $CI->db->get(db_prefix() . 'appointly_appointments');
        
        $appointments = $query->result_array();
        
        return $appointments;
    }
}

if (!function_exists('get_appointment_by_id')) {
    /**
     * Get a single appointment by ID
     * 
     * @param int $appointment_id Appointment ID
     * @return array|null Appointment data or null
     */
    function get_appointment_by_id($appointment_id)
    {
        $CI = &get_instance();
        
        $CI->db->select('*');
        $CI->db->where('id', $appointment_id);
        $query = $CI->db->get(db_prefix() . 'appointly_appointments');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null;
    }
}

if (!function_exists('get_appointments_count_by_lead')) {
    /**
     * Get total count of appointments for a specific lead
     * 
     * @param int $lead_id Lead ID
     * @return int Count of appointments
     */
    function get_appointments_count_by_lead($lead_id)
    {
        $CI = &get_instance();
        
        $CI->db->where('rel_type', 'lead');
        $CI->db->where('rel_id', $lead_id);
        
        return $CI->db->count_all_results(db_prefix() . 'appointly_appointments');
    }
}

if (!function_exists('get_upcoming_appointments_by_lead')) {
    /**
     * Get upcoming appointments for a specific lead
     * 
     * @param int $lead_id Lead ID
     * @return array Array of upcoming appointment records
     */
    function get_upcoming_appointments_by_lead($lead_id)
    {
        $CI = &get_instance();
        $appointments = array();
        
        $CI->db->select('*');
        $CI->db->where('rel_type', 'lead');
        $CI->db->where('rel_id', $lead_id);
        $CI->db->where('date >=', date('Y-m-d'));
        $CI->db->order_by('date', 'ASC');
        $query = $CI->db->get(db_prefix() . 'appointly_appointments');
        
        $appointments = $query->result_array();
        
        return $appointments;
    }
}

if (!function_exists('get_past_appointments_by_lead')) {
    /**
     * Get past appointments for a specific lead
     * 
     * @param int $lead_id Lead ID
     * @return array Array of past appointment records
     */
    function get_past_appointments_by_lead($lead_id)
    {
        $CI = &get_instance();
        $appointments = array();
        
        $CI->db->select('*');
        $CI->db->where('rel_type', 'lead');
        $CI->db->where('rel_id', $lead_id);
        $CI->db->where('date <', date('Y-m-d'));
        $CI->db->order_by('date', 'DESC');
        $query = $CI->db->get(db_prefix() . 'appointly_appointments');
        
        $appointments = $query->result_array();
        
        return $appointments;
    }
}

if (!function_exists('get_appointments_by_status')) {
    /**
     * Get appointments by lead ID and status
     * 
     * @param int $lead_id Lead ID
     * @param string $status Appointment status (scheduled, cancelled, complete)
     * @return array Array of appointment records
     */
    function get_appointments_by_status($lead_id, $status = 'scheduled')
    {
        $CI = &get_instance();
        $appointments = array();
        
        $CI->db->select('*');
        $CI->db->where('rel_type', 'lead');
        $CI->db->where('rel_id', $lead_id);
        $CI->db->where('appointment_status', $status);
        $CI->db->order_by('date', 'DESC');
        $query = $CI->db->get(db_prefix() . 'appointly_appointments');
        
        $appointments = $query->result_array();
        
        return $appointments;
    }
}

if (!function_exists('get_latest_appointment_by_lead')) {
    /**
     * Get the most recent appointment for a lead
     * 
     * @param int $lead_id Lead ID
     * @return array|null Latest appointment data or null
     */
    function get_latest_appointment_by_lead($lead_id)
    {
        $CI = &get_instance();
        
        $CI->db->select('*');
        $CI->db->where('rel_type', 'lead');
        $CI->db->where('rel_id', $lead_id);
        $CI->db->order_by('date', 'DESC');
        $CI->db->limit(1);
        $query = $CI->db->get(db_prefix() . 'appointly_appointments');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null;
    }
}

if (!function_exists('has_appointments')) {
    /**
     * Check if a lead has any appointments
     * 
     * @param int $lead_id Lead ID
     * @return bool True if lead has appointments
     */
    function has_appointments($lead_id)
    {
        return get_appointments_count_by_lead($lead_id) > 0;
    }
}

if (!function_exists('get_appointments_with_estimates')) {
    /**
     * Get appointments that have estimates attached
     * 
     * @param int $lead_id Lead ID
     * @return array Array of appointments with estimates
     */
    function get_appointments_with_estimates($lead_id)
    {
        $CI = &get_instance();
        $appointments = array();
        
        // Get all appointments for the lead
        $CI->db->select('a.*');
        $CI->db->from(db_prefix() . 'appointly_appointments a');
        $CI->db->join(db_prefix() . 'ella_contractor_estimates e', 'a.id = e.appointment_id', 'inner');
        $CI->db->where('a.rel_type', 'lead');
        $CI->db->where('a.rel_id', $lead_id);
        $CI->db->group_by('a.id');
        $CI->db->order_by('a.date', 'DESC');
        $query = $CI->db->get();
        
        $appointments = $query->result_array();
        
        return $appointments;
    }
}

if (!function_exists('get_appointment_estimates')) {
    /**
     * Get all estimates for a specific appointment
     * 
     * @param int $appointment_id Appointment ID
     * @return array Array of estimates
     */
    function get_appointment_estimates($appointment_id)
    {
        $CI = &get_instance();
        $estimates = array();
        
        $CI->db->select('*');
        $CI->db->where('appointment_id', $appointment_id);
        $CI->db->order_by('created_at', 'DESC');
        $query = $CI->db->get(db_prefix() . 'ella_contractor_estimates');
        
        $estimates = $query->result_array();
        
        return $estimates;
    }
}

if (!function_exists('format_appointment_date')) {
    /**
     * Format appointment date and time for display
     * 
     * @param array $appointment Appointment data
     * @return string Formatted date and time
     */
    function format_appointment_date($appointment)
    {
        if (empty($appointment)) {
            return '';
        }
        
        $date = isset($appointment['date']) ? $appointment['date'] : '';
        $time = isset($appointment['start_hour']) ? $appointment['start_hour'] : '';
        
        if (empty($date)) {
            return '';
        }
        
        $formatted = _d($date);
        
        if (!empty($time)) {
            $formatted .= ' at ' . $time;
        }
        
        return $formatted;
    }
}

if (!function_exists('get_appointment_status_badge')) {
    /**
     * Get HTML badge for appointment status
     * 
     * @param string $status Appointment status
     * @return string HTML badge
     */
    function get_appointment_status_badge($status)
    {
        $badge_class = 'default';
        $status_text = ucfirst($status);
        
        switch ($status) {
            case 'scheduled':
                $badge_class = 'info';
                break;
            case 'complete':
                $badge_class = 'success';
                break;
            case 'cancelled':
                $badge_class = 'danger';
                break;
        }
        
        return '<span class="label label-' . $badge_class . '">' . $status_text . '</span>';
    }
}

if (!function_exists('get_appointments_by_staff')) {
    /**
     * Get appointments assigned to a specific staff member for a lead
     * 
     * @param int $lead_id Lead ID
     * @param int $staff_id Staff ID
     * @return array Array of appointments
     */
    function get_appointments_by_staff($lead_id, $staff_id)
    {
        $CI = &get_instance();
        $appointments = array();
        
        // Check if there's an attendees table join needed
        $CI->db->select('a.*');
        $CI->db->from(db_prefix() . 'appointly_appointments a');
        $CI->db->join(db_prefix() . 'appointly_appointments_attendees aa', 'a.id = aa.appointment_id', 'left');
        $CI->db->where('a.rel_type', 'lead');
        $CI->db->where('a.rel_id', $lead_id);
        $CI->db->group_start();
        $CI->db->where('aa.staff_id', $staff_id);
        $CI->db->or_where('a.created_by', $staff_id);
        $CI->db->group_end();
        $CI->db->group_by('a.id');
        $CI->db->order_by('a.date', 'DESC');
        $query = $CI->db->get();
        
        $appointments = $query->result_array();
        
        return $appointments;
    }
}

/**
 * Generate shareable public link for appointment presentations
 * Direct URL access like leads: /uploads/ella_presentations/general/{filename}
 * 
 * @param int $appointment_id
 * @return string Public URLs (one per line)
 */
if (!function_exists('get_appointment_presentations_public_links')) {
    function get_appointment_presentations_public_links($appointment_id) {
        $CI = &get_instance();
        
        // Get attached presentations
        $CI->db->select('media.*');
        $CI->db->from(db_prefix() . 'ella_appointment_presentations as pivot');
        $CI->db->join(db_prefix() . 'ella_contractor_media as media', 'media.id = pivot.presentation_id');
        $CI->db->where('pivot.appointment_id', $appointment_id);
        $CI->db->where('media.rel_type', 'presentation');
        $CI->db->where('media.active', 1);
        
        $presentations = $CI->db->get()->result();
        
        if (empty($presentations)) {
            return '';
        }
        
        $links = [];
        foreach ($presentations as $presentation) {
            $url = get_ella_presentation_public_url($presentation);
            if ($url) {
                $links[] = $url;
            }
        }
        
        return implode("\n", $links);
    }
}

/**
 * Get formatted public links for SMS/Email
 * 
 * @param int $appointment_id
 * @param string $format 'sms' or 'email'
 * @return string Formatted message with links
 */
if (!function_exists('format_appointment_presentation_links')) {
    function format_appointment_presentation_links($appointment_id, $format = 'sms') {
        $links = get_appointment_presentations_public_links($appointment_id);
        
        if (empty($links)) {
            return '';
        }
        
        $links_array = explode("\n", $links);
        
        if ($format === 'sms') {
            return "\n\nView Presentations:\n" . implode("\n", $links_array);
        } else {
            // Email format with HTML
            $html = '<p><strong>View Presentations:</strong></p><ul>';
            foreach ($links_array as $link) {
                $html .= '<li><a href="' . $link . '" target="_blank">' . $link . '</a></li>';
            }
            $html .= '</ul>';
            return $html;
        }
    }
}
