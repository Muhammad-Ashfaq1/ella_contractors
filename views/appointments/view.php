<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- Load Tutorial CSS -->
<link rel="stylesheet" href="<?php echo module_dir_url('ella_contractors', 'assets/css/appointment-tutorial.css'); ?>">

<style>
.connected-buttons {
    display: inline-flex;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 10px;
    margin-bottom: 10px;
}

.connected-buttons .connected-btn-left {
    border-radius: 4px 0 0 4px;
    border-right: none;
    margin-right: 0;
}

.connected-buttons .connected-btn-middle {
    border-radius: 0;
    border-left: none;
    border-right: none;
    margin-left: 0;
    margin-right: 0;
}

.connected-buttons .connected-btn-right {
    border-radius: 0 4px 4px 0;
    border-left: none;
    margin-left: 0;
}

.connected-buttons .btn {
    position: relative;
    z-index: 1;
    white-space: nowrap;
}

.connected-buttons .btn:hover {
    z-index: 2;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.connected-buttons .btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Ensure proper spacing and prevent layout issues */
.action-buttons-container {
    margin-top: 15px;
    margin-bottom: 15px;
    clear: both;
}

/* Dropzone styles - exactly like leads */
.drop-zone {
  max-width: 100%;
  min-height: 150px;
  height: auto;
  padding: 25px;
  display: inline-flex;
  width: 98%;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-family: "Quicksand", sans-serif;
  font-weight: 500;
  font-size: 20px;
  cursor: pointer;
  color: #cccccc;
  border: 4px dashed #009578;
  border-radius: 10px;
  margin-top: 10px;
  margin-left: 0px;
  float: left;
  position: relative;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.drop-zone--over {
  border-style: solid;
}

.drop-zone__input {
  display: none;
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
    background: red;
    color: #fff;
    border: none;
}

.drop-zone__input{
    display: none !important;
}

.removeimage{
    position: absolute;
    top: 8px;
    right: -10px;
    background: red;
    color: #fff;
    border-radius: 100%;
    width: 30px;
    height: 30px;
    z-index: 99999999;
    text-align: center;
}

.addmorebtn{
  background-color: #3ac529;
    color: #fff;
    padding: 2px 10px;
    float: right;
    border-radius: 7px;
    width: 100px;
    text-align: center;
}

.lead_send_sms{
    width: 190px;
}

.upload-message{
    color: red;
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

.lead-email-activity .media-body{
  margin-top: 20px;
    position: relative;
}

.lead-email-activity .email_head_area{
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}

.lead-email-activity .email_body_area{
    background: #fff;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    margin-bottom: 10px;
}

.lead-email-activity .email_footer_area{
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    font-size: 12px;
    color: #6c757d;
}

/* Prevent scroll jumps and layout shifts */
html {
    scroll-behavior: auto !important;
}

.tab-content {
    min-height: 300px;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

/* Prevent Bootstrap tab transitions from causing scroll issues */
.tab-content > .tab-pane {
    display: none;
    visibility: hidden;
}

.tab-content > .active {
    display: block;
    visibility: visible;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('ella_contractors/appointments'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('back_to_appointments'); ?>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-info" onclick="editAppointment(<?php echo $appointment->id; ?>)">
                                <i class="fa fa-edit"></i> <?php echo _l('edit'); ?>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteAppointment(<?php echo $appointment->id; ?>)">
                                <i class="fa fa-trash"></i> <?php echo _l('delete'); ?>
                            </a>
                            <button type="button" class="btn btn-default" id="restart-view-tutorial" style="margin-left: 10px;" data-toggle="tooltip" data-placement="top" title="Restart Tutorial">
                                <i class="fa fa-question-circle"></i> Help
                            </button>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="no-margin appointment-subject" title="<?php echo htmlspecialchars($appointment->subject); ?>">
                                    <?php echo '<span class="text-muted">' . '(' . $appointment->id . ')' . '</span>'; ?> <?php echo $appointment->subject; ?>
                                </h3>
                                <p class="text-muted">
                                    <?php 
                                    // Format: Monday | September 15th, 2025 | 2hr 30m
                                    $date_obj = DateTime::createFromFormat('Y-m-d', $appointment->date);
                                    if ($date_obj) {
                                        // Get day of week
                                        echo $date_obj->format('l') . ' | ';
                                        // Get formatted date
                                        echo $date_obj->format('F jS, Y');
                                    } else {
                                        echo _d($appointment->date);
                                    }
                                    
                                    // Calculate and display duration
                                    if (!empty($appointment->start_hour) && !empty($appointment->end_time)) {
                                        $start_time = DateTime::createFromFormat('H:i:s', $appointment->start_hour);
                                        if (!$start_time) {
                                            $start_time = DateTime::createFromFormat('H:i', $appointment->start_hour);
                                        }
                                        
                                        $end_time = DateTime::createFromFormat('H:i:s', $appointment->end_time);
                                        if (!$end_time) {
                                            $end_time = DateTime::createFromFormat('H:i', $appointment->end_time);
                                        }
                                        
                                        if ($start_time && $end_time) {
                                            $interval = $start_time->diff($end_time);
                                            $hours = $interval->h + ($interval->days * 24);
                                            $minutes = $interval->i;
                                            
                                            echo ' | Duration: ';
                                            if ($hours > 0) {
                                                echo $hours . 'hr';
                                            }
                                            if ($minutes > 0) {
                                                if ($hours > 0) echo ' ';
                                                echo $minutes . 'm';
                                            }
                                        }
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <!-- Status Display -->
                                <div class="status-display">
                                    <?php 
                                    $status = isset($appointment->appointment_status) ? $appointment->appointment_status : 'scheduled';
                                    $status_class = '';
                                    $status_label = '';
                                    
                                    switch ($status) {
                                        case 'cancelled':
                                            $status_class = 'label-danger';
                                            $status_label = 'CANCELLED';
                                            break;
                                        case 'complete':
                                            $status_class = 'label-success';
                                            $status_label = 'COMPLETE';
                                            break;
                                        case 'scheduled':
                                            $status_class = 'label-info';
                                            $status_label = 'SCHEDULED';
                                            break;
                                        default:
                                            $status_class = 'label-warning';
                                            $status_label = strtoupper($status);
                                    }
                                    ?>
                                    <span class="label <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Lead Information and Attendees Section -->
                        <div class="row">
                            <!-- Left Column: Lead Information -->
                            <div class="col-md-6">
                                <h5>Lead Information</h5>
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('client'); ?>:</strong></td>
                                        <td>
                                           
                                            <?php if($appointment->lead_name): ?>
                                                <?php echo $appointment->lead_name; ?>
                                            <?php else: ?>
                                                <?php echo $appointment->name; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    
                                    <?php if($appointment->email): ?>
                                    <tr>
                                        <td><strong><?php echo _l('email'); ?>:</strong></td>
                                        <td><?php echo $appointment->email; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if($appointment->phone): ?>
                                    <tr>
                                        <td><strong><?php echo _l('phone'); ?>:</strong></td>
                                        <td><?php echo $appointment->phone; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if($appointment->address): ?>
                                    <tr>
                                        <td><strong><?php echo _l('address'); ?>:</strong></td>
                                        <td><?php echo $appointment->address; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>

                                <!-- Attendees Section -->
                                <h5>
                                    <?php echo _l('attendees'); ?>
                                    <button class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; margin-left: 10px;" onclick="editAppointment(<?php echo $appointment->id; ?>)" title="Edit Attendees">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </h5>
                                <div id="attendees-container">
                                    <?php if(!empty($attendees)): ?>
                                        <ul class="list-unstyled">
                                            <?php foreach($attendees as $attendee): ?>
                                                <li><i class="fa fa-user"></i> <?php echo $attendee['name']; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted">No attendees assigned</p>
                                    <?php endif; ?>
                                </div>

                                
                                <!-- Action Buttons -->
                                
                    
                            </div>
                            
                            <!-- Right Column: Attendees, Presentations, and Reminders -->
                            <div class="col-md-6">
                            <div class="action-buttons-container text-right" style="margin-top: 20px;">
                                    <div class="btn-group connected-buttons" role="group">
                                        <?php if (!empty($appointment->contact_id) && !empty($appointment->phone)): ?>
                                            <a href="javascript:void(0)" class="btn btn-success connected-btn-left" onclick="openSMSModal(<?php echo $appointment->contact_id; ?>, '<?php echo $appointment->phone; ?>')">
                                                <i class="fa fa-comment"></i> Send SMS
                                            </a>
                                         <?php endif; ?>
                                        <a href="mailto:<?php echo $appointment->email; ?>" class="btn btn-primary btn-sm <?php echo (!empty($appointment->contact_id) && !empty($appointment->phone)) ? 'connected-btn-right' : 'connected-btn-left connected-btn-right'; ?>" title="<?php echo _l('email_client'); ?>" target="_blank" onclick="logEmailClick(<?php echo $appointment->id; ?>, '<?php echo $appointment->email; ?>')">
                                            <i class="fa fa-envelope"></i> <?php echo _l('email_client'); ?>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Attach Presentation Section -->
                                <div style="margin-top: 20px;">
                                    <h5>
                                        Attach Presentation
                                        <a href="<?php echo admin_url('ella_contractors/presentations'); ?>" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 3px 5px; border-radius: 3px; display: inline-flex; align-items: center; justify-content: center; margin-left: 6px; text-decoration: none;" title="Manage Presentations">
                                            <i class="fa fa-cog" style="font-size: 12px;"></i>
                                        </a>
                                    </h5>
                                    <div id="attached-presentations-container">
                                        <!-- Presentations will be loaded here -->
                                        <p class="text-muted">Loading presentations...</p>
                                    </div>
                                    <button class="btn btn-info btn-sm" style="margin-top: 10px;" onclick="openAttachPresentationModal()">
                                        <i class="fa fa-paperclip"></i> Attach Presentation
                                    </button>
                                </div>
                                
                                <!-- Reminder Settings Section -->
                                <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
                                    <h5 style="margin-top: 0;">Reminder Settings</h5>
                                    
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h6 style="margin-top: 15px; margin-bottom: 10px; font-weight: bold;">Client Reminders</h6>
                                            
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" 
                                                       id="send_reminder_toggle" 
                                                       data-appointment-id="<?php echo $appointment->id; ?>"
                                                       data-field="send_reminder"
                                                       <?php echo (isset($appointment->send_reminder) && $appointment->send_reminder == 1) ? 'checked' : ''; ?>>
                                                <label for="send_reminder_toggle">
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
                                                <input type="checkbox" 
                                                       id="reminder_48h_toggle" 
                                                       data-appointment-id="<?php echo $appointment->id; ?>"
                                                       data-field="reminder_48h"
                                                       <?php echo (isset($appointment->reminder_48h) && $appointment->reminder_48h == 1) ? 'checked' : ''; ?>>
                                                <label for="reminder_48h_toggle">
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
                                                <input type="checkbox" 
                                                       id="reminder_same_day_toggle" 
                                                       data-appointment-id="<?php echo $appointment->id; ?>"
                                                       data-field="reminder_same_day"
                                                       <?php echo (isset($appointment->reminder_same_day) && $appointment->reminder_same_day == 1) ? 'checked' : ''; ?>>
                                                <label for="reminder_same_day_toggle">
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
                                            <h6 style="margin-top: 15px; margin-bottom: 10px; font-weight: bold;">My Reminder</h6>
                                            
                                            <div class="checkbox checkbox-success">
                                                <input type="checkbox" 
                                                       id="staff_reminder_48h_toggle" 
                                                       data-appointment-id="<?php echo $appointment->id; ?>"
                                                       data-field="staff_reminder_48h"
                                                       <?php echo (isset($appointment->staff_reminder_48h) && $appointment->staff_reminder_48h == 1) ? 'checked' : ''; ?>>
                                                <label for="staff_reminder_48h_toggle">
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
                                                <input type="checkbox" 
                                                       id="staff_reminder_same_day_toggle" 
                                                       data-appointment-id="<?php echo $appointment->id; ?>"
                                                       data-field="staff_reminder_same_day"
                                                       <?php echo (isset($appointment->staff_reminder_same_day) && $appointment->staff_reminder_same_day == 1) ? 'checked' : ''; ?>>
                                                <label for="staff_reminder_same_day_toggle">
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
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#measurements-tab" aria-controls="measurements-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-square-o"></i> Measurements
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#estimates-tab" aria-controls="estimates-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> Estimates
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#notes-tab" aria-controls="notes-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-sticky-note-o"></i> Notes
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#attachments-tab" aria-controls="attachments-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-paperclip"></i> Attachments
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#timeline-tab" aria-controls="timeline-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-history"></i> Timeline
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Measurements Tab -->
                            <div role="tabpanel" class="tab-pane active" id="measurements-tab">
                                <?php $this->load->view('appointments/measurements/measurements_list'); ?>
                            </div>

                            <!-- Estimates Tab -->
                            <div role="tabpanel" class="tab-pane" id="estimates-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right mbot15">
                                            <?php if (!empty($appointment->contact_id)): ?>
                                                <a href="<?php echo admin_url('proposals/proposal?rel_type=lead&rel_id=' . $appointment->contact_id . '&create_estimates=true&appt_id=' . $appointment->id); ?>" class="btn btn-info btn-sm">
                                                    <i class="fa fa-plus"></i> New Estimate
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                        <hr class="hr-panel-heading" />
                                        
                                        <div id="estimates-container">
                                            <?php $this->load->view('appointments/estimates/listing'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div role="tabpanel" class="tab-pane" id="notes-tab">
                                <?php $this->load->view('appointments/notes/notes'); ?>
                            </div>

                            <!-- Attachments Tab -->
                            <div role="tabpanel" class="tab-pane" id="attachments-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr class="hr-panel-heading" />
                                        
                                        <?php if (has_permission('ella_contractors', '', 'edit')) { ?>
                                        <div class="clearfix mbot15">
                                            <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#attachmentUploadModal">
                                                <i class="fa fa-upload"></i> <?php echo _l('upload_files'); ?>
                                            </button>
                                        </div>
                                        <?php } ?>
                                        
                                        <div id="attachments-container">
                                            <!-- Attachments will be loaded here via AJAX -->
                                            <div class="text-center">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading attachments...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Tab -->
                            <div role="tabpanel" class="tab-pane" id="timeline-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        
                                        <hr class="hr-panel-heading" />
                                        
                                        <div id="timeline-container">
                                            <!-- Timeline will be loaded here via AJAX -->
                                            <div class="text-center">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading timeline...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Measurement Modal -->
<?php $this->load->view('appointments/measurements/measurements_modal'); ?>

<!-- Include Attachment Upload Modal -->
<?php $this->load->view('appointments/attachments_upload_modal'); ?>

<!-- File Preview Modal -->
<div class="modal fade" id="attachmentPreviewModal" tabindex="-1" role="dialog" aria-labelledby="attachmentPreviewModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="attachmentPreviewModalLabel">File Preview</h4>
            </div>
            <div class="modal-body">
                <div id="attachmentPreviewContent" style="min-height: 400px;">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="#" id="downloadAttachmentBtn" class="btn btn-primary" target="_blank">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<!-- Timeline CSS -->
<link rel="stylesheet" href="<?php echo module_dir_url('ella_contractors', 'assets/css/timeline.css'); ?>">

<!-- Include centralized appointment attendees functionality -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-attendees.js'); ?>"></script>

<!-- Define global variables BEFORE loading other JavaScript files -->
<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
</script>

<!-- Include Measurement Modal and JavaScript -->
<?php $this->load->view('appointments/measurements/measurements_js'); ?>

<script>

$(document).ready(function() {
    // Prevent hash-based scrolling that causes UI blinking
    if (window.location.hash) {
        // Remove hash without triggering scroll
        history.replaceState(null, null, ' ');
    }
    
    // Prevent default Bootstrap tab behavior that causes scroll jumps
    $('a[data-toggle="tab"]').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
        return false;
    });
    
    // Enhanced tab management system
    var currentActiveTab = 'measurements'; // Default tab
    
    // Make tabsLoaded globally accessible so it can be accessed by other functions
    window.tabsLoaded = {
        measurements: false,
        estimates: false,
        notes: false,
        attachments: false,
        timeline: false
    };
    
    // Single unified function to load tab data (handles both initial load and tab switching)
    window.loadTabData = function(tabName, forceReload = false) {
        // Skip if already loaded and not forcing reload
        if (window.tabsLoaded[tabName] && !forceReload) {
            return;
        }
        
        switch(tabName) {
            case 'measurements':
                if (typeof loadMeasurements === 'function') {
                    loadMeasurements();
                }
                break;
            case 'estimates':
                if (typeof loadEstimates === 'function') {
                    loadEstimates();
                }
                break;
            case 'notes':
                if (typeof loadNotes === 'function') {
                    loadNotes();
                }
                break;
            case 'attachments':
                if (typeof loadAttachments === 'function') {
                    loadAttachments(true);
                }
                break;
            case 'timeline':
                if (typeof loadTimeline === 'function') {
                    loadTimeline();
                }
                break;
        }
        
        // Mark as loaded
        window.tabsLoaded[tabName] = true;
    };
    
    // Get initial tab from URL or default to measurements
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var initialTab = tabParam || 'measurements';
    
    // Load initial tab data
    window.loadTabData(initialTab);
    
    // Function to switch tabs and update URL
    function switchToTab(tabName, updateUrl = true) {
        var tabSelector = 'a[href="#' + tabName + '-tab"]';
        $(tabSelector).tab('show');
        currentActiveTab = tabName;
        
        if (updateUrl) {
            // Update URL without page reload (no hash, only search params)
            var url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            url.hash = ''; // Clear any hash
            window.history.replaceState({}, '', url);
        }
    }
    
    // Check for tab parameter in URL and activate appropriate tab
    if (tabParam && tabParam !== 'measurements') {
        currentActiveTab = tabParam;
        
        // Activate the correct tab visually
        $('.nav-tabs li').removeClass('active');
        $('.nav-tabs a[href="#' + tabParam + '-tab"]').parent('li').addClass('active');
        
        // Activate the correct tab pane
        $('.tab-pane').removeClass('active in');
        $('#' + tabParam + '-tab').addClass('active in');
    }
    
    // Track tab changes and update URL - ONLY for main page tabs, not modal tabs
    $('.nav-tabs a[data-toggle="tab"]').not('.modal a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Double-check: Ignore tab events from modals (e.g., measurement modal internal tabs)
        if ($(e.target).closest('.modal').length > 0) {
            return;
        }
        
        var target = $(e.target).attr('href');
        
        // Only handle main page tabs (those ending with -tab)
        if (!target || !target.includes('-tab') || target.includes('measurement_tab')) {
            return;
        }
        
        var tabName = target.replace('#', '').replace('-tab', '');
        currentActiveTab = tabName;
        
        // Update URL without page reload (no hash)
        var url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        url.hash = ''; // Clear any hash fragments
        window.history.replaceState({}, '', url);
        
        // Load data for the tab using unified function
        window.loadTabData(tabName);
    });
    
    // Note: Tab data loading is handled by shown.bs.tab event above
    // No need for separate click handlers as they cause double-loading
    
    // Log email button click
    window.logEmailClick = function(appointmentId, emailAddress) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/log_email_click',
            type: 'POST',
            data: {
                appointment_id: appointmentId,
                email_address: emailAddress,
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json'
        });
    };
});

// Global functions for modal operations
function editAppointment(appointmentId) {
    // Open modal for editing
    if (typeof openAppointmentModal === 'function') {
        openAppointmentModal(appointmentId);
    } else {
        // Fallback: redirect to main page with edit parameter
        window.location.href = admin_url + 'ella_contractors/appointments?edit=' + appointmentId;
    }
}

// Global function to reload measurements only if on measurements tab (optimized)
window.reloadMeasurementsIfActive = function() {
    // Check which tab is currently active
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var currentTab = tabParam || 'measurements';
    
    // Only reload if we're on measurements tab
    if (currentTab === 'measurements') {
        loadMeasurements();
        
        // Mark as loaded
        if (typeof window.tabsLoaded !== 'undefined') {
            window.tabsLoaded.measurements = true;
        }
    } else {
        // Mark measurements as needing reload when user switches back to it
        if (typeof window.tabsLoaded !== 'undefined') {
            window.tabsLoaded.measurements = false;
        }
    }
};

// Global function to ensure only the correct tab is visible based on URL parameter (optimized to prevent scroll issues)
window.ensureCorrectTabVisible = function() {
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var currentTab = tabParam || 'measurements';
    
    // Only update if not already correct (prevents unnecessary reflows)
    var targetTabPane = $('#' + currentTab + '-tab');
    if (targetTabPane.length > 0 && !targetTabPane.hasClass('active')) {
        // Hide all tab panes first
        $('.tab-pane').removeClass('active in');
        targetTabPane.addClass('active in');
        
        // Update nav tabs active state
        $('.nav-tabs li').removeClass('active');
        $('.nav-tabs a[href="#' + currentTab + '-tab"]').parent('li').addClass('active');
    }
};

// Timeline Functions
function loadTimeline() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    if (!appointmentId) {
        $('#timeline-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Invalid appointment ID.</p></div>');
        return;
    }
    
    $('#timeline-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading timeline...</p></div>');
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_timeline/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'html',
        success: function(response) {
            $('#timeline-container').html(response);
        },
        error: function(xhr, status, error) {
            $('#timeline-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading timeline. Please try again.</p></div>');
        }
    });
}

