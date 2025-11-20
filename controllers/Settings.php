<?php defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        if (!is_admin()) {
            access_denied('EllaContractors Settings');
        }
    }

    /**
     * Main settings page
     */
    public function index()
    {
        $data = [];
        $data['title'] = 'EllaContractors Settings';
        
        $this->load->view('ella_contractors/admin/settings', $data);
    }

    /**
     * Save settings (AJAX)
     */
    public function save()
    {
        // Check if user is logged in and is admin
        if (!is_staff_logged_in()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        if (!is_admin()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied - Admin only']);
            exit;
        }

        // Set JSON header
        header('Content-Type: application/json');
        
        // Disable CodeIgniter output profiler for AJAX
        $this->output->enable_profiler(false);

        try {
            // Get POST data (already sanitized by CodeIgniter)
            $client_id = trim($this->input->post('google_calendar_client_id'));
            $client_secret = trim($this->input->post('google_calendar_client_secret'));
            $redirect_uri = trim($this->input->post('google_calendar_redirect_uri'));

            // Debug log
            log_message('info', 'EllaContractors Settings save attempt by staff ID: ' . get_staff_user_id());
            log_message('debug', 'EllaContractors Settings save - Client ID: ' . ($client_id ? 'provided' : 'empty'));
            log_message('debug', 'EllaContractors Settings save - Client Secret: ' . ($client_secret ? 'provided' : 'empty'));

            // Validate required fields
            if (empty($client_id) || empty($client_secret)) {
                log_message('warning', 'EllaContractors Settings save - Missing required fields');
                echo json_encode([
                    'success' => false,
                    'message' => 'Client ID and Client Secret are required.'
                ]);
                exit;
            }

            // Update options
            update_option('google_calendar_client_id', $client_id);
            update_option('google_calendar_client_secret', $client_secret);
            
            // Set default redirect URI if not provided
            if (empty($redirect_uri)) {
                $redirect_uri = site_url('ella_contractors/google_callback');
            }
            update_option('google_calendar_redirect_uri', $redirect_uri);

            // Log activity
            log_activity('EllaContractors Google Calendar settings updated');
            log_message('info', 'EllaContractors Google Calendar settings saved successfully');

            // Return success with new CSRF token
            $response = [
                'success' => true,
                'message' => 'Settings saved successfully! ✓',
                'csrf_token' => $this->security->get_csrf_hash()
            ];
            
            echo json_encode($response);
            exit;
            
        } catch (Exception $e) {
            log_message('error', 'EllaContractors Settings save error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
            exit;
        }
    }
}

