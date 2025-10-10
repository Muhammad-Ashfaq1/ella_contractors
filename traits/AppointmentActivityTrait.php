<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AppointmentActivityTrait
 * 
 * Reusable trait for logging appointment activities following the leads activity pattern
 * Provides consistent logging across all appointment-related operations
 * 
 * @package EllaContractors
 * @version 1.0.0
 */
trait AppointmentActivityTrait
{
    /**
     * Log appointment activity
     * Following the same pattern as Leads_model::log_lead_activity
     * 
     * @param int    $appointment_id    Appointment ID
     * @param string $description_key   Activity description key for translation
     * @param bool   $integration       Whether this is from integration (default: false)
     * @param string $additional_data   Additional data to store (serialized)
     * @return int|false                Log ID on success, false on failure
     */
    public function log_appointment_activity($appointment_id, $description_key, $integration = false, $additional_data = '')
    {
        $CI = &get_instance();
        
        // Get staff ID - use current logged in staff or default to 1
        $staff_id = 1;
        if (get_staff_user_id()) {
            $staff_id = get_staff_user_id();
        }
        
        // If integration, use staff ID 1
        if ($integration == true) {
            $staff_id = 1;
        }
        
        // Update last_activity in staff table (following leads pattern)
        $staff_data['last_login'] = date('Y-m-d H:i:s');
        $CI->db->where('staffid', $staff_id);
        $CI->db->update(db_prefix() . 'staff', $staff_data);
        
        // Get organization ID if available
        $org_id = null;
        if (function_exists('get_organization_id')) {
            $org_id = get_organization_id();
        }
        
        // Prepare log data
        $log = [
            'rel_type'        => 'appointment',
            'rel_id'          => $appointment_id,
            'org_id'          => $org_id,
            'staff_id'        => $staff_id,
            'description'     => _l($description_key), // Translate the description
            'description_key' => $description_key,
            'additional_data' => $additional_data,
            'date'            => date('Y-m-d H:i:s'),
            'full_name'       => get_staff_full_name($staff_id),
        ];
        
        // Insert into activity log table
        $CI->db->insert(db_prefix() . 'ella_appointment_activity_log', $log);
        $log_id = $CI->db->insert_id();
        
        if ($log_id) {
            // Trigger hook for additional processing
            hooks()->do_action('appointment_activity_logged', [
                'log_id' => $log_id,
                'appointment_id' => $appointment_id,
                'description_key' => $description_key,
                'staff_id' => $staff_id
            ]);
        }
        
        return $log_id ? $log_id : false;
    }
    
    /**
     * Get appointment activity log
     * Following the same pattern as Leads_model::get_lead_activity_log
     * 
     * @param int $appointment_id Appointment ID
     * @return array              Array of activity logs
     */
    public function get_appointment_activity_log($appointment_id)
    {
        $CI = &get_instance();
        
        $sorting = hooks()->apply_filters('appointment_activity_log_default_sort', 'DESC');
        
        $CI->db->where('rel_id', $appointment_id);
        $CI->db->where('rel_type', 'appointment');
        $CI->db->order_by('date', $sorting);
        
        return $CI->db->get(db_prefix() . 'ella_appointment_activity_log')->result_array();
    }
    
