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
            FCPATH . 'modules/ella_contractors/helpers/ella_contractors_helper.php'
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
        
        if (!function_exists('get_file_icon')) {
            function get_file_icon($file_type) {
                $icon_map = [
                    'pdf' => 'fa-file-pdf', 'doc' => 'fa-file-word', 'docx' => 'fa-file-word',
                    'xls' => 'fa-file-excel', 'xlsx' => 'fa-file-excel', 'ppt' => 'fa-file-powerpoint',
                    'pptx' => 'fa-file-powerpoint', 'jpg' => 'fa-file-image', 'jpeg' => 'fa-file-image',
                    'png' => 'fa-file-image', 'gif' => 'fa-file-image', 'mp4' => 'fa-file-video'
                ];
                $file_extension = strtolower(pathinfo($file_type, PATHINFO_EXTENSION));
                return isset($icon_map[$file_extension]) ? $icon_map[$file_extension] : 'fa-file';
            }
        }
        
        if (!function_exists('formatBytes')) {
            function formatBytes($bytes, $decimals = 2) {
                if ($bytes === 0) return '0 Bytes';
                $k = 1024;
                $dm = $decimals < 0 ? 0 : $decimals;
                $sizes = ['Bytes', 'KB', 'MB', 'GB'];
                $i = floor(log($bytes) / log($k));
                return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
            }
        }
        
        if (!function_exists('ella_character_limiter')) {
            function ella_character_limiter($str, $length) {
                if (strlen($str) <= $length) return $str;
                return substr($str, 0, $length) . '...';
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
                log_message('debug', 'Default media access granted with hash: ' . $hash);
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
    
    /**
     * Client Portal - Public multi-tab interface for contract information
     * This replaces the media library with a comprehensive client portal
     */
    public function client_portal($contract_id = null, $hash = null)
    {
        // Debug: Log the request
        log_message('debug', 'Client Portal Access: contract_id=' . $contract_id . ', hash=' . $hash);
        
        // Validate access
        if (!$this->check_media_gallery_access($contract_id, $hash)) {
            log_message('error', 'Client Portal Access Denied: contract_id=' . $contract_id . ', hash=' . $hash);
            show_404();
            return;
        }
        
        // Get contract details
        $contract = $this->get_contract_details($contract_id);
        if (!$contract) {
            log_message('error', 'Client Portal Contract Not Found: contract_id=' . $contract_id);
            show_404();
            return;
        }
        
        // Get media files from backend
        $media_files = [];
        if (function_exists('get_contract_media')) {
            $media_files = get_contract_media($contract_id, true); // Include defaults
            log_message('debug', 'Client Portal Media Files: ' . count($media_files) . ' files found');
        } else {
            log_message('error', 'Client Portal Helper Functions Not Available');
        }
        
        // Prepare data for view
        $data = [
            'title' => 'Contract Client Portal - ' . $contract['title'],
            'contract' => $contract,
            'contract_id' => $contract_id,
            'hash' => $hash,
            'media_files' => $media_files,
            'is_public' => true
        ];
        
        // Load the client portal view
        $this->load->view('public/client_portal', $data);
    }
    
    /**
     * Get contract details for public access
     */
    private function get_contract_details($contract_id)
    {
        if (!$contract_id || $contract_id == 0) {
            // Return default contract info for demo
            return [
                'id' => 0,
                'title' => 'Default Project Gallery',
                'client_name' => 'Public Access',
                'client_email' => 'info@example.com',
                'client_phone' => '(555) 123-4567',
                'status' => 'Active',
                'start_date' => '2024-01-01',
                'estimated_completion' => '2024-12-31',
                'total_value' => '$50,000',
                'description' => 'Default project gallery with sample materials and documents.',
                'address' => 'Sample Address, City, State'
            ];
        }
        
        // Get real contract details from proposals table
        $this->db->select('tblproposals.*, tblleads.name as lead_name, tblleads.email as lead_email, 
                          tblleads.phonenumber as lead_phone, tblleads.company as lead_company');
        $this->db->from('tblproposals');
        $this->db->join('tblleads', 'tblproposals.rel_id = tblleads.id AND tblproposals.rel_type = "lead"', 'left');
        $this->db->where('tblproposals.id', $contract_id);
        $proposal = $this->db->get()->row();
        
        if (!$proposal) {
            return null;
        }
        
        return [
            'id' => $proposal->id,
            'title' => $proposal->subject ?: 'Contract #' . $proposal->id,
            'client_name' => $proposal->lead_name ?: 'Client',
            'client_email' => $proposal->lead_email ?: 'client@example.com',
            'client_phone' => $proposal->lead_phone ?: '(555) 000-0000',
            'client_company' => $proposal->lead_company ?: 'Client Company',
            'status' => $this->get_proposal_status($proposal->status),
            'start_date' => $proposal->date ?: date('Y-m-d'),
            'estimated_completion' => $proposal->date ?: date('Y-m-d', strtotime('+30 days')),
            'total_value' => $proposal->total ? '$' . number_format($proposal->total, 2) : '$0.00',
            'description' => $proposal->content ?: 'Contract description not available.',
            'address' => 'Address not specified'
        ];
    }
    
    /**
     * Get human-readable proposal status
     */
    private function get_proposal_status($status)
    {
        $status_map = [
            1 => 'Draft',
            2 => 'Sent',
            3 => 'Accepted',
            4 => 'Declined',
            5 => 'Expired'
        ];
        
        return isset($status_map[$status]) ? $status_map[$status] : 'Unknown';
    }
}