// Global function to refresh all data and maintain current tab
function refreshAppointmentData(activeTab = null) {
    // Mark all tabs as needing reload
    if (typeof window.tabsLoaded !== 'undefined') {
        window.tabsLoaded.measurements = false;
        window.tabsLoaded.estimates = false;
        window.tabsLoaded.notes = false;
        window.tabsLoaded.attachments = false;
        window.tabsLoaded.timeline = false;
    }
    
    var targetTab = activeTab || currentActiveTab || 'measurements';
    
    // Switch to tab if specified
    if (activeTab) {
        switchToTab(activeTab);
    }
    
    // Use unified function to load data with force reload
    window.loadTabData(targetTab, true);
}

// Attendees functionality is now handled by the centralized appointment-attendees.js file

// Attendees Display Refresh Function
function loadAttendeesDisplay(appointmentId) {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_attendees/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var attendeesHtml = '';
                if (response.data && response.data.length > 0) {
                    attendeesHtml = '<ul class="list-unstyled">';
                    response.data.forEach(function(attendee) {
                        attendeesHtml += `<li><i class="fa fa-user"></i> ${attendee.name}</li>`;
                    });
                    attendeesHtml += '</ul>';
                } else {
                    attendeesHtml = '<p class="text-muted">No attendees assigned</p>';
                }
                $('#attendees-container').html(attendeesHtml);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading attendees display:', error);
        }
    });
}

