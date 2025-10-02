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
        
        $this->db->insert(db_prefix() . 'appointly_appointments', $data);
        $appointment_id = $this->db->insert_id();
        
        if ($appointment_id) {
            // Log general activity
            log_activity('New Appointment Created [ID: ' . $appointment_id . ', Subject: ' . $data['subject'] . ']');
            
            // Log detailed appointment activity using trait
            $this->log_appointment_created(
                $appointment_id, 
                $data['subject'], 
                $data['date'] ?? '', 
                $data['start_hour'] ?? ''
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
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appointly_appointments', $data);
        
        if ($this->db->affected_rows() > 0) {
            // Log general activity
            log_activity('Appointment Updated [ID: ' . $id . ', Subject: ' . $data['subject'] . ']');
            
            // Log detailed appointment activity using trait
            $this->log_appointment_updated($id, $data['subject'], $changes);
            
            // Check if status changed specifically
            if (isset($changes['appointment_status'])) {
                $this->log_appointment_status_changed(
                    $id, 
                    $changes['appointment_status']['old'], 
                    $changes['appointment_status']['new']
                );
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
            // Log deletion before actually deleting
            $this->log_appointment_deleted($id, $appointment->subject);
            
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
     * Add note to appointment and log activity
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $note_content  Note content
     * @return int|false            Note ID on success, false on failure
     */
    public function add_appointment_note($appointment_id, $note_content)
    {
        $CI = &get_instance();
        
        // Add note to the appointment's description/notes field
        $appointment = $this->get_appointment($appointment_id);
        if ($appointment) {
            $current_notes = $appointment->description ?? '';
            $new_notes = $current_notes . "\n\n" . date('Y-m-d H:i:s') . " - " . $note_content;
            
            $this->update_appointment($appointment_id, ['description' => $new_notes]);
            
            // Log the note addition
            $this->log_appointment_note_added($appointment_id, $note_content);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Log measurement addition
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $measurement   Measurement details
     * @return int|false            Log ID on success, false on failure
     */
    public function log_measurement_added($appointment_id, $measurement)
    {
        return $this->log_appointment_measurement_added($appointment_id, $measurement);
    }
    
    /**
     * Log measurement removal
     * 
     * @param int    $appointment_id Appointment ID
     * @param string $measurement   Measurement details
     * @return int|false            Log ID on success, false on failure
     */
    public function log_measurement_removed($appointment_id, $measurement)
    {
        return $this->log_appointment_measurement_removed($appointment_id, $measurement);
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
     * Combines appointment activities, notes, and other related activities
     * 
     * @param int $appointment_id Appointment ID
     * @return array              Timeline data
     */
    public function get_appointment_timeline($appointment_id)
    {
        $timeline = [];
        
        // Get appointment activities
        $activities = $this->get_appointment_activity_log($appointment_id);
        
        foreach ($activities as $activity) {
            $timeline[] = [
                'type' => 'activity',
                'date' => $activity['date'],
                'staff_id' => $activity['staff_id'],
                'full_name' => $activity['full_name'],
                'description' => $activity['description'],
                'description_key' => $activity['description_key'],
                'additional_data' => $activity['additional_data'],
                'icon' => $this->get_activity_icon($activity['description_key']),
                'color' => $this->get_activity_color($activity['description_key'])
            ];
        }
        
        // Get appointment creation date
        $appointment = $this->get_appointment($appointment_id);
        if ($appointment) {
            $timeline[] = [
                'type' => 'created',
                'date' => $appointment->dateadded ?? $appointment->date,
                'staff_id' => $appointment->created_by,
                'full_name' => get_staff_full_name($appointment->created_by),
                'description' => 'Appointment created',
                'description_key' => 'appointment_created',
                'additional_data' => serialize(['subject' => $appointment->subject]),
                'icon' => 'fa fa-calendar-plus',
                'color' => 'success'
            ];
        }
        
        // Sort timeline by date (newest first)
        usort($timeline, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $timeline;
    }
    
    /**
     * Get activity icon based on description key
     * 
     * @param string $description_key Activity description key
     * @return string                FontAwesome icon class
     */
    private function get_activity_icon($description_key)
    {
        $icons = [
            'appointment_activity_created' => 'fa fa-calendar-plus',
            'appointment_activity_updated' => 'fa fa-edit',
            'appointment_activity_status_changed' => 'fa fa-exchange',
            'appointment_activity_note_added' => 'fa fa-sticky-note',
            'appointment_activity_measurement_added' => 'fa fa-plus-square',
            'appointment_activity_measurement_removed' => 'fa fa-minus-square',
            'appointment_activity_process' => 'fa fa-cogs',
            'appointment_activity_deleted' => 'fa fa-trash',
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
            'file_attached' => 'fa fa-paperclip',
            'file_deleted' => 'fa fa-times-circle'
        ];
        
        return $icons[$description_key] ?? 'fa fa-info-circle';
    }
    
    /**
     * Get activity color based on description key
     * 
     * @param string $description_key Activity description key
     * @return string                Bootstrap color class
     */
    private function get_activity_color($description_key)
    {
        $colors = [
            'appointment_activity_created' => 'success',
            'appointment_activity_updated' => 'info',
            'appointment_activity_status_changed' => 'warning',
            'appointment_activity_note_added' => 'primary',
            'appointment_activity_measurement_added' => 'success',
            'appointment_activity_measurement_removed' => 'danger',
            'appointment_activity_process' => 'secondary',
            'appointment_activity_deleted' => 'danger',
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
            'file_attached' => 'success',
            'file_deleted' => 'danger'
        ];
        
        return $colors[$description_key] ?? 'default';
    }
}
