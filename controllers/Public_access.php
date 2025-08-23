<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Public_access extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required libraries
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['general', 'text', 'date']);
        
        // Load module helper functions
        $this->load_module_helper();
    }
    
    /**
     * Load module helper with multiple path attempts
     */
    private function load_module_helper()
    {
        $possible_paths = [
            __DIR__ . '/../helpers/ella_contractors_helper.php',
            dirname(__DIR__) . '/helpers/ella_contractors_helper.php',
            FCPATH . 'modules/ella_contractors/helpers/ella_contractors_helper.php',
            APPPATH . 'modules/ella_contractors/helpers/ella_contractors_helper.php'
        ];
        
        $helper_loaded = false;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                require_once $path;
                $helper_loaded = true;
                break;
            }
        }
        
        if (!$helper_loaded) {
            // If helper not found, create fallback functions
            $this->create_fallback_functions();
        }
    }
    
    /**
     * Create fallback functions if helper is not available
     */
    private function create_fallback_functions()
    {
        if (!function_exists('get_contract_media')) {
            function get_contract_media($contract_id) {
                $CI = &get_instance();
                $CI->db->select('*');
                $CI->db->from('ella_contractor_media');
                $CI->db->where('contract_id', $contract_id);
                $CI->db->order_by('date_uploaded', 'DESC');
                return $CI->db->get()->result();
            }
        }
        
        if (!function_exists('get_default_contract_media')) {
            function get_default_contract_media() {
                $CI = &get_instance();
                $CI->db->select('*');
                $CI->db->from('ella_contractor_media');
                $CI->db->where('is_default', 1);
                $CI->db->order_by('date_uploaded', 'DESC');
                return $CI->db->get()->result();
            }
        }
        
        if (!function_exists('get_contract_media_upload_path')) {
            function get_contract_media_upload_path($contract_id) {
                return FCPATH . 'uploads/contracts/media/contract_' . $contract_id . '/';
            }
        }
        
        if (!function_exists('get_default_media_upload_path')) {
            function get_default_media_upload_path() {
                return FCPATH . 'uploads/contracts/default/';
            }
        }
    }
    
    /**
     * Check if media gallery hash is valid for access
     */
    private function check_media_gallery_access($contract_id, $hash)
    {
        if (!$hash) {
            return false;
        }
        
        // Check if this is a contract-specific gallery
        if ($contract_id > 0) {
            $this->db->where('id', $contract_id);
            $this->db->where('hash', $hash);
            $proposal = $this->db->get(db_prefix() . 'proposals')->row();
            
            if ($proposal && $proposal->hash == $hash) {
                return true;
            }
        }
        
        // Check if this is a default media gallery access
        if ($contract_id == 0) {
            // For default media, accept any hash but validate it's not empty
            if (strlen($hash) >= 8) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Main media gallery page - accessible via /ella-contractors/media/{id}/{hash}
     */
    public function media($contract_id, $hash)
    {
        if (!$this->check_media_gallery_access($contract_id, $hash)) {
            show_404();
            return;
        }
        
        // Get media files
        $data['contract_id'] = $contract_id;
        $data['hash'] = $hash;
        
        if ($contract_id > 0) {
            // Contract-specific media
            $data['contract_media'] = get_contract_media($contract_id);
            $data['title'] = 'Contract Media Gallery';
        } else {
            // Default media
            $data['contract_media'] = get_default_contract_media();
            $data['title'] = 'Default Media Gallery';
        }
        
        // Load the public view
        $this->load->view('public/media_gallery', $data);
    }
    
    /**
     * Default media gallery - accessible via /ella-contractors/default-media/{hash}
     */
    public function default_media($hash)
    {
        $this->media(0, $hash);
    }
    
    /**
     * Download media file
     */
    public function download($contract_id, $hash, $file_name)
    {
        if (!$this->check_media_gallery_access($contract_id, $hash)) {
            show_404();
            return;
        }
        
        // Get file path
        if ($contract_id > 0) {
            $file_path = get_contract_media_upload_path($contract_id) . '/' . $file_name;
        } else {
            $file_path = get_default_media_upload_path() . '/' . $file_name;
        }
        
        if (!file_exists($file_path)) {
            show_404();
            return;
        }
        
        // Force download
        $this->load->helper('download');
        force_download($file_name, file_get_contents($file_path));
    }
    
    /**
     * View media file
     */
    public function view($contract_id, $hash, $file_name)
    {
        if (!$this->check_media_gallery_access($contract_id, $hash)) {
            show_404();
            return;
        }
        
        // Get file path
        if ($contract_id > 0) {
            $file_path = get_contract_media_upload_path($contract_id) . '/' . $file_name;
        } else {
            $file_path = get_default_media_upload_path() . '/' . $file_name;
        }
        
        if (!file_exists($file_path)) {
            show_404();
            return;
        }
        
        // Get file info
        $file_info = pathinfo($file_name);
        $extension = strtolower($file_info['extension']);
        
        // Set content type
        $content_types = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        
        $content_type = isset($content_types[$extension]) ? $content_types[$extension] : 'application/octet-stream';
        
        // Display file
        header('Content-Type: ' . $content_type);
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }
}
