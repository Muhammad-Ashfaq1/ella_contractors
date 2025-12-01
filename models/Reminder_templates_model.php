<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reminder_templates_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all reminder templates
     * @param array $where Optional where conditions
     * @return array
     */
    public function get_templates($where = [])
    {
        $this->db->select('t.*, CONCAT(s.firstname, " ", s.lastname) as created_by_name');
        $this->db->from(db_prefix() . 'ella_appointment_reminder_templates t');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = t.created_by', 'left');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by('t.template_type', 'ASC');
        $this->db->order_by('t.reminder_stage', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get single template by ID
     * @param int $id
     * @return object|null
     */
    public function get_template($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ella_appointment_reminder_templates')->row();
    }

    /**
     * Get template by type and stage
     * @param string $type email|sms
     * @param string $stage client_instant|client_48h|staff_48h
     * @return object|null
     */
    public function get_template_by_type_stage($type, $stage)
    {
        $this->db->where('template_type', $type);
        $this->db->where('reminder_stage', $stage);
        $this->db->where('is_active', 1);
        return $this->db->get(db_prefix() . 'ella_appointment_reminder_templates')->row();
    }

    /**
     * Create new template
     * @param array $data
     * @return int|false Template ID on success
     */
    public function create_template($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert(db_prefix() . 'ella_appointment_reminder_templates', $data);
        
        if ($this->db->affected_rows() > 0) {
            $template_id = $this->db->insert_id();
            log_activity('Reminder Template Created [ID: ' . $template_id . ', Name: ' . $data['template_name'] . ']');
            return $template_id;
        }
        
        return false;
    }

    /**
     * Update template
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update_template($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_appointment_reminder_templates', $data);
        
        if ($this->db->affected_rows() >= 0) {
            log_activity('Reminder Template Updated [ID: ' . $id . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Delete template
     * @param int $id
     * @return bool
     */
    public function delete_template($id)
    {
        $template = $this->get_template($id);
        
        if ($template) {
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'ella_appointment_reminder_templates');
            
            if ($this->db->affected_rows() > 0) {
                log_activity('Reminder Template Deleted [ID: ' . $id . ', Name: ' . $template->template_name . ']');
                return true;
            }
        }
        
        return false;
    }

    /**
     * Toggle template active status
     * @param int $id
     * @return bool
     */
    public function toggle_active($id)
    {
        $template = $this->get_template($id);
        
        if ($template) {
            $new_status = $template->is_active ? 0 : 1;
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'ella_appointment_reminder_templates', ['is_active' => $new_status]);
            
            if ($this->db->affected_rows() > 0) {
                log_activity('Reminder Template Status Changed [ID: ' . $id . ', Status: ' . ($new_status ? 'Active' : 'Inactive') . ']');
                return true;
            }
        }
        
        return false;
    }
}



