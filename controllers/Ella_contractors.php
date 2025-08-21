<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
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
        $this->db->where('tblproposals.status', 3); // Status 3 = Accepted
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
     * Settings page
     */
    public function settings() {
        $data['title'] = 'Contractor Settings';
        $data['message'] = 'Hello from Settings page';
        $this->load->view('simple_page', $data);
    }
}
