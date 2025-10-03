<?php defined('BASEPATH') or exit('No direct script access allowed');

class Measurements extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Measurements_model', 'measurements_model');
    }

    public function index()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($this->measurements_model->list_all($this->input->get()));
            return;
        }

        $data['title'] = 'All Measurements';
        $data['measurements_model'] = $this->measurements_model;
        $this->load->view('ella_contractors/measurements/list', $data);
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
        } else {
            $ok  = (bool) $this->measurements_model->create($post);
            $msg = $ok ? 'Created successfully' : 'Failed to create';
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

    public function create()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }
        
        // Get leads and clients for dropdowns
        $this->load->model('leads_model');
        $this->load->model('clients_model');
        
        $data['title'] = 'Add Measurements';
        $data['category'] = 'siding';
        $data['row'] = null;
        $data['all_measurements'] = [];
        $data['leads'] = $this->leads_model->get();
        $data['clients'] = $this->clients_model->get();
        
        $this->load->view('ella_contractors/measurements/form', $data);
    }

    public function edit($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $row = $this->measurements_model->find($id);
        if (!$row) {
            show_404();
        }

        // Get all measurements for the same lead/client
        $all_measurements = $this->measurements_model->get_related_measurements($row['rel_type'], $row['rel_id']);
        
        // Get leads and clients for dropdowns
        $this->load->model('leads_model');
        $this->load->model('clients_model');
        
        $data['title'] = 'Edit Measurements';
        $data['category'] = $row['category'] ?? 'siding';
        $data['row'] = $row;
        $data['all_measurements'] = $all_measurements;
        $data['leads'] = $this->leads_model->get();
        $data['clients'] = $this->clients_model->get();
        
        $this->load->view('ella_contractors/measurements/form', $data);
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

        $ok = $this->measurements_model->delete($id);
        
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
     * Get measurements by category for AJAX
     */
    public function get_measurements_by_category($category)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $params = [];
        if ($this->input->get('rel_type') && $this->input->get('rel_id')) {
            $params['rel_type'] = $this->input->get('rel_type');
            $params['rel_id'] = $this->input->get('rel_id');
        }

        $result = $this->measurements_model->list($category, $params);
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Get all measurements for AJAX
     */
    public function get_all_measurements()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $params = [];
        if ($this->input->get('rel_type') && $this->input->get('rel_id')) {
            $params['rel_type'] = $this->input->get('rel_type');
            $params['rel_id'] = $this->input->get('rel_id');
        }

        $result = $this->measurements_model->list_all($params);
        
        header('Content-Type: application/json');
        echo json_encode($result);
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
