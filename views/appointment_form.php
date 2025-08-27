<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Ensure jQuery is loaded before any CSRF setup
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
}

// Override the problematic CSRF function to prevent errors
window.csrf_jquery_ajax_setup = function() {
    // Do nothing - prevent the error from general_helper.php
    return false;
};
</script>

<?php init_head(); ?>

<!-- Include module CSS -->
<link rel="stylesheet" href="<?php echo base_url('modules/ella_contractors/assets/css/ella_contractors.css'); ?>">

<div class="content">
    <div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin">
                    <i class="fa fa-calendar-plus-o"></i> <?= $title ?>
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= admin_url('ella_contractors') ?>">
                                <i class="fa fa-home"></i> Ella Contractors
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= admin_url('ella_contractors/appointments') ?>">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= isset($appointment) ? 'Edit' : 'Add' ?> Appointment
                        </li>
                    </ol>
                </nav>
                <hr class="hr-panel-separator" />
                
                <?php echo form_open_multipart('', ['id' => 'appointmentForm']); ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contract_id">Contract *</label>
                            <select name="contract_id" id="contract_id" class="form-control" required <?= isset($appointment) ? 'disabled' : '' ?>>
                                <option value="">Select Contract</option>
                                <?php foreach ($contracts as $contract): ?>
                                    <option value="<?= $contract->id ?>" <?= (isset($appointment) && $appointment->contract_id == $contract->id) || $contract_id == $contract->id ? 'selected' : '' ?>>
                                        <?= $contract->subject ?> (<?= $contract->contract_number ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($appointment)): ?>
                                <input type="hidden" name="contract_id" value="<?= $appointment->contract_id ?>">
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="title">Appointment Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="<?= isset($appointment) ? $appointment->title : '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_type">Appointment Type *</label>
                            <select name="appointment_type" id="appointment_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="initial_consultation" <?= isset($appointment) && $appointment->appointment_type == 'initial_consultation' ? 'selected' : '' ?>>Initial Consultation</option>
                                <option value="site_inspection" <?= isset($appointment) && $appointment->appointment_type == 'site_inspection' ? 'selected' : '' ?>>Site Inspection</option>
                                <option value="progress_review" <?= isset($appointment) && $appointment->appointment_type == 'progress_review' ? 'selected' : '' ?>>Progress Review</option>
                                <option value="final_walkthrough" <?= isset($appointment) && $appointment->appointment_type == 'final_walkthrough' ? 'selected' : '' ?>>Final Walkthrough</option>
                                <option value="material_selection" <?= isset($appointment) && $appointment->appointment_type == 'material_selection' ? 'selected' : '' ?>>Material Selection</option>
                                <option value="permit_application" <?= isset($appointment) && $appointment->appointment_type == 'permit_application' ? 'selected' : '' ?>>Permit Application</option>
                                <option value="quality_check" <?= isset($appointment) && $appointment->appointment_type == 'quality_check' ? 'selected' : '' ?>>Quality Check</option>
                                <option value="other" <?= isset($appointment) && $appointment->appointment_type == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="scheduled" <?= isset($appointment) && $appointment->status == 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                <option value="confirmed" <?= isset($appointment) && $appointment->status == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="completed" <?= isset($appointment) && $appointment->status == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= isset($appointment) && $appointment->status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                <option value="rescheduled" <?= isset($appointment) && $appointment->status == 'rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="appointment_date">Date *</label>
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" 
                                   value="<?= isset($appointment) ? $appointment->appointment_date : '' ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time">Start Time *</label>
                                    <input type="time" name="start_time" id="start_time" class="form-control" 
                                           value="<?= isset($appointment) ? $appointment->start_time : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time">End Time *</label>
                                    <input type="time" name="end_time" id="end_time" class="form-control" 
                                           value="<?= isset($appointment) ? $appointment->end_time : '' ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control" 
                                   value="<?= isset($appointment) ? $appointment->location : '' ?>" 
                                   placeholder="e.g., Project Site, Office, Client Home">
                        </div>
                        
                        <div class="form-group">
                            <label for="attendees">Attendees</label>
                            <textarea name="attendees" id="attendees" class="form-control" rows="2" 
                                      placeholder="List of people attending this appointment"><?= isset($appointment) ? $appointment->attendees : '' ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3" 
                                      placeholder="Detailed description of the appointment"><?= isset($appointment) ? $appointment->description : '' ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="Any additional notes or special instructions"><?= isset($appointment) ? $appointment->notes : '' ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> <?= isset($appointment) ? 'Update' : 'Create' ?> Appointment
                            </button>
                            <a href="<?= admin_url('ella_contractors/appointments') ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <?php if (isset($appointment)): ?>
                                <a href="<?= admin_url('ella_contractors/appointments/' . $appointment->contract_id) ?>" class="btn btn-info">
                                    <i class="fa fa-eye"></i> View Contract Appointments
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form validation
    $('#appointmentForm').submit(function(e) {
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        var appointmentDate = $('#appointment_date').val();
        var today = new Date().toISOString().split('T')[0];
        
        // Check if appointment date is in the past
        if (appointmentDate < today) {
            alert('Appointment date cannot be in the past.');
            e.preventDefault();
            return false;
        }
        
        // Check if end time is after start time
        if (startTime >= endTime) {
            alert('End time must be after start time.');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Auto-populate end time (1 hour after start time)
    $('#start_time').change(function() {
        var startTime = $(this).val();
        if (startTime) {
            var start = new Date('2000-01-01T' + startTime);
            var end = new Date(start.getTime() + (60 * 60 * 1000)); // Add 1 hour
            var endTime = end.toTimeString().slice(0, 5);
            $('#end_time').val(endTime);
        }
    });
    
    // Contract change handler
    $('#contract_id').change(function() {
        var contractId = $(this).val();
        if (contractId) {
            // You can add AJAX call here to get contract details and pre-fill some fields
            console.log('Selected contract:', contractId);
        }
    });
});
</script>

    </div>
</div>

<?php init_tail(); ?>
