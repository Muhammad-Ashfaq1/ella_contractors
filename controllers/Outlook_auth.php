<?php defined('BASEPATH') or exit('No direct script access allowed');

class Outlook_auth extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('ella_contractors/Outlook_calendar_sync');
    }

    public function connect()
    {
        if (!is_staff_logged_in()) {
            access_denied('ella_contractors');
        }

        $staff_id = get_staff_user_id();
        $auth_url = $this->outlook_calendar_sync->get_authorization_url($staff_id);

        if ($auth_url) {
            redirect($auth_url);
        } else {
            set_alert('danger', 'Failed to initialize Outlook Calendar connection.');
            redirect(admin_url('ella_contractors/appointments'));
        }
    }

    public function callback()
    {
        if (!is_staff_logged_in()) {
            access_denied('ella_contractors');
        }

        $code = $this->input->get('code');
        $error = $this->input->get('error');
        $staff_id = get_staff_user_id();

        if ($error) {
            $error_desc = $this->input->get('error_description') ?: $error;
            log_message('error', 'Outlook Calendar OAuth error: ' . $error . ' - ' . $error_desc);
            $this->_close_popup('error', 'Authentication failed: ' . $error_desc);
            return;
        }

        if (!$code) {
            log_message('error', 'Outlook Calendar: Missing authorization code in callback');
            $this->_close_popup('error', 'Missing authorization code.');
            return;
        }

        // Check if credentials are configured
        $client_id = get_option('outlook_calendar_client_id');
        $client_secret = get_option('outlook_calendar_client_secret');
        
        if (empty($client_id) || empty($client_secret)) {
            log_message('error', 'Outlook Calendar: Client ID or Secret not configured');
            $this->_close_popup('error', 'Outlook Calendar credentials not configured. Please configure them in Settings → Outlook Calendar.');
            return;
        }

        try {
            $tokens = $this->outlook_calendar_sync->exchange_code_for_tokens($code, $staff_id);

            if ($tokens && isset($tokens['access_token'])) {
                $saved = $this->outlook_calendar_sync->save_tokens($staff_id, $tokens);

                if ($saved) {
                    // Sync all appointments in background
                    $sync_result = $this->outlook_calendar_sync->sync_all_appointments($staff_id);
                    log_message('info', 'Outlook Calendar connected for staff ' . $staff_id . ' - Synced: ' . ($sync_result['synced'] ?? 0));
                    
                    $this->_close_popup('success', 'Outlook Calendar connected successfully!');
                } else {
                    log_message('error', 'Outlook Calendar: Failed to save tokens for staff ' . $staff_id);
                    $this->_close_popup('error', 'Failed to save credentials. Please try again.');
                }
            } else {
                // Get more details from logs - check the last error response
                log_message('error', 'Outlook Calendar: Token exchange returned empty or invalid data for staff ' . $staff_id);
                
                // Try to get more specific error from the library
                $error_msg = 'Failed to obtain access tokens. ';
                $error_msg .= 'Common issues: 1) Client ID and Secret are correct, 2) Redirect URI matches Azure Portal exactly (including http/https), 3) API permissions are granted and admin consent is given. ';
                $error_msg .= 'Check application logs for detailed error message.';
                
                $this->_close_popup('error', $error_msg);
            }
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar callback exception: ' . $e->getMessage());
            log_message('error', 'Outlook Calendar callback trace: ' . $e->getTraceAsString());
            $this->_close_popup('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        if (!is_staff_logged_in()) {
            ajax_access_denied();
        }

        $disconnected = $this->outlook_calendar_sync->disconnect(get_staff_user_id());

        echo json_encode([
            'success' => $disconnected,
            'message' => $disconnected ? 'Outlook Calendar disconnected successfully' : 'Failed to disconnect'
        ]);
    }

    public function status()
    {
        if (!is_staff_logged_in()) {
            ajax_access_denied();
        }

        header('Content-Type: application/json');

        $client_id = get_option('outlook_calendar_client_id');
        $client_secret = get_option('outlook_calendar_client_secret');

        if (empty($client_id) || empty($client_secret)) {
            echo json_encode(['connected' => false, 'message' => 'Not configured']);
            exit;
        }

        echo json_encode($this->outlook_calendar_sync->get_connection_status(get_staff_user_id()));
    }

    public function sync_now()
    {
        if (!is_staff_logged_in()) {
            ajax_access_denied();
        }

        $result = $this->outlook_calendar_sync->sync_all_appointments(get_staff_user_id());
        echo json_encode($result);
    }

    private function _close_popup($type, $message)
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


