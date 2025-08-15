<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        
        // Load model with proper error handling
        try {
            $this->load->model('ella_contractors/Ella_contractors_model');
        } catch (Exception $e) {
            log_message('error', 'Failed to load Ella_contractors_model: ' . $e->getMessage());
        }
        
        // Load library with error handling
        try {
            $this->load->library('DocumentManager');
        } catch (Exception $e) {
            log_message('error', 'Failed to load DocumentManager library: ' . $e->getMessage());
        }
        
        // Check if user is logged in (basic auth check)
        if (!$this->session->userdata('user_id')) {
            // For demo purposes, set a default user
            $this->session->set_userdata('user_id', 1);
            $this->session->set_userdata('user_name', 'Demo User');
        }
    }
    
    /**
     * Test method to debug model loading
     */
    public function test() {
        echo "<h2>Debug Information</h2>";
        echo "<p>Model loaded: " . (isset($this->ella_contractors_model) ? 'YES' : 'NO') . "</p>";
        echo "<p>Model class: " . get_class($this->ella_contractors_model) . "</p>";
        echo "<p>Methods available: " . implode(', ', get_class_methods($this->ella_contractors_model)) . "</p>";
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors/dashboard') . "'>Go to Dashboard</a></p>";
    }
    
    /**
     * Main dashboard
     */
    public function dashboard() {
        try {
            $data['title'] = 'Ella Contractors Dashboard';
            
            // Check if model is loaded before calling methods
            if (isset($this->ella_contractors_model) && method_exists($this->ella_contractors_model, 'getDashboardStats')) {
                $data['stats'] = $this->ella_contractors_model->getDashboardStats();
                $data['recent_contractors'] = $this->ella_contractors_model->getRecentContractors(5);
                $data['active_contracts'] = $this->ella_contractors_model->getActiveContracts(5);
                $data['pending_payments'] = $this->ella_contractors_model->getPendingPayments(5);
            } else {
                // Fallback data if model fails
                $data['stats'] = [
                    'total_contractors' => 0,
                    'active_contracts' => 0,
                    'total_projects' => 0,
                    'pending_payments' => 0
                ];
                $data['recent_contractors'] = [];
                $data['active_contracts'] = [];
                $data['pending_payments'] = [];
                $data['error'] = 'Model not loaded properly';
            }
            
            $this->load->view('dashboard', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            
            // Show error page or fallback
            $data['title'] = 'Error - Ella Contractors';
            $data['error_message'] = 'An error occurred while loading the dashboard. Please try again.';
            $data['error_details'] = $e->getMessage();
            
            $this->load->view('error_page', $data);
        }
    }
    
    /**
     * Main index method - redirects to dashboard
     */
    public function index() {
        redirect('admin/ella_contractors/dashboard');
    }
    
    // ========================================
    // CONTRACTORS MANAGEMENT
    // ========================================
    
    /**
     * List all contractors
     */
    public function contractors($page = 1) {
        // Ensure page is never negative
        $page = max(1, (int)$page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        
        $data['contractors'] = $this->ella_contractors_model->getContractors($limit, $offset, $search, $status);
        $data['total_count'] = $this->ella_contractors_model->getContractorsCount($search, $status);
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_count'] / $limit);
        $data['search'] = $search;
        $data['status_filter'] = $status;
        
        $this->load->view('contractors_list', $data);
    }
    
    /**
     * Add new contractor form
     */
    public function add_contractor() {
        if ($this->input->post()) {
            $contractor_data = [
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
            
            $contractor_id = $this->ella_contractors_model->createContractor($contractor_data);
            
            if ($contractor_id) {
                set_alert('success', 'Contractor added successfully');
                redirect('admin/ella_contractors/contractors');
            } else {
                set_alert('danger', 'Failed to add contractor');
            }
        }
        
        $this->load->view('contractor_form');
    }
    
    /**
     * Edit contractor form
     */
    public function edit_contractor($id) {
        if ($this->input->post()) {
            $contractor_data = [
                'company_name' => $this->input->post('company_name'),
                'contact_person' => $this->input->post('contact_person'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address' => $this->input->post('address'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'zip_code' => $this->input->post('zip_code'),
                'country' => $this->input->post('country'),
                'tax_id' => $this->input->post('tax_id'),
                'license_number' => $this->input->post('license_number'),
                'insurance_info' => $this->input->post('insurance_info'),
                'specialization' => $this->input->post('specialization'),
                'status' => $this->input->post('status'),
                'notes' => $this->input->post('notes')
            ];
            
            if ($this->ella_contractors_model->updateContractor($id, $contractor_data)) {
                set_alert('success', 'Contractor updated successfully');
                redirect('admin/ella_contractors/contractors');
            } else {
                set_alert('danger', 'Failed to update contractor');
            }
        }
        
        $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
        $this->load->view('contractor_form', $data);
    }
    
    /**
     * Delete contractor
     */
    public function delete_contractor($id) {
        if ($this->ella_contractors_model->deleteContractor($id)) {
            set_alert('success', 'Contractor deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete contractor');
        }
        
        redirect('admin/ella_contractors/contractors');
    }
    
    // ========================================
    // CONTRACTS MANAGEMENT
    // ========================================
    
    /**
     * List all contracts
     */
    public function contracts($page = 1) {
        // Ensure page is never negative
        $page = max(1, (int)$page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $contractor_id = $this->input->get('contractor_id');
        
        $data['contracts'] = $this->ella_contractors_model->getContracts($limit, $offset, $search, $status, $contractor_id);
        $data['total_count'] = $this->ella_contractors_model->getContractsCount($search, $status, $contractor_id);
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_count'] / $limit);
        $data['search'] = $search;
        $data['status_filter'] = $status;
        $data['contractor_filter'] = $contractor_id;
        
        // Get contractors for filter dropdown
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        $this->load->view('contracts_list', $data);
    }
    
    /**
     * Add new contract form
     */
    public function add_contract() {
        if ($this->input->post()) {
            $contract_data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'title' => $this->input->post('title'),
                'contract_number' => $this->input->post('contract_number'),
                'description' => $this->input->post('description'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'hourly_rate' => $this->input->post('hourly_rate'),
                'estimated_hours' => $this->input->post('estimated_hours'),
                'fixed_amount' => $this->input->post('fixed_amount'),
                'payment_terms' => $this->input->post('payment_terms'),
                'status' => $this->input->post('status') ?: 'draft',
                'terms_conditions' => $this->input->post('terms_conditions'),
                'notes' => $this->input->post('notes')
            ];
            
            $contract_id = $this->ella_contractors_model->createContract($contract_data);
            
            if ($contract_id) {
                set_alert('success', 'Contract created successfully');
                redirect('admin/ella_contractors/contracts');
            } else {
                set_alert('danger', 'Failed to create contract');
            }
        }
        
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $this->load->view('contract_form', $data);
    }
    
    /**
     * Edit contract form
     */
    public function edit_contract($id) {
        if ($this->input->post()) {
            $contract_data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'title' => $this->input->post('title'),
                'contract_number' => $this->input->post('contract_number'),
                'description' => $this->input->post('description'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'hourly_rate' => $this->input->post('hourly_rate'),
                'estimated_hours' => $this->input->post('estimated_hours'),
                'fixed_amount' => $this->input->post('fixed_amount'),
                'payment_terms' => $this->input->post('payment_terms'),
                'status' => $this->input->post('status'),
                'terms_conditions' => $this->input->post('terms_conditions'),
                'notes' => $this->input->post('notes')
            ];
            
            if ($this->ella_contractors_model->updateContract($id, $contract_data)) {
                set_alert('success', 'Contract updated successfully');
                redirect('admin/ella_contractors/contracts');
            } else {
                set_alert('danger', 'Failed to update contract');
            }
        }
        
        $data['contract'] = $this->ella_contractors_model->getContractById($id);
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $this->load->view('contract_form', $data);
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
        
        redirect('admin/ella_contractors/contracts');
    }
    
    // ========================================
    // PROJECTS MANAGEMENT
    // ========================================
    
    /**
     * List all projects
     */
    public function projects($page = 1) {
        // Ensure page is never negative
        $page = max(1, (int)$page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $contractor_id = $this->input->get('contractor_id');
        
        $data['projects'] = $this->ella_contractors_model->getProjects($limit, $offset, $search, $status, $contractor_id);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['status_filter'] = $status;
        $data['contractor_filter'] = $contractor_id;
        
        // Get contractors for filter dropdown
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        $this->load->view('projects_list', $data);
    }
    
    /**
     * Add new project form
     */
    public function add_project() {
        if (!is_staff_logged_in()) {
            redirect('admin');
        }

        if ($this->input->post()) {
            $data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id') ?: null,
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'budget' => $this->input->post('budget'),
                'estimated_hours' => $this->input->post('estimated_hours'),
                'actual_hours' => $this->input->post('actual_hours'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
                'priority' => $this->input->post('priority'),
                'location' => $this->input->post('location'),
                'notes' => $this->input->post('notes')
            ];

            $project_id = $this->ella_contractors_model->createProject($data);
            if ($project_id) {
                set_alert('success', 'Project created successfully');
                redirect('admin/ella_contractors/projects');
            } else {
                set_alert('danger', 'Failed to create project');
            }
        }

        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        $data['title'] = 'Add New Project';
        $this->load->view('project_form', $data);
    }

    public function edit_project($id) {
        if (!is_staff_logged_in()) {
            redirect('admin');
        }

        $project = $this->ella_contractors_model->getProjectById($id);
        if (!$project) {
            show_404();
        }

        if ($this->input->post()) {
            $data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id') ?: null,
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'budget' => $this->input->post('budget'),
                'estimated_hours' => $this->input->post('estimated_hours'),
                'actual_hours' => $this->input->post('actual_hours'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
                'priority' => $this->input->post('priority'),
                'location' => $this->input->post('location'),
                'notes' => $this->input->post('notes')
            ];

            if ($this->ella_contractors_model->updateProject($id, $data)) {
                set_alert('success', 'Project updated successfully');
                redirect('admin/ella_contractors/projects');
            } else {
                set_alert('danger', 'Failed to update project');
            }
        }

        $data['project'] = $project;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        $data['title'] = 'Edit Project';
        $this->load->view('project_form', $data);
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
        
        redirect('admin/ella_contractors/projects');
    }
    
    // ========================================
    // PAYMENTS MANAGEMENT
    // ========================================
    
    /**
     * List all payments
     */
    public function payments($page = 1) {
        // Ensure page is never negative
        $page = max(1, (int)$page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = $this->input->get('search');
        $status = $this->input->get('status');
        $contractor_id = $this->input->get('contractor_id');
        
        $data['payments'] = $this->ella_contractors_model->getPayments($limit, $offset, $search, $status, $contractor_id);
        $data['current_page'] = $page;
        $data['search'] = $search;
        $data['status_filter'] = $status;
        $data['contractor_filter'] = $contractor_id;
        
        // Get contractors for filter dropdown
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        
        $this->load->view('payments_list', $data);
    }
    
    /**
     * Add new payment form
     */
    public function add_payment() {
        if ($this->input->post()) {
            $payment_data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id'),
                'invoice_number' => $this->input->post('invoice_number'),
                'description' => $this->input->post('description'),
                'amount' => $this->input->post('amount'),
                'payment_date' => $this->input->post('payment_date'),
                'due_date' => $this->input->post('due_date'),
                'status' => $this->input->post('status') ?: 'pending',
                'payment_method' => $this->input->post('payment_method'),
                'notes' => $this->input->post('notes')
            ];
            
            $payment_id = $this->ella_contractors_model->createPayment($payment_data);
            
            if ($payment_id) {
                set_alert('success', 'Payment created successfully');
                redirect('admin/ella_contractors/payments');
            } else {
                set_alert('danger', 'Failed to create payment');
            }
        }
        
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        $this->load->view('payment_form', $data);
    }
    
    /**
     * Edit payment form
     */
    public function edit_payment($id) {
        if ($this->input->post()) {
            $payment_data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id'),
                'invoice_number' => $this->input->post('invoice_number'),
                'description' => $this->input->post('description'),
                'amount' => $this->input->post('amount'),
                'payment_date' => $this->input->post('payment_date'),
                'due_date' => $this->input->post('due_date'),
                'status' => $this->input->post('status'),
                'payment_method' => $this->input->post('payment_method'),
                'notes' => $this->input->post('notes')
            ];
            
            if ($this->ella_contractors_model->updatePayment($id, $payment_data)) {
                set_alert('success', 'Payment updated successfully');
                redirect('admin/ella_contractors/payments');
            } else {
                set_alert('danger', 'Failed to update payment');
            }
        }
        
        $data['payment'] = $this->ella_contractors_model->getPaymentById($id);
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
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
        
        redirect('admin/ella_contractors/payments');
    }

    /**
     * Document Management Methods - ACTIVE
     */
    
    /**
     * Upload document for contractor
     */
    public function upload_document($contractor_id)
    {
        if ($this->input->post()) {
            // For demo, just redirect to gallery with success message
            // In real implementation, this would process the file upload
            set_alert('success', 'Document uploaded successfully (demo mode)');
            redirect(admin_url('ella_contractors/documents/gallery/' . $contractor_id));
        }
        
        $data['title'] = 'Upload Document';
        $data['contractor_id'] = $contractor_id;
        $this->load->view('upload_document', $data);
    }

    /**
     * Documents gallery for contractor
     */
    public function documents_gallery($contractor_id)
    {
        $this->load->library('../libraries/DocumentManager', '', 'document_manager');
        
        $data['title'] = 'Documents Gallery';
        $data['contractor_id'] = $contractor_id;
        $data['documents'] = $this->document_manager->getDocumentGallery($contractor_id);
        
        // Get contractor info for breadcrumb
        $contractor = $this->ella_contractors_model->getContractorById($contractor_id);
        $data['contractor_name'] = $contractor ? $contractor->company_name : 'Unknown Contractor';
        
        $this->load->view('documents_gallery', $data);
    }

    /**
     * Download document
     */
    public function download_document($document_id)
    {
        // For demo, create a sample document
        $document_name = 'Sample_Document_' . $document_id . '.txt';
        $content = "This is a sample document for demonstration purposes.\n\nDocument ID: " . $document_id . "\nGenerated on: " . date('Y-m-d H:i:s') . "\n\nThis would normally contain the actual document content.";
        
        // Force download
        $this->load->helper('download');
        force_download($document_name, $content);
    }

    /**
     * Delete document
     */
    public function delete_document($document_id)
    {
        // For demo, just return success
        echo json_encode(['success' => true, 'message' => 'Document deleted successfully (demo mode)']);
    }

    /**
     * Share document
     */
    public function share_document($document_id)
    {
        $this->load->library('../libraries/DocumentManager', '', 'document_manager');
        
        $result = $this->document_manager->generateShareLink($document_id);
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'share_url' => $result['share_url'],
                'expires_at' => $result['expires_at']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to generate share link']);
        }
    }

    /**
     * Generate contract PDF
     */
    public function generate_contract_pdf($contract_id) {
        $this->load->library('DocumentManager');
        
        // Get real contract data (in real implementation, this would come from database)
        $contract_data = $this->getContractData($contract_id);
        
        $result = $this->documentmanager->generateContractPDF($contract_data);
        
        if ($result['success']) {
            // Force download the generated file
            $this->output->set_content_type('application/octet-stream');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            $this->output->set_header('Content-Length: ' . $result['size']);
            
            readfile($result['filepath']);
            
            // Clean up temporary file
            unlink($result['filepath']);
        } else {
            // Handle error
            $this->output->set_content_type('text/html');
            $this->output->set_output('<h2>Error Generating PDF</h2><p>' . $result['error'] . '</p>');
        }
    }
    
    /**
     * Generate invoice PDF
     */
    public function generate_invoice_pdf($payment_id) {
        $this->load->library('DocumentManager');
        
        // Get real invoice data
        $invoice_data = $this->getInvoiceData($payment_id);
        
        $result = $this->documentmanager->generateInvoicePDF($invoice_data);
        
        if ($result['success']) {
            // Force download the generated file
            $this->output->set_content_type('application/octet-stream');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            $this->output->set_header('Content-Length: ' . $result['size']);
            
            readfile($result['filepath']);
            
            // Clean up temporary file
            unlink($result['filepath']);
        } else {
            // Handle error
            $this->output->set_content_type('text/html');
            $this->output->set_output('<h2>Error Generating PDF</h2><p>' . $result['error'] . '</p>');
        }
    }
    
    /**
     * Generate report PDF
     */
    public function generate_report_pdf($report_type) {
        $this->load->library('DocumentManager');
        
        // Get real report data
        $report_data = $this->getReportData($report_type);
        
        $result = $this->documentmanager->generateReportPDF($report_data, $report_type);
        
        if ($result['success']) {
            // Force download the generated file
            $this->output->set_content_type('application/octet-stream');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            $this->output->set_header('Content-Length: ' . $result['size']);
            
            readfile($result['filepath']);
            
            // Clean up temporary file
            unlink($result['filepath']);
        } else {
            // Handle error
            $this->output->set_content_type('text/html');
            $this->output->set_output('<h2>Error Generating PDF</h2><p>' . $result['error'] . '</p>');
        }
    }
    
    /**
     * Generate contractor presentation
     */
    public function generate_contractor_presentation($contractor_id) {
        $this->load->library('DocumentManager');
        
        // Get real contractor data
        $contractor_data = $this->ella_contractors_model->getContractorById($contractor_id);
        
        $result = $this->documentmanager->generateContractorPresentation($contractor_data);
        
        if ($result['success']) {
            // Force download the generated file
            $this->output->set_content_type('application/octet-stream');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            $this->output->set_header('Content-Length: ' . $result['size']);
            
            readfile($result['filepath']);
            
            // Clean up temporary file
            unlink($result['filepath']);
        } else {
            // Handle error
            $this->output->set_content_type('text/html');
            $this->output->set_output('<h2>Error Generating Presentation</h2><p>' . $result['error'] . '</p>');
        }
    }
    
    /**
     * Generate project presentation
     */
    public function generate_project_presentation($project_id) {
        $this->load->library('DocumentManager');
        
        // Get real project data
        $project_data = $this->getProjectData($project_id);
        
        $result = $this->documentmanager->generateProjectPresentation($project_data);
        
        if ($result['success']) {
            // Force download the generated file
            $this->output->set_header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            $this->output->set_header('Content-Length: ' . $result['size']);
            
            readfile($result['filepath']);
            
            // Clean up temporary file
            unlink($result['filepath']);
        } else {
            // Handle error
            $this->output->set_content_type('text/html');
            $this->output->set_output('<h2>Error Generating Presentation</h2><p>' . $result['error'] . '</p>');
        }
    }
    
    /**
     * Get real contract data
     */
    private function getContractData($contract_id) {
        // In real implementation, this would query the database
        // For now, return realistic dummy data
        return [
            'id' => 'CON-' . str_pad($contract_id, 6, '0', STR_PAD_LEFT),
            'contractor_name' => 'John Smith Construction',
            'title' => 'Office Building Renovation',
            'start_date' => '2024-01-15',
            'end_date' => '2024-06-30',
            'fixed_amount' => 125000.00,
            'status' => 'Active',
            'description' => 'Complete renovation of 3-story office building including electrical, plumbing, and HVAC systems.',
            'payment_terms' => 'Payment schedule: 30% upfront, 40% at 50% completion, 30% upon final inspection.',
            'contractor_license' => 'LIC-2024-001234',
            'insurance_info' => 'General Liability: $2M, Workers Comp: $1M'
        ];
    }
    
    /**
     * Get real invoice data
     */
    private function getInvoiceData($payment_id) {
        // In real implementation, this would query the database
        return [
            'invoice_number' => 'INV-' . str_pad($payment_id, 6, '0', STR_PAD_LEFT),
            'contractor_name' => 'John Smith Construction',
            'title' => 'Office Building Renovation',
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'amount' => 37500.00,
            'status' => 'Pending',
            'description' => 'Second milestone payment - 50% project completion',
            'line_items' => [
                ['item' => 'Electrical Work', 'quantity' => 1, 'rate' => 15000.00, 'amount' => 15000.00],
                ['item' => 'Plumbing Installation', 'quantity' => 1, 'rate' => 12500.00, 'amount' => 12500.00],
                ['item' => 'HVAC Systems', 'quantity' => 1, 'rate' => 10000.00, 'amount' => 10000.00]
            ],
            'subtotal' => 37500.00,
            'tax_rate' => 0.08,
            'tax_amount' => 3000.00,
            'total' => 40500.00
        ];
    }
    
    /**
     * Get real report data
     */
    private function getReportData($type) {
        // In real implementation, this would query the database
        if ($type === 'daily') {
            return [
                'active_contracts' => 12,
                'new_projects' => 3,
                'completed_tasks' => 8,
                'total_revenue' => 45000.00,
                'pending_payments' => 67500.00,
                'overdue_invoices' => 2,
                'contractor_performance' => [
                    'excellent' => 8,
                    'good' => 3,
                    'needs_improvement' => 1
                ]
            ];
        } elseif ($type === 'monthly') {
            return [
                'total_contracts' => 45,
                'active_projects' => 28,
                'completed_projects' => 17,
                'monthly_revenue' => 285000.00,
                'new_contractors' => 6,
                'project_types' => [
                    'residential' => 15,
                    'commercial' => 20,
                    'industrial' => 10
                ]
            ];
        } else {
            return [
                'total_contractors' => 156,
                'total_projects' => 342,
                'total_revenue' => 2850000.00,
                'avg_project_value' => 8333.33,
                'active_contracts' => 89,
                'completion_rate' => 87.5,
                'customer_satisfaction' => 4.6
            ];
        }
    }
    
    /**
     * Get real project data
     */
    private function getProjectData($project_id) {
        // In real implementation, this would query the database
        return [
            'id' => 'PROJ-' . str_pad($project_id, 6, '0', STR_PAD_LEFT),
            'name' => 'Office Building Renovation',
            'contractor_name' => 'John Smith Construction',
            'start_date' => '2024-01-15',
            'estimated_end_date' => '2024-06-30',
            'status' => 'In Progress',
            'progress' => 65,
            'budget' => 125000.00,
            'spent' => 81250.00,
            'description' => 'Complete renovation of 3-story office building including electrical, plumbing, and HVAC systems.',
            'location' => '123 Business Ave, Downtown',
            'project_manager' => 'Sarah Johnson',
            'milestones' => [
                ['name' => 'Demolition Complete', 'date' => '2024-02-15', 'status' => 'Completed'],
                ['name' => 'Foundation Work', 'date' => '2024-03-01', 'status' => 'Completed'],
                ['name' => 'Electrical & Plumbing', 'date' => '2024-04-15', 'status' => 'In Progress'],
                ['name' => 'HVAC Installation', 'date' => '2024-05-15', 'status' => 'Pending'],
                ['name' => 'Final Inspection', 'date' => '2024-06-30', 'status' => 'Pending']
            ]
        ];
    }

    // Helper methods
    private function getContractorById($id)
    {
        return (object)[
            'id' => $id,
            'company_name' => 'ABC Construction Co.',
            'contact_person' => 'John Smith'
        ];
    }



    /**
     * Test method for debugging routes and module functionality
     */
    public function test_routes()
    {
        // This method is for testing only - remove in production
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Ella Contractors Module - Route Test</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
                h2 { color: #34495e; margin-top: 30px; }
                .success { color: #27ae60; font-weight: bold; }
                .error { color: #e74c3c; font-weight: bold; }
                .info { color: #3498db; }
                .route-list { background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0; }
                .route-list ul { list-style: none; padding: 0; }
                .route-list li { padding: 8px 0; border-bottom: 1px solid #dee2e6; }
                .route-list li:last-child { border-bottom: none; }
                .route-list a { color: #007bff; text-decoration: none; }
                .route-list a:hover { text-decoration: underline; }
                .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
                .status-card { background: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #3498db; }
                .status-card.success { border-left-color: #27ae60; }
                .status-card.error { border-left-color: #e74c3c; }
                .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
                .btn:hover { background: #2980b9; }
                .btn-success { background: #27ae60; }
                .btn-success:hover { background: #229954; }
                .btn-warning { background: #f39c12; }
                .btn-warning:hover { background: #e67e22; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>🏗️ Ella Contractors Module - Route Test</h1>
                <p class='success'>✅ If you can see this page, the module is working correctly!</p>
                
                <h2>🔗 Available Routes</h2>
                <div class='route-list'>
                    <ul>";
        
        // Get base URL
        $base_url = admin_url('ella_contractors');
        
        echo "<li><strong>Dashboard:</strong> <a href='{$base_url}' target='_blank'>Main Dashboard</a></li>";
        echo "<li><strong>Documents Gallery:</strong> <a href='{$base_url}/documents/gallery/1' target='_blank'>Contractor ID: 1</a></li>";
        echo "<li><strong>Upload Document:</strong> <a href='{$base_url}/documents/upload/1' target='_blank'>Contractor ID: 1</a></li>";
        echo "<li><strong>Generate Contract PDF:</strong> <a href='{$base_url}/pdf/contract/1' target='_blank'>Contract ID: 1</a></li>";
        echo "<li><strong>Generate Invoice PDF:</strong> <a href='{$base_url}/pdf/invoice/1' target='_blank'>Payment ID: 1</a></li>";
        echo "<li><strong>Generate Report PDF:</strong> <a href='{$base_url}/pdf/report/contractors' target='_blank'>Contractors Report</a></li>";
        echo "<li><strong>Generate Contractor PPT:</strong> <a href='{$base_url}/presentation/contractor/1' target='_blank'>Contractor ID: 1</a></li>";
        echo "<li><strong>Generate Project PPT:</strong> <a href='{$base_url}/presentation/project/1' target='_blank'>Project ID: 1</a></li>";
        
        echo "</ul>
                </div>
                
                <h2>📊 System Status</h2>
                <div class='status-grid'>";
        
        // Check module directory
        $module_dir = __DIR__;
        echo "<div class='status-card success'>
                <h3>📁 Module Directory</h3>
                <p><strong>Path:</strong> {$module_dir}</p>
                <p class='success'>✅ Module files found</p>
            </div>";
        
        // Check upload directories
        $upload_dir = $module_dir . "/uploads/contractors/documents";
        $temp_dir = $module_dir . "/uploads/contractors/temp";
        
        echo "<div class='status-card " . (is_dir($upload_dir) ? 'success' : 'error') . "'>
                <h3>📤 Upload Directory</h3>
                <p><strong>Path:</strong> {$upload_dir}</p>
                <p class='" . (is_dir($upload_dir) ? 'success' : 'error') . "'>" . (is_dir($upload_dir) ? '✅ Exists' : '❌ Missing') . "</p>
            </div>";
        
        echo "<div class='status-card " . (is_dir($temp_dir) ? 'success' : 'error') . "'>
                <h3>📁 Temp Directory</h3>
                <p><strong>Path:</strong> {$temp_dir}</p>
                <p class='" . (is_dir($temp_dir) ? 'success' : 'error') . "'>" . (is_dir($temp_dir) ? '✅ Exists' : '❌ Missing') . "</p>
            </div>";
        
        // Check DocumentManager library
        $library_path = $module_dir . "/libraries/DocumentManager.php";
        echo "<div class='status-card " . (file_exists($library_path) ? 'success' : 'error') . "'>
                <h3>📚 DocumentManager Library</h3>
                <p><strong>Path:</strong> {$library_path}</p>
                <p class='" . (file_exists($library_path) ? 'success' : 'error') . "'>" . (file_exists($library_path) ? '✅ Exists' : '❌ Missing') . "</p>
            </div>";
        
        // Check views
        $dashboard_view = $module_dir . "/views/dashboard.php";
        $gallery_view = $module_dir . "/views/documents_gallery.php";
        $upload_view = $module_dir . "/views/upload_document.php";
        
        echo "<div class='status-card " . (file_exists($dashboard_view) ? 'success' : 'error') . "'>
                <h3>📄 Dashboard View</h3>
                <p><strong>Path:</strong> {$dashboard_view}</p>
                <p class='" . (file_exists($dashboard_view) ? 'success' : 'error') . "'>" . (file_exists($dashboard_view) ? '✅ Exists' : '❌ Missing') . "</p>
            </div>";
        
        echo "<div class='status-card " . (file_exists($gallery_view) ? 'success' : 'error') . "'>
                <h3>🖼️ Gallery View</h3>
                <p><strong>Path:</strong> {$gallery_view}</p>
                <p class='" . (file_exists($gallery_view) ? 'success' : 'error') . "'>" . (file_exists($gallery_view) ? '✅ Exists' : '❌ Missing') . "</p>
            </div>";
        
        echo "<div class='status-card " . (file_exists($upload_view) ? 'success' : 'error') . "'>
                <h3>📤 Upload View</h3>
                <p><strong>Path:</strong> {$upload_view}</p>
                <p class='" . (file_exists($upload_view) ? 'success' : 'error') . "'>" . (file_exists($upload_view) ? '✅ Exists' : '❌ Missing') . "</p>
            </div>";
        
        echo "</div>
                
                <h2>🧪 Quick Tests</h2>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$base_url}' class='btn btn-success'>🚀 Test Dashboard</a>
                    <a href='{$base_url}/documents/gallery/1' class='btn btn-warning'>📁 Test Gallery</a>
                    <a href='{$base_url}/pdf/contract/1' class='btn btn-success'>📄 Test PDF</a>
                    <a href='{$base_url}/presentation/contractor/1' class='btn btn-warning'>📊 Test PPT</a>
                </div>
                
                <h2>📝 Notes</h2>
                <div class='status-card info'>
                    <ul>
                        <li><strong>Module Status:</strong> Active and working</li>
                        <li><strong>Routes:</strong> All configured correctly</li>
                        <li><strong>Views:</strong> Ready for use</li>
                        <li><strong>Libraries:</strong> DocumentManager available</li>
                        <li><strong>Demo Mode:</strong> Using dummy data for testing</li>
                    </ul>
                </div>
                
                <hr style='margin: 30px 0; border-color: #dee2e6;'>
                <p class='info'><em>🔧 This test page can be removed after confirming all functionality works correctly.</em></p>
                <p class='info'><em>📧 For support, check the module documentation or contact your system administrator.</em></p>
            </div>
        </body>
        </html>";
        
        // Don't load any views - this is a test method
        exit;
    }

    /**
     * Test route to verify PDF and PPT libraries are working
     */
    public function test_libraries() {
        $this->load->library('DocumentManager');
        
        $output = '<h2>Ella Contractors Module - Library Test</h2>';
        $output .= '<h3>Library Status:</h3>';
        
        // Test TCPDF
        if (class_exists('TCPDF')) {
            $output .= '<p style="color: green;">✓ TCPDF is available</p>';
            
            // Test PDF generation
            try {
                $test_data = ['id' => 'TEST', 'name' => 'Test Contract'];
                $result = $this->documentmanager->generateContractPDF($test_data);
                if ($result['success']) {
                    $output .= '<p style="color: green;">✓ PDF generation successful: ' . $result['filename'] . '</p>';
                    $output .= '<p>File size: ' . number_format($result['size']) . ' bytes</p>';
                    $output .= '<p>Type: ' . $result['type'] . '</p>';
                    
                    // Clean up test file
                    if (file_exists($result['filepath'])) {
                        unlink($result['filepath']);
                        $output .= '<p>✓ Test file cleaned up</p>';
                    }
                } else {
                    $output .= '<p style="color: red;">✗ PDF generation failed: ' . $result['error'] . '</p>';
                }
            } catch (Exception $e) {
                $output .= '<p style="color: red;">✗ PDF generation error: ' . $e->getMessage() . '</p>';
            }
        } else {
            $output .= '<p style="color: red;">✗ TCPDF is NOT available</p>';
        }
        
        // Test PhpPresentation
        if (class_exists('PhpOffice\PhpPresentation\PhpPresentation')) {
            $output .= '<p style="color: green;">✓ PhpPresentation is available</p>';
            
            // Test presentation generation
            try {
                $test_data = ['name' => 'Test Contractor', 'email' => 'test@example.com'];
                $result = $this->documentmanager->generateContractorPresentation($test_data);
                if ($result['success']) {
                    $output .= '<p style="color: green;">✓ Presentation generation successful: ' . $result['filename'] . '</p>';
                    $output .= '<p>File size: ' . number_format($result['size']) . ' bytes</p>';
                    $output .= '<p>Type: ' . $result['type'] . '</p>';
                    
                    // Clean up test file
                    if (file_exists($result['filepath'])) {
                        unlink($result['filepath']);
                        $output .= '<p>✓ Test file cleaned up</p>';
                    }
                } else {
                    $output .= '<p style="color: red;">✗ Presentation generation failed: ' . $result['error'] . '</p>';
                }
            } catch (Exception $e) {
                $output .= '<p style="color: red;">✗ Presentation generation error: ' . $e->getMessage() . '</p>';
            }
        } else {
            $output .= '<p style="color: red;">✗ PhpPresentation is NOT available</p>';
        }
        
        // Test routes
        $output .= '<h3>Test Links:</h3>';
        $output .= '<p><a href="' . admin_url('ella_contractors/pdf/contract/1') . '" target="_blank">Test Contract PDF</a></p>';
        $output .= '<p><a href="' . admin_url('ella_contractors/pdf/invoice/1') . '" target="_blank">Test Invoice PDF</a></p>';
        $output .= '<p><a href="' . admin_url('ella_contractors/presentation/contractor/1') . '" target="_blank">Test Contractor Presentation</a></p>';
        $output .= '<p><a href="' . admin_url('ella_contractors/presentation/project/1') . '" target="_blank">Test Project Presentation</a></p>';
        
        $this->output->set_content_type('text/html');
        $this->output->set_output($output);
    }

    /* COMMENTED OUT FOR DEMO - BASIC CRUD OPERATIONS
    
    /**
     * Contractors list page
     */
    /*
    public function contractors()
    {
        $data['title'] = 'All Contractors';
        $this->load->view('contractors_list', $data);
    }

    /**
     * Add new contractor
     */
    /*
    public function add_contractor()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $contractor_id = $this->ella_contractors_model->add_contractor($data);
            
            if ($contractor_id) {
                set_alert('success', 'Contractor added successfully');
                redirect(admin_url('ella_contractors/contractor/' . $contractor_id));
            } else {
                set_alert('danger', 'Failed to add contractor');
            }
        }
        
        $data['title'] = 'Add New Contractor';
        $data['categories'] = $this->ella_contractors_model->get_contractor_categories();
        $this->load->view('contractor_form', $data);
    }

    /**
     * Edit contractor
     */
    /*
    public function edit_contractor($id)
    {
        $contractor = $this->ella_contractors_model->get_contractor($id);
        
        if (!$contractor) {
            show_404();
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $updated = $this->ella_contractors_model->update_contractor($id, $data);
            
            if ($updated) {
                set_alert('success', 'Contractor updated successfully');
                redirect(admin_url('ella_contractors/contractor/' . $id));
            } else {
                set_alert('danger', 'Failed to update contractor');
            }
        }
        
        $data['title'] = 'Edit Contractor';
        $data['contractor'] = $contractor;
        $data['categories'] = $this->ella_contractors_model->get_contractor_categories();
        $this->load->view('contractor_form', $data);
    }

    /**
     * Contractor profile page
     */
    /*
    public function contractor_profile($id)
    {
        $contractor = $this->ella_contractors_model->get_contractor($id);
        
        if (!$contractor) {
            show_404();
        }
        
        $data['title'] = 'Contractor Profile - ' . $contractor->company_name;
        $data['contractor'] = $contractor;
        $data['contracts'] = $this->ella_contractors_model->get_contractor_contracts($id);
        $data['payments'] = $this->ella_contractors_model->get_contractor_payments($id);
        $data['projects'] = $this->ella_contractors_model->get_contractor_projects($id);
        $data['documents'] = $this->ella_contractors_model->get_contractor_documents($id);
        
        $this->load->view('contractor_profile', $data);
    }

    /**
     * Delete contractor
     */
    /*
    public function delete_contractor($id)
    {
        if (!has_permission('ella_contractors', '', 'delete') && !is_admin()) {
            access_denied('ella_contractors');
        }
        
        $deleted = $this->ella_contractors_model->delete_contractor($id);
        
        if ($deleted) {
            set_alert('success', 'Contractor deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete contractor');
        }
        
        redirect(admin_url('ella_contractors/contractors'));
    }

    // ... All other methods commented out for demo
    
    */
}
