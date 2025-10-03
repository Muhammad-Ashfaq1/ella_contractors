<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

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

.typos {
    margin: 10px 0;
}

.typos ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.typos ul li {
    margin: 0;
}

.typos ul li a {
    display: inline-block;
    padding: 5px 10px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 3px;
    font-size: 12px;
    transition: background-color 0.3s;
}

.typos ul li a:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
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
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="no-margin appointment-subject" title="<?php echo htmlspecialchars($appointment->subject); ?>"><?php echo $appointment->subject; ?></h3>
                                <p class="text-muted"><?php echo _l('appointment_details'); ?></p>
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
                        
                        <!-- Action Buttons Row -->
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <div class="action-buttons-container">
                                    <div class="btn-group connected-buttons" role="group">

                                        <?php if (!empty($appointment->contact_id) && !empty($appointment->phone)): ?>
                                            <a href="javascript:void(0)" class="btn btn-success" onclick="openSMSModal(<?php echo $appointment->contact_id; ?>, '<?php echo $appointment->phone; ?>')">
                                                <i class="fa fa-comment"></i> Send SMS
                                            </a>
                                         <?php endif; ?>
                                        <a href="mailto:<?php echo $appointment->email; ?>" class="btn btn-primary btn-sm connected-btn-middle" title="<?php echo _l('email_client'); ?>" target="_blank" onclick="logEmailClick(<?php echo $appointment->id; ?>, '<?php echo $appointment->email; ?>')">
                                            <i class="fa fa-envelope"></i> <?php echo _l('email_client'); ?>
                                        </a>
                                        <a href="javascript:void(0)" onclick="sendReminderClient(<?php echo $appointment->id; ?>)" class="btn btn-warning btn-sm connected-btn-right" title="<?php echo _l('send_reminder'); ?>">
                                            <i class="fa fa-bell"></i> <?php echo _l('send_reminder'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo _l('basic_information'); ?></h5>
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('id'); ?>:</strong></td>
                                        <td><?php echo $appointment->id; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_subject'); ?>:</strong></td>
                                        <td><?php echo $appointment->subject; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_start_datetime'); ?>:</strong></td>
                                        <td>
                                            <?php 
                                            // Format date as "July 5th, 2025" to match listing format
                                            $date_obj = DateTime::createFromFormat('Y-m-d', $appointment->date);
                                            if ($date_obj) {
                                                echo $date_obj->format('F jS, Y');
                                            } else {
                                                echo _d($appointment->date);
                                            }
                                            
                                            // Add time underneath
                                            if (!empty($appointment->start_hour)) {
                                                // Format time in 12-hour format with AM/PM
                                                $time_obj = DateTime::createFromFormat('H:i:s', $appointment->start_hour);
                                                if (!$time_obj) {
                                                    $time_obj = DateTime::createFromFormat('H:i', $appointment->start_hour);
                                                }
                                                
                                                if ($time_obj) {
                                                    $time_formatted = strtolower($time_obj->format('g:ia'));
                                                } else {
                                                    // Fallback if parsing fails
                                                    $time_formatted = htmlspecialchars($appointment->start_hour);
                                                }
                                                
                                                echo '<br><small>' . $time_formatted . '</small>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_end_datetime'); ?>:</strong></td>
                                        <td>
                                            <?php 
                                            // Use end_date if available, otherwise use start date
                                            $end_date = $appointment->end_date ?? $appointment->date;
                                            $end_time = $appointment->end_hour ?? $appointment->start_hour;
                                            
                                            // Format date as "July 5th, 2025" to match listing format
                                            $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);
                                            if ($end_date_obj) {
                                                echo $end_date_obj->format('F jS, Y');
                                            } else {
                                                echo _d($end_date);
                                            }
                                            
                                            // Add time underneath
                                            if (!empty($end_time)) {
                                                // Format time in 12-hour format with AM/PM
                                                $end_time_obj = DateTime::createFromFormat('H:i:s', $end_time);
                                                if (!$end_time_obj) {
                                                    $end_time_obj = DateTime::createFromFormat('H:i', $end_time);
                                                }
                                                
                                                if ($end_time_obj) {
                                                    $end_time_formatted = strtolower($end_time_obj->format('g:ia'));
                                                } else {
                                                    // Fallback if parsing fails
                                                    $end_time_formatted = htmlspecialchars($end_time);
                                                }
                                                
                                                echo '<br><small>' . $end_time_formatted . '</small>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><?php echo _l('contact_information'); ?></h5>
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
                            </div>
                        </div>
                    
                        
                        <?php if(!empty($attendees)): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5><?php echo _l('attendees'); ?></h5>
                                <ul class="list-unstyled">
                                    <?php foreach($attendees as $attendee): ?>
                                        <li><i class="fa fa-user"></i> <?php echo $attendee['name']; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
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
                                    <span class="label label-info" style="display: none;" id="estimates-count">0</span>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right mbot15">
                                            <button type="button" class="btn btn-info btn-sm" onclick="openMeasurementModal()">
                                                <i class="fa fa-plus"></i> Add Measurement
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr class="hr-panel-heading" />
                                        
                                        <div id="measurements-container">
                                            <!-- Measurements will be loaded here via AJAX -->
                                            <div class="text-center">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading measurements...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimates Tab -->
                            <div role="tabpanel" class="tab-pane" id="estimates-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right mbot15">
                                            <button type="button" class="btn btn-info btn-sm" onclick="openEstimateModal()">
                                                <i class="fa fa-plus"></i> New Estimate
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr class="hr-panel-heading" />
                                        
                                        <div id="estimates-container">
                                            <!-- Estimates will be loaded here via AJAX -->
                                            <div class="text-center">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading estimates...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div role="tabpanel" class="tab-pane" id="notes-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Add Note Button -->
                                        <div class="pull-right mbot15">
                                            <button type="button" class="btn btn-info btn-sm" onclick="toggleNoteForm()" id="note-btn">
                                                <i class="fa fa-plus"></i> Add Note
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                        
                                        <!-- Add Note Form (Initially Hidden) -->
                                        <div id="note-form" class="hide mbot15">
                                            <?php echo form_open(admin_url('ella_contractors/appointments/add_note/' . $appointment->id), array('id' => 'appointment-notes')); ?>
                                            <div class="form-group" id="appointmentnote">
                                                <div class="lead emoji-picker-container leadnotes">
                                                    <textarea id="appointment_note_description" name="appointment_note_description" class="form-control" rows="3" data-emojiable="true" placeholder="Add a note about this appointment..."></textarea>
                                                </div>
                                            </div>
                                            <?php echo get_typos_by_category('notes'); ?>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-default btn-sm" onclick="toggleNoteForm()">Cancel</button>
                                                <button type="submit" class="btn btn-info btn-sm">Add Note</button>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                        
                                        <hr class="hr-panel-heading" />
                                        
                                        <!-- Hidden tags template for editing -->
                                        <div id="tags-template" class="hide">
                                            <?php echo get_typos_by_category('notes'); ?>
                                        </div>
                                        
                                        <!-- Notes Display -->
                                        <div class="panel_s no-shadow">
                                            <div class="panel-body">
                                                <div id="appointment-notes-container">
                                                    <!-- Notes will be loaded here via AJAX -->
                                                    <div class="text-center">
                                                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                        <p>Loading notes...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachments Tab -->
                            <div role="tabpanel" class="tab-pane" id="attachments-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <hr class="hr-panel-heading" />
                                        
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
                                        <div class="pull-right mbot15">
                                            <button type="button" class="btn btn-info btn-sm" onclick="toggleTimelineNoteForm()" id="timeline-note-btn">
                                                <i class="fa fa-plus"></i> Add Timeline
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                        
                                        <!-- Add Timeline Note Form (Initially Hidden) -->
                                        <div id="timeline-note-form-container" class="hide mbot15">
                                            <?php echo form_open(admin_url('ella_contractors/appointments/add_timeline_note/' . $appointment->id), array('id' => 'timeline-note-form')); ?>
                                            <div class="form-group" id="timeline-note-group">
                                                <div class="lead emoji-picker-container timeline-notes">
                                                    <textarea id="timeline_note_content" name="timeline_note_content" class="form-control" rows="3" data-emojiable="true" placeholder="Add a timeline entry about this appointment..."></textarea>
                                                </div>
                                            </div>
                                            <?php echo get_typos_by_category('timeline_notes'); ?>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-default btn-sm" onclick="toggleTimelineNoteForm()">Cancel</button>
                                                <button type="submit" class="btn btn-info btn-sm">Add Timeline</button>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                        
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

<!-- Measurement Modal -->
<?php $this->load->view('ella_contractors/appointments/measurement_modal'); ?>

<!-- Inline editing is used for Windows and Doors instead of nested modals -->

<?php 
// Include estimate modal data
$data['appointment'] = $appointment;
$this->load->view('appointments/estimate_modal', $data);
?>

<?php init_tail(); ?>

<!-- Load Measurements JavaScript -->
<script src="<?php echo module_dir_url('ella_contractors', 'views/appointments/measurements.js'); ?>"></script>

<!-- Timeline CSS -->
<link rel="stylesheet" href="<?php echo module_dir_url('ella_contractors', 'assets/css/timeline.css'); ?>">

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var appointmentId = <?php echo $appointment->id; ?>;

$(document).ready(function() {
    // loadMeasurements call moved to measurements.js
    
    // Load estimates when page loads
    loadEstimates();
    
    // Load notes when page loads
    loadNotes();
    
    // Check for tab parameter in URL and switch to appropriate tab
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    if (tabParam) {
        // Small delay to ensure data is loaded before switching tabs
        setTimeout(function() {
            if (tabParam === 'measurements') {
                $('a[href="#measurements-tab"]').tab('show');
            } else if (tabParam === 'estimates') {
                $('a[href="#estimates-tab"]').tab('show');
            }
        }, 500);
    }
    
    // measurementModal event handler moved to measurements.js
    
    // Reload estimates when estimate modal is closed
    $('#estimateModal').on('hidden.bs.modal', function() {
        // Small delay to ensure any pending operations complete
        setTimeout(function() {
            loadEstimates();
            // Switch to estimates tab to show updated data
            $('a[href="#estimates-tab"]').tab('show');
        }, 100);
    });
    
    // Reload estimates when estimates tab is shown
    $('a[href="#estimates-tab"]').on('click', function() {
        loadEstimates();
    });
    
    // Reload notes when notes tab is shown
    $('a[href="#notes-tab"]').on('click', function() {
        loadNotes();
    });
    
    // Reload timeline when timeline tab is shown
    $('a[href="#timeline-tab"]').on('click', function() {
        loadTimeline();
    });
    
    // Timeline note form toggle
    window.toggleTimelineNoteForm = function() {
        $('#timeline-note-form-container').toggleClass('hide');
        var btn = $('#timeline-note-btn');
        if ($('#timeline-note-form-container').hasClass('hide')) {
            btn.html('<i class="fa fa-plus"></i> Add Timeline');
        } else {
            btn.html('<i class="fa fa-minus"></i> Cancel');
        }
    };
    
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
    
    // Submit notes on appointment modal do ajax not the regular request
    $("body").on('submit', '#appointment-notes', function () {
        var form = $(this);
        var data = $(form).serialize();
        $.post(form.attr('action'), data).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                // Clear the textarea and hide form
                $("#appointment_note_description").val('');
                $(".emoji-wysiwyg-editor").text('');
                toggleNoteForm(); // Hide the form
                // Reload notes
                loadNotes();
                alert_float('success', 'Note added successfully!');
            } else {
                alert_float('danger', response.message || 'Failed to add note');
            }
        }).fail(function (data) {
            alert_float('danger', data.responseText);
        });
        return false;
    });
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

