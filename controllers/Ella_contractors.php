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
    private function contractor_view($id) {
        $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
        if (!$data['contractor']) {
            set_alert('warning', 'Contractor not found');
            redirect(admin_url('ella_contractors/contractors'));
        }
        
        $data['title'] = 'Contractor: ' . $data['contractor']->company_name;
        $data['contracts'] = $this->ella_contractors_model->getContractsByContractor($id);
        $data['projects'] = $this->ella_contractors_model->getProjectsByContractor($id);
        $data['payments'] = $this->ella_contractors_model->getPaymentsByContractor($id);
        $data['documents'] = $this->ella_contractors_model->getDocumentsByContractor($id);
        
        $this->load->view('contractor_view', $data);
    }
    
    /**
     * Delete contractor
     */
    private function contractor_delete($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $contractor = $this->ella_contractors_model->getContractorById($id);
        if (!$contractor) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Contractor not found']));
            return;
        }
        
        // Check if contractor has active contracts or projects
        $active_contracts = $this->ella_contractors_model->getActiveContractsByContractor($id);
        $active_projects = $this->ella_contractors_model->getActiveProjectsByContractor($id);
        
        if (!empty($active_contracts) || !empty($active_projects)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Cannot delete contractor with active contracts or projects']));
            return;
        }
        
        if ($this->ella_contractors_model->deleteContractor($id)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => true, 'message' => 'Contractor deleted successfully']));
        } else {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Failed to delete contractor']));
        }
    }
    
    /**
     * Contracts management
     */
    public function contracts($action = 'list', $id = null) {
        switch ($action) {
            case 'add':
                $this->contract_form();
                break;
            case 'edit':
                $this->contract_form($id);
                break;
            case 'view':
                $this->contract_view($id);
                break;
            case 'delete':
                $this->contract_delete($id);
                break;
            default:
                $this->contracts_list();
        }
    }
    
    /**
     * Display contracts list
     */
    private function contracts_list() {
        $data['title'] = 'Contracts Management';
        
        $search = $this->input->get('search');
        $status_filter = $this->input->get('status');
        $page = max(1, (int)$this->input->get('page', 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $data['contracts'] = $this->ella_contractors_model->getContracts($per_page, $offset, $search, $status_filter);
        $data['total_count'] = $this->ella_contractors_model->getContractsCount($search, $status_filter);
        $data['total_pages'] = ceil($data['total_count'] / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['status_filter'] = $status_filter;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        $this->load->view('contracts_list', $data);
    }
    
    /**
     * Display contract form
     */
    private function contract_form($id = null) {
        $data['title'] = $id ? 'Edit Contract' : 'Add New Contract';
        $data['contract'] = null;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($id) {
            $data['contract'] = $this->ella_contractors_model->getContractById($id);
            if (!$data['contract']) {
                set_alert('warning', 'Contract not found');
                redirect(admin_url('ella_contractors/contracts'));
            }
        }
        
        if ($this->input->post()) {
            $this->handle_contract_submit($id);
        }
        
        $this->load->view('contract_form', $data);
    }
    
    /**
     * Handle contract form submission
     */
    private function handle_contract_submit($id = null) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('title', 'Contract Title', 'required|trim');
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        $this->form_validation->set_rules('amount', 'Contract Amount', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            return false;
        }
        
        $data = [
            'title' => $this->input->post('title'),
            'contractor_id' => $this->input->post('contractor_id'),
            'description' => $this->input->post('description'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'amount' => $this->input->post('amount'),
            'status' => $this->input->post('status'),
            'terms' => $this->input->post('terms'),
            'notes' => $this->input->post('notes')
        ];
        
        if ($id) {
            $data['date_updated'] = date('Y-m-d H:i:s');
            $data['updated_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->updateContract($id, $data)) {
                set_alert('success', 'Contract updated successfully');
                redirect(admin_url('ella_contractors/contracts'));
            } else {
                set_alert('danger', 'Failed to update contract');
            }
        } else {
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['created_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->createContract($data)) {
                set_alert('success', 'Contract created successfully');
                redirect(admin_url('ella_contractors/contracts'));
            } else {
                set_alert('danger', 'Failed to create contract');
            }
        }
    }
    
    /**
     * Projects management
     */
    public function projects($action = 'list', $id = null) {
        switch ($action) {
            case 'add':
                $this->project_form();
                break;
            case 'edit':
                $this->project_form($id);
                break;
            case 'view':
                $this->project_view($id);
                break;
            case 'delete':
                $this->project_delete($id);
                break;
            default:
                $this->projects_list();
        }
    }
    
    /**
     * Display projects list
     */
    private function projects_list() {
        $data['title'] = 'Projects Management';
        
        $search = $this->input->get('search');
        $status_filter = $this->input->get('status');
        $page = max(1, (int)$this->input->get('page', 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $data['projects'] = $this->ella_contractors_model->getProjects($per_page, $offset, $search, $status_filter);
        $data['total_count'] = $this->ella_contractors_model->getProjectsCount($search, $status_filter);
        $data['total_pages'] = ceil($data['total_count'] / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['status_filter'] = $status_filter;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        $this->load->view('projects_list', $data);
    }
    
    /**
     * Display project form
     */
    private function project_form($id = null) {
        $data['title'] = $id ? 'Edit Project' : 'Add New Project';
        $data['project'] = null;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($id) {
            $data['project'] = $this->ella_contractors_model->getProjectById($id);
            if (!$data['project']) {
                set_alert('warning', 'Project not found');
                redirect(admin_url('ella_contractors/projects'));
            }
        }
        
        if ($this->input->post()) {
            $this->handle_project_submit($id);
        }
        
        $this->load->view('project_form', $data);
    }
    
    /**
     * Handle project form submission
     */
    private function handle_project_submit($id = null) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('name', 'Project Name', 'required|trim');
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('budget', 'Project Budget', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            return false;
        }
        
        $data = [
            'name' => $this->input->post('name'),
            'contractor_id' => $this->input->post('contractor_id'),
            'description' => $this->input->post('description'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
            'budget' => $this->input->post('budget'),
            'status' => $this->input->post('status'),
            'location' => $this->input->post('location'),
            'notes' => $this->input->post('notes')
        ];
        
        if ($id) {
            $data['date_updated'] = date('Y-m-d H:i:s');
            $data['updated_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->updateProject($id, $data)) {
                set_alert('success', 'Project updated successfully');
                redirect(admin_url('ella_contractors/projects'));
            } else {
                set_alert('danger', 'Failed to update project');
            }
        } else {
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['created_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->createProject($data)) {
                set_alert('success', 'Project created successfully');
                redirect(admin_url('ella_contractors/projects'));
            } else {
                set_alert('danger', 'Failed to create project');
            }
        }
    }
    
    /**
     * Payments management
     */
    public function payments($action = 'list', $id = null) {
        switch ($action) {
            case 'add':
                $this->payment_form();
                break;
            case 'edit':
                $this->payment_form($id);
                break;
            case 'view':
                $this->payment_view($id);
                break;
            case 'delete':
                $this->payment_delete($id);
                break;
            default:
                $this->payments_list();
        }
    }
    
    /**
     * Display payments list
     */
    private function payments_list() {
        $data['title'] = 'Payments Management';
        
        $search = $this->input->get('search');
        $status_filter = $this->input->get('status');
        $page = max(1, (int)$this->input->get('page', 1));
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $data['payments'] = $this->ella_contractors_model->getPayments($per_page, $offset, $search, $status_filter);
        $data['total_count'] = $this->ella_contractors_model->getPaymentsCount($search, $status_filter);
        $data['total_pages'] = ceil($data['total_count'] / $per_page);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['status_filter'] = $status_filter;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        $this->load->view('payments_list', $data);
    }
    
    /**
     * Display payment form
     */
    private function payment_form($id = null) {
        $data['title'] = $id ? 'Edit Payment' : 'Add New Payment';
        $data['payment'] = null;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        
        if ($id) {
            $data['payment'] = $this->ella_contractors_model->getPaymentById($id);
            if (!$data['payment']) {
                set_alert('warning', 'Payment not found');
                redirect(admin_url('ella_contractors/payments'));
            }
        }
        
        if ($this->input->post()) {
            $this->handle_payment_submit($id);
        }
        
        $this->load->view('payment_form', $data);
    }
    
    /**
     * Handle payment form submission
     */
    private function handle_payment_submit($id = null) {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('amount', 'Payment Amount', 'required|numeric');
        $this->form_validation->set_rules('payment_date', 'Payment Date', 'required');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            return false;
        }
        
        $data = [
            'contractor_id' => $this->input->post('contractor_id'),
            'contract_id' => $this->input->post('contract_id'),
            'amount' => $this->input->post('amount'),
            'payment_date' => $this->input->post('payment_date'),
            'payment_method' => $this->input->post('payment_method'),
            'reference_number' => $this->input->post('reference_number'),
            'status' => $this->input->post('status'),
            'notes' => $this->input->post('notes')
        ];
        
        if ($id) {
            $data['date_updated'] = date('Y-m-d H:i:s');
            $data['updated_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->updatePayment($id, $data)) {
                set_alert('success', 'Payment updated successfully');
                redirect(admin_url('ella_contractors/payments'));
            } else {
                set_alert('danger', 'Failed to update payment');
            }
        } else {
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['created_by'] = get_staff_user_id();
            
            if ($this->ella_contractors_model->createPayment($data)) {
                set_alert('success', 'Payment created successfully');
                redirect(admin_url('ella_contractors/payments'));
            } else {
                set_alert('danger', 'Failed to create payment');
            }
        }
    }
    
    /**
     * Documents management
     */
    public function documents($action = 'gallery', $contractor_id = null) {
        switch ($action) {
            case 'upload':
                $this->document_upload($contractor_id);
                break;
            case 'delete':
                $this->document_delete();
                break;
            case 'download':
                $this->document_download();
                break;
            default:
                $this->documents_gallery($contractor_id);
        }
    }
    
    /**
     * Display documents gallery
     */
    private function documents_gallery($contractor_id = null) {
        $data['title'] = 'Documents Gallery';
        $data['contractor_id'] = $contractor_id;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($contractor_id) {
            $data['documents'] = $this->ella_contractors_model->getDocumentsByContractor($contractor_id);
            $data['contractor'] = $this->ella_contractors_model->getContractorById($contractor_id);
        } else {
            $data['documents'] = $this->ella_contractors_model->getAllDocuments();
        }
        
        $this->load->view('documents_gallery', $data);
    }
    
    /**
     * Document upload form
     */
    private function document_upload($contractor_id = null) {
        $data['title'] = 'Upload Document';
        $data['contractor_id'] = $contractor_id;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        if ($this->input->post()) {
            $this->handle_document_upload();
        }
        
        $this->load->view('upload_document', $data);
    }
    
    /**
     * Handle document upload
     */
    private function handle_document_upload() {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
        $this->form_validation->set_rules('document_type', 'Document Type', 'required');
        $this->form_validation->set_rules('title', 'Document Title', 'required|trim');
        
        if ($this->form_validation->run() === FALSE) {
            return false;
        }
        
        // Handle file upload
        if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
            $upload_path = 'uploads/contractors/documents/';
            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx|jpg|jpeg|png|gif';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = true;
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('document_file')) {
                $upload_data = $this->upload->data();
                
                $data = [
                    'contractor_id' => $this->input->post('contractor_id'),
                    'title' => $this->input->post('title'),
                    'document_type' => $this->input->post('document_type'),
                    'file_name' => $upload_data['file_name'],
                    'file_path' => $upload_path . $upload_data['file_name'],
                    'file_size' => $upload_data['file_size'],
                    'file_type' => $upload_data['file_type'],
                    'description' => $this->input->post('description'),
                    'date_uploaded' => date('Y-m-d H:i:s'),
                    'uploaded_by' => get_staff_user_id()
                ];
                
                if ($this->ella_contractors_model->createDocument($data)) {
                    set_alert('success', 'Document uploaded successfully');
                    redirect(admin_url('ella_contractors/documents/gallery/' . $data['contractor_id']));
                } else {
                    set_alert('danger', 'Failed to upload document');
                }
            } else {
                set_alert('danger', 'File upload failed: ' . $this->upload->display_errors());
            }
        } else {
            set_alert('danger', 'Please select a file to upload');
        }
    }
    
    /**
     * Delete document
     */
    private function document_delete() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $document_id = $this->input->post('document_id');
        $document = $this->ella_contractors_model->getDocumentById($document_id);
        
        if (!$document) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Document not found']));
            return;
        }
        
        // Delete physical file
        if (file_exists($document->file_path)) {
            unlink($document->file_path);
        }
        
        if ($this->ella_contractors_model->deleteDocument($document_id)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => true, 'message' => 'Document deleted successfully']));
        } else {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Failed to delete document']));
        }
    }
    
    /**
     * Download document
     */
    private function document_download() {
        $document_id = $this->input->get('id');
        $document = $this->ella_contractors_model->getDocumentById($document_id);
        
        if (!$document || !file_exists($document->file_path)) {
            set_alert('danger', 'Document not found');
            redirect(admin_url('ella_contractors/documents'));
        }
        
        $this->load->helper('download');
        force_download($document->file_path, NULL);
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
}
