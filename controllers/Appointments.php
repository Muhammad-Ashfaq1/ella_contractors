<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $this->load->model('ella_contractors/Appointment_reminder_model', 'appointment_reminder_model');
        $this->load->model('ella_contractors/Reminder_template_model', 'reminder_template_model');
        $this->load->model('staff_model');
        $this->load->model('clients_model');
        $this->load->model('leads_model');
        $this->load->model('misc_model');
        
        // Load calendar sync helper
        $calendar_helper_path = module_dir_path('ella_contractors', 'helpers/calendar_sync_helper.php');
        if (file_exists($calendar_helper_path)) {
            require_once($calendar_helper_path);
        }
    }
    
    

    /**
     * Appointments listing page
     */
    public function index()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Appointments';
        $data['appointment_types'] = $this->appointments_model->get_appointment_types();
        $data['statuses'] = $this->appointments_model->get_statuses();
        $this->load->view('appointments/index', $data);
    }


    /**
     * Edit appointment page
     */
    public function edit($id)
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            show_404();
        }

        // Redirect to main page with edit parameter
        redirect(admin_url('ella_contractors/appointments?edit=' . $id));
    }

    /**
     * View appointment details
     */
    public function view($id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            show_404();
        }

        $data['title'] = 'View Appointment';
        $data['appointment'] = $appointment; // Keep as object for easier access
        $data['attendees'] = $this->appointments_model->get_appointment_attendees($id);        
        // Load clients and leads for estimate modal
        $data['clients'] = $this->clients_model->get();
        $data['leads'] = $this->leads_model->get();
        // Load appointment types for modal dropdown
        $data['appointment_types'] = $this->appointments_model->get_appointment_types();
        $data['statuses'] = $this->appointments_model->get_statuses();
        
        // Load language file for view
        $this->lang->load('ella_contractors/ella_contractors', 'english');
        
        // Timeline activities will be loaded via AJAX when tab is clicked
        
        $this->load->view('appointments/view', $data);
    }
    /**
     * Delete appointment
     */
    public function delete($id)
    {
        if (!has_permission('ella_contractor', '', 'delete')) {
            access_denied('ella_contractors');
        }

        if ($this->appointments_model->delete_appointment($id)) {
            set_alert('success', 'Appointment deleted successfully');
        } else {
            set_alert('warning', 'Failed to delete appointment');
        }

        redirect(admin_url('ella_contractors/appointments'));
    }
    
    /**
     * Get relation data values
     * @param string $rel_id
     * @param string $rel_type
     * @return json
     */
    public function get_relation_data_values($rel_id, $rel_type)
    {
        // Initialize the data object
        $data = new StdClass();
        
        // Get lead/client data based on the relation type and ID
        if ($rel_type == 'lead') {
            // Load the leads model
            $this->load->model('leads_model');
            $lead = $this->leads_model->get($rel_id);
            
            if ($lead) {
                $data->to = $lead->name;
                $data->email = $lead->email;
                $data->phone = $lead->phonenumber;
                $data->address = $lead->address;
                $data->city = $lead->city;
                $data->state = $lead->state;
                $data->zip = $lead->zip;
                $data->country = $lead->country;
                
                // Get assigned staff email
                if ($lead->assigned != 0) {
                    $this->db->select('email');
                    $this->db->where('staffid', $lead->assigned);
                    $staff = $this->db->get(db_prefix() . 'staff')->row();
                    $data->staffEmail = $staff ? $staff->email : '';
                } else {
                    $data->staffEmail = '';
                }
                
                // Email validation status
                $data->emailValidaionStatus = 1; // Default to valid
                if ($lead->email && (!filter_var($lead->email, FILTER_VALIDATE_EMAIL) || strpos($lead->email, '@') === false)) {
                    $data->emailValidaionStatus = 0; // Mark as invalid
                }
                
                // Phone number validation
                if ($lead->phonenumber) {
                    $data->phonenumbertype = isset($lead->phonenumber_type) ? $lead->phonenumber_type : 'mobile';
                    $data->phoneNumberValid = 1; // Default to valid
                    if ($data->phonenumbertype == 'landline' || $data->phonenumbertype == 'invalid') {
                        $data->phoneNumberValid = 0; // Mark as invalid
                    }
                } else {
                    $data->phoneNumberValid = null;
                }
            } else {
                $data->error = 'Lead not found';
            }
        } else if ($rel_type == 'customer') {
            // Load the clients model
            $this->load->model('clients_model');
            $client = $this->clients_model->get($rel_id);
            
            if ($client) {
                $data->to = $client->company;
                $data->email = $client->email;
                $data->phone = $client->phonenumber;
                $data->address = $client->address;
                $data->city = $client->city;
                $data->state = $client->state;
                $data->zip = $client->zip;
                $data->country = $client->country;
                
                // Email validation status
                $data->emailValidaionStatus = 1; // Default to valid
                if ($client->email && (!filter_var($client->email, FILTER_VALIDATE_EMAIL) || strpos($client->email, '@') === false)) {
                    $data->emailValidaionStatus = 0; // Mark as invalid
                }
                
                // Phone number validation
                if ($client->phonenumber) {
                    $data->phonenumbertype = isset($client->phonenumber_type) ? $client->phonenumber_type : 'mobile';
                    $data->phoneNumberValid = 1; // Default to valid
                    if ($data->phonenumbertype == 'landline' || $data->phonenumbertype == 'invalid') {
                        $data->phoneNumberValid = 0; // Mark as invalid
                    }
                } else {
                    $data->phoneNumberValid = null;
                }
            } else {
                $data->error = 'Client not found';
            }
        } else {
            $data->error = 'Invalid relation type';
        }
        
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * DataTable server-side processing
     */
    public function table()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('ella_contractors', 'admin/tables/ella_appointments'));
    }

    /**
     * Update appointment status via AJAX
     */
    public function update_appointment_status()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $post_data = $this->input->post();
            $appointment_id = $this->input->post('appointment_id');
            $status = $this->input->post('status');
            
            // Validate appointment exists and user has permission
            $appointment = $this->appointments_model->get_appointment($appointment_id);
            if (!$appointment) {
                echo json_encode([
                    'success' => false,
                    'class' => 'danger',
                    'message' => 'Appointment not found!',
                ]);
                return;
            }
            
            // Check permissions
            $staff_id = get_staff_user_id();
            if (!has_permission('ella_contractor', '', 'edit')) {
                echo json_encode([
                    'success' => false,
                    'class' => 'danger',
                    'message' => 'Access denied!',
                ]);
                return;
            }
            
            // Update the appointment status
            $update_data = [
                'appointment_status' => $status
            ];
            
            // Also update legacy fields for backward compatibility
            if ($status === 'cancelled') {
                $update_data['cancelled'] = 1;
                $update_data['finished'] = 0;
                $update_data['approved'] = 0;
            } elseif ($status === 'complete') {
                $update_data['cancelled'] = 0;
                $update_data['finished'] = 1;
                $update_data['approved'] = 1;
            } else {
                $update_data['cancelled'] = 0;
                $update_data['finished'] = 0;
                $update_data['approved'] = 0;
            }
            
            $result = $this->appointments_model->update_appointment($appointment_id, $update_data);
            
            if ($result) {
                // Log the activity
                log_activity('Appointment Status Updated [ID: ' . $appointment_id . ', Status: ' . $status . ']');
                
                echo json_encode([
                    'success' => true,
                    'class' => 'success',
                    'message' => 'Appointment status updated successfully!',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'class' => 'danger',
                    'message' => 'Failed to update appointment status!',
                ]);
            }
        }
    }

    /**
     * Get appointments data for AJAX
     */
    public function get_appointments_ajax()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $appointments = $this->appointments_model->get_appointments();
        echo json_encode($appointments);
    }

    /**
     * Get appointment data for modal (AJAX)
     */
    public function get_appointment_data()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'No appointment ID provided'
            ]);
            return;
        }
        
        $appointment = $this->appointments_model->get_appointment($id);
        
        if ($appointment) {
            // Convert object to array
            $appointment_data = (array) $appointment;
            $appointment_data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
            $appointment_data['reminder_channel'] = $this->normalize_reminder_channel($appointment->reminder_channel ?? 'both');
            
            // Determine contact type and prepare contact data for dropdown
            if ($appointment->client_name) {
                $appointment_data['contact_type'] = 'client';
                $appointment_data['contact_display_name'] = $appointment->client_name;
                $appointment_data['contact_dropdown_value'] = 'client_' . $appointment->contact_id;
            } elseif ($appointment->lead_name) {
                $appointment_data['contact_type'] = 'lead';
                $appointment_data['contact_display_name'] = $appointment->lead_name;
                $appointment_data['contact_dropdown_value'] = 'lead_' . $appointment->contact_id;
            } else {
                $appointment_data['contact_type'] = '';
                $appointment_data['contact_display_name'] = '';
                $appointment_data['contact_dropdown_value'] = '';
            }
            
            echo json_encode([
                'success' => true,
                'data' => $appointment_data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
        }
    }

    /**
     * Save appointment via AJAX (for modal)
     */


    public function save_ajax()
    {
        if (!has_permission('ella_contractor', '', 'create') && !has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('start_time', 'Start Time', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('end_time', 'End Time', 'required');
        $this->form_validation->set_rules('contact_id', 'Lead/Client', 'required', array('required' => 'Please select a lead or client'));
        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }        
        // Process contact_id - handle client_/lead_ prefixes
        $contact_id = $this->input->post('contact_id');
        if ($contact_id) {
            if (strpos($contact_id, 'client_') === 0) {
                $contact_id = str_replace('client_', '', $contact_id);
            } elseif (strpos($contact_id, 'lead_') === 0) {
                $contact_id = str_replace('lead_', '', $contact_id);
            }
        }
        
        $data = [
            'subject' => $this->input->post('subject'),
            'date' => $this->input->post('start_date'),
            'start_hour' => $this->input->post('start_time'),
            'end_date' => $this->input->post('end_date'),
            'end_time' => $this->input->post('end_time'),
            'contact_id' => $contact_id ?: null,
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'notes' => $this->input->post('notes'),
            'type_id' => $this->input->post('type_id') ?: null,
            'appointment_status' => $this->input->post('status') ?: 'scheduled',
            'source' => 'ella_contractor',
            'send_reminder' => $this->input->post('send_reminder') ? 1 : 0,
            'reminder_48h' => $this->input->post('reminder_48h') ? 1 : 0,
            'reminder_same_day' => $this->input->post('reminder_same_day') ? 1 : 0,
            'staff_reminder_48h' => $this->input->post('staff_reminder_48h') ? 1 : 0,
            'staff_reminder_same_day' => $this->input->post('staff_reminder_same_day') ? 1 : 0,
            'reminder_channel' => $this->normalize_reminder_channel($this->input->post('reminder_channel'))
        ];
        
        $appointment_id = $this->input->post('appointment_id');
        
        try {
            if ($appointment_id) {
                // Update existing appointment
                if ($this->appointments_model->update_appointment($appointment_id, $data)) {
                    // Handle attendees
                    $this->handle_attendees($appointment_id);
                    // Handle notes for update
                    if (!empty($this->input->post('notes'))) {
                        $note_data = [
                            'description' => $this->input->post('notes'),
                            'rel_type' => 'appointment',
                            'rel_id' => $appointment_id
                        ];
                        $this->misc_model->add_note($note_data, 'appointment', $appointment_id);
                    }
                    // Handle file uploads for update
                    $this->handle_appointment_file_uploads($appointment_id);
                    
                    // Update reminder tracking record
                    $this->appointment_reminder_model->sync_from_appointment($appointment_id, $data);
                    
                    // Sync to calendars (Google and Outlook) if staff has connected
                    $this->sync_to_calendar($appointment_id, 'update', 'google');
                    $this->sync_to_calendar($appointment_id, 'update', 'outlook');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment updated successfully'
                    ]);
                } else {
                    die('inside else condition');
                    $db_error = $this->db->error();
                    $error_message = 'Failed to update appointment.';
                    if (!empty($db_error['message'])) {
                        $error_message .= ' Error: ' . $db_error['message'];
                        log_message('error', 'Appointment Update Error - Query: ' . $this->db->last_query() . ' | Error: ' . $db_error['message']);
                    }
                    echo json_encode([
                        'success' => false,
                        'message' => $error_message
                    ]);
                }
            } else {
                // Create new appointment
                $appointment_id = $this->appointments_model->create_appointment($data);
                if ($appointment_id) {
                    // Handle attendees
                    $this->handle_attendees($appointment_id);
                    // Handle notes for creation
                    if (!empty($this->input->post('notes'))) {
                        $note_data = [
                            'description' => $this->input->post('notes'),
                            'rel_type' => 'appointment',
                            'rel_id' => $appointment_id
                        ];
                        $this->misc_model->add_note($note_data, 'appointment', $appointment_id);
                    }
                    // Handle file uploads for creation
                    $this->handle_appointment_file_uploads($appointment_id);
                    
                    // Schedule reminders (emails & ICS files)
                    // if (!function_exists('ella_schedule_reminders')) {
                    //     require_once(module_dir_path('ella_contractors', 'helpers/ella_reminder_helper.php'));
                    // }
                    // ella_schedule_reminders($appointment_id, ['client_instant']);

                    // Create reminder tracking record
                    $this->appointment_reminder_model->sync_from_appointment($appointment_id, $data);
                    
                    // Sync to calendars (Google and Outlook) if staff has connected
                    $this->sync_to_calendar($appointment_id, 'create', 'google');
                    $this->sync_to_calendar($appointment_id, 'create', 'outlook');
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment created successfully',
                        'appointment_id' => $appointment_id
                    ]);
                } else {
                    $db_error = $this->db->error();
                    $error_message = 'Failed to create appointment.';
                    if (!empty($db_error['message'])) {
                        $error_message .= ' Error: ' . $db_error['message'];
                        log_message('error', 'Appointment Creation Error - Query: ' . $this->db->last_query() . ' | Error: ' . $db_error['message']);
                    }
                    echo json_encode([
                        'success' => false,
                        'message' => $error_message
                    ]);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete appointment via AJAX
     */
    public function delete_ajax()
    {
        if (!has_permission('ella_contractor', '', 'delete')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        // Get appointment before deleting to check for Google Calendar sync
        $appointment = $this->appointments_model->get_appointment($id);
        $staff_id = $appointment ? $appointment->created_by : null;
        
        // Sync delete to calendars (Google and Outlook) before deleting from database
        if ($appointment && $staff_id) {
            $this->sync_to_calendar($id, 'delete', 'google');
            $this->sync_to_calendar($id, 'delete', 'outlook');
        }
        
        if ($this->appointments_model->delete_appointment($id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete appointment'
            ]);
        }
    }

    /**
     * Bulk delete appointments via AJAX
     */
    public function bulk_delete()
    {
        if (!has_permission('ella_contractor', '', 'delete')) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }

        $ids = $this->input->post('ids');
        
        if (empty($ids) || !is_array($ids)) {
            echo json_encode([
                'success' => false,
                'message' => 'No appointments selected'
            ]);
            return;
        }

        $deleted_count = 0;
        $failed_count = 0;
        
        foreach ($ids as $id) {
            if ($this->appointments_model->delete_appointment($id)) {
                $deleted_count++;
            } else {
                $failed_count++;
            }
        }

        $total = count($ids);
        
        if ($deleted_count > 0) {
            $message = $deleted_count . ' appointment(s) deleted successfully';
            if ($failed_count > 0) {
                $message .= ' (' . $failed_count . ' failed)';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deleted_count,
                'failed_count' => $failed_count,
                'total' => $total
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete appointments'
            ]);
        }
    }

    /**
     * Handle attendees for appointment
     */
    private function handle_attendees($appointment_id)
    {
        // Get existing attendees for comparison (for calendar sync)
        $old_attendees = $this->appointments_model->get_appointment_attendees($appointment_id);
        
        $attendees = $this->input->post('attendees');
        if ($attendees && is_array($attendees)) {
            // Remove existing attendees
            $this->db->where('appointment_id', $appointment_id);
            $this->db->delete(db_prefix() . 'appointly_attendees');
            
            // Add new attendees
            foreach ($attendees as $staff_id) {
                $this->appointments_model->add_attendee($appointment_id, $staff_id);
            }
            
            // Get new attendees for comparison
            $new_attendees = $this->appointments_model->get_appointment_attendees($appointment_id);
            
            // Sync assignee changes to both Google Calendar and Outlook Calendar
            if (!empty($old_attendees) || !empty($new_attendees)) {
                $this->sync_assignee_change($appointment_id, $old_attendees, $new_attendees);
                $this->sync_outlook_assignee_change($appointment_id, $old_attendees, $new_attendees);
            }
        }
    }

    /**
     * AJAX endpoint to get appointment types for global appointment creation
     */
    public function get_types()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $types = $this->appointments_model->get_appointment_types();
        
        echo json_encode([
            'success' => true,
            'types' => $types
        ]);
    }

    /**
     * Calendar events feed for the logged-in staff member
     */
    public function calendar_events()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $startParam = $this->input->get('start');
        $endParam   = $this->input->get('end');

        $startDate = $this->normalize_calendar_date($startParam);
        $endDate   = $this->normalize_calendar_date($endParam);

        $appointments = $this->appointments_model->get_staff_calendar_appointments(
            get_staff_user_id(),
            $startDate,
            $endDate
        );

        $events = [];
        foreach ($appointments as $appointment) {
            $startDateTime = $this->combine_calendar_datetime($appointment['date'] ?? null, $appointment['start_hour'] ?? null);

            if (!$startDateTime) {
                continue;
            }

            $endDateValue = !empty($appointment['end_date']) ? $appointment['end_date'] : $appointment['date'];
            $endTimeValue = !empty($appointment['end_time']) ? $appointment['end_time'] : ($appointment['start_hour'] ?? null);

            $endDateTime = $this->combine_calendar_datetime($endDateValue, $endTimeValue);

            if (!$endDateTime) {
                $endDateTime = clone $startDateTime;
                $endDateTime->modify('+1 hour');
            } else {
                if (empty($appointment['end_time'])) {
                    $endDateTime->modify('+1 hour');
                }

                if ($endDateTime <= $startDateTime) {
                    $endDateTime = clone $startDateTime;
                    $endDateTime->modify('+1 hour');
                }
            }

            $status = !empty($appointment['appointment_status']) ? strtolower($appointment['appointment_status']) : 'scheduled';

            $events[] = [
                'id'        => (int) $appointment['id'],
                'title'     => $appointment['subject'],
                'start'     => $startDateTime->format(DateTime::ATOM),
                'end'       => $endDateTime->format(DateTime::ATOM),
                'url'       => admin_url('ella_contractors/appointments/view/' . $appointment['id']),
                'status'    => $status,
                'location'  => $appointment['address'] ?? '',
                'allDay'    => false,
                'className' => ['status-' . $status],
            ];
        }

        echo json_encode([
            'success'    => true,
            'data'       => $events,
            'csrf_token' => $this->security->get_csrf_hash(),
        ]);
    }


    /**
     * Send SMS to lead from appointment
     */
    public function send_sms()
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }

        // Temporarily disabled: SMS sending is turned off in QA mode.
        echo json_encode([
            'success' => false,
            'message' => 'SMS sending is temporarily disabled. Please use email instead.'
        ]);
        return;
        
        /*
        
        $response = array();
        
        // Get the SMS POST data
        if (isset($_POST['lead_id'])) {
            $lead_id = $_POST['lead_id'];
            $number = $_POST['contact_number'];
            $staff_id = $_POST['sender_id'];
            $sms_body = $_POST['sms_body'];
            $media_url = $_POST['media_url'];
            $ics_url = '';
            
            // Handle vCalendar attachment if provided
            if (!empty($_POST['vc_fromdate']) && !empty($_POST['vc_todate'])) {
                $lead = $this->leads_model->get($lead_id);
                $agent_name = get_staff_full_name($lead->assigned);
                $vc_fromdate = $_POST['vc_fromdate'];
                $vc_todate = $_POST['vc_todate'];
                
                $vc_summary = $_POST['vc_summary'];
                $vc_description = $_POST['vc_description'];
                $vc_location = $_POST['vc_location'];
                
                if(empty($vc_summary)){
                    $summary = "Your Call with ".$agent_name." from Ella's Bubbles";
                }else{
                    $summary = $vc_summary;
                }
                
                if(empty($vc_description)){
                    $description = "Ella's Bubbles Walk In Tubs is the Nation's Leader in Luxurious & Affordable Walk In Tubs since 2025.";
                }else{
                    $description = $vc_description;
                }
                
                if(empty($vc_location)){
                    $location = "Phone Call/Video Call";
                }else{
                    $location = $vc_location;
                }
                $ics_url = vcalendar_file_url($vc_fromdate,$vc_todate,$summary,$description,$location);
            }
            
            // Load leads model
            $this->load->model('leads_model');
            
            // Set TCPA to false for ella_contractors module
            $dnc_validation = false;
            $tcpa = false;
            
            if (!function_exists('ella_dispatch_sms')) {
                require_once(module_dir_path('ella_contractors', 'helpers/ella_reminder_helper.php'));
            }

            // Dispatch SMS via module helper (uses Telnyx configuration)
            $response = ella_dispatch_sms(
                $number,
                $sms_body,
                [
                    'lead_id'        => !empty($lead_id) ? (int) $lead_id : 0,
                    'staff_id'       => (int) $staff_id,
                    'appointment_id' => $this->input->post('appointment_id') ?: null,
                    'ics_url'        => $ics_url,
                    'media_url'      => $media_url,
                ]
            );
            
            // Update log activity hooks
            if ($response['success']) {
                if (!empty($lead_id)) {
                    log_staff_status_activity('Added SMS Activity from Appointment Lead# [' . $lead_id . ']');
                }
                
                // Log SMS sent activity for appointment timeline
                $appointment_id = $this->input->post('appointment_id');
                if ($appointment_id) {
                    $this->appointments_model->log_activity($appointment_id, 'sent', 'sms', '', ['phone_number' => $number, 'sms_content' => $sms_body, 'lead_id' => $lead_id]);
                }
            }
        } else {
            $response['message'] = 'Something went wrong!';
            $response['success'] = false;
        }
        
        // Return the json response
        echo json_encode($response, true);
        */
    }

    /**
     * Normalize reminder channel input to supported values.
     *
     * @param string|null $value
     * @return string
     */
    private function normalize_reminder_channel($value)
    {
        $allowed = ['sms', 'email', 'both'];
        $value = is_string($value) ? strtolower(trim($value)) : '';
        if (!in_array($value, $allowed, true)) {
            $value = 'both';
        }
        return $value;
    }

    /**
     * Normalize calendar date parameters to Y-m-d or return null on failure
     *
     * @param string|null $value
     * @return string|null
     */
    private function normalize_calendar_date($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return (new DateTime($value))->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Combine date and time into a DateTime instance (UTC unaffected)
     *
     * @param string|null $date
     * @param string|null $time
     * @return DateTime|null
     */
    private function combine_calendar_datetime($date, $time)
    {
        if (empty($date)) {
            return null;
        }

        $time = $time ?: '00:00:00';
        if (strlen($time) === 5) {
            $time .= ':00';
        }

        try {
            return new DateTime(trim($date . ' ' . $time));
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get SMS logs for appointment
     */
    public function get_sms_logs()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }
        
        $lead_id = $this->input->post('lead_id');
        $contact_number = $this->input->post('contact_number');
        
        // Load leads model
        $this->load->model('leads_model');
        
        // Get SMS logs using the same method as leads
        $sms_logs = $this->leads_model->get_lead_sms_logs($lead_id, 'lead_id');
        
        if ($sms_logs) {
            // Add time_ago for each log
            foreach ($sms_logs as &$log) {
                $log['time_ago'] = time_ago($log['date_created']);
            }
            echo json_encode(['success' => true, 'data' => $sms_logs]);
        } else {
            echo json_encode(['success' => true, 'data' => []]);
        }
    }
    
    /**
     * Get SMS template - matching leads functionality
     */
    public function get_template()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }
        
        $response = array();
        $template_id = $this->input->post('template_id');
        
        $templates = $this->db->select('*')
            ->from(db_prefix() . 'custom_templates')
            ->where('type', 'sms')
            ->where('id', $template_id)
            ->get()
            ->result();
            
        if (!empty($templates)) {
            $msg_body = $templates[0]->template_content;
            $response['message'] = $msg_body;
            $response['success'] = true;
        } else {
            $response['message'] = '';
            $response['success'] = false;
        }
        
        echo json_encode($response, true);
    }
    
    /**
     * Insert SMS template - matching leads functionality
     */
    public function insert_template()
    {
        if (!has_permission('ella_contractor', '', 'create')) {
            ajax_access_denied();
        }
        
        $response = array();
        $sms_body_textarea = $this->input->post('sms_body_textarea');
        $template_title = $this->input->post('template_title');
        
        $data = array(
            "staff_id" => get_staff_user_id(),
            "type" => "sms",
            "template_name" => $template_title,
            "template_content" => $sms_body_textarea,
            "media_url" => "",
            "last_seen" => ""
        );
        
        $this->leads_model->add_customTemplate($data);
        
        $templates = $this->db->select('*')
            ->from(db_prefix() . 'custom_templates')
            ->where('type', 'sms')
            ->get()
            ->result();
        
        $response['message'] = $templates;
        $response['success'] = true;
        
        echo json_encode($response, true);
    }
    
    
    public function upload_sms_media()
    {
        if (!has_permission('ella_contractor', '', 'create')) {
            ajax_access_denied();
        }
        
        // Use the existing upload_image controller
        $this->load->library('upload');
        
        $config['upload_path'] = './uploads/leads/';
        $config['allowed_types'] = 'png|jpg|jpeg|gif';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = TRUE;
        
        $this->upload->initialize($config);
        
        if ($this->upload->do_upload('media_image')) {
            $upload_data = $this->upload->data();
            $media_url = base_url('uploads/leads/' . $upload_data['file_name']);
            
            echo json_encode([
                'success' => true,
                'media_url' => $media_url,
                'file_name' => $upload_data['file_name']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $this->upload->display_errors()
            ]);
        }
    }

    /**
     * Send reminder to client via AJAX
     */
    public function send_reminder_ajax()
    {
        $appointment_id = $this->input->post('id');
        
        if (!$appointment_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
            return;
        }

        // Get appointment data
        $appointment = $this->appointments_model->get($appointment_id);
        
        if (!$appointment) {
            echo json_encode(['success' => false, 'message' => 'Appointment not found']);
            return;
        }

        // Check if appointment has email
        if (!$appointment->email) {
            echo json_encode(['success' => false, 'message' => 'No email address available for this appointment']);
            return;
        }

        // Prepare email data
        $email_data = [
            'to' => $appointment->email,
            'subject' => 'Appointment Reminder: ' . $appointment->subject,
            'message' => $this->build_reminder_message($appointment),
            'from_name' => get_option('companyname'),
            'from_email' => get_option('company_email')
        ];

        // Send email using CRM's email system
        $this->load->library('email');
        $this->email->clear();
        $this->email->from($email_data['from_email'], $email_data['from_name']);
        $this->email->to($email_data['to']);
        $this->email->subject($email_data['subject']);
        $this->email->message($email_data['message']);

        if ($this->email->send()) {
            // Log email sent activity
            $this->appointments_model->log_activity($appointment_id, 'sent', 'email', '', ['email_address' => $appointment->email, 'subject' => $email_data['subject'], 'email_type' => 'reminder']);
            echo json_encode(['success' => true, 'message' => 'Reminder sent successfully to ' . $appointment->email]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send reminder: ' . $this->email->print_debugger()]);
        }
    }

    /**
     * Log email button click (AJAX)
     */
    public function log_email_click()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $email_address = $this->input->post('email_address');
        
        if (!$appointment_id || !$email_address) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and email address are required'
            ]);
            return;
        }
        
        $result = $this->appointments_model->log_activity($appointment_id, 'clicked', 'email', '', ['email_address' => $email_address]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Email click logged successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to log email click'
            ]);
        }
    }
    
    /**
     * Build reminder message
     */
    private function build_reminder_message($appointment)
    {
        $message = "Dear " . $appointment->name . ",\n\n";
        $message .= "This is a reminder about your upcoming appointment:\n\n";
        $message .= "Subject: " . $appointment->subject . "\n";
        $message .= "Date: " . _d($appointment->date) . "\n";
        $message .= "Time: " . date("H:i A", strtotime($appointment->start_hour)) . "\n";
        
        if ($appointment->address) {
            $message .= "Address: " . $appointment->address . "\n";
        }
        
        if ($appointment->description) {
            $message .= "\nDescription: " . $appointment->description . "\n";
        }
        
        $message .= "\nPlease contact us if you need to reschedule or have any questions.\n\n";
        $message .= "Best regards,\n";
        $message .= get_option('companyname');
        
        return $message;
    }

    /**
     * Add or update note for appointment
     * If note_id is provided, update existing note
     * If no note_id, create new note
     */
    public function add_note($rel_id, $note_id = null)
    {
        // Set JSON header immediately
        header('Content-Type: application/json');
        
        if (!is_staff_member()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Handle the note description
            $data['description'] = isset($data['appointment_note_description']) 
                ? $data['appointment_note_description'] 
                : (isset($data['description']) ? $data['description'] : '');
            
            $data['description'] = replace_name_shortcodes($rel_id, $data['description']);

            if (isset($data['appointment_note_description'])) {
                unset($data['appointment_note_description']);
            }

            // Remove unnecessary fields
            unset($data['contacted_indicator'], $data['custom_contact_date'], $data['date_contacted']);

            if ($note_id) {
                // Update existing note
                $note = $this->db->get_where(db_prefix() . 'notes', ['id' => $note_id])->row();
                if (!$note || ($note->addedfrom != get_staff_user_id() && !is_admin())) {
                    echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this note']);
                    exit;
                }

                $update_data = [
                    'description' => nl2br($data['description'])
                ];

                $success = $this->misc_model->edit_note($update_data, $note_id);
                
                if ($success) {
                    // Log activity
                    $this->appointments_model->add_activity_log(
                        $rel_id, 
                        'NOTES', 
                        'updated', 
                        [
                            'note_id' => $note_id,
                            'content_preview' => substr(strip_tags($data['description']), 0, 100)
                        ]
                    );
                    echo json_encode(['success' => true, 'message' => 'Note updated successfully', 'note_id' => $note_id]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update note']);
                }
            } else {
                // Create new note
                $note_id = $this->misc_model->add_note($data, 'appointment', $rel_id);
                
                if ($note_id) {
                    // Log activity
                    $this->appointments_model->add_activity_log(
                        $rel_id, 
                        'NOTES', 
                        'created', 
                        [
                            'note_id' => $note_id,
                            'content_preview' => substr(strip_tags($data['description']), 0, 100)
                        ]
                    );
                    echo json_encode(['success' => true, 'message' => 'Note added successfully', 'note_id' => $note_id]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add note']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No data received']);
        }
        exit; // Ensure clean JSON response
    }

    /**
     * Create initial note with appointment timestamp
     * This is called when an appointment is created
     */
    public function create_initial_note($appointment_id)
    {
        // Check if notes already exist for this appointment
        $existing_notes = $this->misc_model->get_notes($appointment_id, 'appointment');
        if (!empty($existing_notes)) {
            return false; // Notes already exist
        }

        // Get appointment data
        $appointment = $this->appointments_model->get_appointment($appointment_id);
        if (!$appointment) {
            return false;
        }

        // Create initial note with appointment timestamp
        $note_data = [
            'description' => 'Appointment created on ' . _d($appointment->date) . ' at ' . date('H:i A', strtotime($appointment->start_hour)),
            'dateadded' => $appointment->date . ' ' . $appointment->start_hour,
            'addedfrom' => get_staff_user_id()
        ];

        $note_id = $this->misc_model->add_note($note_data, 'appointment', $appointment_id);
        return $note_id;
    }

    /**
     * Get notes for appointment
     */
    public function get_notes($appointment_id)
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        // Fetch only real notes from tblnotes table, NOT timeline activities
        $notes = $this->misc_model->get_notes($appointment_id, 'appointment');
        
        // Filter out any notes that look like timeline activities
        $filtered_notes = [];
        if ($notes) {
            foreach ($notes as $note) {
                // Skip notes that are actually timeline activity descriptions
                $description = strip_tags($note['description']);
                $timeline_keywords = ['APPOINTMENT CREATED', 'NOTE ADDED', 'NOTE UPDATED', 'APPOINTMENT UPDATED', 'APPOINTMENT STATUS CHANGED'];
                
                $is_timeline_activity = false;
                foreach ($timeline_keywords as $keyword) {
                    if (stripos($description, $keyword) !== false && strlen($description) < 50) {
                        $is_timeline_activity = true;
                        break;
                    }
                }
                
                if (!$is_timeline_activity) {
                    $filtered_notes[] = $note;
                }
            }
        }
        
        if ($filtered_notes) {
            // Add time_ago and profile image for each note
            foreach ($filtered_notes as &$note) {
                $note['time_ago'] = time_ago($note['dateadded']);
                
                // Add profile image path (same as timeline)
                if (isset($note['staffid']) && $note['staffid'] > 0) {
                    $note['profile_image'] = staff_profile_image($note['staffid'], ['staff-profile-xs-image']);
                    // Extract just the src URL from the HTML
                    if (preg_match('/src="([^"]+)"/', $note['profile_image'], $matches)) {
                        $note['profile_image'] = $matches[1];
                    }
                } else {
                    $note['profile_image'] = admin_url('assets/images/user-placeholder.jpg');
                }
            }
            echo json_encode(['success' => true, 'data' => $filtered_notes]);
        } else {
            echo json_encode(['success' => true, 'data' => []]);
        }
    }

    /**
     * Delete note
     */
    public function delete_note($note_id)
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        // Get note details before deleting for logging
        $note = $this->db->get_where(db_prefix() . 'notes', ['id' => $note_id])->row();
        
        $success = $this->misc_model->delete_note($note_id);
        
        if ($success) {
            // Log note deletion using unified method
            if ($note && $note->rel_type === 'appointment') {
                $this->appointments_model->add_activity_log(
                    $note->rel_id, 
                    'NOTES', 
                    'deleted', 
                    [
                        'note_id' => $note_id,
                        'content_preview' => substr(strip_tags($note->description), 0, 100),
                        'deleted_by' => get_staff_user_id()
                    ]
                );
            }
            echo json_encode(['success' => true, 'message' => 'Note deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete note or you do not have permission']);
        }
    }


    /**
     * Get appointment attachments for AJAX
     * @param int $appointment_id
     */
    public function get_appointment_attachments($appointment_id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/ella_media_model');
        $attachments = $this->ella_media_model->get_appointment_attachments($appointment_id);
        
        // Add full file paths to each attachment
        foreach ($attachments as &$attachment) {
            $attachment['file_path'] = 'uploads/ella_appointments/' . $appointment_id . '/' . $attachment['file_name'];
            $attachment['download_url'] = admin_url('ella_contractors/appointments/download_attachment/' . $attachment['id']);
            
            // Direct public URL for external viewers (Microsoft/Google)
            $direct_url = site_url('uploads/ella_appointments/' . $appointment_id . '/' . $attachment['file_name']);
            // Force HTTPS for external viewer compatibility
            $attachment['public_url'] = str_replace('http://', 'https://', $direct_url);
        }
        
        echo json_encode([
            'success' => true,
            'attachments' => $attachments
        ]);
    }

    /**
     * Upload attachment via Dropzone (AJAX endpoint)
     * @param int $appointment_id
     */
    public function upload_attachment($appointment_id)
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }

        // Validate appointment exists
        $appointment = $this->appointments_model->get_appointment($appointment_id);
        if (!$appointment) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
            return;
        }

        // Load models
        $this->load->model('ella_contractors/ella_media_model');
        
        // Debug: Log received files
        log_message('debug', 'Upload Attachment - FILES received: ' . json_encode(array_keys($_FILES)));
        
        // Handle multiple file uploads - check both 'file' and 'file[]' keys
        $files_key = null;
        if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
            $files_key = 'file';
        } elseif (isset($_FILES['file_']) && !empty($_FILES['file_']['name'])) {
            // Some browsers/frameworks use 'file_' when 'file[]' is used
            $files_key = 'file_';
        }
        
        if ($files_key) {
            // Check if it's a single file or multiple files
            if (!is_array($_FILES[$files_key]['name'])) {
                // Single file - convert to array format for uniform processing
                $_FILES[$files_key]['name'] = [$_FILES[$files_key]['name']];
                $_FILES[$files_key]['type'] = [$_FILES[$files_key]['type']];
                $_FILES[$files_key]['tmp_name'] = [$_FILES[$files_key]['tmp_name']];
                $_FILES[$files_key]['error'] = [$_FILES[$files_key]['error']];
                $_FILES[$files_key]['size'] = [$_FILES[$files_key]['size']];
            }
            
            log_message('debug', 'Upload Attachment - Processing ' . count($_FILES[$files_key]['name']) . ' file(s)');

            $uploaded_files = [];
            $errors = [];

            // Process each file
            for ($i = 0; $i < count($_FILES[$files_key]['name']); $i++) {
                // Skip if no file or error uploading
                if (empty($_FILES[$files_key]['tmp_name'][$i]) || $_FILES[$files_key]['error'][$i] !== UPLOAD_ERR_OK) {
                    $errors[] = 'File upload error for: ' . ($_FILES[$files_key]['name'][$i] ?? 'unknown');
                    continue;
                }

                $file_data = [
                    'name' => $_FILES[$files_key]['name'][$i],
                    'type' => $_FILES[$files_key]['type'][$i],
                    'tmp_name' => $_FILES[$files_key]['tmp_name'][$i],
                    'error' => $_FILES[$files_key]['error'][$i],
                    'size' => $_FILES[$files_key]['size'][$i]
                ];

                // Use existing handle_appointment_file_upload method
                $result = $this->handle_appointment_file_upload($file_data, $appointment_id);
                
                if ($result['success']) {
                    $uploaded_files[] = $result;
                    
                    // Log attachment upload activity using unified method
                    $this->appointments_model->add_activity_log(
                        $appointment_id,
                        'ATTACHMENTS',
                        'uploaded',
                        [
                            'filename' => $file_data['name'],
                            'file_size' => $this->format_bytes($file_data['size']),
                            'file_type' => strtolower(pathinfo($file_data['name'], PATHINFO_EXTENSION)),
                            'media_id' => $result['media_id'] ?? null
                        ]
                    );
                } else {
                    $errors[] = $result['message'];
                }
            }

            // Return response
            if (count($uploaded_files) > 0) {
                $message = count($uploaded_files) . ' file(s) uploaded successfully';
                if (count($errors) > 0) {
                    $message .= ' (' . count($errors) . ' failed)';
                }
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'uploaded' => $uploaded_files,
                    'errors' => $errors
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to upload files. ' . (count($errors) > 0 ? implode(', ', $errors) : 'Unknown error'),
                    'errors' => $errors
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No files received'
            ]);
        }
    }

    /**
     * Delete appointment attachment
     * @param int $attachment_id
     */
    public function delete_appointment_attachment($attachment_id)
    {
        if (!has_permission('ella_contractor', '', 'delete')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/ella_media_model');
        
        // Get attachment info before deleting
        $attachment = $this->ella_media_model->get_file($attachment_id);
        
        if (!$attachment || $attachment->rel_type !== 'attachment') {
            echo json_encode([
                'success' => false,
                'message' => 'Attachment not found'
            ]);
            return;
        }
        
        // Delete from database
        $success = $this->ella_media_model->delete_appointment_attachment($attachment_id);
        
        if ($success) {
            // Delete physical file
            $file_path = FCPATH . 'uploads/ella_appointments/' . $attachment->rel_id . '/' . $attachment->file_name;
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            
            // Log attachment deletion activity using unified method
            $this->appointments_model->add_activity_log(
                $attachment->rel_id,
                'ATTACHMENTS',
                'deleted',
                [
                    'filename' => $attachment->original_name,
                    'file_type' => strtolower(pathinfo($attachment->original_name, PATHINFO_EXTENSION)),
                    'attachment_id' => $attachment_id
                ]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete attachment'
            ]);
        }
    }

    /**
     * Preview appointment attachment (PDF/PPT/PPTX)
     * Same logic as presentations module - converts PPT/PPTX to PDF
     * @param int $attachment_id
     */
    public function preview_attachment($attachment_id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contractors/ella_media_model');
        $attachment = $this->ella_media_model->get_file($attachment_id);
        
        if (!$attachment || $attachment->rel_type !== 'appointment') {
            show_404();
        }

        // Get file path - construct from appointment ID and file name
        $file_path = FCPATH . 'uploads/ella_appointments/' . $attachment->rel_id . '/' . $attachment->file_name;
        
        if (!file_exists($file_path)) {
            show_404();
        }

        $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
        
        // If it's a PDF, serve it directly for inline viewing
        if ($ext === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $attachment->original_name . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
        
        // For PPT/PPTX files, convert to PDF for preview (same as presentations module)
        if (in_array($ext, ['ppt', 'pptx'])) {
            $this->convert_appointment_ppt_to_pdf($attachment, $file_path);
        } else {
            show_404();
        }
    }
    
    /**
     * Convert PPT/PPTX to PDF for preview
     * Reuses the same logic as presentations module (Ella_contractors.php)
     */
    private function convert_appointment_ppt_to_pdf($attachment, $original_path)
    {
        $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
        
        // Create a cache directory for converted PDFs
        $cache_dir = FCPATH . 'uploads/ella_appointments/cache/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        // Generate cache filename
        $cache_filename = 'preview_' . $attachment->id . '_' . md5($attachment->file_name . $attachment->date_uploaded) . '.pdf';
        $cache_path = $cache_dir . $cache_filename;
        
        // Check if cached PDF exists and is newer than original file
        if (file_exists($cache_path) && filemtime($cache_path) > filemtime($original_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . pathinfo($attachment->original_name, PATHINFO_FILENAME) . '.pdf"');
            readfile($cache_path);
            exit;
        }
        
        // Try to convert using LibreOffice (if available)
        if ($this->convert_with_libreoffice($original_path, $cache_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . pathinfo($attachment->original_name, PATHINFO_FILENAME) . '.pdf"');
            readfile($cache_path);
            exit;
        }
        
        // Try alternative conversion methods (creates fallback PDF)
        if ($this->convert_with_alternative_method($original_path, $cache_path, $ext, $attachment)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . pathinfo($attachment->original_name, PATHINFO_FILENAME) . '.pdf"');
            readfile($cache_path);
            exit;
        }
        
        // Fallback: Show error message
        $this->show_appointment_conversion_error($attachment);
    }

    /**
     * Convert file using LibreOffice (same as presentations module)
     */
    private function convert_with_libreoffice($input_path, $output_path)
    {
        // Check if LibreOffice is available
        $libreoffice_path = $this->find_libreoffice();
        if (!$libreoffice_path) {
            return false;
        }
        
        // Create output directory
        $output_dir = dirname($output_path);
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0755, true);
        }
        
        // Convert using LibreOffice
        $command = escapeshellarg($libreoffice_path) . 
                  ' --headless --convert-to pdf --outdir ' . escapeshellarg($output_dir) . 
                  ' ' . escapeshellarg($input_path) . ' 2>&1';
        
        $output = [];
        $return_code = 0;
        exec($command, $output, $return_code);
        
        // Check if conversion was successful
        if ($return_code === 0 && file_exists($output_path)) {
            return true;
        }
        
        log_message('error', 'LibreOffice conversion failed for appointment attachment: ' . implode("\n", $output));
        return false;
    }

    /**
     * Find LibreOffice executable (same as presentations module)
     */
    private function find_libreoffice()
    {
        $possible_paths = [
            '/usr/bin/libreoffice',
            '/usr/local/bin/libreoffice',
            '/opt/libreoffice/program/soffice',
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
            'libreoffice', // Try PATH
        ];
        
        foreach ($possible_paths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }
        
        return false;
    }

    /**
     * Alternative conversion method - creates fallback PDF (same as presentations module)
     */
    private function convert_with_alternative_method($input_path, $output_path, $ext, $attachment)
    {
        // Create a simple fallback PDF with file information
        $this->create_fallback_pdf($input_path, $output_path, $ext, $attachment);
        return true;
    }

    /**
     * Create fallback PDF when LibreOffice is not available (same as presentations module)
     */
    private function create_fallback_pdf($input_path, $output_path, $ext, $attachment)
    {
        $filename = pathinfo($input_path, PATHINFO_FILENAME);
        $file_size = file_exists($input_path) ? filesize($input_path) : 0;
        $file_size_formatted = $this->format_bytes($file_size);
        
        // Create a simple text-based PDF
        $pdf_content = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 350
>>
stream
BT
/F1 16 Tf
72 720 Td
(PowerPoint Preview: " . $filename . ") Tj
0 -30 Td
/F1 12 Tf
(File Type: " . strtoupper($ext) . ") Tj
0 -20 Td
(File Size: " . $file_size_formatted . ") Tj
0 -20 Td
(Uploaded: " . date('M d, Y', strtotime($attachment->date_uploaded)) . ") Tj
0 -40 Td
(This is a preview. LibreOffice conversion not available.) Tj
0 -20 Td
(Download the original file from the CRM) Tj
0 -20 Td
(to view the full presentation with animations.) Tj
ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000274 00000 n 
0000000675 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
775
%%EOF";

        file_put_contents($output_path, $pdf_content);
    }

    /**
     * Format bytes to human readable format (same as presentations module)
     */
    private function format_bytes($bytes, $decimals = 2)
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $dm = $decimals < 0 ? 0 : $decimals;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }

    /**
     * Show conversion error (same as presentations module)
     */
    private function show_appointment_conversion_error($attachment)
    {
        $ext = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
        $download_url = admin_url('ella_contractors/appointments/download_attachment/' . $attachment->id);
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Preview Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .error-container { text-align: center; max-width: 500px; margin: 0 auto; }
        .error-icon { font-size: 48px; color: #e74c3c; margin-bottom: 20px; }
        .error-title { font-size: 24px; color: #2c3e50; margin-bottom: 15px; }
        .error-message { color: #7f8c8d; margin-bottom: 20px; }
        .download-btn { 
            background: #3498db; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <div class="error-title">Preview Not Available</div>
        <div class="error-message">
            Unable to convert ' . strtoupper($ext) . ' file to PDF for preview.<br>
            This may be due to server configuration or missing conversion tools.
        </div>
        <a href="' . $download_url . '" class="download-btn">
            📥 Download Original File
        </a>
    </div>
</body>
</html>';
        exit;
    }

    /**
     * Download appointment attachment
     * @param int $attachment_id
     */
    public function download_attachment($attachment_id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contractors/ella_media_model');
        $attachment = $this->ella_media_model->get_file($attachment_id);
        
        if (!$attachment || $attachment->rel_type !== 'attachment') {
            show_404();
        }

        // Get file path - construct from appointment ID and file name
        $file_path = FCPATH . 'uploads/ella_appointments/' . $attachment->rel_id . '/' . $attachment->file_name;
        
        if (!file_exists($file_path)) {
            show_404();
        }

        // Force download
        $this->load->helper('download');
        force_download($attachment->original_name, file_get_contents($file_path));
    }

    /**
     * Handle appointment file uploads (called from save_ajax)
     * @param int $appointment_id
     */
    private function handle_appointment_file_uploads($appointment_id)
    {
        // Check if files were uploaded
        if (isset($_FILES['appointment_files']) && !empty($_FILES['appointment_files']['name'][0])) {
            $this->load->model('ella_contractors/ella_media_model');
            $files = $_FILES['appointment_files'];
            $file_count = count($files['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_data = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    $this->handle_appointment_file_upload($file_data, $appointment_id);
                }
            }
        }
    }
    
    /**
     * Handle individual file upload for appointment
     * @param array $file_data
     * @param int $appointment_id
     * @return array
     */
    private function handle_appointment_file_upload($file_data, $appointment_id)
    {
        // Validate file type
        $allowed_types = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        if (!in_array($file_data['type'], $allowed_types)) {
            return [
                'success' => false,
                'message' => 'Invalid file type: ' . $file_data['name']
            ];
        }

        // Validate file size (max 50MB)
        $max_size = 50 * 1024 * 1024; // 50MB
        if ($file_data['size'] > $max_size) {
            return [
                'success' => false,
                'message' => 'File too large: ' . $file_data['name']
            ];
        }

        // Create upload directory
        $upload_path = FCPATH . 'uploads/ella_appointments/' . $appointment_id . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($file_data['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $file_path = $upload_path . $unique_filename;

        // Move uploaded file
        if (move_uploaded_file($file_data['tmp_name'], $file_path)) {
            // Save to database - using 'attachment' rel_type (presentations are separate)
            $media_data = [
                'rel_type' => 'attachment',
                'rel_id' => $appointment_id,
                'org_id' => null,
                'file_name' => $unique_filename,
                'original_name' => $file_data['name'],
                'file_type' => $file_data['type'],
                'file_size' => $file_data['size'],
                'description' => 'Appointment attachment',
                'date_uploaded' => date('Y-m-d H:i:s')
            ];

            $media_id = $this->ella_media_model->add_media($media_data);

            if ($media_id) {
                return [
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'media_id' => $media_id
                ];
            } else {
                // Remove file if database insert failed
                unlink($file_path);
                return [
                    'success' => false,
                    'message' => 'Database error for file: ' . $file_data['name']
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Failed to move file: ' . $file_data['name']
            ];
        }
    }
    
    /**
     * Get timeline for appointment (AJAX)
     */
    public function get_timeline($id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }
        
        // Get appointment data
        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            echo '<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Appointment not found.</p></div>';
            return;
        }
        
        // Get timeline activities using unified method
        $timeline_activities = $this->appointments_model->get_timeline($id);
        
        // Load language file for timeline
        $this->lang->load('ella_contractors/ella_contractors', 'english');
        
        // Load helper function for timeline formatting
        $this->load->helper('timeline');
        
        // Load the timeline view
        $this->load->view('admin/appointments/timeline', [
            'appointment' => $appointment,
            'timeline_activities' => $timeline_activities
        ]);
    }

    /**
     * Get attendees for appointment (AJAX) - for display refresh
     */
    public function get_attendees($appointment_id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $attendees = $this->appointments_model->get_appointment_attendees($appointment_id);
        
        echo json_encode([
            'success' => true,
            'data' => $attendees
        ]);
    }

    /**
     * Get staff members for attendees dropdown (AJAX)
     */
    public function get_staff()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }
        $this->db->select('staffid, firstname, lastname, email');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where('active', 1);
        $this->db->order_by('firstname', 'ASC');
        
        $staff = $this->db->get()->result_array();
        
        echo json_encode([
            'success' => true,
            'data' => $staff
        ]);
    }
    
    
    
    /**
     * Get timeline action label based on description key
     * Helper method for timeline formatting
     * 
     * @param string $description_key The description key
     * @return string                Formatted action label
     */
    public function get_timeline_action_label($description_key)
    {
        $action_map = [
            'appointment_created' => _l('timeline_action_created'),
            'appointment_updated' => _l('timeline_action_updated'),
            'appointment_status_changed' => _l('timeline_action_status_changed'),
            'measurement_created' => _l('timeline_action_measurement_added'),
            'measurement_updated' => _l('timeline_action_measurement_updated'),
            'measurement_deleted' => _l('timeline_action_measurement_removed'),
            'note_created' => _l('timeline_action_note_added'),
            'note_updated' => _l('timeline_action_note_updated'),
            'note_deleted' => _l('timeline_action_note_removed'),
            'attachments_uploaded' => _l('timeline_action_attachment_uploaded'),
            'attachments_deleted' => _l('timeline_action_attachment_removed'),
            'proposal_created' => _l('timeline_action_proposal_created'),
            'proposal_updated' => _l('timeline_action_proposal_updated'),
            'proposal_deleted' => _l('timeline_action_proposal_deleted'),
            'estimates_created' => _l('timeline_action_estimates_created'),
            'estimates_updated' => _l('timeline_action_estimates_updated'),
            'estimates_deleted' => _l('timeline_action_estimates_deleted'),
            'process_completed' => _l('timeline_action_process_completed'),
            'process_failed' => _l('timeline_action_process_failed'),
            'appointment_deleted' => _l('timeline_action_deleted')
        ];
        
        // Return mapped action or fallback to formatted description key
        if (isset($action_map[$description_key])) {
            return $action_map[$description_key];
        }
        
        // Fallback: convert description_key to readable format
        $parts = explode('_', $description_key);
        return strtoupper(implode(' ', $parts));
    }
    
    /**
     * Log scheduled process (AJAX)
     */
    public function log_scheduled_process()
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $process = $this->input->post('process');
        $status = $this->input->post('status', true) ?: 'completed';
        
        if (!$appointment_id || !$process) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and process are required'
            ]);
            return;
        }
        
        $result = $this->appointments_model->log_scheduled_process($appointment_id, $process, $status);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Process logged successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to log process'
            ]);
        }
    }
    
    /**
     * Update reminder setting (AJAX)
     * Updates send_reminder or reminder_48h fields for an appointment
     */
    public function update_reminder_setting()
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $field = $this->input->post('field'); // 'send_reminder', 'reminder_48h', 'reminder_same_day', 'staff_reminder_48h', 'staff_reminder_same_day'
        $value = $this->input->post('value'); // 0 or 1
        
        // Validate inputs
        if (!$appointment_id || !$field) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and field are required'
            ]);
            return;
        }
        
        // Validate field name for security - allow all reminder fields
        $allowed_fields = [
            'send_reminder',
            'reminder_48h',
            'reminder_same_day',
            'staff_reminder_48h',
            'staff_reminder_same_day'
        ];
        
        if (!in_array($field, $allowed_fields)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid field name'
            ]);
            return;
        }
        
        // Validate appointment exists
        $appointment = $this->appointments_model->get_appointment($appointment_id);
        if (!$appointment) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
            return;
        }
        
        // Update the field
        $update_data = [$field => $value ? 1 : 0];
        $result = $this->appointments_model->update_appointment($appointment_id, $update_data);
        
        if ($result) {
            // Log activity
            $reminder_type_map = [
                'send_reminder' => 'Instant reminder',
                'reminder_48h' => '48-hour reminder (Client)',
                'reminder_same_day' => 'Same day reminder (Client)',
                'staff_reminder_48h' => '48-hour reminder (Staff)',
                'staff_reminder_same_day' => 'Same day reminder (Staff)'
            ];
            $reminder_type = isset($reminder_type_map[$field]) ? $reminder_type_map[$field] : $field;
            $action = $value ? 'enabled' : 'disabled';
            
            $this->appointments_model->add_activity_log(
                $appointment_id,
                'REMINDER_SETTING',
                $action,
                [
                    'reminder_type' => $reminder_type,
                    'field' => $field,
                    'value' => $value
                ]
            );
            
            // Also update the reminder tracking record
            $this->appointment_reminder_model->sync_from_appointment($appointment_id, $update_data);
            
            echo json_encode([
                'success' => true,
                'message' => 'Reminder setting updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update reminder setting'
            ]);
        }
    }
    
    /**
     * Get attached presentations for an appointment (AJAX)
     */
    public function get_attached_presentations()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->get('appointment_id');
        
        if (!$appointment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID is required'
            ]);
            return;
        }
        
        // Query presentations attached to this appointment
        $this->db->select('
            media.id,
            media.file_name,
            media.original_name,
            media.file_type,
            media.file_size,
            media.date_uploaded,
            pivot.attached_at,
            pivot.attached_by
        ');
        $this->db->from(db_prefix() . 'ella_appointment_presentations as pivot');
        $this->db->join(db_prefix() . 'ella_contractor_media as media', 'media.id = pivot.presentation_id');
        $this->db->where('pivot.appointment_id', $appointment_id);
        $this->db->where('media.rel_type', 'presentation');
        $this->db->order_by('pivot.attached_at', 'DESC');
        
        $presentations = $this->db->get()->result_array();

        if (!empty($presentations)) {
            foreach ($presentations as &$presentation) {
                $public_url = site_url('uploads/ella_presentations/' . $presentation['file_name']);
                $presentation['public_url'] = str_replace('http://', 'https://', $public_url);
            }
            unset($presentation);
        }
        
        echo json_encode([
            'success' => true,
            'data' => $presentations
        ]);
    }
    
    /**
     * Attach presentation to appointment (AJAX)
     */
    public function attach_presentation()
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $presentation_id = $this->input->post('presentation_id');
        
        // Validate inputs
        if (!$appointment_id || !$presentation_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and Presentation ID are required'
            ]);
            return;
        }
        
        // Validate appointment exists
        $appointment = $this->appointments_model->get_appointment($appointment_id);
        if (!$appointment) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
            return;
        }
        
        // Validate presentation exists
        $this->load->model('ella_contractors/ella_media_model');
        $presentation = $this->ella_media_model->get_file($presentation_id);
        if (!$presentation || $presentation->rel_type !== 'presentation') {
            echo json_encode([
                'success' => false,
                'message' => 'Presentation not found'
            ]);
            return;
        }
        
        // Check if already attached (UNIQUE constraint will prevent duplicates, but check anyway)
        $this->db->where('appointment_id', $appointment_id);
        $this->db->where('presentation_id', $presentation_id);
        $existing = $this->db->get(db_prefix() . 'ella_appointment_presentations')->row();
        
        if ($existing) {
            echo json_encode([
                'success' => false,
                'message' => 'This presentation is already attached to this appointment'
            ]);
            return;
        }
        
        // Create link in pivot table
        $data = [
            'appointment_id' => $appointment_id,
            'presentation_id' => $presentation_id,
            'attached_by' => get_staff_user_id(),
            'attached_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert(db_prefix() . 'ella_appointment_presentations', $data);
        
        if ($this->db->affected_rows() > 0) {
            // Log activity
            $this->appointments_model->add_activity_log(
                $appointment_id,
                'PRESENTATION',
                'attached',
                [
                    'presentation_id' => $presentation_id,
                    'presentation_name' => $presentation->original_name,
                    'attached_by' => get_staff_user_id()
                ]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Presentation attached successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to attach presentation'
            ]);
        }
    }
    
    /**
     * Detach presentation from appointment (AJAX)
     */
    public function detach_presentation()
    {
        if (!has_permission('ella_contractor', '', 'delete')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $presentation_id = $this->input->post('presentation_id');
        
        // Validate inputs
        if (!$appointment_id || !$presentation_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and Presentation ID are required'
            ]);
            return;
        }
        
        // Get presentation info before deleting for logging
        $this->load->model('ella_contractors/ella_media_model');
        $presentation = $this->ella_media_model->get_file($presentation_id);
        
        // Delete the link
        $this->db->where('appointment_id', $appointment_id);
        $this->db->where('presentation_id', $presentation_id);
        $this->db->delete(db_prefix() . 'ella_appointment_presentations');
        
        if ($this->db->affected_rows() > 0) {
            // Log activity
            $this->appointments_model->add_activity_log(
                $appointment_id,
                'PRESENTATION',
                'detached',
                [
                    'presentation_id' => $presentation_id,
                    'presentation_name' => $presentation ? $presentation->original_name : 'Unknown',
                    'detached_by' => get_staff_user_id()
                ]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Presentation removed successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to remove presentation or it was not attached'
            ]);
        }
    }

    /**
     * Download ICS calendar file for appointment
     * Allows staff to download calendar invitations for client or themselves
     * 
     * @param int $appointment_id Appointment ID
     * @param string $type 'client' or 'staff'
     */
    public function download_ics($appointment_id, $type = 'client')
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        // Validate type parameter
        if (!in_array($type, ['client', 'staff'])) {
            show_404();
        }
        
        // Load helper
        if (!function_exists('ella_generate_ics')) {
            require_once(module_dir_path('ella_contractors', 'helpers/ella_reminder_helper.php'));
        }
        
        // Generate ICS file
        $ics_file = ella_generate_ics($appointment_id, $type);
        
        if ($ics_file && file_exists($ics_file)) {
            // Force download
            $this->load->helper('download');
            $filename = $type . '_appointment_' . $appointment_id . '.ics';
            force_download($filename, file_get_contents($ics_file));
        } else {
            // Failed to generate ICS file
            set_alert('danger', 'Failed to generate calendar file. Please try again.');
            redirect(admin_url('ella_contractors/appointments/view/' . $appointment_id));
        }
    }

    /**
     * Check tutorial status for current user
     * Returns whether tutorial should be shown
     * 
     * @return json
     */
    public function check_tutorial_status()
    {
        if (!is_staff_logged_in()) {
            echo json_encode(['show_tutorial' => false]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Check user meta for tutorial dismissal
        if (!function_exists('get_meta')) {
            $this->load->helper('user_meta');
        }
        
        $tutorial_dismissed = get_meta('staff', $staff_id, 'ella_contractors_tutorial_dismissed');
        
        $show_tutorial = empty($tutorial_dismissed) || $tutorial_dismissed != '1';
        
        echo json_encode([
            'show_tutorial' => $show_tutorial,
            'dismissed' => $tutorial_dismissed == '1'
        ]);
    }

    /**
     * Save tutorial preference (dismissed state)
     * 
     * @return json
     */
    public function save_tutorial_preference()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        $dismissed = $this->input->post('dismissed') ? 1 : 0;
        
        // Load user meta helper if not loaded
        if (!function_exists('update_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Save preference
        $result = update_meta('staff', $staff_id, 'ella_contractors_tutorial_dismissed', $dismissed);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Tutorial preference saved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save tutorial preference'
            ]);
        }
    }

    /**
     * Reset tutorial for current user (admin function)
     * Allows users to restart the tutorial
     * 
     * @return json
     */
    public function reset_tutorial()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Load user meta helper if not loaded
        if (!function_exists('delete_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Remove tutorial dismissal preference
        $result = delete_meta('staff', $staff_id, 'ella_contractors_tutorial_dismissed');
        
        echo json_encode([
            'success' => true,
            'message' => 'Tutorial reset successfully. Refresh the page to see it again.'
        ]);
    }

    /**
     * Check estimate tutorial status for current user
     * Returns whether tutorial should be shown
     * 
     * @return json
     */
    public function check_estimate_tutorial_status()
    {
        if (!is_staff_logged_in()) {
            echo json_encode(['show_tutorial' => false]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Check user meta for tutorial dismissal
        if (!function_exists('get_meta')) {
            $this->load->helper('user_meta');
        }
        
        $tutorial_dismissed = get_meta('staff', $staff_id, 'ella_contractors_estimate_tutorial_dismissed');
        
        $show_tutorial = empty($tutorial_dismissed) || $tutorial_dismissed != '1';
        
        echo json_encode([
            'show_tutorial' => $show_tutorial,
            'dismissed' => $tutorial_dismissed == '1'
        ]);
    }

    /**
     * Save estimate tutorial preference (dismissed state)
     * 
     * @return json
     */
    public function save_estimate_tutorial_preference()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        $dismissed = $this->input->post('dismissed') ? 1 : 0;
        
        // Load user meta helper if not loaded
        if (!function_exists('update_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Save preference
        $result = update_meta('staff', $staff_id, 'ella_contractors_estimate_tutorial_dismissed', $dismissed);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Tutorial preference saved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save tutorial preference'
            ]);
        }
    }

    /**
     * Reset estimate tutorial for current user
     * Allows users to restart the estimate tutorial
     * 
     * @return json
     */
    public function reset_estimate_tutorial()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Load user meta helper if not loaded
        if (!function_exists('delete_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Remove tutorial dismissal preference
        $result = delete_meta('staff', $staff_id, 'ella_contractors_estimate_tutorial_dismissed');
        
        echo json_encode([
            'success' => true,
            'message' => 'Estimate tutorial reset successfully. Refresh the page to see it again.'
        ]);
    }

    /**
     * Check service items tutorial status for current user
     * Returns whether tutorial should be shown
     * 
     * @return json
     */
    public function check_service_items_tutorial_status()
    {
        if (!is_staff_logged_in()) {
            echo json_encode(['show_tutorial' => false]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Check user meta for tutorial dismissal
        if (!function_exists('get_meta')) {
            $this->load->helper('user_meta');
        }
        
        $tutorial_dismissed = get_meta('staff', $staff_id, 'ella_contractors_service_items_tutorial_dismissed');
        
        $show_tutorial = empty($tutorial_dismissed) || $tutorial_dismissed != '1';
        
        echo json_encode([
            'show_tutorial' => $show_tutorial,
            'dismissed' => $tutorial_dismissed == '1'
        ]);
    }

    /**
     * Save service items tutorial preference (dismissed state)
     * 
     * @return json
     */
    public function save_service_items_tutorial_preference()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        $dismissed = $this->input->post('dismissed') ? 1 : 0;
        
        // Load user meta helper if not loaded
        if (!function_exists('update_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Save preference
        $result = update_meta('staff', $staff_id, 'ella_contractors_service_items_tutorial_dismissed', $dismissed);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Tutorial preference saved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save tutorial preference'
            ]);
        }
    }

    /**
     * Reset service items tutorial for current user
     * Allows users to restart the service items tutorial
     * 
     * @return json
     */
    public function reset_service_items_tutorial()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Load user meta helper if not loaded
        if (!function_exists('delete_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Remove tutorial dismissal preference
        $result = delete_meta('staff', $staff_id, 'ella_contractors_service_items_tutorial_dismissed');
        
        echo json_encode([
            'success' => true,
            'message' => 'Service items tutorial reset successfully. Refresh the page to see it again.'
        ]);
    }

    /**
     * Sync appointment to calendar (Google or Outlook)
     * Wrapper for calendar_sync_helper functions
     */
    private function sync_to_calendar($appointment_id, $action = 'create', $provider = 'google')
    {
        return sync_appointment_to_calendar($this, $appointment_id, $action, $provider);
    }

    /**
     * Handle assignee change - sync to calendar (Google or Outlook)
     * Wrapper for calendar_sync_helper functions
     */
    private function sync_calendar_assignee_change($appointment_id, $old_assignees, $new_assignees, $provider = 'google')
    {
        sync_calendar_assignee_change($this, $appointment_id, $old_assignees, $new_assignees, $provider);
    }

    /**
     * Wrapper: Sync appointment to Google Calendar
     * @deprecated Use sync_to_calendar($appointment_id, $action, 'google') instead
     */
    private function sync_to_google_calendar($appointment_id, $action = 'create')
    {
        return $this->sync_to_calendar($appointment_id, $action, 'google');
    }
    
    /**
     * Wrapper: Sync appointment to Outlook Calendar
     * @deprecated Use sync_to_calendar($appointment_id, $action, 'outlook') instead
     */
    private function sync_to_outlook_calendar($appointment_id, $action = 'create')
    {
        return $this->sync_to_calendar($appointment_id, $action, 'outlook');
                }
    
    /**
     * Wrapper: Handle assignee change for Google Calendar
     * @deprecated Use sync_calendar_assignee_change($appointment_id, $old, $new, 'google') instead
     */
    private function sync_assignee_change($appointment_id, $old_assignees, $new_assignees)
    {
        $this->sync_calendar_assignee_change($appointment_id, $old_assignees, $new_assignees, 'google');
        }
    
    /**
     * Wrapper: Handle assignee change for Outlook Calendar
     * @deprecated Use sync_calendar_assignee_change($appointment_id, $old, $new, 'outlook') instead
     */
    private function sync_outlook_assignee_change($appointment_id, $old_assignees, $new_assignees)
    {
        $this->sync_calendar_assignee_change($appointment_id, $old_assignees, $new_assignees, 'outlook');
    }

    /**
     * Get reminder template preview (AJAX)
     * Creates default template if none exists
     */
    public function get_reminder_template_preview()
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $reminder_stage = $this->input->post('reminder_stage');
        $template_type = $this->input->post('template_type'); // 'email' or 'sms'
        $recipient_type = $this->input->post('recipient_type'); // 'client' or 'staff'
        $appointment_id = $this->input->post('appointment_id');

        if (!$reminder_stage || !$template_type || !$recipient_type) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            return;
        }

        $template = $this->reminder_template_model->get_by_stage($reminder_stage, $template_type, $recipient_type);
        
        // If template doesn't exist, create a default one
        if (!$template) {
            $template = $this->create_default_template($reminder_stage, $template_type, $recipient_type);
            
            if (!$template) {
                echo json_encode(['success' => false, 'message' => 'Failed to create default template']);
                return;
            }
        }

        // Extract which fields are currently in the template
        $available_fields = [
            '{appointment_subject}',
            '{appointment_date}',
            '{appointment_time}',
            '{appointment_location}',
            '{client_name}',
            '{staff_name}',
            '{company_name}',
            '{company_phone}',
            '{company_email}',
            '{appointment_notes}',
            '{presentation_block}',
            '{crm_link}'
        ];
        
        $included_fields = [];
        foreach ($available_fields as $field) {
            if (strpos($template->content, $field) !== false || strpos($template->subject, $field) !== false) {
                $included_fields[] = $field;
            }
        }
        
        // If appointment_id provided, parse template with actual data for preview
        $preview_content = $template->content;
        $preview_subject = $template->subject;
        
        if ($appointment_id) {
            $appointment = $this->appointments_model->get_appointment($appointment_id);
            if ($appointment) {
                $preview_content = $this->parse_template($template->content, $appointment, $recipient_type);
                if ($template->subject) {
                    $preview_subject = $this->parse_template($template->subject, $appointment, $recipient_type);
                }
            }
        } else {
            // For preview without appointment, highlight fields with yellow background
            foreach ($available_fields as $field) {
                $field_name = trim($field, '{}');
                $preview_content = str_replace($field, '<span style="background: #fff3cd; padding: 2px 5px; border-radius: 3px; font-weight: bold;">' . $field . '</span>', $preview_content);
                $preview_subject = str_replace($field, '<span style="background: #fff3cd; padding: 2px 5px; border-radius: 3px; font-weight: bold;">' . $field . '</span>', $preview_subject);
            }
        }

        echo json_encode([
            'success' => true,
            'template' => [
                'id' => $template->id,
                'name' => $template->template_name,
                'subject' => $preview_subject,
                'content' => $preview_content,
                'original_subject' => $template->subject,
                'original_content' => $template->content,
                'type' => $template->template_type,
                'included_fields' => $included_fields
            ]
        ]);
    }

    /**
     * Create default template if none exists
     */
    private function create_default_template($reminder_stage, $template_type, $recipient_type)
    {
        // Load email templates helper
        $email_templates_helper = module_dir_path('ella_contractors', 'helpers/ella_email_templates_helper.php');
        if (file_exists($email_templates_helper) && !function_exists('ella_get_client_reminder_template')) {
            require_once($email_templates_helper);
        }
        
        // Define default templates
        $default_templates = [
            'client_instant' => [
                'email' => [
                    'name' => 'Client Instant Email',
                    'subject' => 'Appointment Confirmation: {appointment_subject}',
                    'content' => function_exists('ella_get_client_reminder_template') ? ella_get_client_reminder_template() : $this->get_fallback_client_email_template()
                ],
                'sms' => [
                    'name' => 'Client Instant SMS',
                    'subject' => null,
                    'content' => 'Appointment Confirmed: {appointment_subject} on {appointment_date} at {appointment_time}. Location: {appointment_location}'
                ]
            ],
            'client_48h' => [
                'email' => [
                    'name' => 'Client 48h Email',
                    'subject' => 'Appointment Reminder: {appointment_subject}',
                    'content' => function_exists('ella_get_client_reminder_template') ? ella_get_client_reminder_template() : $this->get_fallback_client_email_template()
                ],
                'sms' => [
                    'name' => 'Client 48h SMS',
                    'subject' => null,
                    'content' => 'Reminder: {appointment_subject} on {appointment_date} at {appointment_time}. Location: {appointment_location}'
                ]
            ],
            'client_same_day' => [
                'email' => [
                    'name' => 'Client Same Day Email',
                    'subject' => 'Reminder: Your Appointment Today - {appointment_subject}',
                    'content' => function_exists('ella_get_client_reminder_template') ? ella_get_client_reminder_template() : $this->get_fallback_client_email_template()
                ],
                'sms' => [
                    'name' => 'Client Same Day SMS',
                    'subject' => null,
                    'content' => 'Reminder: Your appointment {appointment_subject} is today at {appointment_time}. Location: {appointment_location}'
                ]
            ],
            'staff_48h' => [
                'email' => [
                    'name' => 'Staff 48h Email',
                    'subject' => 'Your Appointment Reminder: {appointment_subject}',
                    'content' => function_exists('ella_get_staff_reminder_template') ? ella_get_staff_reminder_template() : $this->get_fallback_staff_email_template()
                ],
                'sms' => [
                    'name' => 'Staff 48h SMS',
                    'subject' => null,
                    'content' => 'Reminder: {appointment_subject} with {client_name} on {appointment_date} at {appointment_time}'
                ]
            ],
            'staff_same_day' => [
                'email' => [
                    'name' => 'Staff Same Day Email',
                    'subject' => 'Reminder: Appointment Today - {appointment_subject}',
                    'content' => function_exists('ella_get_staff_reminder_template') ? ella_get_staff_reminder_template() : $this->get_fallback_staff_email_template()
                ],
                'sms' => [
                    'name' => 'Staff Same Day SMS',
                    'subject' => null,
                    'content' => 'Reminder: Appointment {appointment_subject} with {client_name} is today at {appointment_time}. Location: {appointment_location}'
                ]
            ]
        ];
        
        if (!isset($default_templates[$reminder_stage][$template_type])) {
            return false;
        }
        
        $default = $default_templates[$reminder_stage][$template_type];
        
        $data = [
            'template_name' => $default['name'],
            'template_type' => $template_type,
            'reminder_stage' => $reminder_stage,
            'recipient_type' => $recipient_type,
            'subject' => $default['subject'],
            'content' => is_callable($default['content']) ? $default['content']() : $default['content'],
            'is_active' => 1,
            'created_by' => get_staff_user_id() ?: 0
        ];
        
        $template_id = $this->reminder_template_model->create($data);
        
        if ($template_id) {
            return $this->reminder_template_model->get($template_id);
        }
        
        return false;
    }

    /**
     * Get fallback client email template
     */
    private function get_fallback_client_email_template()
    {
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial; padding: 20px;"><h2>Appointment Confirmation</h2><p>Dear {client_name},</p><p>This is a confirmation of your upcoming appointment.</p><p><strong>Appointment:</strong> {appointment_subject}<br><strong>Date:</strong> {appointment_date}<br><strong>Time:</strong> {appointment_time}<br><strong>Location:</strong> {appointment_location}</p><p>{appointment_notes}</p><p>{presentation_block}</p><p>Best regards,<br>{company_name}</p></body></html>';
    }

    /**
     * Get fallback staff email template
     */
    private function get_fallback_staff_email_template()
    {
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial; padding: 20px;"><h2>Appointment Reminder</h2><p>Hi {staff_name},</p><p>This is a reminder about your upcoming appointment.</p><p><strong>Appointment:</strong> {appointment_subject}<br><strong>Client:</strong> {client_name}<br><strong>Date:</strong> {appointment_date}<br><strong>Time:</strong> {appointment_time}<br><strong>Location:</strong> {appointment_location}</p><p><strong>Notes:</strong><br>{appointment_notes}</p><p>{presentation_block}</p><p><a href="{crm_link}">View in CRM</a></p><p>Best regards,<br>{company_name} CRM</p></body></html>';
    }

    /**
     * Save reminder template (AJAX)
     */
    public function save_reminder_template()
    {
        if (!has_permission('ella_contractor', '', 'edit')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        // Get content from either direct input or from template_structure
        $content = $this->input->post('content');
        $template_structure = $this->input->post('template_structure');
        
        // If template_structure is provided, rebuild content from it
        if ($template_structure) {
            $structure = json_decode($template_structure, true);
            if ($structure && isset($structure['html'])) {
                $content = $structure['html'];
            }
        }
        
        $data = [
            'template_name' => $this->input->post('template_name'),
            'template_type' => $this->input->post('template_type'),
            'reminder_stage' => $this->input->post('reminder_stage'),
            'recipient_type' => $this->input->post('recipient_type'),
            'subject' => $this->input->post('subject'),
            'content' => $content,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        ];

        if ($id) {
            $result = $this->reminder_template_model->update($id, $data);
            $message = $result ? 'Template updated successfully' : 'Failed to update template';
        } else {
            $id = $this->reminder_template_model->create($data);
            $result = $id > 0;
            $message = $result ? 'Template created successfully' : 'Failed to create template';
        }

        echo json_encode([
            'success' => $result,
            'message' => $message,
            'id' => $id
        ]);
    }

    /**
     * Parse template with appointment data
     */
    private function parse_template($template, $appointment, $recipient_type = 'client')
    {
        $CI = &get_instance();
        $CI->load->model('leads_model');
        $CI->load->model('clients_model');

        // Get client/lead name
        $client_or_lead_name = 'Valued Customer';
        if (!empty($appointment->contact_id)) {
            if (!empty($appointment->lead_name)) {
                $client_or_lead_name = $appointment->lead_name;
            } else {
                $client = $CI->clients_model->get($appointment->contact_id);
                if ($client) {
                    $client_or_lead_name = $client->company ?: trim(($client->firstname ?? '') . ' ' . ($client->lastname ?? ''));
                }
            }
        }

        // Get presentation block for staff same-day reminders
        $presentation_block = '';
        if ($recipient_type === 'staff' && in_array($appointment->reminder_stage ?? '', ['staff_same_day'])) {
            if (!function_exists('ella_get_presentation_links_for_email')) {
                require_once(module_dir_path('ella_contractors', 'helpers/ella_reminder_helper.php'));
            }
            $presentations_for_email = ella_get_presentation_links_for_email($appointment->id);
            $presentation_block = ella_build_presentation_block_html($presentations_for_email);
        }

        $replacements = [
            '{appointment_subject}' => htmlspecialchars($appointment->subject ?? ''),
            '{appointment_date}' => $appointment->date ? date('F j, Y', strtotime($appointment->date)) : '',
            '{appointment_time}' => $appointment->start_hour ? date('g:i A', strtotime($appointment->start_hour)) : '',
            '{appointment_location}' => htmlspecialchars($appointment->address ?: 'Online/Phone Call'),
            '{client_name}' => htmlspecialchars($client_or_lead_name),
            '{staff_name}' => get_staff_full_name($appointment->created_by ?? 0),
            '{company_name}' => get_option('companyname') ?: 'Our Company',
            '{company_phone}' => get_option('company_phone_number') ?: '',
            '{company_email}' => get_option('company_email') ?: '',
            '{crm_link}' => $recipient_type === 'staff' ? admin_url('ella_contractors/appointments/view/' . $appointment->id) : '',
            '{appointment_notes}' => !empty($appointment->notes) ? nl2br(htmlspecialchars($appointment->notes)) : 'No additional notes',
            '{presentation_block}' => $presentation_block,
        ];

        foreach ($replacements as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }

    
}
