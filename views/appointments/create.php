<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin">Create New Appointment</h4>
                <hr class="hr-panel-heading" />
                
                <form action="<?php echo admin_url('ella_contractors/appointments/save'); ?>" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_id">Appointment Type</label>
                                <select class="form-control" id="type_id" name="type_id">
                                    <option value="0">Select Type</option>
                                    <?php foreach($appointment_types as $type): ?>
                                        <option value="<?php echo $type['id']; ?>"><?php echo $type['type']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_hour">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="start_hour" name="start_hour" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_id">Client/Lead</label>
                                <select class="form-control" id="contact_id" name="contact_id">
                                    <option value="">Select Client/Lead</option>
                                    <optgroup label="Clients">
                                        <?php foreach($clients as $client): ?>
                                            <option value="<?php echo $client['userid']; ?>"><?php echo $client['company']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Leads">
                                        <?php foreach($leads as $lead): ?>
                                            <option value="<?php echo $lead['id']; ?>"><?php echo $lead['name']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="attendees">Attendees</label>
                                <select class="form-control selectpicker" id="attendees" name="attendees[]" multiple>
                                    <?php foreach($staff as $member): ?>
                                        <option value="<?php echo $member['staffid']; ?>"><?php echo $member['firstname'] . ' ' . $member['lastname']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Contact Name</label>
                                <input type="text" class="form-control" id="name" name="name">
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
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="approved" value="1"> Approved
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="finished" value="1"> Finished
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="cancelled" value="1"> Cancelled
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-bottom-toolbar text-right">
                        <a href="<?php echo admin_url('ella_contractors/appointments'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-info">Create Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
    
    // Set today's date as default
    $('#date').val('<?php echo date('Y-m-d'); ?>');
});
</script>