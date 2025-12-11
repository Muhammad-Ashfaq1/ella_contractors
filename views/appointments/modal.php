<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
/* Dropzone styles for appointment modal */
.drop-zone {
  max-width: 100%;
  min-height: 150px;
  height: auto;
  padding: 25px;
  display: flex;
  flex-direction: column;
  width: 100%;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-family: "Quicksand", sans-serif;
  font-weight: 500;
  font-size: 16px;
  cursor: pointer;
  color: #666;
  border: 2px dashed #009578;
  border-radius: 10px;
  margin-top: 10px;
  position: relative;
  margin-bottom: 20px;
  transition: all 0.3s ease;
}

.drop-zone:hover {
  border-color: #007a5c;
  background-color: #f8f9fa;
}

.drop-zone--over {
  border-style: solid;
}

.drop-zone__input {
  display: none !important;
}

.drop-zone__thumb {
  width: 150px;
  height: 150px;
  margin: 5px;
  background-color: #fff;
  background-size: cover;
  position: relative;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.drop-zone__thumb img {
  max-width: 100%;
  max-height: 100px;
  display: block;
  height: 150px;
  width: 150px;
}

button.delete-btn {
  position: absolute;
  right: 0;
  top: 0;
  background: red;
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  z-index: 99999999;
  text-align: center;
  cursor: pointer;
}

.drop-zone__thumb div {
  content: attr(data-label);
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 5px 0;
  color: #ffffff;
  background: rgba(0, 0, 0, 0.75);
  font-size: 14px;
  text-align: center;
}

.drop-zone__thumbnails {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 15px;
  width: 100%;
  justify-content: center;
}

.drop-zone__prompt {
  display: block;
  width: 100%;
  text-align: center;
  color: #666;
  font-size: 16px;
  margin: auto;
  line-height: 1.5;
}

.upload-message {
  color: red;
  font-size: 14px;
  margin-top: 10px;
}

.loader {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  animation: spin 2s linear infinite;
  display: inline-block;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* ========================================
   PLACEHOLDER STYLING - Matching Measurements
   Only affects EMPTY placeholders, not values
   ======================================== */

/* Standard input placeholders (text, email, tel, etc.) */
#appointmentModal input::-webkit-input-placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

#appointmentModal input::-moz-placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

#appointmentModal input:-ms-input-placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

#appointmentModal input::placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

/* Textarea placeholders */
#appointmentModal textarea::-webkit-input-placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

#appointmentModal textarea::-moz-placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

#appointmentModal textarea:-ms-input-placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

#appointmentModal textarea::placeholder {
  color: #778485 !important;
  opacity: 0.85 !important;
}

/* Native select placeholder option (for non-Select2/Bootstrap Select dropdowns) */
#appointmentModal select option[value=""] {
  color: #778485 !important;
}

/* Bootstrap Select - Placeholder style ONLY for empty states */
#appointmentModal button[title="Select Client/Lead"] .filter-option-inner-inner,
#appointmentModal button[title="Nothing selected"] .filter-option-inner-inner,
#appointmentModal button[title="Loading staff members..."] .filter-option-inner-inner {
  color: #778485 !important;
  opacity: 0.85 !important;
}

/* ========================================
   DATETIME-LOCAL INPUT STYLING
   ======================================== */

/* Style empty datetime-local inputs to show placeholder-like styling */
#appointmentModal input[type="datetime-local"]:not(:focus):invalid {
  color: #778485 !important;
  opacity: 0.85 !important;
}

/* When datetime has a value, show normally */
#appointmentModal input[type="datetime-local"]:valid {
  color: #333 !important;
  opacity: 1 !important;
  font-style: normal !important;
}

/* Style the individual parts when empty */
#appointmentModal input[type="datetime-local"]:invalid::-webkit-datetime-edit {
  color: #778485 !important;
}

/* ========================================
   ENSURE FILLED VALUES REMAIN NORMAL
   ======================================== */

/* Any input with a value should display normally */
#appointmentModal input:not([type="radio"]):not([type="checkbox"]):not(:placeholder-shown) {
  color: #333 !important;
  opacity: 1 !important;
  font-style: normal !important;
}

#appointmentModal textarea:not(:placeholder-shown) {
  color: #333 !important;
  opacity: 1 !important;
  font-style: normal !important;
}

/* Client Notifications heading styling */
#appointmentModal h5 {
  font-weight: bold;
  font-size: 16px; /* +2px from default 14px */
}

/* Ensure native checkbox is hidden - fix for double checkbox display */
#appointmentModal .checkbox input[type="checkbox"] {
  position: absolute !important;
  opacity: 0 !important;
  z-index: 1 !important;
  width: 17px !important;
  height: 17px !important;
  margin: 0 !important;
  padding: 0 !important;
  cursor: pointer !important;
}

/* Ensure checkbox label has proper positioning for custom styling */
#appointmentModal .checkbox label {
  position: relative !important;
  padding-left: 5px !important;
  cursor: pointer !important;
}
</style>

