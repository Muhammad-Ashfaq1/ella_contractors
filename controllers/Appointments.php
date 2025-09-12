<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Appointments listing page
     */
    public function index()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Appointments';
        $this->load->view('appointments/index', $data);
    }
}
