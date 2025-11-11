<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Appointment_reminder_model extends App_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'appointment_reminder';
    }

    /**
     * Fetch a reminder by primary key.
     */
    public function get($id)
    {
        return $this->db->get_where($this->table, ['id' => (int) $id])->row();
    }

    /**
     * Fetch reminder row for a specific appointment.
     */
    public function get_by_appointment($appointment_id)
    {
        return $this->db
            ->where('appointment_id', (int) $appointment_id)
            ->get($this->table)
            ->row();
    }

    /**
     * Create a reminder record.
     */
    public function create(array $data)
    {
        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Update a reminder record by ID.
     */
    public function update($id, array $data)
    {
        $this->db->where('id', (int) $id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Update reminder row by appointment ID.
     */
    public function update_by_appointment($appointment_id, array $data)
    {
        $this->db->where('appointment_id', (int) $appointment_id);
        $this->db->update($this->table, $data);

        return $this->db->affected_rows() >= 0;
    }
}