// Global function to refresh all data and switch to appropriate tab
function refreshAppointmentData(activeTab = null) {
    // Reload both measurements and estimates
    loadMeasurements();
    loadEstimates();
    
    // Switch to specified tab or stay on current tab
    if (activeTab) {
        $('a[href="#' + activeTab + '"]').tab('show');
    }
}

// Estimate Row Management Functions
var estimateRowCounter = 0;
var estimateRowCounterRoofing = 0;

function addEstimateRow(category = 'siding') {
    var containerId = category === 'roofing' ? '#estimate-rows-container-roofing' : '#estimate-rows-container';
    var prefix = category === 'roofing' ? 'roofing_' : '';
    var namePrefix = category === 'roofing' ? 'measurements_roofing' : 'measurements';
    
    if (category === 'roofing') {
        estimateRowCounterRoofing++;
        var counter = estimateRowCounterRoofing;
    } else {
        estimateRowCounter++;
        var counter = estimateRowCounter;
    }
    
    var rowHtml = '<div class="row estimate-row" data-row="' + counter + '">' +
        '<div class="col-md-4">' +
            '<div class="form-group">' +
                '<label for="measurement_name_' + prefix + counter + '">Measurement Name</label>' +
                '<input type="text" class="form-control" name="' + namePrefix + '[' + counter + '][name]" id="measurement_name_' + prefix + counter + '" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_' + prefix + counter + '">Measurement Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="' + namePrefix + '[' + counter + '][value]" id="measurement_value_' + prefix + counter + '" placeholder="0.0000">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_' + prefix + counter + '">Unit</label>' +
                '<select class="form-control" name="' + namePrefix + '[' + counter + '][unit]" id="measurement_unit_' + prefix + counter + '">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="lf">Linear Feet (lf)</option>' +
                    '<option value="ea">Each (ea)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="gal">Gallons (gal)</option>' +
                    '<option value="lb">Pounds (lb)</option>' +
                    '<option value="kg">Kilograms (kg)</option>' +
                    '<option value="ton">Tons (ton)</option>' +
                '</select>' +
            '</div>' +
        '</div>' +
        '<div class="col-md-2">' +
            '<div class="form-group">' +
                '<label>&nbsp;</label>' +
                '<div>' +
                    '<button type="button" class="btn btn-success btn-sm" onclick="addEstimateRow(\'' + category + '\')" title="Add Estimate">' +
                        '<i class="fa fa-plus"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="removeEstimateRow(this)" title="Remove Row">' +
                        '<i class="fa fa-minus"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    $(containerId).append(rowHtml);
    
    // Show remove buttons for all rows in this category if there's more than one
    var rowCount = $(containerId + ' .estimate-row').length;
    if (rowCount > 1) {
        $(containerId + ' .estimate-row .btn-danger').show();
    }
}

