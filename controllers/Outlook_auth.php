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

        try {
            $staff_id = get_staff_user_id();
            $auth_url = $this->outlook_calendar_sync->get_authorization_url($staff_id);

            if ($auth_url) {
                redirect($auth_url);
            } else {
                set_alert('danger', 'Failed to initialize Outlook Calendar connection. Please check your Azure App Registration settings.');
                redirect(admin_url('ella_contractors/appointments'));
            }
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar connect error: ' . $e->getMessage());
            set_alert('danger', 'Error initializing Outlook Calendar connection: ' . $e->getMessage());
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
            
            // Provide specific guidance for common errors
            $user_message = 'Authentication failed: ' . $error_desc;
            
            if (stripos($error_desc, 'unauthorized_client') !== false || stripos($error, 'unauthorized_client') !== false) {
                $user_message = 'Azure App Registration Error: The application is not enabled for personal Microsoft accounts. ';
                $user_message .= 'To fix this, go to Azure Portal → App Registrations → Your App → Authentication → ';
                $user_message .= 'Under "Supported account types", select "Accounts in any organizational directory and personal Microsoft accounts".';
            } elseif (stripos($error_desc, 'redirect_uri_mismatch') !== false) {
                $user_message = 'Redirect URI Mismatch: The redirect URI in Azure Portal must exactly match: ' . $this->outlook_calendar_sync->get_redirect_uri();
            } elseif (stripos($error_desc, 'Proof Key for Code Exchange') !== false || stripos($error_desc, 'PKCE') !== false) {
                $user_message = 'PKCE Error: Your redirect URI is configured as "Single-page application" in Azure Portal, but this requires PKCE. ';
                $user_message .= 'SOLUTION: Go to Azure Portal → App Registrations → Your App → Authentication → ';
                $user_message .= 'Delete the SPA redirect URI and add it as "Web" type instead. ';
                $user_message .= 'The redirect URI should be: ' . $this->outlook_calendar_sync->get_redirect_uri();
            }
            
            $this->_close_popup('error', $user_message);
            return;
        }

        if (!$code) {
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
                    $this->outlook_calendar_sync->sync_all_appointments($staff_id);
                    
                    $this->_close_popup('success', 'Outlook Calendar connected successfully!');
                } else {
                    log_message('error', 'Outlook Calendar: Failed to save tokens');
                    $this->_close_popup('error', 'Failed to save credentials. Please try again.');
                }
            } else {
                // Check if there's a stored error from the library
                $stored_error = $this->session->flashdata('outlook_error');
                $error_msg = 'Failed to obtain access tokens. ';
                
                if ($stored_error) {
                    // Check for specific error types
                    if (stripos($stored_error, 'unauthorized_client') !== false) {
                        $error_msg = 'Azure App Registration Error: The application is not enabled for personal Microsoft accounts. ';
                        $error_msg .= 'To fix this, go to Azure Portal → App Registrations → Your App → Authentication → ';
                        $error_msg .= 'Under "Supported account types", select "Accounts in any organizational directory and personal Microsoft accounts".';
                    } elseif (stripos($stored_error, 'redirect_uri_mismatch') !== false) {
                        $error_msg = 'Redirect URI Mismatch: The redirect URI in Azure Portal must exactly match: ' . $this->outlook_calendar_sync->get_redirect_uri();
                    } else {
                        $error_msg .= 'Error details: ' . $stored_error . '. ';
                        $error_msg .= 'Common issues: 1) Client ID and Secret are correct, 2) Redirect URI matches Azure Portal exactly (including http/https), 3) API permissions are granted and admin consent is given.';
                    }
                } else {
                    $error_msg .= 'Common issues: 1) Client ID and Secret are correct, 2) Redirect URI matches Azure Portal exactly (including http/https), 3) API permissions are granted and admin consent is given. ';
                    $error_msg .= 'Check application logs for detailed error message.';
                }
                
                $this->_close_popup('error', $error_msg);
            }
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar callback exception: ' . $e->getMessage());
            
            $error_msg = 'An error occurred during Outlook Calendar connection: ' . $e->getMessage();
            
            // Check if it's related to unauthorized_client
            if (stripos($e->getMessage(), 'unauthorized_client') !== false) {
                $error_msg = 'Azure App Registration Error: The application is not enabled for personal Microsoft accounts. ';
                $error_msg .= 'To fix this, go to Azure Portal → App Registrations → Your App → Authentication → ';
                $error_msg .= 'Under "Supported account types", select "Accounts in any organizational directory and personal Microsoft accounts".';
            }
            
            $this->_close_popup('error', $error_msg);
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


