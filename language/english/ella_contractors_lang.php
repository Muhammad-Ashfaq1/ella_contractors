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

// Reminder Templates
$lang['reminder_templates'] = 'Reminder Templates';
$lang['reminder_template'] = 'Reminder Template';
$lang['new_reminder_template'] = 'New Reminder Template';
$lang['edit_reminder_template'] = 'Edit Reminder Template';
$lang['template_name'] = 'Template Name';
$lang['template_type'] = 'Template Type';
$lang['reminder_stage'] = 'Reminder Stage';
$lang['email_subject'] = 'Email Subject';
$lang['message_content'] = 'Message Content';
$lang['template_active'] = 'Active';
$lang['template_inactive'] = 'Inactive';
$lang['client_instant'] = 'Client Instant';
$lang['client_48h'] = 'Client 48 Hours';
$lang['staff_48h'] = 'Staff 48 Hours';
$lang['template_preview'] = 'Template Preview';
$lang['available_placeholders'] = 'Available Placeholders';
$lang['template_saved_successfully'] = 'Template saved successfully';
$lang['template_deleted_successfully'] = 'Template deleted successfully';
$lang['template_status_updated'] = 'Template status updated';
$lang['template_already_exists'] = 'A template for this type and stage already exists';
$lang['fill_all_required_fields'] = 'Please fill in all required fields';
$lang['email_subject_required'] = 'Email subject is required for email templates';