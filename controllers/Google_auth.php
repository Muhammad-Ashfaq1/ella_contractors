<?php defined('BASEPATH') or exit('No direct script access allowed');

class Google_auth extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('ella_contractors/Google_calendar_sync');
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

        $staff_id = get_staff_user_id();
        $redirect_uri = get_option('google_calendar_redirect_uri') ?: site_url('ella_contractors/google_callback');

        // Get authorization URL
        $auth_url = $this->google_calendar_sync->get_authorization_url($staff_id, $redirect_uri);

        if ($auth_url) {
            redirect($auth_url);
        } else {
            set_alert('danger', 'Failed to initialize Google Calendar connection. Please check your Google Calendar API credentials in settings.');
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
            set_alert('danger', 'Google Calendar connection failed: ' . $error_message);
            redirect(admin_url('ella_contractors/appointments'));
            return;
        }

        // Handle missing authorization code
        if (!$code) {
            set_alert('danger', 'Missing authorization code. Please try connecting again.');
            redirect(admin_url('ella_contractors/appointments'));
            return;
        }

        try {
            // Exchange code for tokens
            $tokens = $this->google_calendar_sync->exchange_code_for_tokens($code, $staff_id);

            if ($tokens && isset($tokens['access_token'])) {
                // Save tokens to database
                $saved = $this->google_calendar_sync->save_tokens($staff_id, $tokens);

                if ($saved) {
                    set_alert('success', 'Google Calendar connected successfully!');

                    // Immediately sync all existing appointments for this staff member
                    $this->google_calendar_sync->sync_all_appointments($staff_id);

                    redirect(admin_url('ella_contractors/appointments'));
                } else {
                    set_alert('danger', 'Failed to save Google Calendar credentials. Please try again.');
                    redirect(admin_url('ella_contractors/appointments'));
                }
            } else {
                set_alert('danger', 'Failed to obtain access tokens from Google. Please try again.');
                redirect(admin_url('ella_contractors/appointments'));
            }
        } catch (Exception $e) {
            log_message('error', 'Google Calendar OAuth callback error: ' . $e->getMessage());
            set_alert('danger', 'An error occurred during Google Calendar connection: ' . $e->getMessage());
            redirect(admin_url('ella_contractors/appointments'));
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

        $staff_id = get_staff_user_id();
        
        // Check if credentials are configured
        $client_id = get_option('google_calendar_client_id');
        $client_secret = get_option('google_calendar_client_secret');
        
        if (empty($client_id) || empty($client_secret)) {
            echo json_encode([
                'connected' => false,
                'error' => 'Google Calendar API credentials not configured. Please configure them in Settings.',
                'message' => 'Not configured'
            ]);
            return;
        }
        
        $status = $this->google_calendar_sync->get_connection_status($staff_id);

        echo json_encode($status);
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
}
