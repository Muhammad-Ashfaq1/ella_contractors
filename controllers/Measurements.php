<?php defined('BASEPATH') or exit('No direct script access allowed');

class Measurements extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Measurements_model', 'measurements_model');
        $this->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
    }

    /**
     * Save measurement with dynamic tabs - Generic handler
     */
    public function save()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        $post = $this->input->post(null, true);
        $record_id = isset($post['id']) ? (int) $post['id'] : 0;
        
        // Extract relationship data
        $appointment_id = isset($post['appointment_id']) ? (int) $post['appointment_id'] : 0;
        $rel_type = isset($post['rel_type']) ? $post['rel_type'] : 'appointment';
        $rel_id = isset($post['rel_id']) ? (int) $post['rel_id'] : $appointment_id;
        $org_id = isset($post['org_id']) ? (int) $post['org_id'] : null;
        
        // Collect all tab data dynamically
        $tabs_data = [];
        foreach ($post as $key => $value) {
            // Match pattern: tab_measurements_[tab_id] (e.g., tab_measurements_custom1)
            if (preg_match('/^tab_measurements_(.+)$/', $key, $matches) && is_array($value)) {
                $tab_id = $matches[1];
                $tab_name = isset($post['tab_name_' . $tab_id]) ? $post['tab_name_' . $tab_id] : ucfirst($tab_id);
                
                $measurements = [];
                foreach ($value as $index => $measurement) {
                    if (!empty($measurement['name']) && !empty($measurement['unit']) && isset($measurement['value'])) {
                        $measurements[] = [
                            'name' => $measurement['name'],
                            'value' => (float) $measurement['value'],
                            'unit' => $measurement['unit'],
                            'sort_order' => $index
                        ];
                    }
                }
                
                if (!empty($measurements)) {
                    $tabs_data[] = [
                        'tab_name' => $tab_name,
                        'measurements' => $measurements
                    ];
                }
            }
        }
        
        // Validation
        if (empty($tabs_data)) {
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please add at least one measurement in any tab'
                ]);
                return;
            }
            set_alert('danger', 'Please add at least one measurement');
            redirect($_SERVER['HTTP_REFERER'] ?? admin_url('ella_contractors/appointments'));
            return;
        }
        
        $this->db->trans_start();
        
        try {
            if ($record_id > 0) {
                // Update: Delete old items and create new ones
                // First, get all existing measurement record IDs for this measurement
                $existing_records = $this->db->where('id', $record_id)
                    ->or_where('appointment_id', $appointment_id)
                    ->get(db_prefix() . 'ella_contractor_measurement_records')
                    ->result_array();
                
                foreach ($existing_records as $rec) {
                    // Delete items
                    $this->db->where('measurement_record_id', $rec['id'])
                        ->delete(db_prefix() . 'ella_contractor_measurement_items');
                }
                
                // Delete records
                $this->db->where('id', $record_id)
                    ->or_where('appointment_id', $appointment_id)
                    ->delete(db_prefix() . 'ella_contractor_measurement_records');
            }
            
            // Insert new records and items
            foreach ($tabs_data as $tab_data) {
                $record_data = [
                    'rel_type' => $rel_type,
                    'rel_id' => $rel_id,
                    'org_id' => $org_id,
                    'appointment_id' => $appointment_id,
                    'tab_name' => $tab_data['tab_name'],
                    'created_by' => get_staff_user_id()
                ];
                
                $this->db->insert(db_prefix() . 'ella_contractor_measurement_records', $record_data);
                $new_record_id = $this->db->insert_id();
                
                // Insert measurement items
                foreach ($tab_data['measurements'] as $measurement) {
                    $item_data = [
                        'measurement_record_id' => $new_record_id,
                        'name' => $measurement['name'],
                        'value' => $measurement['value'],
                        'unit' => $measurement['unit'],
                        'sort_order' => $measurement['sort_order']
                    ];
                    
                    $this->db->insert(db_prefix() . 'ella_contractor_measurement_items', $item_data);
                }
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction failed');
            }
            
            // Log activity
            if ($appointment_id) {
                $action = $record_id > 0 ? 'updated' : 'created';
                $this->appointments_model->log_activity($appointment_id, $action, 'measurement', 'Measurements', ['tab_count' => count($tabs_data)]);
            }
            
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => true,
                    'message' => $record_id > 0 ? 'Measurements updated successfully' : 'Measurements created successfully'
                ]);
            } else {
                set_alert('success', $record_id > 0 ? 'Updated successfully' : 'Created successfully');
                redirect($_SERVER['HTTP_REFERER'] ?? admin_url('ella_contractors/appointments'));
            }
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Measurement save error: ' . $e->getMessage());
            
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            } else {
                set_alert('danger', 'Error: ' . $e->getMessage());
                redirect($_SERVER['HTTP_REFERER'] ?? admin_url('ella_contractors/appointments'));
            }
        }
    }


    /**
     * Delete measurement record and all its items
     */
    public function delete($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
                ajax_access_denied();
        }

        // Get record details before deleting for logging
        $record = $this->db->where('id', $id)
            ->get(db_prefix() . 'ella_contractor_measurement_records')
            ->row_array();
        
        if (!$record) {
            echo json_encode([
                'success' => false,
                'message' => 'Measurement record not found'
            ]);
            return;
        }
        
        // Delete items first (CASCADE will do this automatically, but being explicit)
        $this->db->where('measurement_record_id', $id)
            ->delete(db_prefix() . 'ella_contractor_measurement_items');
        
        // Delete record
        $ok = $this->db->where('id', $id)
            ->delete(db_prefix() . 'ella_contractor_measurement_records');
        
        // Log deletion
        if ($ok && isset($record['appointment_id']) && $record['appointment_id']) {
            $this->appointments_model->log_activity(
                $record['appointment_id'], 
                'deleted', 
                'measurement', 
                $record['tab_name'] ?? 'Measurement',
                ['record_id' => $id]
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Measurement deleted successfully' : 'Failed to delete measurement'
        ]);
    }

    /**
     * Get measurement record with items for editing
     */
    public function get_measurement($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        // Get record
        $record = $this->db->where('id', $id)
            ->get(db_prefix() . 'ella_contractor_measurement_records')
            ->row_array();
        
        if (!$record) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Record not found'
            ]);
            return;
        }
        
        // Get items for this record
        $items = $this->db->where('measurement_record_id', $id)
            ->order_by('sort_order', 'ASC')
            ->get(db_prefix() . 'ella_contractor_measurement_items')
            ->result_array();
        
        $record['items'] = $items;
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $record
        ]);
    }

    /**
     * Get all measurements for appointment - organized by tabs
     */
    public function get_appointment_measurements($appointment_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        // Get all records for this appointment
        $records = $this->db->where('appointment_id', $appointment_id)
            ->order_by('created_at', 'DESC')
            ->get(db_prefix() . 'ella_contractor_measurement_records')
            ->result_array();
        
        // Get items for each record
        foreach ($records as &$record) {
            $items = $this->db->where('measurement_record_id', $record['id'])
                ->order_by('sort_order', 'ASC')
                ->get(db_prefix() . 'ella_contractor_measurement_items')
                ->result_array();
            
            $record['items'] = $items;
            $record['items_count'] = count($items);
            
            // Format created_at date using CRM's date format
            $record['formatted_date'] = _d($record['created_at']);
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * Check if category name already exists for this appointment
     * Used for validation during measurement creation/editing
     */
    public function check_duplicate_category()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $appointment_id = $this->input->post('appointment_id', true);
        $category_name = $this->input->post('category_name', true);
        $measurement_id = $this->input->post('measurement_id', true); // For edit mode
        
        if (empty($appointment_id) || empty($category_name)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'duplicate' => false,
                'message' => 'Invalid parameters'
            ]);
            return;
        }
        
        // Check if category name exists in other measurements for this appointment
        $this->db->where('appointment_id', $appointment_id);
        $this->db->where('LOWER(tab_name)', strtolower($category_name));
        
        // Exclude current measurement if editing
        if (!empty($measurement_id) && $measurement_id > 0) {
            $this->db->where('id !=', $measurement_id);
        }
        
        $existing = $this->db->get(db_prefix() . 'ella_contractor_measurement_records')->row();
        
        $is_duplicate = !empty($existing);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => !$is_duplicate,
            'duplicate' => $is_duplicate,
            'message' => $is_duplicate ? 'Category name already exists' : 'Category name is available'
        ]);
    }
}