// Function to open reminder template modal
function openReminderTemplateModal(reminderStage, templateType, recipientType, appointmentId) {
    appointmentId = appointmentId || <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    // Show loading
    $('#reminderTemplateModal').modal('show');
    $('#template_content').val('Loading...');
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_reminder_template_preview',
        type: 'POST',
        data: {
            reminder_stage: reminderStage,
            template_type: templateType,
            recipient_type: recipientType,
            appointment_id: appointmentId,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.template) {
                var template = response.template;
                $('#template_id').val(template.id);
                $('#template_name').val(template.name);
                $('#template_subject').val(template.subject || '');
                $('#template_content').val(template.content);
                $('#template_reminder_stage').val(reminderStage);
                $('#template_type').val(templateType);
                $('#template_recipient_type').val(recipientType);
                
                // Update reminder type display
                var reminderTypeText = '';
                if (reminderStage === 'client_instant') reminderTypeText = ' - Client Instant Confirmation';
                else if (reminderStage === 'client_48h') reminderTypeText = ' - Client 48-Hour Reminder';
                else if (reminderStage === 'client_same_day') reminderTypeText = ' - Client Same Day Reminder';
                else if (reminderStage === 'staff_48h') reminderTypeText = ' - Staff 48-Hour Reminder';
                else if (reminderStage === 'staff_same_day') reminderTypeText = ' - Staff Same Day Reminder';
                $('#template_reminder_type_display').text(reminderTypeText);
                
                // Show/hide subject field based on template type
                if (templateType === 'email') {
                    $('#template_subject_group').show();
                    // Update preview with rendered HTML
                    if (typeof updateTemplatePreview === 'function') {
                        updateTemplatePreview(template.content);
                    }
                } else {
                    $('#template_subject_group').hide();
                    $('#template_preview_container').html('<div class="alert alert-info"><strong>SMS Template:</strong> This is a text message template. The preview shows the actual message that will be sent.</div><pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; white-space: pre-wrap;">' + escapeHtml(template.content) + '</pre>');
                }
            } else {
                alert_float('danger', response.message || 'Failed to load template');
                $('#reminderTemplateModal').modal('hide');
            }
        },
        error: function() {
            alert_float('danger', 'Error loading template');
            $('#reminderTemplateModal').modal('hide');
        }
    });
}

