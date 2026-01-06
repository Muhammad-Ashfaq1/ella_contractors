<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ella Contractors Reminder Helper
 * Laravel-style service helper for appointment reminders and ICS calendar generation
 * 
 * @package EllaContractors
 * @author Custom
 * @version 1.0.0
 */

/**
 * Generate ICS calendar file for appointment
 * 
 * @param int $appointment_id Appointment ID
 * @param string $type Type: 'client' or 'staff'
 * @return string|false Path to generated ICS file, or false on failure
 */
function ella_generate_ics($appointment_id, $type = 'client')
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/ella_appointments_model');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot generate ICS - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    // Build datetime strings
    $start_datetime = $appointment->date . ' ' . $appointment->start_hour;
    
    // Handle end datetime - use end_date and end_time if available, otherwise calculate
    if (!empty($appointment->end_date) && !empty($appointment->end_time)) {
        $end_datetime = $appointment->end_date . ' ' . $appointment->end_time;
    } else {
        // Default: add 1 hour to start time
        $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . ' +1 hour'));
    }
    
    // Event details based on type
    if ($type === 'staff') {
        $client_or_lead_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Unknown Client');
        $summary = 'Appointment: ' . $appointment->subject . ' with ' . $client_or_lead_name;
        
        $description = $appointment->subject . "\n";
        $description .= "Client/Lead: " . $client_or_lead_name . "\n";
        if (!empty($appointment->notes)) {
            $description .= "\nNotes: " . strip_tags($appointment->notes) . "\n";
        }
        $description .= "\nView in CRM: " . admin_url('ella_contractors/appointments/view/' . $appointment_id);
    } else {
        // Client ICS
        $summary = 'Your Appointment: ' . $appointment->subject;
        $company_name = get_option('companyname') ?: 'Our Company';
        
        $description = $appointment->subject . "\n";
        $description .= "Company: " . $company_name . "\n";
        if (!empty($appointment->notes)) {
            $description .= "\nDetails: " . strip_tags($appointment->notes) . "\n";
        }
        $description .= "\nContact: " . (get_option('company_phone_number') ?: get_option('company_email'));
    }
    
    // Location
    $location = !empty($appointment->address) ? $appointment->address : 'Online/Phone Call';
    
    // Generate ICS content
    $ics_content = ella_build_ics_content($start_datetime, $end_datetime, $summary, $description, $location);
    
    // Save to file
    $upload_dir = FCPATH . 'uploads/ella_appointments/ics/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $filename = $type . '_appointment_' . $appointment_id . '_' . time() . '.ics';
    $file_path = $upload_dir . $filename;
    
    if (file_put_contents($file_path, $ics_content) !== false) {
        log_message('info', 'EllaContractors: ICS file generated successfully - ' . $filename);
        return $file_path;
    }
    
    log_message('error', 'EllaContractors: Failed to write ICS file - ' . $file_path);
    return false;
}

/**
 * Build ICS file content following RFC 5545 standard
 * 
 * @param string $start_datetime Start datetime (Y-m-d H:i:s)
 * @param string $end_datetime End datetime (Y-m-d H:i:s)
 * @param string $summary Event title
 * @param string $description Event description
 * @param string $location Event location
 * @return string ICS content
 */
function ella_build_ics_content($start_datetime, $end_datetime, $summary, $description, $location)
{
    // Use America/Chicago timezone (matching existing CRM timezone)
    $timezone = new DateTimeZone('America/Chicago');
    
    try {
        $startDT = new DateTime($start_datetime, $timezone);
        $endDT = new DateTime($end_datetime, $timezone);
        
        // Convert to UTC for ICS standard
        $startDT->setTimezone(new DateTimeZone('UTC'));
        $endDT->setTimezone(new DateTimeZone('UTC'));
        
        $formattedStart = $startDT->format('Ymd\THis\Z');
        $formattedEnd = $endDT->format('Ymd\THis\Z');
    } catch (Exception $e) {
        log_message('error', 'EllaContractors: DateTime parsing error in ICS generation - ' . $e->getMessage());
        // Fallback to current time
        $formattedStart = gmdate('Ymd\THis\Z');
        $formattedEnd = gmdate('Ymd\THis\Z', strtotime('+1 hour'));
    }
    
    // Get company info for organizer
    $company_name = get_option('companyname') ?: 'EllaContractors';
    $company_email = get_option('company_email') ?: 'noreply@ellacontractors.com';
    
    // Generate unique UID
    $domain = str_replace(['http://', 'https://', 'www.'], '', site_url());
    $uid = uniqid() . '@' . $domain;
    
    // Build ICS content with proper line breaks (CRLF)
    $ics  = "BEGIN:VCALENDAR\r\n";
    $ics .= "VERSION:2.0\r\n";
    $ics .= "PRODID:-//EllaContractors//CRM//EN\r\n";
    $ics .= "METHOD:REQUEST\r\n";
    $ics .= "CALSCALE:GREGORIAN\r\n";
    $ics .= "BEGIN:VEVENT\r\n";
    $ics .= "UID:" . $uid . "\r\n";
    $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
    $ics .= "ORGANIZER;CN=\"" . ella_escape_ics_text($company_name) . "\":mailto:" . $company_email . "\r\n";
    $ics .= "DTSTART:" . $formattedStart . "\r\n";
    $ics .= "DTEND:" . $formattedEnd . "\r\n";
    $ics .= "SUMMARY:" . ella_escape_ics_text($summary) . "\r\n";
    $ics .= "DESCRIPTION:" . ella_escape_ics_text($description) . "\r\n";
    $ics .= "LOCATION:" . ella_escape_ics_text($location) . "\r\n";
    $ics .= "STATUS:CONFIRMED\r\n";
    $ics .= "SEQUENCE:0\r\n";
    $ics .= "PRIORITY:5\r\n";
    $ics .= "CLASS:PUBLIC\r\n";
    $ics .= "BEGIN:VALARM\r\n";
    $ics .= "TRIGGER:-PT15M\r\n";
    $ics .= "ACTION:DISPLAY\r\n";
    $ics .= "DESCRIPTION:Appointment Reminder\r\n";
    $ics .= "END:VALARM\r\n";
    $ics .= "END:VEVENT\r\n";
    $ics .= "END:VCALENDAR\r\n";
    
    return $ics;
}

