<?php defined('BASEPATH') or exit('No direct script access allowed');

class Google_auth extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        // Try to load Google Calendar sync library
        try {
            $this->load->library('ella_contractors/Google_calendar_sync');
        } catch (Exception $e) {
            log_message('error', 'Google_auth: Failed to load Google_calendar_sync library - ' . $e->getMessage());
            log_message('error', 'Google_auth: Exception trace - ' . $e->getTraceAsString());
            // Don't set library property, so we can check and show proper error later
        }
    }

    /**
     * Index method - redirects to connect (default action)
     */
    public function index()
    {
        $this->connect();
    }

    /**
     * Initiate Google OAuth2 authentication
     * Redirects user to Google consent screen
     */
    public function connect()
    {
        if (!is_staff_logged_in()) {
            access_denied('ella_contractors');
        }

        // Check if library loaded successfully
        if (!isset($this->google_calendar_sync)) {
            // Try to load it one more time with better error reporting
            try {
                $this->load->library('ella_contractors/Google_calendar_sync');
            } catch (Exception $e) {
                $error_msg = 'Google Calendar library failed to load: ' . $e->getMessage();
                log_message('error', 'Google_auth connect: ' . $error_msg);
                
                // Extract module path for clearer instructions
                $module_path = module_dir_path('ella_contractors', '');
                $module_path = realpath($module_path) ?: $module_path;
                $module_path = rtrim($module_path, '/\\');
                
                // Provide helpful error message based on the exception
                $user_msg = '';
                if (strpos($e->getMessage(), 'composer install') !== false || strpos($e->getMessage(), 'not found') !== false) {
                    $user_msg = '<strong>Google Calendar API client library not installed.</strong><br><br>';
                    $user_msg .= 'Please run these commands on your server:<br>';
                    $user_msg .= '<pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 10px;">cd ' . htmlspecialchars($module_path) . '
composer install
composer dump-autoload</pre>';
                } elseif (strpos($e->getMessage(), 'Google_Client') !== false || strpos($e->getMessage(), 'not available') !== false) {
                    $user_msg = '<strong>Google Calendar API client library not properly loaded.</strong><br><br>';
                    $user_msg .= 'Please run these commands on your server:<br>';
                    $user_msg .= '<pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; margin-top: 10px;">cd ' . htmlspecialchars($module_path) . '
composer dump-autoload</pre>';
                } else {
                    $user_msg = '<strong>Google Calendar library error:</strong><br>' . htmlspecialchars($e->getMessage());
                }
                
                // Add troubleshooting info
                $user_msg .= '<br><br><small>If the issue persists, check server logs for detailed error messages.</small>';
                
                set_alert('danger', $user_msg);
                redirect(admin_url('ella_contractors/appointments'));
                return;
            }
        }
        
        // Final check after retry
        if (!isset($this->google_calendar_sync)) {
            set_alert('danger', 'Google Calendar library not available. Please check server logs for details.');
            redirect(admin_url('ella_contractors/appointments'));
            return;
        }

        $staff_id = get_staff_user_id();
        $redirect_uri = get_option('google_calendar_redirect_uri') ?: site_url('ella_contractors/google_callback');

        try {
            // Get authorization URL
            $auth_url = $this->google_calendar_sync->get_authorization_url($staff_id, $redirect_uri);

            if ($auth_url) {
                redirect($auth_url);
            } else {
                set_alert('danger', 'Failed to initialize Google Calendar connection. Please check your Google Calendar API credentials in settings.');
                redirect(admin_url('ella_contractors/appointments'));
            }
        } catch (Exception $e) {
            log_message('error', 'Google Calendar connect error: ' . $e->getMessage());
            set_alert('danger', 'Error: ' . $e->getMessage());
            redirect(admin_url('ella_contractors/appointments'));
        }
    }

    /**
     * OAuth callback handler
     * Receives authorization code from Google and exchanges for tokens
     */
    public function callback()
    {
        if (!is_staff_logged_in()) {
            access_denied('ella_contractors');
        }

        $code = $this->input->get('code');
        $error = $this->input->get('error');
        $staff_id = get_staff_user_id();

        // Handle OAuth errors - always redirect back
        if ($error) {
            $error_message = $this->input->get('error_description') ?: 'Authentication was cancelled or failed.';
            
            $user_message = 'Google Calendar connection failed: ' . $error_message;
            if (stripos($error_message, 'access_denied') !== false) {
                $user_message = 'Google Calendar connection was cancelled. Please try again when ready.';
            }
            
            $this->_close_popup_with_message('error', $user_message);
            return;
        }

        // Handle missing authorization code - always redirect back
        if (!$code) {
            $this->_close_popup_with_message('error', 'Missing authorization code. Please try connecting again.');
            return;
        }

        try {
            // Exchange code for tokens
            $tokens = $this->google_calendar_sync->exchange_code_for_tokens($code, $staff_id);

            if ($tokens && isset($tokens['access_token'])) {
                // Save tokens to database
                $saved = $this->google_calendar_sync->save_tokens($staff_id, $tokens);

                if ($saved) {
                    // Immediately sync all existing appointments for this staff member
                    $this->google_calendar_sync->sync_all_appointments($staff_id);

                    $this->_close_popup_with_message('success', 'Google Calendar connected successfully!');
                } else {
                    log_message('error', 'Google Calendar: Failed to save tokens');
                    $this->_close_popup_with_message('error', 'Failed to save Google Calendar credentials. Please try again.');
                }
            } else {
                $this->_close_popup_with_message('error', 'Failed to obtain access tokens from Google. Please check your Google Cloud Console settings and try again.');
            }
        } catch (Exception $e) {
            log_message('error', 'Google Calendar OAuth callback exception: ' . $e->getMessage());
            
            $error_msg = 'An error occurred during Google Calendar connection: ' . $e->getMessage();
            $this->_close_popup_with_message('error', $error_msg);
        }
    }

    /**
     * Disconnect Google Calendar (revoke tokens)
     */
    public function disconnect()
    {
        if (!is_staff_logged_in()) {
            ajax_access_denied();
        }

        $staff_id = get_staff_user_id();

        try {
            // Revoke tokens and delete from database
            $disconnected = $this->google_calendar_sync->disconnect($staff_id);

            if ($disconnected) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Google Calendar disconnected successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to disconnect Google Calendar'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Google Calendar disconnect error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while disconnecting: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check connection status (AJAX)
     */
    public function status()
    {
        if (!is_staff_logged_in()) {
            ajax_access_denied();
        }

        // Set JSON response header
        header('Content-Type: application/json');

        try {
            $staff_id = get_staff_user_id();
            
            // Check if credentials are configured (EllaContractors-specific only)
            $client_id = get_option('google_calendar_client_id');
            $client_secret = get_option('google_calendar_client_secret');
            
            if (empty($client_id) || empty($client_secret)) {
                echo json_encode([
                    'connected' => false,
                    'error' => 'Google Calendar API credentials not configured. Please configure them in EllaContractors → Settings.',
                    'message' => 'Not configured'
                ]);
                exit;
            }
            
            // Load library if not already loaded
            if (!isset($this->google_calendar_sync)) {
                try {
                    $this->load->library('ella_contractors/Google_calendar_sync');
                } catch (Exception $lib_e) {
                    log_message('error', 'Google Calendar: Failed to load library - ' . $lib_e->getMessage());
                    
                    // Provide helpful error message
                    $module_path = module_dir_path('ella_contractors', '');
                    $module_path = realpath($module_path) ?: $module_path;
                    $module_path = rtrim($module_path, '/\\');
                    
                    $error_detail = 'Failed to load Google Calendar library. ';
                    if (strpos($lib_e->getMessage(), 'composer') !== false || strpos($lib_e->getMessage(), 'not found') !== false) {
                        $error_detail .= 'Please run: cd ' . $module_path . ' && composer install && composer dump-autoload';
                    } else {
                        $error_detail .= $lib_e->getMessage();
                    }
                    
                    echo json_encode([
                        'connected' => false,
                        'error' => $error_detail,
                        'message' => 'Library error',
                        'troubleshooting' => 'Run: cd ' . $module_path . ' && composer dump-autoload'
                    ]);
                    exit;
                }
            }
            
            $status = $this->google_calendar_sync->get_connection_status($staff_id);

            if (!is_array($status)) {
                // If status is not an array, something went wrong
                echo json_encode([
                    'connected' => false,
                    'error' => 'Invalid response from Google Calendar sync library',
                    'message' => 'Invalid response'
                ]);
                exit;
            }

            echo json_encode($status);
            exit;
        } catch (Exception $e) {
            log_message('error', 'Google Calendar status check error: ' . $e->getMessage());
            log_message('error', 'Google Calendar status check trace: ' . $e->getTraceAsString());
            echo json_encode([
                'connected' => false,
                'error' => 'Failed to check Google Calendar status: ' . $e->getMessage(),
                'message' => 'Error checking status'
            ]);
            exit;
        }
    }


    /**
     * Manual sync all appointments (AJAX)
     */
    public function sync_now()
    {
        if (!is_staff_logged_in()) {
            ajax_access_denied();
        }

        $staff_id = get_staff_user_id();

        try {
            $result = $this->google_calendar_sync->sync_all_appointments($staff_id);

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Sync completed successfully. ' . $result['synced'] . ' appointment(s) synced.',
                    'synced' => $result['synced'],
                    'failed' => $result['failed']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Google Calendar sync_now error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred during sync: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect to appointments page with message
     * 
     * @param string $type - 'success' or 'error'
     * @param string $message - Message to display
     */
    private function _close_popup_with_message($type, $message)
    {
        // Store message in session flash data
        if ($type === 'success') {
            set_alert('success', $message);
        } else {
            set_alert('danger', $message);
        }
        
        // Redirect back to appointments page
        redirect(admin_url('ella_contractors/appointments'));
    }
}
