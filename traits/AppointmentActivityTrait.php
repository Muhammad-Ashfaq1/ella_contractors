<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AppointmentActivityTrait
 * 
 * Unified activity logging system for appointment-related operations
 * Supports: APPOINTMENT, NOTES, ATTACHMENTS, MEASUREMENT, ESTIMATES, PROPOSAL
 * Actions: created, updated, deleted, added, sent, clicked
 * 
 * @package EllaContractors
 * @version 2.0.0
 */
trait AppointmentActivityTrait
{
    /**
     * Unified activity logger - Single entry point for all timeline logs
     * 
     * Usage Examples:
     * - add_activity_log(1, 'APPOINTMENT', 'created', ['subject' => 'Meeting', 'date' => '2025-01-15'])
     * - add_activity_log(1, 'NOTES', 'updated', ['note_id' => 5, 'content' => 'Updated note'])
     * - add_activity_log(1, 'ATTACHMENTS', 'deleted', ['filename' => 'doc.pdf'])
     * - add_activity_log(1, 'MEASUREMENT', 'created', ['tab_name' => 'Room 1', 'items' => 5])
     * - add_activity_log(1, 'ESTIMATES', 'created', ['estimate_id' => 10, 'total' => 5000])
     * 
     * @param int    $appointment_id    Appointment ID (rel_id)
     * @param string $log_type          Log type: APPOINTMENT, NOTES, ATTACHMENTS, MEASUREMENT, ESTIMATES, PROPOSAL
     * @param string $action            Action: created, updated, deleted, added, sent, clicked
     * @param array  $additional_data   Additional data to store (flexible array)
     * @param bool   $integration       Whether from integration (default: false)
     * @return int|false                Log ID on success, false on failure
     */
    public function add_activity_log($appointment_id, $log_type, $action, $additional_data = [], $integration = false)
    {
        $CI = &get_instance();
        
        // Validate log_type
        $valid_log_types = ['APPOINTMENT', 'NOTES', 'ATTACHMENTS', 'MEASUREMENT', 'ESTIMATES', 'PROPOSAL'];
        $log_type = strtoupper($log_type);
        if (!in_array($log_type, $valid_log_types)) {
            log_message('error', 'Invalid log_type: ' . $log_type);
            return false;
        }
        
        // Validate action
        $valid_actions = ['created', 'updated', 'deleted', 'added', 'sent', 'clicked', 'uploaded', 'removed'];
        $action = strtolower($action);
        if (!in_array($action, $valid_actions)) {
            log_message('error', 'Invalid action: ' . $action);
            return false;
        }
        
        // Get staff ID
        $staff_id = 1;
        if (get_staff_user_id()) {
            $staff_id = get_staff_user_id();
        }
        if ($integration === true) {
            $staff_id = 1;
        }
        
        // Update staff last activity
        $CI->db->where('staffid', $staff_id);
        $CI->db->update(db_prefix() . 'staff', ['last_login' => date('Y-m-d H:i:s')]);
        
        // Get organization ID
        $org_id = null;
        if (function_exists('get_organization_id')) {
            $org_id = get_organization_id();
        }
        
        // Build description key for language translation
        $description_key = strtolower($log_type) . '_' . $action;
        
        // Build human-readable description
        $description = $this->build_description($log_type, $action, $additional_data);
        
        // Ensure table columns exist (check and add if needed)
        $this->ensure_log_table_columns();
        
        // Prepare log data
        $log = [
            'rel_type'        => 'appointment',
            'rel_id'          => $appointment_id,
            'org_id'          => $org_id,
            'staff_id'        => $staff_id,
            'log_type'        => $log_type,
            'action'          => $action,
            'description'     => $description,
            'description_key' => $description_key,
            'additional_data' => !empty($additional_data) ? json_encode($additional_data) : null,
            'date'            => date('Y-m-d H:i:s'),
            'full_name'       => get_staff_full_name($staff_id),
            'created_at'      => date('Y-m-d H:i:s')
        ];
        
        // Insert log
        $CI->db->insert(db_prefix() . 'ella_appointment_activity_log', $log);
        $log_id = $CI->db->insert_id();
        
        // Trigger hook for extensibility
        if ($log_id) {
            hooks()->do_action('appointment_activity_logged', [
                'log_id'         => $log_id,
                'appointment_id' => $appointment_id,
                'log_type'       => $log_type,
                'action'         => $action,
                'staff_id'       => $staff_id,
                'data'           => $additional_data
            ]);
        }
        
        return $log_id ? $log_id : false;
    }
    
