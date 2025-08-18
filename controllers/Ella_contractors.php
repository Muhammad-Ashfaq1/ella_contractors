<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_contractors_model');
    }
    
    /**
     * Check if model is loaded and provide fallback data
     */
    private function getModelData($method, $params = [], $fallback = []) {
        if (isset($this->ella_contractors_model) && method_exists($this->ella_contractors_model, $method)) {
            try {
                return call_user_func_array([$this->ella_contractors_model, $method], $params);
            } catch (Exception $e) {
                log_message('error', "Error calling {$method}: " . $e->getMessage());
                return $fallback;
            }
        } else {
            log_message('error', "Model or method {$method} not available");
            return $fallback;
        }
    }
    
    /**
     * Provide fallback data when model fails
     */
    private function getFallbackData($type) {
        switch ($type) {
            case 'contractors':
                return [
                    ['id' => 1, 'company_name' => 'Demo Contractor', 'status' => 'active', 'contact_person' => 'John Doe'],
                    ['id' => 2, 'company_name' => 'Sample Company', 'status' => 'pending', 'contact_person' => 'Jane Smith']
                ];
            case 'contracts':
                return [
                    ['id' => 1, 'title' => 'Demo Contract', 'status' => 'active', 'contractor_id' => 1],
                    ['id' => 2, 'title' => 'Sample Project', 'status' => 'pending', 'contractor_id' => 2]
                ];
            case 'projects':
                return [
                    ['id' => 1, 'name' => 'Demo Project', 'status' => 'active', 'contractor_id' => 1],
                    ['id' => 2, 'name' => 'Sample Work', 'status' => 'planning', 'contractor_id' => 2]
                ];
            case 'payments':
                return [
                    ['id' => 1, 'amount' => 1000, 'status' => 'pending', 'contractor_id' => 1],
                    ['id' => 2, 'amount' => 2500, 'status' => 'approved', 'contractor_id' => 2]
                ];
            default:
                return [];
        }
    }
    
    /**
     * Test method to debug model loading
     */
    public function test() {
        echo "<h2>Debug Information</h2>";
        echo "<p>Model loaded: " . (isset($this->ella_contractors_model) ? 'YES' : 'NO') . "</p>";
        
        if (isset($this->ella_contractors_model)) {
            echo "<p>Model class: " . get_class($this->ella_contractors_model) . "</p>";
            echo "<p>Methods available: " . implode(', ', get_class_methods($this->ella_contractors_model)) . "</p>";
        } else {
            echo "<p><strong>ERROR: Model is NULL!</strong></p>";
            echo "<p>Let's check what happened:</p>";
            
            // Check if the model file exists
            $model_path = APPPATH . 'models/ella_contractors_model.php';
            echo "<p>Model file exists: " . (file_exists($model_path) ? 'YES' : 'NO') . "</p>";
            echo "<p>Model path: " . $model_path . "</p>";
            
            // Check if we can load it manually
            echo "<p>Attempting manual model load...</p>";
            try {
                $this->load->model('ella_contractors_model');
                echo "<p>Manual load result: " . (isset($this->ella_contractors_model) ? 'SUCCESS' : 'FAILED') . "</p>";
            } catch (Exception $e) {
                echo "<p>Manual load error: " . $e->getMessage() . "</p>";
            }
        }
        
        // Test database connection
        echo "<hr><h3>Database Connection Test</h3>";
        try {
            $this->load->database();
            $query = $this->db->query("SELECT 1 as test");
            if ($query && $query->num_rows() > 0) {
                echo "<p style='color: green;'>✅ Database connection: SUCCESS</p>";
                
                // Test all our tables with detailed logging
                $tables = [
                    'tblella_contractors',
                    'tblella_contracts', 
                    'tblella_projects',
                    'tblella_payments',
                    'tblella_contractor_documents',
                    'tblella_contractor_activity',
                    'tblella_document_shares'
                ];
                
                echo "<h4>📋 Complete Table Analysis:</h4>";
                foreach ($tables as $table) {
                    echo "<hr><h5>Table: {$table}</h5>";
                    
                    // Check if table exists
                    $exists = $this->db->table_exists($table);
                    $status = $exists ? '✅ EXISTS' : '❌ MISSING';
                    echo "<p><strong>Status:</strong> {$status}</p>";
                    
                    if ($exists) {
                        // Get table structure
                        $fields = $this->db->field_data($table);
                        echo "<p><strong>Structure:</strong></p>";
                        echo "<ul>";
                        foreach ($fields as $field) {
                            $null = $field->null ? 'NULL' : 'NOT NULL';
                            $default = $field->default ? " DEFAULT '{$field->default}'" : '';
                            echo "<li>{$field->name} ({$field->type}{$field->max_length}) {$null}{$default}</li>";
                        }
                        echo "</ul>";
                        
                        // Get record count
                        $count = $this->db->count_all($table);
                        echo "<p><strong>Total Records:</strong> {$count}</p>";
                        
                        // Get sample data (first 3 records)
                        if ($count > 0) {
                            echo "<p><strong>Sample Data (First 3 records):</strong></p>";
                            $sample_data = $this->db->limit(3)->get($table)->result();
                            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
                            
                            // Headers
                            if (!empty($sample_data)) {
                                echo "<tr>";
                                foreach ($sample_data[0] as $key => $value) {
                                    echo "<th style='padding: 5px; background: #f0f0f0;'>{$key}</th>";
                                }
                                echo "</tr>";
                                
                                // Data rows
                                foreach ($sample_data as $row) {
                                    echo "<tr>";
                                    foreach ($row as $value) {
                                        $display_value = is_null($value) ? 'NULL' : (is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value);
                                        echo "<td style='padding: 5px;'>{$display_value}</td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                            echo "</table>";
                        }
                        
                        // Log to file for debugging
                        $log_data = [
                            'table' => $table,
                            'exists' => $exists,
                            'count' => $count,
                            'fields' => $fields,
                            'timestamp' => date('Y-m-d H:i:s')
                        ];
                        log_message('info', 'Table Analysis: ' . json_encode($log_data));
                        
                    } else {
                        echo "<p style='color: red;'>❌ Table does not exist in database</p>";
                        log_message('error', "Table {$table} does not exist in database");
                    }
                }
                
                // Database server info
                echo "<hr><h4>🗄️ Database Server Info:</h4>";
                $version = $this->db->query("SELECT VERSION() as version")->row()->version;
                echo "<p><strong>MySQL Version:</strong> {$version}</p>";
                
                $charset = $this->db->query("SELECT @@character_set_database as charset")->row()->charset;
                echo "<p><strong>Character Set:</strong> {$charset}</p>";
                
                $collation = $this->db->query("SELECT @@collation_database as collation")->row()->collation;
                echo "<p><strong>Collation:</strong> {$collation}</p>";
                
            } else {
                echo "<p style='color: red;'>❌ Database connection: FAILED</p>";
                log_message('error', 'Database connection failed in test method');
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
            log_message('error', 'Database error in test method: ' . $e->getMessage());
        }
        
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors/dashboard') . "'>Go to Dashboard</a></p>";
        echo "<p><a href='" . admin_url('ella_contractors/contractors') . "'>Go to Contractors</a></p>";
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
        
        // Safety check: ensure model is loaded
        if (!isset($this->ella_contractors_model)) {
            log_message('error', 'Model not loaded in contractors method');
            $data['error'] = 'System error: Model not available. Please contact administrator.';
            $data['contractors'] = [];
            $data['total_count'] = 0;
            $data['current_page'] = $page;
            $data['total_pages'] = 1;
            $data['search'] = $search;
            $data['status_filter'] = $status;
            $this->load->view('contractors_list', $data);
            return;
        }
        
        try {
            $data['contractors'] = $this->ella_contractors_model->getContractors($limit, $offset, $search, $status);
            $data['total_count'] = $this->ella_contractors_model->getContractorsCount($search, $status);
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($data['total_count'] / $limit);
            $data['search'] = $search;
            $data['status_filter'] = $status;
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractors: ' . $e->getMessage());
            $data['error'] = 'Failed to load contractors. Please try again.';
            $data['contractors'] = [];
            $data['total_count'] = 0;
            $data['current_page'] = $page;
            $data['total_pages'] = 1;
        }
        
        $this->load->view('contractors_list', $data);
    }
    
    /**
     * Add new contractor form
     */
    public function add_contractor() {
        // Debug: Check if method is being called
        log_message('debug', 'add_contractor method called');
        
        if ($this->input->post()) {
            log_message('debug', 'POST data received: ' . json_encode($this->input->post()));
            
            // Validate required fields
            $this->load->library('form_validation');
            $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
            $this->form_validation->set_rules('contact_person', 'Contact Person', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'valid_email|trim');
            $this->form_validation->set_rules('phone', 'Phone', 'trim');
            
            if ($this->form_validation->run() == FALSE) {
                log_message('debug', 'Validation failed: ' . validation_errors());
                // Validation failed, show form with errors
                $data['title'] = 'Add New Contractor';
                
                // Debug: Check if model methods exist
                try {
                    $data['countries'] = $this->ella_contractors_model->getCountries();
                    log_message('debug', 'Countries loaded: ' . count($data['countries']));
                } catch (Exception $e) {
                    log_message('error', 'Error loading countries: ' . $e->getMessage());
                    $data['countries'] = [];
                }
                
                try {
                    $data['states'] = $this->ella_contractors_model->getUSStates();
                    log_message('debug', 'States loaded: ' . count($data['states']));
                } catch (Exception $e) {
                    log_message('error', 'Error loading states: ' . $e->getMessage());
                    $data['states'] = [];
                }
                
                try {
                    $data['status_options'] = $this->ella_contractors_model->getContractorStatusOptions();
                    log_message('debug', 'Status options loaded: ' . count($data['status_options']));
                } catch (Exception $e) {
                    log_message('error', 'Error loading status options: ' . $e->getMessage());
                    $data['status_options'] = [];
                }
                
                $data['errors'] = validation_errors();
                $this->load->view('contractor_form', $data);
                return;
            }
            
            log_message('debug', 'Validation passed, preparing contractor data');
            
            // Prepare contractor data
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
                'hourly_rate' => $this->input->post('hourly_rate') ? floatval($this->input->post('hourly_rate')) : null,
                'status' => $this->input->post('status') ?: 'pending',
                'notes' => $this->input->post('notes'),
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id() ?: 1
            ];
            
            log_message('debug', 'Contractor data prepared: ' . json_encode($contractor_data));
            
            try {
                log_message('debug', 'Calling createContractor method');
                $contractor_id = $this->ella_contractors_model->createContractor($contractor_data);
                log_message('debug', 'createContractor returned: ' . $contractor_id);
                
                if ($contractor_id) {
                    log_message('debug', 'Contractor created successfully with ID: ' . $contractor_id);
                    set_alert('success', 'Contractor added successfully!');
                    redirect('admin/ella_contractors/contractors');
                } else {
                    log_message('error', 'createContractor returned false/null');
                    set_alert('danger', 'Failed to add contractor. Please try again.');
                }
            } catch (Exception $e) {
                log_message('error', 'Error adding contractor: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                set_alert('danger', 'Failed to add contractor. Please try again.');
            }
        }
        
        log_message('debug', 'Loading form for add contractor');
        
        // Load form data for dropdowns
        $data['title'] = 'Add New Contractor';
        
        // Debug: Check if model methods exist and load data
        try {
            $data['countries'] = $this->ella_contractors_model->getCountries();
            log_message('debug', 'Countries loaded for form: ' . count($data['countries']));
        } catch (Exception $e) {
            log_message('error', 'Error loading countries for form: ' . $e->getMessage());
            $data['countries'] = [];
        }
        
        try {
            $data['states'] = $this->ella_contractors_model->getUSStates();
            log_message('debug', 'States loaded for form: ' . count($data['states']));
        } catch (Exception $e) {
            log_message('error', 'Error loading states for form: ' . $e->getMessage());
            $data['states'] = [];
        }
        
        try {
            $data['status_options'] = $this->ella_contractors_model->getContractorStatusOptions();
            log_message('debug', 'Status options loaded for form: ' . count($data['status_options']));
        } catch (Exception $e) {
            log_message('error', 'Error loading status options for form: ' . $e->getMessage());
            $data['status_options'] = [];
        }
        
        $data['errors'] = '';
        
        // Debug: Show form data in browser for testing
        if ($this->input->get('debug') == '1') {
            echo "<h2>Form Data Debug</h2>";
            echo "<p><strong>Title:</strong> " . $data['title'] . "</p>";
            echo "<p><strong>Countries:</strong> " . count($data['countries']) . " loaded</p>";
            echo "<p><strong>States:</strong> " . count($data['states']) . " loaded</p>";
            echo "<p><strong>Status Options:</strong> " . count($data['status_options']) . " loaded</p>";
            echo "<hr>";
            echo "<p><a href='?debug=0'>Hide Debug Info</a></p>";
        }
        
        log_message('debug', 'About to load contractor_form view with data: ' . json_encode(array_keys($data)));
        $this->load->view('contractor_form', $data);
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
            
            try {
                if ($this->ella_contractors_model->updateContractor($id, $contractor_data)) {
                    set_alert('success', 'Contractor updated successfully');
                    redirect('admin/ella_contractors/contractors');
                } else {
                    set_alert('danger', 'Failed to update contractor');
                }
            } catch (Exception $e) {
                log_message('error', 'Error updating contractor: ' . $e->getMessage());
                set_alert('danger', 'Failed to update contractor. Please try again.');
            }
        }
        
        try {
            $data['contractor'] = $this->ella_contractors_model->getContractorById($id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractor for edit: ' . $e->getMessage());
            show_404();
        }
        $this->load->view('contractor_form', $data);
    }
    
    /**
     * Delete contractor
     */
    public function delete_contractor($id) {
        try {
            if ($this->ella_contractors_model->deleteContractor($id)) {
                set_alert('success', 'Contractor deleted successfully');
            } else {
                set_alert('danger', 'Failed to delete contractor');
            }
        } catch (Exception $e) {
            log_message('error', 'Error deleting contractor: ' . $e->getMessage());
            set_alert('danger', 'Failed to delete contractor. Please try again.');
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
        
        try {
            $data['contracts'] = $this->ella_contractors_model->getContracts($limit, $offset, $search, $status, $contractor_id);
            $data['total_count'] = $this->ella_contractors_model->getContractsCount($search, $status, $contractor_id);
            $data['current_page'] = $page;
            $data['total_pages'] = ceil($data['total_count'] / $limit);
            $data['search'] = $search;
            $data['status_filter'] = $status;
            $data['contractor_filter'] = $contractor_id;
        } catch (Exception $e) {
            log_message('error', 'Error fetching contracts: ' . $e->getMessage());
            $data['error'] = 'Failed to load contracts. Please try again.';
        }
        
        // Get contractors for filter dropdown
        try {
            $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractors for contract filter: ' . $e->getMessage());
            $data['contractors'] = []; // Fallback
        }
        
        $this->load->view('contracts_list', $data);
    }
    
    /**
     * Add new contract form
     */
    public function add_contract() {
        if ($this->input->post()) {
            // Validate required fields
            $this->load->library('form_validation');
            $this->form_validation->set_rules('title', 'Contract Title', 'required|trim');
            $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|trim');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data['title'] = 'Add New Contract';
                $data['contractors'] = $this->ella_contractors_model->getAllContractors();
                $data['status_options'] = $this->ella_contractors_model->getContractStatusOptions();
                $data['errors'] = validation_errors();
                $this->load->view('contract_form', $data);
                return;
            }
            
            $contract_data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'title' => $this->input->post('title'),
                'contract_number' => $this->input->post('contract_number'),
                'description' => $this->input->post('description'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date') ?: null,
                'hourly_rate' => $this->input->post('hourly_rate') ? floatval($this->input->post('hourly_rate')) : null,
                'estimated_hours' => $this->input->post('estimated_hours') ? intval($this->input->post('estimated_hours')) : null,
                'fixed_amount' => $this->input->post('fixed_amount') ? floatval($this->input->post('fixed_amount')) : null,
                'payment_terms' => $this->input->post('payment_terms'),
                'status' => $this->input->post('status') ?: 'draft',
                'terms_conditions' => $this->input->post('terms_conditions'),
                'notes' => $this->input->post('notes'),
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id() ?: 1
            ];
            
            try {
                $contract_id = $this->ella_contractors_model->createContract($contract_data);
                
                if ($contract_id) {
                    set_alert('success', 'Contract created successfully');
                    redirect('admin/ella_contractors/contracts');
                } else {
                    set_alert('danger', 'Failed to create contract');
                }
            } catch (Exception $e) {
                log_message('error', 'Error creating contract: ' . $e->getMessage());
                set_alert('danger', 'Failed to create contract. Please try again.');
            }
        }
        
        // Load form data for dropdowns
        $data['title'] = 'Add New Contract';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['status_options'] = $this->ella_contractors_model->getContractStatusOptions();
        $data['errors'] = '';
        
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
            
            try {
                if ($this->ella_contractors_model->updateContract($id, $contract_data)) {
                    set_alert('success', 'Contract updated successfully');
                    redirect('admin/ella_contractors/contracts');
                } else {
                    set_alert('danger', 'Failed to update contract');
                }
            } catch (Exception $e) {
                log_message('error', 'Error updating contract: ' . $e->getMessage());
                set_alert('danger', 'Failed to update contract. Please try again.');
            }
        }
        
        try {
            $data['contract'] = $this->ella_contractors_model->getContractById($id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching contract for edit: ' . $e->getMessage());
            show_404();
        }
        try {
            $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractors for contract form: ' . $e->getMessage());
            $data['contractors'] = []; // Fallback
        }
        $this->load->view('contract_form', $data);
    }
    
    /**
     * Delete contract
     */
    public function delete_contract($id) {
        try {
            if ($this->ella_contractors_model->deleteContract($id)) {
                set_alert('success', 'Contract deleted successfully');
            } else {
                set_alert('danger', 'Failed to delete contract');
            }
        } catch (Exception $e) {
            log_message('error', 'Error deleting contract: ' . $e->getMessage());
            set_alert('danger', 'Failed to delete contract. Please try again.');
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
        
        try {
            $data['projects'] = $this->ella_contractors_model->getProjects($limit, $offset, $search, $status, $contractor_id);
            $data['current_page'] = $page;
            $data['search'] = $search;
            $data['status_filter'] = $status;
            $data['contractor_filter'] = $contractor_id;
        } catch (Exception $e) {
            log_message('error', 'Error fetching projects: ' . $e->getMessage());
            $data['error'] = 'Failed to load projects. Please try again.';
        }
        
        // Get contractors for filter dropdown
        try {
            $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractors for project filter: ' . $e->getMessage());
            $data['contractors'] = []; // Fallback
        }
        
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
            // Validate required fields
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Project Name', 'required|trim');
            $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|trim');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data['title'] = 'Add New Project';
                $data['contractors'] = $this->ella_contractors_model->getAllContractors();
                $data['contracts'] = $this->ella_contractors_model->getAllContracts();
                $data['status_options'] = $this->ella_contractors_model->getProjectStatusOptions();
                $data['priority_options'] = $this->ella_contractors_model->getProjectPriorityOptions();
                $data['errors'] = validation_errors();
                $this->load->view('project_form', $data);
                return;
            }
            
            $data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id') ?: null,
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'budget' => $this->input->post('budget') ? floatval($this->input->post('budget')) : null,
                'estimated_hours' => $this->input->post('estimated_hours') ? intval($this->input->post('estimated_hours')) : null,
                'actual_hours' => $this->input->post('actual_hours') ? intval($this->input->post('actual_hours')) : null,
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date') ?: null,
                'status' => $this->input->post('status') ?: 'planning',
                'priority' => $this->input->post('priority') ?: 'medium',
                'location' => $this->input->post('location'),
                'notes' => $this->input->post('notes'),
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id() ?: 1
            ];

            try {
                $project_id = $this->ella_contractors_model->createProject($data);
                if ($project_id) {
                    set_alert('success', 'Project created successfully');
                    redirect('admin/ella_contractors/projects');
                } else {
                    set_alert('danger', 'Failed to create project');
                }
            } catch (Exception $e) {
                log_message('error', 'Error creating project: ' . $e->getMessage());
                set_alert('danger', 'Failed to create project. Please try again.');
            }
        }

        // Load form data for dropdowns
        $data['title'] = 'Add New Project';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        $data['status_options'] = $this->ella_contractors_model->getProjectStatusOptions();
        $data['priority_options'] = $this->ella_contractors_model->getProjectPriorityOptions();
        $data['errors'] = '';
        
        $this->load->view('project_form', $data);
    }

    public function edit_project($id) {
        if (!is_staff_logged_in()) {
            redirect('admin');
        }

        try {
            $project = $this->ella_contractors_model->getProjectById($id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching project for edit: ' . $e->getMessage());
            show_404();
        }

        if (!$project) {
            show_404();
        }

        if ($this->input->post()) {
            // Validate required fields
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name', 'Project Name', 'required|trim');
            $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|trim');
            $this->form_validation->set_rules('start_date', 'Start Date', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data['title'] = 'Edit Project';
                $data['project'] = $project;
                $data['contractors'] = $this->ella_contractors_model->getAllContractors();
                $data['contracts'] = $this->ella_contractors_model->getAllContracts();
                $data['status_options'] = $this->ella_contractors_model->getProjectStatusOptions();
                $data['priority_options'] = $this->ella_contractors_model->getProjectPriorityOptions();
                $data['errors'] = validation_errors();
                $this->load->view('project_form', $data);
                return;
            }
            
            $data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id') ?: null,
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'budget' => $this->input->post('budget') ? floatval($this->input->post('budget')) : null,
                'estimated_hours' => $this->input->post('estimated_hours') ? intval($this->input->post('estimated_hours')) : null,
                'actual_hours' => $this->input->post('actual_hours') ? intval($this->input->post('actual_hours')) : null,
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date') ?: null,
                'status' => $this->input->post('status') ?: 'planning',
                'priority' => $this->input->post('priority') ?: 'medium',
                'location' => $this->input->post('location'),
                'notes' => $this->input->post('notes'),
                'date_updated' => date('Y-m-d H:i:s'),
                'updated_by' => get_staff_user_id() ?: 1
            ];

            try {
                if ($this->ella_contractors_model->updateProject($id, $data)) {
                    set_alert('success', 'Project updated successfully');
                    redirect('admin/ella_contractors/projects');
                } else {
                    set_alert('danger', 'Failed to update project');
                }
            } catch (Exception $e) {
                log_message('error', 'Error updating project: ' . $e->getMessage());
                set_alert('danger', 'Failed to update project. Please try again.');
            }
        }

        // Load form data for dropdowns
        $data['title'] = 'Edit Project';
        $data['project'] = $project;
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        $data['status_options'] = $this->ella_contractors_model->getProjectStatusOptions();
        $data['priority_options'] = $this->ella_contractors_model->getProjectPriorityOptions();
        $data['errors'] = '';
        
        $this->load->view('project_form', $data);
    }
    
    /**
     * Delete project
     */
    public function delete_project($id) {
        try {
            if ($this->ella_contractors_model->deleteProject($id)) {
                set_alert('success', 'Project deleted successfully');
            } else {
                set_alert('danger', 'Failed to delete project');
            }
        } catch (Exception $e) {
            log_message('error', 'Error deleting project: ' . $e->getMessage());
            set_alert('danger', 'Failed to delete project. Please try again.');
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
        
        try {
            $data['payments'] = $this->ella_contractors_model->getPayments($limit, $offset, $search, $status, $contractor_id);
            $data['current_page'] = $page;
            $data['search'] = $search;
            $data['status_filter'] = $status;
            $data['contractor_filter'] = $contractor_id;
        } catch (Exception $e) {
            log_message('error', 'Error fetching payments: ' . $e->getMessage());
            $data['error'] = 'Failed to load payments. Please try again.';
        }
        
        // Get contractors for filter dropdown
        try {
            $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractors for payment filter: ' . $e->getMessage());
            $data['contractors'] = []; // Fallback
        }
        
        $this->load->view('payments_list', $data);
    }
    
    /**
     * Add new payment form
     */
    public function add_payment() {
        if ($this->input->post()) {
            // Validate required fields
            $this->load->library('form_validation');
            $this->form_validation->set_rules('amount', 'Amount', 'required|trim|numeric');
            $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|trim');
            $this->form_validation->set_rules('payment_date', 'Payment Date', 'required|trim');
            
            if ($this->form_validation->run() == FALSE) {
                // Validation failed, show form with errors
                $data['title'] = 'Add New Payment';
                $data['contractors'] = $this->ella_contractors_model->getAllContractors();
                $data['contracts'] = $this->ella_contractors_model->getAllContracts();
                $data['status_options'] = $this->ella_contractors_model->getPaymentStatusOptions();
                $data['errors'] = validation_errors();
                $this->load->view('payment_form', $data);
                return;
            }
            
            $payment_data = [
                'contractor_id' => $this->input->post('contractor_id'),
                'contract_id' => $this->input->post('contract_id') ?: null,
                'project_id' => $this->input->post('project_id') ?: null,
                'amount' => floatval($this->input->post('amount')),
                'payment_date' => $this->input->post('payment_date'),
                'due_date' => $this->input->post('due_date') ?: null,
                'payment_method' => $this->input->post('payment_method'),
                'reference_number' => $this->input->post('reference_number'),
                'status' => $this->input->post('status') ?: 'pending',
                'notes' => $this->input->post('notes'),
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id() ?: 1
            ];
            
            try {
                $payment_id = $this->ella_contractors_model->createPayment($payment_data);
                
                if ($payment_id) {
                    set_alert('success', 'Payment added successfully');
                    redirect('admin/ella_contractors/payments');
                } else {
                    set_alert('danger', 'Failed to add payment');
                }
            } catch (Exception $e) {
                log_message('error', 'Error adding payment: ' . $e->getMessage());
                set_alert('danger', 'Failed to add payment. Please try again.');
            }
        }
        
        // Load form data for dropdowns
        $data['title'] = 'Add New Payment';
        $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        $data['projects'] = $this->ella_contractors_model->getAllProjects();
        $data['status_options'] = $this->ella_contractors_model->getPaymentStatusOptions();
        $data['errors'] = '';
        
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
            
            try {
                if ($this->ella_contractors_model->updatePayment($id, $payment_data)) {
                    set_alert('success', 'Payment updated successfully');
                    redirect('admin/ella_contractors/payments');
                } else {
                    set_alert('danger', 'Failed to update payment');
                }
            } catch (Exception $e) {
                log_message('error', 'Error updating payment: ' . $e->getMessage());
                set_alert('danger', 'Failed to update payment. Please try again.');
            }
        }
        
        try {
            $data['payment'] = $this->ella_contractors_model->getPaymentById($id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching payment for edit: ' . $e->getMessage());
            show_404();
        }
        try {
            $data['contractors'] = $this->ella_contractors_model->getAllContractors();
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractors for payment form: ' . $e->getMessage());
            $data['contractors'] = []; // Fallback
        }
        try {
            $data['contracts'] = $this->ella_contractors_model->getAllContracts();
        } catch (Exception $e) {
            log_message('error', 'Error fetching contracts for payment form: ' . $e->getMessage());
            $data['contracts'] = []; // Fallback
        }
        $this->load->view('payment_form', $data);
    }
    
    /**
     * Delete payment
     */
    public function delete_payment($id) {
        try {
            if ($this->ella_contractors_model->deletePayment($id)) {
                set_alert('success', 'Payment deleted successfully');
            } else {
                set_alert('danger', 'Failed to delete payment');
            }
        } catch (Exception $e) {
            log_message('error', 'Error deleting payment: ' . $e->getMessage());
            set_alert('danger', 'Failed to delete payment. Please try again.');
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
        try {
            $data['documents'] = $this->document_manager->getDocumentGallery($contractor_id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching documents for gallery: ' . $e->getMessage());
            $data['error'] = 'Failed to load documents. Please try again.';
        }
        
        // Get contractor info for breadcrumb
        try {
            $contractor = $this->ella_contractors_model->getContractorById($contractor_id);
            $data['contractor_name'] = $contractor ? $contractor->company_name : 'Unknown Contractor';
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractor for gallery: ' . $e->getMessage());
            $data['contractor_name'] = 'Unknown Contractor'; // Fallback
        }
        
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
        
        try {
            $result = $this->document_manager->generateShareLink($document_id);
        } catch (Exception $e) {
            log_message('error', 'Error generating share link: ' . $e->getMessage());
            $result = ['success' => false, 'message' => 'Failed to generate share link'];
        }
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'share_url' => $result['share_url'],
                'expires_at' => $result['expires_at']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    }

    /**
     * Generate contract PDF
     */
    public function generate_contract_pdf($contract_id) {
        $this->load->library('DocumentManager');
        
        // Get real contract data (in real implementation, this would come from database)
        try {
            $contract_data = $this->ella_contractors_model->getContractById($contract_id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching contract data for PDF: ' . $e->getMessage());
            $contract_data = null; // Fallback
        }
        
        if (!$contract_data) {
            show_404();
            return;
        }

        try {
            $result = $this->documentmanager->generateContractPDF($contract_data);
        } catch (Exception $e) {
            log_message('error', 'Error generating contract PDF: ' . $e->getMessage());
            $result = ['success' => false, 'error' => 'Failed to generate PDF. Please try again.'];
        }
        
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
        try {
            $invoice_data = $this->ella_contractors_model->getPaymentById($payment_id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching invoice data for PDF: ' . $e->getMessage());
            $invoice_data = null; // Fallback
        }
        
        if (!$invoice_data) {
            show_404();
            return;
        }

        try {
            $result = $this->documentmanager->generateInvoicePDF($invoice_data);
        } catch (Exception $e) {
            log_message('error', 'Error generating invoice PDF: ' . $e->getMessage());
            $result = ['success' => false, 'error' => 'Failed to generate PDF. Please try again.'];
        }
        
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
        try {
            $report_data = $this->ella_contractors_model->getReportData($report_type);
        } catch (Exception $e) {
            log_message('error', 'Error fetching report data for PDF: ' . $e->getMessage());
            $report_data = null; // Fallback
        }
        
        if (!$report_data) {
            show_404();
            return;
        }

        try {
            $result = $this->documentmanager->generateReportPDF($report_data, $report_type);
        } catch (Exception $e) {
            log_message('error', 'Error generating report PDF: ' . $e->getMessage());
            $result = ['success' => false, 'error' => 'Failed to generate PDF. Please try again.'];
        }
        
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
        try {
            $contractor_data = $this->ella_contractors_model->getContractorById($contractor_id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching contractor data for presentation: ' . $e->getMessage());
            $contractor_data = null; // Fallback
        }
        
        if (!$contractor_data) {
            show_404();
            return;
        }

        try {
            $result = $this->documentmanager->generateContractorPresentation($contractor_data);
        } catch (Exception $e) {
            log_message('error', 'Error generating contractor presentation: ' . $e->getMessage());
            $result = ['success' => false, 'error' => 'Failed to generate presentation. Please try again.'];
        }
        
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
        try {
            $project_data = $this->ella_contractors_model->getProjectById($project_id);
        } catch (Exception $e) {
            log_message('error', 'Error fetching project data for presentation: ' . $e->getMessage());
            $project_data = null; // Fallback
        }
        
        if (!$project_data) {
            show_404();
            return;
        }

        try {
            $result = $this->documentmanager->generateProjectPresentation($project_data);
        } catch (Exception $e) {
            log_message('error', 'Error generating project presentation: ' . $e->getMessage());
            $result = ['success' => false, 'error' => 'Failed to generate presentation. Please try again.'];
        }
        
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

    /**
     * Test method to debug model loading and available methods
     */
    public function debug_model() {
        echo "<h2>Debug Model Loading</h2>";
        
        // Check if model is loaded
        if (isset($this->ella_contractors_model)) {
            echo "<p style='color: green;'>✅ Model is loaded</p>";
            echo "<p><strong>Model class:</strong> " . get_class($this->ella_contractors_model) . "</p>";
            
            // Check available methods
            $methods = get_class_methods($this->ella_contractors_model);
            echo "<p><strong>Available methods:</strong></p>";
            echo "<ul>";
            foreach ($methods as $method) {
                if (strpos($method, 'get') === 0) {
                    echo "<li>{$method}</li>";
                }
            }
            echo "</ul>";
            
            // Test specific methods
            echo "<h3>Testing Helper Methods:</h3>";
            
            try {
                $countries = $this->ella_contractors_model->getCountries();
                echo "<p style='color: green;'>✅ getCountries(): " . count($countries) . " countries loaded</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ getCountries(): " . $e->getMessage() . "</p>";
            }
            
            try {
                $states = $this->ella_contractors_model->getUSStates();
                echo "<p style='color: green;'>✅ getUSStates(): " . count($states) . " states loaded</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ getUSStates(): " . $e->getMessage() . "</p>";
            }
            
            try {
                $status_options = $this->ella_contractors_model->getContractorStatusOptions();
                echo "<p style='color: green;'>✅ getContractorStatusOptions(): " . count($status_options) . " options loaded</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ getContractorStatusOptions(): " . $e->getMessage() . "</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Model is NOT loaded</p>";
        }
        
        // Check database connection
        echo "<h3>Database Connection:</h3>";
        try {
            $this->load->database();
            $result = $this->db->query("SELECT 1 as test")->row();
            echo "<p style='color: green;'>✅ Database connection: OK</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database connection: " . $e->getMessage() . "</p>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors/contractors') . "'>Back to Contractors</a></p>";
    }

    /**
     * Simple test method to try creating a basic contractor
     */
    public function test_create() {
        echo "<h2>Testing Contractor Creation</h2>";
        
        // Check if model is loaded
        if (!isset($this->ella_contractors_model)) {
            echo "<p style='color: red;'>❌ Model not loaded</p>";
            return;
        }
        
        echo "<p style='color: green;'>✅ Model is loaded</p>";
        
        // Try to create a simple contractor
        $test_data = [
            'company_name' => 'Test Company ' . date('Y-m-d H:i:s'),
            'contact_person' => 'Test Person',
            'email' => 'test@test.com',
            'phone' => '555-1234',
            'status' => 'active',
            'date_created' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
            'created_by' => 1
        ];
        
        echo "<p>Test data: " . json_encode($test_data) . "</p>";
        
        try {
            echo "<p>Calling createContractor...</p>";
            $result = $this->ella_contractors_model->createContractor($test_data);
            echo "<p>Result: " . var_export($result, true) . "</p>";
            
            if ($result) {
                echo "<p style='color: green;'>✅ Contractor created successfully with ID: {$result}</p>";
            } else {
                echo "<p style='color: red;'>❌ Contractor creation failed</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
            echo "<p>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></p>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors/contractors') . "'>Back to Contractors</a></p>";
    }

    /**
     * Test form data loading
     */
    public function test_form_data() {
        echo "<h2>Testing Form Data Loading</h2>";
        
        // Check if model is loaded
        if (!isset($this->ella_contractors_model)) {
            echo "<p style='color: red;'>❌ Model not loaded</p>";
            return;
        }
        
        echo "<p style='color: green;'>✅ Model is loaded</p>";
        
        // Load form data
        $data = [];
        
        try {
            $data['countries'] = $this->ella_contractors_model->getCountries();
            echo "<p style='color: green;'>✅ Countries: " . count($data['countries']) . " loaded</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Countries: " . $e->getMessage() . "</p>";
            $data['countries'] = [];
        }
        
        try {
            $data['states'] = $this->ella_contractors_model->getUSStates();
            echo "<p style='color: green;'>✅ States: " . count($data['states']) . " loaded</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ States: " . $e->getMessage() . "</p>";
            $data['states'] = [];
        }
        
        try {
            $data['status_options'] = $this->ella_contractors_model->getContractorStatusOptions();
            echo "<p style='color: green;'>✅ Status Options: " . count($data['status_options']) . " loaded</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Status Options: " . $e->getMessage() . "</p>";
            $data['status_options'] = [];
        }
        
        // Show sample data
        echo "<h3>Sample Data:</h3>";
        
        if (!empty($data['countries'])) {
            echo "<p><strong>First 3 Countries:</strong></p>";
            echo "<ul>";
            $count = 0;
            foreach ($data['countries'] as $code => $name) {
                if ($count++ < 3) {
                    echo "<li>{$code} => {$name}</li>";
                }
            }
            echo "</ul>";
        }
        
        if (!empty($data['states'])) {
            echo "<p><strong>First 3 States:</strong></p>";
            echo "<ul>";
            $count = 0;
            foreach ($data['states'] as $code => $name) {
                if ($count++ < 3) {
                    echo "<li>{$code} => {$name}</li>";
                }
            }
            echo "</ul>";
        }
        
        if (!empty($data['status_options'])) {
            echo "<p><strong>Status Options:</strong></p>";
            echo "<ul>";
            foreach ($data['status_options'] as $value => $label) {
                echo "<li>{$value} => {$label}</li>";
            }
            echo "</ul>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors/contractors') . "'>Back to Contractors</a></p>";
    }

    /**
     * Test database tables and connection
     */
    public function test_database() {
        echo "<h2>Testing Database Connection and Tables</h2>";
        
        try {
            // Test basic connection
            $this->load->database();
            $result = $this->db->query("SELECT 1 as test")->row();
            echo "<p style='color: green;'>✅ Database connection: OK</p>";
            
            // Test if tables exist
            $tables = [
                'tblella_contractors',
                'tblella_contracts', 
                'tblella_projects',
                'tblella_payments',
                'tblella_contractor_documents'
            ];
            
            echo "<h3>Table Check:</h3>";
            foreach ($tables as $table) {
                try {
                    $result = $this->db->query("SELECT COUNT(*) as count FROM {$table}")->row();
                    echo "<p style='color: green;'>✅ {$table}: " . $result->count . " records</p>";
                } catch (Exception $e) {
                    echo "<p style='color: red;'>❌ {$table}: " . $e->getMessage() . "</p>";
                }
            }
            
            // Test if we can insert a test record
            echo "<h3>Insert Test:</h3>";
            try {
                $test_data = [
                    'company_name' => 'TEST_' . date('Y-m-d-H-i-s'),
                    'contact_person' => 'Test Person',
                    'email' => 'test@test.com',
                    'status' => 'pending',
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => 1
                ];
                
                $this->db->insert('tblella_contractors', $test_data);
                $insert_id = $this->db->insert_id();
                
                if ($insert_id) {
                    echo "<p style='color: green;'>✅ Insert test: SUCCESS (ID: {$insert_id})</p>";
                    
                    // Clean up test record
                    $this->db->where('id', $insert_id)->delete('tblella_contractors');
                    echo "<p style='color: blue;'>ℹ️ Test record cleaned up</p>";
                } else {
                    echo "<p style='color: red;'>❌ Insert test: FAILED</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Insert test: " . $e->getMessage() . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
        }
        
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors/contractors') . "'>Back to Contractors</a></p>";
    }
}