function removeEstimateRow(button) {
    $(button).closest('.estimate-row').remove();
    
    // Hide remove buttons if only one row left in the container
    var container = $(button).closest('[id^="estimate-rows-container"]');
    var rowCount = container.find('.estimate-row').length;
    if (rowCount <= 1) {
        container.find('.estimate-row .btn-danger').hide();
    }
}

function deleteAppointment(appointmentId) {
    if (confirm('<?php echo _l('confirm_delete_appointment'); ?>')) {
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
                    window.location.href = admin_url + 'ella_contractors/appointments';
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', '<?php echo _l('error_deleting_appointment'); ?>');
            }
        });
    }
}

// Action Button Functions

function sendEmailClient(appointmentId) {
    // Get appointment data first
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_data/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.email) {
                // Open email modal or redirect to email page
                window.open(admin_url + 'leads/send_lead_email/' + response.data.lead_id + '?email=' + response.data.email, '_blank');
            } else {
                alert_float('danger', '<?php echo _l('no_email_available'); ?>');
            }
        },
        error: function() {
            alert_float('danger', '<?php echo _l('error_loading_appointment_data'); ?>');
        }
    });
}

function sendReminderClient(appointmentId) {
    if (confirm('<?php echo _l('confirm_send_reminder'); ?>')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/send_reminder_ajax',
            type: 'POST',
            data: {
                id: appointmentId,
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', '<?php echo _l('error_sending_reminder'); ?>');
            }
        });
    }
}

