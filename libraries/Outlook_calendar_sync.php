<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Outlook Calendar Sync Library for EllaContractors
 * Handles OAuth2 authentication and calendar event synchronization via Microsoft Graph API
 */
class Outlook_calendar_sync
{
    private $client_id;
    private $client_secret;
    private $tenant_id;
    private $redirect_uri;
    private $table_name;
    private $graph_api_endpoint = 'https://graph.microsoft.com/v1.0';
    private $auth_endpoint = 'https://login.microsoftonline.com';

    public function __construct()
    {
        // Use EllaContractors-specific credentials
        $this->client_id = get_option('outlook_calendar_client_id');
        $this->client_secret = get_option('outlook_calendar_client_secret');
        $this->tenant_id = get_option('outlook_calendar_tenant_id') ?: 'common';
        $this->redirect_uri = get_option('outlook_calendar_redirect_uri') ?: site_url('ella_contractors/outlook_auth/callback');
        $this->table_name = db_prefix() . 'staff_outlook_tokens';
    }

    /**
     * Get authorization URL for OAuth2 flow
     *
     * @param int $staff_id Staff ID
     * @return string|false Authorization URL or false on failure
     */
    public function get_authorization_url($staff_id)
    {
        if (empty($this->client_id) || empty($this->client_secret)) {
            log_message('error', 'Outlook Calendar: Missing client_id or client_secret in settings');
            return false;
        }

        try {
            // Generate state parameter for CSRF protection
            $state = base64_encode(json_encode([
                'staff_id' => $staff_id,
                'nonce' => uniqid(),
                'timestamp' => time()
            ]));

            // Required scopes for calendar access
            $scopes = [
                'openid',
                'profile',
                'offline_access',
                'User.Read',
                'Calendars.ReadWrite'
            ];

            $params = [
                'client_id' => $this->client_id,
                'response_type' => 'code',
                'redirect_uri' => $this->redirect_uri,
                'response_mode' => 'query',
                'scope' => implode(' ', $scopes),
                'state' => $state
            ];

            $auth_url = $this->auth_endpoint . '/' . $this->tenant_id . '/oauth2/v2.0/authorize?' . http_build_query($params);

            return $auth_url;
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar: Failed to create auth URL - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exchange authorization code for access tokens
     *
     * @param string $code Authorization code from Microsoft
     * @param int $staff_id Staff ID
     * @return array|false Token data or false on failure
     */
    public function exchange_code_for_tokens($code, $staff_id)
    {
        if (empty($this->client_id) || empty($this->client_secret)) {
            return false;
        }

        try {
            $token_url = $this->auth_endpoint . '/' . $this->tenant_id . '/oauth2/v2.0/token';

            $params = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'redirect_uri' => $this->redirect_uri,
                'grant_type' => 'authorization_code',
                'scope' => 'openid profile offline_access User.Read Calendars.ReadWrite'
            ];

            $ch = curl_init($token_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code !== 200) {
                log_message('error', 'Outlook Calendar: Token exchange failed - HTTP ' . $http_code . ' - ' . $response);
                return false;
            }

            $token_data = json_decode($response, true);

            if (isset($token_data['error'])) {
                log_message('error', 'Outlook Calendar: Token exchange error - ' . $token_data['error_description']);
                return false;
            }

            // Calculate expires_at
            $expires_in = isset($token_data['expires_in']) ? (int)$token_data['expires_in'] : 3600;
            $expires_at = date('Y-m-d H:i:s', time() + $expires_in);

            return [
                'access_token' => $token_data['access_token'],
                'refresh_token' => isset($token_data['refresh_token']) ? $token_data['refresh_token'] : null,
                'expires_in' => $expires_in,
                'expires_at' => $expires_at,
                'token_type' => isset($token_data['token_type']) ? $token_data['token_type'] : 'Bearer',
                'scope' => isset($token_data['scope']) ? $token_data['scope'] : ''
            ];
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar: Token exchange exception - ' . $e->getMessage());
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
            'token_type' => isset($tokens['token_type']) ? $tokens['token_type'] : 'Bearer',
            'scope' => isset($tokens['scope']) ? $tokens['scope'] : '',
            'calendar_id' => 'primary',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $CI->db->where('staff_id', $staff_id);
        $existing = $CI->db->get($this->table_name)->row();

        if ($existing) {
            $CI->db->where('staff_id', $staff_id);
            $CI->db->update($this->table_name, $data);
        } else {
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
            log_message('error', 'Outlook Calendar: No refresh token available for staff ' . $staff_id);
            return false;
        }

        try {
            $token_url = $this->auth_endpoint . '/' . $this->tenant_id . '/oauth2/v2.0/token';

            $params = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $tokens['refresh_token'],
                'grant_type' => 'refresh_token',
                'scope' => 'openid profile offline_access User.Read Calendars.ReadWrite'
            ];

            $ch = curl_init($token_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code !== 200) {
                log_message('error', 'Outlook Calendar: Token refresh failed - HTTP ' . $http_code . ' - ' . $response);
                return false;
            }

            $new_token = json_decode($response, true);

            if (isset($new_token['error'])) {
                log_message('error', 'Outlook Calendar: Token refresh error - ' . $new_token['error_description']);
                return false;
            }

            // Save refreshed tokens
            $expires_in = isset($new_token['expires_in']) ? (int)$new_token['expires_in'] : 3600;
            $expires_at = date('Y-m-d H:i:s', time() + $expires_in);

            $refresh_data = [
                'access_token' => $new_token['access_token'],
                'expires_at' => $expires_at,
                'expires_in' => $expires_in,
            ];

            // Preserve refresh token if not provided in new token
            if (isset($new_token['refresh_token'])) {
                $refresh_data['refresh_token'] = $new_token['refresh_token'];
            } else {
                $refresh_data['refresh_token'] = $tokens['refresh_token'];
            }

            return $this->save_tokens($staff_id, $refresh_data);
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar: Token refresh exception - ' . $e->getMessage());
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
                return ['connected' => false, 'message' => 'Not connected'];
            }

            $is_expired = false;
            if (!empty($tokens['expires_at'])) {
                $expires_timestamp = strtotime($tokens['expires_at']);
                $is_expired = time() >= $expires_timestamp;
            }

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
            log_message('error', 'Outlook Calendar: get_connection_status error - ' . $e->getMessage());
            return ['connected' => false, 'error' => $e->getMessage(), 'message' => 'Error checking status'];
        }
    }