<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="appointmentModalLabel">Create Appointment</h4>
            </div>
            <div class="modal-body">
                <form id="appointmentForm">
                    <input type="hidden" id="appointment_id" name="appointment_id" value="">
                    <!-- Hidden fields for separate date and time (for backend compatibility) -->
                    <input type="hidden" id="start_date" name="start_date" value="">
                    <input type="hidden" id="start_time" name="start_time" value="">
                    <input type="hidden" id="end_date" name="end_date" value="">
                    <input type="hidden" id="end_time" name="end_time" value="">
                    <!-- Hidden fields for data validation -->
                    <input type="hidden" id="email_validated" name="email_validated" value="1">
                    <input type="hidden" id="phone_validated" name="phone_validated" value="">
                    <input type="hidden" id="type_id" name="type_id" value="">
                    <input type="hidden" id="status" name="status" value="scheduled">
                    <input type="hidden" id="selected_presentation_ids" name="selected_presentation_ids" value="">
                    <input type="hidden" name="reminder_channel" value="both">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_id">Client/Lead <span class="text-danger">*</span></label>
                                <select class="form-control ajax-search" id="contact_id" name="contact_id" data-live-search="true" required>
                                    <option value="">Select Client/Lead</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject"> Appointment Name <span class="text-danger">*</span> 
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Give this appointment a friendly name to remember"></i>
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_datetime">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_datetime">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="attendees">Attendees 
                                    <!-- <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Which staff members will be joining you or handling this Appointment?"></i> -->
                                </label>
                                <select class="form-control selectpicker" id="attendees" name="attendees[]" multiple data-live-search="true">
                                    <option value="">Loading staff members...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5>Client Reminders</h5>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="send_reminder" id="send_reminder" value="1" checked>
                                        <label for="send_reminder">
                                            Appointment Creation Notice
                                            <i class="fa fa-info-circle text-info reminder-template-preview" 
                                               data-reminder-stage="client_instant" 
                                               data-template-type="email" 
                                               data-recipient-type="client"
                                               style="cursor: pointer; margin-left: 5px;" 
                                               title="Preview/Edit Email Template"></i>
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="reminder_48h" id="reminder_48h" value="1" checked>
                                        <label for="reminder_48h">
                                            2 Day Notice
                                            <i class="fa fa-info-circle text-info reminder-template-preview" 
                                               data-reminder-stage="client_48h" 
                                               data-template-type="email" 
                                               data-recipient-type="client"
                                               style="cursor: pointer; margin-left: 5px;" 
                                               title="Preview/Edit Email Template"></i>
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="reminder_same_day" id="reminder_same_day" value="1">
                                        <label for="reminder_same_day">
                                            Same Day Reminder
                                            <i class="fa fa-info-circle text-info reminder-template-preview" 
                                               data-reminder-stage="client_same_day" 
                                               data-template-type="email" 
                                               data-recipient-type="client"
                                               style="cursor: pointer; margin-left: 5px;" 
                                               title="Preview/Edit Email Template"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <h5>My Reminder</h5>
                                    <div class="checkbox checkbox-success">
                                        <input type="checkbox" name="staff_reminder_48h" id="staff_reminder_48h" value="1" checked>
                                        <label for="staff_reminder_48h">
                                            2 Day Notice
                                            <i class="fa fa-info-circle text-info reminder-template-preview" 
                                               data-reminder-stage="staff_48h" 
                                               data-template-type="email" 
                                               data-recipient-type="staff"
                                               style="cursor: pointer; margin-left: 5px;" 
                                               title="Preview/Edit Email Template"></i>
                                        </label>
                                    </div>
                                    <div class="checkbox checkbox-success">
                                        <input type="checkbox" name="staff_reminder_same_day" id="staff_reminder_same_day" value="1">
                                        <label for="staff_reminder_same_day">
                                            Same Day Reminder
                                            <i class="fa fa-info-circle text-info reminder-template-preview" 
                                               data-reminder-stage="staff_same_day" 
                                               data-template-type="email" 
                                               data-recipient-type="staff"
                                               style="cursor: pointer; margin-left: 5px;" 
                                               title="Preview/Edit Email Template (includes presentations)"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Choose Presentations <span style="opacity: 0.7;">(Optional)</span></h5>
                            <div class="form-group">
                                <select class="form-control selectpicker" id="presentation_select" name="presentation_ids[]" multiple data-live-search="true">
                                    <option value="">Nothing selected</option>
                                </select>
                            </div>
                            <h5>Selected Presentations</h5>
                            <div id="modal-presentation-list">
                                <p style="text-align: center; color: #778485; margin: 10px 0;">None</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <hr class="hr-panel-heading" />
                            <h5>Attachments</h5>                            
                            <div class="drop-zone" id="appointmentDropzone">
                                <span class="drop-zone__prompt">Drop Files Here or Click to Select</span>
                                <input type="file" name="appointment_files[]" class="drop-zone__input" id="appointment_files" multiple accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                <div class="drop-zone__thumbnails" id="appointmentThumbnails"></div>
                            </div>
                            
                            <!-- Hidden field to store uploaded file paths -->
                            <input type="hidden" id="appointment_uploaded_files" name="appointment_uploaded_files" value="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" id="saveAppointment">Save Appointment</button>
            </div>
        </div>
    </div>
