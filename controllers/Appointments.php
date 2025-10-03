<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $this->load->model('ella_contractors/Measurements_model', 'measurements_model');
        $this->load->model('staff_model');
        $this->load->model('clients_model');
        $this->load->model('leads_model');
        $this->load->model('misc_model');
    }
    
    

    /**
     * Appointments listing page
     */
    public function index()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
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
        if (!has_permission('ella_contractors', '', 'edit')) {
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
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            show_404();
        }

        $data['title'] = 'View Appointment';
        $data['appointment'] = $appointment; // Keep as object for easier access
        $data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
        
        // Load measurements for this appointment
        $data['measurements'] = $this->measurements_model->get_related_measurements('appointment', $id);
        
        // Load clients and leads for estimate modal
        $data['clients'] = $this->clients_model->get();
        $data['leads'] = $this->leads_model->get();
        
        // Timeline activities will be loaded via AJAX when tab is clicked
        
        $this->load->view('appointments/view', $data);
    }
    /**
     * Delete appointment
     */
    public function delete($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
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
        if (!has_permission('ella_contractors', '', 'view')) {
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
            if (!has_permission('ella_contractors', '', 'edit')) {
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
        if (!has_permission('ella_contractors', '', 'view')) {
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
        if (!has_permission('ella_contractors', '', 'view')) {
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
        if (!has_permission('ella_contractors', '', 'create') && !has_permission('ella_contractors', '', 'edit')) {
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
            'type_id' => $this->input->post('type_id') ?: 0,
            'appointment_status' => $this->input->post('status') ?: 'scheduled',
            'source' => 'ella_contractor',
            'send_reminder' => $this->input->post('send_reminder') ? 1 : 0
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
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment updated successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update appointment. Database error: ' . $this->db->last_query()
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
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment created successfully',
                        'appointment_id' => $appointment_id
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create appointment. Database error: ' . $this->db->last_query()
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
        if (!has_permission('ella_contractors', '', 'delete')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
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
     * Handle attendees for appointment
     */
    private function handle_attendees($appointment_id)
    {
        $attendees = $this->input->post('attendees');
        if ($attendees && is_array($attendees)) {
            // Remove existing attendees
            $this->db->where('appointment_id', $appointment_id);
            $this->db->delete(db_prefix() . 'appointly_attendees');
            
            // Add new attendees
            foreach ($attendees as $staff_id) {
                $this->appointments_model->add_attendee($appointment_id, $staff_id);
            }
        }
    }

    /**
     * Get measurements for appointment (AJAX)
     */
    public function get_measurements($appointment_id)
    {
        log_message('debug', 'Getting measurements for appointment: ' . $appointment_id);
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        // Get measurements using the existing table structure (keep original functionality)
        $this->db->select('id, name, category, rel_type, rel_id, appointment_id, attributes_json, dtmCreated as dateadded');
        $this->db->from(db_prefix() . 'ella_contractors_measurements');
        $this->db->where('appointment_id', $appointment_id);
        $this->db->order_by('dtmCreated', 'DESC');
        
        $measurements = $this->db->get()->result_array();
        
        // Process measurements to extract the new structure from attributes_json for siding/roofing
        $processed_measurements = [];
        foreach ($measurements as $measurement) {
            $attributes = json_decode($measurement['attributes_json'] ?? '{}', true);
            
            // Extract siding measurements
            if (isset($attributes['siding_measurements']) && is_array($attributes['siding_measurements'])) {
                foreach ($attributes['siding_measurements'] as $siding_measurement) {
                    $processed_measurements[] = [
                        'id' => $measurement['id'],
                        'measurement_name' => $siding_measurement['name'] ?? 'N/A',
                        'measurement_value' => $siding_measurement['value'] ?? '0.0000',
                        'measurement_unit' => $siding_measurement['unit'] ?? 'N/A',
                        'category' => 'siding',
                        'dateadded' => $measurement['dateadded']
                    ];
                }
            }
            
            // Extract roofing measurements
            if (isset($attributes['roofing_measurements']) && is_array($attributes['roofing_measurements'])) {
                foreach ($attributes['roofing_measurements'] as $roofing_measurement) {
                    $processed_measurements[] = [
                        'id' => $measurement['id'],
                        'measurement_name' => $roofing_measurement['name'] ?? 'N/A',
                        'measurement_value' => $roofing_measurement['value'] ?? '0.0000',
                        'measurement_unit' => $roofing_measurement['unit'] ?? 'N/A',
                        'category' => 'roofing',
                        'dateadded' => $measurement['dateadded']
                    ];
                }
            }
            
            // For existing measurements (windows, doors, etc.), use the original structure
            if (empty($attributes['siding_measurements']) && empty($attributes['roofing_measurements'])) {
                $processed_measurements[] = [
                    'id' => $measurement['id'],
                    'measurement_name' => $measurement['name'] ?? 'N/A',
                    'measurement_value' => '0.0000',
                    'measurement_unit' => 'N/A',
                    'category' => $measurement['category'] ?? 'other',
                    'dateadded' => $measurement['dateadded']
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $processed_measurements
        ]);
    }

    /**
     * Get single measurement for editing (AJAX)
     */
    public function get_measurement($appointment_id, $measurement_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $measurement = $this->measurements_model->find($measurement_id);
        
        // Verify measurement belongs to this appointment
        if (!$measurement || $measurement['rel_type'] != 'appointment' || $measurement['rel_id'] != $appointment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Measurement not found or access denied'
            ]);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $measurement
        ]);
    }

    /**
     * Save measurement for appointment (AJAX) - Updated structure with new siding/roofing
     */
    public function save_measurement($appointment_id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        $post = $this->input->post(null, true);
        $id = isset($post['id']) ? (int) $post['id'] : 0;

        // Bulk save handler for windows/doors (and optionally siding/roofing) arrays
        $bulk = $this->input->post('bulk');
        if ($bulk && is_array($bulk)) {
            // Find or create a single combined measurement record for this appointment
            $table = db_prefix() . 'ella_contractors_measurements';
            // Find existing combined measurement either by appointment_id
            // or by legacy pairing of rel_type + rel_id
            $this->db->select('id')->from($table);
            $this->db->group_start()
                ->where('appointment_id', (int)$appointment_id)
                ->or_group_start()
                    ->where('rel_type', 'appointment')
                    ->where('rel_id', (int)$appointment_id)
                ->group_end()
            ->group_end();
            $this->db->where('category', 'combined');
            $this->db->order_by('id', 'ASC');
            $existing = $this->db->get()->row_array();

            // Merge incoming bulk with existing attributes, updating only provided keys
            $existing_attributes = [];
            if ($existing) {
                $row = $this->measurements_model->find((int)$existing['id']);
                $existing_attributes = json_decode($row['attributes_json'] ?? '[]', true) ?: [];
            }
            foreach (['windows','doors','siding','roofing'] as $key) {
                if (isset($bulk[$key])) {
                    $existing_attributes[$key] = $bulk[$key];
                }
            }

            $payload = [
                'category' => 'combined',
                'rel_type' => 'appointment',
                'rel_id' => (int)$appointment_id,
                'appointment_id' => (int)$appointment_id,
                'name' => 'Combined Measurement',
                'attributes_json' => json_encode($existing_attributes),
            ];

            if ($existing) {
                $ok = $this->measurements_model->update((int)$existing['id'], $payload);
                $respId = (int)$existing['id'];
            } else {
                $respId = (int)$this->measurements_model->create($payload);
                $ok = $respId > 0;
            }

            echo json_encode([
                'success' => (bool)$ok,
                'message' => $ok ? 'Measurements saved successfully' : 'Failed to save measurements',
                'data' => ['id' => $respId, 'attributes' => json_decode($payload['attributes_json'], true)],
            ]);
            return;
        }

        // Set appointment relationship
        $post['rel_type'] = 'appointment';
        $post['rel_id'] = $appointment_id;
        $post['appointment_id'] = $appointment_id;

        // Handle category-specific attributes (same as original measurements controller)
        $categorySpecificData = [];
        $categories = ['siding', 'roofing', 'windows', 'doors'];
        
        foreach ($categories as $category) {
            if (isset($post[$category]) && is_array($post[$category])) {
                $categorySpecificData[$category] = $post[$category];
                unset($post[$category]);
            }
        }

        // Handle new siding and roofing measurements
        if (isset($post['siding_measurements']) && is_array($post['siding_measurements'])) {
            $categorySpecificData['siding_measurements'] = $post['siding_measurements'];
            unset($post['siding_measurements']);
        }
        if (isset($post['roofing_measurements']) && is_array($post['roofing_measurements'])) {
            $categorySpecificData['roofing_measurements'] = $post['roofing_measurements'];
            unset($post['roofing_measurements']);
        }

        // Merge with existing attributes_json if editing
        if ($id > 0) {
            $existing = $this->measurements_model->find($id);
            $existing_attributes = json_decode($existing['attributes_json'] ?? '{}', true);
            $post['attributes_json'] = json_encode(array_merge($existing_attributes, $categorySpecificData));
        } else {
            $post['attributes_json'] = json_encode($categorySpecificData);
        }

        // Handle basic measurement fields if they exist
        if (isset($post['width_val']) && isset($post['height_val'])) {
            $width = (float) $post['width_val'];
            $height = (float) $post['height_val'];
            if ($width && $height) {
                if (!isset($post['united_inches_val']) || $post['united_inches_val'] === '') {
                    $post['united_inches_val'] = $width + $height;
                }
                if (!isset($post['area_val']) || $post['area_val'] === '') {
                    $lenU = $post['length_unit'] ?? 'in';
                    $areaU = $post['area_unit'] ?? 'sqft';
                    if ($lenU === 'in' && $areaU === 'sqft') {
                        $post['area_val'] = ($width * $height) / 144.0;
                    }
                }
            }
        }

        // Set default values for required fields
        $post['designator'] = $post['designator'] ?? '';
        $post['name'] = $post['name'] ?? 'Unnamed ' . ucfirst($post['category'] ?? 'item');
        $post['location_label'] = $post['location_label'] ?? '';
        $post['level_label'] = $post['level_label'] ?? '';
        $post['quantity'] = $post['quantity'] ?? 1;
        $post['length_unit'] = $post['length_unit'] ?? 'in';
        $post['area_unit'] = $post['area_unit'] ?? 'sqft';
        $post['ui_unit'] = $post['ui_unit'] ?? 'in';

        if ($id > 0) {
            $ok = $this->measurements_model->update($id, $post);
            if ($ok) {
                // Log measurement update activity
                $this->appointments_model->log_activity($appointment_id, 'updated', 'measurement', $post['name'], ['measurement_id' => $id, 'category' => $post['category'] ?? 'general']);
            }
            $msg = $ok ? 'Measurement updated successfully' : 'Nothing changed';
        } else {
            $measurement_id = $this->measurements_model->create($post);
            $ok = (bool) $measurement_id;
            if ($ok) {
                // Log measurement creation activity
                $this->appointments_model->log_activity($appointment_id, 'created', 'measurement', $post['name'], ['measurement_id' => $measurement_id, 'category' => $post['category'] ?? 'general']);
            }
            $msg = $ok ? 'Measurement created successfully' : 'Failed to create measurement';
        }

        echo json_encode([
            'success' => $ok,
            'message' => $msg,
            'data' => $ok ? $this->measurements_model->find($id ?: $this->db->insert_id()) : null
        ]);
    }

    /**
     * Delete measurement for appointment (AJAX)
     */
    public function delete_measurement($appointment_id, $measurement_id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            ajax_access_denied();
        }

        // Verify measurement belongs to this appointment
        $measurement = $this->measurements_model->find($measurement_id);
        if (!$measurement || $measurement['rel_type'] != 'appointment' || $measurement['rel_id'] != $appointment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Measurement not found or access denied'
            ]);
            return;
        }

        $ok = $this->measurements_model->delete($measurement_id);
        
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Measurement deleted successfully' : 'Failed to delete measurement'
        ]);
    }

    // ==================== ESTIMATES MANAGEMENT ====================


    /**
     * Save estimate for appointment
     */
    public function save_estimate()
    {
        if (!has_permission('ella_contractors', '', 'create') && !has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contractors/Ella_estimates_model', 'estimates_model');
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('estimate_name', 'Estimate Name', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }

            $data = [
                'estimate_name' => $this->input->post('estimate_name'),
                // 'client_id' => $this->input->post('client_id') ?: null,  // Commented out for now
                // 'lead_id' => $this->input->post('lead_id') ?: null,      // Commented out for now
                'appointment_id' => $this->input->post('appointment_id'),
                'status' => $this->input->post('status')
            ];

        $estimate_id = $this->input->post('estimate_id');
        
        try {
            if ($estimate_id) {
                // Update existing estimate
                if ($this->estimates_model->update_estimate($estimate_id, $data)) {
                    // Log estimate update activity
                    $this->appointments_model->log_activity($data['appointment_id'], 'updated', 'estimate', $data['estimate_name'], ['estimate_id' => $estimate_id]);
                    
                    // Handle line items
                    $this->handle_estimate_line_items($estimate_id);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Estimate updated successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update estimate'
                    ]);
                }
            } else {
                // Create new estimate
                $estimate_id = $this->estimates_model->create_estimate($data);
                if ($estimate_id) {
                    // Log estimate creation activity
                    $this->appointments_model->log_activity($data['appointment_id'], 'created', 'estimate', $data['estimate_name'], ['estimate_id' => $estimate_id]);
                    
                    // Handle line items
                    $this->handle_estimate_line_items($estimate_id);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Estimate created successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create estimate'
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
     * Handle estimate line items
     */
    private function handle_estimate_line_items($estimate_id)
    {
		$line_items = $this->input->post('line_items');
		
		if ($line_items && is_array($line_items)) {
			// Normalize and merge duplicates by line_item_id to satisfy unique constraint
			$merged = [];
			foreach ($line_items as $item) {
				$line_item_id = isset($item['line_item_id']) ? (int) $item['line_item_id'] : 0;
				$quantity = isset($item['quantity']) ? (float) $item['quantity'] : 0;
				$unit_price = isset($item['unit_price']) ? (float) $item['unit_price'] : 0;
				
				if ($line_item_id > 0 && $quantity > 0 && $unit_price >= 0) {
					if (!isset($merged[$line_item_id])) {
						$merged[$line_item_id] = [
							'quantity' => 0.0,
							'unit_price' => $unit_price,
						];
					}
					// Sum quantities; use the latest provided unit_price
					$merged[$line_item_id]['quantity'] += $quantity;
					$merged[$line_item_id]['unit_price'] = $unit_price ?: $merged[$line_item_id]['unit_price'];
				}
			}
			
			// Delete existing line items then insert merged set
			$this->db->where('estimate_id', $estimate_id);
			$this->db->delete(db_prefix() . 'ella_contractor_estimate_line_items');
			
			// Validate foreign keys: ensure line_item_id exists
			$idsToInsert = array_map('intval', array_keys($merged));
			$existingIds = [];
			if (!empty($idsToInsert)) {
				$q = $this->db->select('id')
					->from(db_prefix() . 'ella_contractor_line_items')
					->where_in('id', $idsToInsert)
					->get();
				foreach ($q->result() as $row) {
					$existingIds[(int)$row->id] = true;
				}
			}
			
			foreach ($merged as $lid => $data) {
				$lid = (int)$lid;
				if (!isset($existingIds[$lid])) {
					log_message('error', 'Skipping insertion of non-existent line_item_id ' . $lid . ' for estimate ' . $estimate_id);
					continue;
				}
				$this->estimates_model->add_line_item_to_estimate(
					$estimate_id,
					$lid,
					(float)$data['quantity'],
					(float)$data['unit_price']
				);
			}
			
			// Update estimate totals
			$this->estimates_model->update_estimate_totals($estimate_id);
		}
    }

    /**
     * Get estimate data for AJAX
     */
    public function get_estimate_data($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/Ella_estimates_model', 'estimates_model');
        
        $estimate = (array) $this->estimates_model->get_estimate($id);
        $estimate['line_items'] = $this->estimates_model->get_estimate_line_items($id);
        
        echo json_encode($estimate);
    }

    /**
     * DataTable server-side processing for appointment estimates
     */
    public function estimates_table($appointment_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/Ella_estimates_model', 'estimates_model');
        
        // Get estimates for this appointment
        $estimates = $this->estimates_model->get_estimates(null, null, null, $appointment_id);
        
        // Format data for DataTable
        $data = [];
        foreach ($estimates as $estimate) {
            $statusClass = '';
            switch($estimate['status']) {
                case 'draft': $statusClass = 'label-warning'; break;
                case 'sent': $statusClass = 'label-info'; break;
                case 'accepted': $statusClass = 'label-success'; break;
                case 'rejected': $statusClass = 'label-danger'; break;
                case 'expired': $statusClass = 'label-default'; break;
            }
            
            $data[] = [
                'estimate_name' => $estimate['estimate_name'],
                'status' => '<span class="label ' . $statusClass . '">' . strtoupper($estimate['status']) . '</span>',
                'line_items_count' => $estimate['line_items_count'] ?: 0,
                'total_quantity' => number_format($estimate['total_quantity'] ?: 0, 2),
                'total_amount' => '$' . number_format($estimate['total_amount'] ?: 0, 2),
                'created_by_name' => $estimate['created_by_name'] ?: '-',
                'created_at' => _d($estimate['created_at']),
                'updated_at' => _d($estimate['updated_at']),
                'actions' => '<a href="' . admin_url('ella_contractors/appointments/estimates/' . $appointment_id . '?edit=' . $estimate['id']) . '" class="btn btn-default btn-xs" title="Edit"><i class="fa fa-edit"></i></a> ' .
                           '<a href="' . admin_url('ella_contractors/appointments/delete_estimate/' . $appointment_id . '/' . $estimate['id']) . '" class="btn btn-danger btn-xs" title="Delete" onclick="return confirm(\'Are you sure?\')"><i class="fa fa-trash"></i></a>'
            ];
        }
        
        echo json_encode([
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data)
        ]);
    }

    /**
     * Delete estimate for appointment
     */
    public function delete_estimate($appointment_id, $estimate_id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/Ella_estimates_model', 'estimates_model');
        
        // Verify estimate belongs to this appointment
        $estimate = $this->estimates_model->get_estimate($estimate_id);
        if (!$estimate || $estimate->appointment_id != $appointment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Estimate not found or access denied'
            ]);
            return;
        }

        if ($this->estimates_model->delete_estimate($estimate_id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Estimate deleted successfully'
            ]);
        } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete estimate'
        ]);
    }
    }
    /**
     * AJAX endpoint to get appointment types for global appointment creation
     */
    public function get_types()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $types = $this->appointments_model->get_appointment_types();
        
        echo json_encode([
            'success' => true,
            'types' => $types
        ]);
    }


    /**
     * Send SMS to lead from appointment
     */
    public function send_sms()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }
        
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
            
            // Call the method to send SMS
            $response = $this->leads_model->send_sms($lead_id, $staff_id, $number, $sms_body, $media_url, $tcpa, $dnc_validation, $ics_url);
            
            // Update lead last contact and log activity
            if ($response['success']) {
                update_lead_last_contact($lead_id, get_staff_user_id(), "SMS");
                log_staff_status_activity('Added SMS Activity from Appointment Lead# [' . $lead_id . ']');
                // Mark all SMS as read
                $this->leads_model->updated_sms_log_status($lead_id, 'lead_id', '0');
                
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
    }
    
    /**
     * Get SMS logs for appointment
     */
    public function get_sms_logs()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
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
        if (!has_permission('ella_contractors', '', 'view')) {
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
        if (!has_permission('ella_contractors', '', 'create')) {
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
    
    /**
     * Send test SMS - matching leads functionality
     */
    public function send_test_sms()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }
        
        $response = array();
        $test_number = $this->input->post('test_number');
        $test_sms_body = $this->input->post('test_sms_body');
        
        if (empty($test_number) || empty($test_sms_body)) {
            $response['message'] = 'Please provide both phone number and message';
            $response['success'] = false;
        } else {
            // Load leads model
            $this->load->model('leads_model');
            
            // Send test SMS using the same method as leads
            $response = $this->leads_model->send_sms(0, get_staff_user_id(), $test_number, $test_sms_body, '', false, false, '');
        }
        
        echo json_encode($response, true);
    }
    
    public function upload_sms_media()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
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
     * Remove line item from estimate
     */
    public function remove_estimate_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/Ella_estimates_model', 'estimates_model');
        
        if ($this->estimates_model->remove_line_item_from_estimate($id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Line item removed successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to remove line item',
            ]);
        }
    }

    /**
     * Get estimates for appointment (AJAX)
     */
    public function get_estimates($appointment_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('ella_contractors/Ella_estimates_model', 'estimates_model');
        
        $estimates = $this->estimates_model->get_estimates(null, null, null, $appointment_id);
        
        echo json_encode([
            'success' => true,
            'data' => $estimates
        ]);
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
        if (!has_permission('ella_contractors', '', 'view')) {
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
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Handle the note description
            $data['description'] = isset($data['appointment_note_description']) ? $data['appointment_note_description'] : $data['description'];
            $data['description'] = replace_name_shortcodes($rel_id, $data['description']);

            if (isset($data['appointment_note_description'])) {
                unset($data['appointment_note_description']);
            }

            // Remove unnecessary fields that might cause issues
            unset($data['contacted_indicator']);
            unset($data['custom_contact_date']);
            unset($data['date_contacted']);

            if ($note_id) {
                // Update existing note
                $note = $this->db->get_where(db_prefix() . 'notes', ['id' => $note_id])->row();
                if (!$note || ($note->addedfrom != get_staff_user_id() && !is_admin())) {
                    echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this note']);
                    return;
                }

                $update_data = [
                    'description' => nl2br($data['description'])
                ];

                $success = $this->misc_model->edit_note($update_data, $note_id);
                
                if ($success) {
                    // Log note update activity
                    $this->appointments_model->log_activity($rel_id, 'updated', 'note', '', ['note_id' => $note_id, 'changes' => ['content' => ['old' => $note->description, 'new' => $data['description']]]]);
                    echo json_encode(['success' => true, 'message' => 'Note updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update note']);
                }
            } else {
                // Create new note
                $note_id = $this->misc_model->add_note($data, 'appointment', $rel_id);
                
                if ($note_id) {
                    // Log note addition activity
                    $this->appointments_model->log_activity($rel_id, 'added', 'note', '', ['note_id' => $note_id, 'note_content' => $data['description']]);
                    echo json_encode(['success' => true, 'message' => 'Note added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add note']);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No data received']);
        }
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

        $notes = $this->misc_model->get_notes($appointment_id, 'appointment');
        
        if ($notes) {
            // Add time_ago for each note
            foreach ($notes as &$note) {
                $note['time_ago'] = time_ago($note['dateadded']);
            }
            echo json_encode(['success' => true, 'data' => $notes]);
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

        $success = $this->misc_model->delete_note($note_id);
        
        if ($success) {
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
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->load->model('ella_media_model');
        $attachments = $this->ella_media_model->get_appointment_attachments($appointment_id);
        
        // Add full file paths to each attachment
        foreach ($attachments as &$attachment) {
            $attachment['file_path'] = 'uploads/ella_appointments/' . $appointment_id . '/' . $attachment['file_name'];
            $attachment['download_url'] = admin_url('ella_contractors/appointments/download_attachment/' . $attachment['id']);
        }
        
        echo json_encode([
            'success' => true,
            'attachments' => $attachments
        ]);
    }

    /**
     * Delete appointment attachment
     * @param int $attachment_id
     */
    public function delete_appointment_attachment($attachment_id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            ajax_access_denied();
        }

        $this->load->model('ella_media_model');
        $success = $this->ella_media_model->delete_appointment_attachment($attachment_id);
        
        if ($success) {
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
     * Download appointment attachment
     * @param int $attachment_id
     */
    public function download_attachment($attachment_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_media_model');
        $attachment = $this->ella_media_model->get_file($attachment_id);
        
        if (!$attachment || $attachment->rel_type !== 'appointment') {
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
            $this->load->model('ella_media_model');
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
            // Save to database
            $media_data = [
                'rel_type' => 'appointment',
                'rel_id' => $appointment_id,
                'org_id' => null,
                'folder_id' => null,
                'lead_id' => null,
                'file_name' => $unique_filename,
                'original_name' => $file_data['name'],
                'file_type' => $file_data['type'],
                'file_size' => $file_data['size'],
                'description' => 'Appointment attachment',
                'is_default' => 0,
                'active' => 1,
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
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }
        
        // Get appointment data
        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            echo '<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Appointment not found.</p></div>';
            return;
        }
        
        // Get timeline activities
        $timeline_activities = $this->appointments_model->get_appointment_timeline($id);
        
        // Load language file for timeline
        $this->lang->load('ella_contractors/ella_contractors', 'english');
        
        // Load the timeline view
        $this->load->view('admin/appointments/timeline', [
            'appointment' => $appointment,
            'timeline_activities' => $timeline_activities
        ]);
    }
    
    /**
     * Add note to appointment timeline (AJAX)
     */
    public function add_timeline_note($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }
        
        $note_content = $this->input->post('timeline_note_content');
        
        if (empty($note_content)) {
            echo json_encode([
                'success' => false,
                'message' => 'Note content is required'
            ]);
            return;
        }
        
        $result = $this->appointments_model->add_appointment_note($id, $note_content);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Note added successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add note'
            ]);
        }
    }
    
    /**
     * Log measurement addition (AJAX)
     */
    public function log_measurement_added()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $measurement = $this->input->post('measurement');
        
        if (!$appointment_id || !$measurement) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and measurement are required'
            ]);
            return;
        }
        
        $result = $this->appointments_model->log_measurement_added($appointment_id, $measurement);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Measurement logged successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to log measurement'
            ]);
        }
    }
    
    /**
     * Log measurement removal (AJAX)
     */
    public function log_measurement_removed()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }
        
        $appointment_id = $this->input->post('appointment_id');
        $measurement = $this->input->post('measurement');
        
        if (!$appointment_id || !$measurement) {
            echo json_encode([
                'success' => false,
                'message' => 'Appointment ID and measurement are required'
            ]);
            return;
        }
        
        $result = $this->appointments_model->log_measurement_removed($appointment_id, $measurement);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Measurement removal logged successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to log measurement removal'
            ]);
        }
    }
    
    /**
     * Log scheduled process (AJAX)
     */
    public function log_scheduled_process()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
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
    

}
