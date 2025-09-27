<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_media_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_folders($lead_id = null)
    {
        if ($lead_id) {
            $this->db->where('lead_id', $lead_id);
        }
        return $this->db->get(db_prefix() . 'ella_media_folders')->result_array();
    }

    public function create_folder($data)
    {
        $this->db->insert(db_prefix() . 'ella_media_folders', $data);
        return $this->db->insert_id();
    }

    public function get_media($folder_id = null, $lead_id = null)
    {
        if ($folder_id) {
            $this->db->where('folder_id', $folder_id);
        }
        if ($lead_id) {
            $this->db->where('lead_id', $lead_id);
        }
        $this->db->or_where('is_default', 1);
        return $this->db->get(db_prefix() . 'ella_contractor_media')->result_array();
    }

    public function add_media($data)
    {
        $this->db->insert(db_prefix() . 'ella_contractor_media', $data);
        return $this->db->insert_id();
    }

    public function get_file($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ella_contractor_media')->row();
    }

    public function get_folder_name($folder_id)
    {
        $this->db->select('name');
        $this->db->where('id', $folder_id);
        $result = $this->db->get(db_prefix() . 'ella_media_folders')->row();
        return $result ? $result->name : 'Unknown';
    }

    /**
     * Get appointment attachments
     * @param int $appointment_id
     * @return array
     */
    public function get_appointment_attachments($appointment_id)
    {
        $this->db->where('rel_type', 'appointment');
        $this->db->where('rel_id', $appointment_id);
        $this->db->where('active', 1);
        $this->db->order_by('date_uploaded', 'DESC');
        return $this->db->get(db_prefix() . 'ella_contractor_media')->result_array();
    }

    /**
     * Delete appointment attachment
     * @param int $attachment_id
     * @return bool
     */
    public function delete_appointment_attachment($attachment_id)
    {
        $this->db->where('id', $attachment_id);
        $this->db->where('rel_type', 'appointment');
        return $this->db->delete(db_prefix() . 'ella_contractor_media');
    }
}
