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
     * Add new contractor
     */
    public function add_contractor() {
        $data['title'] = 'Add New Contractor';
        $data['contractor'] = null;
        
        if ($this->input->post()) {
            $this->handle_contractor_submit();
        }
        
        $this->load->view('contractor_form', $data);
    }
    
    /**
     * Edit contractor
     */
    public function edit_contractor($id) {
        $data['title'] = 'Edit Contractor';
        $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
        
        if (!$data['contractor']) {
            set_alert('warning', 'Contractor not found');
            redirect(admin_url('ella_contractors/contractors'));
        }
        
        if ($this->input->post()) {
            $this->handle_contractor_submit($id);
        }
        
        $this->load->view('contractor_form', $data);
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
        $data['contract'] = null;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($this->input->post()) {
            $this->handle_contract_submit();
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
            set_alert('warning', 'Contract not found');
            redirect(admin_url('ella_contractors/contracts'));
        }
        
        if ($this->input->post()) {
            $this->handle_contract_submit($id);
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
    
    /**
     * Handle contract form submission
     */
    private function handle_contract_submit($id = null) {
        $this->load->library('form_validation');
        
        // Validation rules
        $this->form_validation->set_rules('title', 'Contract Title', 'required|trim');
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            return;
        }
        
        $data = [
            'contractor_id' => $this->input->post('contractor_id'),
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'amount' => $this->input->post('amount'),
            'status' => $this->input->post('status'),
            'terms' => $this->input->post('terms'),
            'notes' => $this->input->post('notes'),
            'updated_by' => get_staff_user_id(),
            'date_updated' => date('Y-m-d H:i:s')
        ];
        
        if ($id) {
            // Update existing contract
            if ($this->ella_contractors_model->updateContract($id, $data)) {
                set_alert('success', 'Contract updated successfully');
                redirect(admin_url('ella_contractors/contracts/view/' . $id));
            } else {
                set_alert('danger', 'Failed to update contract');
            }
        } else {
            // Add new contract
            $data['created_by'] = get_staff_user_id();
            $data['date_created'] = date('Y-m-d H:i:s');
            
            if ($this->ella_contractors_model->createContract($data)) {
                set_alert('success', 'Contract added successfully');
                redirect(admin_url('ella_contractors/contracts'));
            } else {
                set_alert('danger', 'Failed to add contract');
            }
        }
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
        $data['project'] = null;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($this->input->post()) {
            $this->handle_project_submit();
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
            set_alert('warning', 'Project not found');
            redirect(admin_url('ella_contractors/projects'));
        }
        
        if ($this->input->post()) {
            $this->handle_project_submit($id);
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
    
    /**
     * Handle project form submission
     */
    private function handle_project_submit($id = null) {
        $this->load->library('form_validation');
        
        // Validation rules
        $this->form_validation->set_rules('name', 'Project Name', 'required|trim');
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('budget', 'Budget', 'required|numeric');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            return;
        }
        
        $data = [
            'contractor_id' => $this->input->post('contractor_id'),
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'budget' => $this->input->post('budget'),
            'status' => $this->input->post('status'),
            'location' => $this->input->post('location'),
            'progress' => $this->input->post('progress'),
            'notes' => $this->input->post('notes'),
            'updated_by' => get_staff_user_id(),
            'date_updated' => date('Y-m-d H:i:s')
        ];
        
        if ($id) {
            // Update existing project
            if ($this->ella_contractors_model->updateProject($id, $data)) {
                set_alert('success', 'Project updated successfully');
                redirect(admin_url('ella_contractors/projects/view/' . $id));
            } else {
                set_alert('danger', 'Failed to update project');
            }
        } else {
            // Add new project
            $data['created_by'] = get_staff_user_id();
            $data['date_created'] = date('Y-m-d H:i:s');
            
            if ($this->ella_contractors_model->createProject($data)) {
                set_alert('success', 'Project added successfully');
                redirect(admin_url('ella_contractors/projects'));
            } else {
                set_alert('danger', 'Failed to add project');
            }
        }
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
        $data['payment'] = null;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        
        if ($this->input->post()) {
            $this->handle_payment_submit();
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
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        
        if (!$data['payment']) {
            set_alert('warning', 'Payment not found');
            redirect(admin_url('ella_contractors/payments'));
        }
        
        if ($this->input->post()) {
            $this->handle_payment_submit($id);
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
    
    /**
     * Handle payment form submission
     */
    private function handle_payment_submit($id = null) {
        $this->load->library('form_validation');
        
        // Validation rules
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('payment_date', 'Payment Date', 'required');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            return;
        }
        
        $data = [
            'contractor_id' => $this->input->post('contractor_id'),
            'contract_id' => $this->input->post('contract_id') ?: null,
            'amount' => $this->input->post('amount'),
            'payment_date' => $this->input->post('payment_date'),
            'payment_method' => $this->input->post('payment_method'),
            'reference_number' => $this->input->post('reference_number'),
            'status' => $this->input->post('status'),
            'notes' => $this->input->post('notes'),
            'updated_by' => get_staff_user_id(),
            'date_updated' => date('Y-m-d H:i:s')
        ];
        
        if ($id) {
            // Update existing payment
            if ($this->ella_contractors_model->updatePayment($id, $data)) {
                set_alert('success', 'Payment updated successfully');
                redirect(admin_url('ella_contractors/payments'));
            } else {
                set_alert('danger', 'Failed to update payment');
            }
        } else {
            // Add new payment
            $data['created_by'] = get_staff_user_id();
            $data['date_created'] = date('Y-m-d H:i:s');
            
            if ($this->ella_contractors_model->createPayment($data)) {
                set_alert('success', 'Payment added successfully');
                redirect(admin_url('ella_contractors/payments'));
            } else {
                set_alert('danger', 'Failed to add payment');
            }
        }
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
        
        // For now, generate a simple text report that can be saved as .txt
        $this->generateContractorTextReport($contractor, $contracts, $projects, $payments);
    }
    
    /**
     * Generate text report for contractor (fallback for PPT generation)
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
        
        // Get contractor info
        $contractor = $this->ella_contractors_model->getContractorById($contract->contractor_id);
        
        // For now, generate a simple HTML report
        $data['contract'] = $contract;
        $data['contractor'] = $contractor;
        
        $this->load->view('contract_pdf_report', $data);
    }
    
    /**
     * Generate PDF for project
     */
    public function generateProjectPDF($id) {
        $project = $this->ella_contractors_model->getProjectById($id);
        if (!$project) {
            show_404();
        }
        
        // Get contractor info
        $contractor = $this->ella_contractors_model->getContractorById($project->contractor_id);
        
        // For now, generate a simple HTML report
        $data['project'] = $project;
        $data['contractor'] = $contractor;
        
        $this->load->view('project_pdf_report', $data);
    }
    
    /**
     * Generate PowerPoint for contract
     */
    public function generateContractPPT($id) {
        $contract = $this->ella_contractors_model->getContractById($id);
        if (!$contract) {
            show_404();
        }
        
        // Get contractor info
        $contractor = $this->ella_contractors_model->getContractorById($contract->contractor_id);
        
        // Generate text report for now
        $this->generateContractTextReport($contract, $contractor);
    }
    
    /**
     * Generate PowerPoint for project
     */
    public function generateProjectPPT($id) {
        $project = $this->ella_contractors_model->getProjectById($id);
        if (!$project) {
            show_404();
        }
        
        // Get contractor info
        $contractor = $this->ella_contractors_model->getContractorById($project->contractor_id);
        
        // Generate text report for now
        $this->generateProjectTextReport($project, $contractor);
    }
    
    /**
     * Generate text report for contract (fallback for PPT generation)
     */
    private function generateContractTextReport($contract, $contractor) {
        $filename = 'contract_' . $contract->id . '_' . date('Y-m-d') . '.txt';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $content = "CONTRACT DETAILS\n";
        $content .= "================\n\n";
        $content .= "Title: " . $contract->title . "\n";
        $content .= "Contractor: " . $contractor->company_name . "\n";
        $content .= "Amount: $" . number_format($contract->amount, 2) . "\n";
        $content .= "Start Date: " . date('F j, Y', strtotime($contract->start_date)) . "\n";
        $content .= "End Date: " . date('F j, Y', strtotime($contract->end_date)) . "\n";
        $content .= "Status: " . ucfirst($contract->status) . "\n\n";
        
        if ($contract->description) {
            $content .= "Description:\n" . $contract->description . "\n\n";
        }
        
        if ($contract->terms) {
            $content .= "Terms & Conditions:\n" . $contract->terms . "\n\n";
        }
        
        if ($contract->notes) {
            $content .= "Notes:\n" . $contract->notes . "\n\n";
        }
        
        $content .= "Generated on " . date('F j, Y \a\t g:i A') . " by Ella Contractors CRM\n";
        
        echo $content;
        exit;
    }
    
    /**
     * Generate text report for project (fallback for PPT generation)
     */
    private function generateProjectTextReport($project, $contractor) {
        $filename = 'project_' . $project->id . '_' . date('Y-m-d') . '.txt';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $content = "PROJECT DETAILS\n";
        $content .= "===============\n\n";
        $content .= "Name: " . $project->name . "\n";
        $content .= "Contractor: " . $contractor->company_name . "\n";
        $content .= "Budget: $" . number_format($project->budget, 2) . "\n";
        $content .= "Start Date: " . date('F j, Y', strtotime($project->start_date)) . "\n";
        if ($project->end_date) {
            $content .= "End Date: " . date('F j, Y', strtotime($project->end_date)) . "\n";
        }
        $content .= "Status: " . ucfirst($project->status) . "\n";
        $content .= "Progress: " . $project->progress . "%\n";
        if ($project->location) {
            $content .= "Location: " . $project->location . "\n";
        }
        $content .= "\n";
        
        if ($project->description) {
            $content .= "Description:\n" . $project->description . "\n\n";
        }
        
        if ($project->notes) {
            $content .= "Notes:\n" . $project->notes . "\n\n";
        }
        
        $content .= "Generated on " . date('F j, Y \a\t g:i A') . " by Ella Contractors CRM\n";
        
        echo $content;
        exit;
    }
}