/**
 * Escape text for ICS format (handle special chars, newlines)
 * Following RFC 5545 specification
 * 
 * @param string $text Text to escape
 * @return string Escaped text
 */
function ella_escape_ics_text($text)
{
    // Remove HTML tags
    $text = strip_tags($text);
    
    // Replace newlines with literal \n
    $text = str_replace(["\r\n", "\n", "\r"], '\n', $text);
    
    // Escape special characters
    $text = str_replace(',', '\,', $text);
    $text = str_replace(';', '\;', $text);
    $text = str_replace('\\', '\\\\', $text);
    
    // Limit length for compatibility (ICS lines should be < 75 chars, but we'll allow more and let the mailer handle folding)
    return $text;
}

/**
 * Send appointment reminder email with ICS attachment
 * Uses existing CRM email library (same as send_reminder_ajax in controller)
 * 
 * @param int $appointment_id Appointment ID
 * @param string $type 'client' or 'staff'
 * @return bool Success status
 */
function ella_send_reminder_email($appointment_id, $stage)
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/ella_appointments_model');
    $CI->load->model('leads_model');
    $CI->load->model('clients_model');
    $CI->load->library('email');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot send email - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    $stage = strtolower($stage);
    $type  = ($stage === 'staff_48h' || $stage === 'staff_same_day') ? 'staff' : 'client';
    $context = $stage;
    
    // Generate ICS file
    $ics_file = ella_generate_ics($appointment_id, $type);

    // Prepare presentation links block (especially for same-day staff reminders)
    $presentations_for_email = ella_get_presentation_links_for_email($appointment_id);
    $appointment->presentation_block = ella_build_presentation_block_html($presentations_for_email);
    
    // Load template from database
    $CI->load->model('ella_contractors/Reminder_template_model');
    $template_record = $CI->Reminder_template_model->get_by_stage($stage, 'email', $type);
    
    if (!$template_record || !$template_record->is_active) {
        // Fallback to default template if database template not found
        $template_record = (object)[
            'subject' => $stage === 'client_instant' ? 'Appointment Confirmation: {appointment_subject}' : 'Appointment Reminder: {appointment_subject}',
            'content' => $type === 'staff' ? ella_get_staff_reminder_template() : ella_get_client_reminder_template()
        ];
    }
    
    if ($type === 'staff') {
        // Send reminder to the staff member who created the appointment
        $staff = $CI->db->select('firstname, lastname, email')
                        ->where('staffid', $appointment->created_by)
                        ->get(db_prefix() . 'staff')
                        ->row();
        if (!$staff || empty($staff->email)) {
            log_message('error', 'EllaContractors: Cannot send staff email - Staff not found or missing email for appointment ' . $appointment_id);
            return false;
        }
        
        $to_email = $staff->email;
        $to_name = trim($staff->firstname . ' ' . $staff->lastname);
    } else {
        // Default to the email stored on the appointment
        $to_email = $appointment->email;
        $to_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Valued Customer');
        
        // Fallback: try to load email from the related lead/client record
        if (empty($to_email) && !empty($appointment->contact_id)) {
            if (!empty($appointment->lead_name)) {
                $lead = $CI->leads_model->get($appointment->contact_id);
                if ($lead && !empty($lead->email)) {
                    $to_email = $lead->email;
                    $to_name = $lead->name;
                }
            } else {
                $client = $CI->clients_model->get($appointment->contact_id);
                if ($client && !empty($client->email)) {
                    $to_email = $client->email;
                    $to_name = $client->company ?: trim(($client->firstname ?? '') . ' ' . ($client->lastname ?? ''));
                }
            }
        }
        
        if (empty($to_email)) {
            log_message('error', 'EllaContractors: Cannot send client email - No recipient email address for appointment ' . $appointment_id);
            return false;
        }
    }

    // Parse template with appointment data
    $email_body = ella_parse_email_template_from_db($template_record->content, $appointment, $type);
    $subject = ella_parse_email_template_from_db($template_record->subject ?? '', $appointment, $type);

    // Ensure presentation links are visible even if template lacks placeholder
    if (!empty($appointment->presentation_block)
        && strpos($email_body, $appointment->presentation_block) === false) {
        $email_body .= $appointment->presentation_block;
    }
    
    // Get from email with fallback (use smtp_email like the rest of CRM)
    $from_email = get_option('smtp_email');
    if (empty($from_email)) {
        $from_email = get_option('company_email');
    }
    
    $from_name = get_option('companyname');
    
    // Fallback if email not set or invalid
    if (empty($from_email) || !filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
        $from_email = 'noreply@ellasbubbles.com';
        log_message('warning', 'EllaContractors: SMTP email not configured, using fallback email: ' . $from_email);
    }
    
    if (empty($from_name)) {
        $from_name = 'EllaContractors CRM';
    }

    log_message('info', 'EllaContractors: Sending email FROM: ' . $from_email . ' (' . $from_name . ') TO: ' . $to_email);
    
    // Initialize email library with CRM's SMTP configuration
    $CI->load->config('email');
    $CI->email->clear(true);
    $CI->email->initialize();
    
    // Set newline and crlf settings (required for proper email formatting)
    $CI->email->set_newline(config_item('newline'));
    $CI->email->set_crlf(config_item('crlf'));
    
    // Send email using CRM's email system (same pattern as send_reminder_ajax)
    $CI->email->from($from_email, $from_name);
    $CI->email->to($to_email);
    $CI->email->subject($subject);
    $CI->email->message($email_body);
    
    // Only enable debug in development/testing (comment out in production)
    // $CI->email->SMTPDebug = 2;
    // $CI->email->set_debug_output('error_log');
    
    // Attach ICS file if generated successfully
    if ($ics_file && file_exists($ics_file)) {
        $CI->email->attach($ics_file);
    }
    
    // Send email (App_Email library handles queuing automatically if enabled in settings)
    $result = $CI->email->send();
    
    if ($result) {
        log_message('info', 'EllaContractors: Email sent successfully to ' . $to_email . ' for appointment ' . $appointment_id);
        
        // Log email activity to appointment timeline
        $CI->ella_appointments_model->add_activity_log(
            $appointment_id,
            'EMAIL',
            'sent',
            [
                'email_address' => $to_email,
                'subject' => $subject,
                'email_type' => $context,
                'reminder_stage' => $context,
                'has_ics_attachment' => $ics_file ? true : false
            ]
        );
    } else {
        log_message('error', 'EllaContractors: Failed to send email to ' . $to_email . ' - ' . $CI->email->print_debugger());
    }
    
    return $result;
}


