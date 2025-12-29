<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reminder_template_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get template by ID
     */
    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ella_reminder_templates')->row();
    }

    /**
     * Get template by stage and type
     */
    public function get_by_stage($reminder_stage, $template_type, $recipient_type = null)
    {
        $this->db->where('reminder_stage', $reminder_stage);
        $this->db->where('template_type', $template_type);
        $this->db->where('is_active', 1);
        
        if ($recipient_type) {
            $this->db->where('recipient_type', $recipient_type);
        }
        
        $this->db->order_by('id', 'ASC');
        return $this->db->get(db_prefix() . 'ella_reminder_templates')->row();
    }

    /**
     * Get all templates
     */
    public function get_all($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->order_by('template_type', 'ASC');
        $this->db->order_by('reminder_stage', 'ASC');
        return $this->db->get(db_prefix() . 'ella_reminder_templates')->result();
    }

    /**
     * Create new template
     */
    public function create($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert(db_prefix() . 'ella_reminder_templates', $data);
        return $this->db->insert_id();
    }

    /**
     * Update template
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_reminder_templates', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete template
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ella_reminder_templates');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get available merge fields for templates
     */
    public function get_merge_fields()
    {
        return [
            'email' => [
                '{appointment_subject}',
                '{appointment_date}',
                '{appointment_time}',
                '{appointment_location}',
                '{client_name}',
                '{staff_name}',
                '{company_name}',
                '{company_phone}',
                '{company_email}',
                '{appointment_notes}',
                '{presentation_block}',
                '{crm_link}'
            ],
            'sms' => [
                '{appointment_subject}',
                '{appointment_date}',
                '{appointment_time}',
                '{appointment_location}',
                '{client_name}',
                '{staff_name}',
                '{company_name}',
                '{company_phone}'
            ]
        ];
    }
}

