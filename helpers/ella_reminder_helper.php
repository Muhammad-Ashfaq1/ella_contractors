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
function ella_send_appointment_email($appointment_id, $type = 'client')
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/ella_appointments_model');
    $CI->load->library('email');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot send email - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    // Generate ICS file
    $ics_file = ella_generate_ics($appointment_id, $type);
    
    // ========== TESTING MODE - HARDCODED VALUES ==========
    // TODO: Remove this section after testing is complete
    $to_email = 'mitf19e032@gmail.com';  // Hardcoded test email
    $to_phone = '+923157364689';          // Hardcoded test phone (not used in email, but logged)
    
    if ($type === 'staff') {
        $to_name = 'Test Staff Member';
        $subject = 'Your Appointment Reminder: ' . $appointment->subject;
        $template = 'staff_appointment_reminder';
        log_message('info', 'EllaContractors: [TEST MODE] Sending STAFF reminder to ' . $to_email . ' (Phone: ' . $to_phone . ')');
    } else {
        $to_name = 'Test Client';
        $subject = 'Appointment Confirmation: ' . $appointment->subject;
        $template = 'client_appointment_reminder';
        log_message('info', 'EllaContractors: [TEST MODE] Sending CLIENT reminder to ' . $to_email . ' (Phone: ' . $to_phone . ')');
    }
    // ========== END TESTING MODE ==========
    
    /* COMMENTED OUT FOR TESTING - UNCOMMENT AFTER TESTING
    // Determine recipient
    if ($type === 'staff') {
        // Send to appointment creator (assigned staff)
        $staff = $CI->db->get_where(db_prefix() . 'staff', ['staffid' => $appointment->created_by])->row();
        if (!$staff || empty($staff->email)) {
            log_message('error', 'EllaContractors: Cannot send staff email - Staff not found or no email: ' . $appointment->created_by);
            return false;
        }
        $to_email = $staff->email;
        $to_name = $staff->firstname . ' ' . $staff->lastname;
        $subject = 'Your Appointment Reminder: ' . $appointment->subject;
        $template = 'staff_appointment_reminder';
    } else {
        // Send to client/lead
        if (empty($appointment->email)) {
            log_message('error', 'EllaContractors: Cannot send client email - No email address for appointment: ' . $appointment_id);
            return false;
        }
        $to_email = $appointment->email;
        $to_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Valued Customer');
        $subject = 'Appointment Confirmation: ' . $appointment->subject;
        $template = 'client_appointment_reminder';
    }
    END COMMENTED SECTION */
    
    // Build email body from template
    $email_body = ella_parse_email_template($template, $appointment, $type);
    
    // Get from email with fallback (use smtp_email like the rest of CRM)
    $from_email = get_option('smtp_email');
    if (empty($from_email)) {
        $from_email = get_option('company_email');
    }
    
    $from_name = get_option('companyname');
    
    // Fallback if email not set or invalid
    if (empty($from_email) || !filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
        // Use test email for testing mode
        $from_email = 'mitf19e032@gmail.com';
        log_message('warning', 'EllaContractors: SMTP email not configured, using test email: ' . $from_email);
    }
    
    if (empty($from_name)) {
        $from_name = 'EllaContractors CRM';
    }
    
    log_message('info', 'EllaContractors: Sending email FROM: ' . $from_email . ' (' . $from_name . ') TO: ' . $to_email);
    
    // Send email using CRM's email system (same pattern as send_reminder_ajax)
    $CI->email->clear();
    $CI->email->from($from_email, $from_name);
    $CI->email->to($to_email);
    $CI->email->subject($subject);
    $CI->email->message($email_body);
    
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
                'email_type' => $type === 'staff' ? 'staff_reminder' : 'client_reminder',
                'has_ics_attachment' => $ics_file ? true : false
            ]
        );
    } else {
        log_message('error', 'EllaContractors: Failed to send email to ' . $to_email . ' - ' . $CI->email->print_debugger());
    }
    
    return $result;
}


