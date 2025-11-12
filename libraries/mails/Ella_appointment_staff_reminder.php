<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_appointment_staff_reminder extends App_mail_template
{
    protected $for = 'staff';
    public $slug   = 'ella-appointment-staff-reminder';

    protected $appointment;
    protected $merge_fields;
    protected $ics_path;

    public function __construct($appointment, $merge_fields = [], $ics_path = null)
    {
        parent::__construct();
        $this->appointment   = $appointment;
        $this->merge_fields  = $merge_fields;
        $this->ics_path      = $ics_path;
    }

    public function build()
    {
        if (empty($this->appointment->email)) {
            return false;
        }

        $this->to($this->appointment->email)
            ->set_merge_fields($this->merge_fields)
            ->set_rel_id($this->appointment->id)
            ->set_rel_type('appointment')
            ->set_staff_id($this->appointment->created_by ?? null);

        if ($this->ics_path && file_exists($this->ics_path)) {
            $this->add_attachment([
                'attachment' => $this->ics_path,
                'filename'   => basename($this->ics_path),
                'type'       => 'text/calendar',
            ]);
        }
    }
}

