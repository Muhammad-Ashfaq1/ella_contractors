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
     * Contractors listing page
     */
    public function contractors($page = 1) {
        $data['title'] = 'Contractors Management';
        $data['message'] = 'Hello from Contractors page';
        $this->load->view('simple_page', $data);
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
     * Activate module manually
     */
    public function activate_module() {
        // Check if user has permission
        if (!is_admin()) {
            show_error('Access denied. Admin privileges required.');
            return;
        }

        // Clean up any duplicate tables first
        $this->cleanup_duplicate_tables();
        
        // Create the table
        $this->ensure_contract_media_table();
        
        // Verify table was created
        if ($this->db->table_exists('ella_contractor_media')) {
            set_alert('success', 'Module activated successfully! Database table created.');
        } else {
            set_alert('danger', 'Module activation failed! Table could not be created.');
        }
        
        // Redirect back to dashboard
        redirect(admin_url('ella_contractors'));
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
}
