<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Public_media_gallery extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models and helpers
        $this->load->model('proposals_model');
        $this->load->helper(['ella_contractors', 'general', 'text', 'date']);
        $this->load->library(['session', 'form_validation']);
        
        // Set language
        $this->lang->load('english', 'english');
    }
    
    /**
     * Public access to contract-specific media gallery
     * @param int $contract_id Contract ID
     * @param string $hash Access hash
     */
    public function index($contract_id, $hash)
    {
        // Check access restrictions
        if (!check_media_gallery_access($contract_id, $hash)) {
            show_404();
        }
        
        // Get contract/proposal details
        $proposal = $this->proposals_model->get($contract_id);
        if (!$proposal) {
            show_404();
        }
        
        // Get media files
        $media_files = get_public_media_gallery($contract_id, $hash);
        
        // Prepare data for view
        $data = [
            'title' => 'Media Gallery - ' . $proposal->subject,
            'proposal' => $proposal,
            'media_files' => $media_files,
            'contract_id' => $contract_id,
            'hash' => $hash,
            'is_public' => true
        ];
        
        // Load public view
        $this->load->view('public/media_gallery', $data);
    }
    
    /**
     * Public access to default media gallery
     * @param string $hash Access hash
     */
    public function default_gallery($hash)
    {
        // Check access restrictions
        if (!check_media_gallery_access(0, $hash)) {
            show_404();
        }
        
        // Get default media files
        $media_files = get_public_media_gallery(0, $hash);
        
        // Prepare data for view
        $data = [
            'title' => 'Default Media Gallery',
            'media_files' => $media_files,
            'contract_id' => 0,
            'hash' => $hash,
            'is_public' => true,
            'is_default_gallery' => true
        ];
        
        // Load public view
        $this->load->view('public/default_media_gallery', $data);
    }
    
    /**
     * Download media file (public access)
     * @param int $contract_id Contract ID
     * @param string $hash Access hash
     * @param string $file_name File name to download
     */
    public function download($contract_id, $hash, $file_name)
    {
        // Check access restrictions
        if (!check_media_gallery_access($contract_id, $hash)) {
            show_404();
        }
        
        // Get media file details
        $CI = &get_instance();
        $CI->db->where('file_name', $file_name);
        if ($contract_id > 0) {
            $CI->db->where('contract_id', $contract_id);
        } else {
            $CI->db->where('is_default', 1);
        }
        
        $media = $CI->db->get(db_prefix() . 'ella_contractor_media')->row();
        
        if (!$media) {
            show_404();
        }
        
        // Get file path
        $file_path = get_contract_media_upload_path($contract_id) . $file_name;
        
        if (!file_exists($file_path)) {
            show_404();
        }
        
        // Force download
        $this->load->helper('download');
        force_download($media->original_name, file_get_contents($file_path));
    }
    
    /**
     * View media file (public access)
     * @param int $contract_id Contract ID
     * @param string $hash Access hash
     * @param string $file_name File name to view
     */
    public function view($contract_id, $hash, $file_name)
    {
        // Check access restrictions
        if (!check_media_gallery_access($contract_id, $hash)) {
            show_404();
        }
        
        // Get media file details
        $CI = &get_instance();
        $CI->db->where('file_name', $file_name);
        if ($contract_id > 0) {
            $CI->db->where('contract_id', $contract_id);
        } else {
            $CI->db->where('is_default', 1);
        }
        
        $media = $CI->db->get(db_prefix() . 'ella_contractor_media')->row();
        
        if (!$media) {
            show_404();
        }
        
        // Get file path
        $file_path = get_contract_media_upload_path($contract_id) . $file_name;
        
        if (!file_exists($file_path)) {
            show_404();
        }
        
        // Check if it's an image
        $image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        
        if (in_array($media->file_type, $image_types)) {
            // Display image
            $this->output->set_content_type($media->file_type);
            $this->output->set_output(file_get_contents($file_path));
        } else {
            // For non-images, try to display in browser if possible
            $this->output->set_content_type($media->file_type);
            $this->output->set_output(file_get_contents($file_path));
        }
    }
}