function printAppointment(appointmentId) {
    // Open print view in new window
    window.open(admin_url + 'ella_contractors/appointments/print/' + appointmentId, '_blank');
}

// Measurements Functions - moved to measurements.js

// Estimates Functions
function loadEstimates() {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_estimates/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayEstimates(response.data);
            } else {
                $('#estimates-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No estimates found for this appointment.</p></div>');
            }
        },
        error: function() {
            $('#estimates-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading estimates.</p></div>');
        }
    });
}

// Notes Functions
function loadNotes() {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_notes/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayNotes(response.data);
            } else {
                $('#appointment-notes-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No notes found for this appointment.</p></div>');
            }
        },
        error: function() {
            $('#appointment-notes-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading notes.</p></div>');
        }
    });
}

// Timeline Functions
function loadTimeline() {
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
        error: function() {
            $('#timeline-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading timeline.</p></div>');
        }
    });
}

function displayNotes(notes) {
    if (notes.length === 0) {
        $('#appointment-notes-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No notes found for this appointment.</p></div>');
        return;
    }

    var html = '';
    var len = notes.length;
    var i = 0;
    
    notes.forEach(function(note) {
        var timeAgo = note.time_ago || moment(note.dateadded).fromNow();
        var staffName = note.firstname + ' ' + note.lastname;
        var staffProfileImage = note.profile_image && note.profile_image !== '' ? note.profile_image : admin_url + 'assets/images/user-placeholder.jpg';
        
        html += '<div class="feed-item">';
        html += '<div class="date">';
        html += '<span class="text-has-action" data-toggle="tooltip" data-title="' + note.dateadded + '" data-original-title="" title="">' + timeAgo + '</span>';
        html += '</div>';
        html += '<div class="text">';
        html += '<a href="' + admin_url + 'admin/profile/' + note.addedfrom + '">';
        html += '<img class="staff-profile-xs-image pull-left mright5" src="' + staffProfileImage + '" alt="' + staffName + '">';
        html += '</a>';
        html += '<b>' + staffName + '</b> - ' + note.description;
        html += '</div>';
        html += '<div class="text-right mtop5">';
        html += '<button class="btn btn-default btn-xs" onclick="editNote(' + note.id + ')" title="Edit Note"><i class="fa fa-edit"></i></button> ';
        html += '<button class="btn btn-danger btn-xs" onclick="deleteNote(' + note.id + ')" title="Delete Note"><i class="fa fa-trash"></i></button>';
        html += '</div>';
        html += '</div>';
        
        if (i >= 0 && i != len - 1) {
            html += '<hr />';
        }
        i++;
    });
    
    $('#appointment-notes-container').html(html);
    
    // Set up time refresh every minute
    if (typeof window.notesTimeRefresh !== 'undefined') {
        clearInterval(window.notesTimeRefresh);
    }
    
    window.notesTimeRefresh = setInterval(function() {
        refreshNotesTime();
    }, 60000); // Refresh every minute
}

// Function to refresh time display for notes
function refreshNotesTime() {
    $('.feed-item .date .text-has-action').each(function() {
        var $this = $(this);
        var originalDate = $this.data('title');
        if (originalDate) {
            // Use moment.js to calculate fresh time
            var timeAgo = moment(originalDate).fromNow();
            $this.text(timeAgo);
        }
    });
}

// Cleanup function to clear interval when page is unloaded
$(window).on('beforeunload', function() {
    if (typeof window.notesTimeRefresh !== 'undefined') {
        clearInterval(window.notesTimeRefresh);
    }
});

// Toggle note form
function toggleNoteForm() {
    $('#note-form').toggleClass('hide');
    if (!$('#note-form').hasClass('hide')) {
        $('#appointment_note_description').focus();
    }
}