// Handle reminder checkbox changes in real-time
$(document).ready(function() {
    // Bind change event to all reminder checkboxes
    $('[id$="_toggle"]').filter(function() {
        return $(this).data('appointment-id') !== undefined;
    }).on('change', function() {
        var $checkbox = $(this);
        var appointmentId = $checkbox.data('appointment-id');
        var field = $checkbox.data('field');
        var isChecked = $checkbox.is(':checked');
        
        if (!field) {
            console.error('Field not specified for checkbox:', $checkbox.attr('id'));
            return;
        }
        
        // Get friendly name for the reminder
        var fieldNames = {
            'send_reminder': 'Appointment Creation Notice',
            'reminder_48h': '2 Day Notice (Client)',
            'reminder_same_day': 'Same Day Reminder (Client)',
            'staff_reminder_48h': '2 Day Notice (Staff)',
            'staff_reminder_same_day': 'Same Day Reminder (Staff)'
        };
        var friendlyName = fieldNames[field] || field;
        
        // Make AJAX call to update setting
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/update_reminder_setting',
            type: 'POST',
            data: {
                appointment_id: appointmentId,
                field: field,
                value: isChecked ? 1 : 0,
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var action = isChecked ? 'enabled' : 'disabled';
                    alert_float('success', friendlyName + ' ' + action + ' successfully');
                } else {
                    alert_float('danger', response.message || 'Failed to update reminder setting');
                    // Revert checkbox state on failure
                    $checkbox.prop('checked', !isChecked);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error updating reminder setting');
                console.error('AJAX Error:', error);
                $checkbox.prop('checked', !isChecked);
            }
        });
    });
    
    // Handle reminder template preview icon clicks
    $(document).on('click', '.reminder-template-preview', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var reminderStage = $(this).data('reminder-stage');
        var templateType = $(this).data('template-type');
        var recipientType = $(this).data('recipient-type');
        
        openReminderTemplateModal(reminderStage, templateType, recipientType, appointmentId);
    });
});

