<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors_merge_fields extends App_merge_fields
{
    public function build()
    {
        $templates = [
            'ella-appointment-reminder-client',
            'ella-appointment-reminder-staff',
        ];

        return [
            [
                'name'      => 'Ella Appointment Subject',
                'key'       => '{ella_appointment_subject}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Appointment Date',
                'key'       => '{ella_appointment_date}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Appointment Time',
                'key'       => '{ella_appointment_time}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Appointment Address',
                'key'       => '{ella_appointment_address}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Appointment Status',
                'key'       => '{ella_appointment_status}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Appointment URL',
                'key'       => '{ella_appointment_url}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Client Name',
                'key'       => '{ella_client_name}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Staff Name',
                'key'       => '{ella_staff_name}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Reminder Stage',
                'key'       => '{ella_reminder_stage}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Recipient Name',
                'key'       => '{ella_recipient_name}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Presentation Block',
                'key'       => '{ella_presentation_block}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
            [
                'name'      => 'Ella Appointment Notes',
                'key'       => '{ella_appointment_notes}',
                'available' => ['ella_contractors'],
                'templates' => $templates,
            ],
        ];
    }

    public function format($appointment_id, $stage = '', $extra = [])
    {
        $fields = [];

        $this->ci->load->model('ella_contractors/Ella_appointments_model', 'ella_contractors_appointments_model');

        $appointment = $this->ci->ella_contractors_appointments_model->get_appointment($appointment_id);

        if (!$appointment) {
            return $fields;
        }

        $dateString = !empty($appointment->date) ? _d($appointment->date) : '';
        $timeString = '';
        if (!empty($appointment->start_hour)) {
            $timeString = date('g:i A', strtotime($appointment->start_hour));
        }

        $status = !empty($appointment->appointment_status) ? ucfirst($appointment->appointment_status) : 'Scheduled';

        $fields['{ella_appointment_subject}']  = $appointment->subject;
        $fields['{ella_appointment_date}']     = $dateString;
        $fields['{ella_appointment_time}']     = $timeString;
        $fields['{ella_appointment_address}']  = $appointment->address;
        $fields['{ella_appointment_status}']   = $status;
        $fields['{ella_appointment_notes}']    = $appointment->notes ?? '';
        $fields['{ella_staff_name}']           = get_staff_full_name($appointment->created_by);
        $fields['{ella_client_name}']          = $appointment->lead_name ?: ($appointment->client_name ?: '');
        $fields['{ella_appointment_url}']      = admin_url('ella_contractors/appointments/view/' . $appointment->id);
        $fields['{ella_reminder_stage}']       = function_exists('ella_get_reminder_stage_label')
            ? ella_get_reminder_stage_label($stage)
            : $this->format_stage_label($stage);
        $fields['{ella_recipient_name}']       = $extra['recipient_name'] ?? ($fields['{ella_client_name}'] ?: $fields['{ella_staff_name}']);
        $fields['{ella_presentation_block}']   = $extra['presentation_block'] ?? '';

        return $fields;
    }

    private function format_stage_label($stage)
    {
        switch (strtolower($stage)) {
            case 'client_instant':
                return 'Instant Confirmation';
            case 'client_48h':
            case 'client_48_hours':
                return '48 Hour Reminder';
            case 'staff_48h':
            case 'staff_48_hours':
                return 'Staff 48 Hour Reminder';
            default:
                return ucfirst($stage);
        }
    }
}

