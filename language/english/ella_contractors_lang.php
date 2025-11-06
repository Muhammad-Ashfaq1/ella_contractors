<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * EllaContractors Language File
 * Contains translations for appointment activity logging
 */

$lang['appointment_activity_created'] = 'Appointment created';
$lang['appointment_activity_updated'] = 'Appointment updated';
$lang['appointment_activity_status_changed'] = 'Appointment status changed';
$lang['appointment_activity_measurement_added'] = 'Measurement added to appointment';
$lang['appointment_activity_measurement_removed'] = 'Measurement removed from appointment';
$lang['appointment_activity_process'] = 'Scheduled event process';
$lang['appointment_activity_deleted'] = 'Appointment deleted';
$lang['appointment_details'] = 'Appointment Details';
$lang['email_client'] = 'Email Client';
$lang['send_reminder'] = 'Send Reminder';
$lang['appointment_send_reminder_followup'] = 'Send Reminder & Followup to Customer';
$lang['appointment_attachments'] = 'Attachments';
$lang['upload_attachments'] = 'Upload Attachments';
$lang['upload_files'] = 'Upload Files';
$lang['attachment_upload_instructions'] = 'Drag and drop files here or click to browse. You can upload multiple files at once.';
$lang['allowed_file_types'] = 'Allowed file types';
$lang['max_file_size'] = 'Maximum file size';
$lang['drop_files_here_to_upload'] = 'Drop files here to upload';
$lang['or_click_to_browse'] = 'or click to browse';
$lang['close'] = 'Close';

// Timeline activity labels for attachments (dynamic)
$lang['appointment_activity_attachment_uploaded'] = 'Attachment uploaded';
$lang['appointment_activity_attachment_deleted'] = 'Attachment deleted';

// Basic status strings
$lang['scheduled'] = 'Scheduled';
$lang['cancelled'] = 'Cancelled';
$lang['complete'] = 'Complete';

// Basic action strings
$lang['edit'] = 'Edit';
$lang['delete'] = 'Delete';

// Timeline specific
$lang['timeline_tab_title'] = 'Timeline';
$lang['timeline_activity_feed'] = 'Activity Feed';
$lang['timeline_no_activities'] = 'No activities found for this appointment';
$lang['timeline_loading'] = 'Loading timeline...';

// New timeline format strings
$lang['timeline_format_icon_name_action'] = '%s %s - %s';

// Activity action labels for timeline
$lang['timeline_action_created'] = 'CREATED';
$lang['timeline_action_appointment_created'] = 'APPOINTMENT CREATED';
$lang['timeline_action_updated'] = 'UPDATED';
$lang['timeline_action_appointment_updated'] = 'APPOINTMENT UPDATED';
$lang['timeline_action_status_changed'] = 'STATUS CHANGED';
$lang['timeline_action_measurement_added'] = 'MEASUREMENT ADDED';
$lang['timeline_action_measurement_updated'] = 'MEASUREMENT UPDATED';
$lang['timeline_action_measurement_removed'] = 'MEASUREMENT REMOVED';
$lang['timeline_action_note_added'] = 'NOTE ADDED';
$lang['timeline_action_note_updated'] = 'NOTE UPDATED';
$lang['timeline_action_note_removed'] = 'NOTE REMOVED';
$lang['timeline_action_attachment_uploaded'] = 'ATTACHMENT UPLOADED';
$lang['timeline_action_attachment_removed'] = 'ATTACHMENT REMOVED';
$lang['timeline_action_proposal_created'] = 'PROPOSAL CREATED';
$lang['timeline_action_proposal_updated'] = 'PROPOSAL UPDATED';
$lang['timeline_action_proposal_deleted'] = 'PROPOSAL DELETED';
$lang['timeline_action_process_completed'] = 'PROCESS COMPLETED';
$lang['timeline_action_process_failed'] = 'PROCESS FAILED';
$lang['timeline_action_deleted'] = 'DELETED';
$lang['timeline_action_appointment_deleted'] = 'APPOINTMENT DELETED';

// Legacy activity descriptions (kept for backward compatibility)
$lang['appointment_created_with_details'] = 'Appointment "%s" was created for %s at %s';
$lang['appointment_updated_with_details'] = 'Appointment "%s" was updated';
$lang['appointment_status_changed_from_to'] = 'Status changed from "%s" to "%s"';
$lang['note_added_to_appointment'] = 'Note added: %s';
$lang['measurement_added_to_appointment'] = 'Measurement added: %s';
$lang['measurement_removed_from_appointment'] = 'Measurement removed: %s';
$lang['scheduled_process_completed'] = 'Scheduled process "%s" completed';
$lang['scheduled_process_failed'] = 'Scheduled process "%s" failed';
$lang['appointment_deleted_with_subject'] = 'Appointment "%s" was deleted';