function editNote(noteId) {
    // Get the note content
    var noteElement = $('button[onclick="editNote(' + noteId + ')"]').closest('.feed-item');
    var noteText = noteElement.find('.text').html();
    
    // Extract just the text content (remove HTML tags)
    var textContent = noteText.replace(/<[^>]*>/g, '').replace(/^[^-]+-\s*/, '');
    
    // Create edit form with tags section
    var editForm = '<div class="feed-item" data-note-edit="' + noteId + '">';
    editForm += '<div class="form-group">';
    editForm += '<div class="lead emoji-picker-container leadnotes">';
    editForm += '<textarea class="form-control" rows="3" id="edit-note-' + noteId + '" data-emojiable="true">' + textContent + '</textarea>';
    editForm += '</div>';
    editForm += '</div>';
    
    // Add tags section (clone from the template)
    var tagsHtml = $('#tags-template').html();
    if (tagsHtml) {
        editForm += '<div class="form-group">' + tagsHtml + '</div>';
    }
    
    editForm += '<div class="text-right">';
    editForm += '<button class="btn btn-default btn-xs" onclick="cancelEditNote(' + noteId + ')" title="Cancel">Cancel</button> ';
    editForm += '<button class="btn btn-info btn-xs" onclick="updateNote(' + noteId + ')" title="Update">Update</button>';
    editForm += '</div>';
    editForm += '</div>';
    
    // Replace the note with edit form
    noteElement.replaceWith(editForm);
}

function cancelEditNote(noteId) {
    // Reload notes to restore original display
    loadNotes();
}

function updateNote(noteId) {
    var newDescription = $('#edit-note-' + noteId).val();
    
    if (!newDescription.trim()) {
        alert_float('danger', 'Note description cannot be empty');
        return;
    }
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/add_note/' + appointmentId + '/' + noteId,
        type: 'POST',
        data: {
            description: newDescription,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Note updated successfully!');
                loadNotes(); // Reload notes
            } else {
                alert_float('danger', response.message || 'Failed to update note');
            }
        },
        error: function() {
            alert_float('danger', 'Error updating note');
        }
    });
}

function deleteNote(noteId) {
    if (confirm('Are you sure you want to delete this note?')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_note/' + noteId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Note deleted successfully!');
                    loadNotes(); // Reload notes
                } else {
                    alert_float('danger', response.message || 'Failed to delete note');
                }
            },
            error: function() {
                alert_float('danger', 'Error deleting note');
            }
        });
    }
}

function displayEstimates(estimates) {
    if (estimates.length === 0) {
        $('#estimates-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No estimates found for this appointment.</p></div>');
        return;
    }

    var html = '<div class="table-responsive"><table class="table table-hover" style="margin-bottom: 0;">';
    html += '<thead style="background-color: #2c3e50; color: white;">';
    html += '<tr>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Estimate Name</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Status</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Line Items</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Total Amount</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Created By</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Created Date</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600; width: 120px;">Actions</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';

    estimates.forEach(function(estimate, idx) {
        var statusClass = '';
        var statusText = estimate.status;
        var statusColor = '';
        
        switch(estimate.status) {
            case 'draft':
                statusClass = 'label-warning';
                statusColor = '#f39c12';
                break;
            case 'sent':
                statusClass = 'label-info';
                statusColor = '#3498db';
                break;
            case 'accepted':
                statusClass = 'label-success';
                statusColor = '#27ae60';
                break;
            case 'rejected':
                statusClass = 'label-danger';
                statusColor = '#e74c3c';
                break;
            case 'expired':
                statusClass = 'label-default';
                statusColor = '#95a5a6';
                break;
        }
        
        var totalAmount = estimate.total_amount ? parseFloat(estimate.total_amount).toFixed(2) : '0.00';
        var createdDate = estimate.created_at ? new Date(estimate.created_at).toLocaleDateString() : '-';

        // Alternate row colors
        var rowClass = (idx % 2 === 0) ? 'style="background-color: #f8f9fa;"' : 'style="background-color: white;"';

        html += '<tr ' + rowClass + '>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + estimate.estimate_name + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;">';
        html += '<span style="background-color: ' + statusColor + '; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">' + statusText.toUpperCase() + '</span>';
        html += '</td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + (estimate.line_items_count || 0) + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>$' + totalAmount + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + (estimate.created_by_name || '-') + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + createdDate + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;">';
        html += '<div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">';
        html += '<button class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="openEstimateModal(' + estimate.id + ')" title="Edit Estimate"><i class="fa fa-edit"></i></button>';
        html += '<button class="btn btn-sm" style="background-color: #dc3545; border: 1px solid #dc3545; color: white; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteEstimate(' + estimate.id + ')" title="Delete Estimate"><i class="fa fa-trash"></i></button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    $('#estimates-container').html(html);
}

function deleteEstimate(estimateId) {
    if (confirm('Are you sure you want to delete this estimate?')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_estimate/' + appointmentId + '/' + estimateId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Estimate deleted successfully');
                    loadEstimates(); // Reload estimates
                } else {
                    alert_float('danger', response.message || 'Failed to delete estimate');
                }
            },
            error: function() {
                alert_float('danger', 'Error deleting estimate');
            }
        });
    }
}

// displayMeasurements function moved to measurements.js

// openMeasurementModal function moved to measurements.js

// loadMeasurementData function moved to measurements.js

// populateMeasurementForm function moved to measurements.js

// Populate windows and doors tables with data
function populateWindowsDoorsTables(category, data) {
    var tbody = $('#' + category + '-tbody');
    tbody.html(''); // Clear existing data
    
    if (Array.isArray(data)) {
        data.forEach(function(item) {
            if (category === 'windows') {
                addToWindowsTable(item);
            } else if (category === 'doors') {
                addToDoorsTable(item);
            }
        });
    }
}

