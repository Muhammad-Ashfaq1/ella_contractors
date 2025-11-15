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
     * Ensure a reminder record exists for the given appointment.
     *
     * @param int $appointment_id
     * @return object|null
     */
    public function ensure_exists($appointment_id)
    {
        $existing = $this->get_by_appointment($appointment_id);

        if ($existing) {
            return $existing;
        }

        $data = [
            'appointment_id'           => (int) $appointment_id,
            'client_instant_remind'    => 0,
            'client_48_hours'          => 0,
            'staff_48_hours'           => 0,
            'client_sms_reminder'      => 0,
            'sms_send'                 => 0,
            'email_send'               => 0,
            'client_instant_sent'      => 0,
            'client_48_hours_sent'     => 0,
            'staff_48_hours_sent'      => 0,
            'client_sms_48_hours_sent' => 0,
            'staff_sms_48_hours_sent'  => 0,
            'rel_type'                 => 'appointment',
            'rel_id'                   => (int) $appointment_id,
            'org_id'                   => null,
        ];

        $insertId = $this->create($data);

        return $insertId ? $this->get($insertId) : null;
    }

    /**
     * Sync reminder tracking flags based on the appointment form payload.
     *
     * @param int   $appointment_id
     * @param array $appointment_data
     * @return bool|int
     */
    public function sync_from_appointment($appointment_id, array $appointment_data)
    {
        $appointment_id = (int) $appointment_id;

        $flags = [
            'client_instant_remind' => !empty($appointment_data['send_reminder']) ? 1 : 0,
            'client_48_hours'       => !empty($appointment_data['reminder_48h']) ? 1 : 0,
            'staff_48_hours'        => !empty($appointment_data['staff_reminder_48h']) ? 1 : 0,
            'client_sms_reminder'   => isset($appointment_data['reminder_channel']) && in_array($appointment_data['reminder_channel'], ['sms', 'both'], true) ? 1 : 0,
            'rel_type'              => 'appointment',
            'rel_id'                => $appointment_id,
            'org_id'                => null,
        ];

        $existing = $this->get_by_appointment($appointment_id);

        if ($existing) {
            return $this->update($existing->id, $flags);
        }

        $flags['appointment_id']           = $appointment_id;
        $flags['sms_send']                 = 0;
        $flags['email_send']               = 0;
        $flags['client_instant_sent']      = 0;
        $flags['client_48_hours_sent']     = 0;
        $flags['staff_48_hours_sent']      = 0;
        $flags['client_sms_48_hours_sent'] = 0;
        $flags['staff_sms_48_hours_sent']  = 0;

        return $this->create($flags);
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

    /**
     * Mark an email reminder stage as sent.
     *
     * @param int    $appointment_id
     * @param string $stage          client_instant | client_48h | staff_48h
     * @return bool
     */
    public function mark_email_stage_sent($appointment_id, $stage)
    {
        $stage = strtolower($stage);

        $data = [
            'email_send'         => 1,
            'last_email_sent_at' => date('Y-m-d H:i:s'),
        ];

        switch ($stage) {
            case 'client_instant':
                $data['client_instant_sent'] = 1;
                break;
            case 'client_48h':
            case 'client_48_hours':
                $data['client_48_hours_sent'] = 1;
                break;
            case 'staff_48h':
            case 'staff_48_hours':
                $data['staff_48_hours_sent'] = 1;
                break;
            default:
                // leave only generic flags
                break;
        }

        return $this->update_by_appointment($appointment_id, $data);
    }

    /**
     * Mark an SMS reminder stage as sent.
     *
     * @param int    $appointment_id
     * @param string $stage          client_48h | staff_48h
     * @return bool
     */
    public function mark_sms_stage_sent($appointment_id, $stage)
    {
        $stage = strtolower($stage);

        $data = [
            'sms_send'        => 1,
            'last_sms_sent_at'=> date('Y-m-d H:i:s'),
        ];

        switch ($stage) {
            case 'client_instant':
            case 'client_instant_remind':
                // Base fields already mark sms_send/last_sms_sent_at
                break;
            case 'client_48h':
            case 'client_48_hours':
                $data['client_sms_48_hours_sent'] = 1;
                break;
            case 'staff_48h':
            case 'staff_48_hours':
                $data['staff_sms_48_hours_sent'] = 1;
                break;
            default:
                break;
        }

        return $this->update_by_appointment($appointment_id, $data);
    }
}

