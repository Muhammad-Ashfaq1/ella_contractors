<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_media_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_media()
    {
        $this->db->order_by('date_uploaded', 'DESC');
        return $this->db->get(db_prefix() . 'ella_contractor_media')->result_array();
    }

    public function add_media($data)
    {
        $this->db->insert(db_prefix() . 'ella_contractor_media', $data);
        return $this->db->insert_id();
    }

    /**
     * Get media by relation type and ID
     * @param string $rel_type
     * @param int $rel_id
     * @return array
     */
    public function get_media_by_relation($rel_type, $rel_id)
    {
        $this->db->where('rel_type', $rel_type);
        $this->db->where('rel_id', $rel_id);
        $this->db->where('active', 1);
        $this->db->order_by('date_uploaded', 'DESC');
        return $this->db->get(db_prefix() . 'ella_contractor_media')->result_array();
    }

    public function get_file($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ella_contractor_media')->row();
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
