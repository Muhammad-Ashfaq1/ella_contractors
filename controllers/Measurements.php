<?php defined('BASEPATH') or exit('No direct script access allowed');

class Measurements extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Measurements_model', 'measurements_model');
        $this->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
    }


    public function save()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $post = $this->input->post(null, true);
        $id   = isset($post['id']) ? (int) $post['id'] : 0;
        
        // Handle relationships (appointment, lead, or other)
        if (isset($post['appointment_id']) && !empty($post['appointment_id'])) {
            $post['rel_type'] = 'appointment';
            $post['rel_id'] = (int) $post['appointment_id'];
        } elseif (isset($post['lead_id']) && !empty($post['lead_id'])) {
            $post['rel_type'] = 'lead';
            $post['rel_id'] = (int) $post['lead_id'];
        } else {
            $post['rel_type'] = 'appointment'; // Default to appointment if no specific type
            $post['rel_id'] = isset($post['appointment_id']) ? (int) $post['appointment_id'] : 0;
        }

        // Handle client name (store in notes or attributes if needed)
        if (isset($post['client_name']) && !empty($post['client_name'])) {
            $clientName = $post['client_name'];
            // Store client name in notes or create a separate field
            if (empty($post['notes'])) {
                $post['notes'] = 'Client: ' . $clientName;
            } else {
                $post['notes'] = $post['notes'] . ' | Client: ' . $clientName;
            }
        }

        // Handle basic measurement fields
        $width  = (float) ($post['width_val'] ?? 0);
        $height = (float) ($post['height_val'] ?? 0);
        if ($width && $height) {
            if (!isset($post['united_inches_val']) || $post['united_inches_val'] === '') {
                $post['united_inches_val'] = $width + $height;
            }
            if (!isset($post['area_val']) || $post['area_val'] === '') {
                $lenU  = $post['length_unit'] ?? 'in';
                $areaU = $post['area_unit'] ?? 'sqft';
                if ($lenU === 'in' && $areaU === 'sqft') {
                    $post['area_val'] = ($width * $height) / 144.0;
                }
            }
        }

        // Handle category-specific attributes
        $categorySpecificData = [];
        $categories = ['siding', 'roofing', 'windows', 'doors'];
        
        // Check for bulk data first (from Windows/Doors saves)
        if (isset($post['bulk']) && is_array($post['bulk'])) {
            log_message('debug', 'Bulk data received: ' . json_encode($post['bulk']));
            $categorySpecificData = $post['bulk'];
            unset($post['bulk']);
        } else {
            // Check for individual category data
            foreach ($categories as $category) {
                if (isset($post[$category]) && is_array($post[$category])) {
                    $categorySpecificData[$category] = $post[$category];
                    unset($post[$category]);
                }
            }
            
            // Handle new siding and roofing measurements structure
            if (isset($post['measurements']) && is_array($post['measurements'])) {
                $sidingMeasurements = [];
                foreach ($post['measurements'] as $measurement) {
                    if (!empty($measurement['name']) && !empty($measurement['value']) && !empty($measurement['unit'])) {
                        $sidingMeasurements[] = [
                            'name' => $measurement['name'],
                            'value' => (float) $measurement['value'],
                            'unit' => $measurement['unit']
                        ];
                    }
                }
                if (!empty($sidingMeasurements)) {
                    $categorySpecificData['siding_measurements'] = $sidingMeasurements;
                }
                unset($post['measurements']);
            }
            
            if (isset($post['measurements_roofing']) && is_array($post['measurements_roofing'])) {
                $roofingMeasurements = [];
                foreach ($post['measurements_roofing'] as $measurement) {
                    if (!empty($measurement['name']) && !empty($measurement['value']) && !empty($measurement['unit'])) {
                        $roofingMeasurements[] = [
                            'name' => $measurement['name'],
                            'value' => (float) $measurement['value'],
                            'unit' => $measurement['unit']
                        ];
                    }
                }
                if (!empty($roofingMeasurements)) {
                    $categorySpecificData['roofing_measurements'] = $roofingMeasurements;
                }
                unset($post['measurements_roofing']);
            }
        }

        // Merge with existing attributes_json if editing
        if ($id > 0) {
            $existing = $this->measurements_model->find($id);
            $existing_attributes = json_decode($existing['attributes_json'] ?? '{}', true);
            $post['attributes_json'] = json_encode(array_merge($existing_attributes, $categorySpecificData));
            log_message('debug', 'Updated attributes_json: ' . $post['attributes_json']);
        } else {
            $post['attributes_json'] = json_encode($categorySpecificData);
            log_message('debug', 'New attributes_json: ' . $post['attributes_json']);
        }
        
        // Set required fields for the database
        if (empty($post['name'])) {
            $post['name'] = 'Combined Measurement';
        }
        if (empty($post['category'])) {
            $post['category'] = 'other';
        }
        if (empty($post['status_code'])) {
            $post['status_code'] = 1;
        }
        if (empty($post['sort_order'])) {
            $post['sort_order'] = 0;
        }
        if (empty($post['intRecordStatusCode'])) {
            $post['intRecordStatusCode'] = 1;
        }
        
        // Debug: Log the final data being saved
        log_message('debug', 'Final data being saved: ' . json_encode($post));

        if ($id > 0) {
            $ok  = $this->measurements_model->update($id, $post);
            $msg = $ok ? 'Updated successfully' : 'Nothing changed';
            
            // Log measurement update if successful and it's for an appointment
            if ($ok && isset($post['appointment_id']) && $post['appointment_id']) {
                $measurement_name = $post['name'] ?? 'Measurement';
                $this->appointments_model->log_activity($post['appointment_id'], 'updated', 'measurement', $measurement_name, ['measurement_id' => $id, 'category' => $post['category'] ?? 'general']);
            }
        } else {
            // Debug: Log what we're trying to insert
            log_message('debug', 'Attempting to create measurement with data: ' . json_encode($post));
            
            // Try direct database insert first
            $insert_data = [
                'category' => $post['category'] ?? 'other',
                'rel_type' => $post['rel_type'] ?? 'appointment',
                'rel_id' => $post['rel_id'] ?? $post['appointment_id'] ?? 0,
                'appointment_id' => $post['appointment_id'] ?? 0,
                'name' => $post['name'] ?? 'Combined Measurement',
                'attributes_json' => $post['attributes_json'] ?? '{}',
                'status_code' => $post['status_code'] ?? 1,
                'sort_order' => $post['sort_order'] ?? 0,
                'intRecordStatusCode' => $post['intRecordStatusCode'] ?? 1,
                'dtmCreated' => date('Y-m-d H:i:s')
            ];
            
            log_message('debug', 'Direct insert data: ' . json_encode($insert_data));
            
            $this->db->insert(db_prefix() . 'ella_contractors_measurements', $insert_data);
            $measurement_id = $this->db->insert_id();
            $ok = (bool) $measurement_id;
            $msg = $ok ? 'Created successfully' : 'Failed to create';
            
            // Debug: Log the result
            log_message('debug', 'Direct insert result: ' . ($ok ? 'SUCCESS' : 'FAILED') . ', ID: ' . $measurement_id);
            if (!$ok) {
                log_message('error', 'Direct insert error: ' . $this->db->last_query());
                log_message('error', 'Direct insert error message: ' . $this->db->error()['message']);
                log_message('error', 'Full direct insert error: ' . json_encode($this->db->error()));
            }
            
            // Log measurement creation if successful and it's for an appointment
            if ($ok && isset($post['appointment_id']) && $post['appointment_id']) {
                $measurement_name = $post['name'] ?? 'Measurement';
                $this->appointments_model->log_activity($post['appointment_id'], 'created', 'measurement', $measurement_name, ['measurement_id' => $measurement_id, 'category' => $post['category'] ?? 'general']);
            }
        }

        // Handle AJAX requests
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            
            if ($ok) {
                $measurement = $this->measurements_model->find($id ?: $this->db->insert_id());
                $response_data = $measurement;
                
                // If this is a bulk save (Windows/Doors), return the attributes in the expected format
                if (isset($post['bulk']) && is_array($post['bulk'])) {
                    $attributes = json_decode($measurement['attributes_json'] ?? '{}', true);
                    $response_data = [
                        'id' => $measurement['id'],
                        'attributes' => $attributes
                    ];
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => $msg,
                    'data' => $response_data
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $msg,
                    'data' => null
                ]);
            }
            return;
        }

        set_alert($ok ? 'success' : 'danger', $msg);
        $redirectCategory = ($post['category'] === 'combined') ? 'siding' : ($post['category'] ?? 'siding');
        redirect(admin_url('ella_contractors/measurements/' . $redirectCategory));
    }


    public function delete($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            if ($this->input->is_ajax_request()) {
                ajax_access_denied();
            } else {
                access_denied('ella_contractors');
            }
        }

        // Get measurement details before deleting for logging
        $measurement = $this->measurements_model->find($id);
        
        $ok = $this->measurements_model->delete($id);
        
        // Log measurement deletion if successful and it's for an appointment
        if ($ok && $measurement && isset($measurement['appointment_id']) && $measurement['appointment_id']) {
            $measurement_name = $measurement['name'] ?? 'Measurement';
            $this->appointments_model->log_activity($measurement['appointment_id'], 'deleted', 'measurement', $measurement_name, ['measurement_id' => $id, 'category' => $measurement['category'] ?? 'general']);
        }
        
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $ok,
                'message' => $ok ? 'Measurement deleted successfully' : 'Measurement not found'
            ]);
            return;
        }
        
        set_alert($ok ? 'success' : 'danger', $ok ? 'Deleted' : 'Not found');
        redirect($_SERVER['HTTP_REFERER'] ?? admin_url('ella_contractors/measurements'));
    }


    /**
     * Get single measurement for AJAX
     */
    public function get_measurement($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $measurement = $this->measurements_model->find($id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $measurement ? true : false,
            'data' => $measurement
        ]);
    }


    /**
     * Get measurements for appointment (AJAX) - Return original structure for listing
     */
    public function get_appointment_measurements($appointment_id)
    {
        log_message('debug', 'Getting measurements for appointment: ' . $appointment_id);
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        // Get measurements using the existing table structure
        $this->db->select('id, name, category, rel_type, rel_id, appointment_id, attributes_json, dtmCreated as dateadded');
        $this->db->from(db_prefix() . 'ella_contractors_measurements');
        $this->db->where('(appointment_id = ' . (int) $appointment_id . ' OR (rel_type = "appointment" AND rel_id = ' . (int) $appointment_id . '))');
        $this->db->order_by('dtmCreated', 'DESC');
        
        $measurements = $this->db->get()->result_array();
        
        // Process measurements to ensure proper structure
        foreach ($measurements as &$measurement) {
            // Ensure attributes_json is properly decoded
            if (!empty($measurement['attributes_json'])) {
                $measurement['attributes'] = json_decode($measurement['attributes_json'], true);
            } else {
                $measurement['attributes'] = [];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $measurements
        ]);
    }


    /**
     * Save measurement via AJAX (for modals)
     */
    public function save_measurement_ajax()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        $post = $this->input->post(null, true);
        $id = isset($post['id']) ? (int) $post['id'] : 0;

        // Handle lead relationship
        if (isset($post['lead_id']) && !empty($post['lead_id'])) {
            $post['rel_type'] = 'lead';
            $post['rel_id'] = (int) $post['lead_id'];
        } else {
            $post['rel_type'] = 'other';
            $post['rel_id'] = 0;
        }

        // Handle client name (store in notes or attributes if needed)
        if (isset($post['client_name']) && !empty($post['client_name'])) {
            $clientName = $post['client_name'];
            if (empty($post['notes'])) {
                $post['notes'] = 'Client: ' . $clientName;
            } else {
                $post['notes'] = $post['notes'] . ' | Client: ' . $clientName;
            }
        }

        // Handle basic measurement fields
        $width = (float) ($post['width'] ?? 0);
        $height = (float) ($post['height'] ?? 0);
        if ($width && $height) {
            if (!isset($post['united_inches_val']) || $post['united_inches_val'] === '') {
                $post['united_inches_val'] = $width + $height;
            }
            if (!isset($post['area_val']) || $post['area_val'] === '') {
                $post['area_val'] = ($width * $height) / 144.0; // Convert to sqft
            }
        }

        // Set category based on the form type
        if (isset($post['form_type'])) {
            $post['category'] = $post['form_type'];
        }

        // Set default values for required fields
        $post['designator'] = $post['designator'] ?? '';
        $post['name'] = $post['name'] ?? 'Unnamed ' . ucfirst($post['category'] ?? 'item');
        $post['location_label'] = $post['location'] ?? '';
        $post['level_label'] = $post['level'] ?? '';
        $post['width_val'] = $width;
        $post['height_val'] = $height;
        $post['quantity'] = $post['quantity'] ?? 1;
        $post['length_unit'] = 'in';
        $post['area_unit'] = 'sqft';
        $post['ui_unit'] = 'in';

        if ($id > 0) {
            $ok = $this->measurements_model->update($id, $post);
            $msg = $ok ? 'Updated successfully' : 'Nothing changed';
        } else {
            $ok = (bool) $this->measurements_model->create($post);
            $msg = $ok ? 'Created successfully' : 'Failed to create';
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $msg,
            'data' => $ok ? $this->measurements_model->find($id ?: $this->db->insert_id()) : null
        ]);
    }
}