// Update template preview when content changes
function updateTemplatePreview(htmlContent) {
    // Create an iframe to render the HTML safely
    var previewContainer = $('#template_preview_container');
    previewContainer.html('<iframe id="template_preview_iframe" style="width: 100%; height: 600px; border: 1px solid #ddd; background: white;"></iframe>');
    
    var iframe = document.getElementById('template_preview_iframe');
    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
    iframeDoc.open();
    iframeDoc.write(htmlContent);
    iframeDoc.close();
}

// Update preview when content changes in edit tab
$('#template_content').on('input', function() {
    if ($('#template-preview-tab').hasClass('active')) {
        var content = $(this).val();
        if ($('#template_type').val() === 'email') {
            updateTemplatePreview(content);
        }
    }
});

// Update preview when switching to preview tab
$('a[href="#template-preview-tab"]').on('shown.bs.tab', function() {
    var content = $('#template_content').val();
    if ($('#template_type').val() === 'email') {
        updateTemplatePreview(content);
    }
});

// Toggle merge fields help
$('#toggle_merge_fields_help').on('click', function() {
    $('#merge_fields_help').slideToggle();
    var btnText = $(this).find('i').hasClass('fa-question-circle') ? 
        '<i class="fa fa-times-circle"></i> Hide Available Fields' : 
        '<i class="fa fa-question-circle"></i> Show Available Fields';
    $(this).html(btnText);
});

// Helper function to escape HTML
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Save Template
$('#saveTemplateBtn').on('click', function() {
    var formData = {
        id: $('#template_id').val(),
        template_name: $('#template_name').val(),
        template_type: $('#template_type').val(),
        reminder_stage: $('#template_reminder_stage').val(),
        recipient_type: $('#template_recipient_type').val(),
        subject: $('#template_subject').val(),
        content: $('#template_content').val(),
        is_active: $('#template_is_active').is(':checked') ? 1 : 0,
        [csrf_token_name]: csrf_hash
    };
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/save_reminder_template',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#reminderTemplateModal').modal('hide');
            } else {
                alert_float('danger', response.message || 'Failed to save template');
            }
        },
        error: function() {
            alert_float('danger', 'Error saving template');
        }
    });
});

// Load Attached Presentations (local wrapper for view page - calls centralized function)
function loadAttachedPresentationsLocal() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    // Use centralized function if available
    if (typeof window.loadAttachedPresentations === 'function') {
        window.loadAttachedPresentations(appointmentId, 'attached-presentations-container');
    }
}

// Store originally attached presentation IDs for comparison
var originallyAttachedPresentationIds = [];

// Open Attach Presentation Modal (using centralized functions)
function openAttachPresentationModal() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    // Create modal dynamically with multiple selection support
    var modalHtml = `
    <div class="modal fade" id="attachPresentationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Attach Presentation(s)</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Presentation(s) <small class="text-muted">(You can select multiple)</small></label>
                        <select class="form-control selectpicker" id="attach_modal_presentation_select" 
                                data-live-search="true" 
                                data-selected-text-format="count > 2"
                                multiple>
                            <option value="">Loading presentations...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updatePresentationsForAppointment()">
                        <i class="fa fa-refresh"></i> Update Presentations
                    </button>
                </div>
            </div>
        </div>
    </div>`;
    
    // Remove existing modal if any
    $('#attachPresentationModal').remove();
    
    // Append to body
    $('body').append(modalHtml);
    
    // Load available presentations using centralized function
    if (typeof loadPresentationsForDropdown === 'function') {
        loadPresentationsForDropdown('attach_modal_presentation_select', function() {
            // After loading all presentations, load and pre-select already attached ones
            if (typeof loadAttachedPresentations === 'function') {
                loadAttachedPresentations(appointmentId, null, function(response) {
                    if (response.success && response.data && response.data.length > 0) {
                        // Store originally attached IDs for comparison
                        originallyAttachedPresentationIds = response.data.map(function(p) { return p.id.toString(); });
                        
                        // Pre-select already attached presentations
                        $('#attach_modal_presentation_select').selectpicker('val', originallyAttachedPresentationIds);
                    } else {
                        originallyAttachedPresentationIds = [];
                    }
                    
                    // Show modal after pre-selection
                    $('#attachPresentationModal').modal('show');
                    $('#attach_modal_presentation_select').selectpicker('refresh');
                });
            } else {
                // No pre-selection available, just show modal
                originallyAttachedPresentationIds = [];
                $('#attachPresentationModal').modal('show');
                $('#attach_modal_presentation_select').selectpicker('refresh');
            }
        });
    } else {
        // Fallback if centralized function not available
        originallyAttachedPresentationIds = [];
        $('#attachPresentationModal').modal('show');
        $('#attach_modal_presentation_select').selectpicker('refresh');
    }
}

// Update Presentations for Appointment (sync mode - add/remove as needed)
function updatePresentationsForAppointment() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    var selectedPresentations = $('#attach_modal_presentation_select').val() || [];
    
    // Convert to array if not already
    var currentlySelected = Array.isArray(selectedPresentations) ? selectedPresentations : [selectedPresentations];
    
    // Show loading state
    var $btn = $('#attachPresentationModal .btn-primary');
    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
    
    // Determine what to add and what to remove
    var toAttach = currentlySelected.filter(function(id) {
        return originallyAttachedPresentationIds.indexOf(id) === -1; // Not previously attached
    });
    
    var toDetach = originallyAttachedPresentationIds.filter(function(id) {
        return currentlySelected.indexOf(id) === -1; // No longer selected
    });
    
    var attachCount = 0;
    var detachCount = 0;
    
    // Step 1: Detach presentations that are no longer selected
    function detachPresentations(callback) {
        if (toDetach.length === 0) {
            callback();
            return;
        }
        
        var detachIndex = 0;
        
        function detachNext() {
            if (detachIndex >= toDetach.length) {
                callback();
                return;
            }
            
            var presentationId = toDetach[detachIndex];
            
            if (typeof window.detachPresentationFromAppointment === 'function') {
                window.detachPresentationFromAppointment(presentationId, appointmentId, null, function(response) {
                    if (response && response.success) {
                        detachCount++;
                    }
                    detachIndex++;
                    detachNext();
                });
            } else {
                detachIndex++;
                detachNext();
            }
        }
        
        detachNext();
    }
    
    // Step 2: Attach new presentations
    function attachPresentations(callback) {
        if (toAttach.length === 0) {
            callback();
            return;
        }
        
        var attachIndex = 0;
        
        function attachNext() {
            if (attachIndex >= toAttach.length) {
                callback();
                return;
            }
            
            var presentationId = toAttach[attachIndex];
            
            if (typeof attachPresentationToAppointment === 'function') {
                attachPresentationToAppointment(appointmentId, presentationId, function(response) {
                    if (response.success) {
                        attachCount++;
                    }
                    attachIndex++;
                    attachNext();
                });
            } else {
                attachIndex++;
                attachNext();
            }
        }
        
        attachNext();
    }
    
    // Execute: First detach, then attach
    detachPresentations(function() {
        attachPresentations(function() {
            // All done
            $btn.prop('disabled', false).html(originalHtml);
            
            // Simple success message
            alert_float('success', 'Presentations updated');
            
            $('#attachPresentationModal').modal('hide');
            loadAttachedPresentationsLocal(); // Reload list
        });
    });
}

