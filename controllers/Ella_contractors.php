<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public $active_status = 3;
    public function __construct() {
        parent::__construct();
        
        // Load CodeIgniter system helpers
        $this->load->helper(['text', 'date']);
        
        // Load helper functions from module directory
        $helper_path = __DIR__ . '/../helpers/ella_contractors_helper.php';
        if (file_exists($helper_path)) {
            require_once($helper_path);
        }
        
        // Ensure database table exists
        $this->ensure_contract_media_table();
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
     * New method
     */
    public function new() {
        die('test');
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
     * Contracts listing page - shows accepted proposals
     */
    public function contracts($page = 1) {
        $data['title'] = 'Contracts Management';
        
        // Load the proposals model to get accepted proposals
        $this->load->model('proposals_model');
        
        // Get accepted proposals (status = 3 means accepted)
        $this->db->select('tblproposals.*, tblleads.name as lead_name, tblleads.email as lead_email, 
                          tblleads.phonenumber as lead_phone, tblleads.company as lead_company,
                          tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tblproposals');
        $this->db->join('tblleads', 'tblproposals.rel_id = tblleads.id AND tblproposals.rel_type = "lead"', 'left');
        $this->db->join('tblstaff', 'tblleads.assigned = tblstaff.staffid', 'left');
        $this->db->where('tblproposals.status', $this->active_status); // Status 3 = Accepted
        $this->db->order_by('tblproposals.date', 'DESC');
        
        $data['accepted_proposals'] = $this->db->get()->result();
        
        $this->load->view('contracts_table', $data);
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
     * View contract details with media gallery
     */
    public function view_contract($contract_id) {
        // Get contract (proposal) details
        $this->db->select('tblproposals.*, tblleads.name as lead_name, tblleads.email as lead_email, 
                          tblleads.phonenumber as lead_phone, tblleads.company as lead_company,
                          tblstaff.firstname, tblstaff.lastname');
        $this->db->from('tblproposals');
        $this->db->join('tblleads', 'tblproposals.rel_id = tblleads.id AND tblproposals.rel_type = "lead"', 'left');
        $this->db->join('tblstaff', 'tblleads.assigned = tblstaff.staffid', 'left');
        $this->db->where('tblproposals.id', $contract_id);
        $this->db->where('tblproposals.status', 3); // Only accepted proposals
        
        $contract = $this->db->get()->row();
        
        if (!$contract) {
            show_404();
                return;
            }
            
                        // Get media files for this contract
        $contract_media = get_contract_media($contract_id, false);
        
        // Get default media files
        $default_media = get_default_contract_media();
        
        // Get base currency for formatting
        $base_currency = get_base_currency();
        
        $data['title'] = 'Contract Details - ' . $contract->subject;
        $data['contract'] = $contract;
        $data['contract_media'] = $contract_media;
        $data['default_media'] = $default_media;
        $data['base_currency'] = $base_currency;
        
        $this->load->view('view_contract', $data);
    }
    
    /**
     * Upload media for contract
     */
    public function upload_media($contract_id = null) {
        if ($this->input->post()) {
            $description = $this->input->post('description');
            $is_default = $this->input->post('is_default') ? true : false;
            
                        if (!empty($_FILES['media_file']['name'])) {
                $result = upload_contract_media($contract_id, 'media_file', $description, $is_default);
                
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
            $this->db->from('tblproposals');
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
     * Settings page
     */
    public function settings() {
        $data['title'] = 'Contractor Settings';
        $data['message'] = 'Hello from Settings page';
        $this->load->view('simple_page', $data);
    }
    
    /**
     * Test method to verify module is accessible
     */
    public function myFunction() {
        echo "<h2>Ella Contractors Module Test</h2>";
        echo "<p>✅ Module is accessible!</p>";
        echo "<p>✅ Controller is working!</p>";
        echo "<p>✅ Database connection: ";
        
        try {
            $this->db->simple_query('SELECT 1');
            echo "Working</p>";
        } catch (Exception $e) {
            echo "Failed: " . $e->getMessage() . "</p>";
        }
        
        echo "<p><strong>Test URLs:</strong></p>";
        echo "<ul>";
                        echo "<li><a href='" . site_url('modules/ella_contractors/client-portal/1/test123') . "'>Test Contract Client Portal</a></li>";
                        echo "<li><a href='" . site_url('modules/ella_contractors/client-portal/default/test123') . "'>Test Default Client Portal</a></li>";
        echo "</ul>";
        
        echo "<hr>";
        echo "<p><a href='" . admin_url('ella_contractors') . "'>← Back to Ella Contractors</a></p>";
    }


}
