<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_appointments_model extends App_Model
{
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
            log_activity('New Appointment Created [ID: ' . $appointment_id . ', Subject: ' . $data['subject'] . ']');
            return $appointment_id;
        }
        
        return false;
    }

    /**
     * Update appointment
     */
    public function update_appointment($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'appointly_appointments', $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Appointment Updated [ID: ' . $id . ', Subject: ' . $data['subject'] . ']');
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
}
