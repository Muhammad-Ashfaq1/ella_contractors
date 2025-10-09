<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Timeline Helper Functions
 * Contains helper functions for timeline formatting
 */

if (!function_exists('get_timeline_action_label')) {
    /**
     * Get timeline action label based on description key
     * Helper function for timeline formatting
     * 
     * @param string $description_key The description key
     * @return string                Formatted action label
     */
    function get_timeline_action_label($description_key)
    {
        $CI =& get_instance();
        
        // Load language file if not already loaded
        $CI->lang->load('ella_contractors/ella_contractors', 'english');
        
        $action_map = [
            'appointment_created' => _l('timeline_action_appointment_created'),
            'appointment_updated' => _l('timeline_action_appointment_updated'),
            'appointment_status_changed' => _l('timeline_action_status_changed'),
            'measurement_created' => _l('timeline_action_measurement_added'),
            'measurement_updated' => _l('timeline_action_measurement_added'),
            'measurement_deleted' => _l('timeline_action_measurement_removed'),
            'note_created' => _l('timeline_action_note_added'),
            'note_updated' => _l('timeline_action_note_added'),
            'process_completed' => _l('timeline_action_process_completed'),
            'process_failed' => _l('timeline_action_process_failed'),
            'appointment_deleted' => _l('timeline_action_appointment_deleted')
        ];
        
        // Return mapped action or fallback to formatted description key
        if (isset($action_map[$description_key])) {
            return $action_map[$description_key];
        }
        
        // Fallback: convert description_key to readable format
        $parts = explode('_', $description_key);
        return strtoupper(implode(' ', $parts));
    }
}
