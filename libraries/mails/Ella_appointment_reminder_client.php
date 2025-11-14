<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_appointment_reminder_client extends App_mail_template
{
    protected $for = 'customer';

    protected $skipQueue = true;

    protected $appointment;

    protected $stage;

    protected $recipientEmail;

    protected $recipientName;

    protected $presentationBlock;

    public $slug = 'ella-appointment-reminder-client';

    public function __construct($appointment, $stage, $email, $name = '', $presentationBlock = '')
    {
        parent::__construct();

        $this->appointment       = $appointment;
        $this->stage             = $stage;
        $this->recipientEmail    = $email;
        $this->recipientName     = $name;
        $this->presentationBlock = $presentationBlock;

        $this->set_merge_fields('ella_contractors_merge_fields', $this->appointment->id, $stage, [
            'recipient_name'     => $this->recipientName,
            'presentation_block' => $this->presentationBlock,
        ]);
    }

    public function build()
    {
        $this->to($this->recipientEmail);
    }
}