//
$lang['appointment_start_datetime'] = 'Appointment Start Date & Time';
$lang['appointment_end_datetime'] = 'Appointment End Date & Time';
$lang['basic_information'] = 'Basic Information';
$lang['contact_information'] = 'Contact Information';
$lang['estimate_line_items'] = 'Estimate Line Items';
$lang['select_line_item'] = 'Select Line Item';
$lang['unit_price'] = 'Unit Price';
$lang['total_price'] = 'Total Price';
$lang['confirm_delete_appointment'] = 'Confirm Delete Appointment';
$lang['email'] = 'Email';
$lang['phone'] = 'Phone';
$lang['attendees'] = 'Attendees';
$lang['appointment_subject'] = 'Appointment Subject';
$lang['appointment_description'] = 'Appointment Description';
$lang['appointment_status'] = 'Appointment Status';
$lang['appointment_created_by'] = 'Appointment Created By';
$lang['back_to_appointments'] = 'Back to Appointments';

// Status translations
$lang['appointment_status_scheduled'] = 'Scheduled';
$lang['appointment_status_cancelled'] = 'Cancelled';
$lang['appointment_status_complete'] = 'Complete';

// Time formatting
$lang['time_ago_just_now'] = 'Just now';
$lang['time_ago_minutes'] = '%d minutes ago';
$lang['time_ago_hours'] = '%d hours ago';
$lang['time_ago_days'] = '%d days ago';
$lang['time_ago_weeks'] = '%d weeks ago';
$lang['time_ago_months'] = '%d months ago';
$lang['time_ago_years'] = '%d years ago';

// ==================== EMAIL TEMPLATES (NEW - My Reminder Feature) ====================

/**
 * Client Appointment Reminder Email Template
 * Available merge fields: {appointment_subject}, {appointment_date}, {appointment_time}, 
 * {appointment_location}, {appointment_notes}, {client_name}, {company_name}, {company_phone}
 */
$lang['client_appointment_reminder'] = '<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); padding: 30px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 28px;">Appointment Confirmation</h1>
        </div>
        <div style="padding: 40px;">
            <p style="margin: 0 0 20px; font-size: 16px; color: #333;">Dear <strong>{client_name}</strong>,</p>
            <p style="margin: 0 0 25px; font-size: 16px; color: #333;">This is a confirmation of your upcoming appointment:</p>
            <table style="width: 100%; background-color: #f8f9fa; border-radius: 6px; margin-bottom: 25px; border: 1px solid #e9ecef;">
                <tr><td style="padding: 20px;">
                    <table style="width: 100%;">
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Appointment:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{appointment_subject}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Date:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{appointment_date}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Time:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{appointment_time}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0;">Location:</td>
                            <td style="padding: 8px 0;">{appointment_location}</td></tr>
                    </table>
                </td></tr>
            </table>
            <p style="margin: 0 0 20px; font-size: 16px; color: #333;">A calendar invitation is attached to this email.</p>
            <p style="margin: 0 0 25px; font-size: 16px; color: #333;">If you need to reschedule, please contact us at <strong>{company_phone}</strong>.</p>
        </div>
        <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; font-size: 14px; color: #6c757d;">Best regards,<br><strong>{company_name}</strong></p>
        </div>
    </div>
</body></html>';

/**
 * Staff Appointment Reminder Email Template
 * Available merge fields: {appointment_subject}, {appointment_date}, {appointment_time},
 * {client_name}, {staff_name}, {company_name}, {crm_link}
 */
$lang['staff_appointment_reminder'] = '<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); padding: 30px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 28px;">Appointment Reminder</h1>
        </div>
        <div style="padding: 40px;">
            <p style="margin: 0 0 20px; font-size: 16px; color: #333;">Hi <strong>{staff_name}</strong>,</p>
            <p style="margin: 0 0 25px; font-size: 16px; color: #333;">This is a reminder about your upcoming appointment:</p>
            <table style="width: 100%; background-color: #f8f9fa; border-radius: 6px; margin-bottom: 25px; border: 1px solid #e9ecef;">
                <tr><td style="padding: 20px;">
                    <table style="width: 100%;">
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Appointment:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{appointment_subject}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Client/Lead:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{client_name}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Date:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{appointment_date}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6;">Time:</td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6;">{appointment_time}</td></tr>
                        <tr><td style="font-weight: bold; padding: 8px 0;">Location:</td>
                            <td style="padding: 8px 0;">{appointment_location}</td></tr>
                    </table>
                </td></tr>
            </table>
            <div style="text-align: center; margin-bottom: 25px;">
                <a href="{crm_link}" style="display: inline-block; padding: 14px 30px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">View in CRM</a>
            </div>
            <p style="margin: 0 0 20px; font-size: 16px; color: #333;">A calendar invitation is attached to this email.</p>
        </div>
        <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; font-size: 14px; color: #6c757d;">This is an automated reminder from<br><strong>{company_name} CRM</strong></p>
        </div>
    </div>
</body></html>';