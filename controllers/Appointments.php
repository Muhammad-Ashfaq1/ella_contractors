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
    }
    
    /**
     * Ensure appointment_status column exists in the database
     */
    private function ensure_appointment_status_column()
    {
        if (!$this->db->field_exists('appointment_status', db_prefix() . 'appointly_appointments')) {
            try {
                // Add the column
                $this->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `appointment_status` ENUM(\'scheduled\',\'cancelled\',\'complete\') NULL DEFAULT \'scheduled\' AFTER `cancelled`');
                
                // Update existing records
                $this->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "cancelled" WHERE `cancelled` = 1');
                $this->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "complete" WHERE `finished` = 1 OR `approved` = 1');
                $this->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "scheduled" WHERE `appointment_status` IS NULL');
                
                error_log('Ella Appointments - Created appointment_status column and updated existing records');
            } catch (Exception $e) {
                error_log('Ella Appointments - Error creating appointment_status column: ' . $e->getMessage());
            }
        }
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
     * Create appointment page (redirects to index with modal)
     */
    public function create()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        redirect(admin_url('ella_contractors/appointments'));
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

        $data['title'] = 'Edit Appointment';
        $data['appointment'] = (array) $appointment; // Convert object to array
        $data['staff'] = $this->staff_model->get();
        $data['clients'] = $this->clients_model->get();
        $data['leads'] = $this->leads_model->get();
        $data['appointment_types'] = $this->appointments_model->get_appointment_types();
        $data['statuses'] = $this->appointments_model->get_statuses();
        $data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
        
        $this->load->view('appointments/edit', $data);
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
        $data['appointment'] = (array) $appointment; // Convert object to array
        $data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
        
        // Load measurements for this appointment
        $data['measurements'] = $this->measurements_model->get_related_measurements('appointment', $id);
        
        // Load clients and leads for estimate modal
        $data['clients'] = $this->clients_model->get();
        $data['leads'] = $this->leads_model->get();
        
        $this->load->view('appointments/view', $data);
    }

    /**
     * Save appointment (create/update)
     */
    public function save()
    {
        if (!has_permission('ella_contractors', '', 'create') && !has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('start_hour', 'Start Time', 'required');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            // Debug: Log the status being saved
            $status_value = $this->input->post('status');
            error_log('Ella Appointments - Status being saved: ' . $status_value);
            
            // Ensure appointment_status column exists
            $this->ensure_appointment_status_column();
            
            $data = [
                'subject' => $this->input->post('subject'),
                'description' => $this->input->post('description'),
                'date' => $this->input->post('date'),
                'start_hour' => $this->input->post('start_hour'),
                'contact_id' => $this->input->post('contact_id') ?: null,
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address' => $this->input->post('address'),
                'notes' => $this->input->post('notes'),
                'type_id' => $this->input->post('type_id') ?: 0,
                'appointment_status' => $status_value ?: 'scheduled',
                'source' => 'ella_contractor'
            ];
            
            // Debug: Log the complete data array
            error_log('Ella Appointments - Data being saved: ' . json_encode($data));

            $appointment_id = $this->input->post('appointment_id');
            
            if ($appointment_id) {
                // Update existing appointment
                if ($this->appointments_model->update_appointment($appointment_id, $data)) {
                    set_alert('success', 'Appointment updated successfully');
                } else {
                    set_alert('warning', 'Failed to update appointment');
                }
            } else {
                // Create new appointment
                $appointment_id = $this->appointments_model->create_appointment($data);
                if ($appointment_id) {
                    set_alert('success', 'Appointment created successfully');
                } else {
                    set_alert('warning', 'Failed to create appointment');
                }
            }

            // Handle attendees
            if ($appointment_id) {
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
        }

        redirect(admin_url('ella_contractors/appointments'));
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
     * Get upcoming appointments
     */
    public function upcoming()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Upcoming Appointments';
        $data['appointments'] = $this->appointments_model->get_upcoming_appointments();
        $this->load->view('appointments/upcoming', $data);
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
        
        // Debug: Log the ID being requested
        log_message('debug', 'Getting appointment data for ID: ' . $id);
        
        $appointment = $this->appointments_model->get_appointment($id);
        
        if ($appointment) {
            // Convert object to array
            $appointment_data = (array) $appointment;
            $appointment_data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
            
            // Debug: Log the appointment data
            log_message('debug', 'Appointment data: ' . json_encode($appointment_data));
            
            echo json_encode([
                'success' => true,
                'data' => $appointment_data
            ]);
        } else {
            log_message('debug', 'Appointment not found for ID: ' . $id);
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
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('start_hour', 'Start Time', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }

            // Debug: Log the status being saved via AJAX
            $status_value = $this->input->post('status');
            error_log('Ella Appointments AJAX - Status being saved: ' . $status_value);
            
            // Ensure appointment_status column exists
            $this->ensure_appointment_status_column();
            
            $data = [
                'subject' => $this->input->post('subject'),
                'description' => $this->input->post('description'),
                'date' => $this->input->post('date'),
                'start_hour' => $this->input->post('start_hour'),
                'contact_id' => $this->input->post('contact_id') ?: null,
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address' => $this->input->post('address'),
                'notes' => $this->input->post('notes'),
                'type_id' => $this->input->post('type_id') ?: 0,
                'appointment_status' => $status_value ?: 'scheduled',
                'source' => 'ella_contractor'
            ];
            
            // Debug: Log the complete data array
            error_log('Ella Appointments AJAX - Data being saved: ' . json_encode($data));

        // Debug: Log the data being sent
        log_message('debug', 'Appointment data: ' . json_encode($data));

        $appointment_id = $this->input->post('appointment_id');
        
        try {
            if ($appointment_id) {
                // Update existing appointment
                if ($this->appointments_model->update_appointment($appointment_id, $data)) {
                    // Handle attendees
                    $this->handle_attendees($appointment_id);
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
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment created successfully'
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

        $measurements = $this->measurements_model->get_related_measurements('appointment', $appointment_id);
        
        echo json_encode([
            'success' => true,
            'data' => $measurements
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
     * Save measurement for appointment (AJAX) - Using original measurements system
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
            $msg = $ok ? 'Measurement updated successfully' : 'Nothing changed';
        } else {
            $ok = (bool) $this->measurements_model->create($post);
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
                'description' => $this->input->post('description'),
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
}
