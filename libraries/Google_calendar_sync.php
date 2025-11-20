<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Google Calendar Sync Library for EllaContractors
 * Handles OAuth2 authentication and calendar event synchronization
 */
class Google_calendar_sync
{
    private $client;
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $table_name;

    public function __construct()
    {
        // Use ONLY EllaContractors-specific credentials (no fallback to Appointly)
        $this->client_id = get_option('google_calendar_client_id');
        $this->client_secret = get_option('google_calendar_client_secret');
        $this->redirect_uri = get_option('google_calendar_redirect_uri') ?: site_url('ella_contractors/google_callback');
        $this->table_name = db_prefix() . 'staff_google_calendar_tokens';

        // Load Google API client if not already loaded
        if (!class_exists('Google_Client')) {
            try {
                // Try to load from appointly module vendor first (reuse library)
                $appointly_vendor = module_dir_path('appointly', 'vendor/autoload.php');
                if (file_exists($appointly_vendor)) {
                    require_once($appointly_vendor);
                } else {
                    // Try global vendor
                    $global_vendor = FCPATH . 'vendor/autoload.php';
                    if (file_exists($global_vendor)) {
                        require_once($global_vendor);
                    } else {
                        log_message('error', 'Google Calendar: Google API Client library not found. Please install via Composer: composer require google/apiclient:^2.0');
                        $this->client = null;
                        return;
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Google Calendar: Failed to load autoload - ' . $e->getMessage());
                $this->client = null;
                return;
            }
        }
    }

    /**
     * Initialize Google Client instance
     *
     * @param int $staff_id Staff ID (optional, for token refresh)
     * @return Google_Client|false
     */
    private function init_client($staff_id = null)
    {
        if (empty($this->client_id) || empty($this->client_secret)) {
            log_message('error', 'Google Calendar: Missing client_id or client_secret in settings');
            return false;
        }

        if (!class_exists('Google_Client')) {
            log_message('error', 'Google Calendar: Google_Client class not found. Please ensure Google API client is installed.');
            return false;
        }

        try {
            $this->client = new Google_Client();
            $this->client->setAccessType('offline');
            $this->client->setApprovalPrompt('force');
            $this->client->setApplicationName('EllaContractors Google Calendar Sync');
            $this->client->setClientId($this->client_id);
            $this->client->setClientSecret($this->client_secret);
            $this->client->setRedirectUri($this->redirect_uri);
            $this->client->addScope('https://www.googleapis.com/auth/calendar');
            $this->client->addScope('https://www.googleapis.com/auth/calendar.events');

            // Disable SSL verification (matching Appointly module pattern)
            $httpClient = new GuzzleHttp\Client([
                'verify' => false,
            ]);
            $this->client->setHttpClient($httpClient);

            // If staff_id provided, try to load and set access token
            if ($staff_id) {
                $token_data = $this->get_tokens($staff_id);
                if ($token_data && isset($token_data['access_token'])) {
                    $this->client->setAccessToken([
                        'access_token' => $token_data['access_token'],
                        'refresh_token' => $token_data['refresh_token'],
                        'expires_in' => $token_data['expires_in'] ?? 3600,
                        'created' => isset($token_data['created_at']) ? strtotime($token_data['created_at']) : time(),
                    ]);

                    // Refresh token if expired
                    if ($this->client->isAccessTokenExpired()) {
                        $this->refresh_token($staff_id);
                    }
                }
            }

            return $this->client;
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: Failed to initialize client - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get authorization URL for OAuth2 flow
     *
     * @param int $staff_id Staff ID
     * @param string|null $redirect_uri Custom redirect URI
     * @return string|false Authorization URL or false on failure
     */
    public function get_authorization_url($staff_id, $redirect_uri = null)
    {
        if (!$this->init_client()) {
            return false;
        }

        if ($redirect_uri) {
            $this->client->setRedirectUri($redirect_uri);
        }

        try {
            // Store staff_id in state for callback verification
            $state = base64_encode(json_encode(['staff_id' => $staff_id, 'nonce' => uniqid()]));
            $this->client->setState($state);

            return $this->client->createAuthUrl();
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: Failed to create auth URL - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exchange authorization code for access tokens
     *
     * @param string $code Authorization code from Google
     * @param int $staff_id Staff ID
     * @return array|false Token data or false on failure
     */
    public function exchange_code_for_tokens($code, $staff_id)
    {
        if (!$this->init_client()) {
            return false;
        }

        try {
            $access_token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($access_token['error'])) {
                log_message('error', 'Google Calendar: Token exchange error - ' . $access_token['error_description']);
                return false;
            }

            // Calculate expires_at
            $expires_in = isset($access_token['expires_in']) ? (int)$access_token['expires_in'] : 3600;
            $expires_at = date('Y-m-d H:i:s', time() + $expires_in);

            return [
                'access_token' => $access_token['access_token'],
                'refresh_token' => isset($access_token['refresh_token']) ? $access_token['refresh_token'] : null,
                'expires_in' => $expires_in,
                'expires_at' => $expires_at,
                'token_type' => isset($access_token['token_type']) ? $access_token['token_type'] : 'Bearer',
                'created' => isset($access_token['created']) ? $access_token['created'] : time(),
            ];
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: Token exchange exception - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save tokens to database
     *
     * @param int $staff_id Staff ID
     * @param array $tokens Token data
     * @return bool Success status
     */
    public function save_tokens($staff_id, $tokens)
    {
        $CI = &get_instance();

        $data = [
            'staff_id' => $staff_id,
            'access_token' => $tokens['access_token'],
            'refresh_token' => isset($tokens['refresh_token']) ? $tokens['refresh_token'] : null,
            'expires_at' => isset($tokens['expires_at']) ? $tokens['expires_at'] : null,
            'calendar_id' => 'primary', // Default to primary calendar
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Check if record exists
        $CI->db->where('staff_id', $staff_id);
        $existing = $CI->db->get($this->table_name)->row();

        if ($existing) {
            // Update existing record
            $CI->db->where('staff_id', $staff_id);
            $CI->db->update($this->table_name, $data);
        } else {
            // Insert new record
            $data['created_at'] = date('Y-m-d H:i:s');
            $CI->db->insert($this->table_name, $data);
        }

        return $CI->db->affected_rows() > 0;
    }

    /**
     * Get tokens for a staff member
     *
     * @param int $staff_id Staff ID
     * @return array|false Token data or false if not found
     */
    public function get_tokens($staff_id)
    {
        $CI = &get_instance();

        $CI->db->where('staff_id', $staff_id);
        $row = $CI->db->get($this->table_name)->row_array();

        if ($row) {
            // Calculate expires_in from expires_at
            if (!empty($row['expires_at'])) {
                $expires_timestamp = strtotime($row['expires_at']);
                $expires_in = max(0, $expires_timestamp - time());
                $row['expires_in'] = $expires_in;
            }

            return $row;
        }

        return false;
    }

    /**
     * Refresh access token using refresh token
     *
     * @param int $staff_id Staff ID
     * @return bool Success status
     */
    private function refresh_token($staff_id)
    {
        $tokens = $this->get_tokens($staff_id);

        if (!$tokens || empty($tokens['refresh_token'])) {
            log_message('error', 'Google Calendar: No refresh token available for staff ' . $staff_id);
            return false;
        }

        if (!$this->init_client()) {
            return false;
        }

        try {
            $this->client->setAccessToken([
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => $tokens['expires_in'] ?? 3600,
                'created' => isset($tokens['created_at']) ? strtotime($tokens['created_at']) : time(),
            ]);

            $new_token = $this->client->refreshToken($tokens['refresh_token']);

            if (isset($new_token['error'])) {
                log_message('error', 'Google Calendar: Token refresh error - ' . $new_token['error_description']);
                return false;
            }

            // Save refreshed tokens
            $expires_in = isset($new_token['expires_in']) ? (int)$new_token['expires_in'] : 3600;
            $expires_at = date('Y-m-d H:i:s', time() + $expires_in);

            $refresh_data = [
                'access_token' => $new_token['access_token'],
                'expires_at' => $expires_at,
                'expires_in' => $expires_in,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Preserve refresh token if not provided in new token
            if (!isset($new_token['refresh_token'])) {
                $refresh_data['refresh_token'] = $tokens['refresh_token'];
            } else {
                $refresh_data['refresh_token'] = $new_token['refresh_token'];
            }

            return $this->save_tokens($staff_id, $refresh_data);
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: Token refresh exception - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get connection status for a staff member
     *
     * @param int $staff_id Staff ID
     * @return array Connection status
     */
    public function get_connection_status($staff_id)
    {
        try {
            $tokens = $this->get_tokens($staff_id);

            if (!$tokens) {
                return [
                    'connected' => false,
                    'message' => 'Not connected'
                ];
            }

            // Check if token is expired
            $is_expired = false;
            if (!empty($tokens['expires_at'])) {
                $expires_timestamp = strtotime($tokens['expires_at']);
                $is_expired = time() >= $expires_timestamp;
            }

            // Try to refresh if expired
            if ($is_expired && !empty($tokens['refresh_token'])) {
                $refreshed = $this->refresh_token($staff_id);
                if ($refreshed) {
                    $tokens = $this->get_tokens($staff_id);
                }
            }

            return [
                'connected' => !empty($tokens['access_token']),
                'expired' => $is_expired,
                'calendar_id' => $tokens['calendar_id'] ?? 'primary',
                'message' => !empty($tokens['access_token']) ? 'Connected' : 'Not connected'
            ];
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: get_connection_status error - ' . $e->getMessage());
            return [
                'connected' => false,
                'error' => $e->getMessage(),
                'message' => 'Error checking status'
            ];
        }
    }

    /**
     * Disconnect Google Calendar (delete tokens)
     *
     * @param int $staff_id Staff ID
     * @return bool Success status
     */
    public function disconnect($staff_id)
    {
        $CI = &get_instance();

        // Optionally revoke token with Google (not required, but clean)
        $tokens = $this->get_tokens($staff_id);
        if ($tokens && !empty($tokens['access_token'])) {
            try {
                if ($this->init_client($staff_id)) {
                    $this->client->revokeToken($tokens['access_token']);
                }
            } catch (Exception $e) {
                // Continue with deletion even if revoke fails
                log_message('warning', 'Google Calendar: Failed to revoke token - ' . $e->getMessage());
            }
        }

        // Delete tokens from database
        $CI->db->where('staff_id', $staff_id);
        $CI->db->delete($this->table_name);

        // Also clear google_event_id from appointments for this staff
        $CI->db->where('created_by', $staff_id);
        $CI->db->where('source', 'ella_contractor');
        $CI->db->update(db_prefix() . 'appointly_appointments', [
            'google_event_id' => null,
            'google_calendar_id' => null
        ]);

        return true;
    }

    /**
     * Ensure valid access token (refresh if needed)
     *
     * @param int $staff_id Staff ID
     * @return bool Success status
     */
    private function ensure_valid_token($staff_id)
    {
        $tokens = $this->get_tokens($staff_id);

        if (!$tokens || empty($tokens['access_token'])) {
            return false;
        }

        // Check if token is expired or will expire soon (within 5 minutes)
        if (!empty($tokens['expires_at'])) {
            $expires_timestamp = strtotime($tokens['expires_at']);
            $time_until_expiry = $expires_timestamp - time();

            if ($time_until_expiry < 300) { // Less than 5 minutes
                // Try to refresh token
                if (!empty($tokens['refresh_token'])) {
                    return $this->refresh_token($staff_id);
                } else {
                    // No refresh token, connection invalid
                    log_message('warning', 'Google Calendar: Token expired and no refresh token for staff ' . $staff_id);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Create calendar event for appointment
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID (appointment creator/assignee)
     * @return array|false Event data or false on failure
     */
    public function create_event($appointment_id, $staff_id)
    {
        if (!$this->ensure_valid_token($staff_id)) {
            log_message('error', 'Google Calendar: No valid token for staff ' . $staff_id);
            return false;
        }

        // Load appointment data
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $appointment = $CI->appointments_model->get_appointment($appointment_id);

        if (!$appointment || $appointment->source !== 'ella_contractor') {
            log_message('error', 'Google Calendar: Invalid appointment or not EllaContractors appointment - ' . $appointment_id);
            return false;
        }

        // Initialize client with valid token
        if (!$this->init_client($staff_id)) {
            return false;
        }

        try {
            $service = new Google_Service_Calendar($this->client);
            $tokens = $this->get_tokens($staff_id);
            $calendar_id = $tokens['calendar_id'] ?? 'primary';

            // Build event data
            $event_data = $this->build_event_data($appointment);
            $event = new Google_Service_Calendar_Event($event_data);

            // Insert event
            $created_event = $service->events->insert($calendar_id, $event, [
                'conferenceDataVersion' => 1, // Enable Google Meet links
                'sendUpdates' => 'all', // Send notifications to attendees
            ]);

            // Save Google event ID to appointment
            $CI->db->where('id', $appointment_id);
            $CI->db->update(db_prefix() . 'appointly_appointments', [
                'google_event_id' => $created_event->getId(),
                'google_calendar_id' => $calendar_id
            ]);

            log_activity('Google Calendar event created [Appointment ID: ' . $appointment_id . ', Event ID: ' . $created_event->getId() . ']');

            return [
                'event_id' => $created_event->getId(),
                'html_link' => $created_event->getHtmlLink(),
                'hangout_link' => $created_event->getHangoutLink() ?? null,
                'i_cal_uid' => $created_event->getICalUID(),
            ];
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: Failed to create event - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update calendar event for appointment
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID
     * @return array|false Updated event data or false on failure
     */
    public function update_event($appointment_id, $staff_id)
    {
        if (!$this->ensure_valid_token($staff_id)) {
            return false;
        }

        // Load appointment data
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $appointment = $CI->appointments_model->get_appointment($appointment_id);

        if (!$appointment || $appointment->source !== 'ella_contractor') {
            return false;
        }

        // Check if event already exists
        if (empty($appointment->google_event_id)) {
            // Create new event instead
            return $this->create_event($appointment_id, $staff_id);
        }

        // Initialize client
        if (!$this->init_client($staff_id)) {
            return false;
        }

        try {
            $service = new Google_Service_Calendar($this->client);
            $tokens = $this->get_tokens($staff_id);
            $calendar_id = $tokens['calendar_id'] ?? 'primary';

            // Get existing event
            try {
                $existing_event = $service->events->get($calendar_id, $appointment->google_event_id);
            } catch (Exception $e) {
                // Event not found, create new one
                log_message('info', 'Google Calendar: Event not found, creating new - ' . $appointment->google_event_id);
                return $this->create_event($appointment_id, $staff_id);
            }

            // Build updated event data
            $event_data = $this->build_event_data($appointment);
            
            // Preserve event ID and iCalUID
            $existing_event->setSummary($event_data['summary']);
            $existing_event->setDescription($event_data['description']);
            $existing_event->setLocation($event_data['location']);
            $existing_event->setStart($event_data['start']);
            $existing_event->setEnd($event_data['end']);
            
            // Update attendees
            if (isset($event_data['attendees'])) {
                $attendees = [];
                foreach ($event_data['attendees'] as $attendee) {
                    $attendees[] = new Google_Service_Calendar_EventAttendee($attendee);
                }
                $existing_event->setAttendees($attendees);
            }

            // Update event
            $updated_event = $service->events->update($calendar_id, $existing_event->getId(), $existing_event, [
                'sendUpdates' => 'all',
            ]);

            log_activity('Google Calendar event updated [Appointment ID: ' . $appointment_id . ', Event ID: ' . $updated_event->getId() . ']');

            return [
                'event_id' => $updated_event->getId(),
                'html_link' => $updated_event->getHtmlLink(),
                'hangout_link' => $updated_event->getHangoutLink() ?? null,
                'i_cal_uid' => $updated_event->getICalUID(),
            ];
        } catch (Exception $e) {
            log_message('error', 'Google Calendar: Failed to update event - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete calendar event for appointment
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID
     * @return bool Success status
     */
    public function delete_event($appointment_id, $staff_id)
    {
        if (!$this->ensure_valid_token($staff_id)) {
            return false;
        }

        // Load appointment data
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $appointment = $CI->appointments_model->get_appointment($appointment_id);

        if (!$appointment || empty($appointment->google_event_id)) {
            return true; // No event to delete
        }

        // Initialize client
        if (!$this->init_client($staff_id)) {
            return false;
        }

        try {
            $service = new Google_Service_Calendar($this->client);
            $tokens = $this->get_tokens($staff_id);
            $calendar_id = $tokens['calendar_id'] ?? 'primary';

            // Delete event
            $service->events->delete($calendar_id, $appointment->google_event_id, [
                'sendUpdates' => 'all',
            ]);

            // Clear Google event ID from appointment
            $CI->db->where('id', $appointment_id);
            $CI->db->update(db_prefix() . 'appointly_appointments', [
                'google_event_id' => null,
                'google_calendar_id' => null
            ]);

            log_activity('Google Calendar event deleted [Appointment ID: ' . $appointment_id . ', Event ID: ' . $appointment->google_event_id . ']');

            return true;
        } catch (Exception $e) {
            // If event not found (404), consider it already deleted
            if (strpos($e->getMessage(), '404') !== false) {
                // Clear from database anyway
                $CI->db->where('id', $appointment_id);
                $CI->db->update(db_prefix() . 'appointly_appointments', [
                    'google_event_id' => null,
                    'google_calendar_id' => null
                ]);
                return true;
            }

            log_message('error', 'Google Calendar: Failed to delete event - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build event data array for Google Calendar API
     *
     * @param object $appointment Appointment object
     * @return array Event data
     */
    private function build_event_data($appointment)
    {
        $CI = &get_instance();
        $CI->load->model('leads_model');
        $CI->load->model('clients_model');

        // Build event title
        $client_name = '';
        if (!empty($appointment->lead_name)) {
            $client_name = $appointment->lead_name;
        } elseif (!empty($appointment->client_name)) {
            $client_name = $appointment->client_name;
        }

        $summary = $appointment->subject;
        if ($client_name) {
            $summary .= ' - ' . $client_name;
        }

        // Build description with CRM link
        $description = $appointment->subject . "\n\n";
        if ($client_name) {
            $description .= "Client: " . $client_name . "\n";
        }
        if (!empty($appointment->notes)) {
            $description .= "\nNotes: " . strip_tags($appointment->notes) . "\n";
        }
        $description .= "\nView in CRM: " . admin_url('ella_contractors/appointments/view/' . $appointment->id);

        // Build start/end datetime
        $start_datetime = $appointment->date . ' ' . ($appointment->start_hour ?? '00:00:00');
        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime(date('c', strtotime($start_datetime)));
        $start->setTimeZone(get_option('default_timezone') ?: 'America/Chicago');

        // Build end datetime
        if (!empty($appointment->end_date) && !empty($appointment->end_time)) {
            $end_datetime = $appointment->end_date . ' ' . $appointment->end_time;
        } else {
            // Default: 1 hour duration
            $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . ' +1 hour'));
        }
        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime(date('c', strtotime($end_datetime)));
        $end->setTimeZone(get_option('default_timezone') ?: 'America/Chicago');

        // Build location
        $location = !empty($appointment->address) ? $appointment->address : 'Online/Phone Call';

        // Build attendees list
        $attendees = [];
        
        // Add client/lead email as attendee
        if (!empty($appointment->email)) {
            $attendees[] = [
                'email' => $appointment->email,
                'displayName' => $client_name ?: 'Client'
            ];
        }

        // Add appointment attendees (staff members)
        if (!empty($appointment->id)) {
            $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
            $appointment_attendees = $CI->appointments_model->get_appointment_attendees($appointment->id);
            
            foreach ($appointment_attendees as $attendee) {
                if (!empty($attendee['email'])) {
                    $attendees[] = [
                        'email' => $attendee['email'],
                        'displayName' => $attendee['name']
                    ];
                }
            }
        }

        return [
            'summary' => $summary,
            'description' => $description,
            'location' => $location,
            'start' => $start,
            'end' => $end,
            'attendees' => $attendees,
        ];
    }

    /**
     * Sync all appointments for a staff member
     *
     * @param int $staff_id Staff ID
     * @return array Sync result
     */
    public function sync_all_appointments($staff_id)
    {
        if (!$this->ensure_valid_token($staff_id)) {
            return [
                'success' => false,
                'message' => 'No valid Google Calendar connection',
                'synced' => 0,
                'failed' => 0
            ];
        }

        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');

        // Get all appointments for this staff (past and future)
        $where = [
            'a.source' => 'ella_contractor',
        ];

        // Get appointments where staff is creator or attendee
        $CI->db->select('a.*');
        $CI->db->from(db_prefix() . 'appointly_appointments a');
        $CI->db->join(db_prefix() . 'appointly_attendees att', 'att.appointment_id = a.id', 'left');
        $CI->db->group_start();
        $CI->db->where('a.created_by', $staff_id);
        $CI->db->or_where('att.staff_id', $staff_id);
        $CI->db->group_end();
        $CI->db->group_by('a.id');

        $appointments = $CI->db->get()->result();

        $synced = 0;
        $failed = 0;

        foreach ($appointments as $appointment) {
            // Skip cancelled appointments
            if ($appointment->appointment_status === 'cancelled') {
                continue;
            }

            // If event already exists, update it
            if (!empty($appointment->google_event_id)) {
                $result = $this->update_event($appointment->id, $staff_id);
            } else {
                // Create new event
                $result = $this->create_event($appointment->id, $staff_id);
            }

            if ($result) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => true,
            'message' => 'Sync completed',
            'synced' => $synced,
            'failed' => $failed
        ];
    }
}
