<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Calendar Sync Helper Functions
 * 
 * Contains all calendar synchronization logic for appointments
 * Used by Appointments controller
 * Supports: Google Calendar, Outlook Calendar
 * 
 * @package EllaContractors
 * @version 1.0.0
 */

if (!function_exists('sync_appointment_to_calendar')) {
    /**
     * Sync appointment to calendar (Google or Outlook)
     * Handles sync for creator and all attendees who have the calendar connected
     * 
     * @param object $controller Controller instance
     * @param int $appointment_id Appointment ID
     * @param string $action Action: 'create', 'update', or 'delete'
     * @param string $provider Calendar provider: 'google' or 'outlook'
     * @return bool Success status
     */
    function sync_appointment_to_calendar($controller, $appointment_id, $action = 'create', $provider = 'google')
    {
        $CI = &get_instance();
        
        // Load appropriate calendar sync library
        $library_map = [
            'google' => 'ella_contractors/Google_calendar_sync',
            'outlook' => 'ella_contractors/Outlook_calendar_sync'
        ];
        
        $library_name = isset($library_map[$provider]) ? $library_map[$provider] : null;
        if (!$library_name) {
            log_message('error', 'Invalid calendar provider: ' . $provider);
            return false;
        }

        try {
            $CI->load->library($library_name);
        } catch (Exception $e) {
            log_message('error', 'Failed to load ' . $provider . ' calendar sync library: ' . $e->getMessage());
            return false;
        }

        // Get sync library instance
        $sync_property = $provider . '_calendar_sync';
        if (!isset($CI->$sync_property)) {
            log_message('error', 'Calendar sync library not loaded: ' . $provider);
            return false;
        }
        $sync_lib = $CI->$sync_property;

        // Load model if not already loaded
        if (!isset($CI->appointments_model)) {
            $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        }

        // Get appointment data
        $appointment = $CI->appointments_model->get_appointment($appointment_id);
        if (!$appointment || $appointment->source !== 'ella_contractor') {
            return false;
        }

        try {
            // Get all staff who should have this appointment in their calendar
            $staff_to_sync = [];
            
            // Add creator
            if (!empty($appointment->created_by)) {
                $staff_to_sync[] = $appointment->created_by;
            }
            
            // Add attendees
            $attendees = $CI->appointments_model->get_appointment_attendees($appointment_id);
            foreach ($attendees as $attendee) {
                if (!empty($attendee['staffid']) && !in_array($attendee['staffid'], $staff_to_sync)) {
                    $staff_to_sync[] = $attendee['staffid'];
                }
            }

            // Sync for each connected staff member
            $synced_count = 0;
            foreach ($staff_to_sync as $staff_id) {
                // Check if staff has calendar connected
                $status = $sync_lib->get_connection_status($staff_id);
                if (!$status || !$status['connected']) {
                    continue; // Skip if not connected
                }

                // Perform sync based on action
                $result = false;
                switch ($action) {
                    case 'create':
                        $result = $sync_lib->create_event($appointment_id, $staff_id);
                        break;
                    
                    case 'update':
                        // Check if appointment status changed to cancelled
                        if (isset($appointment->appointment_status) && $appointment->appointment_status === 'cancelled') {
                            $result = $sync_lib->delete_event($appointment_id, $staff_id);
                        } else {
                            $result = $sync_lib->update_event($appointment_id, $staff_id);
                        }
                        break;
                    
                    case 'delete':
                        $result = $sync_lib->delete_event($appointment_id, $staff_id);
                        break;
                    
                    default:
                        continue 2; // Skip to next staff
                }

                if ($result !== false) {
                    $synced_count++;
                }
            }

            return $synced_count > 0;
        } catch (Exception $e) {
            log_message('error', ucfirst($provider) . ' Calendar sync error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('sync_calendar_assignee_change')) {
    /**
     * Handle assignee change - sync to calendar (Google or Outlook)
     * This is called when attendees are updated
     * 
     * @param object $controller Controller instance
     * @param int $appointment_id Appointment ID
     * @param array $old_assignees Old attendees list
     * @param array $new_assignees New attendees list
     * @param string $provider Calendar provider: 'google' or 'outlook'
     */
    function sync_calendar_assignee_change($controller, $appointment_id, $old_assignees, $new_assignees, $provider = 'google')
    {
        $CI = &get_instance();
        
        // Load appropriate calendar sync library
        $library_map = [
            'google' => 'ella_contractors/Google_calendar_sync',
            'outlook' => 'ella_contractors/Outlook_calendar_sync'
        ];
        
        $library_name = isset($library_map[$provider]) ? $library_map[$provider] : null;
        if (!$library_name) {
            log_message('error', 'Invalid calendar provider: ' . $provider);
            return;
        }

        try {
            $CI->load->library($library_name);
        } catch (Exception $e) {
            log_message('error', 'Failed to load ' . $provider . ' calendar sync library: ' . $e->getMessage());
            return;
        }

        // Get sync library instance
        $sync_property = $provider . '_calendar_sync';
        if (!isset($CI->$sync_property)) {
            log_message('error', 'Calendar sync library not loaded: ' . $provider);
            return;
        }
        $sync_lib = $CI->$sync_property;

        // Load model if not already loaded
        if (!isset($CI->appointments_model)) {
            $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        }

        $appointment = $CI->appointments_model->get_appointment($appointment_id);
        if (!$appointment || $appointment->source !== 'ella_contractor') {
            return;
        }

        // Get old and new staff IDs
        $old_staff_ids = [];
        if (!empty($old_assignees)) {
            $old_staff_ids = array_column($old_assignees, 'staffid');
        }
        // Always include creator in old list
        if (!empty($appointment->created_by) && !in_array($appointment->created_by, $old_staff_ids)) {
            $old_staff_ids[] = $appointment->created_by;
        }

        $new_staff_ids = [];
        if (!empty($new_assignees)) {
            $new_staff_ids = array_column($new_assignees, 'staffid');
        }
        // Always include creator in new list
        if (!empty($appointment->created_by) && !in_array($appointment->created_by, $new_staff_ids)) {
            $new_staff_ids[] = $appointment->created_by;
        }

        // Find removed staff (need to delete from their calendars, but not creator)
        $removed_staff = array_diff($old_staff_ids, $new_staff_ids);
        foreach ($removed_staff as $staff_id) {
            // Don't delete from creator's calendar (they should always have it)
            if ($staff_id == $appointment->created_by) {
                continue;
            }
            
            $status = $sync_lib->get_connection_status($staff_id);
            if ($status && $status['connected']) {
                $sync_lib->delete_event($appointment_id, $staff_id);
            }
        }

        // Find added staff (need to create in their calendars)
        $added_staff = array_diff($new_staff_ids, $old_staff_ids);
        foreach ($added_staff as $staff_id) {
            $status = $sync_lib->get_connection_status($staff_id);
            if ($status && $status['connected']) {
                // Check if event already exists for this staff (should not, but safety check)
                $event_id_field = $provider . '_event_id';
                $existing_event_id = null;
                if ($staff_id == $appointment->created_by && !empty($appointment->$event_id_field)) {
                    $existing_event_id = $appointment->$event_id_field;
                }
                
                if ($existing_event_id) {
                    // Update existing event
                    $sync_lib->update_event($appointment_id, $staff_id);
                } else {
                    // Create new event
                    $sync_lib->create_event($appointment_id, $staff_id);
                }
            }
        }
    }
}


