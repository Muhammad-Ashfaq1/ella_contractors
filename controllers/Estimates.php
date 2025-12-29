<?php defined('BASEPATH') or exit('No direct script access allowed');

class Estimates extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposals_model');
        $this->load->model('staff_model');
    }

    /**
     * Get estimates/proposals for a specific appointment (AJAX)
     * @param int $appointment_id
     */
    public function get_appointment_estimates($appointment_id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        if (!$appointment_id) {
            echo json_encode([
                'success' => false,
                'message' => 'No appointment ID provided'
            ]);
            return;
        }

        // Get all proposals linked to this appointment
        $proposals = $this->proposals_model->get('', ['appointment_id' => $appointment_id]);
        
        // If single proposal returned as object, convert to array
        if (is_object($proposals)) {
            $proposals = [$proposals];
        }

        // Format the data for display
        $formatted_proposals = [];
        
        if (!empty($proposals)) {
            foreach ($proposals as $proposal) {
                // Convert object to array if needed
                $prop = is_object($proposal) ? (array)$proposal : $proposal;
                
                // Build edit URL with appointment parameters
                $edit_params = '?create_estimates=true&appt_id=' . $appointment_id;
                if (!empty($prop['rel_type']) && !empty($prop['rel_id'])) {
                    $edit_params .= '&rel_type=' . $prop['rel_type'] . '&rel_id=' . $prop['rel_id'];
                }
                
                $formatted_proposals[] = [
                    'id' => $prop['id'],
                    'subject' => $prop['subject'],
                    'proposal_to' => $prop['proposal_to'],
                    'total' => $prop['total'],
                    'date' => $prop['date'],
                    'open_till' => $prop['open_till'],
                    'status' => $prop['status'],
                    'status_formatted' => format_proposal_status($prop['status']),
                    'view_url' => admin_url('proposals/list_proposals/' . $prop['id']),
                    'edit_url' => admin_url('proposals/proposal/' . $prop['id'] . $edit_params),
                    'created_by' => get_staff_full_name($prop['addedfrom']),
                    'date_created' => $prop['datecreated']
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $formatted_proposals,
            'count' => count($formatted_proposals)
        ]);
    }

    /**
     * Get estimates count for appointment
     * @param int $appointment_id
     */
    public function get_appointment_estimates_count($appointment_id)
    {
        if (!has_permission('ella_contractor', '', 'view')) {
            ajax_access_denied();
        }

        $this->db->where('appointment_id', $appointment_id);
        $count = $this->db->count_all_results(db_prefix() . 'proposals');

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Send estimate via email and SMS (AJAX)
     * Uses the same flow as Proposals controller send_proposal_sms_email method
     * @param int $proposal_id
     */
    public function send_estimate($proposal_id)
    {
        // Check view permissions (same as Proposals controller)
        $canView = user_can_view_proposal($proposal_id);
        if (!$canView) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }
        
        if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && $canView == false) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }

        if (!$proposal_id) {
            echo json_encode([
                'success' => false,
                'message' => 'No proposal ID provided'
            ]);
            return;
        }

        // Send proposal to email (exactly like send_proposal_sms_email method)
        $success = $this->proposals_model->send_proposal_to_email($proposal_id);
        
        // Get proposal details for SMS
        $getDetails = $this->proposals_model->get($proposal_id);
        
        // Also send SMS if phone number exists (exactly like proposal.php)
        if(!empty($getDetails->phone)) {
            $staff_id = get_staff_user_id();
            $pro_text = get_sms_template($getDetails->rel_id, 'send_proposal_notification_to_customer');
            $sms_body = replace_proposal_name_shortcodes($proposal_id, $pro_text);
            $this->proposals_model->send_proposal_sms($getDetails->rel_id, $staff_id, $getDetails->phone, $sms_body, '', true); 
        }
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Estimate sent successfully via Email & SMS'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send estimate. Please check email configuration.'
            ]);
        }
    }

    /**
     * Delete estimate (AJAX)
     * @param int $proposal_id
     */
    public function delete_estimate($proposal_id)
    {
        if (!has_permission('proposals', '', 'delete')) {
            ajax_access_denied();
        }

        if (!$proposal_id) {
            echo json_encode([
                'success' => false,
                'message' => 'No proposal ID provided'
            ]);
            return;
        }

        $response = $this->proposals_model->delete($proposal_id);
        
        if ($response == true) {
            echo json_encode([
                'success' => true,
                'message' => 'Estimate deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete estimate'
            ]);
        }
    }
}

