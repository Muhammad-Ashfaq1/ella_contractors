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

// Timeline specific
$lang['timeline_tab_title'] = 'Timeline';
$lang['timeline_activity_feed'] = 'Activity Feed';
$lang['timeline_no_activities'] = 'No activities found for this appointment';
$lang['timeline_loading'] = 'Loading timeline...';

// New timeline format strings
$lang['timeline_format_icon_name_action'] = '%s %s - %s';

// Activity action labels for timeline
$lang['timeline_action_created'] = 'CREATED';
$lang['timeline_action_updated'] = 'UPDATED';
$lang['timeline_action_status_changed'] = 'STATUS CHANGED';
$lang['timeline_action_measurement_added'] = 'MEASUREMENT ADDED';
$lang['timeline_action_measurement_removed'] = 'MEASUREMENT REMOVED';
$lang['timeline_action_note_added'] = 'NOTE ADDED';
$lang['timeline_action_process_completed'] = 'PROCESS COMPLETED';
$lang['timeline_action_process_failed'] = 'PROCESS FAILED';
$lang['timeline_action_deleted'] = 'DELETED';

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