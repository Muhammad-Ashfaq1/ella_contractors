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
    $CI->load->model('leads_model');
    $CI->load->model('clients_model');
    $CI->load->library('email');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot send email - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    // Generate ICS file
    $ics_file = ella_generate_ics($appointment_id, $type);
    
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
        $subject = 'Your Appointment Reminder: ' . $appointment->subject;
        $template = 'staff_appointment_reminder';
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
        
        $subject = 'Appointment Confirmation: ' . $appointment->subject;
        $template = 'client_appointment_reminder';
    }

    // Capture intended recipient for future reference
    $intended_email = $to_email;
    // Temporary override: route all appointment emails to test inbox (intended recipient stored in $intended_email)
    $to_email = 'mitf19e032@gmail.com';
    $to_name  = $to_name ?? 'Test Recipient';
    $intended_email_log = $intended_email ?: '[unknown]';
    log_message('info', 'EllaContractors: Email intended for ' . $intended_email_log . ' routed to test inbox: ' . $to_email);
    
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
    // Get template based on type
    if ($template_name === 'staff_appointment_reminder' || $type === 'staff') {
        $template = ella_get_staff_reminder_template();
    } else {
        $template = ella_get_client_reminder_template();
    }
    
    // Fallback template if function doesn't exist
    // Fallback template if function doesn't exist
    if (empty($template)) {
        $template = '<html><body><h2>Appointment Reminder</h2><p>You have an upcoming appointment.</p></body></html>';
        log_message('error', 'EllaContractors: Email template function not found - ' . $template_name);
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
    
    return $template;
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
    $CI->load->model('clients_model');
    
    $appointment = $CI->ella_appointments_model->get_appointment($appointment_id);
    if (!$appointment) {
        log_message('error', 'EllaContractors: Cannot send SMS - Appointment not found: ' . $appointment_id);
        return false;
    }
    
    $staff_id = get_staff_user_id();
    $lead_id = 0;
    $recipient_phone = '';
    
    if ($type === 'staff') {
        $staff = $CI->db->select('phonenumber, firstname, lastname')
                        ->where('staffid', $appointment->created_by)
                        ->get(db_prefix() . 'staff')
                        ->row();
        $recipient_phone = $staff->phonenumber ?? '';
    } else {
        $recipient_phone = $appointment->phone;
        if (empty($recipient_phone) && !empty($appointment->contact_id)) {
            if (!empty($appointment->lead_name)) {
                $lead = $CI->leads_model->get($appointment->contact_id);
                if ($lead) {
                    $recipient_phone = $lead->phonenumber ?? '';
                    $lead_id = (int) $lead->id;
                }
            } else {
                $client = $CI->clients_model->get($appointment->contact_id);
                if ($client) {
                    $recipient_phone = $client->phonenumber ?? '';
                }
            }
        } else {
            if (!empty($appointment->lead_name) && !empty($appointment->contact_id)) {
                $lead_id = (int) $appointment->contact_id;
            }
        }
    }
    
    $intended_phone = ella_normalize_phone($recipient_phone);
    if (empty($intended_phone)) {
        log_message('error', 'EllaContractors: Cannot send SMS - No valid phone number for appointment ' . $appointment_id . ' [' . $type . ']');
        return false;
    }
    // Temporary override: route all appointment SMS to test number (intended recipient stored in $intended_phone)
    $recipient_phone = '+923157364689';
    
    if ($type === 'staff') {
        $client_name = $appointment->lead_name ?: ($appointment->client_name ?: 'Client');
        $sms_body = "Appointment Reminder: {$appointment->subject} with {$client_name} on " .
            date('M j, Y', strtotime($appointment->date)) . " at " .
            date('g:i A', strtotime($appointment->start_hour));
    } else {
        $sms_body = "Appointment Confirmation: {$appointment->subject} on " .
            date('M j, Y', strtotime($appointment->date)) . " at " .
            date('g:i A', strtotime($appointment->start_hour)) .
            ". Location: " . ($appointment->address ?: 'Online/Phone');
    }
    
    $ics_url = ella_get_ics_public_url($appointment_id, $type);
    
    $dispatch_result = ella_dispatch_sms($recipient_phone, $sms_body, [
        'lead_id'        => $lead_id,
        'staff_id'       => $staff_id,
        'appointment_id' => $appointment_id,
        'ics_url'        => $ics_url,
        'media_url'      => ''
    ]);
    
    if (!empty($dispatch_result['success'])) {
        $CI->ella_appointments_model->add_activity_log(
            $appointment_id,
            'SMS',
            'sent',
            [
                'phone_number' => $recipient_phone,
                'sms_content'  => substr($sms_body, 0, 255),
                'sms_type'     => $type === 'staff' ? 'staff_reminder' : 'client_reminder',
                'telnyx_sid'   => $dispatch_result['sid'] ?? ''
            ]
        );
        return true;
    }
    
    log_message('error', 'EllaContractors: Failed to send SMS to ' . $recipient_phone . ' for appointment ' . $appointment_id . ' - ' . ($dispatch_result['message'] ?? 'Unknown error'));
    return false;
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

    $intended_number = $phoneNumber;
    // Temporary override: ensure SMS always goes to test number during QA
    $phoneNumber = '+923157364689';
    $appointmentRef = isset($options['appointment_id']) ? ' (Appointment #' . $options['appointment_id'] . ')' : '';
    $intended_number_log = $intended_number ?: '[unknown]';
    log_message('info', 'EllaContractors: SMS intended for ' . $intended_number_log . ' routed to test number ' . $phoneNumber . $appointmentRef);

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

    try {
        \Telnyx\Telnyx::setApiKey($apiKey);
        $response = \Telnyx\Message::create($payload);
    } catch (\Telnyx\Exception\ApiErrorException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    } catch (\Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }

    $sid = isset($response->id) ? trim($response->id) : '';

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

/**
 * Schedule all reminders for an appointment (called after save)
 * This is the main entry point called from the controller
 * Sends both EMAIL and SMS reminders
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

    $channel = isset($appointment->reminder_channel) ? strtolower($appointment->reminder_channel) : 'both';
    if (!in_array($channel, ['sms', 'email', 'both'], true)) {
        $channel = 'both';
    }
    $allow_email = in_array($channel, ['email', 'both'], true);
    $allow_sms = in_array($channel, ['sms', 'both'], true);
    
    $results = [];
    $scheduled_reminders = [];
    
    // 1. Client Instant Reminder (EMAIL + SMS - send immediately)
    // 1. Client Instant Reminder (EMAIL + SMS - send immediately)
    if (isset($appointment->send_reminder) && $appointment->send_reminder == 1) {
        // Send Email
        if ($allow_email) {
            $email_result = ella_send_appointment_email($appointment_id, 'client');
            $results[] = $email_result;
            if ($email_result) {
                $scheduled_reminders[] = 'Client Instant Email';
            }
        }
        if ($allow_sms) {
            log_message('info', 'EllaContractors: SMS reminders temporarily disabled for appointment ' . $appointment_id . ' (client instant)');
            $results[] = true;
        }
        /*
        // Send SMS
        $sms_result = ella_send_appointment_sms($appointment_id, 'client');
        $results[] = $sms_result;
        if ($sms_result) {
            $scheduled_reminders[] = 'Client Instant SMS';
        }
        */
    }
    
    // 2. Client 48h Reminder (EMAIL + SMS - send immediately for testing)
    // NOTE: In production, you may want to schedule this via cron job
    if (isset($appointment->reminder_48h) && $appointment->reminder_48h == 1) {
        // Send Email
        if ($allow_email) {
            $email_result = ella_send_appointment_email($appointment_id, 'client');
            $results[] = $email_result;
            if ($email_result) {
                $scheduled_reminders[] = 'Client 48h Email';
            }
        }
        if ($allow_sms) {
            log_message('info', 'EllaContractors: SMS reminders temporarily disabled for appointment ' . $appointment_id . ' (client 48h)');
            $results[] = true;
        }
        /*
        // Send SMS
        $sms_result = ella_send_appointment_sms($appointment_id, 'client');
        $results[] = $sms_result;
        if ($sms_result) {
            $scheduled_reminders[] = 'Client 48h SMS';
        }
        */
    }
    
    // 3. Staff 48h Reminder (EMAIL + SMS - send immediately for testing) - NEW
    // NOTE: In production, you may want to schedule this via cron job
    if (isset($appointment->staff_reminder_48h) && $appointment->staff_reminder_48h == 1) {
        // Send Email
        if ($allow_email) {
            $email_result = ella_send_appointment_email($appointment_id, 'staff');
            $results[] = $email_result;
            if ($email_result) {
                $scheduled_reminders[] = 'Staff 48h Email';
            }
        }
        if ($allow_sms) {
            log_message('info', 'EllaContractors: SMS reminders temporarily disabled for appointment ' . $appointment_id . ' (staff 48h)');
            $results[] = true;
        }
        /*
        // Send SMS
        $sms_result = ella_send_appointment_sms($appointment_id, 'staff');
        $results[] = $sms_result;
        if ($sms_result) {
            $scheduled_reminders[] = 'Staff 48h SMS';
        }
        */
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

