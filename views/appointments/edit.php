<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

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
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <h4 class="no-margin"><?php echo _l('edit_appointment'); ?></h4>
                        <p class="text-muted"><?php echo _l('edit_appointment_description'); ?></p>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <form id="appointmentForm" method="post" action="<?php echo admin_url('ella_contractors/appointments/save'); ?>">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="subject"><?php echo _l('appointment_subject'); ?> <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $appointment['subject']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type_id"><?php echo _l('appointment_type'); ?></label>
                                                <select class="form-control" id="type_id" name="type_id">
                                                    <option value="0"><?php echo _l('select_type'); ?></option>
                                                    <?php foreach($appointment_types as $type): ?>
                                                        <option value="<?php echo $type['id']; ?>" <?php echo ($appointment['type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                                            <?php echo $type['type']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date"><?php echo _l('appointment_meeting_date'); ?> <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $appointment['date']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="start_hour"><?php echo _l('appointment_time'); ?> <span class="text-danger">*</span></label>
                                                <input type="time" class="form-control" id="start_hour" name="start_hour" value="<?php echo $appointment['start_hour']; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_id"><?php echo _l('client'); ?></label>
                                                <select class="form-control" id="contact_id" name="contact_id">
                                                    <option value=""><?php echo _l('select_client'); ?></option>
                                                    <optgroup label="<?php echo _l('clients'); ?>">
                                                        <?php foreach($clients as $client): ?>
                                                            <option value="<?php echo $client['userid']; ?>" <?php echo ($appointment['contact_id'] == $client['userid']) ? 'selected' : ''; ?>>
                                                                <?php echo $client['company']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                    <optgroup label="<?php echo _l('leads'); ?>">
                                                        <?php foreach($leads as $lead): ?>
                                                            <option value="<?php echo $lead['id']; ?>" <?php echo ($appointment['contact_id'] == $lead['id']) ? 'selected' : ''; ?>>
                                                                <?php echo $lead['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="attendees"><?php echo _l('attendees'); ?></label>
                                                <select class="form-control selectpicker" id="attendees" name="attendees[]" multiple>
                                                    <?php foreach($staff as $member): ?>
                                                        <option value="<?php echo $member['staffid']; ?>" <?php echo (in_array($member['staffid'], array_column($attendees, 'staff_id'))) ? 'selected' : ''; ?>>
                                                            <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name"><?php echo _l('contact_name'); ?></label>
                                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $appointment['name']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email"><?php echo _l('email'); ?></label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $appointment['email']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone"><?php echo _l('phone'); ?></label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $appointment['phone']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address"><?php echo _l('address'); ?></label>
                                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $appointment['address']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="description"><?php echo _l('description'); ?></label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $appointment['description']; ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="notes"><?php echo _l('notes'); ?></label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $appointment['notes']; ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status"><?php echo _l('appointment_status'); ?></label>
                                                <?php
                                                    $status = 'pending';
                                                    if (!empty($appointment['cancelled'])) { $status = 'cancelled'; }
                                                    else if (!empty($appointment['finished'])) { $status = 'finished'; }
                                                    else if (!empty($appointment['approved'])) { $status = 'approved'; }
                                                ?>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="pending" <?php echo $status=='pending'?'selected':''; ?>><?php echo _l('pending'); ?></option>
                                                    <option value="approved" <?php echo $status=='approved'?'selected':''; ?>><?php echo _l('approved'); ?></option>
                                                    <option value="finished" <?php echo $status=='finished'?'selected':''; ?>><?php echo _l('finished'); ?></option>
                                                    <option value="cancelled" <?php echo $status=='cancelled'?'selected':''; ?>><?php echo _l('cancelled'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="btn-bottom-toolbar text-right">
                                        <a href="<?php echo admin_url('ella_contractors/appointments'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                                        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
});
</script>
