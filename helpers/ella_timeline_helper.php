<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ella Timeline Helper
 * 
 * Helper functions for appointment timeline functionality
 * Following the same patterns as existing Perfex CRM helpers
 * 
 * @package EllaContractors
 * @version 1.0.0
 */

if (!function_exists('ella_log_appointment_activity')) {
    /**
     * Log appointment activity using the trait
     * 
     * @param int    $appointment_id    Appointment ID
     * @param string $description_key   Activity description key
     * @param bool   $integration       Whether this is from integration
     * @param string $additional_data   Additional data to store
     * @return int|false                Log ID on success, false on failure
     */
    function ella_log_appointment_activity($appointment_id, $description_key, $integration = false, $additional_data = '')
    {
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        
        return $CI->appointments_model->log_appointment_activity($appointment_id, $description_key, $integration, $additional_data);
    }
}

if (!function_exists('ella_get_appointment_timeline')) {
    /**
     * Get appointment timeline activities
     * 
     * @param int $appointment_id Appointment ID
     * @return array              Timeline activities
     */
    function ella_get_appointment_timeline($appointment_id)
    {
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        
        return $CI->appointments_model->get_appointment_timeline($appointment_id);
    }
}

if (!function_exists('ella_log_measurement_activity')) {
    /**
     * Log measurement activity for appointment
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $action        'added' or 'removed'
     * @param string $measurement   Measurement details
     * @return int|false            Log ID on success, false on failure
     */
    function ella_log_measurement_activity($appointment_id, $action, $measurement)
    {
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        
        if ($action === 'added') {
            return $CI->appointments_model->log_measurement_added($appointment_id, $measurement);
        } elseif ($action === 'removed') {
            return $CI->appointments_model->log_measurement_removed($appointment_id, $measurement);
        }
        
        return false;
    }
}


if (!function_exists('ella_log_scheduled_process')) {
    /**
     * Log scheduled event process
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $process       Process description
     * @param string $status        Process status
     * @return int|false            Log ID on success, false on failure
     */
    function ella_log_scheduled_process($appointment_id, $process, $status = 'completed')
    {
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        
        return $CI->appointments_model->log_scheduled_process($appointment_id, $process, $status);
    }
}

if (!function_exists('ella_get_activity_icon')) {
    /**
     * Get activity icon based on description key
     * 
     * @param string $description_key Activity description key
     * @return string                FontAwesome icon class
     */
    function ella_get_activity_icon($description_key)
    {
        $icons = [
            'appointment_activity_created' => 'fa fa-calendar-plus',
            'appointment_activity_updated' => 'fa fa-edit',
            'appointment_activity_status_changed' => 'fa fa-exchange',
            'appointment_activity_measurement_added' => 'fa fa-plus-square',
            'appointment_activity_measurement_removed' => 'fa fa-minus-square',
            'appointment_activity_process' => 'fa fa-cogs',
            'appointment_activity_deleted' => 'fa fa-trash'
        ];
        
        return $icons[$description_key] ?? 'fa fa-info-circle';
    }
}

if (!function_exists('ella_get_activity_color')) {
    /**
     * Get activity color based on description key
     * 
     * @param string $description_key Activity description key
     * @return string                Bootstrap color class
     */
    function ella_get_activity_color($description_key)
    {
        $colors = [
            'appointment_activity_created' => 'success',
            'appointment_activity_updated' => 'info',
            'appointment_activity_status_changed' => 'warning',
            'appointment_activity_measurement_added' => 'success',
            'appointment_activity_measurement_removed' => 'danger',
            'appointment_activity_process' => 'secondary',
            'appointment_activity_deleted' => 'danger'
        ];
        
        return $colors[$description_key] ?? 'default';
    }
}

if (!function_exists('ella_format_timeline_activity')) {
    /**
     * Format timeline activity for display
     * 
     * @param array $activity Activity data
     * @return array          Formatted activity data
     */
    function ella_format_timeline_activity($activity)
    {
        return [
            'id' => $activity['id'],
            'type' => 'activity',
            'date' => $activity['date'],
            'staff_id' => $activity['staff_id'],
            'full_name' => $activity['full_name'],
            'description' => $activity['description'],
            'description_key' => $activity['description_key'],
            'additional_data' => $activity['additional_data'],
            'icon' => ella_get_activity_icon($activity['description_key']),
            'color' => ella_get_activity_color($activity['description_key']),
            'time_ago' => time_ago($activity['date']),
            'formatted_date' => _dt($activity['date'])
        ];
    }
}