/**
 * Parse email template with appointment data (replace merge fields)
 * 
 * @param string $template_name Template name: 'client_appointment_reminder' or 'staff_appointment_reminder'
 * @param object $appointment Appointment object
 * @param string $type 'client' or 'staff'
 * @return string Parsed email body
 */
function ella_parse_email_template($template_name, $appointment, $type)
{
    $CI =& get_instance();
    
    // Load email templates helper
    $templates_helper = module_dir_path('ella_contractors', 'helpers/ella_email_templates_helper.php');
    if (!function_exists('ella_get_client_reminder_template')) {
        require_once($templates_helper);
    }
    
    // Get template based on type
    if ($template_name === 'staff_appointment_reminder' || $type === 'staff') {
        $template = ella_get_staff_reminder_template();
    } else {
        $template = ella_get_client_reminder_template();
    }
    
    // Fallback template if function doesn't exist
    if (empty($template)) {
        $template = '<html><body><h2>Appointment Reminder</h2><p>You have an upcoming appointment.</p></body></html>';
        log_message('error', 'EllaContractors: Email template function not found - ' . $template_name);
    }
    
    // Prepare replacement data
    $client_or_lead_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Valued Customer');
    
    $replacements = [
        '{appointment_subject}' => htmlspecialchars($appointment->subject),
        '{appointment_date}' => date('F j, Y', strtotime($appointment->date)),
        '{appointment_time}' => date('g:i A', strtotime($appointment->start_hour)),
        '{appointment_location}' => htmlspecialchars($appointment->address ?: 'Online/Phone Call'),
        '{client_name}' => htmlspecialchars($client_or_lead_name),
        '{staff_name}' => get_staff_full_name($appointment->created_by),
        '{company_name}' => get_option('companyname') ?: 'Our Company',
        '{company_phone}' => get_option('company_phone_number') ?: '',
        '{company_email}' => get_option('company_email') ?: '',
        '{crm_link}' => $type === 'staff' ? admin_url('ella_contractors/appointments/view/' . $appointment->id) : '',
        '{appointment_notes}' => !empty($appointment->notes) ? nl2br(htmlspecialchars($appointment->notes)) : 'No additional notes',
    ];
    
    foreach ($replacements as $key => $value) {
        $template = str_replace($key, $value, $template);
    }

    if (!function_exists('format_appointment_presentation_links')) {
        $CI->load->helper('ella_contractors/ella_appointments_helper');
    }
    $presentationsHtml = '';
    if (function_exists('format_appointment_presentation_links')) {
        $presentationsHtml = format_appointment_presentation_links($appointment->id, 'email');
    }

    $notesHtml = '';
    if (!empty($appointment->notes)) {
        $notesHtml = '<h3 style="font-size:16px;margin:20px 0 10px;color:#161c2d;">Notes</h3>'
            . '<div style="font-size:14px;line-height:22px;color:#4a4a4a;">'
            . nl2br(htmlspecialchars($appointment->notes)) . '</div>';
    }

    $greetingName = $type === 'staff' ? get_staff_full_name($appointment->created_by) : $replacements['{client_name}'];
    $introLine = $type === 'staff'
        ? 'Here are the latest details for the scheduled appointment.'
        : 'This is a quick reminder about your upcoming appointment.';

    $crmLink = '';
    if (!empty($replacements['{crm_link}'])) {
        $crmLink = '<p style="margin:18px 0 0;font-size:14px;">'
            . '<a href="' . htmlspecialchars($replacements['{crm_link}']) . '" style="color:#1b5f8c;text-decoration:none;">'
            . 'View appointment in CRM</a></p>';
    }

    $detailsTable = '
        <table style="width:100%;border-collapse:collapse;margin:15px 0 5px;">
            <tbody>
                <tr>
                    <td style="padding:10px 12px;background:#f7f9fc;border:1px solid #e6e9ef;font-size:13px;font-weight:600;color:#161c2d;width:35%;">Appointment</td>
                    <td style="padding:10px 12px;border:1px solid #e6e9ef;font-size:13px;color:#161c2d;">' . $replacements['{appointment_subject}'] . '</td>
                </tr>
                <tr>
                    <td style="padding:10px 12px;background:#f7f9fc;border:1px solid #e6e9ef;font-size:13px;font-weight:600;color:#161c2d;">Date</td>
                    <td style="padding:10px 12px;border:1px solid #e6e9ef;font-size:13px;color:#161c2d;">' . $replacements['{appointment_date}'] . '</td>
                </tr>
                <tr>
                    <td style="padding:10px 12px;background:#f7f9fc;border:1px solid #e6e9ef;font-size:13px;font-weight:600;color:#161c2d;">Time</td>
                    <td style="padding:10px 12px;border:1px solid #e6e9ef;font-size:13px;color:#161c2d;">' . $replacements['{appointment_time}'] . '</td>
                </tr>
                <tr>
                    <td style="padding:10px 12px;background:#f7f9fc;border:1px solid #e6e9ef;font-size:13px;font-weight:600;color:#161c2d;">Location</td>
                    <td style="padding:10px 12px;border:1px solid #e6e9ef;font-size:13px;color:#161c2d;">' . $replacements['{appointment_location}'] . '</td>
                </tr>
            </tbody>
        </table>';

    $messageBody = '<div style="font-family:Helvetica,Arial,sans-serif;color:#161c2d;font-size:14px;line-height:22px;">'
        . '<p style="margin:0 0 12px;">Hello ' . htmlspecialchars($greetingName) . ',</p>'
        . '<p style="margin:0 0 15px;">' . $introLine . '</p>'
        . $detailsTable
        . $notesHtml
        . $crmLink
        . (!empty($presentationsHtml) ? $presentationsHtml : '')
        . '<p style="margin:22px 0 0;">Thank you,<br>' . htmlspecialchars($replacements['{company_name}']) . '</p>'
        . '</div>';

    $header = get_option('email_header') ?: '';
    $footer = get_option('email_footer') ?: '';

    return $header . $messageBody . $footer;
}

