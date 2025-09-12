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
                            <a href="javascript:void(0)" class="btn btn-info" onclick="editAppointment(<?php echo $appointment['id']; ?>)">
                                <i class="fa fa-edit"></i> <?php echo _l('edit'); ?>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteAppointment(<?php echo $appointment['id']; ?>)">
                                <i class="fa fa-trash"></i> <?php echo _l('delete'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin"><?php echo $appointment['subject']; ?></h4>
                                <p class="text-muted"><?php echo _l('appointment_details'); ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if($appointment['cancelled']): ?>
                                    <span class="label label-danger"><?php echo _l('cancelled'); ?></span>
                                <?php elseif($appointment['finished']): ?>
                                    <span class="label label-success"><?php echo _l('finished'); ?></span>
                                <?php elseif($appointment['approved']): ?>
                                    <span class="label label-info"><?php echo _l('approved'); ?></span>
                                <?php else: ?>
                                    <span class="label label-warning"><?php echo _l('pending'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo _l('basic_information'); ?></h5>
                                <table class="table table-condensed">
                                    <tr>
                                        <td><strong><?php echo _l('id'); ?>:</strong></td>
                                        <td><?php echo $appointment['id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_subject'); ?>:</strong></td>
                                        <td><?php echo $appointment['subject']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_meeting_date'); ?>:</strong></td>
                                        <td><?php echo _d($appointment['date']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_time'); ?>:</strong></td>
                                        <td><?php echo $appointment['start_hour']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('appointment_status'); ?>:</strong></td>
                                        <td>
                                            <?php if($appointment['cancelled']): ?>
                                                <span class="label label-danger"><?php echo _l('cancelled'); ?></span>
                                            <?php elseif($appointment['finished']): ?>
                                                <span class="label label-success"><?php echo _l('finished'); ?></span>
                                            <?php elseif($appointment['approved']): ?>
                                                <span class="label label-info"><?php echo _l('approved'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-warning"><?php echo _l('pending'); ?></span>
                                            <?php endif; ?>
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
                                            <?php if($appointment['client_name']): ?>
                                                <?php echo $appointment['client_name']; ?>
                                            <?php elseif($appointment['lead_name']): ?>
                                                <?php echo $appointment['lead_name']; ?>
                                            <?php else: ?>
                                                <?php echo $appointment['name']; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if($appointment['name']): ?>
                                    <tr>
                                        <td><strong><?php echo _l('contact_name'); ?>:</strong></td>
                                        <td><?php echo $appointment['name']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if($appointment['email']): ?>
                                    <tr>
                                        <td><strong><?php echo _l('email'); ?>:</strong></td>
                                        <td><?php echo $appointment['email']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if($appointment['phone']): ?>
                                    <tr>
                                        <td><strong><?php echo _l('phone'); ?>:</strong></td>
                                        <td><?php echo $appointment['phone']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if($appointment['address']): ?>
                                    <tr>
                                        <td><strong><?php echo _l('address'); ?>:</strong></td>
                                        <td><?php echo $appointment['address']; ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                
                        <?php if($appointment['description']): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5><?php echo _l('description'); ?></h5>
                                <p><?php echo nl2br($appointment['description']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($appointment['notes']): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5><?php echo _l('notes'); ?></h5>
                                <p><?php echo nl2br($appointment['notes']); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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
    </div>
</div>

<?php init_tail(); ?>

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Global functions for modal operations
function editAppointment(appointmentId) {
    // Redirect to edit page or open modal
    window.location.href = admin_url + 'ella_contractors/appointments/edit/' + appointmentId;
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
</script>