/**
 * Determine if an email reminder was already sent for the given stage.
 *
 * @param int    $appointment_id
 * @param string $stage
 * @return bool
 */
function ella_email_already_sent($appointment_id, $stage)
{
    $CI =& get_instance();
    $stage = strtolower($stage);

    // Prefer reminder tracking table
    $CI->load->model('ella_contractors/Appointment_reminder_model');
    $reminder = $CI->appointment_reminder_model->get_by_appointment($appointment_id);

    if ($reminder) {
        switch ($stage) {
            case 'client_instant':
                return (int) $reminder->client_instant_sent === 1;
            case 'client_48h':
            case 'client_48_hours':
                return (int) $reminder->client_48_hours_sent === 1;
            case 'staff_48h':
            case 'staff_48_hours':
                return (int) $reminder->staff_48_hours_sent === 1;
        }
    }

    // Fallback to legacy timeline logs for backwards compatibility
    $CI->db->where('rel_type', 'appointment');
    $CI->db->where('rel_id', $appointment_id);
    $CI->db->where('log_type', 'EMAIL');
    $CI->db->like('additional_data', '"reminder_stage":"' . $stage . '"', 'both');
    $exists = $CI->db->count_all_results(db_prefix() . 'ella_appointment_activity_log') > 0;

    if (!$exists) {
        $legacyKey = $stage === 'client_instant' ? 'client_reminder' : ($stage === 'client_48h' ? 'client_reminder' : ($stage === 'staff_48h' ? 'staff_reminder' : $stage));
        $CI->db->where('rel_type', 'appointment');
        $CI->db->where('rel_id', $appointment_id);
        $CI->db->where('log_type', 'EMAIL');
        $CI->db->like('additional_data', '"email_type":"' . $legacyKey . '"', 'both');
        $exists = $CI->db->count_all_results(db_prefix() . 'ella_appointment_activity_log') > 0;
    }

    return $exists;
}

/**
 * Calculate appointment start timestamp.
 *
 * @param object $appointment
 * @return int|null
 */
function ella_get_appointment_start_timestamp($appointment)
{
    if (empty($appointment->date)) {
        return null;
    }
    $time = $appointment->start_hour ?? '00:00:00';
    if (strlen($time) === 5) {
        $time .= ':00';
    }
    $datetime = $appointment->date . ' ' . $time;
    $timestamp = strtotime($datetime);
    return $timestamp ?: null;
}

/**
 * Calculate hours until appointment start.
 *
 * @param object $appointment
 * @return float|null
 */
