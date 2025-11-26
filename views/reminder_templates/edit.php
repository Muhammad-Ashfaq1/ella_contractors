<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-<?php echo isset($template) && $template->id ? 'pencil-square-o' : 'plus'; ?>"></i> 
                            <?php echo isset($template) && $template->id ? 'Edit' : 'New'; ?> Reminder Template
                        </h4>
                        <hr class="hr-panel-heading" />
                        <form id="templateForm">
                                    <input type="hidden" name="id" value="<?php echo isset($template) ? $template->id : ''; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="template_name">Template Name <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="template_name" 
                                                       name="template_name" 
                                                       value="<?php echo isset($template) ? htmlspecialchars($template->template_name) : ''; ?>" 
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="template_type">Template Type <span class="text-danger">*</span></label>
                                                <select class="form-control" id="template_type" name="template_type" required>
                                                    <option value="">Select Type</option>
                                                    <option value="email" <?php echo (isset($template) && $template->template_type == 'email') ? 'selected' : ''; ?>>Email</option>
                                                    <option value="sms" <?php echo (isset($template) && $template->template_type == 'sms') ? 'selected' : ''; ?>>SMS</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="reminder_stage">Reminder Stage <span class="text-danger">*</span></label>
                                                <select class="form-control" id="reminder_stage" name="reminder_stage" required>
                                                    <option value="">Select Stage</option>
                                                    <option value="client_instant" <?php echo (isset($template) && $template->reminder_stage == 'client_instant') ? 'selected' : ''; ?>>Client Instant</option>
                                                    <option value="client_48h" <?php echo (isset($template) && $template->reminder_stage == 'client_48h') ? 'selected' : ''; ?>>Client 48 Hours</option>
                                                    <option value="staff_48h" <?php echo (isset($template) && $template->reminder_stage == 'staff_48h') ? 'selected' : ''; ?>>Staff 48 Hours</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="subjectGroup" style="<?php echo (isset($template) && $template->template_type == 'sms') ? 'display:none;' : ''; ?>">
                                                <label for="subject">Email Subject <span class="text-danger" id="subjectRequired">*</span></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="subject" 
                                                       name="subject" 
                                                       value="<?php echo isset($template) ? htmlspecialchars($template->subject) : ''; ?>" 
                                                       placeholder="e.g., Appointment Reminder: {appointment_subject}">
                                                <small class="text-muted">Use placeholders like {appointment_subject}, {appointment_date}, etc.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="message_content">Message Content <span class="text-danger">*</span></label>
                                                <textarea class="form-control" 
                                                          id="message_content" 
                                                          name="message_content" 
                                                          rows="10" 
                                                          required><?php echo isset($template) ? htmlspecialchars($template->message_content) : ''; ?></textarea>
                                                <small class="text-muted">
                                                    Available placeholders: 
                                                    <code>{appointment_subject}</code>, 
                                                    <code>{appointment_date}</code>, 
                                                    <code>{appointment_time}</code>, 
                                                    <code>{appointment_address}</code>, 
                                                    <code>{contact_name}</code>, 
                                                    <code>{contact_email}</code>, 
                                                    <code>{staff_name}</code>, 
                                                    <code>{company_name}</code>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" 
                                                               name="is_active" 
                                                               value="1" 
                                                               <?php echo (!isset($template) || $template->is_active) ? 'checked' : ''; ?>>
                                                        Active (Template will be used for reminders)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Save Template
                                            </button>
                                            <a href="<?php echo admin_url('ella_contractors/reminder_templates'); ?>" class="btn btn-default">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
$(document).ready(function() {
    // Show/hide subject field based on template type
    $('#template_type').on('change', function() {
        if ($(this).val() == 'email') {
            $('#subjectGroup').show();
            $('#subject').prop('required', true);
        } else {
            $('#subjectGroup').hide();
            $('#subject').prop('required', false);
        }
    });

    // Handle form submission
    $('#templateForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        formData += '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        
        $.post(admin_url + 'ella_contractors/reminder_templates/save', formData, function(response) {
            response = typeof response === 'string' ? JSON.parse(response) : response;
            
            if (response.success) {
                alert_float('success', response.message);
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert_float('danger', response.message);
            }
        }).fail(function() {
            alert_float('danger', 'An error occurred. Please try again.');
        });
    });
});
</script>