// Detach Presentation from Appointment (wrapper for view page)
function detachPresentation(presentationId) {
    if (!confirm('Are you sure you want to remove this presentation?')) {
        return;
    }
    
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    // Use centralized function if available
    if (typeof window.detachPresentationFromAppointment === 'function') {
        window.detachPresentationFromAppointment(presentationId, appointmentId, 'attached-presentations-container');
    }
}

// Load presentations on page load (using centralized function)
$(document).ready(function() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    if (typeof window.loadAttachedPresentations === 'function') {
        window.loadAttachedPresentations(appointmentId, 'attached-presentations-container');
    } else {
        loadAttachedPresentationsLocal();
    }
});

// Delete appointment function for view page
function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
        // Show loading indicator
        alert_float('info', 'Deleting appointment...');
        
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_ajax',
            type: 'POST',
            data: {
                id: appointmentId,
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    // Redirect to appointments list after successful deletion
                    window.location.href = admin_url + 'ella_contractors/appointments';
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error deleting appointment: ' + error);
                console.error('Delete error:', error);
            }
        });
    }
}

</script>

<?php $this->load->view('appointments/modal'); ?>
<?php $this->load->view('appointments/sms_modal'); ?>
<?php $this->load->view('appointments/reminder_template_modal'); ?>

<script>
// Include the appointment modal functions from index.php
var admin_url = '<?php echo admin_url(); ?>';
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Custom function to initialize combined AJAX search for leads and clients
function init_combined_ajax_search(selector) {
    var ajaxSelector = $(selector);
    
    if (ajaxSelector.length) {
        var options = {
            ajax: {
                url: admin_url + 'misc/get_relation_data',
                data: function () {
                    var data = {};
                    data.type = 'lead'; // Search leads
                    data.rel_id = '';
                    data.q = '{{{q}}}';
                    data[csrf_token_name] = csrf_hash; // Add CSRF token
                    return data;
                }
            },
            locale: {
                emptyTitle: 'Search for leads...',
                statusInitialized: 'Ready to search',
                statusSearching: 'Searching...',
                statusNoResults: 'No results found',
                searchPlaceholder: 'Type to search leads...',
                currentlySelected: 'Currently selected'
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function (processData) {
                var bs_data = [];
                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        'value': 'lead_' + processData[i].id, // Add lead_ prefix
                        'text': processData[i].name + ' (Lead)',
                    };
                    if (processData[i].subtext) {
                        tmp_data.data = {
                            subtext: processData[i].subtext
                        };
                    }
                    bs_data.push(tmp_data);
                }
                return bs_data;
            },
            preserveSelectedPosition: 'after',
            preserveSelected: false
        };
        
        ajaxSelector.selectpicker().ajaxSelectPicker(options);
        ajaxSelector.selectpicker('val', '');
    }
}

// Reset appointment modal to default state
function resetAppointmentModal() {
    $('#appointmentForm')[0].reset();
    $('#appointment_id').val('');
    $('#appointmentModalLabel').text('Create Appointment');
    $('#saveAppointment').text('Create Appointment'); // Update button text for creation
    $('#contact_id').html('<option value="">Select Client/Lead</option>');
    $('#contact_id').selectpicker('val', '');
    $('.selectpicker').selectpicker('refresh');
    
    // Clear uploaded files using shared function
    clearAppointmentDropzone();
    
    // Clear presentation selections
    if (typeof $('#presentation_select').selectpicker !== 'undefined') {
        $('#presentation_select').selectpicker('deselectAll');
    }
    
    // Clear presentation preview using centralized function
    if (typeof clearPresentationSelectionPreview === 'function') {
        clearPresentationSelectionPreview();
    } else {
        $('#modal-presentation-list').html('<p style="text-align: center; color: #778485; margin: 10px 0;">None</p>');
    }
    
    // Reset reminder checkboxes to default (checked)
    $('#send_reminder').prop('checked', true);
    $('#reminder_48h').prop('checked', true);
    // Reminder channel defaults to 'both' (Email + SMS) via hidden field
    $('input[name="reminder_channel"]').val('both');
}

// Global functions for modal operations
function openAppointmentModal(appointmentId = null) {
    if ($('#appointmentForm').length === 0) {
        return;
    }
    
    // Reset form
    resetAppointmentModal();
    
    // Show modal immediately for new appointments
    if (!appointmentId) {
        $('#appointmentModal').modal('show');
    } else {
        // For editing, use the dedicated function that loads data first
        loadAppointmentDataAndShowModal(appointmentId);
    }
}

