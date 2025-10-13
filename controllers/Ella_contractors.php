<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_media_model');
        $this->load->model('ella_line_items_model');
        $this->load->model('ella_line_item_groups_model');
        $this->load->model('ella_estimates_model');
        // Helper removed - presentation functionality disabled
        // $this->load->helper('ella_media');
    }
    
    /**
     * Main index method - redirects to admin dashboard
     */
    public function index() {
        redirect(admin_url());
    }

    // ==================== PRESENTATION FUNCTIONALITY REMOVED ====================
    // All presentation-related methods have been removed to streamline the module.
    // Appointment attachment functionality remains intact and fully functional.

    // ==================== LINE ITEMS MANAGEMENT ====================

    /**
     * Service Items Management
     */
    public function line_items()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $data['title'] = 'Service Items Management';
        $data['groups'] = $this->ella_line_item_groups_model->get_groups();
        $data['unit_types'] = $this->ella_line_items_model->get_unit_types();
        $data['line_items'] = $this->ella_line_items_model->get_line_items();
        
        $this->load->view('ella_contractors/line_items', $data);
    }

    /**
     * Create Service Item
     */
    public function create_line_item()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('group_id', 'Group', 'required|numeric');
        $this->form_validation->set_rules('unit_type', 'Unit Type', 'required');
        $this->form_validation->set_rules('cost', 'Cost', 'numeric');
        $this->form_validation->set_rules('quantity', 'Quantity', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'cost' => $this->input->post('cost') ?: null,
                'quantity' => $this->input->post('quantity') ?: 1.00,
                'unit_type' => $this->input->post('unit_type'),
                'group_id' => $this->input->post('group_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_result = $this->handle_line_item_image_upload();
                if ($upload_result) {
                    $data['image'] = $upload_result;
                }
            }

            $line_item_id = $this->ella_line_items_model->create_line_item($data);
            if ($line_item_id) {
                set_alert('success', 'Line item created successfully');
            } else {
                set_alert('warning', 'Failed to create line item');
            }
        }
        redirect(admin_url('ella_contractors/line_items'));
    }

    /**
     * Update Line Item
     */
    public function update_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('group_id', 'Group', 'required|numeric');
        $this->form_validation->set_rules('unit_type', 'Unit Type', 'required');
        $this->form_validation->set_rules('cost', 'Cost', 'numeric');
        $this->form_validation->set_rules('quantity', 'Quantity', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'cost' => $this->input->post('cost') ?: null,
                'quantity' => $this->input->post('quantity') ?: 1.00,
                'unit_type' => $this->input->post('unit_type'),
                'group_id' => $this->input->post('group_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_result = $this->handle_line_item_image_upload();
                if ($upload_result) {
                    $data['image'] = $upload_result;
                }
            }

            if ($this->ella_line_items_model->update_line_item($id, $data)) {
                set_alert('success', 'Line item updated successfully');
            } else {
                set_alert('warning', 'Failed to update line item');
            }
        }
        redirect(admin_url('ella_contractors/line_items'));
    }

    /**
     * Delete Line Item
     */
    public function delete_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        if ($this->ella_line_items_model->delete_line_item($id)) {
            set_alert('success', 'Line item deleted successfully');
        } else {
            set_alert('warning', 'Failed to delete line item');
        }
        redirect(admin_url('ella_contractors/line_items'));
    }

    /**
     * Toggle Line Item Active Status
     */
    public function toggle_line_item_active($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        if ($this->ella_line_items_model->toggle_active($id)) {
            set_alert('success', 'Line item status updated successfully');
        } else {
            set_alert('warning', 'Failed to update line item status');
        }
        redirect(admin_url('ella_contractors/line_items'));
    }


    /**
     * Handle Line Item Image Upload
     */
    private function handle_line_item_image_upload()
    {
        $upload_path = FCPATH . 'uploads/ella_line_items/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        } else {
            log_message('error', 'Line item image upload failed: ' . $this->upload->display_errors());
            return false;
        }
    }

    /**
     * Get Line Item Data for AJAX
     */
    public function get_line_item_data($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $line_item = $this->ella_line_items_model->get_line_item($id);
        if ($line_item) {
            echo json_encode($line_item);
        } else {
            echo json_encode(['error' => 'Line item not found']);
        }
    }

    // Table method removed - using direct view rendering

    /**
     * Manage Line Item (Add/Edit) - AJAX
     */
    public function manage_line_item()
    {
        if (has_permission('ella_contractors', '', 'view')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                if ($data['itemid'] == '') {
                    if (!has_permission('ella_contractors', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id = $this->ella_line_items_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('line_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item' => $this->ella_line_items_model->get($id),
                    ]);
                } else {
                    if (!has_permission('ella_contractors', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->ella_line_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('line_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    /**
     * Add Group
     */
    public function add_group()
    {
        if ($this->input->post() && has_permission('ella_contractors', '', 'create')) {
            $this->ella_line_item_groups_model->add_group($this->input->post());
            set_alert('success', _l('added_successfully', _l('item_group')));
        }
    }

    /**
     * Update Group
     */
    public function update_group($id)
    {
        if ($this->input->post() && has_permission('ella_contractors', '', 'edit')) {
            $this->ella_line_item_groups_model->edit_group($this->input->post(), $id);
            set_alert('success', _l('updated_successfully', _l('item_group')));
        }
    }

    /**
     * Delete Group
     */
    public function delete_group($id)
    {
        if (has_permission('ella_contractors', '', 'delete')) {
            if ($this->ella_line_item_groups_model->delete_group($id)) {
                set_alert('success', _l('deleted', _l('item_group')));
            }
        }
        redirect(admin_url('ella_contractors/line_items?groups_modal=true'));
    }

    /**
     * Bulk Actions
     */
    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_line_items');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $has_permission_delete = has_permission('ella_contractors', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->ella_line_items_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    // ==================== ESTIMATES MANAGEMENT ====================

    /**
     * Estimates Management
     */
    public function estimates()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();
        
        $this->load->model('leads_model');
        $data['leads'] = $this->leads_model->get();
        $data['line_items'] = $this->ella_line_items_model->get_line_items(null, true);

        $data['title'] = 'Estimates Management';
        $data['estimates'] = $this->ella_estimates_model->get_estimates();
        $data['statuses'] = $this->ella_estimates_model->get_statuses();
        
        $this->load->view('ella_contractors/estimates', $data);
    }

    /**
     * Create Estimate
     */
    public function create_estimate()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('estimate_name', 'Estimate Name', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'estimate_name' => $this->input->post('estimate_name'),
                'description' => $this->input->post('description'),
                'client_id' => $this->input->post('client_id') ?: null,
                'lead_id' => $this->input->post('lead_id') ?: null,
                'status' => $this->input->post('status')
            ];

            $estimate_id = $this->ella_estimates_model->create_estimate($data);
            if ($estimate_id) {
                // Add line items if provided
                $line_items = $this->input->post('line_items');
                if ($line_items && is_array($line_items)) {
                    foreach ($line_items as $item) {
                        if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                            $this->ella_estimates_model->add_line_item_to_estimate(
                                $estimate_id,
                                $item['line_item_id'],
                                $item['quantity'],
                                $item['unit_price']
                            );
                        }
                    }
                }
                
                set_alert('success', 'Estimate created successfully');
                redirect(admin_url('ella_contractors/estimates'));
            } else {
                set_alert('warning', 'Failed to create estimate');
            }
        }
        redirect(admin_url('ella_contractors/estimates'));
    }

    /**
     * Update Estimate
     */
    public function update_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('estimate_name', 'Estimate Name', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'estimate_name' => $this->input->post('estimate_name'),
                'description' => $this->input->post('description'),
                'client_id' => $this->input->post('client_id') ?: null,
                'lead_id' => $this->input->post('lead_id') ?: null,
                'status' => $this->input->post('status')
            ];

            if ($this->ella_estimates_model->update_estimate($id, $data)) {
                set_alert('success', 'Estimate updated successfully');
            } else {
                set_alert('warning', 'Failed to update estimate');
            }
        }
        redirect(admin_url('ella_contractors/estimates'));
    }

    /**
     * Delete Estimate
     */
    public function delete_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        if ($this->ella_estimates_model->delete_estimate($id)) {
            set_alert('success', 'Estimate deleted successfully');
        } else {
            set_alert('warning', 'Failed to delete estimate');
        }
        redirect(admin_url('ella_contractors/estimates'));
    }

    /**
     * View Estimate Details
     */
    public function view_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Estimate Details';
        $data['estimate'] = $this->ella_estimates_model->get_estimate($id);
        $data['estimate_line_items'] = $this->ella_estimates_model->get_estimate_line_items($id);
        $data['line_items'] = $this->ella_line_items_model->get_line_items(null, true);
        $data['statuses'] = $this->ella_estimates_model->get_statuses();
        
        if (!$data['estimate']) {
            show_404();
        }

        $this->load->view('ella_contractors/view_estimate', $data);
    }

    /**
     * Add Line Item to Estimate
     */
    public function add_line_item_to_estimate()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $estimate_id = $this->input->post('estimate_id');
        $line_items = $this->input->post('line_items');

        $added_count = 0;
        if ($line_items && is_array($line_items)) {
            foreach ($line_items as $item) {
                if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                    if ($this->ella_estimates_model->add_line_item_to_estimate(
                        $estimate_id,
                        $item['line_item_id'],
                        $item['quantity'],
                        $item['unit_price']
                    )) {
                        $added_count++;
                    }
                }
            }
        }

        if ($added_count > 0) {
            // Update totals
            $this->ella_estimates_model->update_estimate_totals($estimate_id);
            set_alert('success', $added_count . ' line item(s) added to estimate successfully');
        } else {
            set_alert('warning', 'Failed to add line items to estimate');
        }

        redirect(admin_url('ella_contractors/view_estimate/' . $estimate_id));
    }

    /**
     * Update Line Item in Estimate
     */
    public function update_estimate_line_item()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $id = $this->input->post('id');
        $quantity = $this->input->post('quantity');
        $unit_price = $this->input->post('unit_price');

        if ($this->ella_estimates_model->update_estimate_line_item($id, $quantity, $unit_price)) {
            set_alert('success', 'Line item updated successfully');
        } else {
            set_alert('warning', 'Failed to update line item');
        }

        $estimate_id = $this->input->post('estimate_id');
        redirect(admin_url('ella_contractors/view_estimate/' . $estimate_id));
    }

    /**
     * Remove Line Item from Estimate
     */
    public function remove_estimate_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        // Get estimate_id before deletion
        $this->db->select('estimate_id');
        $this->db->where('id', $id);
        $result = $this->db->get(db_prefix() . 'ella_contractor_estimate_line_items')->row();

        if ($this->ella_estimates_model->remove_line_item_from_estimate($id)) {
            set_alert('success', 'Line item removed from estimate successfully');
        } else {
            set_alert('warning', 'Failed to remove line item from estimate');
        }

        if ($result) {
            redirect(admin_url('ella_contractors/view_estimate/' . $result->estimate_id));
        } else {
            redirect(admin_url('ella_contractors/estimates'));
        }
    }

    /**
     * Get Line Item Data for AJAX
     */
    public function get_line_item_for_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $line_item = $this->ella_line_items_model->get_line_item($id);
        if ($line_item) {
            echo json_encode($line_item);
        } else {
            echo json_encode(['error' => 'Line item not found']);
        }
    }

    /**
     * Manage Estimate (Add/Edit) - AJAX
     */
    public function manage_estimate()
    {
        if (has_permission('ella_contractors', '', 'view')) {
            if ($this->input->post()) {
                try {
                    $data = $this->input->post();
                    
                    // Debug logging
                    log_message('debug', 'Manage estimate data: ' . json_encode($data));
                if ($data['estimate_id'] == '') {
                    if (!has_permission('ella_contractors', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    
                    // Remove line_items from main data
                    $line_items = isset($data['line_items']) ? $data['line_items'] : [];
                    unset($data['line_items']);
                    
                    $id = $this->ella_estimates_model->create_estimate($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        // Add line items if provided
                        if ($line_items && is_array($line_items)) {
                            foreach ($line_items as $item) {
                                if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                                    $this->ella_estimates_model->add_line_item_to_estimate(
                                        $id,
                                        $item['line_item_id'],
                                        $item['quantity'],
                                        $item['unit_price']
                                    );
                                }
                            }
                        }
                        
                        $success = true;
                        $message = _l('added_successfully', _l('estimate'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'estimate' => $this->ella_estimates_model->get_estimate($id),
                    ]);
                } else {
                    if (!has_permission('ella_contractors', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    
                    // Extract line_items before removing from main data
                    $line_items = isset($data['line_items']) ? $data['line_items'] : [];
                    unset($data['line_items']);
                    
                    $success = $this->ella_estimates_model->update_estimate($data['estimate_id'], $data);
                    $message = '';
                    if ($success) {
                        // Delete existing line items
                        $this->db->where('estimate_id', $data['estimate_id']);
                        $this->db->delete(db_prefix() . 'ella_contractor_estimate_line_items');
                        
                        // Add posted line items
                        if (is_array($line_items)) {
                            foreach ($line_items as $item) {
                                if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                                    $this->ella_estimates_model->add_line_item_to_estimate(
                                        $data['estimate_id'],
                                        $item['line_item_id'],
                                        $item['quantity'],
                                        $item['unit_price']
                                    );
                                }
                            }
                        }
                        
                        // Update totals
                        $this->ella_estimates_model->update_estimate_totals($data['estimate_id']);
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'debug' => [
                            'estimate_id' => $data['estimate_id'],
                            'line_items_count' => count($line_items)
                        ]
                    ]);
                }
                } catch (Exception $e) {
                    log_message('error', 'Estimate management error: ' . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Get Estimate Data for AJAX
     */
    public function get_estimate_data($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $estimate = (array) $this->ella_estimates_model->get_estimate($id);
        $estimate['line_items'] = $this->ella_estimates_model->get_estimate_line_items($id);
        echo json_encode($estimate);
    }
    
    /**
     * DataTable server-side processing for estimates
     */
    public function estimates_table()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('ella_contractor_estimates');
    }

    /**
     * Get Estimates Data for AJAX
     */
    public function get_estimates_ajax()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $estimates = $this->ella_estimates_model->get_estimates();
        echo json_encode($estimates);
    }

    /**
     * Estimates Bulk Actions
     */
    public function estimates_bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_estimates');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $has_permission_delete = has_permission('ella_contractors', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->ella_estimates_model->delete_estimate($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    public function get_line_items_ajax()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }
        
        $line_items = $this->ella_line_items_model->get_line_items(null, true);        
        // Debug logging
        log_message('debug', 'Line items count: ' . count($line_items));
        if (!empty($line_items)) {
            log_message('debug', 'First line item: ' . json_encode($line_items[0]));
        }
        
        $data = [];
        foreach($line_items as $item) {
            $data[] = [
                'id' => $item['id'],
                'name' => htmlspecialchars($item['name']) . ' - $' . number_format($item['cost'], 2),
                'cost' => $item['cost'],
                'unit_price' => $item['cost'],
                'description' => $item['description'] ?? '',
                'unit_type' => $item['unit_type'] ?? ''
            ];
        }
        
        // Debug logging
        log_message('debug', 'Data count: ' . count($data));
        if (!empty($data)) {
            log_message('debug', 'First data item: ' . json_encode($data[0]));
        }
        
        // Return in the format expected by the sample function
        $response = [
            'success' => true,
            'data' => $data,
            'message' => 'Line items loaded successfully'
        ];
        
        // Set proper content type
        header('Content-Type: application/json');
        echo json_encode($response);
    }

}