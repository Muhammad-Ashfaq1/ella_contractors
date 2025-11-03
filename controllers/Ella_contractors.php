<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_media_model');
        $this->load->helper('ella_media');
    }
    
    /**
     * Main index method - redirects to admin dashboard
     */
    public function index() {
        redirect(admin_url());
    }
}