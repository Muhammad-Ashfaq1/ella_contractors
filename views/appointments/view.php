<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="no-margin"><?php echo $appointment['subject']; ?></h4>
                        <p class="text-muted">Appointment Details</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group">
                            <a href="<?php echo admin_url('ella_contractors/appointments/edit/' . $appointment['id']); ?>" class="btn btn-info btn-xs">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="<?php echo admin_url('ella_contractors/appointments/delete/' . $appointment['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                <i class="fa fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="hr-panel-heading" />
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Basic Information</h5>
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td><?php echo $appointment['id']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Subject:</strong></td>
                                <td><?php echo $appointment['subject']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td><?php echo _d($appointment['date']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Time:</strong></td>
                                <td><?php echo $appointment['start_hour']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php if($appointment['cancelled']): ?>
                                        <span class="label label-danger">Cancelled</span>
                                    <?php elseif($appointment['finished']): ?>
                                        <span class="label label-success">Finished</span>
                                    <?php elseif($appointment['approved']): ?>
                                        <span class="label label-info">Approved</span>
                                    <?php else: ?>
                                        <span class="label label-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Contact Information</h5>
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Client/Lead:</strong></td>
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
                                <td><strong>Contact Name:</strong></td>
                                <td><?php echo $appointment['name']; ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if($appointment['email']): ?>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?php echo $appointment['email']; ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if($appointment['phone']): ?>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td><?php echo $appointment['phone']; ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if($appointment['address']): ?>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td><?php echo $appointment['address']; ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <?php if($appointment['description']): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Description</h5>
                        <p><?php echo nl2br($appointment['description']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($appointment['notes']): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Notes</h5>
                        <p><?php echo nl2br($appointment['notes']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($attendees)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <h5>Attendees</h5>
                        <ul class="list-unstyled">
                            <?php foreach($attendees as $attendee): ?>
                                <li><i class="fa fa-user"></i> <?php echo $attendee['firstname'] . ' ' . $attendee['lastname']; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('ella_contractors/appointments'); ?>" class="btn btn-default">Back to Appointments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