function loadAppointmentData(appointmentId) {    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_data',
        type: 'POST',
        data: {
            id: appointmentId,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                
                // Populate form fields
                $('#appointment_id').val(data.id);
                $('#subject').val(data.subject);
                
                // Handle combined datetime fields
                var startDateTime = '';
                var endDateTime = '';
                
                if (data.start_date && data.start_time) {
                    startDateTime = data.start_date + 'T' + data.start_time;
                } else if (data.date && data.start_hour) {
                    startDateTime = data.date + 'T' + data.start_hour;
                }
                
                if (data.end_date && data.end_time) {
                    endDateTime = data.end_date + 'T' + data.end_time;
                } else if (data.date && data.start_hour) {
                    // For end time, add 1 hour to start time as default
                    var endTime = new Date(data.date + 'T' + data.start_hour);
                    endTime.setHours(endTime.getHours() + 1);
                    endDateTime = endTime.toISOString().slice(0, 16);
                }
                
                $('#start_datetime').val(startDateTime);
                $('#end_datetime').val(endDateTime);
                
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#notes').val(data.notes);
                
                // Handle reminder checkboxes
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                $('#reminder_48h').prop('checked', data.reminder_48h == 1);
                // Reminder channel defaults to 'both' (Email + SMS) via hidden field
                var reminderChannel = data.reminder_channel || 'both';
                $('input[name="reminder_channel"]').val(reminderChannel);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Set appointment type (types already loaded from PHP)
                $('#type_id').val(data.type_id);
                
                // Refresh selectpicker
                $('.selectpicker').selectpicker('refresh');
                
                // Re-initialize AJAX search for contact dropdown
                $('#contact_id').off('change'); // Remove existing change handler
                init_combined_ajax_search('#contact_id.ajax-search');
                
                // Re-add the change handler
                $('#contact_id').on('change', function() {
                    var selectedValue = $(this).val();
                    if (selectedValue) {
                        var splitValue = selectedValue.split('_');
                        var relType = splitValue[0]; // 'lead' or 'client'
                        var relId = splitValue[1];   // The ID number
                        
                        if (relId) {
                            $.ajax({
                                url: admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if (data.error) {
                                        alert_float('danger', data.error);
                                        return;
                                    }
                                    
                                    // Populate form fields with the selected relation data
                                    $('#email').val(data.email || '');
                                    $('#phone').val(data.phone || '');
                                    $('#address').val(data.address || '');
                                    
                                    // Store validation status in hidden fields
                                    if (typeof data.emailValidaionStatus !== 'undefined') {
                                        $('#email_validated').val(data.emailValidaionStatus);
                                    }
                                    
                                    if (typeof data.phoneNumberValid !== 'undefined') {
                                        $('#phone_validated').val(data.phoneNumberValid);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert_float('danger', 'Error loading lead information');
                                }
                            });
                        }
                    } else {
                        // Clear form fields if no contact is selected
                        $('#email').val('');
                        $('#phone').val('');
                        $('#address').val('');
                    }
                });
                
                // Re-set the contact value after AJAX search is initialized
                if (data.contact_dropdown_value && data.contact_display_name) {
                    setTimeout(function() {
                        var contactOption = '<option value="' + data.contact_dropdown_value + '">' + data.contact_display_name + ' (' + data.contact_type + ')</option>';
                        $('#contact_id').append(contactOption);
                        $('#contact_id').selectpicker('val', data.contact_dropdown_value);
                    }, 100);
                }
                
                // Reload staff and set attendees using centralized function
                reloadStaffAndSetAttendees(data.attendees);
                
                // Update modal title and button text for editing
                $('#appointmentModalLabel').text('Edit Appointment');
                $('#saveAppointment').text('Save Appointment');
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error loading appointment data: ' + error);
        }
    });
}

function loadAppointmentDataAndShowModal(appointmentId) {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_data',
        type: 'POST',
        data: {
            id: appointmentId,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                
                // Populate form fields
                $('#appointment_id').val(data.id);
                $('#subject').val(data.subject);
                
                // Handle combined datetime fields
                var startDateTime = '';
                var endDateTime = '';
                
                if (data.start_date && data.start_time) {
                    startDateTime = data.start_date + 'T' + data.start_time;
                } else if (data.date && data.start_hour) {
                    startDateTime = data.date + 'T' + data.start_hour;
                }
                
                if (data.end_date && data.end_time) {
                    endDateTime = data.end_date + 'T' + data.end_time;
                } else if (data.date && data.start_hour) {
                    // For end time, add 1 hour to start time as default
                    var endTime = new Date(data.date + 'T' + data.start_hour);
                    endTime.setHours(endTime.getHours() + 1);
                    endDateTime = endTime.toISOString().slice(0, 16);
                }
                
                $('#start_datetime').val(startDateTime);
                $('#end_datetime').val(endDateTime);
                
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#notes').val(data.notes);
                
                // Handle reminder checkboxes
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                $('#reminder_48h').prop('checked', data.reminder_48h == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Set appointment type (types already loaded from PHP)
                $('#type_id').val(data.type_id);
                
                // Refresh selectpicker
                $('.selectpicker').selectpicker('refresh');
                
                // Re-initialize AJAX search for contact dropdown
                $('#contact_id').off('change'); // Remove existing change handler
                init_combined_ajax_search('#contact_id.ajax-search');
                
                // Re-add the change handler
                $('#contact_id').on('change', function() {
                    var selectedValue = $(this).val();
                    if (selectedValue) {
                        var splitValue = selectedValue.split('_');
                        var relType = splitValue[0]; // 'lead' or 'client'
                        var relId = splitValue[1];   // The ID number
                        
                        if (relId) {
                            $.ajax({
                                url: admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if (data.error) {
                                        alert_float('danger', data.error);
                                        return;
                                    }
                                    
                                    // Populate form fields with the selected relation data
                                    $('#email').val(data.email || '');
                                    $('#phone').val(data.phone || '');
                                    $('#address').val(data.address || '');
                                    
                                    // Store validation status in hidden fields
                                    if (typeof data.emailValidaionStatus !== 'undefined') {
                                        $('#email_validated').val(data.emailValidaionStatus);
                                    }
                                    
                                    if (typeof data.phoneNumberValid !== 'undefined') {
                                        $('#phone_validated').val(data.phoneNumberValid);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert_float('danger', 'Error loading lead information');
                                }
                            });
                        }
                    } else {
                        // Clear form fields if no contact is selected
                        $('#email').val('');
                        $('#phone').val('');
                        $('#address').val('');
                    }
                });
                
                // Re-set the contact value after AJAX search is initialized
                if (data.contact_dropdown_value && data.contact_display_name) {
                    setTimeout(function() {
                        var contactOption = '<option value="' + data.contact_dropdown_value + '">' + data.contact_display_name + ' (' + data.contact_type + ')</option>';
                        $('#contact_id').append(contactOption);
                        $('#contact_id').selectpicker('val', data.contact_dropdown_value);
                    }, 100);
                }
                
                // Reload staff and set attendees using centralized function
                reloadStaffAndSetAttendees(data.attendees);
                
                // Load presentations and attached presentations
                if (typeof loadPresentationsForDropdown === 'function') {
                    loadPresentationsForDropdown('presentation_select', function() {
                        // After presentations dropdown is loaded, initialize preview
                        if (typeof initPresentationSelectionPreview === 'function') {
                            initPresentationSelectionPreview('presentation_select', 'modal-presentation-list');
                        }
                        
                        // Load already attached presentations for this appointment
                        if (typeof loadAttachedPresentations === 'function') {
                            loadAttachedPresentations(appointmentId, null, function(response) {
                                if (response.success && response.data && response.data.length > 0) {
                                    // Update the global array with actual presentation details
                                    if (typeof selectedPresentationsInModal !== 'undefined') {
                                        selectedPresentationsInModal = response.data.map(function(p) {
                                            return {
                                                id: p.id.toString(),
                                                name: p.original_name || p.file_name,
                                                file_name: p.file_name
                                            };
                                        });
                                    }
                                    
                                    // Pre-select in dropdown
                                    var selectedIds = response.data.map(function(p) { return p.id.toString(); });
                                    $('#presentation_select').selectpicker('val', selectedIds);
                                    
                                    // Render preview
                                    if (typeof renderPresentationSelectionPreview === 'function') {
                                        renderPresentationSelectionPreview('modal-presentation-list');
                                    }
                                }
                            });
                        }
                    });
                }
                
                // Update modal title and button text for editing
                $('#appointmentModalLabel').text('Edit Appointment');
                $('#saveAppointment').text('Save Appointment');
                
                // Show modal after data is loaded
                $('#appointmentModal').modal('show');
                
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error loading appointment data: ' + error);
        }
    });
}

