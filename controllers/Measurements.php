<?php defined('BASEPATH') or exit('No direct script access allowed');

class Measurements extends AdminController
{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Measurements index method
     */
    public function index() {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $data['title'] = 'Measurements';
        $this->load->view('ella_contractors/measurements/index', $data);
    }
}