function ella_hours_until_appointment($appointment)
{
    $timestamp = ella_get_appointment_start_timestamp($appointment);
    if ($timestamp === null) {
        return null;
    }
    return ($timestamp - time()) / 3600;
}

/**
 * Cron processor for 48-hour reminders.
 *
 * @return void
 */
function ella_get_presentation_links_for_email($appointment_id)
{
    $CI =& get_instance();

    $CI->db->select('media.id, media.original_name, media.file_name, media.file_type, media.file_size, media.date_uploaded, pivot.attached_at');
    $CI->db->from(db_prefix() . 'ella_appointment_presentations as pivot');
    $CI->db->join(db_prefix() . 'ella_contractor_media as media', 'media.id = pivot.presentation_id');
    $CI->db->where('pivot.appointment_id', $appointment_id);
    $CI->db->where('media.rel_type', 'presentation');
    $CI->db->order_by('media.original_name', 'ASC');

    $presentations = $CI->db->get()->result_array();

    if (empty($presentations)) {
        return [];
    }

    $results = [];
    foreach ($presentations as $presentation) {
        $fileName = $presentation['file_name'];
        if (!$fileName) {
            continue;
        }
        $publicUrl = site_url('uploads/ella_presentations/' . $fileName);
        $publicUrl = str_replace('http://', 'https://', $publicUrl);
        $results[] = [
            'name'         => $presentation['original_name'] ?: $fileName,
            'display_name' => $presentation['original_name'] ?: $fileName,
            'url'          => $publicUrl,
            'public_url'   => $publicUrl,
            'file_type'    => $presentation['file_type'] ?? null,
            'file_size'    => $presentation['file_size'] ?? null,
            'attached_at'  => $presentation['attached_at'] ?? null,
        ];
    }

    return $results;
}

/**
 * Build HTML block for presentation links.
 *
 * @param array $presentations
 * @return string
 */
function ella_build_presentation_block_html($presentations)
{
    if (empty($presentations)) {
        return '';
    }

    $items = '';
    foreach ($presentations as $presentation) {
        $displayName = $presentation['display_name'] ?? $presentation['name'] ?? '';
        $link = $presentation['public_url'] ?? $presentation['url'] ?? '';

        $name = htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8');
        $url  = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');

        if (!empty($url)) {
            $items .= '<li style="margin-bottom: 8px;"><a href="' . $url . '" target="_blank" style="color: #007bff; text-decoration: none;">' . $name . '</a></li>';
        } else {
            $items .= '<li style="margin-bottom: 8px;">' . $name . '</li>';
        }
    }

    if ($items === '') {
        return '';
    }

    return '
        <div style="margin-bottom: 25px;">
            <h3 style="margin: 0 0 12px; font-size: 18px; color: #333333;">Included Presentations</h3>
            <ul style="padding-left: 18px; margin: 0; color: #333333;">
                ' . $items . '
            </ul>
        </div>';
}


/**
 * Parse email template with appointment data (replace merge fields)
 *
 * @param string $template_name Template name: 'client_appointment_reminder' or 'staff_appointment_reminder'
 * @param object $appointment   Appointment object
 * @param string $type          'client' or 'staff'
 *
 * @return string Parsed email body
 */
function ella_parse_email_template($template_name, $appointment, $type)
{
    $CI =& get_instance();

    // Load email templates helper if not already loaded
    $templates_helper = module_dir_path('ella_contractors', 'helpers/ella_email_templates_helper.php');
    if (!function_exists('ella_get_client_reminder_template')) {
        require_once($templates_helper);
    }

    // Get template based on reminder type
    if ($template_name === 'staff_appointment_reminder' || $type === 'staff') {
        $template = ella_get_staff_reminder_template();
    } else {
        $template = ella_get_client_reminder_template();
    }

    // Fallback template if lookup failed
    if (empty($template)) {
        $template = '<html><body><h2>Appointment Reminder</h2><p>You have an upcoming appointment.</p></body></html>';
        log_message('error', 'EllaContractors: Email template function not found - ' . $template_name);
    }

    // Merge appointment data into template
    $client_or_lead_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Valued Customer');

    $replacements = [
        '{appointment_subject}' => htmlspecialchars($appointment->subject),
        '{appointment_date}'    => date('F j, Y', strtotime($appointment->date)),
        '{appointment_time}'    => date('g:i A', strtotime($appointment->start_hour)),
        '{appointment_location}' => htmlspecialchars($appointment->address ?: 'Online/Phone Call'),
        '{client_name}'         => htmlspecialchars($client_or_lead_name),
        '{staff_name}'          => get_staff_full_name($appointment->created_by),
        '{company_name}'        => get_option('companyname') ?: 'Our Company',
        '{company_phone}'       => get_option('company_phone_number') ?: '',
        '{company_email}'       => get_option('company_email') ?: '',
        '{crm_link}'            => $type === 'staff' ? admin_url('ella_contractors/appointments/view/' . $appointment->id) : '',
        '{appointment_notes}'   => !empty($appointment->notes) ? nl2br(htmlspecialchars($appointment->notes)) : 'No additional notes',
        '{presentation_block}'  => isset($appointment->presentation_block) ? $appointment->presentation_block : '',
    ];

    foreach ($replacements as $key => $value) {
        $template = str_replace($key, $value, $template);
    }

    return $template;
}