// Initialize modal functionality when document is ready
$(document).ready(function() {
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
    
    // Initialize attendees functionality
    initAppointmentAttendees();
    
    // Initialize AJAX search for leads and clients
    init_combined_ajax_search('#contact_id.ajax-search');
    
    // Handle contact selection and populate form fields with lead/client data
    $('#contact_id').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            var splitValue = selectedValue.split('_');
            var relType = splitValue[0]; // 'lead' or 'client'
            var relId = splitValue[1];   // The ID number
            
            if (relId) {
                $.ajax({
                    url: admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            alert_float('danger', data.error);
                            return;
                        }
                        
                        // Auto-fill Appointment Name if empty
                        if (!$('#subject').val()) {
                            var contactName = data.to || '';
                            if (contactName && contactName.trim() !== '') {
                                $('#subject').val('Appointment with ' + contactName.trim());
                            } else {
                                $('#subject').val('Appointment with ' + relType.charAt(0).toUpperCase() + relType.slice(1) + ' ID: ' + relId);
                            }
                        }
                        
                        // Populate form fields with the selected relation data
                        $('#email').val(data.email || '');
                        $('#phone').val(data.phone || '');
                        $('#address').val(data.address || '');
                        
                        // Store validation status in hidden fields
                        if (typeof data.emailValidaionStatus !== 'undefined') {
                            $('#email_validated').val(data.emailValidaionStatus);
                        }
                        
                        if (typeof data.phoneNumberValid !== 'undefined') {
                            $('#phone_validated').val(data.phoneNumberValid);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert_float('danger', 'Error loading lead information');
                    }
                });
            }
        } else {
            // Clear form fields if no contact is selected
            $('#email').val('');
            $('#phone').val('');
            $('#address').val('');
        }
    });

    // Save appointment
    $('#saveAppointment').on('click', function() {
        // Client-side validation
        if (!$('#subject').val()) {
            alert_float('danger', 'Appointment name is required');
            return;
        }
        
        if (!$('#start_datetime').val()) {
            alert_float('danger', 'Start date & time is required');
            return;
        }
        
        if (!$('#end_datetime').val()) {
            alert_float('danger', 'End date & time is required');
            return;
        }
        
        // Validate end time is greater than start time
        var startDateTimeObj = new Date($('#start_datetime').val());
        var endDateTimeObj = new Date($('#end_datetime').val());
        
        if (endDateTimeObj <= startDateTimeObj) {
            alert_float('danger', 'End date & time must be greater than start date & time');
            return;
        }
        
        if (!$('#contact_id').val()) {
            alert_float('danger', 'Please select a lead or client');
            return;
        }
        
        // Split datetime fields into separate date and time fields
        var startDateTime = $('#start_datetime').val();
        var endDateTime = $('#end_datetime').val();
        
        if (startDateTime) {
            var startParts = startDateTime.split('T');
            $('#start_date').val(startParts[0]);
            $('#start_time').val(startParts[1]);
        }
        
        if (endDateTime) {
            var endParts = endDateTime.split('T');
            $('#end_date').val(endParts[0]);
            $('#end_time').val(endParts[1]);
        }
        
        // Show loader and disable button
        var $saveBtn = $('#saveAppointment');
        var originalBtnText = $saveBtn.text();
        $saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        // Get form data
        var formData = $('#appointmentForm').serialize();
        
        // Add CSRF token
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/save_ajax',
            type: 'POST',
            data: formData + '&' + csrf_token_name + '=' + csrf_hash,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    $('#appointmentModal').modal('hide');
                    resetAppointmentModal();
                    // Refresh attendees display
                    loadAttendeesDisplay(<?php echo $appointment->id; ?>);
                    // Reload the page to show updated data
                    window.location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error saving appointment: ' + error);
            },
            complete: function() {
                // Hide loader and re-enable button
                $saveBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

});

// Activate sidebar menu for appointments view page
$(function () {
  $('.menu-item-ella_contractors').addClass('active').find('ul').addClass('in');
  $('.sub-menu-item-ella_contractors_appointments').addClass('active');
});

</script>

<!-- Include shared appointment dropzone functionality -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-dropzone.js'); ?>"></script>

<!-- Include centralized appointment presentations functionality -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-presentations.js'); ?>"></script>

<?php $this->load->view('appointments/notes/notes_js'); ?>
<?php $this->load->view('appointments/attachments_js.php'); ?>
<?php $this->load->view('appointments/sms_js.php'); ?>
<?php $this->load->view('appointments/estimates/estimates_js'); ?>

<!-- Load module CSS for SMS modal styling -->
<link rel="stylesheet" href="<?php echo module_dir_url('ella_contractors', 'assets/css/ella-contractors.css'); ?>">

<!-- Include Tutorial System for View Page -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-view-tutorial.js'); ?>"></script>

<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Restart Tutorial Button for View Page
    $('#restart-view-tutorial').on('click', function(e) {
        // Prevent event propagation to avoid sidebar closing
        e.stopPropagation();
        e.preventDefault();
        
        // Add tutorial-active class immediately to prevent sidebar from closing
        $('body').addClass('tutorial-active');
        
        // if (confirm('Would you like to restart the tutorial? This will show you step-by-step guidance on how to use the appointment details page.')) {
            // Clear preferences
            localStorage.removeItem('ella_contractors_view_tutorial_dismissed');
            localStorage.removeItem('ella_contractors_view_tutorial_completed');
            
            // Reset on server
            $.ajax({
                url: admin_url + 'ella_contractors/appointments/reset_tutorial',
                type: 'POST',
                data: {
                    tutorial_id: 'appointment_view_tutorial'
                },
                success: function() {
                    // Restart tutorial
                    if (typeof AppointmentViewTutorial !== 'undefined') {
                        AppointmentViewTutorial.restart();
                    } else {
                        // Reload page to restart tutorial
                        location.reload();
                    }
                }
            });
        // }
    });
});
</script>
