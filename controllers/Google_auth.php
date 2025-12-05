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
                
                // Provide helpful error message based on the exception
                if (strpos($e->getMessage(), 'composer install') !== false) {
                    $user_msg = 'Google Calendar API client library not installed. Please run: <code>cd modules/ella_contractors && composer install</code>';
                } elseif (strpos($e->getMessage(), 'Google_Client') !== false) {
                    $user_msg = 'Google Calendar API client library not properly loaded. Please run: <code>cd modules/ella_contractors && composer dump-autoload</code>';
                } else {
                    $user_msg = 'Google Calendar library error: ' . htmlspecialchars($e->getMessage());
                }
                
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

        // Handle OAuth errors
        if ($error) {
            $error_message = $this->input->get('error_description') ?: 'Authentication was cancelled or failed.';
            $this->_close_popup_with_message('error', 'Google Calendar connection failed: ' . $error_message);
            return;
        }

        // Handle missing authorization code
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
                    $this->_close_popup_with_message('error', 'Failed to save Google Calendar credentials. Please try again.');
                }
            } else {
                $this->_close_popup_with_message('error', 'Failed to obtain access tokens from Google. Please try again.');
            }
        } catch (Exception $e) {
            log_message('error', 'Google Calendar OAuth callback error: ' . $e->getMessage());
            $this->_close_popup_with_message('error', 'An error occurred during Google Calendar connection: ' . $e->getMessage());
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
                    echo json_encode([
                        'connected' => false,
                        'error' => 'Failed to load Google Calendar library: ' . $lib_e->getMessage(),
                        'message' => 'Library error'
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
     * Close popup window and send message to parent
     * 
     * @param string $type - 'success' or 'error'
     * @param string $message - Message to display
     */
    private function _close_popup_with_message($type, $message)
    {
        // Output HTML that sends postMessage to opener and closes the popup
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Google Calendar Connection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: ' . ($type === 'success' ? '#4caf50' : '#f44336') . ';
            color: white;
        }
        .message-container {
            text-align: center;
            padding: 20px;
        }
        .message-container h2 {
            margin-bottom: 10px;
        }
        .message-container p {
            font-size: 16px;
        }
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid white;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>' . ($type === 'success' ? '✓ Success!' : '✗ Error') . '</h2>
        <p>' . htmlspecialchars($message) . '</p>
        <div class="spinner"></div>
        <p style="font-size: 14px; margin-top: 15px;">Closing window...</p>
    </div>
    <script>
        // Send message to parent window
        if (window.opener) {
            window.opener.postMessage({
                type: "' . ($type === 'success' ? 'google_calendar_auth_success' : 'google_calendar_auth_error') . '",
                message: "' . addslashes($message) . '"
            }, window.location.origin);
        }
        
        // Close popup after a short delay
        setTimeout(function() {
            window.close();
        }, ' . ($type === 'success' ? '1500' : '3000') . ');
    </script>
</body>
</html>';

        echo $html;
        exit;
    }
}