    /**
     * Get timeline (activity logs) with optional filtering
     * 
     * Usage Examples:
     * - get_timeline(1) - Get all logs for appointment ID 1
     * - get_timeline(1, ['log_type' => 'NOTES']) - Only notes logs
     * - get_timeline(1, ['action' => 'created']) - Only creation logs
     * - get_timeline(1, ['log_type' => 'MEASUREMENT', 'action' => 'deleted']) - Deleted measurements
     * - get_timeline(1, ['limit' => 10]) - Latest 10 logs
     * - get_timeline(1, ['order' => 'ASC']) - Oldest first
     * 
     * @param int   $appointment_id  Appointment ID
     * @param array $filters         Optional filters: log_type, action, limit, order (ASC/DESC)
     * @return array                 Array of activity logs
     */
    public function get_timeline($appointment_id, $filters = [])
    {
        $CI = &get_instance();
        
        // Base query
        $CI->db->where('rel_id', $appointment_id);
        $CI->db->where('rel_type', 'appointment');
        
        // Apply filters
        if (!empty($filters['log_type'])) {
            $CI->db->where('log_type', strtoupper($filters['log_type']));
        }
        
        if (!empty($filters['action'])) {
            $CI->db->where('action', strtolower($filters['action']));
        }
        
        // Sorting
        $order = !empty($filters['order']) ? strtoupper($filters['order']) : 'DESC';
        $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
        $CI->db->order_by('date', $order);
        
        // Limit
        if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
            $CI->db->limit($filters['limit']);
        }
        
        $logs = $CI->db->get(db_prefix() . 'ella_appointment_activity_log')->result_array();
        
        // Decode JSON additional_data for each log
        foreach ($logs as &$log) {
            if (!empty($log['additional_data'])) {
                $decoded = json_decode($log['additional_data'], true);
                $log['additional_data'] = $decoded ? $decoded : $log['additional_data'];
            }
        }
        
