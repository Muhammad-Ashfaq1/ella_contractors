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
        if (!has_permission('ella_contractors', '', 'view')) {
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
                    'edit_url' => admin_url('proposals/proposal/' . $prop['id']),
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
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->db->where('appointment_id', $appointment_id);
        $count = $this->db->count_all_results(db_prefix() . 'proposals');

        echo json_encode([
            'success' => true,
            'count' => $count
        ]);
    }
}