// Display existing windows and doors data in the measurement modal
function displayExistingWindowsDoorsData(attributes) {
    // Clear existing data first
    $('#windows-tbody').html('');
    $('#doors-tbody').html('');
    
    if (attributes.windows && Array.isArray(attributes.windows)) {
        attributes.windows.forEach(function(window, index) {
            // Add rowId to track existing data
            window.rowId = 'existing_window_' + index;
            addToWindowsTable(window, true);
        });
    }
    
    if (attributes.doors && Array.isArray(attributes.doors)) {
        attributes.doors.forEach(function(door, index) {
            // Add rowId to track existing data
            door.rowId = 'existing_door_' + index;
            addToDoorsTable(door, true);
        });
    }
}

// editMeasurement and deleteMeasurement functions moved to measurements.js

// Collect all tabs data function (from original measurements form)
function collectAllTabsData() {
    var allData = {};
    
    // Collect data from each category tab
    ['siding', 'roofing', 'windows', 'doors'].forEach(function(category) {
        var categoryData = {};
        
        if (category === 'windows' || category === 'doors') {
            // Handle windows and doors from tables
            var tableData = collectTableData(category);
            if (Object.keys(tableData).length > 0) {
                allData[category] = tableData;
            }
        } else {
            // Get all inputs for this category (siding, roofing)
            $('input[name^="' + category + '["]').each(function() {
                var name = $(this).attr('name');
                var value = $(this).val();
                if (value !== '' && value !== null && value !== undefined) {
                    // Extract field name from name attribute like "siding[siding_total_area]"
                    var fieldName = name.match(/\[([^\]]+)\]/)[1];
                    categoryData[fieldName] = value;
                }
            });
            
            if (Object.keys(categoryData).length > 0) {
                allData[category] = categoryData;
            }
        }
    });
    
    return allData;
}

// Collect data from windows and doors tables
function collectTableData(category) {
    var tableData = [];
    var tbody = $('#' + category + '-tbody');
    if (tbody.length === 0) { return tableData; }
    
        tbody.find('tr').each(function() {
            var row = $(this);
        var isInline = row.hasClass('inline-measure-row');
        var data = {};

        if (isInline) {
            data.designator = row.find('.cell-designator').val() || '';
            data.name = row.find('.cell-name').val() || '';
            data.location_label = row.find('.cell-location').val() || '';
            data.level_label = row.find('.cell-level').val() || '';
            data.width_val = row.find('.cell-width').val() || '';
            data.height_val = row.find('.cell-height').val() || '';
            data.united_inches_val = row.find('.cell-ui-text').text() || '';
            data.area_val = row.find('.cell-area-text').text() || '';
        } else {
            var cells = row.find('td');
            data.designator = cells.eq(0).text().trim();
            data.name = cells.eq(1).text().trim();
            data.location_label = cells.eq(2).text().trim();
            data.level_label = cells.eq(3).text().trim();
            data.width_val = cells.eq(4).text().trim();
            data.height_val = cells.eq(5).text().trim();
            data.united_inches_val = cells.eq(6).text().trim();
            data.area_val = cells.eq(7).text().trim();
        }

        if (data.name) { tableData.push(data); }
    });
    
    return tableData;
}

// Add window to windows table
function addToWindowsTable(data, isExisting = false) {
    var tbody = $('#windows-tbody');
    var rowId = isExisting ? (data.rowId || 'window_' + Date.now()) : 'window_' + Date.now();
    
    var row = '<tr id="' + rowId + '">';
    row += '<td>' + (data.designator || '') + '</td>';
    row += '<td>' + (data.name || '') + '</td>';
    row += '<td>' + (data.location_label || '') + '</td>';
    row += '<td>' + (data.level_label || '') + '</td>';
    row += '<td>' + (data.width_val || '') + '</td>';
    row += '<td>' + (data.height_val || '') + '</td>';
    row += '<td>' + (data.united_inches_val || '') + '</td>';
    row += '<td>' + (data.area_val || '') + '</td>';
    row += '<td>';
    row += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'windows\')" title="Edit"><i class="fa fa-edit"></i></button> ';
    row += '<button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button>';
    row += '</td>';
    row += '</tr>';
    
    tbody.append(row);
}

// Add door to doors table
function addToDoorsTable(data, isExisting = false) {
    var tbody = $('#doors-tbody');
    var rowId = isExisting ? (data.rowId || 'door_' + Date.now()) : 'door_' + Date.now();
    
    var row = '<tr id="' + rowId + '">';
    row += '<td>' + (data.designator || '') + '</td>';
    row += '<td>' + (data.name || '') + '</td>';
    row += '<td>' + (data.location_label || '') + '</td>';
    row += '<td>' + (data.level_label || '') + '</td>';
    row += '<td>' + (data.width_val || '') + '</td>';
    row += '<td>' + (data.height_val || '') + '</td>';
    row += '<td>' + (data.united_inches_val || '') + '</td>';
    row += '<td>' + (data.area_val || '') + '</td>';
    row += '<td>';
    row += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'doors\')" title="Edit"><i class="fa fa-edit"></i></button> ';
    row += '<button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button>';
    row += '</td>';
    row += '</tr>';
    
    tbody.append(row);
}