/**
 * Send appointment reminder SMS with ICS link
 * Uses existing CRM SMS functionality via leads_model
 * 
 * @param int $appointment_id Appointment ID
 * @param string $type 'client' or 'staff'
 * @return bool Success status
 */
function ella_send_appointment_sms($appointment_id, $type = 'client')
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/ella_appointments_model');
    $CI->load->model('leads_model');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot send SMS - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    // ========== TESTING MODE - HARDCODED VALUES ==========
    $to_phone = '+923158600761';  // Hardcoded test phone
    $lead_id = $appointment->contact_id ?: 0;  // Use actual lead_id if available
    $staff_id = get_staff_user_id();
    
    // Build SMS message
    if ($type === 'staff') {
        $client_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Client');
        $sms_body = "Appointment Reminder: {$appointment->subject} with {$client_name} on " . 
                    date('M j, Y', strtotime($appointment->date)) . " at " . 
                    date('g:i A', strtotime($appointment->start_hour));
        log_message('info', 'EllaContractors: [TEST MODE] Sending STAFF SMS to ' . $to_phone);
    } else {
        $sms_body = "Appointment Confirmation: {$appointment->subject} on " . 
                    date('M j, Y', strtotime($appointment->date)) . " at " . 
                    date('g:i A', strtotime($appointment->start_hour)) . 
                    ". Location: " . ($appointment->address ?: 'Online/Phone');
        log_message('info', 'EllaContractors: [TEST MODE] Sending CLIENT SMS to ' . $to_phone);
    }
    
    // Generate ICS URL (optional - for SMS link)
    $ics_url = '';
    
    // Send SMS using CRM's existing method (same as Appointments controller line 672)
    $media_url = '';
    $tcpa = false;  // Disable TCPA for ella_contractors
    $dnc_validation = false;  // Disable DNC validation for ella_contractors
    
    $response = $CI->leads_model->send_sms($lead_id, $staff_id, $to_phone, $sms_body, $media_url, $tcpa, $dnc_validation, $ics_url);
    
    if ($response['success']) {
        log_message('info', 'EllaContractors: SMS sent successfully to ' . $to_phone . ' for appointment ' . $appointment_id);
        
        // Log SMS activity to appointment timeline
        $CI->ella_appointments_model->add_activity_log(
            $appointment_id,
            'SMS',
            'sent',
            [
                'phone_number' => $to_phone,
                'sms_content' => substr($sms_body, 0, 100),
                'sms_type' => $type === 'staff' ? 'staff_reminder' : 'client_reminder'
            ]
        );
        return true;
    } else {
        log_message('error', 'EllaContractors: Failed to send SMS to ' . $to_phone . ' - ' . ($response['message'] ?? 'Unknown error'));
        return false;
    }
}