/**
 * Parse email template from database with appointment data (replace merge fields)
 * This function is used when templates are loaded from the database
 *
 * @param string $template_content Template content from database
 * @param object $appointment     Appointment object
 * @param string $type            'client' or 'staff'
 *
 * @return string Parsed email content
 */
function ella_parse_email_template_from_db($template_content, $appointment, $type)
{
    if (empty($template_content)) {
        return '';
    }

    // Merge appointment data into template
    $client_or_lead_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Valued Customer');

    $replacements = [
        '{appointment_subject}' => htmlspecialchars($appointment->subject ?? ''),
        '{appointment_date}'    => !empty($appointment->date) ? date('F j, Y', strtotime($appointment->date)) : '',
        '{appointment_time}'    => !empty($appointment->start_hour) ? date('g:i A', strtotime($appointment->start_hour)) : '',
        '{appointment_location}' => htmlspecialchars($appointment->address ?? 'Online/Phone Call'),
        '{client_name}'         => htmlspecialchars($client_or_lead_name),
        '{staff_name}'          => !empty($appointment->created_by) ? get_staff_full_name($appointment->created_by) : '',
        '{company_name}'        => get_option('companyname') ?: 'Our Company',
        '{company_phone}'       => get_option('company_phone_number') ?: '',
        '{company_email}'       => get_option('company_email') ?: '',
        '{crm_link}'            => $type === 'staff' && !empty($appointment->id) ? admin_url('ella_contractors/appointments/view/' . $appointment->id) : '',
        '{appointment_notes}'   => !empty($appointment->notes) ? nl2br(htmlspecialchars($appointment->notes)) : 'No additional notes',
        '{presentation_block}'  => isset($appointment->presentation_block) ? $appointment->presentation_block : '',
    ];

    $parsed = $template_content;
    foreach ($replacements as $key => $value) {
        $parsed = str_replace($key, $value, $parsed);
    }

    return $parsed;
}

/**
 * Parse SMS template from database with appointment data (replace merge fields)
 * This function is used when SMS templates are loaded from the database
 *
 * @param string $template_content Template content from database
 * @param object $appointment     Appointment object
 * @param string $type            'client' or 'staff'
 *
 * @return string Parsed SMS message
 */
function ella_parse_sms_template_from_db($template_content, $appointment, $type)
{
    if (empty($template_content)) {
        return '';
    }

    // Merge appointment data into template (SMS doesn't need HTML encoding)
    $client_or_lead_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Valued Customer');

    $replacements = [
        '{appointment_subject}' => $appointment->subject ?? '',
        '{appointment_date}'    => !empty($appointment->date) ? date('F j, Y', strtotime($appointment->date)) : '',
        '{appointment_time}'    => !empty($appointment->start_hour) ? date('g:i A', strtotime($appointment->start_hour)) : '',
        '{appointment_location}' => $appointment->address ?? 'Online/Phone Call',
        '{client_name}'         => $client_or_lead_name,
        '{staff_name}'          => !empty($appointment->created_by) ? get_staff_full_name($appointment->created_by) : '',
        '{company_name}'        => get_option('companyname') ?: 'Our Company',
        '{company_phone}'       => get_option('company_phone_number') ?: '',
        '{company_email}'       => get_option('company_email') ?: '',
        '{crm_link}'            => $type === 'staff' && !empty($appointment->id) ? admin_url('ella_contractors/appointments/view/' . $appointment->id) : '',
        '{appointment_notes}'   => $appointment->notes ?? 'No additional notes',
    ];

    $parsed = $template_content;
    foreach ($replacements as $key => $value) {
        $parsed = str_replace($key, $value, $parsed);
    }

    return $parsed;
}

/**
 * Send appointment reminder SMS with ICS link
 * Uses existing CRM SMS functionality via leads_model
 * 
 * @param int $appointment_id Appointment ID
 * @param string $type 'client' or 'staff'
 * @return bool Success status
 */
