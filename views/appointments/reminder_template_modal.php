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
            <div class="modal-body" style="padding: 15px;">
                <div class="alert alert-info" style="margin-bottom: 15px; font-size: 13px;">
                    <i class="fa fa-info-circle"></i> <strong>Customize Your Email Template:</strong> Select which information to include and edit the content directly in the preview. Changes update in real-time.
                </div>
                
                <form id="templateEditForm">
                    <input type="hidden" id="template_id" name="id">
                    <input type="hidden" id="template_reminder_stage" name="reminder_stage">
                    <input type="hidden" id="template_type" name="template_type">
                    <input type="hidden" id="template_recipient_type" name="recipient_type">
                    <input type="hidden" id="template_content" name="content">
                    <input type="hidden" id="template_structure" name="template_structure">
                    
                    <div class="row">
                        <!-- Left Column: Field Selector -->
                        <div class="col-md-4" style="border-right: 1px solid #ddd; padding-right: 15px;">
                            <div class="form-group" id="template_subject_group" style="margin-bottom: 20px;">
                                <label><strong>Email Subject Line</strong></label>
                                <input type="text" class="form-control" id="template_subject" name="subject" placeholder="e.g., Appointment Confirmation">
                                <small class="text-muted" style="font-size: 11px;">You can use: {appointment_subject}, {appointment_date}</small>
                            </div>
                            
                            <h5 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; font-weight: bold;">
                                <i class="fa fa-check-square-o"></i> Include These Fields:
                            </h5>
                            
                            <div id="template_fields_selector" style="max-height: 500px; overflow-y: auto;">
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_appointment_subject" data-field="{appointment_subject}" checked>
                                    <label for="field_appointment_subject" style="font-weight: normal; cursor: pointer;">
                                        <strong>Appointment Title</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_appointment_date" data-field="{appointment_date}" checked>
                                    <label for="field_appointment_date" style="font-weight: normal; cursor: pointer;">
                                        <strong>Appointment Date</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_appointment_time" data-field="{appointment_time}" checked>
                                    <label for="field_appointment_time" style="font-weight: normal; cursor: pointer;">
                                        <strong>Appointment Time</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_appointment_location" data-field="{appointment_location}" checked>
                                    <label for="field_appointment_location" style="font-weight: normal; cursor: pointer;">
                                        <strong>Location/Address</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_client_name" data-field="{client_name}" checked>
                                    <label for="field_client_name" style="font-weight: normal; cursor: pointer;">
                                        <strong>Client Name</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_staff_name" data-field="{staff_name}" checked>
                                    <label for="field_staff_name" style="font-weight: normal; cursor: pointer;">
                                        <strong>Staff Name</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_company_name" data-field="{company_name}" checked>
                                    <label for="field_company_name" style="font-weight: normal; cursor: pointer;">
                                        <strong>Company Name</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_company_phone" data-field="{company_phone}">
                                    <label for="field_company_phone" style="font-weight: normal; cursor: pointer;">
                                        <strong>Company Phone</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_company_email" data-field="{company_email}">
                                    <label for="field_company_email" style="font-weight: normal; cursor: pointer;">
                                        <strong>Company Email</strong>
                                    </label>
                                </div>
                                <div class="checkbox" style="margin-bottom: 10px;">
                                    <input type="checkbox" id="field_appointment_notes" data-field="{appointment_notes}">
                                    <label for="field_appointment_notes" style="font-weight: normal; cursor: pointer;">
                                        <strong>Appointment Notes</strong>
                                    </label>
                                </div>
                                <div class="checkbox" id="field_presentation_block_wrapper" style="margin-bottom: 10px; display: none;">
                                    <input type="checkbox" id="field_presentation_block" data-field="{presentation_block}">
                                    <label for="field_presentation_block" style="font-weight: normal; cursor: pointer;">
                                        <strong>Presentation Links</strong> <small class="text-muted">(Staff only)</small>
                                    </label>
                                </div>
                                <div class="checkbox" id="field_crm_link_wrapper" style="margin-bottom: 10px; display: none;">
                                    <input type="checkbox" id="field_crm_link" data-field="{crm_link}">
                                    <label for="field_crm_link" style="font-weight: normal; cursor: pointer;">
                                        <strong>CRM Link</strong> <small class="text-muted">(Staff only)</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                                <div class="checkbox">
                                    <input type="checkbox" id="template_is_active" name="is_active" value="1" checked>
                                    <label for="template_is_active" style="font-weight: normal;">
                                        <strong>Active</strong> - Use this template when sending reminders
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Editable Preview -->
                        <div class="col-md-8" style="padding-left: 20px;">
                            <h5 style="margin-top: 0; margin-bottom: 15px; font-size: 14px; font-weight: bold;">
                                <i class="fa fa-eye"></i> Email Preview (Click to Edit)
                            </h5>
                            <div id="template_preview_container" 
                                 contenteditable="true" 
                                 style="border: 2px solid #ddd; padding: 20px; background: #ffffff; min-height: 500px; max-height: 600px; overflow-y: auto; border-radius: 4px; font-family: Arial, sans-serif; cursor: text;">
                                <p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading preview...</p>
                            </div>
                            <small class="text-muted" style="display: block; margin-top: 10px;">
                                <i class="fa fa-lightbulb-o"></i> Tip: Click anywhere in the preview to edit the text. Fields marked with <span style="background: #fff3cd; padding: 2px 5px; border-radius: 3px;">yellow</span> will be replaced with actual data.
                            </small>
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