/**
 * Schedule all reminders for an appointment (called after save)
 * This is the main entry point called from the controller
 * Sends both EMAIL and SMS reminders
 * 
 * @param int $appointment_id Appointment ID
 * @return bool Success status
 */
function ella_schedule_reminders($appointment_id)
{
    $CI =& get_instance();
    $CI->load->model('ella_contractors/ella_appointments_model');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot schedule reminders - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    $results = [];
    $scheduled_reminders = [];
    
    // 1. Client Instant Reminder (EMAIL + SMS - send immediately)
    if (isset($appointment->send_reminder) && $appointment->send_reminder == 1) {
        // Send Email
        $email_result = ella_send_appointment_email($appointment_id, 'client');
        $results[] = $email_result;
        if ($email_result) {
            $scheduled_reminders[] = 'Client Instant Email';
        }
        
        // Send SMS
        $sms_result = ella_send_appointment_sms($appointment_id, 'client');
        $results[] = $sms_result;
        if ($sms_result) {
            $scheduled_reminders[] = 'Client Instant SMS';
        }
    }
    
    // 2. Client 48h Reminder (EMAIL + SMS - send immediately for testing)
    // NOTE: In production, you may want to schedule this via cron job
    if (isset($appointment->reminder_48h) && $appointment->reminder_48h == 1) {
        // Send Email
        $email_result = ella_send_appointment_email($appointment_id, 'client');
        $results[] = $email_result;
        if ($email_result) {
            $scheduled_reminders[] = 'Client 48h Email';
        }
        
        // Send SMS
        $sms_result = ella_send_appointment_sms($appointment_id, 'client');
        $results[] = $sms_result;
        if ($sms_result) {
            $scheduled_reminders[] = 'Client 48h SMS';
        }
    }
    
    // 3. Staff 48h Reminder (EMAIL + SMS - send immediately for testing) - NEW
    // NOTE: In production, you may want to schedule this via cron job
    if (isset($appointment->staff_reminder_48h) && $appointment->staff_reminder_48h == 1) {
        // Send Email
        $email_result = ella_send_appointment_email($appointment_id, 'staff');
        $results[] = $email_result;
        if ($email_result) {
            $scheduled_reminders[] = 'Staff 48h Email';
        }
        
        // Send SMS
        $sms_result = ella_send_appointment_sms($appointment_id, 'staff');
        $results[] = $sms_result;
        if ($sms_result) {
            $scheduled_reminders[] = 'Staff 48h SMS';
        }
    }
    
    // Log activity if any reminders were scheduled
    if (!empty($scheduled_reminders)) {
        $CI->ella_appointments_model->add_activity_log(
            $appointment_id,
            'REMINDERS',
            'scheduled',
            [
                'client_instant' => $appointment->send_reminder ?? 0,
                'client_48h' => $appointment->reminder_48h ?? 0,
                'staff_48h' => $appointment->staff_reminder_48h ?? 0,
                'scheduled_types' => implode(', ', $scheduled_reminders)
            ]
        );
        
        log_message('info', 'EllaContractors: Reminders scheduled for appointment ' . $appointment_id . ' - ' . implode(', ', $scheduled_reminders));
    } else {
        log_message('info', 'EllaContractors: No reminders to schedule for appointment ' . $appointment_id);
    }
    
    return !empty($results);
}

