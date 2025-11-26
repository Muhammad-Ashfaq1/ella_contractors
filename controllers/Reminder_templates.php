<?php defined('BASEPATH') or exit('No direct script access allowed');

class Reminder_templates extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ella_contractors/Reminder_templates_model', 'templates_model');
    }

    /**
     * List all reminder templates
     */
    public function index()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Reminder Templates';
        $data['templates'] = $this->templates_model->get_templates();
        
        $this->load->view('reminder_templates/index', $data);
    }

    /**
     * Edit template (loads form)
     */
    public function edit($id = '')
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $template = null;
        
        if ($id != '' && $id != 'new') {
            $template = $this->templates_model->get_template($id);
            
            if (!$template) {
                show_404();
            }
        }

        $data['title'] = ($template && $template->id) ? 'Edit Reminder Template' : 'New Reminder Template';
        $data['template'] = $template;
        
        $this->load->view('reminder_templates/edit', $data);
    }

    /**
     * Save template (create or update)
     */
    public function save()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $data = [
                'template_name' => $this->input->post('template_name'),
                'template_type' => $this->input->post('template_type'),
                'reminder_stage' => $this->input->post('reminder_stage'),
                'subject' => $this->input->post('subject'),
                'message_content' => $this->input->post('message_content', false), // false = don't escape HTML
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            // Validate required fields
            if (empty($data['template_name']) || empty($data['template_type']) || 
                empty($data['reminder_stage']) || empty($data['message_content'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ]);
                return;
            }

            // For email templates, subject is required
            if ($data['template_type'] == 'email' && empty($data['subject'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email subject is required for email templates'
                ]);
                return;
            }

            if ($id) {
                // Update existing template
                $result = $this->templates_model->update_template($id, $data);
                $message = $result ? 'Template updated successfully' : 'Failed to update template';
            } else {
                // Create new template
                // Check if template with same type and stage already exists
                $existing = $this->templates_model->get_template_by_type_stage($data['template_type'], $data['reminder_stage']);
                if ($existing && $existing->id != $id) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'A template for this type and stage already exists. Please edit the existing template instead.'
                    ]);
                    return;
                }
                
                $result = $this->templates_model->create_template($data);
                $message = $result ? 'Template created successfully' : 'Failed to create template';
            }

            if ($result) {
                set_alert('success', $message);
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'redirect' => admin_url('ella_contractors/reminder_templates')
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $message
                ]);
            }
        }
    }

    /**
     * Toggle template active status via AJAX
     */
    public function toggle_active()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Template ID is required'
            ]);
            return;
        }

        $result = $this->templates_model->toggle_active($id);
        
        if ($result) {
            $template = $this->templates_model->get_template($id);
            echo json_encode([
                'success' => true,
                'message' => 'Template status updated',
                'is_active' => $template->is_active
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update template status'
            ]);
        }
    }

    /**
     * Delete template via AJAX
     */
    public function delete()
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Template ID is required'
            ]);
            return;
        }

        $result = $this->templates_model->delete_template($id);
        
        if ($result) {
            set_alert('success', 'Template deleted successfully');
            echo json_encode([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete template'
            ]);
        }
    }

    /**
     * Get template preview (for testing)
     */
    public function preview()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $id = $this->input->post('id');
        
        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => 'Template ID is required'
            ]);
            return;
        }

        $template = $this->templates_model->get_template($id);
        
        if (!$template) {
            echo json_encode([
                'success' => false,
                'message' => 'Template not found'
            ]);
            return;
        }

        // Replace placeholders with sample data
        $preview_content = $template->message_content;
        $preview_subject = $template->subject;
        
        $replacements = [
            '{appointment_subject}' => 'Sample Appointment',
            '{appointment_date}' => date('F j, Y'),
            '{appointment_time}' => '10:00 AM',
            '{appointment_address}' => '123 Main Street, City, State 12345',
            '{contact_name}' => 'John Doe',
            '{contact_email}' => 'john.doe@example.com',
            '{staff_name}' => 'Jane Smith',
            '{company_name}' => get_option('companyname') ?: 'Your Company'
        ];

        foreach ($replacements as $placeholder => $value) {
            $preview_content = str_replace($placeholder, $value, $preview_content);
            if ($preview_subject) {
                $preview_subject = str_replace($placeholder, $value, $preview_subject);
            }
        }

        echo json_encode([
            'success' => true,
            'subject' => $preview_subject,
            'content' => nl2br(htmlspecialchars($preview_content))
        ]);
    }
}