</div>

<!-- Template Preview/Edit Modal -->
<div class="modal fade" id="reminderTemplateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-envelope"></i> Email Template Preview & Edit
                    <small class="text-muted" id="template_reminder_type_display"></small>
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="margin-bottom: 20px;">
                    <i class="fa fa-info-circle"></i> <strong>What is this?</strong> This is the email template that will be sent to your clients/staff. 
                    The placeholders like <code>{appointment_subject}</code> will be automatically replaced with actual appointment details when the email is sent.
                </div>
                
                <form id="templateEditForm">
                    <input type="hidden" id="template_id" name="id">
                    <input type="hidden" id="template_reminder_stage" name="reminder_stage">
                    <input type="hidden" id="template_type" name="template_type">
                    <input type="hidden" id="template_recipient_type" name="recipient_type">
                    
                    <div class="form-group">
                        <label>Template Name</label>
                        <input type="text" class="form-control" id="template_name" name="template_name" readonly>
                    </div>
                    
                    <div class="form-group" id="template_subject_group" style="display:none;">
                        <label>Email Subject Line</label>
                        <input type="text" class="form-control" id="template_subject" name="subject" placeholder="e.g., Appointment Confirmation: {appointment_subject}">
                        <small class="text-muted">
                            <i class="fa fa-lightbulb-o"></i> Tip: Use placeholders like <code>{appointment_subject}</code>, <code>{appointment_date}</code>, <code>{client_name}</code>
                        </small>
                    </div>
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active">
                            <a href="#template-preview-tab" aria-controls="template-preview-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-eye"></i> Preview (How it looks)
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#template-edit-tab" aria-controls="template-edit-tab" role="tab" data-toggle="tab">
                                <i class="fa fa-code"></i> Edit HTML Code
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Preview Tab -->
                        <div role="tabpanel" class="tab-pane active" id="template-preview-tab">
                            <div class="alert alert-warning" style="margin-bottom: 15px;">
                                <i class="fa fa-exclamation-triangle"></i> <strong>Preview Mode:</strong> This shows how the email will look. 
                                Placeholders like <code>{appointment_subject}</code> will show as-is here, but will be replaced with real data when sent.
                            </div>
                            <div id="template_preview_container" style="border: 1px solid #ddd; padding: 20px; background: #f9f9f9; max-height: 600px; overflow-y: auto;">
                                <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading preview...</p>
                            </div>
                        </div>
                        
                        <!-- Edit Tab -->
                        <div role="tabpanel" class="tab-pane" id="template-edit-tab">
                            <div class="form-group">
                                <label>
                                    Email Content (HTML)
                                    <button type="button" class="btn btn-xs btn-default pull-right" id="toggle_merge_fields_help" style="margin-left: 10px;">
                                        <i class="fa fa-question-circle"></i> Show Available Fields
                                    </button>
                                </label>
                                <textarea class="form-control" id="template_content" name="content" rows="20" style="font-family: 'Courier New', monospace; font-size: 13px;"></textarea>
                                
                                <div id="merge_fields_help" style="display:none; margin-top: 10px; padding: 15px; background: #f0f7ff; border-left: 4px solid #007bff; border-radius: 4px;">
                                    <strong><i class="fa fa-info-circle"></i> Available Placeholders:</strong>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-md-6">
                                            <ul style="margin-bottom: 0;">
                                                <li><code>{appointment_subject}</code> - Appointment title</li>
                                                <li><code>{appointment_date}</code> - Date (e.g., "December 4, 2025")</li>
                                                <li><code>{appointment_time}</code> - Time (e.g., "2:39 AM")</li>
                                                <li><code>{appointment_location}</code> - Address/location</li>
                                                <li><code>{client_name}</code> - Client/lead name</li>
                                                <li><code>{staff_name}</code> - Staff member name</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul style="margin-bottom: 0;">
                                                <li><code>{company_name}</code> - Your company name</li>
                                                <li><code>{company_phone}</code> - Company phone</li>
                                                <li><code>{company_email}</code> - Company email</li>
                                                <li><code>{appointment_notes}</code> - Appointment notes</li>
                                                <li><code>{presentation_block}</code> - Presentation links <small class="text-muted">(staff only)</small></li>
                                                <li><code>{crm_link}</code> - Link to CRM <small class="text-muted">(staff only)</small></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p style="margin-top: 10px; margin-bottom: 0; font-size: 12px;">
                                        <strong>Tip:</strong> Just type the placeholder exactly as shown (with curly braces) and it will be replaced automatically when the email is sent.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <div class="checkbox">
                            <input type="checkbox" id="template_is_active" name="is_active" value="1" checked>
                            <label for="template_is_active">
                                <strong>Active</strong> - Use this template when sending reminders
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" id="saveTemplateBtn">
                    <i class="fa fa-save"></i> Save Template
                </button>
            </div>
        </div>
    </div>
</div>


