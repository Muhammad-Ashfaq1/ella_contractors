<?php defined('BASEPATH') or exit('No direct script access allowed');

// Load the AppointmentActivityTrait
require_once(module_dir_path('ella_contractors') . 'traits/AppointmentActivityTrait.php');

class Ella_appointments_model extends App_Model
{
    use AppointmentActivityTrait;
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get default appointment type ID (English)
     * Returns the ID of "English" appointment type from appointly_appointment_types
     * @return int|null
     */
    private function get_default_type_id()
    {
        $english_type = $this->db->where('type', 'English')
                                 ->get(db_prefix() . 'appointly_appointment_types')
                                 ->row();
        
        if ($english_type && !empty($english_type->id) && $english_type->id > 0) {
            return $english_type->id;
        }
        
        return null;
    }

    /**
     * Get all appointments with additional EllaContractor data
     * Only shows appointments created from EllaContractors module
     */
    public function get_appointments($where = [])
    {
        $this->db->select('a.*, 
                          CONCAT(s.firstname, " ", s.lastname) as created_by_name,
                          c.company as client_name,
                          l.name as lead_name,
                          at.type as appointment_type');
        $this->db->from(db_prefix() . 'appointly_appointments a');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = a.created_by', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'leads l', 'l.id = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'appointly_appointment_types at', 'at.id = a.type_id', 'left');
        
        // Filter to show only appointments created from EllaContractors module
        $this->db->where('a.source', 'ella_contractor');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by('a.date', 'DESC');
        $this->db->order_by('a.start_hour', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get single appointment by ID
     * Only returns appointments created from EllaContractors module
     */
    public function get_appointment($id)
    {
        $this->db->select('a.*, 
                          CONCAT(s.firstname, " ", s.lastname) as created_by_name,
                          c.company as client_name,
                          l.name as lead_name,
                          at.type as appointment_type');
        $this->db->from(db_prefix() . 'appointly_appointments a');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = a.created_by', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'leads l', 'l.id = a.contact_id', 'left');
        $this->db->join(db_prefix() . 'appointly_appointment_types at', 'at.id = a.type_id', 'left');
        $this->db->where('a.id', $id);
        $this->db->where('a.source', 'ella_contractor');
        
        return $this->db->get()->row();
    }

    /**
     * Create new appointment
     */
    public function create_appointment($data)
    {
        $data['created_by'] = get_staff_user_id();
        
        // Set default type_id to "English" if not provided or empty
        if (!isset($data['type_id']) || empty($data['type_id']) || $data['type_id'] == 0) {
            $data['type_id'] = $this->get_default_type_id();
        }
        
        $this->db->insert(db_prefix() . 'appointly_appointments', $data);
        
        // Check for database errors
        if ($this->db->error()['code'] != 0) {
            $error = $this->db->error();
            log_message('error', 'Appointment Creation Failed | Error: ' . $error['message']);
            return false;
        }
        
        $appointment_id = $this->db->insert_id();
        
        if ($appointment_id) {
            // Log general activity
            log_activity('New Appointment Created [ID: ' . $appointment_id . ', Subject: ' . $data['subject'] . ']');
            
            // Log to timeline using unified method
            $this->add_activity_log(
                $appointment_id, 
                'APPOINTMENT', 
                'created', 
                [
                    'subject' => $data['subject'],
                    'date' => $data['date'] ?? '',
                    'time' => $data['start_hour'] ?? '',
                    'contact_id' => $data['contact_id'] ?? null,
                    'type_id' => $data['type_id'] ?? null
                ]
            );
            
            return $appointment_id;
        }
        
        return false;
    }

    /**
     * Update appointment
     */
    public function update_appointment($id, $data)
    {
        // Get original data for comparison
        $original = $this->get_appointment($id);
        if (!$original) {
            return false;
        }
        
        // Track changes
        $changes = [];
        $fields_to_track = ['subject', 'date', 'start_hour', 'end_time', 'description', 'location', 'appointment_status'];
        
        foreach ($fields_to_track as $field) {
            if (isset($data[$field]) && $data[$field] != $original->$field) {
                $changes[$field] = [
                    'old' => $original->$field,
                    'new' => $data[$field]
                ];
            }
        }

        // For update, don't override type_id or status with defaults
        // Remove them from $data if not provided to preserve existing values
        if (!isset($data['type_id']) || empty($data['type_id'])) {
            unset($data['type_id']);
        }
        
        if (!isset($data['appointment_status']) || empty($data['appointment_status'])) {
            unset($data['appointment_status']);
        }
        
        if (!isset($data['status']) || empty($data['status'])) {
            unset($data['status']);
        }
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appointly_appointments', $data);
        
        // Check for database errors
        if ($this->db->error()['code'] != 0) {
            $error = $this->db->error();
            log_message('error', 'Appointment Update Failed - ID: ' . $id . ' | Error: ' . $error['message']);
            return false;
        }
        
        if ($this->db->affected_rows() >= 0) {
            // Log general activity (only if there were actual changes)
            if (!empty($changes)) {
                log_activity('Appointment Updated [ID: ' . $id . ', Subject: ' . ($data['subject'] ?? $original->subject) . ']');
                
                // Log to timeline using unified method
                $this->add_activity_log(
                    $id, 
                    'APPOINTMENT', 
                    'updated', 
                    [
                        'subject' => $data['subject'] ?? $original->subject,
                        'changes' => $changes,
                        'changed_fields' => array_keys($changes)
                    ]
                );
                
                // Check if status changed - log separately for better filtering
                if (isset($changes['appointment_status'])) {
                    $this->add_activity_log(
                        $id,
                        'APPOINTMENT',
                        'updated',
                        [
                            'subject' => $data['subject'] ?? $original->subject,
                            'status_change' => true,
                            'old_status' => $changes['appointment_status']['old'],
                            'new_status' => $changes['appointment_status']['new']
                        ]
                    );
                }
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Delete appointment
     */
    public function delete_appointment($id)
    {
        $appointment = $this->get_appointment($id);
        
        if ($appointment) {
            // Log deletion before actually deleting (using unified method)
            $this->add_activity_log(
                $id, 
                'APPOINTMENT', 
                'deleted', 
                [
                    'subject' => $appointment->subject,
                    'date' => $appointment->date,
                    'time' => $appointment->start_hour,
                    'deleted_by' => get_staff_user_id()
                ]
            );
            
            // Delete attendees first
            $this->db->where('appointment_id', $id);
            $this->db->delete(db_prefix() . 'appointly_attendees');
            
            // Delete appointment
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'appointly_appointments');
            
            if ($this->db->affected_rows() > 0) {
                log_activity('Appointment Deleted [ID: ' . $id . ', Subject: ' . $appointment->subject . ']');
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get appointment statuses
     */
    public function get_statuses()
    {
        return [
            'scheduled' => _l('scheduled'),
            'cancelled' => _l('cancelled'),
            'complete' => _l('complete')
        ];
    }

    /**
     * Get appointment types
     */
    public function get_appointment_types()
    {
        $this->db->order_by('type', 'ASC');
        return $this->db->get(db_prefix() . 'appointly_appointment_types')->result_array();
    }

    /**
     * Get appointment attendees
     */
    public function get_appointment_attendees($appointment_id)
    {
        $this->db->select('s.staffid, CONCAT(s.firstname, " ", s.lastname) as name, s.email');
        $this->db->from(db_prefix() . 'appointly_attendees aa');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = aa.staff_id');
        $this->db->where('aa.appointment_id', $appointment_id);
        
        return $this->db->get()->result_array();
    }

    /**
     * Add attendee to appointment
     */
    public function add_attendee($appointment_id, $staff_id)
    {
        $data = [
            'appointment_id' => $appointment_id,
            'staff_id' => $staff_id
        ];
        
        $this->db->insert(db_prefix() . 'appointly_attendees', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Remove attendee from appointment
     */
    public function remove_attendee($appointment_id, $staff_id)
    {
        $this->db->where('appointment_id', $appointment_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete(db_prefix() . 'appointly_attendees');
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get upcoming appointments
     * Only shows appointments created from EllaContractors module
     */
    public function get_upcoming_appointments($limit = 10)
    {
        $where = [
            'date >=' => date('Y-m-d'),
            'cancelled' => 0
        ];
        
        $this->db->order_by('date', 'ASC');
        $this->db->order_by('start_hour', 'ASC');
        $this->db->limit($limit);
        
        return $this->get_appointments($where);
    }

    /**
     * Get appointments by status
     * Only shows appointments created from EllaContractors module
     */
    public function get_appointments_by_status($status, $limit = null)
    {
        $where = [];
        
        if (!empty($status)) {
            $where = ['appointment_status' => $status];
        }
        
        if ($limit) {
            $this->db->limit($limit);
        }
        
        return $this->get_appointments($where);
    }
    
    
    
    /**
     * Log scheduled event process
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $process       Process description
     * @param string $status        Process status
     * @return int|false            Log ID on success, false on failure
     */
    public function log_scheduled_process($appointment_id, $process, $status = 'completed')
    {
        return $this->log_appointment_process($appointment_id, $process, $status);
    }
    
    /**
     * Get comprehensive timeline for appointment
     * Uses unified get_timeline() method and enriches with icons/colors
     * 
     * @param int $appointment_id Appointment ID
     * @return array              Timeline data with icons and colors
     */
    public function get_appointment_timeline($appointment_id)
    {
        // Get all activities using unified method
        $activities = $this->get_timeline($appointment_id);
        
        $timeline = [];
        
        foreach ($activities as $activity) {
            // Determine icon and color based on log_type and action
            $icon = $this->get_icon_for_activity($activity['log_type'] ?? '', $activity['action'] ?? '');
            $color = $this->get_color_for_activity($activity['log_type'] ?? '', $activity['action'] ?? '');
            
            $timeline[] = [
                'type' => 'activity',
                'log_type' => $activity['log_type'] ?? 'APPOINTMENT',
                'action' => $activity['action'] ?? '',
                'date' => $activity['date'],
                'staff_id' => $activity['staff_id'],
                'full_name' => $activity['full_name'],
                'description' => $activity['description'],
                'description_key' => $activity['description_key'],
                'additional_data' => $activity['additional_data'], // Already decoded by get_timeline()
                'icon' => $icon,
                'color' => $color
            ];
        }
        
        // Sort timeline by date (newest first) - already sorted but ensure
        usort($timeline, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $timeline;
    }
    
    /**
     * Get icon based on log_type and action (NEW unified system)
     * 
     * @param string $log_type  Log type (APPOINTMENT, NOTES, ATTACHMENTS, etc.)
     * @param string $action    Action (created, updated, deleted, etc.)
     * @return string           FontAwesome icon class
     */
    private function get_icon_for_activity($log_type, $action)
    {
        // Icon mapping by log_type and action
        $icon_map = [
            'APPOINTMENT' => [
                'created' => 'fa fa-calendar-plus',
                'updated' => 'fa fa-edit',
                'deleted' => 'fa fa-trash'
            ],
            'NOTES' => [
                'created' => 'fa fa-sticky-note',
                'updated' => 'fa fa-edit',
                'deleted' => 'fa fa-trash'
            ],
            'ATTACHMENTS' => [
                'uploaded' => 'fa fa-upload',
                'deleted' => 'fa fa-trash',
                'created' => 'fa fa-paperclip'
            ],
            'MEASUREMENT' => [
                'created' => 'fa fa-ruler-combined',
                'updated' => 'fa fa-ruler',
                'deleted' => 'fa fa-minus-square'
            ],
            'ESTIMATES' => [
                'created' => 'fa fa-file-invoice',
                'updated' => 'fa fa-file-invoice-dollar',
                'deleted' => 'fa fa-trash'
            ],
            'PROPOSAL' => [
                'created' => 'fa fa-file-invoice',
                'updated' => 'fa fa-file-invoice-dollar',
                'deleted' => 'fa fa-trash'
            ],
            'SMS' => [
                'sent' => 'fa fa-comment'
            ],
            'EMAIL' => [
                'sent' => 'fa fa-envelope',
                'clicked' => 'fa fa-envelope-open'
            ]
        ];
        
        return $icon_map[$log_type][$action] ?? 'fa fa-info-circle';
    }
    
    /**
     * Get color based on log_type and action (NEW unified system)
     * 
     * @param string $log_type  Log type
     * @param string $action    Action
     * @return string           Bootstrap color class
     */
    private function get_color_for_activity($log_type, $action)
    {
        // Color mapping by action (mostly action-based)
        $action_colors = [
            'created' => 'success',
            'uploaded' => 'success',
            'added' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'removed' => 'danger',
            'sent' => 'primary',
            'clicked' => 'warning'
        ];
        
        // Override for specific log_types if needed
        if ($log_type === 'MEASUREMENT') {
            return $action_colors[$action] ?? 'primary';
        }
        
        return $action_colors[$action] ?? 'default';
    }
    
    /**
     * @deprecated Use get_icon_for_activity() instead
     * Legacy method for backward compatibility
     */
    private function get_activity_icon($description_key)
    {
        $icons = [
            'appointment_activity_created' => 'fa fa-calendar-plus',
            'appointment_activity_updated' => 'fa fa-edit',
            'appointment_activity_status_changed' => 'fa fa-exchange',
            'appointment_activity_measurement_added' => 'fa fa-plus-square',
            'appointment_activity_measurement_removed' => 'fa fa-minus-square',
            'appointment_activity_process' => 'fa fa-cogs',
            'appointment_activity_deleted' => 'fa fa-trash',
            'appointment_activity_attachment_uploaded' => 'fa fa-upload',
            'appointment_activity_attachment_deleted' => 'fa fa-trash',
            'estimate_created' => 'fa fa-file-invoice',
            'estimate_updated' => 'fa fa-file-invoice-dollar',
            'estimate_deleted' => 'fa fa-file-invoice-dollar',
            'measurement_created' => 'fa fa-ruler-combined',
            'measurement_updated' => 'fa fa-ruler',
            'measurement_deleted' => 'fa fa-ruler-horizontal',
            'sms_sent' => 'fa fa-comment',
            'email_sent' => 'fa fa-envelope',
            'email_clicked' => 'fa fa-envelope-open',
            'note_added' => 'fa fa-sticky-note',
            'note_updated' => 'fa fa-edit',
            'note_deleted' => 'fa fa-trash',
            'notes_created' => 'fa fa-sticky-note',
            'notes_updated' => 'fa fa-edit',
            'notes_deleted' => 'fa fa-trash',
            'attachments_uploaded' => 'fa fa-upload',
            'attachments_deleted' => 'fa fa-trash',
            'file_attached' => 'fa fa-paperclip',
            'file_deleted' => 'fa fa-times-circle'
        ];
        
        return $icons[$description_key] ?? 'fa fa-info-circle';
    }
    
    /**
     * @deprecated Use get_color_for_activity() instead
     * Legacy method for backward compatibility
     */
    private function get_activity_color($description_key)
    {
        $colors = [
            'appointment_activity_created' => 'success',
            'appointment_activity_updated' => 'info',
            'appointment_activity_status_changed' => 'warning',
            'appointment_activity_measurement_added' => 'success',
            'appointment_activity_measurement_removed' => 'danger',
            'appointment_activity_process' => 'secondary',
            'appointment_activity_deleted' => 'danger',
            'appointment_activity_attachment_uploaded' => 'success',
            'appointment_activity_attachment_deleted' => 'danger',
            'estimate_created' => 'success',
            'estimate_updated' => 'info',
            'estimate_deleted' => 'danger',
            'measurement_created' => 'primary',
            'measurement_updated' => 'info',
            'measurement_deleted' => 'danger',
            'sms_sent' => 'success',
            'email_sent' => 'primary',
            'email_clicked' => 'warning',
            'note_added' => 'secondary',
            'note_updated' => 'info',
            'note_deleted' => 'danger',
            'notes_created' => 'secondary',
            'notes_updated' => 'info',
            'notes_deleted' => 'danger',
            'attachments_uploaded' => 'success',
            'attachments_deleted' => 'danger',
            'file_attached' => 'success',
            'file_deleted' => 'danger'
        ];
        
        return $colors[$description_key] ?? 'default';
    }
}
