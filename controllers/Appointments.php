<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointments extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Ella_appointments_model', 'appointments_model');
        $this->load->model('staff_model');
        $this->load->model('clients_model');
        $this->load->model('leads_model');
    }

    /**
     * Appointments listing page
     */
    public function index()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Appointments';
        $data['appointment_types'] = $this->appointments_model->get_appointment_types();
        $data['statuses'] = $this->appointments_model->get_statuses();
        $this->load->view('appointments/index', $data);
    }

    /**
     * Create appointment page (redirects to index with modal)
     */
    public function create()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        redirect(admin_url('ella_contractors/appointments'));
    }

    /**
     * Edit appointment page
     */
    public function edit($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            show_404();
        }

        $data['title'] = 'Edit Appointment';
        $data['appointment'] = (array) $appointment; // Convert object to array
        $data['staff'] = $this->staff_model->get();
        $data['clients'] = $this->clients_model->get();
        $data['leads'] = $this->leads_model->get();
        $data['appointment_types'] = $this->appointments_model->get_appointment_types();
        $data['statuses'] = $this->appointments_model->get_statuses();
        $data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
        
        $this->load->view('appointments/edit', $data);
    }

    /**
     * View appointment details
     */
    public function view($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $appointment = $this->appointments_model->get_appointment($id);
        if (!$appointment) {
            show_404();
        }

        $data['title'] = 'View Appointment';
        $data['appointment'] = (array) $appointment; // Convert object to array
        $data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
        
        $this->load->view('appointments/view', $data);
    }

    /**
     * Save appointment (create/update)
     */
    public function save()
    {
        if (!has_permission('ella_contractors', '', 'create') && !has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('start_hour', 'Start Time', 'required');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'subject' => $this->input->post('subject'),
                'description' => $this->input->post('description'),
                'date' => $this->input->post('date'),
                'start_hour' => $this->input->post('start_hour'),
                'contact_id' => $this->input->post('contact_id') ?: null,
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address' => $this->input->post('address'),
                'notes' => $this->input->post('notes'),
                'type_id' => $this->input->post('type_id') ?: 0,
                'approved' => $this->input->post('approved') ? 1 : 0,
                'finished' => $this->input->post('finished') ? 1 : 0,
                'cancelled' => $this->input->post('cancelled') ? 1 : 0,
                'source' => 'ella_contractor'
            ];

            $appointment_id = $this->input->post('appointment_id');
            
            if ($appointment_id) {
                // Update existing appointment
                if ($this->appointments_model->update_appointment($appointment_id, $data)) {
                    set_alert('success', 'Appointment updated successfully');
                } else {
                    set_alert('warning', 'Failed to update appointment');
                }
            } else {
                // Create new appointment
                $appointment_id = $this->appointments_model->create_appointment($data);
                if ($appointment_id) {
                    set_alert('success', 'Appointment created successfully');
                } else {
                    set_alert('warning', 'Failed to create appointment');
                }
            }

            // Handle attendees
            if ($appointment_id) {
                $attendees = $this->input->post('attendees');
                if ($attendees && is_array($attendees)) {
                    // Remove existing attendees
                    $this->db->where('appointment_id', $appointment_id);
                    $this->db->delete(db_prefix() . 'appointly_attendees');
                    
                    // Add new attendees
                    foreach ($attendees as $staff_id) {
                        $this->appointments_model->add_attendee($appointment_id, $staff_id);
                    }
                }
            }
        }

        redirect(admin_url('ella_contractors/appointments'));
    }

    /**
     * Delete appointment
     */
    public function delete($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        if ($this->appointments_model->delete_appointment($id)) {
            set_alert('success', 'Appointment deleted successfully');
        } else {
            set_alert('warning', 'Failed to delete appointment');
        }

        redirect(admin_url('ella_contractors/appointments'));
    }

    /**
     * DataTable server-side processing
     */
    public function table()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('ella_contractors', 'admin/tables/ella_appointments'));
    }

    /**
     * Get appointments data for AJAX
     */
    public function get_appointments_ajax()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $appointments = $this->appointments_model->get_appointments();
        echo json_encode($appointments);
    }

    /**
     * Get upcoming appointments
     */
    public function upcoming()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Upcoming Appointments';
        $data['appointments'] = $this->appointments_model->get_upcoming_appointments();
        $this->load->view('appointments/upcoming', $data);
    }

    /**
     * Get past appointments
     */
    public function past()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Past Appointments';
        $this->load->view('appointments/past', $data);
    }

    /**
     * Get appointment data for modal (AJAX)
     */
    public function get_appointment_data()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        // Debug: Log the ID being requested
        log_message('debug', 'Getting appointment data for ID: ' . $id);
        
        $appointment = $this->appointments_model->get_appointment($id);
        
        if ($appointment) {
            // Convert object to array
            $appointment_data = (array) $appointment;
            $appointment_data['attendees'] = $this->appointments_model->get_appointment_attendees($id);
            
            // Debug: Log the appointment data
            log_message('debug', 'Appointment data: ' . json_encode($appointment_data));
            
            echo json_encode([
                'success' => true,
                'data' => $appointment_data
            ]);
        } else {
            log_message('debug', 'Appointment not found for ID: ' . $id);
            echo json_encode([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
        }
    }

    /**
     * Save appointment via AJAX (for modal)
     */
    public function save_ajax()
    {
        if (!has_permission('ella_contractors', '', 'create') && !has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', 'Subject', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('start_hour', 'Start Time', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors()
            ]);
            return;
        }

        $data = [
            'subject' => $this->input->post('subject'),
            'description' => $this->input->post('description'),
            'date' => $this->input->post('date'),
            'start_hour' => $this->input->post('start_hour'),
            'contact_id' => $this->input->post('contact_id') ?: null,
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'address' => $this->input->post('address'),
            'notes' => $this->input->post('notes'),
            'type_id' => $this->input->post('type_id') ?: 0,
            'approved' => $this->input->post('approved') ? 1 : 0,
            'finished' => $this->input->post('finished') ? 1 : 0,
            'cancelled' => $this->input->post('cancelled') ? 1 : 0,
            'source' => 'ella_contractor'
        ];

        // Debug: Log the data being sent
        log_message('debug', 'Appointment data: ' . json_encode($data));

        $appointment_id = $this->input->post('appointment_id');
        
        try {
            if ($appointment_id) {
                // Update existing appointment
                if ($this->appointments_model->update_appointment($appointment_id, $data)) {
                    // Handle attendees
                    $this->handle_attendees($appointment_id);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment updated successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update appointment. Database error: ' . $this->db->last_query()
                    ]);
                }
            } else {
                // Create new appointment
                $appointment_id = $this->appointments_model->create_appointment($data);
                if ($appointment_id) {
                    // Handle attendees
                    $this->handle_attendees($appointment_id);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Appointment created successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create appointment. Database error: ' . $this->db->last_query()
                    ]);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete appointment via AJAX
     */
    public function delete_ajax()
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        if ($this->appointments_model->delete_appointment($id)) {
            echo json_encode([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete appointment'
            ]);
        }
    }

    /**
     * Handle attendees for appointment
     */
    private function handle_attendees($appointment_id)
    {
        $attendees = $this->input->post('attendees');
        if ($attendees && is_array($attendees)) {
            // Remove existing attendees
            $this->db->where('appointment_id', $appointment_id);
            $this->db->delete(db_prefix() . 'appointly_attendees');
            
            // Add new attendees
            foreach ($attendees as $staff_id) {
                $this->appointments_model->add_attendee($appointment_id, $staff_id);
            }
        }
    }
}
