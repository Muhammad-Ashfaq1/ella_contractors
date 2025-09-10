<?php defined('BASEPATH') or exit('No direct script access allowed');

class Estimates extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/ella_estimates_model');
        $this->load->model('ella_contractors/ella_line_items_model');
        $this->load->model('clients_model');
        $this->load->model('leads_model');
    }

    /**
     * Estimates listing page
     */
    public function index()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Estimates Management';
        $data['clients'] = $this->clients_model->get();
        $data['leads'] = $this->leads_model->get();
        $data['line_items'] = $this->ella_line_items_model->get_line_items(null, true);
        $data['statuses'] = $this->ella_estimates_model->get_statuses();
        
        $this->load->view('ella_contractors/estimates', $data);
    }

    /**
     * DataTable server-side processing for estimates
     */
    public function table()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        // Simple test response first
        echo json_encode([
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
        return;
        
        $this->app->get_table_data('ella_contractor_estimates');
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
     * Manage Estimate (Add/Edit) - AJAX
     */
    public function manage_estimate()
    {
        if (has_permission('ella_contractors', '', 'view')) {
            if ($this->input->post()) {
                try {
                    $data = $this->input->post();

                    // die(json_encode($data));
                    
                    if (empty($data['estimate_id'])) {
                        if (!has_permission('ella_contractors', '', 'create')) {
                            header('HTTP/1.0 400 Bad error');
                            echo _l('access_denied');
                            die;
                        }
                        
                        // Remove line_items from main data
                        $line_items = isset($data['line_items']) ? $data['line_items'] : [];
                        unset($data['line_items']);
                        unset($data['estimate_id']);
                        
                        $id = $this->ella_estimates_model->create_estimate($data);

                        $success = false;
                        $message = '';
                        if ($id) {
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
                    } 
                    
                    else {
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
}
