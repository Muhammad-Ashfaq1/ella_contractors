<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        
        // Load CodeIgniter system helpers
        $this->load->helper(['text', 'date']);
        
        // Load form validation library
        $this->load->library('form_validation');
        
        // Load models
        $this->load->model('ella_contracts_model');
        $this->load->model('ella_contractors_model');
        $this->load->model('ella_contract_notes_model');
        
        // Load helper functions from module directory
        $helper_path = __DIR__ . '/../helpers/ella_contractors_helper.php';
        if (file_exists($helper_path)) {
            require_once($helper_path);
        }
        
        // Ensure database table exists
        $this->ensure_contract_media_table();
        
        // Ensure appointments table exists
        $this->ensure_appointments_table();
    }
    
    /**
     * Main dashboard
     */
    public function dashboard() {
            $data['title'] = 'Ella Contractors Dashboard';
            $this->load->view('dashboard', $data);
    }
    
    /**
     * Main index method - redirects to dashboard
     */
    public function index() {
        redirect('admin/ella_contractors/dashboard');
    }
    
    /**
     * Contractors listing page
     */
    public function contractors($page = 1) {
        $data['title'] = 'Contractors Management';
        
        // Load the contractors model
        $this->load->model('ella_contractors_model');
        
        // Get filters from request
        $filters = [
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
            'specialization' => $this->input->get('specialization')
        ];
        
        // Pagination settings
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        // Get contractors with filters and pagination
        $data['contractors'] = $this->ella_contractors_model->get_contractors($filters, $per_page, $offset);
        $data['total_contractors'] = $this->ella_contractors_model->get_contractors_count($filters);
        $data['stats'] = $this->ella_contractors_model->get_contractors_stats();
        
        // Pagination configuration
        $this->load->library('pagination');
        $config['base_url'] = admin_url('ella_contractors/contractors');
        $config['total_rows'] = $data['total_contractors'];
        $config['per_page'] = $per_page;
        $config['uri_segment'] = 4;
        $config['page_query_string'] = false;
        
        // Pagination styling
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        
        // Pass filters for view
        $data['filters'] = $filters;
        
        $this->load->view('contractors_list', $data);
    }
    
    /**
     * Add new contractor
     */
    public function add_contractor() {
        $data['title'] = 'Add New Contractor';
        
        if ($this->input->post()) {
            $this->load->library('form_validation');
            // Set validation rules
            $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
            $this->form_validation->set_rules('contact_person', 'Contact Person', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('phone', 'Phone', 'trim');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim');
            $this->form_validation->set_rules('city', 'City', 'trim');
            $this->form_validation->set_rules('state', 'State', 'trim');
            $this->form_validation->set_rules('zip_code', 'ZIP Code', 'trim');
            $this->form_validation->set_rules('country', 'Country', 'trim');
            $this->form_validation->set_rules('tax_id', 'Tax ID', 'trim');
            $this->form_validation->set_rules('business_license', 'Business License', 'trim');
            $this->form_validation->set_rules('insurance_info', 'Insurance Info', 'trim');
            $this->form_validation->set_rules('specialization', 'Specialization', 'trim');
            $this->form_validation->set_rules('hourly_rate', 'Hourly Rate', 'trim|numeric');
            $this->form_validation->set_rules('payment_terms', 'Payment Terms', 'trim');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,pending,blacklisted]');
            $this->form_validation->set_rules('rating', 'Rating', 'trim|numeric|greater_than[0]|less_than[6]');
            $this->form_validation->set_rules('notes', 'Notes', 'trim');
            
            if ($this->form_validation->run() == false) {
                $data['errors'] = validation_errors();
            } else {
                $this->load->model('ella_contractors_model');
                
                // Check if email already exists
                if ($this->ella_contractors_model->email_exists($this->input->post('email'))) {
                    $data['errors'] = 'Email address already exists.';
                } else {
                    // Prepare data for insertion
                    $contractor_data = [
                        'company_name' => $this->input->post('company_name'),
                        'contact_person' => $this->input->post('contact_person'),
                        'email' => $this->input->post('email'),
                        'phone' => $this->input->post('phone'),
                        'mobile' => $this->input->post('mobile'),
                        'address' => $this->input->post('address'),
                        'city' => $this->input->post('city'),
                        'state' => $this->input->post('state'),
                        'zip_code' => $this->input->post('zip_code'),
                        'country' => $this->input->post('country'),
                        'tax_id' => $this->input->post('tax_id'),
                        'business_license' => $this->input->post('business_license'),
                        'insurance_info' => $this->input->post('insurance_info'),
                        'specialization' => $this->input->post('specialization'),
                        'hourly_rate' => $this->input->post('hourly_rate') ?: null,
                        'payment_terms' => $this->input->post('payment_terms'),
                        'status' => $this->input->post('status'),
                        'rating' => $this->input->post('rating') ?: null,
                        'notes' => $this->input->post('notes')
                    ];
                    
                    // Create contractor
                    $contractor_id = $this->ella_contractors_model->create_contractor($contractor_data);
                    
                    if ($contractor_id) {
                        set_alert('success', 'Contractor added successfully.');
                        redirect(admin_url('ella_contractors/contractors'));
                    } else {
                        $data['errors'] = 'Failed to add contractor. Please try again.';
                    }
                }
            }
        }
        
        $this->load->view('contractor_form', $data);
    }
    
    /**
     * Edit contractor
     */
    public function edit_contractor($id) {
        $data['title'] = 'Edit Contractor';
        
        $this->load->model('ella_contractors_model');
        $data['contractor'] = $this->ella_contractors_model->get_contractor($id);
        
        if (!$data['contractor']) {
            show_404();
            return;
        }
        
        if ($this->input->post()) {
            $this->load->library('form_validation');
            
            // Set validation rules
            $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
            $this->form_validation->set_rules('contact_person', 'Contact Person', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('phone', 'Phone', 'trim');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim');
            $this->form_validation->set_rules('city', 'City', 'trim');
            $this->form_validation->set_rules('state', 'State', 'trim');
            $this->form_validation->set_rules('zip_code', 'ZIP Code', 'trim');
            $this->form_validation->set_rules('country', 'Country', 'trim');
            $this->form_validation->set_rules('tax_id', 'Tax ID', 'trim');
            $this->form_validation->set_rules('business_license', 'Business License', 'trim');
            $this->form_validation->set_rules('insurance_info', 'Insurance Info', 'trim');
            $this->form_validation->set_rules('specialization', 'Specialization', 'trim');
            $this->form_validation->set_rules('hourly_rate', 'Hourly Rate', 'trim|numeric');
            $this->form_validation->set_rules('payment_terms', 'Payment Terms', 'trim');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,pending,blacklisted]');
            $this->form_validation->set_rules('rating', 'Rating', 'trim|numeric|greater_than[0]|less_than[6]');
            $this->form_validation->set_rules('notes', 'Notes', 'trim');
            
            if ($this->form_validation->run() == false) {
                $data['errors'] = validation_errors();
            } else {
                // Check if email already exists (excluding current contractor)
                if ($this->ella_contractors_model->email_exists($this->input->post('email'), $id)) {
                    $data['errors'] = 'Email address already exists.';
                } else {
                    // Prepare data for update
                    $contractor_data = [
                        'company_name' => $this->input->post('company_name'),
                        'contact_person' => $this->input->post('contact_person'),
                        'email' => $this->input->post('email'),
                        'phone' => $this->input->post('phone'),
                        'mobile' => $this->input->post('mobile'),
                        'address' => $this->input->post('address'),
                        'city' => $this->input->post('city'),
                        'state' => $this->input->post('state'),
                        'zip_code' => $this->input->post('zip_code'),
                        'country' => $this->input->post('country'),
                        'tax_id' => $this->input->post('tax_id'),
                        'business_license' => $this->input->post('business_license'),
                        'insurance_info' => $this->input->post('insurance_info'),
                        'specialization' => $this->input->post('specialization'),
                        'hourly_rate' => $this->input->post('hourly_rate') ?: null,
                        'payment_terms' => $this->input->post('payment_terms'),
                        'status' => $this->input->post('status'),
                        'rating' => $this->input->post('rating') ?: null,
                        'notes' => $this->input->post('notes')
                    ];
                    
                    // Update contractor
                    if ($this->ella_contractors_model->update_contractor($id, $contractor_data)) {
                        set_alert('success', 'Contractor updated successfully.');
                        redirect(admin_url('ella_contractors/contractors'));
                    } else {
                        $data['errors'] = 'Failed to update contractor. Please try again.';
                    }
                }
            }
        }
        
        $this->load->view('contractor_form', $data);
    }
    
    /**
     * View contractor details
     */
    public function view_contractor($id) {
        $data['title'] = 'Contractor Details';
        
        $this->load->model('ella_contractors_model');
        $data['contractor'] = $this->ella_contractors_model->get_contractor($id);
        
        if (!$data['contractor']) {
            show_404();
            return;
        }
        
        $this->load->view('contractor_view', $data);
    }
    
    /**
     * Delete contractor
     */
    public function delete_contractor($id) {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }
        
        $this->load->model('ella_contractors_model');
        
        if ($this->ella_contractors_model->delete_contractor($id)) {
            set_alert('success', 'Contractor deleted successfully.');
        } else {
            set_alert('danger', 'Failed to delete contractor.');
        }
        
        redirect(admin_url('ella_contractors/contractors'));
    }
    
    /**
     * Contracts listing page
     */
    public function contracts($page = 1)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contracts_model');
        
        // Get filters
        $filters = [
            'status' => $this->input->get('status'),
            'search' => $this->input->get('search'),
            'lead_id' => $this->input->get('lead_id'),
            'contractor_id' => $this->input->get('contractor_id')
        ];
        
        // Get contracts with pagination
        $result = $this->ella_contracts_model->get_contracts($filters, $page);
        
        // Get statistics
        $stats = $this->ella_contracts_model->get_contracts_stats();
        $counts = $this->ella_contracts_model->get_contracts_count();
        
        // Get leads and contractors for filters
        $leads = $this->ella_contracts_model->get_leads_for_contracts();
        $contractors = $this->ella_contracts_model->get_contractors_for_contracts();
        
        $data = [
            'title' => 'Contracts Management',
            'contracts' => $result['contracts'],
            'pagination' => [
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages'],
                'total_records' => $result['total_records']
            ],
            'stats' => $stats,
            'counts' => $counts,
            'filters' => $filters,
            'leads' => $leads,
            'contractors' => $contractors
        ];
        
        $this->load->view('contracts_list', $data);
    }
    
    /**
     * Projects listing page
     */
    public function projects($page = 1) {
        $data['title'] = 'Projects Management';
        $data['message'] = 'Hello from Projects page';
        $this->load->view('simple_page', $data);
    }
    
    /**
     * Payments listing page
     */
    public function payments($page = 1) {
        $data['title'] = 'Payments Management';
        $data['message'] = 'Hello from Payments page';
        $this->load->view('simple_page', $data);
    }
    

    
    /**
     * Upload media for contract
     */
    public function upload_media($contract_id = null) {
        if ($this->input->post()) {
            $description = $this->input->post('description');
            $media_category = $this->input->post('media_category');
            $tags = $this->input->post('tags');
            $is_default = $this->input->post('is_default') ? true : false;
            
            // Validate required fields
            if (empty($media_category)) {
                set_alert('danger', 'Please select a media category.');
                redirect(admin_url('ella_contractors/upload_media/' . $contract_id));
                return;
            }
            
                if (!empty($_FILES['media_file']['name'])) {
                $result = upload_contract_media($contract_id, 'media_file', $description, $is_default, $media_category, $tags);
                
                if ($result['success']) {
                    set_alert('success', 'Media file uploaded successfully!');
                } else {
                    set_alert('danger', 'Upload failed: ' . $result['error']);
                }
                } else {
                set_alert('danger', 'Please select a file to upload.');
            }
            
            if ($contract_id) {
                redirect(admin_url('ella_contractors/view_contract/' . $contract_id));
            } else {
                redirect(admin_url('ella_contractors/media_gallery'));
            }
        }
        
        $data['title'] = $contract_id ? 'Upload Media for Contract' : 'Upload Default Media';
        $data['contract_id'] = $contract_id;
        

        
        // Get contract details if contract_id provided
        if ($contract_id) {
            $this->db->select('subject');
            $this->db->from('tblella_contracts');
            $this->db->where('id', $contract_id);
            $contract = $this->db->get()->row();
            $data['contract_subject'] = $contract ? $contract->subject : 'Unknown Contract';
        }
        
        $this->load->view('upload_media', $data);
    }
    
    /**
     * Delete media file
     */
    public function delete_media($media_id) {
        if (delete_contract_media($media_id)) {
            set_alert('success', 'Media file deleted successfully!');
        } else {
            set_alert('danger', 'Failed to delete media file.');
        }
        
        // Redirect back to previous page
        $redirect_to = $this->input->get('redirect');
        if ($redirect_to) {
            redirect($redirect_to);
        } else {
            redirect(admin_url('ella_contractors/contracts'));
        }
    }
    
    /**
     * Media gallery - shows all default media files
     */
    public function media_gallery($contract_id = null) {
                // Check if helper functions are loaded
        if (!function_exists('get_default_contract_media')) {
            $data['error'] = 'Helper functions not loaded';
            $data['media_files'] = [];
        } else {
            if ($contract_id) {
                $data['title'] = 'Contract Media Gallery';
                $data['media_files'] = get_contract_media($contract_id, false);
                $data['contract_id'] = $contract_id;
        } else {
                $data['title'] = 'Default Media Gallery';
                $data['media_files'] = get_default_contract_media();
                $data['contract_id'] = null;
            }
        }
        
        $this->load->view('media_gallery', $data);
    }
    

    
    /**
     * Bulk actions for contractors
     */
    public function bulk_actions() {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }
        
        $action = $this->input->post('bulk_action');
        $ids = $this->input->post('contractor_ids');
        
        if (!$action || !$ids) {
            set_alert('warning', 'Please select action and contractors.');
            redirect(admin_url('ella_contractors/contractors'));
        }
        
        $this->load->model('ella_contractors_model');
        
        switch ($action) {
            case 'activate':
                $this->ella_contractors_model->bulk_update_status($ids, 'active');
                set_alert('success', 'Selected contractors activated successfully.');
                break;
                
            case 'deactivate':
                $this->ella_contractors_model->bulk_update_status($ids, 'inactive');
                set_alert('success', 'Selected contractors deactivated successfully.');
                break;
                
            case 'suspend':
                $this->ella_contractors_model->bulk_update_status($ids, 'suspended');
                set_alert('success', 'Selected contractors suspended successfully.');
                break;
                
            case 'delete':
                if (has_permission('ella_contractors', '', 'delete')) {
                    foreach ($ids as $id) {
                        $this->ella_contractors_model->delete_contractor($id);
                    }
                    set_alert('success', 'Selected contractors deleted successfully.');
        } else {
                    set_alert('danger', 'You do not have permission to delete contractors.');
                }
                break;
                
            default:
                set_alert('warning', 'Invalid action selected.');
                break;
        }
        
        redirect(admin_url('ella_contractors/contractors'));
    }
    
    /**
     * Activate module and create database tables
     */
    public function activate() {
        if (!is_super_admin()) {
            access_denied('ella_contractors');
        }
        
        $this->load->model('ella_contractors_model');
        
        // Trigger module activation
        ella_contractors_activate_module();
        
        set_alert('success', 'Module activated successfully. Database tables have been created.');
        redirect(admin_url('ella_contractors/contractors'));
    }
    
    /**
     * Ensure the contract media table exists
     */
    private function ensure_contract_media_table() {
        $table_name = 'ella_contractor_media';
        
        // Check if table exists (with and without tbl prefix)
        if (!$this->db->table_exists($table_name) && !$this->db->table_exists('tblella_contractor_media')) {
            $this->load->dbforge();
            
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'contract_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'file_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'original_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'file_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => FALSE
                ],
                'file_size' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ],
                'file_path' => [
                    'type' => 'VARCHAR',
                    'constraint' => 500,
                    'null' => FALSE
                ],
                'is_default' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'media_category' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => TRUE
                ],
                'tags' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'uploaded_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ],
                'date_uploaded' => [
                    'type' => 'DATETIME',
                    'null' => FALSE
                ]
            ];
            
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('contract_id');
            $this->dbforge->add_key('is_default');
            $this->dbforge->create_table($table_name);
        } else {
            // Table exists, check if we need to add new columns
            $this->ensure_media_table_columns();
        }
    }
    
    /**
     * Ensure all required columns exist in the media table
     */
    private function ensure_media_table_columns() {
        $table_name = 'ella_contractor_media';
        
        if ($this->db->table_exists($table_name)) {
            $this->load->dbforge();
            
            // Check and add media_category column if it doesn't exist
            if (!$this->db->field_exists('media_category', $table_name)) {
                $fields = [
                    'media_category' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => TRUE,
                        'after' => 'description'
                    ]
                ];
                $this->dbforge->add_column($table_name, $fields);
            }
            
            // Check and add tags column if it doesn't exist
            if (!$this->db->field_exists('tags', $table_name)) {
                $fields = [
                    'tags' => [
                        'type' => 'TEXT',
                        'null' => TRUE,
                        'after' => 'media_category'
                    ]
                ];
                $this->dbforge->add_column($table_name, $fields);
            }
        }
    }
    
    /**
     * Ensure appointments table exists
     */
    private function ensure_appointments_table() {
        $table_name = 'ella_appointments';
        
        if (!$this->db->table_exists($table_name)) {
            $this->load->dbforge();
            
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ],
                'contract_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ],
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => FALSE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'appointment_date' => [
                    'type' => 'DATE',
                    'null' => FALSE
                ],
                'start_time' => [
                    'type' => 'TIME',
                    'null' => FALSE
                ],
                'end_time' => [
                    'type' => 'TIME',
                    'null' => FALSE
                ],
                'appointment_type' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => FALSE
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['scheduled', 'confirmed', 'completed', 'cancelled', 'rescheduled'],
                    'default' => 'scheduled'
                ],
                'location' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => TRUE
                ],
                'attendees' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ],
                'reminder_sent' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ],
                'created_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => FALSE
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE
                ],
                'updated_by' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => TRUE
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ];
            
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('contract_id');
            $this->dbforge->add_key('appointment_date');
            $this->dbforge->add_key('status');
            $this->dbforge->create_table($table_name);
            
            log_message('info', 'Ella Contractors: Created appointments table ' . $table_name);
        } else {
            log_message('info', 'Ella Contractors: Appointments table ' . $table_name . ' already exists');
        }
    }
    
    /**
     * Clean up duplicate tables
     */
    private function cleanup_duplicate_tables() {
        // Check for old table names and clean them up
        $old_tables = ['tblcontract_media', 'tbltblcontract_media', 'contract_media', 'tblella_contractor_media', 'tbltblella_contractor_media'];
        
        foreach ($old_tables as $old_table) {
            if ($this->db->table_exists($old_table)) {
                // Drop the old table
                $this->load->dbforge();
                $this->dbforge->drop_table($old_table);
            }
        }
    }
    
    /**
     * Default Media Gallery - shows media files available for all contracts
     */
    public function default_media() {
        $data['title'] = 'Default Media Gallery';
        $data['media_files'] = get_default_contract_media();
        $data['contract_id'] = null;
        $data['is_default_gallery'] = true;
        
        $this->load->view('media_gallery', $data);
    }
    
    /**
     * Update media table structure (for existing installations)
     */
    public function update_media_table() {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }
        
        // Ensure the table structure is up to date
        $this->ensure_contract_media_table();
        
        set_alert('success', 'Media table structure has been updated successfully!');
        redirect(admin_url('ella_contractors/media_gallery'));
    }
    
    /**
     * Settings page
     */
    public function settings() {
        $data['title'] = 'Contractor Settings';
        $data['message'] = 'Hello from Settings page';
        $this->load->view('simple_page', $data);
    }

    /**
     * Add new contract
     */
    public function add_contract()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contracts_model');
        $this->load->library('form_validation');
        
        if ($this->input->post()) {
            // Form validation
            $this->form_validation->set_rules('lead_id', 'Lead', 'required|numeric');
            $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
            $this->form_validation->set_rules('subject', 'Subject', 'required|max_length[255]');
            $this->form_validation->set_rules('contract_value', 'Contract Value', 'numeric');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,active,completed,cancelled,expired]');
            
            if ($this->form_validation->run() == false) {
                $errors = validation_errors();
                set_alert('danger', $errors);
            } else {
                $data = [
                    'lead_id' => $this->input->post('lead_id'),
                    'contractor_id' => $this->input->post('contractor_id'),
                    'subject' => $this->input->post('subject'),
                    'description' => $this->input->post('description'),
                    'contract_value' => $this->input->post('contract_value'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'status' => $this->input->post('status'),
                    'payment_terms' => $this->input->post('payment_terms'),
                    'notes' => $this->input->post('notes')
                ];
                
                // Check if lead already has active contract
                if ($this->ella_contracts_model->lead_has_active_contract($data['lead_id']) && in_array($data['status'], ['draft', 'active'])) {
                    set_alert('warning', 'This lead already has an active contract. Please review existing contracts first.');
                } else {
                    $contract_id = $this->ella_contracts_model->create_contract($data);
                    
                    if ($contract_id) {
                        log_activity('New Contract Created [ContractID: ' . $contract_id . ']');
                        set_alert('success', 'Contract created successfully.');
                        redirect(admin_url('ella_contractors/contracts'));
                    } else {
                        set_alert('danger', 'Failed to create contract.');
                    }
                }
            }
        }
        
        // Get leads and contractors for dropdowns
        $leads = $this->ella_contracts_model->get_leads_for_contracts();
        $contractors = $this->ella_contracts_model->get_contractors_for_contracts();
        
        $data = [
            'title' => 'Add New Contract',
            'leads' => $leads,
            'contractors' => $contractors
        ];
        
        $this->load->view('contract_form', $data);
    }

    /**
     * Edit existing contract
     */
    public function edit_contract($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contracts_model');
        $this->load->library('form_validation');
        
        $contract = $this->ella_contracts_model->get_contract($id);
        if (!$contract) {
            set_alert('danger', 'Contract not found.');
            redirect(admin_url('ella_contractors/contracts'));
        }
        
        if ($this->input->post()) {
            // Form validation
            $this->form_validation->set_rules('lead_id', 'Lead', 'required|numeric');
            $this->form_validation->set_rules('contractor_id', 'Contractor', 'required|numeric');
            $this->form_validation->set_rules('subject', 'Subject', 'required|max_length[255]');
            $this->form_validation->set_rules('contract_value', 'Contract Value', 'numeric');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[draft,active,completed,cancelled,expired]');
            
            if ($this->form_validation->run() == false) {
                $errors = validation_errors();
                set_alert('danger', $errors);
            } else {
                $data = [
                    'lead_id' => $this->input->post('lead_id'),
                    'contractor_id' => $this->input->post('contractor_id'),
                    'subject' => $this->input->post('subject'),
                    'description' => $this->input->post('description'),
                    'contract_value' => $this->input->post('contract_value'),
                    'start_date' => $this->input->post('start_date'),
                    'end_date' => $this->input->post('end_date'),
                    'status' => $this->input->post('status'),
                    'payment_terms' => $this->input->post('payment_terms'),
                    'notes' => $this->input->post('notes')
                ];
                
                // Check if lead already has active contract (excluding current contract)
                if ($data['lead_id'] != $contract->lead_id && 
                    $this->ella_contracts_model->lead_has_active_contract($data['lead_id']) && 
                    in_array($data['status'], ['draft', 'active'])) {
                    set_alert('warning', 'This lead already has an active contract. Please review existing contracts first.');
                } else {
                    $updated = $this->ella_contracts_model->update_contract($id, $data);
                    
                    if ($updated) {
                        log_activity('Contract Updated [ContractID: ' . $id . ']');
                        set_alert('success', 'Contract updated successfully.');
                        redirect(admin_url('ella_contractors/contracts'));
                    } else {
                        set_alert('danger', 'Failed to update contract.');
                    }
                }
            }
        }
        
        // Get leads and contractors for dropdowns
        $leads = $this->ella_contracts_model->get_leads_for_contracts();
        $contractors = $this->ella_contracts_model->get_contractors_for_contracts();
        
        $data = [
            'title' => 'Edit Contract',
            'contract' => $contract,
            'leads' => $leads,
            'contractors' => $contractors
        ];
        
        $this->load->view('contract_form', $data);
    }

    /**
     * View contract details
     */
    public function view_contract($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contracts_model');
        
        $contract = $this->ella_contracts_model->get_contract($id);
        if (!$contract) {
            set_alert('danger', 'Contract not found.');
            redirect(admin_url('ella_contractors/contracts'));
        }
        
        // Load contract media files
        $contract_media = [];
        $default_media = [];
        
        if (function_exists('get_contract_media')) {
            $contract_media = get_contract_media($id, false); // Only contract-specific media
            $default_media = get_default_contract_media(); // Default media for all contracts
        }
        
        $data = [
            'title' => 'Contract Details',
            'contract' => $contract,
            'contract_media' => $contract_media,
            'default_media' => $default_media
        ];
        
        $this->load->view('view_contract', $data);
    }

    /**
     * Delete contract
     */
    public function delete_contract($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contracts_model');
        
        $contract = $this->ella_contracts_model->get_contract($id);
        if (!$contract) {
            set_alert('danger', 'Contract not found.');
            redirect(admin_url('ella_contractors/contracts'));
        }
        
        // Check if contract can be deleted (only draft contracts)
        if ($contract->status !== 'draft') {
            set_alert('warning', 'Only draft contracts can be deleted.');
            redirect(admin_url('ella_contractors/contracts'));
        }
        
        $deleted = $this->ella_contracts_model->delete_contract($id);
        
        if ($deleted) {
            log_activity('Contract Deleted [ContractID: ' . $id . ']');
            set_alert('success', 'Contract deleted successfully.');
        } else {
            set_alert('danger', 'Failed to delete contract.');
        }
        
        redirect(admin_url('ella_contractors/contracts'));
    }

    /**
     * Bulk actions for contracts
     */
    public function bulk_contract_actions()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->model('ella_contracts_model');
        
        $action = $this->input->post('bulk_action');
        $contract_ids = $this->input->post('contract_ids');
        
        if (empty($contract_ids) || empty($action)) {
            set_alert('warning', 'Please select contracts and an action.');
            redirect(admin_url('ella_contractors/contracts'));
        }
        
        $success_count = 0;
        
        switch ($action) {
            case 'activate':
                $success_count = $this->ella_contracts_model->bulk_update_status($contract_ids, 'active');
                $message = 'Selected contracts activated successfully.';
                break;
                
            case 'complete':
                $success_count = $this->ella_contracts_model->bulk_update_status($contract_ids, 'completed');
                $message = 'Selected contracts marked as completed.';
                break;
                
            case 'cancel':
                $success_count = $this->ella_contracts_model->bulk_update_status($contract_ids, 'cancelled');
                $message = 'Selected contracts cancelled successfully.';
                break;
                
            case 'delete':
                if (has_permission('ella_contractors', '', 'delete')) {
                    foreach ($contract_ids as $contract_id) {
                        $contract = $this->ella_contracts_model->get_contract($contract_id);
                        if ($contract && $contract->status === 'draft') {
                            if ($this->ella_contracts_model->delete_contract($contract_id)) {
                                $success_count++;
                            }
                        }
                    }
                    $message = 'Selected draft contracts deleted successfully.';
                } else {
                    set_alert('danger', 'You do not have permission to delete contracts.');
                    redirect(admin_url('ella_contractors/contracts'));
                }
                break;
                
            default:
                set_alert('warning', 'Invalid action selected.');
                redirect(admin_url('ella_contractors/contracts'));
        }
        
        if ($success_count > 0) {
            set_alert('success', $message . ' ' . $success_count . ' contracts affected.');
        } else {
            set_alert('warning', 'No contracts were affected by this action.');
        }
        
        redirect(admin_url('ella_contractors/contracts'));
    }

    /**
     * Public contract view (for shareable links)
     */
    public function public_view($id)
    {
        // Load the contracts model
        $this->load->model('ella_contractors_model');
        
        // Get contract data
        $contract = $this->ella_contractors_model->get_contract($id);
        if (!$contract) {
            show_404();
        }
        
        // Check if token is valid (basic validation)
        $token = $this->input->get('token');
        if (!$token) {
            show_404();
        }
        
        // For now, we'll do basic token validation
        // In production, implement proper token validation
        $expected_token = base64_encode('contract_' . $id . '_' . strtotime('today'));
        if (strpos($token, 'contract_' . $id) === false) {
            show_404();
        }
        
        // Load public view
        $data = [
            'contract' => $contract,
            'is_public' => true
        ];
        
        $this->load->view('contract_public_view', $data);
    }

    /**
     * Appointments management
     */
    public function appointments($contract_id = null)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        if ($contract_id) {
            // Single contract appointments
            $contract = $this->ella_contracts_model->get_contract($contract_id);
            if (!$contract) {
                show_404();
            }
            
            $data = [
                'title' => 'Appointments - ' . $contract->subject,
                'contract' => $contract,
                'contract_id' => $contract_id,
                'appointments' => $this->get_contract_appointments($contract_id)
            ];
            
            $this->load->view('appointments_contract', $data);
        } else {
            // All appointments across contracts
            try {
                $contracts_result = $this->ella_contracts_model->get_contracts(['status' => 'active']);
                $contracts = $contracts_result['contracts'];
            } catch (Exception $e) {
                log_message('error', 'Failed to load contracts for appointments: ' . $e->getMessage());
                $contracts = [];
            }
            
            $data = [
                'title' => 'All Appointments',
                'appointments' => $this->get_all_appointments(),
                'contracts' => $contracts
            ];
            
            $this->load->view('appointments_list', $data);
        }
    }

    /**
     * Add new appointment
     */
    public function add_appointment($contract_id = null)
    {
        if (!has_permission('ella_contractors', '', 'add')) {
            access_denied('ella_contractors');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('contract_id', 'Contract', 'required|numeric');
            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('appointment_date', 'Date', 'required');
            $this->form_validation->set_rules('start_time', 'Start Time', 'required');
            $this->form_validation->set_rules('end_time', 'End Time', 'required');
            $this->form_validation->set_rules('appointment_type', 'Type', 'required');
            
            if ($this->form_validation->run()) {
                $appointment_data = [
                    'contract_id' => $this->input->post('contract_id'),
                    'title' => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'appointment_date' => $this->input->post('appointment_date'),
                    'start_time' => $this->input->post('start_time'),
                    'end_time' => $this->input->post('end_time'),
                    'appointment_type' => $this->input->post('appointment_type'),
                    'status' => $this->input->post('status'),
                    'location' => $this->input->post('location'),
                    'attendees' => $this->input->post('attendees'),
                    'notes' => $this->input->post('notes'),
                    'created_by' => get_staff_user_id(),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                if ($this->add_appointment_data($appointment_data)) {
                    set_alert('success', 'Appointment added successfully.');
                    redirect(admin_url('ella_contractors/appointments/' . $appointment_data['contract_id']));
                } else {
                    set_alert('danger', 'Failed to add appointment.');
                }
            }
        }
        
        try {
            $contracts_result = $this->ella_contracts_model->get_contracts(['status' => 'active']);
            $contracts = $contracts_result['contracts'];
        } catch (Exception $e) {
            log_message('error', 'Failed to load contracts for appointments: ' . $e->getMessage());
            $contracts = [];
        }
        
        $data = [
            'title' => 'Add New Appointment',
            'contract_id' => $contract_id,
            'contracts' => $contracts
        ];
        
        $this->load->view('appointment_form', $data);
    }

    /**
     * Edit appointment
     */
    public function edit_appointment($appointment_id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->get_appointment($appointment_id);
        if (!$appointment) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('appointment_date', 'Date', 'required');
            $this->form_validation->set_rules('start_time', 'Start Time', 'required');
            $this->form_validation->set_rules('end_time', 'End Time', 'required');
            $this->form_validation->set_rules('appointment_type', 'Type', 'required');
            
            if ($this->form_validation->run()) {
                $appointment_data = [
                    'title' => $this->input->post('title'),
                    'description' => $this->input->post('description'),
                    'appointment_date' => $this->input->post('appointment_date'),
                    'start_time' => $this->input->post('start_time'),
                    'end_time' => $this->input->post('end_time'),
                    'appointment_type' => $this->input->post('appointment_type'),
                    'status' => $this->input->post('status'),
                    'location' => $this->input->post('location'),
                    'attendees' => $this->input->post('attendees'),
                    'notes' => $this->input->post('notes'),
                    'updated_by' => get_staff_user_id(),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($this->update_appointment_data($appointment_id, $appointment_data)) {
                    set_alert('success', 'Appointment updated successfully.');
                    redirect(admin_url('ella_contractors/appointments/' . $appointment->contract_id));
                } else {
                    set_alert('danger', 'Failed to update appointment.');
                }
            }
        }
        
        try {
            $contracts_result = $this->ella_contracts_model->get_contracts(['status' => 'active']);
            $contracts = $contracts_result['contracts'];
        } catch (Exception $e) {
            log_message('error', 'Failed to load contracts for appointments: ' . $e->getMessage());
            $contracts = [];
        }
        
        $data = [
            'title' => 'Edit Appointment',
            'appointment' => $appointment,
            'contracts' => $contracts
        ];
        
        $this->load->view('appointment_form', $data);
    }

    /**
     * Delete appointment
     */
    public function delete_appointment($appointment_id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->get_appointment($appointment_id);
        if (!$appointment) {
            show_404();
        }

        if ($this->delete_appointment_data($appointment_id)) {
            set_alert('success', 'Appointment deleted successfully.');
        } else {
            set_alert('danger', 'Failed to delete appointment.');
        }
        
        redirect(admin_url('ella_contractors/appointments/' . $appointment->contract_id));
    }

    /**
     * Update appointment status
     */
    public function update_appointment_status()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $appointment_id = $this->input->post('appointment_id');
        $status = $this->input->post('status');
        
        if ($this->update_appointment_status_data($appointment_id, $status)) {
            set_alert('success', 'Appointment status updated successfully.');
        } else {
            set_alert('danger', 'Failed to update appointment status.');
        }
        
        redirect(admin_url('ella_contractors/appointments'));
    }

    /**
     * Get contract appointments
     */
    private function get_contract_appointments($contract_id)
    {
        $this->db->select('a.*, c.subject as contract_title, c.contract_number');
        $this->db->from('ella_appointments a');
        $this->db->join('tblella_contracts c', 'c.id = a.contract_id', 'left');
        $this->db->where('a.contract_id', $contract_id);
        $this->db->order_by('a.appointment_date', 'ASC');
        $this->db->order_by('a.start_time', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get all appointments
     */
    private function get_all_appointments()
    {
        $this->db->select('a.*, c.subject as contract_title, c.contract_number');
        $this->db->from('ella_appointments a');
        $this->db->join('tblella_contracts c', 'c.id = a.contract_id', 'left');
        $this->db->order_by('a.appointment_date', 'ASC');
        $this->db->order_by('a.start_time', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get single appointment
     */
    private function get_appointment($appointment_id)
    {
        $this->db->select('a.*, c.subject as contract_title, c.contract_number');
        $this->db->from('ella_appointments a');
        $this->db->join('tblella_contracts c', 'c.id = a.contract_id', 'left');
        $this->db->where('a.id', $appointment_id);
        
        return $this->db->get()->row();
    }

    /**
     * Add appointment data
     */
    private function add_appointment_data($data)
    {
        return $this->db->insert('ella_appointments', $data);
    }

    /**
     * Update appointment data
     */
    private function update_appointment_data($appointment_id, $data)
    {
        $this->db->where('id', $appointment_id);
        return $this->db->update('ella_appointments', $data);
    }

    /**
     * Delete appointment data
     */
    private function delete_appointment_data($appointment_id)
    {
        $this->db->where('id', $appointment_id);
        return $this->db->delete('ella_appointments');
    }

    /**
     * Update appointment status
     */
    private function update_appointment_status_data($appointment_id, $status)
    {
        $this->db->where('id', $appointment_id);
        return $this->db->update('ella_appointments', [
            'status' => $status,
            'updated_by' => get_staff_user_id(),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get contract appointments for AJAX request
     */
    public function get_contract_appointments_ajax($contract_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        try {
            $appointments = $this->get_contract_appointments($contract_id);
            
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => true,
                'appointments' => $appointments
            ]);
        } catch (Exception $e) {
            log_message('error', 'Failed to get appointments for contract ' . $contract_id . ': ' . $e->getMessage());
            
            $this->output->set_status_header(403);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load appointments'
            ]);
        }
    }

    /**
     * Add appointment via AJAX
     */
    public function add_appointment_ajax()
    {
        if (!has_permission('ella_contractors', '', 'add')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        $this->form_validation->set_rules('contract_id', 'Contract', 'required|numeric');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('appointment_date', 'Date', 'required');
        $this->form_validation->set_rules('start_time', 'Start Time', 'required');
        $this->form_validation->set_rules('end_time', 'End Time', 'required');
        $this->form_validation->set_rules('appointment_type', 'Type', 'required');
        
        if ($this->form_validation->run()) {
            $appointment_data = [
                'contract_id' => $this->input->post('contract_id'),
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'appointment_date' => $this->input->post('appointment_date'),
                'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
                'appointment_type' => $this->input->post('appointment_type'),
                'status' => $this->input->post('status'),
                'location' => $this->input->post('location'),
                'attendees' => '',
                'notes' => '',
                'created_by' => get_staff_user_id(),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if ($this->add_appointment_data($appointment_data)) {
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Appointment added successfully'
                ]);
            } else {
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to add appointment'
                ]);
            }
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
        }
    }

    /**
     * Contract Notes Management
     */
    public function contract_notes($contract_id = null)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        if (!$contract_id) {
            show_404();
            return;
        }

        try {
            $data['title'] = 'Contract Notes';
            $data['contract_id'] = $contract_id;
            
            // Get contract details
            $data['contract'] = $this->ella_contracts_model->get_contract($contract_id);
            if (!$data['contract']) {
                show_404();
                return;
            }
            
            // Check if notes table exists
            if (!$this->db->table_exists('tblella_contract_notes')) {
                // Table doesn't exist, show message to run migration
                $data['notes'] = [];
                $data['notes_summary'] = [
                    'total' => 0,
                    'public' => 0,
                    'private' => 0,
                    'by_type' => []
                ];
                $data['table_missing'] = true;
            } else {
                // Get notes for this contract
                $data['notes'] = $this->ella_contract_notes_model->get_contract_notes($contract_id);
                $data['notes_summary'] = $this->ella_contract_notes_model->get_notes_summary($contract_id);
                $data['table_missing'] = false;
            }
            
            // Load the view with proper admin layout
            $this->load->view('contract_notes', $data);
            
        } catch (Exception $e) {
            log_message('error', 'Error in contract_notes method: ' . $e->getMessage());
            show_error('An error occurred while loading contract notes. Please check the error logs.');
        }
    }

    /**
     * Add new note via AJAX
     */
    public function add_note_ajax()
    {
        if (!has_permission('ella_contractors', '', 'add')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }

        $this->form_validation->set_rules('contract_id', 'Contract', 'required|numeric');
        $this->form_validation->set_rules('note_title', 'Note Title', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('note_content', 'Note Content', 'required|trim');
        $this->form_validation->set_rules('note_type', 'Note Type', 'required|in_list[general,progress,issue,milestone,other]');
        
        if ($this->form_validation->run()) {
            $note_data = [
                'contract_id' => $this->input->post('contract_id'),
                'note_title' => $this->input->post('note_title'),
                'note_content' => $this->input->post('note_content'),
                'note_type' => $this->input->post('note_type'),
                'is_public' => $this->input->post('is_public') ? 1 : 0,
                'created_by' => get_staff_user_id()
            ];
            
            $note_id = $this->ella_contract_notes_model->create_note($note_data);
            
            if ($note_id) {
                // Get the created note with creator info
                $note = $this->ella_contract_notes_model->get_note($note_id);
                
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Note added successfully',
                    'note' => $note,
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to add note',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            }
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Update note via AJAX
     */
    public function update_note_ajax()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }

        $this->form_validation->set_rules('note_id', 'Note ID', 'required|numeric');
        $this->form_validation->set_rules('note_title', 'Note Title', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('note_content', 'Note Content', 'required|trim');
        $this->form_validation->set_rules('note_type', 'Note Type', 'required|in_list[general,progress,issue,milestone,other]');
        
        if ($this->form_validation->run()) {
            $note_data = [
                'note_title' => $this->input->post('note_title'),
                'note_content' => $this->input->post('note_content'),
                'note_type' => $this->input->post('note_type'),
                'is_public' => $this->input->post('is_public') ? 1 : 0,
                'updated_by' => get_staff_user_id()
            ];
            
            $note_id = $this->input->post('note_id');
            
            if ($this->ella_contract_notes_model->update_note($note_id, $note_data)) {
                // Get the updated note
                $note = $this->ella_contract_notes_model->get_note($note_id);
                
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Note updated successfully',
                    'note' => $note,
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update note',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            }
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'message' => validation_errors(),
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Delete note via AJAX
     */
    public function delete_note_ajax()
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }

        $note_id = $this->input->post('note_id');
        
        if (!$note_id) {
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Note ID is required',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        if ($this->ella_contract_notes_model->delete_note($note_id)) {
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Note deleted successfully',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } else {
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete note',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * Get notes for contract via AJAX
     */
    public function get_contract_notes_ajax($contract_id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Access denied', 'csrf_token' => $this->security->get_csrf_hash()]);
            return;
        }

        try {
            $notes = $this->ella_contract_notes_model->get_contract_notes($contract_id);
            $summary = $this->ella_contract_notes_model->get_notes_summary($contract_id);
            
            $this->output->set_content_type('application/json');
            echo json_encode([
                'success' => true,
                'notes' => $notes,
                'summary' => $summary,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        } catch (Exception $e) {
            log_message('error', 'Failed to get notes for contract ' . $contract_id . ': ' . $e->getMessage());
            
            $this->output->set_status_header(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load notes',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
        }
    }

}
