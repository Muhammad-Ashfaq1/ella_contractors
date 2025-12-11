<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

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

