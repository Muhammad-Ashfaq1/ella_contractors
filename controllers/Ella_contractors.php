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

    public function presentations() {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        $this->load->model('leads_model');
        $data['title'] = 'Presentations';
        $data['folders'] = $this->ella_media_model->get_folders();
        $data['media'] = $this->ella_media_model->get_media();
        $data['leads'] = $this->leads_model->get();
        $this->load->view('ella_contractors/presentations', $data);
    }

    public function create_folder() {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Folder Name', 'required');
        $this->form_validation->set_rules('lead_id', 'Lead', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
            } else {
                $data = [
                'name' => $this->input->post('name'),
                'lead_id' => $this->input->post('lead_id') ?: null,
                'is_default' => 0, // Folders are never default
                'active' => 1      // Folders are always active
            ];
            $folder_id = $this->ella_media_model->create_folder($data);
            if ($folder_id) {
                set_alert('success', 'Folder created successfully');
                } else {
                set_alert('warning', 'Failed to create folder');
            }
        }
        redirect(admin_url('ella_contractors/presentations'));
    }

    public function upload_presentation($folder_id) {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }
        $lead_id = $this->input->post('lead_id') ?: null;
        $is_default = $this->input->post('is_default') ? 1 : 0;
        $active = $this->input->post('active') ? 1 : 0;
        $description = $this->input->post('description');

        $uploaded = handle_ella_media_upload($folder_id, $lead_id, $is_default, $active);

        if ($uploaded) {
            // Update description if needed
            foreach ($uploaded as $id) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'ella_contractor_media', ['description' => $description]);
            }
            set_alert('success', 'File uploaded successfully');
            } else {
            set_alert('warning', 'Failed to upload file');
        }
        redirect(admin_url('ella_contractors/presentations'));
    }

    public function preview_file($id) {
        $file = $this->ella_media_model->get_file($id);
        if (!$file) {
            show_404();
        }
        $data['file'] = $file;
        $data['title'] = 'Preview ' . $file->original_name;
        $this->load->view('ella_contractors/preview', $data);
    }
}