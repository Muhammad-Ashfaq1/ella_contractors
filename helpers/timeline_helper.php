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
            // Appointment activities
            'appointment_created' => _l('timeline_action_appointment_created'),
            'appointment_updated' => _l('timeline_action_appointment_updated'),
            'appointment_status_changed' => _l('timeline_action_status_changed'),
            'appointment_deleted' => _l('timeline_action_appointment_deleted'),
            
            // Measurement activities
            'measurement_created' => _l('timeline_action_measurement_added'),
            'measurement_updated' => _l('timeline_action_measurement_updated'),
            'measurement_deleted' => _l('timeline_action_measurement_removed'),
            
            // Note activities
            'note_created' => _l('timeline_action_note_added'),
            'note_updated' => _l('timeline_action_note_updated'),
            'note_deleted' => _l('timeline_action_note_removed'),
            
            // Attachment activities
            'attachments_uploaded' => _l('timeline_action_attachment_uploaded'),
            'attachments_deleted' => _l('timeline_action_attachment_removed'),
            'appointment_activity_attachment_uploaded' => _l('timeline_action_attachment_uploaded'),
            'appointment_activity_attachment_deleted' => _l('timeline_action_attachment_removed'),
            
            // Proposal activities
            'proposal_created' => _l('timeline_action_proposal_created'),
            'proposal_updated' => _l('timeline_action_proposal_updated'),
            'proposal_deleted' => _l('timeline_action_proposal_deleted'),
            
            // Process activities
            'process_completed' => _l('timeline_action_process_completed'),
            'process_failed' => _l('timeline_action_process_failed')
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
