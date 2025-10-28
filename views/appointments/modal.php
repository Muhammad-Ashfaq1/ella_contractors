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
                                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Which staff members will be joining you or handling this Appointment?"></i>
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
                        <div class="col-md-12">
                            <h5>Customer Notifications</h5>
                            <div class="checkbox">
                                <input type="checkbox" name="send_reminder" id="send_reminder" value="1" checked>
                                <label for="send_reminder">Instantly Send Appointment Date/Time to Customer</label>
                            </div>
                            <div class="checkbox">
                                <input type="checkbox" name="reminder_48h" id="reminder_48h" value="1" checked>
                                <label for="reminder_48h">Send 48 Hour Reminder to Customer</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <hr class="hr-panel-heading" />
                            <h5><?php echo _l('appointment_attachments'); ?></h5>                            
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