// Edit table row
function editTableRow(rowId, category) {
    var row = $('#' + rowId);
    var cells = row.find('td');
    
    // Extract data from row
    var data = {
        designator: cells.eq(0).text(),
        name: cells.eq(1).text(),
        location_label: cells.eq(2).text(),
        level_label: cells.eq(3).text(),
        width_val: cells.eq(4).text(),
        height_val: cells.eq(5).text(),
        united_inches_val: cells.eq(6).text(),
        area_val: cells.eq(7).text()
    };
    
    // Open appropriate modal with data
    if (category === 'windows') {
        openWindowModal(data);
    } else if (category === 'doors') {
        openDoorModal(data);
    }
    
    // Mark row for deletion when new data is saved
    row.attr('data-to-delete', 'true');
}

// removeTableRow function moved to measurements.js

// Open window modal with data
function openWindowModal(data) {
    $('#window-form')[0].reset();
    $('#windowModal .modal-title').text('Edit Window');
    
    // Populate form with data
    if (data) {
        $('input[name="designator"]').val(data.designator || '');
        $('input[name="name"]').val(data.name || '');
        $('select[name="location_label"]').val(data.location_label || '');
        $('select[name="level_label"]').val(data.level_label || '');
        $('input[name="width_val"]').val(data.width_val || '');
        $('input[name="height_val"]').val(data.height_val || '');
        $('input[name="united_inches_val"]').val(data.united_inches_val || '');
        $('input[name="area_val"]').val(data.area_val || '');
    }
    
    $('#windowModal').modal('show');
}

// Open door modal with data
function openDoorModal(data) {
    $('#door-form')[0].reset();
    $('#doorModal .modal-title').text('Edit Door');
    
    // Populate form with data
    if (data) {
        $('input[name="designator"]').val(data.designator || '');
        $('input[name="name"]').val(data.name || '');
        $('select[name="location_label"]').val(data.location_label || '');
        $('select[name="level_label"]').val(data.level_label || '');
        $('input[name="width_val"]').val(data.width_val || '');
        $('input[name="height_val"]').val(data.height_val || '');
        $('input[name="united_inches_val"]').val(data.united_inches_val || '');
        $('input[name="area_val"]').val(data.area_val || '');
    }
    
    $('#doorModal').modal('show');
}

// Update windows table row
function updateWindowsTableRow(row, data) {
    var cells = row.find('td');
    cells.eq(0).text(data.designator || '');
    cells.eq(1).text(data.name || '');
    cells.eq(2).text(data.location_label || '');
    cells.eq(3).text(data.level_label || '');
    cells.eq(4).text(data.width_val || '');
    cells.eq(5).text(data.height_val || '');
    cells.eq(6).text(data.united_inches_val || '');
    cells.eq(7).text(data.area_val || '');
}

// Update doors table row
function updateDoorsTableRow(row, data) {
    var cells = row.find('td');
    cells.eq(0).text(data.designator || '');
    cells.eq(1).text(data.name || '');
    cells.eq(2).text(data.location_label || '');
    cells.eq(3).text(data.level_label || '');
    cells.eq(4).text(data.width_val || '');
    cells.eq(5).text(data.height_val || '');
    cells.eq(6).text(data.united_inches_val || '');
    cells.eq(7).text(data.area_val || '');
}

// Tab handling
$('#category-tabs a[data-toggle="tab"]').on('click', function(e) {
    e.preventDefault();
    var category = $(this).data('category');
    $('#selected-category').val(category);

    // Show the corresponding tab content
    $('.tab-pane').removeClass('active');
    $('#' + category + '-tab').addClass('active');

    // Update active tab
    $('#category-tabs li').removeClass('active');
    $(this).parent().addClass('active');

    // loadMeasurementsByCategory call moved to measurements.js
});

// loadMeasurementsByCategory function moved to measurements.js

// populateMeasurementsTable function moved to measurements.js

// calculateMeasurements function moved to measurements.js

// calculateMeasurements event handler moved to measurements.js

// saveMeasurement event handler moved to measurements.js

// Windows/Doors save event handler moved to measurements.js

// saveMeasurementAjax function moved to measurements.js

// Inline add row handlers moved to measurements.js

// buildLocationOptions and buildLevelOptions functions moved to measurements.js

// appendInlineRow function moved to measurements.js

// Convert existing text row into inline editable inputs
function editTableRow(rowId, category) {
    var row = $('#' + rowId);
    var cells = row.find('td');
    var existingId = row.data('measurement-id') || '';
    var data = {
        designator: cells.eq(0).text(),
        name: cells.eq(1).text(),
        location_label: cells.eq(2).text(),
        level_label: cells.eq(3).text(),
        width_val: cells.eq(4).text(),
        height_val: cells.eq(5).text(),
        united_inches_val: cells.eq(6).text(),
        area_val: cells.eq(7).text(),
        id: existingId
    };
    row.remove();
    appendInlineRow(category, data);
}