    /**
     * Disconnect Outlook Calendar (delete tokens)
     *
     * @param int $staff_id Staff ID
     * @return bool Success status
     */
    public function disconnect($staff_id)
    {
        $CI = &get_instance();

        $CI->db->where('staff_id', $staff_id);
        $CI->db->delete($this->table_name);

        $CI->db->where('created_by', $staff_id);
        $CI->db->where('source', 'ella_contractor');
        $CI->db->update(db_prefix() . 'appointly_appointments', [
            'outlook_event_id' => null,
            'outlook_calendar_link' => null
        ]);

        $CI->db->where('staff_id', $staff_id);
        $CI->db->delete(db_prefix() . 'appointment_outlook_events');

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

        if (!empty($tokens['expires_at'])) {
            $expires_timestamp = strtotime($tokens['expires_at']);
            $time_until_expiry = $expires_timestamp - time();

            if ($time_until_expiry < 300) {
                if (!empty($tokens['refresh_token'])) {
                    return $this->refresh_token($staff_id);
                } else {
                    log_message('warning', 'Outlook Calendar: Token expired and no refresh token for staff ' . $staff_id);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Make Graph API request
     *
     * @param string $method HTTP method (GET, POST, PATCH, DELETE)
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @param int $staff_id Staff ID
     * @return array|false Response data or false on failure
     */
    private function graph_api_request($method, $endpoint, $staff_id, $data = null)
    {
        if (!$this->ensure_valid_token($staff_id)) {
            return false;
        }

        $tokens = $this->get_tokens($staff_id);
        $access_token = $tokens['access_token'];

        $url = $this->graph_api_endpoint . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // DELETE returns 204 No Content on success
        if ($method === 'DELETE' && $http_code === 204) {
            return ['success' => true];
        }

        if ($http_code >= 200 && $http_code < 300) {
            return json_decode($response, true);
        }

        log_message('error', 'Outlook Calendar: Graph API request failed - ' . $method . ' ' . $endpoint . ' - HTTP ' . $http_code . ' - ' . $response);
        return false;
    }

    /**
     * Create calendar event for appointment
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID
     * @return array|false Event data or false on failure
     */
    public function create_event($appointment_id, $staff_id)
    {
        if (!$this->ensure_valid_token($staff_id)) {
            log_message('error', 'Outlook Calendar: No valid token for staff ' . $staff_id);
            return false;
        }

        // Load appointment data
        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $appointment = $CI->appointments_model->get_appointment($appointment_id);

        if (!$appointment || $appointment->source !== 'ella_contractor') {
            log_message('error', 'Outlook Calendar: Invalid appointment or not EllaContractors appointment - ' . $appointment_id);
            return false;
        }

        try {
            // Build event data
            $event_data = $this->build_event_data($appointment);

            // Search for existing event before creating (duplicate prevention)
            $existing_event_id = $this->search_existing_event($staff_id, $event_data);
            
            if ($existing_event_id) {
                // Event already exists → save mapping and update it instead of creating duplicate
                log_message('info', 'Outlook Calendar: Found existing event for appointment ' . $appointment_id . ' (staff ' . $staff_id . '), updating instead of creating - Event ID: ' . $existing_event_id);
                $this->save_event_mapping($appointment_id, $staff_id, $existing_event_id, 'primary', null);
                return $this->update_event($appointment_id, $staff_id);
            }

            // No existing event found → proceed with create
            $response = $this->graph_api_request('POST', '/me/events', $staff_id, $event_data);

            if ($response && isset($response['id'])) {
                // Save event mapping to junction table
                $this->save_event_mapping(
                    $appointment_id,
                    $staff_id,
                    $response['id'],
                    'primary',
                    isset($response['webLink']) ? $response['webLink'] : null
                );

                // Also save to appointment table for backward compatibility (creator only)
                if ($staff_id == $appointment->created_by) {
                    $CI->db->where('id', $appointment_id);
                    $CI->db->update(db_prefix() . 'appointly_appointments', [
                        'outlook_event_id' => $response['id'],
                        'outlook_calendar_link' => isset($response['webLink']) ? $response['webLink'] : null
                    ]);
                }

                log_activity('Outlook Calendar event created for staff ' . $staff_id . ' [Appointment ID: ' . $appointment_id . ', Event ID: ' . $response['id'] . ']');

                return [
                    'event_id' => $response['id'],
                    'web_link' => isset($response['webLink']) ? $response['webLink'] : null,
                    'i_cal_uid' => isset($response['iCalUId']) ? $response['iCalUId'] : null
                ];
            }

            return false;
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar: Failed to create event - ' . $e->getMessage());
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

        // Check if event exists for this staff member
        $event_mapping = $this->get_event_mapping($appointment_id, $staff_id);

        if (empty($event_mapping)) {
            // Create new event instead
            return $this->create_event($appointment_id, $staff_id);
        }

        try {
            // Build updated event data
            $event_data = $this->build_event_data($appointment);

            // Update event via Graph API
            $response = $this->graph_api_request(
                'PATCH',
                '/me/events/' . $event_mapping['outlook_event_id'],
                $staff_id,
                $event_data
            );

            if ($response && isset($response['id'])) {
                // Update event mapping
                $this->save_event_mapping(
                    $appointment_id,
                    $staff_id,
                    $response['id'],
                    'primary',
                    isset($response['webLink']) ? $response['webLink'] : null
                );

                log_activity('Outlook Calendar event updated for staff ' . $staff_id . ' [Appointment ID: ' . $appointment_id . ', Event ID: ' . $response['id'] . ']');

                return [
                    'event_id' => $response['id'],
                    'web_link' => isset($response['webLink']) ? $response['webLink'] : null,
                    'i_cal_uid' => isset($response['iCalUId']) ? $response['iCalUId'] : null
                ];
            }

            // If event not found (404), create new one
            log_message('info', 'Outlook Calendar: Event not found for staff ' . $staff_id . ', creating new');
            $this->delete_event_mapping($appointment_id, $staff_id);
            return $this->create_event($appointment_id, $staff_id);
        } catch (Exception $e) {
            log_message('error', 'Outlook Calendar: Failed to update event - ' . $e->getMessage());
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

        // Check if event exists for this staff member
        $event_mapping = $this->get_event_mapping($appointment_id, $staff_id);

        if (empty($event_mapping)) {
            return true; // No event to delete
        }

        $CI = &get_instance();

        try {
            // Delete event via Graph API
            $response = $this->graph_api_request(
                'DELETE',
                '/me/events/' . $event_mapping['outlook_event_id'],
                $staff_id,
                null
            );

            // Delete event mapping from junction table
            $this->delete_event_mapping($appointment_id, $staff_id);

            log_activity('Outlook Calendar event deleted for staff ' . $staff_id . ' [Appointment ID: ' . $appointment_id . ', Event ID: ' . $event_mapping['outlook_event_id'] . ']');

            return true;
        } catch (Exception $e) {
            // If event not found, consider it already deleted
            $this->delete_event_mapping($appointment_id, $staff_id);
            log_message('info', 'Outlook Calendar: Event not found in Outlook, removed mapping for staff ' . $staff_id);
            return true;
        }
    }

    /**
     * Build event data array for Microsoft Graph API
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

        $subject = $appointment->subject;
        if ($client_name) {
            $subject .= ' - ' . $client_name;
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
        
        if (!empty($appointment->end_date) && !empty($appointment->end_time)) {
            $end_datetime = $appointment->end_date . ' ' . $appointment->end_time;
        } else {
            // Default: 1 hour duration
            $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . ' +1 hour'));
        }

        // Format for Microsoft Graph API (ISO 8601)
        $timezone = get_option('default_timezone') ?: 'America/Chicago';

        // Build location
        $location = !empty($appointment->address) ? $appointment->address : 'Online/Phone Call';

        // Build attendees list
        $attendees = [];

        // Add client/lead email as attendee
        if (!empty($appointment->email)) {
            $attendees[] = [
                'emailAddress' => [
                    'address' => $appointment->email,
                    'name' => $client_name ?: 'Client'
                ],
                'type' => 'required'
            ];
        }

        // Add appointment attendees (staff members)
        if (!empty($appointment->id)) {
            $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
            $appointment_attendees = $CI->appointments_model->get_appointment_attendees($appointment->id);

            foreach ($appointment_attendees as $attendee) {
                if (!empty($attendee['email'])) {
                    $attendees[] = [
                        'emailAddress' => [
                            'address' => $attendee['email'],
                            'name' => $attendee['name']
                        ],
                        'type' => 'required'
                    ];
                }
            }
        }

        return [
            'subject' => $subject,
            'body' => [
                'contentType' => 'Text',
                'content' => $description
            ],
            'start' => [
                'dateTime' => date('c', strtotime($start_datetime)),
                'timeZone' => $timezone
            ],
            'end' => [
                'dateTime' => date('c', strtotime($end_datetime)),
                'timeZone' => $timezone
            ],
            'location' => [
                'displayName' => $location
            ],
            'attendees' => $attendees,
            'isReminderOn' => true,
            'reminderMinutesBeforeStart' => 60
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
                'message' => 'No valid Outlook Calendar connection',
                'synced' => 0,
                'failed' => 0
            ];
        }

        $CI = &get_instance();
        $CI->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');

        // Get all appointments for this staff (past and future)
        $CI->db->select('a.*');
        $CI->db->from(db_prefix() . 'appointly_appointments a');
        $CI->db->join(db_prefix() . 'appointly_attendees att', 'att.appointment_id = a.id', 'left');
        $CI->db->group_start();
        $CI->db->where('a.created_by', $staff_id);
        $CI->db->or_where('att.staff_id', $staff_id);
        $CI->db->group_end();
        $CI->db->where('a.source', 'ella_contractor');
        $CI->db->group_by('a.id');

        $appointments = $CI->db->get()->result();

        $synced = 0;
        $failed = 0;

        foreach ($appointments as $appointment) {
            // Skip cancelled appointments
            if ($appointment->appointment_status === 'cancelled') {
                continue;
            }

            // Check if event already exists
            $event_mapping = $this->get_event_mapping($appointment->id, $staff_id);

            if ($event_mapping) {
                $result = $this->update_event($appointment->id, $staff_id);
            } else {
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

    // ==================== JUNCTION TABLE HELPERS ====================

    /**
     * Save event mapping to junction table
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID
     * @param string $outlook_event_id Outlook event ID
     * @param string $outlook_calendar_id Calendar ID
     * @param string $outlook_calendar_link Web link to event
     * @return bool Success status
     */
    private function save_event_mapping($appointment_id, $staff_id, $outlook_event_id, $outlook_calendar_id = 'primary', $outlook_calendar_link = null)
    {
        $CI = &get_instance();

        // Check if mapping exists
        $existing = $CI->db->get_where(db_prefix() . 'appointment_outlook_events', [
            'rel_type' => 'appointment',
            'rel_id' => $appointment_id,
            'staff_id' => $staff_id
        ])->row_array();

        $data = [
            'rel_type' => 'appointment',
            'rel_id' => $appointment_id,
            'org_id' => null,
            'staff_id' => $staff_id,
            'outlook_event_id' => $outlook_event_id,
            'outlook_calendar_id' => $outlook_calendar_id,
            'outlook_calendar_link' => $outlook_calendar_link,
        ];

        if ($existing) {
            // Update existing mapping
            $CI->db->where('id', $existing['id']);
            return $CI->db->update(db_prefix() . 'appointment_outlook_events', $data);
        } else {
            // Insert new mapping
            return $CI->db->insert(db_prefix() . 'appointment_outlook_events', $data);
        }
    }

    /**
     * Get event mapping from junction table
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID
     * @return array|null Event mapping data or null if not found
     */
    private function get_event_mapping($appointment_id, $staff_id)
    {
        $CI = &get_instance();

        $mapping = $CI->db->get_where(db_prefix() . 'appointment_outlook_events', [
            'rel_type' => 'appointment',
            'rel_id' => $appointment_id,
            'staff_id' => $staff_id
        ])->row_array();

        return $mapping ?: null;
    }

    /**
     * Delete event mapping from junction table
     *
     * @param int $appointment_id Appointment ID
     * @param int $staff_id Staff ID
     * @return bool Success status
     */
    private function delete_event_mapping($appointment_id, $staff_id)
    {
        $CI = &get_instance();

        $CI->db->where('rel_type', 'appointment');
        $CI->db->where('rel_id', $appointment_id);
        $CI->db->where('staff_id', $staff_id);
        return $CI->db->delete(db_prefix() . 'appointment_outlook_events');
    }

    /**
     * Get all event mappings for an appointment (all staff members)
     *
     * @param int $appointment_id Appointment ID
     * @return array Event mappings
     */
    public function get_all_event_mappings($appointment_id)
    {
        $CI = &get_instance();

        return $CI->db->get_where(db_prefix() . 'appointment_outlook_events', [
            'rel_type' => 'appointment',
            'rel_id' => $appointment_id
        ])->result_array();
    }

    /**
     * Delete all event mappings for an appointment (all staff members)
     *
     * @param int $appointment_id Appointment ID
     * @return bool Success status
     */
    public function delete_all_event_mappings($appointment_id)
    {
        $CI = &get_instance();

        $CI->db->where('rel_type', 'appointment');
        $CI->db->where('rel_id', $appointment_id);
        return $CI->db->delete(db_prefix() . 'appointment_outlook_events');
    }
    
    /**
     * Search for existing event in Outlook Calendar by matching appointment details
     * This prevents duplicate events when mappings are lost or don't exist yet
     * 
     * @param int $staff_id Staff ID
     * @param array $event_data Event data to match (from build_event_data)
     * @return string|null Event ID if found, null otherwise
     */
    private function search_existing_event($staff_id, $event_data)
    {
        try {
            // Extract search criteria from event data
            $subject = $event_data['subject'];
            $start_time = $event_data['start']['dateTime'];
            $timezone = $event_data['start']['timeZone'];
            
            // Parse start time
            $start_datetime = new DateTime($start_time);
            
            // Define search window: ±2 minutes tolerance to account for timing variations
            $search_start = clone $start_datetime;
            $search_start->modify('-2 minutes');
            
            $search_end = clone $start_datetime;
            $search_end->modify('+2 minutes');
            
            // Format times for Microsoft Graph API query
            $filter_start = $search_start->format('Y-m-d\TH:i:s');
            $filter_end = $search_end->format('Y-m-d\TH:i:s');
            
            // Build Graph API endpoint with calendarView query
            // calendarView expands recurring events and filters by time range
            $endpoint = '/me/calendarView?startDateTime=' . urlencode($filter_start) . 
                        '&endDateTime=' . urlencode($filter_end) . 
                        '&$top=50';
            
            // Query events via Microsoft Graph API
            $response = $this->graph_api_request('GET', $endpoint, $staff_id, null);
            
            if (!$response || !isset($response['value'])) {
                return null;
            }
            
            // Search for matching event by subject (title) and start time
            foreach ($response['value'] as $event) {
                // Match by subject (title) - must be exact match
                if (!isset($event['subject']) || $event['subject'] !== $subject) {
                    continue;
                }
                
                // Match by start time (within 2-minute tolerance)
                if (!isset($event['start']['dateTime'])) {
                    continue;
                }
                
                $event_start_dt = new DateTime($event['start']['dateTime']);
                $time_diff = abs($event_start_dt->getTimestamp() - $start_datetime->getTimestamp());
                
                // If time difference is within 2 minutes (120 seconds), consider it a match
                if ($time_diff <= 120) {
                    log_message('info', 'Outlook Calendar: Found existing event matching appointment details - Event ID: ' . $event['id'] . ', Subject: ' . $subject);
                    return $event['id'];
                }
            }
            
            // No matching event found
            return null;
        } catch (Exception $e) {
            // On search error, log and return null (safe to proceed with create)
            log_message('error', 'Outlook Calendar: search_existing_event failed - ' . $e->getMessage());
            return null;
        }
    }
}

