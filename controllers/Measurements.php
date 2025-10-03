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
            $post['rel_type'] = 'other';
            $post['rel_id'] = 0;
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

        if ($id > 0) {
            $ok  = $this->measurements_model->update($id, $post);
            $msg = $ok ? 'Updated successfully' : 'Nothing changed';
            
            // Log measurement update if successful and it's for an appointment
            if ($ok && isset($post['appointment_id']) && $post['appointment_id']) {
                $measurement_name = $post['name'] ?? 'Measurement';
                $this->appointments_model->log_activity($post['appointment_id'], 'updated', 'measurement', $measurement_name, ['measurement_id' => $id, 'category' => $post['category'] ?? 'general']);
            }
        } else {
            $measurement_id = $this->measurements_model->create($post);
            $ok = (bool) $measurement_id;
            $msg = $ok ? 'Created successfully' : 'Failed to create';
            
            // Log measurement creation if successful and it's for an appointment
            if ($ok && isset($post['appointment_id']) && $post['appointment_id']) {
                $measurement_name = $post['name'] ?? 'Measurement';
                $this->appointments_model->log_activity($post['appointment_id'], 'created', 'measurement', $measurement_name, ['measurement_id' => $measurement_id, 'category' => $post['category'] ?? 'general']);
            }
        }

        // Handle AJAX requests
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $ok,
                'message' => $msg,
                'data' => $ok ? $this->measurements_model->find($id ?: $this->db->insert_id()) : null
            ]);
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