        return $logs;
    }
    
    /**
     * Build human-readable description from log_type, action, and data
     * 
     * @param string $log_type         Log type
     * @param string $action           Action
     * @param array  $additional_data  Additional data
     * @return string                  Human-readable description
     */
    private function build_description($log_type, $action, $additional_data)
    {
        $entity = $this->get_entity_display_name($log_type);
        $action_display = ucfirst($action);
        
        // Extract key info from additional_data
        $name = '';
        if (!empty($additional_data['subject'])) {
            $name = $additional_data['subject'];
        } elseif (!empty($additional_data['tab_name'])) {
            $name = $additional_data['tab_name'];
        } elseif (!empty($additional_data['filename'])) {
            $name = $additional_data['filename'];
        } elseif (!empty($additional_data['estimate_id'])) {
            $name = 'Estimate #' . $additional_data['estimate_id'];
        } elseif (!empty($additional_data['note_id'])) {
            $name = 'Note #' . $additional_data['note_id'];
        }
        
        // Build description based on action
        switch ($action) {
            case 'created':
            case 'added':
                return $name ? "{$entity} '{$name}' {$action_display}" : "{$entity} {$action_display}";
            
            case 'updated':
                if (!empty($additional_data['changes'])) {
                    $changes = is_array($additional_data['changes']) ? implode(', ', array_keys($additional_data['changes'])) : '';
                    return $name ? "{$entity} '{$name}' Updated ({$changes})" : "{$entity} Updated";
                }
                return $name ? "{$entity} '{$name}' Updated" : "{$entity} Updated";
            
            case 'deleted':
            case 'removed':
                return $name ? "{$entity} '{$name}' {$action_display}" : "{$entity} {$action_display}";
            
            case 'sent':
                if ($log_type === 'SMS' && !empty($additional_data['phone_number'])) {
                    return "SMS Sent to {$additional_data['phone_number']}";
                } elseif ($log_type === 'EMAIL' && !empty($additional_data['email_address'])) {
                    return "Email Sent to {$additional_data['email_address']}";
                }
                return "{$entity} Sent";
            
            case 'clicked':
                return "{$entity} Clicked";
            
            case 'uploaded':
                return $name ? "{$entity} '{$name}' Uploaded" : "{$entity} Uploaded";
            
            default:
                return "{$entity} {$action_display}";
        }
    }
    
    /**
     * Get display name for entity type
     * 
     * @param string $log_type  Log type
     * @return string           Display name
     */
    private function get_entity_display_name($log_type)
    {
        $names = [
            'APPOINTMENT'  => 'Appointment',
            'NOTES'        => 'Note',
            'ATTACHMENTS'  => 'Attachment',
            'MEASUREMENT'  => 'Measurement',
            'ESTIMATES'    => 'Estimate',
            'PROPOSAL'     => 'Proposal',
            'SMS'          => 'SMS',
            'EMAIL'        => 'Email'
        ];
        
        return $names[$log_type] ?? ucfirst(strtolower($log_type));
    }
    
    /**
     * Ensure activity log table has required columns
     * Adds log_type and action columns if they don't exist
     * 
     * @return void
     */
    private function ensure_log_table_columns()
    {
        $CI = &get_instance();
        $table = db_prefix() . 'ella_appointment_activity_log';
        
        // Check and add log_type column
        if (!$CI->db->field_exists('log_type', $table)) {
            try {
                $CI->db->query("ALTER TABLE `{$table}` ADD COLUMN `log_type` VARCHAR(50) NULL AFTER `staff_id`");
                $CI->db->query("ALTER TABLE `{$table}` ADD INDEX `idx_log_type` (`log_type`)");
                log_message('info', 'Added log_type column to ella_appointment_activity_log table');
            } catch (Exception $e) {
                log_message('error', 'Failed to add log_type column: ' . $e->getMessage());
            }
        }
        
        // Check and add action column
        if (!$CI->db->field_exists('action', $table)) {
            try {
                $CI->db->query("ALTER TABLE `{$table}` ADD COLUMN `action` VARCHAR(50) NULL AFTER `log_type`");
                $CI->db->query("ALTER TABLE `{$table}` ADD INDEX `idx_action` (`action`)");
                log_message('info', 'Added action column to ella_appointment_activity_log table');
            } catch (Exception $e) {
                log_message('error', 'Failed to add action column: ' . $e->getMessage());
            }
        }
    }
    
    // ==================== BACKWARD COMPATIBILITY METHODS ====================
    // These methods maintain compatibility with existing code
    // They now use the unified add_activity_log() internally
    
    /**
     * @deprecated Use add_activity_log() instead
     */
    public function log_activity($appointment_id, $activity_type, $entity_type, $entity_name = '', $data = [])
    {
        // Map old entity_type to new log_type
        $log_type_map = [
            'appointment' => 'APPOINTMENT',
            'note'        => 'NOTES',
            'attachment'  => 'ATTACHMENTS',
            'file'        => 'ATTACHMENTS',
            'measurement' => 'MEASUREMENT',
            'estimate'    => 'ESTIMATES',
            'proposal'    => 'PROPOSAL',
            'sms'         => 'SMS',
            'email'       => 'EMAIL'
        ];
        
        $log_type = $log_type_map[strtolower($entity_type)] ?? strtoupper($entity_type);
        
        // Add entity name to data if provided
        if ($entity_name) {
            $data['entity_name'] = $entity_name;
        }
        
        return $this->add_activity_log($appointment_id, $log_type, $activity_type, $data);
    }
    
    /**
     * @deprecated Use add_activity_log() instead
     */
    public function log_appointment_attachment_activity($appointment_id, $action, $filename, $additional_data = [])
    {
        $additional_data['filename'] = $filename;
        return $this->add_activity_log($appointment_id, 'ATTACHMENTS', $action, $additional_data);
    }
    
    /**
     * @deprecated Use get_timeline() instead
     */
    public function get_appointment_activity_log($appointment_id)
    {
        return $this->get_timeline($appointment_id);
    }
}