function ella_send_reminder_sms($appointment_id, $stage)
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/ella_appointments_model');
    $CI->load->model('leads_model');
    $CI->load->model('clients_model');

    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot send SMS - Appointment not found: ' . $appointment_id);
        return false;
    }

    $stage   = strtolower($stage);
    $type    = ($stage === 'staff_48h' || $stage === 'staff_same_day') ? 'staff' : 'client';
    $context = $stage;

    if ($type === 'staff') {
        $staff = $CI->db->select('firstname, lastname, phonenumber')
                        ->where('staffid', $appointment->created_by)
                        ->get(db_prefix() . 'staff')
                        ->row();

        if (!$staff || empty($staff->phonenumber)) {
            log_message('error', 'EllaContractors: Staff SMS skipped - no phone number [Appointment ' . $appointment_id . ']');
            return false;
        }

        $normalized = ella_normalize_phone($staff->phonenumber);
        if (strpos($normalized, '+1') !== 0) {
            log_message('info', 'EllaContractors: Staff SMS skipped (non-USA number) [Appointment ' . $appointment_id . ']');
            return false;
        }

        // Load template from database
        $CI->load->model('ella_contractors/Reminder_template_model');
        $template_record = $CI->Reminder_template_model->get_by_stage($stage, 'sms', 'staff');
        
        if ($template_record && $template_record->is_active) {
            $message = ella_parse_sms_template_from_db($template_record->content, $appointment, 'staff');
        } else {
            // Fallback to default message
            $message = 'Reminder: ' . $appointment->subject . ' on ' . date('F j, Y g:i A', strtotime($appointment->date . ' ' . $appointment->start_hour));
        }
        
        $dispatch = ella_dispatch_sms($normalized, $message, [
            'appointment_id' => $appointment_id,
            'staff_id'       => $appointment->created_by,
            'stage'          => $context,
        ]);

        if ($dispatch['success']) {
            $CI->ella_appointments_model->log_activity(
                $appointment_id,
                'sent',
                'sms',
                '',
                [
                    'phone_number' => $normalized,
                    'sms_content'  => $message,
                    'stage'        => $context,
                    'staff_id'     => $appointment->created_by,
                ]
            );
        } else {
            log_message('error', 'EllaContractors: Staff SMS failed for appointment ' . $appointment_id . ' - ' . $dispatch['message']);
        }

        return $dispatch['success'];
    }

    // Client SMS logic
    $phoneNumber = $appointment->phone;
    $leadId = 0;

    if (empty($phoneNumber) && !empty($appointment->contact_id)) {
        if (!empty($appointment->lead_name)) {
            $lead = $CI->leads_model->get($appointment->contact_id);
            if ($lead && !empty($lead->phonenumber)) {
                $phoneNumber = $lead->phonenumber;
                $leadId = $lead->id;
            }
        } else {
            $client = $CI->clients_model->get($appointment->contact_id);
            if ($client && !empty($client->phonenumber)) {
                $phoneNumber = $client->phonenumber;
            }
        }
    }

    if (empty($phoneNumber)) {
        log_message('error', 'EllaContractors: Client SMS skipped - no phone number [Appointment ' . $appointment_id . ']');
        return false;
    }

    $normalized = ella_normalize_phone($phoneNumber);
    if (empty($normalized)) {
        log_message('error', 'EllaContractors: Client SMS skipped - invalid phone number [Appointment ' . $appointment_id . ']');
        return false;
    }

    // Load template from database
    $CI->load->model('ella_contractors/Reminder_template_model');
    $template_record = $CI->Reminder_template_model->get_by_stage($stage, 'sms', 'client');
    
    if ($template_record && $template_record->is_active) {
        $message = ella_parse_sms_template_from_db($template_record->content, $appointment, 'client');
    } else {
        // Fallback to default message
        $message = 'Reminder: ' . $appointment->subject . ' on ' . date('F j, Y g:i A', strtotime($appointment->date . ' ' . $appointment->start_hour));
    }
    
    $dispatch = ella_dispatch_sms($normalized, $message, [
        'appointment_id' => $appointment_id,
        'lead_id'        => $leadId,
        'stage'          => $context,
    ]);

    if ($dispatch['success']) {
        $CI->ella_appointments_model->log_activity(
            $appointment_id,
            'sent',
            'sms',
            '',
            [
                'phone_number' => $normalized,
                'sms_content'  => $message,
                'stage'        => $context,
                'lead_id'      => $leadId,
            ]
        );
    } else {
        log_message('error', 'EllaContractors: Client SMS failed for appointment ' . $appointment_id . ' - ' . $dispatch['message']);
    }

    return $dispatch['success'];
}

/**
 * Dispatch SMS via Telnyx using CRM configuration
 *
 * @param string $phoneNumber Recipient number (E.164 preferred)
 * @param string $message SMS body (without ICS link)
 * @param array  $options Additional payload data
 *
 * @return array ['success'=>bool, 'message'=>string, 'sid'=>string|null]
 */
