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
     * Contracts listing page
     */
    public function contracts($page = 1) {
        $data['title'] = 'Contracts Management';
        $data['message'] = 'Hello from Contracts page';
        $this->load->view('simple_page', $data);
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