// Auto-calc UI & Area inside inline rows
$(document).on('input change', '.inline-measure-row .cell-width, .inline-measure-row .cell-height', function() {
    var row = $(this).closest('tr');
    var width = parseFloat(row.find('.cell-width').val()) || 0;
    var height = parseFloat(row.find('.cell-height').val()) || 0;
    if (width > 0 && height > 0) {
        var ui = width + height;
        var area = (width * height) / 144.0;
        row.find('.cell-ui-text').text(ui.toFixed(2));
        row.find('.cell-area-text').text(area.toFixed(2));
    } else {
        row.find('.cell-ui-text').text('');
        row.find('.cell-area-text').text('');
    }
});

function renderSavedRow(row, category, data, id) {
    var rowId = row.attr('id');
    var html = '';
    html += '<td>' + (data.designator || '') + '</td>';
    html += '<td>' + (data.name || '') + '</td>';
    html += '<td>' + (data.location_label || '') + '</td>';
    html += '<td>' + (data.level_label || '') + '</td>';
    html += '<td>' + (data.width_val || '') + '</td>';
    html += '<td>' + (data.height_val || '') + '</td>';
    html += '<td>' + (data.united_inches_val || '') + '</td>';
    html += '<td>' + (data.area_val || '') + '</td>';
    var actions = '';
    actions += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'' + category + '\')" title="Edit"><i class="fa fa-edit"></i></button> ';
    if (id) {
        actions += '<button class="btn btn-danger btn-xs" onclick="deleteMeasurement(' + id + ')" title="Delete"><i class="fa fa-trash"></i></button>';
    } else {
        actions += '<button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button>';
    }
    html += '<td>' + actions + '</td>';
    row.removeClass('inline-measure-row').attr('data-measurement-id', id || '').html(html);
}
</script>

<?php $this->load->view('appointments/modal'); ?>
<?php $this->load->view('appointments/sms_modal'); ?>

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
    $('#contact_id').html('<option value="">Select Client/Lead</option>');
    $('#contact_id').selectpicker('val', '');
    $('.selectpicker').selectpicker('refresh');
}

// Global functions for modal operations
function openAppointmentModal(appointmentId = null) {
    if ($('#appointmentForm').length === 0) {
        return;
    }
    
    // Reset form
    resetAppointmentModal();
    
    // Set today's date as default (only for new appointments)
    if (!appointmentId) {
        var today = new Date().toISOString().slice(0, 16);
        $('#start_datetime').val(today);
        $('#end_datetime').val(today);
    }
    
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
                $('#type_id').val(data.type_id);
                
                // Handle reminder checkbox
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
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
                                    
                                    // Only populate form fields if they are empty
                                    if (!$('#email').val() && data.email) {
                                        $('#email').val(data.email);
                                    }
                                    if (!$('#phone').val() && data.phone) {
                                        $('#phone').val(data.phone);
                                    }
                                    if (!$('#address').val() && data.address) {
                                        $('#address').val(data.address);
                                    }
                                    
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
                
                // Set attendees
                var attendeeIds = [];
                if (data.attendees) {
                    $.each(data.attendees, function(index, attendee) {
                        attendeeIds.push(attendee.staff_id);
                    });
                }
                $('#attendees').val(attendeeIds);
                
                // Update modal title
                $('#appointmentModalLabel').text('Edit Appointment');
                
                // Refresh selectpicker
                $('.selectpicker').selectpicker('refresh');
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
                $('#type_id').val(data.type_id);
                
                // Handle reminder checkbox
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
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
                                    
                                    // Only populate form fields if they are empty
                                    if (!$('#email').val() && data.email) {
                                        $('#email').val(data.email);
                                    }
                                    if (!$('#phone').val() && data.phone) {
                                        $('#phone').val(data.phone);
                                    }
                                    if (!$('#address').val() && data.address) {
                                        $('#address').val(data.address);
                                    }
                                    
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
                
                // Set attendees
                var attendeeIds = [];
                if (data.attendees) {
                    $.each(data.attendees, function(index, attendee) {
                        attendeeIds.push(attendee.staff_id);
                    });
                }
                $('#attendees').val(attendeeIds);
                
                // Update modal title
                $('#appointmentModalLabel').text('Edit Appointment');
                
                // Refresh selectpicker
                $('.selectpicker').selectpicker('refresh');
                
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
                        
                        // Only populate form fields if they are empty
                        if (!$('#email').val() && data.email) {
                            $('#email').val(data.email);
                        }
                        if (!$('#phone').val() && data.phone) {
                            $('#phone').val(data.phone);
                        }
                        if (!$('#address').val() && data.address) {
                            $('#address').val(data.address);
                        }
                        
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
            alert_float('danger', 'Subject is required');
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
                    // Reload the page to show updated data
                    window.location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error saving appointment: ' + error);
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

<?php $this->load->view('appointments/attachments_js.php'); ?>
<?php $this->load->view('appointments/sms_js.php'); ?>

<!-- Load module CSS for SMS modal styling -->
<link rel="stylesheet" href="<?php echo module_dir_url('ella_contractors', 'assets/css/ella-contractors.css'); ?>">