function ella_dispatch_sms($phoneNumber, $message, $options = [])
{
    $CI =& get_instance();
    $CI->load->model('leads_model');

    $phoneNumber = ella_normalize_phone($phoneNumber);
    if (empty($phoneNumber)) {
        return [
            'success' => false,
            'message' => 'Recipient phone number is invalid.'
        ];
    }

    $apiKey     = get_option('sms_telnyx_api_key');
    $fromNumber = get_option('sms_telnyx_phone_number');
    $profileId  = get_option('sms_telnyx_messaging_profile_id');

    if (empty($apiKey) || empty($fromNumber)) {
        return [
            'success' => false,
            'message' => 'Telnyx configuration missing (API key or from number).'
        ];
    }

    if (!class_exists('\Telnyx\Telnyx')) {
        $globalAutoload = FCPATH . 'vendor/autoload.php';
        $moduleAutoload = module_dir_path('ella_contractors', 'vendor/autoload.php');

        if (file_exists($globalAutoload)) {
            require_once $globalAutoload;
        } elseif (file_exists($moduleAutoload)) {
            require_once $moduleAutoload;
        }

        if (!class_exists('\Telnyx\Telnyx')) {
            return [
                'success' => false,
                'message' => 'Telnyx SDK not available on server.'
            ];
        }
    }

    $payloadText = trim($message);
    if (!empty($options['ics_url'])) {
        $payloadText = trim($payloadText . ' ' . $options['ics_url']);
    }

    if ($payloadText === '') {
        return [
            'success' => false,
            'message' => 'SMS body is empty.'
        ];
    }

    $payload = [
        'from' => $fromNumber,
        'to'   => $phoneNumber,
        'text' => $payloadText,
    ];

    if (!empty($profileId)) {
        $payload['messaging_profile_id'] = $profileId;
    }

    $mediaUrl = $options['media_url'] ?? '';
    if (!empty($mediaUrl)) {
        $payload['media_urls'] = [$mediaUrl];
    }

    $ch = curl_init('https://api.telnyx.com/v2/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $responseBody = curl_exec($ch);
    $curlError    = curl_error($ch);
    $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($responseBody === false || $httpCode < 200 || $httpCode >= 300) {
        $errorMessage = $curlError ?: 'HTTP ' . $httpCode . ' ' . $responseBody;
        return ['success' => false, 'message' => $errorMessage];
    }

    $response = json_decode($responseBody, true);
    $sid = isset($response['data']['id']) ? trim($response['data']['id']) : '';

    $logData = [
        'lead_id'     => $options['lead_id'] ?? 0,
        'sender_id'   => $options['staff_id'] ?? get_staff_user_id(),
        'sender_type' => 'staff',
        'msg_type'    => 'SMS',
        'msg_body'    => nl2br($payloadText),
        'sent_from'   => $fromNumber,
        'sent_to'     => $phoneNumber,
        'MessageSid'  => $sid,
        'date_created'=> date('Y-m-d H:i:s'),
    ];

    if (!empty($mediaUrl)) {
        $logData['MediaUrl0'] = $mediaUrl;
    }

    $CI->leads_model->add_sms_log($logData);

    $leadId = (int) ($options['lead_id'] ?? 0);
    if ($leadId > 0) {
        if (function_exists('update_lead_last_contact')) {
            update_lead_last_contact($leadId, $logData['sender_id'], 'SMS');
        }
        $CI->leads_model->updated_sms_log_status($leadId, 'lead_id', '0');
    }

    return [
        'success' => true,
        'message' => 'SMS dispatched successfully.',
        'sid'     => $sid,
    ];
}

/**
 * Normalize phone numbers to E.164 when possible.
 *
 * @param string $phone
 * @return string
 */
function ella_normalize_phone($phone)
{
    $phone = trim((string) $phone);
    if ($phone === '') {
        return '';
    }

    $digits = preg_replace('/[^0-9\+]/', '', $phone);
    if ($digits === '') {
        return '';
    }

    if ($digits[0] !== '+') {
        if (strlen($digits) === 10) {
            $digits = '+1' . $digits;
        } elseif (strlen($digits) === 11 && $digits[0] === '1') {
            $digits = '+' . $digits;
        } else {
            $digits = '+' . $digits;
        }
    }

    return $digits;
}

/**
 * Helper to convert generated ICS file path to a public URL.
 *
 * @param int    $appointment_id
 * @param string $type
 * @return string
 */
function ella_get_ics_public_url($appointment_id, $type = 'client')
{
    $ics_file = ella_generate_ics($appointment_id, $type);
    if (!$ics_file || !file_exists($ics_file)) {
        return '';
    }

    $relative_path = ltrim(str_replace(FCPATH, '', $ics_file), '/');
    return site_url($relative_path);
}

function ella_run_reminder_dispatch()
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/Appointment_reminder_model', 'appointment_reminder_model');
    $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');

    $query = $CI->db->select([
            'rem.*',
            'app.id AS appointment_id',
            'app.subject',
            'app.date',
            'app.start_hour',
            'app.end_date',
            'app.end_time',
            'app.email',
            'app.phone',
            'app.reminder_channel',
            'app.send_reminder',
            'app.reminder_48h',
            'app.reminder_same_day',
            'app.staff_reminder_48h',
            'app.staff_reminder_same_day',
            'app.appointment_status',
            'app.source',
        ])
        ->from(db_prefix() . 'appointment_reminder AS rem')
        ->join(db_prefix() . 'appointly_appointments AS app', 'app.id = rem.appointment_id', 'inner')
        ->where('app.source', 'ella_contractor')
        ->where('(app.appointment_status IS NULL OR app.appointment_status NOT IN ("cancelled","complete"))', null, false)
        ->get();

    $rows = $query->result();
    $processed = count($rows);
    $notificationsSent = 0;

    foreach ($rows as $row) {
        $channel = strtolower($row->reminder_channel ?? 'both');
        $sendEmail = in_array($channel, ['email', 'both'], true);
        $sendSms   = in_array($channel, ['sms', 'both'], true);

        $hoursUntil = ella_hours_until_appointment($row);
        $within48Hours = $hoursUntil !== null && $hoursUntil <= 48 && $hoursUntil >= 0;
        $withinSameDay = $hoursUntil !== null && $hoursUntil <= 24 && $hoursUntil >= 0;
        
        // // condition 1 client instant confirmation starts here 
        if ((int)$row->client_instant_remind === 1 && (int)$row->client_instant_sent === 0) {
            if ($sendEmail && ella_send_reminder_email($row->appointment_id, 'client_instant')) {
                $CI->appointment_reminder_model->mark_email_stage_sent($row->appointment_id, 'client_instant');
                $notificationsSent++;
            }
        }
        // condition 1 client instant confirmation ends here 



        //Condition 2 client 48-hour reminder starts here
        if ((int)$row->client_48_hours === 1 && (int)$row->client_48_hours_sent === 0 && (int)$row->reminder_48h === 1 && $within48Hours) {
            if ($sendEmail && ella_send_reminder_email($row->appointment_id, 'client_48h')) {
                $CI->appointment_reminder_model->mark_email_stage_sent($row->appointment_id, 'client_48h');
                $notificationsSent++;
            }
        }
        //Condition 2 client 48-hour reminder ends here


        //Condition 3 staff 48-hour reminder starts here
        if ((int)$row->staff_48_hours === 1 && (int)$row->staff_48_hours_sent === 0 && (int)$row->staff_reminder_48h === 1 && $within48Hours) {
            if ($sendEmail && ella_send_reminder_email($row->appointment_id, 'staff_48h')) {
                $CI->appointment_reminder_model->mark_email_stage_sent($row->appointment_id, 'staff_48h');
                $notificationsSent++;
            }
        }
        //Condition 3 staff 48-hour reminder ends here
        
        //Condition 4 client same-day reminder starts here
        if ((int)$row->client_same_day === 1 && (int)$row->client_same_day_sent === 0 && (int)$row->reminder_same_day === 1 && $withinSameDay) {
            if ($sendEmail && ella_send_reminder_email($row->appointment_id, 'client_same_day')) {
                $CI->appointment_reminder_model->mark_email_stage_sent($row->appointment_id, 'client_same_day');
                $notificationsSent++;
            }
        }
        //Condition 4 client same-day reminder ends here
        
        //Condition 5 staff same-day reminder starts here (includes presentations)
        if ((int)$row->staff_same_day === 1 && (int)$row->staff_same_day_sent === 0 && (int)$row->staff_reminder_same_day === 1 && $withinSameDay) {
            if ($sendEmail && ella_send_reminder_email($row->appointment_id, 'staff_same_day')) {
                $CI->appointment_reminder_model->mark_email_stage_sent($row->appointment_id, 'staff_same_day');
                $notificationsSent++;
            }
        }
        //Condition 5 staff same-day reminder ends here
        
        // =========================
        // SMS Conditions
        // =========================

        // Condition 1: Client instant SMS (only once)
        if ($sendSms
            && (int)$row->client_instant_remind === 1
            && (int)$row->client_sms_reminder === 1
            && (int)$row->sms_send === 0
        ) {
            if (ella_send_reminder_sms($row->appointment_id, 'client_instant')) {
                $CI->appointment_reminder_model->mark_sms_stage_sent($row->appointment_id, 'client_instant');
                $notificationsSent++;
            }
        }

        // Condition 2: Client 48-hour SMS reminder
        if ($sendSms
            && (int)$row->client_sms_reminder === 1
            && (int)$row->client_sms_48_hours_sent === 0
            && (int)$row->client_48_hours === 1
            && (int)$row->reminder_48h === 1
            && $within48Hours
        ) {
            if (ella_send_reminder_sms($row->appointment_id, 'client_48h')) {
                $CI->appointment_reminder_model->mark_sms_stage_sent($row->appointment_id, 'client_48h');
                $notificationsSent++;
            }
        }

        // Condition 3: Staff 48-hour SMS reminder
        if ($sendSms
            && (int)$row->staff_sms_48_hours_sent === 0
            && (int)$row->staff_48_hours === 1
            && (int)$row->staff_reminder_48h === 1
            && $within48Hours
        ) {
            if (ella_send_reminder_sms($row->appointment_id, 'staff_48h')) {
                $CI->appointment_reminder_model->mark_sms_stage_sent($row->appointment_id, 'staff_48h');
                $notificationsSent++;
            }
        }
        
        // Condition 4: Client same-day SMS reminder
        if ($sendSms
            && (int)$row->client_sms_reminder === 1
            && (int)$row->client_same_day === 1
            && (int)$row->reminder_same_day === 1
            && (int)$row->client_sms_same_day_sent === 0
            && $withinSameDay
        ) {
            if (ella_send_reminder_sms($row->appointment_id, 'client_same_day')) {
                $CI->appointment_reminder_model->mark_sms_stage_sent($row->appointment_id, 'client_same_day');
                $notificationsSent++;
            }
        }
        
        // Condition 5: Staff same-day SMS reminder
        if ($sendSms
            && (int)$row->staff_same_day === 1
            && (int)$row->staff_reminder_same_day === 1
            && (int)$row->staff_sms_same_day_sent === 0
            && $withinSameDay
        ) {
            if (ella_send_reminder_sms($row->appointment_id, 'staff_same_day')) {
                $CI->appointment_reminder_model->mark_sms_stage_sent($row->appointment_id, 'staff_same_day');
                $notificationsSent++;
            }
        }
    }

    return [
        'processed' => $processed,
        'notifications_sent' => $notificationsSent,
    ];
}