    /**
     * Log appointment creation
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $subject       Appointment subject
     * @param string $date          Appointment date
     * @param string $time          Appointment time
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_created($appointment_id, $subject, $date, $time)
    {
        $additional_data = serialize([
            'subject' => $subject,
            'date' => $date,
            'time' => $time
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_created', 
            false, 
            $additional_data
        );
    }
    
    /**
     * Log appointment update
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $subject       Appointment subject
     * @param array  $changes       Array of changed fields
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_updated($appointment_id, $subject, $changes = [])
    {
        $additional_data = serialize([
            'subject' => $subject,
            'changes' => $changes
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_updated', 
            false, 
            $additional_data
        );
    }
    
    /**
     * Log appointment status change
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $old_status    Previous status
     * @param string $new_status    New status
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_status_changed($appointment_id, $old_status, $new_status)
    {
        $additional_data = serialize([
            'old_status' => $old_status,
            'new_status' => $new_status
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_status_changed', 
            false, 
            $additional_data
        );
    }
    
    
    /**
     * Log appointment measurement added
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $measurement   Measurement details
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_measurement_added($appointment_id, $measurement)
    {
        $additional_data = serialize([
            'measurement' => $measurement
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_measurement_added', 
            false, 
            $additional_data
        );
    }
    
    /**
     * Log appointment measurement removed
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $measurement   Measurement details
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_measurement_removed($appointment_id, $measurement)
    {
        $additional_data = serialize([
            'measurement' => $measurement
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_measurement_removed', 
            false, 
            $additional_data
        );
    }
    
    /**
     * Log appointment scheduled event process
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $process       Process description
     * @param string $status        Process status
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_process($appointment_id, $process, $status = 'completed')
    {
        $additional_data = serialize([
            'process' => $process,
            'status' => $status
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_process', 
            false, 
            $additional_data
        );
    }
    
    /**
     * Log appointment deletion
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $subject       Appointment subject
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_deleted($appointment_id, $subject)
    {
        $additional_data = serialize([
            'subject' => $subject
        ]);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            'appointment_activity_deleted', 
            false, 
            $additional_data
        );
    }
    
    /**
     * Log appointment attachment activity (generic method)
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $action        Action type (uploaded, deleted, etc.)
     * @param string $filename      File name
     * @param array  $additional_data Additional data (file_size, file_type, etc.)
     * @return int|false            Log ID on success, false on failure
     */
    public function log_appointment_attachment_activity($appointment_id, $action, $filename, $additional_data = [])
    {
        // Build description key dynamically
        $description_key = 'appointment_activity_attachment_' . $action;
        
        // Merge filename with additional data
        $data = array_merge([
            'filename' => $filename
        ], $additional_data);
        
        $serialized_data = serialize($data);
        
        return $this->log_appointment_activity(
            $appointment_id, 
            $description_key, 
            false, 
            $serialized_data
        );
    }
    
    
    /**
     * Generic activity logger - handles all activity types dynamically
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $activity_type Activity type (created, updated, deleted, sent, etc.)
     * @param string $entity_type   Entity type (estimate, measurement, note, email, sms, file)
     * @param string $entity_name   Name/title of the entity
     * @param array  $data          Additional data to store
     * @param string $description   Custom description (optional)
     * @return int|false            Log ID on success, false on failure
     */
    public function log_activity($appointment_id, $activity_type, $entity_type, $entity_name = '', $data = [], $description = '')
    {
        // Build description if not provided
        if (empty($description)) {
            $description = $this->build_activity_description($activity_type, $entity_type, $entity_name, $data);
        }
        
        // Build description key
        $description_key = $entity_type . '_' . $activity_type;
        
        // Prepare additional data
        $additional_data = serialize(array_merge($data, [
            'entity_type' => $entity_type,
            'entity_name' => $entity_name,
            'activity_type' => $activity_type
        ]));
        
        return $this->log_appointment_activity($appointment_id, $description_key, $description, $additional_data);
    }
    
    /**
     * Build activity description dynamically
     * 
     * @param string $activity_type Activity type
     * @param string $entity_type   Entity type
     * @param string $entity_name   Entity name
     * @param array  $data          Additional data
     * @return string               Generated description
     */
    private function build_activity_description($activity_type, $entity_type, $entity_name, $data)
    {
        // Proper entity display names
        $entity_display = $this->get_proper_entity_name($entity_type);
        $activity_display = $this->get_proper_activity_name($activity_type);
        
        switch ($activity_type) {
            case 'created':
                if ($entity_name) {
                    return "{$entity_display} '{$entity_name}' Created";
                }
                return "{$entity_display} Created";
            case 'updated':
                if ($entity_name) {
                    return "{$entity_display} '{$entity_name}' Updated";
                }
                return "{$entity_display} Updated";
            case 'deleted':
                if ($entity_name) {
                    return "{$entity_display} '{$entity_name}' Deleted";
                }
                return "{$entity_display} Deleted";
            case 'sent':
                if ($entity_type === 'sms') {
                    $phone = $data['phone_number'] ?? 'unknown';
                    return "SMS Sent to {$phone}";
                } elseif ($entity_type === 'email') {
                    $email = $data['email_address'] ?? 'unknown';
                    $subject = $data['subject'] ?? '';
                    return "Email Sent to {$email}" . ($subject ? ": {$subject}" : '');
                }
                return "{$entity_display} Sent";
            case 'clicked':
                if ($entity_type === 'email') {
                    $email = $data['email_address'] ?? 'unknown';
                    return "Email Button Clicked for {$email}";
                }
                return "{$entity_display} Clicked";
            case 'added':
                return "{$entity_display} Added to Appointment";
            default:
                return "{$entity_display} {$activity_display}";
        }
    }
    
    /**
     * Get proper entity display name
     * 
     * @param string $entity_type Entity type
     * @return string             Proper display name
     */
    private function get_proper_entity_name($entity_type)
    {
        $names = [
            'appointment' => 'Appointment',
            'estimate' => 'Estimate',
            'measurement' => 'Measurement',
            'note' => 'Note',
            'email' => 'Email',
            'sms' => 'SMS',
            'file' => 'File',
            'attachment' => 'Attachment'
        ];
        
        return $names[$entity_type] ?? ucfirst($entity_type);
    }
    
    /**
     * Get proper activity display name
     * 
     * @param string $activity_type Activity type
     * @return string               Proper display name
     */
    private function get_proper_activity_name($activity_type)
    {
        $names = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'sent' => 'Sent',
            'clicked' => 'Clicked',
            'added' => 'Added'
        ];
        
        return $names[$activity_type] ?? ucfirst($activity_type);
    }
}
