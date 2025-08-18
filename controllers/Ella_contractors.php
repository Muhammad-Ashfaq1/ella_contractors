<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_contractors_model');
        $this->load->helper('url');
        $this->load->language('ella_contractors', 'english');
    }
    
    /**
     * Dashboard - Main entry point
     */
    public function index() {
        $data['title'] = 'Ella Contractors Dashboard';
        
        // Get summary statistics
        $data['total_contractors'] = $this->ella_contractors_model->getContractorsCount();
        $data['active_contracts'] = $this->ella_contractors_model->getActiveContractsCount();
        $data['pending_payments'] = $this->ella_contractors_model->getPendingPaymentsCount();
        $data['active_projects'] = $this->ella_contractors_model->getActiveProjectsCount();
        
        // Get recent activities
        $data['recent_contractors'] = $this->ella_contractors_model->getRecentContractors(5);
        $data['recent_contracts'] = $this->ella_contractors_model->getRecentContracts(5);
        $data['recent_payments'] = $this->ella_contractors_model->getRecentPayments(5);
        
        $this->load->view('dashboard', $data);
    }
    
    /**
     * Contractors management
     */
    public function contractors($action = 'list', $id = null) {
        switch ($action) {
            case 'add':
                $this->contractor_form();
                break;
            case 'edit':
                $this->contractor_form($id);
                break;
            case 'view':
                $this->contractor_view($id);
                break;
            case 'delete':
                $this->contractor_delete($id);
                break;
            default:
                $this->contractors_list();
        }
    }
    
    /**
     * Display contractors list with search and filters
     */
    private function contractors_list() {
        $data['title'] = 'Contractors Management';
        
        // Get search parameters
        $search = $this->input->get('search');
        $status_filter = $this->input->get('status');
        $page = max(1, (int)$this->input->get('page', 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        // Get contractors with pagination
        $data['contractors'] = $this->ella_contractors_model->getContractors($per_page, $offset, $search, $status_filter);
        $data['total_count'] = $this->ella_contractors_model->getContractorsCount($search, $status_filter);
        $data['total_pages'] = ceil($data['total_count'] / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['status_filter'] = $status_filter;
        
        $this->load->view('contractors_list', $data);
    }
    
    /**
     * Display contractor form (add/edit)
     */
    private function contractor_form($id = null) {
        $data['title'] = $id ? 'Edit Contractor' : 'Add New Contractor';
        $data['contractor'] = null;
        
        if ($id) {
            $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
            if (!$data['contractor']) {
                set_alert('warning', 'Contractor not found');
                redirect(admin_url('ella_contractors/contractors'));
            }
        }
        
        // Get form data if POST request
        if ($this->input->post()) {
            $this->handle_contractor_submit($id);
        }
        
        $this->load->view('contractor_form', $data);
    }
    
    /**
     * Handle contractor form submission
     */
    private function handle_contractor_submit($id = null) {
        $this->load->library('form_validation');
        
        // Set validation rules
        $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
        $this->form_validation->set_rules('contact_person', 'Contact Person', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
        
        if ($this->form_validation->run() === FALSE) {
            return false;
        }
        
        $data = [
            'company_name' => $this->input->post('company_name'),
            'contact_person' => $this->input->post('contact_person'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'zip_code' => $this->input->post('zip_code'),
            'country' => $this->input->post('country'),
            'website' => $this->input->post('website'),
            'tax_id' => $this->input->post('tax_id'),
            'business_license' => $this->input->post('business_license'),
            'insurance_info' => $this->input->post('insurance_info'),
            'specialties' => $this->input->post('specialties'),
            'hourly_rate' => $this->input->post('hourly_rate'),
            'status' => $this->input->post('status'),
            'notes' => $this->input->post('notes')
        ];
        
        // Handle file upload for profile image
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $upload_path = 'uploads/contractors/';
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = true;
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('profile_image')) {
                $upload_data = $this->upload->data();
                $data['profile_image'] = $upload_path . $upload_data['file_name'];
            }
        }
        
        if ($id) {
            // Update existing contractor
            $data['date_updated'] = date('Y-m-d H:i:s');
            $data['updated_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->updateContractor($id, $data)) {
                set_alert('success', 'Contractor updated successfully');
                redirect(admin_url('ella_contractors/contractors'));
            } else {
                set_alert('danger', 'Failed to update contractor');
            }
        } else {
            // Create new contractor
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['created_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->createContractor($data)) {
                set_alert('success', 'Contractor created successfully');
                redirect(admin_url('ella_contractors/contractors'));
            } else {
                set_alert('danger', 'Failed to create contractor');
            }
        }
    }
    
    /**
     * View contractor details
     */
    public function view_contractor($id) {
        $data['title'] = 'Contractor Details';
        $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
        
        if (!$data['contractor']) {
            show_404();
        }
        
        // Get related data
        $data['contracts'] = $this->ella_contractors_model->getContractsByContractor($id);
        $data['projects'] = $this->ella_contractors_model->getProjectsByContractor($id);
        $data['payments'] = $this->ella_contractors_model->getPaymentsByContractor($id);
        $data['documents'] = $this->ella_contractors_model->getDocumentsByContractor($id);
        
        $this->load->view('contractor_view', $data);
    }
    
    /**
     * Delete contractor
     */
    public function contractor_delete($id) {
        if ($this->ella_contractors_model->deleteContractor($id)) {
            set_alert('success', 'Contractor deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete contractor');
        }
        redirect(admin_url('ella_contractors/contractors'));
    }
    
    // ========================================
    // CONTRACTS MANAGEMENT
    // ========================================
    
    /**
     * Contracts list
     */
    public function contracts() {
        $data['title'] = 'Contracts';
        $data['contracts'] = $this->ella_contractors_model->getContracts(20, 0);
        $data['total_contracts'] = $this->ella_contractors_model->getContractsCount();
        
        $this->load->view('contracts_list', $data);
    }
    
    /**
     * Add new contract
     */
    public function add_contract() {
        $data['title'] = 'Add New Contract';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($this->input->post()) {
            $contract_data = $this->input->post();
            $contract_id = $this->ella_contractors_model->createContract($contract_data);
            
            if ($contract_id) {
                set_alert('success', 'Contract created successfully');
                redirect(admin_url('ella_contractors/contracts'));
            } else {
                set_alert('danger', 'Failed to create contract');
            }
        }
        
        $this->load->view('contract_form', $data);
    }
    
    /**
     * Edit contract
     */
    public function edit_contract($id) {
        $data['title'] = 'Edit Contract';
        $data['contract'] = $this->ella_contractors_model->getContractById($id);
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if (!$data['contract']) {
            show_404();
        }
        
        if ($this->input->post()) {
            $contract_data = $this->input->post();
            if ($this->ella_contractors_model->updateContract($id, $contract_data)) {
                set_alert('success', 'Contract updated successfully');
                redirect(admin_url('ella_contractors/contracts'));
            } else {
                set_alert('danger', 'Failed to update contract');
            }
        }
        
        $this->load->view('contract_form', $data);
    }
    
    /**
     * View contract
     */
    public function view_contract($id) {
        $data['title'] = 'Contract Details';
        $data['contract'] = $this->ella_contractors_model->getContractById($id);
        
        if (!$data['contract']) {
            show_404();
        }
        
        $this->load->view('contract_view', $data);
    }
    
    /**
     * Delete contract
     */
    public function delete_contract($id) {
        if ($this->ella_contractors_model->deleteContract($id)) {
            set_alert('success', 'Contract deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete contract');
        }
        redirect(admin_url('ella_contractors/contracts'));
    }
    
    // ========================================
    // PROJECTS MANAGEMENT
    // ========================================
    
    /**
     * Projects list
     */
    public function projects() {
        $data['title'] = 'Projects';
        $data['projects'] = $this->ella_contractors_model->getProjects(20, 0);
        $data['total_projects'] = $this->ella_contractors_model->getProjectsCount();
        
        $this->load->view('projects_list', $data);
    }
    
    /**
     * Add new project
     */
    public function add_project() {
        $data['title'] = 'Add New Project';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($this->input->post()) {
            $project_data = $this->input->post();
            $project_id = $this->ella_contractors_model->createProject($project_data);
            
            if ($project_id) {
                set_alert('success', 'Project created successfully');
                redirect(admin_url('ella_contractors/projects'));
            } else {
                set_alert('danger', 'Failed to create project');
            }
        }
        
        $this->load->view('project_form', $data);
    }
    
    /**
     * Edit project
     */
    public function edit_project($id) {
        $data['title'] = 'Edit Project';
        $data['project'] = $this->ella_contractors_model->getProjectById($id);
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if (!$data['project']) {
            show_404();
        }
        
        if ($this->input->post()) {
            $project_data = $this->input->post();
            if ($this->ella_contractors_model->updateProject($id, $project_data)) {
                set_alert('success', 'Project updated successfully');
                redirect(admin_url('ella_contractors/projects'));
            } else {
                set_alert('danger', 'Failed to update project');
            }
        }
        
        $this->load->view('project_form', $data);
    }
    
    /**
     * View project
     */
    public function view_project($id) {
        $data['title'] = 'Project Details';
        $data['project'] = $this->ella_contractors_model->getProjectById($id);
        
        if (!$data['project']) {
            show_404();
        }
        
        $this->load->view('project_view', $data);
    }
    
    /**
     * Delete project
     */
    public function delete_project($id) {
        if ($this->ella_contractors_model->deleteProject($id)) {
            set_alert('success', 'Project deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete project');
        }
        redirect(admin_url('ella_contractors/projects'));
    }
    
    // ========================================
    // PAYMENTS MANAGEMENT
    // ========================================
    
    /**
     * Payments list
     */
    public function payments() {
        $data['title'] = 'Payments';
        $data['payments'] = $this->ella_contractors_model->getPayments(20, 0);
        $data['total_payments'] = $this->ella_contractors_model->getPaymentsCount();
        
        $this->load->view('payments_list', $data);
    }
    
    /**
     * Add new payment
     */
    public function add_payment() {
        $data['title'] = 'Add New Payment';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContractsForExport();
        
        if ($this->input->post()) {
            $payment_data = $this->input->post();
            $payment_id = $this->ella_contractors_model->createPayment($payment_data);
            
            if ($payment_id) {
                set_alert('success', 'Payment recorded successfully');
                redirect(admin_url('ella_contractors/payments'));
            } else {
                set_alert('danger', 'Failed to record payment');
            }
        }
        
        $this->load->view('payment_form', $data);
    }
    
    /**
     * Edit payment
     */
    public function edit_payment($id) {
        $data['title'] = 'Edit Payment';
        $data['payment'] = $this->ella_contractors_model->getPaymentById($id);
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContractsForExport();
        
        if (!$data['payment']) {
            show_404();
        }
        
        if ($this->input->post()) {
            $payment_data = $this->input->post();
            if ($this->ella_contractors_model->updatePayment($id, $payment_data)) {
                set_alert('success', 'Payment updated successfully');
                redirect(admin_url('ella_contractors/payments'));
            } else {
                set_alert('danger', 'Failed to update payment');
            }
        }
        
        $this->load->view('payment_form', $data);
    }
    
    /**
     * Delete payment
     */
    public function delete_payment($id) {
        if ($this->ella_contractors_model->deletePayment($id)) {
            set_alert('success', 'Payment deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete payment');
        }
        redirect(admin_url('ella_contractors/payments'));
    }
    
    // ========================================
    // DOCUMENTS MANAGEMENT
    // ========================================
    
    /**
     * Documents list
     */
    public function documents() {
        $data['title'] = 'Documents';
        $data['documents'] = $this->ella_contractors_model->getAllDocuments();
        
        $this->load->view('documents_list', $data);
    }
    
    /**
     * Upload document
     */
    public function upload_document() {
        $data['title'] = 'Upload Document';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($this->input->post()) {
            $document_data = $this->input->post();
            
            // Handle file upload
            $config['upload_path'] = './uploads/contractors/documents/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|txt';
            $config['max_size'] = 10240; // 10MB
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('document_file')) {
                $upload_data = $this->upload->data();
                
                $document_data['file_name'] = $upload_data['file_name'];
                $document_data['file_path'] = 'uploads/contractors/documents/' . $upload_data['file_name'];
                $document_data['file_size'] = $upload_data['file_size'];
                $document_data['file_type'] = $upload_data['file_type'];
                
                $document_id = $this->ella_contractors_model->createDocument($document_data);
                
                if ($document_id) {
                    set_alert('success', 'Document uploaded successfully');
                    redirect(admin_url('ella_contractors/documents'));
                } else {
                    set_alert('danger', 'Failed to save document');
                }
            } else {
                set_alert('danger', $this->upload->display_errors());
            }
        }
        
        $this->load->view('document_upload', $data);
    }
    
    /**
     * Download document
     */
    public function download_document() {
        $id = $this->input->get('id');
        $document = $this->ella_contractors_model->getDocumentById($id);
        
        if (!$document) {
            show_404();
        }
        
        $file_path = FCPATH . $document->file_path;
        
        if (file_exists($file_path)) {
            $this->load->helper('download');
            force_download($document->file_name, file_get_contents($file_path));
        } else {
            set_alert('danger', 'File not found');
            redirect(admin_url('ella_contractors/documents'));
        }
    }
    
    /**
     * Delete document
     */
    public function delete_document($id) {
        $document = $this->ella_contractors_model->getDocumentById($id);
        
        if ($document) {
            // Delete physical file
            $file_path = FCPATH . $document->file_path;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete database record
            if ($this->ella_contractors_model->deleteDocument($id)) {
                set_alert('success', 'Document deleted successfully');
            } else {
                set_alert('danger', 'Failed to delete document');
            }
        } else {
            set_alert('danger', 'Document not found');
        }
        
        redirect(admin_url('ella_contractors/documents'));
    }
    
    // ========================================
    // AJAX METHODS
    // ========================================
    
    /**
     * Search contractors via AJAX
     */
    public function search_contractors() {
        $search = $this->input->get('search');
        $contractors = $this->ella_contractors_model->searchContractors($search);
        
        echo json_encode($contractors);
    }
    
    /**
     * Save settings via AJAX
     */
    public function save_settings() {
        $settings = $this->input->post();
        
        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Export data via AJAX
     */
    public function export_data() {
        $type = $this->input->post('type');
        
        switch ($type) {
            case 'contractors':
                $data = $this->ella_contractors_model->getAllContractorsForExport();
                break;
            case 'contracts':
                $data = $this->ella_contractors_model->getAllContractsForExport();
                break;
            case 'projects':
                $data = $this->ella_contractors_model->getAllProjectsForExport();
                break;
            case 'payments':
                $data = $this->ella_contractors_model->getAllPaymentsForExport();
                break;
            default:
                $data = [];
        }
        
        // Generate CSV
        $filename = 'ella_contractors_' . $type . '_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys((array)$data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($output, (array)$row);
            }
        }
        
        fclose($output);
    }
    
    /**
     * Clear all data via AJAX
     */
    public function clear_data() {
        // This is a dangerous operation - should require confirmation
        $CI = &get_instance();
        $CI->load->dbforge();
        
        $tables = [
            'tblella_contractor_documents',
            'tblella_payments',
            'tblella_projects',
            'tblella_contracts',
            'tblella_contractors'
        ];
        
        foreach ($tables as $table) {
            $CI->db->truncate($table);
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Reset settings to defaults via AJAX
     */
    public function reset_settings() {
        $default_settings = [
            'ella_contractors_default_status' => 'pending',
            'ella_contractors_auto_approve' => '0',
            'ella_contractors_notification_email' => '',
            'ella_contractors_document_types' => 'contract,license,insurance,certificate,other',
            'ella_contractors_max_file_size' => '10485760',
            'ella_contractors_contract_number_format' => 'CON-{YEAR}-{SEQUENCE}',
            'ella_contractors_contract_reminder_days' => '30',
            'ella_contractors_default_payment_terms' => 'Net 30',
            'ella_contractors_late_payment_fee' => '0.05'
        ];
        
        foreach ($default_settings as $key => $value) {
            update_option($key, $value);
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Settings page
     */
    public function settings() {
        if (!is_staff_logged_in() || (!is_super_admin() && !has_permission('ella_contractors_settings', '', 'view'))) {
            access_denied('ella_contractors_settings');
        }
        
        $data['title'] = 'Ella Contractors Settings';
        
        if ($this->input->post()) {
            $this->handle_settings_submit();
        }
        
        $this->load->view('settings', $data);
    }
    
    /**
     * Handle settings submission
     */
    private function handle_settings_submit() {
        // Handle settings form submission
        $settings = [
            'ella_contractors_default_status' => $this->input->post('default_status'),
            'ella_contractors_auto_approve' => $this->input->post('auto_approve'),
            'ella_contractors_notification_email' => $this->input->post('notification_email'),
            'ella_contractors_document_types' => $this->input->post('document_types')
        ];
        
        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }
        
        set_alert('success', 'Settings updated successfully');
        redirect(admin_url('ella_contractors/settings'));
    }
    
    /**
     * AJAX endpoints for dynamic data
     */
    public function ajax_get_contractors() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $search = $this->input->get('search');
        $contractors = $this->ella_contractors_model->searchContractors($search);
        
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($contractors));
    }
    
    public function ajax_get_contracts() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $contractor_id = $this->input->get('contractor_id');
        $contracts = $this->ella_contractors_model->getContractsByContractor($contractor_id);
        
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($contracts));
    }
    
    public function ajax_get_projects() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $contractor_id = $this->input->get('contractor_id');
        $projects = $this->ella_contractors_model->getProjectsByContractor($contractor_id);
        
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($projects));
    }
    
    /**
     * Generate PDF reports
     */
    public function generate_pdf($type = 'contractor', $id = null) {
        $this->load->library('pdf');
        
        switch ($type) {
            case 'contractor':
                $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
                $data['contracts'] = $this->ella_contractors_model->getContractsByContractor($id);
                $data['projects'] = $this->ella_contractors_model->getProjectsByContractor($id);
                $data['payments'] = $this->ella_contractors_model->getPaymentsByContractor($id);
                
                $html = $this->load->view('pdf/contractor_report', $data, true);
                break;
                
            case 'contract':
                $data['contract'] = $this->ella_contractors_model->getContractById($id);
                $data['contractor'] = $this->ella_contractors_model->getContractorById($data['contract']->contractor_id);
                
                $html = $this->load->view('pdf/contract_report', $data, true);
                break;
                
            case 'project':
                $data['project'] = $this->ella_contractors_model->getProjectById($id);
                $data['contractor'] = $this->ella_contractors_model->getContractorById($data['project']->contractor_id);
                
                $html = $this->load->view('pdf/project_report', $data, true);
                break;
                
            default:
                show_404();
        }
        
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        $this->pdf->stream($type . '_' . $id . '.pdf', ['Attachment' => false]);
    }
    
    /**
     * Export data to Excel/CSV
     */
    public function export($type = 'contractors', $format = 'csv') {
        $this->load->library('excel');
        
        switch ($type) {
            case 'contractors':
                $data = $this->ella_contractors_model->getAllContractorsForExport();
                $filename = 'contractors_' . date('Y-m-d') . '.' . $format;
                break;
                
            case 'contracts':
                $data = $this->ella_contractors_model->getAllContractsForExport();
                $filename = 'contracts_' . date('Y-m-d') . '.' . $format;
                break;
                
            case 'projects':
                $data = $this->ella_contractors_model->getAllProjectsForExport();
                $filename = 'projects_' . date('Y-m-d') . '.' . $format;
                break;
                
            case 'payments':
                $data = $this->ella_contractors_model->getAllPaymentsForExport();
                $filename = 'payments_' . date('Y-m-d') . '.' . $format;
                break;
                
            default:
                show_404();
        }
        
        if ($format == 'csv') {
            $this->export_to_csv($data, $filename);
        } else {
            $this->export_to_excel($data, $filename);
        }
    }
    
    /**
     * Export to CSV
     */
    private function export_to_csv($data, $filename) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys((array)$data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($output, (array)$row);
            }
        }
        
        fclose($output);
    }
    
    /**
     * Export to Excel
     */
    private function export_to_excel($data, $filename) {
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle($type);
        
        if (!empty($data)) {
            // Write headers
            $headers = array_keys((array)$data[0]);
            $col = 'A';
            foreach ($headers as $header) {
                $this->excel->getActiveSheet()->setCellValue($col . '1', $header);
                $col++;
            }
            
            // Write data
            $row = 2;
            foreach ($data as $data_row) {
                $col = 'A';
                foreach ((array)$data_row as $value) {
                    $this->excel->getActiveSheet()->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }
        }
        
        $this->excel->getActiveSheet()->getStyle('A1:' . $col . '1')->getFont()->setBold(true);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    
    // ========================================
    // PDF GENERATION METHODS
    // ========================================
    
    /**
     * Generate PDF for contractor
     */
    public function generateContractorPDF($id) {
        $contractor = $this->ella_contractors_model->getContractorById($id);
        if (!$contractor) {
            show_404();
        }
        
        // Get related data
        $contracts = $this->ella_contractors_model->getContractsByContractor($id);
        $projects = $this->ella_contractors_model->getProjectsByContractor($id);
        $payments = $this->ella_contractors_model->getPaymentsByContractor($id);
        $documents = $this->ella_contractors_model->getDocumentsByContractor($id);
        
        // For now, generate a simple HTML report that can be printed as PDF
        $data['contractor'] = $contractor;
        $data['contracts'] = $contracts;
        $data['projects'] = $projects;
        $data['payments'] = $payments;
        $data['documents'] = $documents;
        
        $this->load->view('contractor_pdf_report', $data);
    }
    
    /**
     * Generate PowerPoint for contractor
     */
    public function generateContractorPPT($id) {
        $contractor = $this->ella_contractors_model->getContractorById($id);
        if (!$contractor) {
            show_404();
        }
        
        // Get related data
        $contracts = $this->ella_contractors_model->getContractsByContractor($id);
        $projects = $this->ella_contractors_model->getProjectsByContractor($id);
        $payments = $this->ella_contractors_model->getPaymentsByContractor($id);
        
        // Check if PHPPresentation library is available
        if (!class_exists('\PhpOffice\PhpPresentation\PhpPresentation')) {
            // Fallback: Generate a simple text file with presentation content
            $this->generateContractorTextReport($contractor, $contracts, $projects, $payments);
            return;
        }
        
        // Load PHPPresentation library
        $this->load->library('presentation');
        
        // Create new presentation
        $presentation = new \PhpOffice\PhpPresentation\PhpPresentation();
        
        // Set document properties
        $presentation->getDocumentProperties()
            ->setCreator('Ella Contractors CRM')
            ->setLastModifiedBy('CRM System')
            ->setTitle('Contractor Profile - ' . $contractor->company_name)
            ->setSubject('Contractor Information')
            ->setDescription('Dynamic contractor profile presentation');
        
        // Remove default slide
        $presentation->removeSlideByIndex(0);
        
        // Slide 1: Title Slide
        $slide1 = $presentation->getActiveSlide();
        $slide1->setName('Title Slide');
        
        // Title
        $shape = $slide1->createRichTextShape()
            ->setHeight(100)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(100);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun('CONTRACTOR PROFILE');
        $textRun->getFont()
            ->setSize(32)
            ->setColor(new \PhpOffice\PhpPresentation\Style\Color('4075A1'));
        
        // Company Name
        $shape = $slide1->createRichTextShape()
            ->setHeight(50)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(220);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun($contractor->company_name);
        $textRun->getFont()
            ->setSize(24)
            ->setColor(new \PhpOffice\PhpPresentation\Style\Color('333333'));
        
        // Date
        $shape = $slide1->createRichTextShape()
            ->setHeight(30)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(300);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun('Generated on ' . date('F j, Y'));
        $textRun->getFont()
            ->setSize(14)
            ->setColor(new \PhpOffice\PhpPresentation\Style\Color('666666'));
        
        // Slide 2: Company Information
        $slide2 = $presentation->createSlide();
        $slide2->setName('Company Information');
        
        // Slide title
        $shape = $slide2->createRichTextShape()
            ->setHeight(50)
            ->setWidth(600)
            ->setOffsetX(50)
            ->setOffsetY(50);
        $textRun = $shape->createTextRun('Company Information');
        $textRun->getFont()
            ->setSize(24)
            ->setColor(new \PhpOffice\PhpPresentation\Style\Color('4075A1'));
        
        // Company details
        $y_offset = 120;
        $details = [
            'Company Name' => $contractor->company_name,
            'Contact Person' => $contractor->contact_person,
            'Email' => $contractor->email,
            'Phone' => $contractor->phone ?: 'Not provided',
            'Website' => $contractor->website ?: 'Not provided',
            'Status' => ucfirst($contractor->status),
            'Specialties' => $contractor->specialties ?: 'Not specified',
            'Hourly Rate' => $contractor->hourly_rate ? '$' . number_format($contractor->hourly_rate, 2) . '/hr' : 'Not set'
        ];
        
        foreach ($details as $label => $value) {
            $shape = $slide2->createRichTextShape()
                ->setHeight(30)
                ->setWidth(300)
                ->setOffsetX(50)
                ->setOffsetY($y_offset);
            $textRun = $shape->createTextRun($label . ': ' . $value);
            $textRun->getFont()->setSize(12);
            $y_offset += 35;
        }
        
        // Slide 3: Address Information
        if ($contractor->address || $contractor->city || $contractor->state) {
            $slide3 = $presentation->createSlide();
            $slide3->setName('Address Information');
            
            // Slide title
            $shape = $slide3->createRichTextShape()
                ->setHeight(50)
                ->setWidth(600)
                ->setOffsetX(50)
                ->setOffsetY(50);
            $textRun = $shape->createTextRun('Address Information');
            $textRun->getFont()
                ->setSize(24)
                ->setColor(new \PhpOffice\PhpPresentation\Style\Color('4075A1'));
            
            // Address details
            $y_offset = 120;
            if ($contractor->address) {
                $shape = $slide3->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(500)
                    ->setOffsetX(50)
                    ->setOffsetY($y_offset);
                $textRun = $shape->createTextRun('Address: ' . $contractor->address);
                $textRun->getFont()->setSize(12);
                $y_offset += 35;
            }
            
            $address_line = '';
            if ($contractor->city) $address_line .= $contractor->city;
            if ($contractor->state) $address_line .= ($address_line ? ', ' : '') . $contractor->state;
            if ($contractor->zip_code) $address_line .= ($address_line ? ' ' : '') . $contractor->zip_code;
            if ($contractor->country) $address_line .= ($address_line ? ', ' : '') . $contractor->country;
            
            if ($address_line) {
                $shape = $slide3->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(500)
                    ->setOffsetX(50)
                    ->setOffsetY($y_offset);
                $textRun = $shape->createTextRun($address_line);
                $textRun->getFont()->setSize(12);
            }
        }
        
        // Slide 4: Contracts Summary
        if (!empty($contracts)) {
            $slide4 = $presentation->createSlide();
            $slide4->setName('Contracts Summary');
            
            // Slide title
            $shape = $slide4->createRichTextShape()
                ->setHeight(50)
                ->setWidth(600)
                ->setOffsetX(50)
                ->setOffsetY(50);
            $textRun = $shape->createTextRun('Contracts Summary (' . count($contracts) . ')');
            $textRun->getFont()
                ->setSize(24)
                ->setColor(new \PhpOffice\PhpPresentation\Style\Color('4075A1'));
            
            // Contracts list
            $y_offset = 120;
            $total_value = 0;
            foreach ($contracts as $contract) {
                $total_value += $contract->amount;
                $shape = $slide4->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(500)
                    ->setOffsetX(50)
                    ->setOffsetY($y_offset);
                $textRun = $shape->createTextRun('• ' . $contract->title . ' - $' . number_format($contract->amount, 2) . ' (' . ucfirst($contract->status) . ')');
                $textRun->getFont()->setSize(12);
                $y_offset += 35;
            }
            
            // Total
            $shape = $slide4->createRichTextShape()
                ->setHeight(30)
                ->setWidth(500)
                ->setOffsetX(50)
                ->setOffsetY($y_offset);
            $textRun = $shape->createTextRun('Total Contract Value: $' . number_format($total_value, 2));
            $textRun->getFont()->setSize(14)->setBold(true);
        }
        
        // Slide 5: Projects Summary
        if (!empty($projects)) {
            $slide5 = $presentation->createSlide();
            $slide5->setName('Projects Summary');
            
            // Slide title
            $shape = $slide5->createRichTextShape()
                ->setHeight(50)
                ->setWidth(600)
                ->setOffsetX(50)
                ->setOffsetY(50);
            $textRun = $shape->createTextRun('Projects Summary (' . count($projects) . ')');
            $textRun->getFont()
                ->setSize(24)
                ->setColor(new \PhpOffice\PhpPresentation\Style\Color('4075A1'));
            
            // Projects list
            $y_offset = 120;
            $total_budget = 0;
            foreach ($projects as $project) {
                $total_budget += $project->budget;
                $shape = $slide5->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(500)
                    ->setOffsetX(50)
                    ->setOffsetY($y_offset);
                $textRun = $shape->createTextRun('• ' . $project->name . ' - $' . number_format($project->budget, 2) . ' (' . ucfirst($project->status) . ')');
                $textRun->getFont()->setSize(12);
                $y_offset += 35;
            }
            
            // Total
            $shape = $slide5->createRichTextShape()
                ->setHeight(30)
                ->setWidth(500)
                ->setOffsetX(50)
                ->setOffsetY($y_offset);
            $textRun = $shape->createTextRun('Total Project Budget: $' . number_format($total_budget, 2));
            $textRun->getFont()->setSize(14)->setBold(true);
        }
        
        // Slide 6: Financial Summary
        if (!empty($payments)) {
            $slide6 = $presentation->createSlide();
            $slide6->setName('Financial Summary');
            
            // Slide title
            $shape = $slide6->createRichTextShape()
                ->setHeight(50)
                ->setWidth(600)
                ->setOffsetX(50)
                ->setOffsetY(50);
            $textRun = $shape->createTextRun('Financial Summary');
            $textRun->getFont()
                ->setSize(24)
                ->setColor(new \PhpOffice\PhpPresentation\Style\Color('4075A1'));
            
            // Calculate totals
            $total_paid = 0;
            $total_pending = 0;
            foreach ($payments as $payment) {
                if ($payment->status == 'paid') {
                    $total_paid += $payment->amount;
                } elseif ($payment->status == 'pending') {
                    $total_pending += $payment->amount;
                }
            }
            
            // Financial details
            $y_offset = 120;
            $financials = [
                'Total Payments' => count($payments),
                'Total Paid' => '$' . number_format($total_paid, 2),
                'Total Pending' => '$' . number_format($total_pending, 2),
                'Payment Status' => $total_pending > 0 ? 'Outstanding' : 'Current'
            ];
            
            foreach ($financials as $label => $value) {
                $shape = $slide6->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(300)
                    ->setOffsetX(50)
                    ->setOffsetY($y_offset);
                $textRun = $shape->createTextRun($label . ': ' . $value);
                $textRun->getFont()->setSize(14);
                $y_offset += 40;
            }
        }
        
        // Save presentation
        $filename = 'contractor_' . $contractor->id . '_' . date('Y-m-d') . '.pptx';
        $writer = \PhpOffice\PhpPresentation\IOFactory::createWriter($presentation, 'PowerPoint2007');
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Output to browser
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Fallback method to generate text report when PowerPoint library is not available
     */
    private function generateContractorTextReport($contractor, $contracts, $projects, $payments) {
        $filename = 'contractor_' . $contractor->id . '_' . date('Y-m-d') . '.txt';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $content = "CONTRACTOR PROFILE\n";
        $content .= "==================\n\n";
        $content .= "Company: " . $contractor->company_name . "\n";
        $content .= "Contact: " . $contractor->contact_person . "\n";
        $content .= "Email: " . $contractor->email . "\n";
        $content .= "Phone: " . ($contractor->phone ?: 'Not provided') . "\n";
        $content .= "Status: " . ucfirst($contractor->status) . "\n";
        $content .= "Created: " . date('F j, Y', strtotime($contractor->date_created)) . "\n\n";
        
        if (!empty($contracts)) {
            $content .= "CONTRACTS (" . count($contracts) . ")\n";
            $content .= "==========\n";
            $total_value = 0;
            foreach ($contracts as $contract) {
                $total_value += $contract->amount;
                $content .= "• " . $contract->title . " - $" . number_format($contract->amount, 2) . " (" . ucfirst($contract->status) . ")\n";
            }
            $content .= "Total Contract Value: $" . number_format($total_value, 2) . "\n\n";
        }
        
        if (!empty($projects)) {
            $content .= "PROJECTS (" . count($projects) . ")\n";
            $content .= "==========\n";
            $total_budget = 0;
            foreach ($projects as $project) {
                $total_budget += $project->budget;
                $content .= "• " . $project->name . " - $" . number_format($project->budget, 2) . " (" . ucfirst($project->status) . ")\n";
            }
            $content .= "Total Project Budget: $" . number_format($total_budget, 2) . "\n\n";
        }
        
        if (!empty($payments)) {
            $content .= "PAYMENTS (" . count($payments) . ")\n";
            $content .= "==========\n";
            $total_paid = 0;
            $total_pending = 0;
            foreach ($payments as $payment) {
                if ($payment->status == 'paid') {
                    $total_paid += $payment->amount;
                } elseif ($payment->status == 'pending') {
                    $total_pending += $payment->amount;
                }
                $content .= "• $" . number_format($payment->amount, 2) . " - " . ucfirst($payment->status) . "\n";
            }
            $content .= "Total Paid: $" . number_format($total_paid, 2) . "\n";
            $content .= "Total Pending: $" . number_format($total_pending, 2) . "\n\n";
        }
        
        $content .= "Generated on " . date('F j, Y \a\t g:i A') . " by Ella Contractors CRM\n";
        
        echo $content;
        exit;
    }
    
    /**
     * Generate PDF for contract
     */
    public function generateContractPDF($id) {
        $contract = $this->ella_contractors_model->getContractById($id);
        if (!$contract) {
            show_404();
        }
        
        // Load TCPDF library
        $this->load->library('pdf');
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Ella Contractors CRM');
        $pdf->SetAuthor('CRM System');
        $pdf->SetTitle('Contract - ' . $contract->title);
        $pdf->SetSubject('Contract Details');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, 'Ella Contractors CRM', 'Contract Report', array(64, 117, 161), array(64, 117, 161));
        $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 12);
        
        // Contract Header
        $pdf->SetFillColor(64, 117, 161);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 15, 'CONTRACT DETAILS', 0, 1, 'C', true);
        $pdf->Ln(5);
        
        // Reset colors
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        
        // Contract Information
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $contract->title, 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, 'Contractor: ' . $contract->company_name, 0, 1, 'L');
        $pdf->Cell(0, 8, 'Amount: $' . number_format($contract->amount, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, 'Start Date: ' . date('F j, Y', strtotime($contract->start_date)), 0, 1, 'L');
        $pdf->Cell(0, 8, 'End Date: ' . date('F j, Y', strtotime($contract->end_date)), 0, 1, 'L');
        $pdf->Cell(0, 8, 'Status: ' . ucfirst($contract->status), 0, 1, 'L');
        $pdf->Ln(5);
        
        // Description
        if ($contract->description) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Description', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 8, $contract->description, 0, 'L');
            $pdf->Ln(5);
        }
        
        // Terms
        if ($contract->terms) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Terms & Conditions', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 8, $contract->terms, 0, 'L');
            $pdf->Ln(5);
        }
        
        // Notes
        if ($contract->notes) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Notes', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 8, $contract->notes, 0, 'L');
            $pdf->Ln(5);
        }
        
        // Footer
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Generated on ' . date('F j, Y \a\t g:i A') . ' by Ella Contractors CRM', 0, 0, 'C');
        
        // Output PDF
        $filename = 'contract_' . $contract->id . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
    }
    
    /**
     * Generate PDF for project
     */
    public function generateProjectPDF($id) {
        $project = $this->ella_contractors_model->getProjectById($id);
        if (!$project) {
            show_404();
        }
        
        // Load TCPDF library
        $this->load->library('pdf');
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Ella Contractors CRM');
        $pdf->SetAuthor('CRM System');
        $pdf->SetTitle('Project - ' . $project->name);
        $pdf->SetSubject('Project Details');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, 'Ella Contractors CRM', 'Project Report', array(64, 117, 161), array(64, 117, 161));
        $pdf->setFooterData(array(0, 0, 0), array(0, 0, 0));
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 12);
        
        // Project Header
        $pdf->SetFillColor(64, 117, 161);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 15, 'PROJECT DETAILS', 0, 1, 'C', true);
        $pdf->Ln(5);
        
        // Reset colors
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        
        // Project Information
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $project->name, 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 8, 'Contractor: ' . $project->company_name, 0, 1, 'L');
        $pdf->Cell(0, 8, 'Budget: $' . number_format($project->budget, 2), 0, 1, 'L');
        $pdf->Cell(0, 8, 'Start Date: ' . date('F j, Y', strtotime($project->start_date)), 0, 1, 'L');
        if ($project->end_date) {
            $pdf->Cell(0, 8, 'End Date: ' . date('F j, Y', strtotime($project->end_date)), 0, 1, 'L');
        }
        $pdf->Cell(0, 8, 'Status: ' . ucfirst($project->status), 0, 1, 'L');
        $pdf->Cell(0, 8, 'Progress: ' . $project->progress . '%', 0, 1, 'L');
        if ($project->location) {
            $pdf->Cell(0, 8, 'Location: ' . $project->location, 0, 1, 'L');
        }
        $pdf->Ln(5);
        
        // Description
        if ($project->description) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Description', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 8, $project->description, 0, 'L');
            $pdf->Ln(5);
        }
        
        // Notes
        if ($project->notes) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Notes', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 8, $project->notes, 0, 'L');
            $pdf->Ln(5);
        }
        
        // Footer
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 10, 'Generated on ' . date('F j, Y \a\t g:i A') . ' by Ella Contractors CRM', 0, 0, 'C');
        
        // Output PDF
        $filename = 'project_' . $project->id . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
    }
}

