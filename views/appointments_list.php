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
                                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin">
                                    <i class="fa fa-calendar-check-o"></i> <?= $title ?>
                                </h4>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="<?= admin_url('ella_contractors') ?>">
                                                <i class="fa fa-home"></i> Ella Contractors
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Appointments</li>
                                    </ol>
                                </nav>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?= admin_url('ella_contractors/add_appointment') ?>" class="btn btn-info">
                                    <i class="fa fa-plus"></i> Add New Appointment
                                </a>
                            </div>
                        </div>
                <hr class="hr-panel-separator" />
                
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default filter-btn active" data-filter="all">All</button>
                            <button type="button" class="btn btn-default filter-btn" data-filter="scheduled">Scheduled</button>
                            <button type="button" class="btn btn-default filter-btn" data-filter="confirmed">Confirmed</button>
                            <button type="button" class="btn btn-default filter-btn" data-filter="completed">Completed</button>
                            <button type="button" class="btn btn-default filter-btn" data-filter="cancelled">Cancelled</button>
                        </div>
                        
                        <div class="pull-right">
                            <select class="form-control" id="contractFilter">
                                <option value="">All Contracts</option>
                                <?php foreach ($contracts as $contract): ?>
                                <option value="<?= $contract->id ?>"><?= $contract->subject ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Appointments Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Title</th>
                                <th>Contract</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $appointment): ?>
                                <tr class="appointment-row" data-status="<?= $appointment->status ?>" data-contract="<?= $appointment->contract_id ?>">
                                    <td>
                                        <strong><?= date('M d, Y', strtotime($appointment->appointment_date)) ?></strong><br>
                                        <small class="text-muted">
                                            <?= date('g:i A', strtotime($appointment->start_time)) ?> - 
                                            <?= date('g:i A', strtotime($appointment->end_time)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?= $appointment->title ?></strong>
                                        <?php if ($appointment->description): ?>
                                            <br><small class="text-muted"><?= character_limiter($appointment->description, 50) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/contracts/view/' . $appointment->contract_id) ?>">
                                            <?= $appointment->contract_title ?: 'Contract #' . $appointment->contract_id ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="label label-info"><?= ucfirst($appointment->appointment_type) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        switch ($appointment->status) {
                                            case 'scheduled':
                                                $status_class = 'label-default';
                                                break;
                                            case 'confirmed':
                                                $status_class = 'label-success';
                                                break;
                                            case 'completed':
                                                $status_class = 'label-primary';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'label-danger';
                                                break;
                                            case 'rescheduled':
                                                $status_class = 'label-warning';
                                                break;
                                        }
                                        ?>
                                        <span class="label <?= $status_class ?>"><?= ucfirst($appointment->status) ?></span>
                                    </td>
                                    <td>
                                        <?= $appointment->location ?: '<span class="text-muted">Not specified</span>' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= admin_url('ella_contractors/edit_appointment/' . $appointment->id) ?>" 
                                               class="btn btn-default btn-xs" title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/appointments/' . $appointment->contract_id) ?>" 
                                               class="btn btn-default btn-xs" title="View Contract Appointments">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" 
                                                    data-toggle="dropdown" title="More Actions">
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" onclick="updateStatus(<?= $appointment->id ?>, 'confirmed')">
                                                        <i class="fa fa-check text-success"></i> Mark Confirmed
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="updateStatus(<?= $appointment->id ?>, 'completed')">
                                                        <i class="fa fa-check-circle text-primary"></i> Mark Completed
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="updateStatus(<?= $appointment->id ?>, 'cancelled')">
                                                        <i class="fa fa-times text-danger"></i> Mark Cancelled
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="#" onclick="deleteAppointment(<?= $appointment->id ?>)" class="text-danger">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <p class="text-muted">No appointments found.</p>
                                        <a href="<?= admin_url('ella_contractors/add_appointment') ?>" class="btn btn-info">
                                            <i class="fa fa-plus"></i> Add First Appointment
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="statusForm" method="post" action="<?= admin_url('ella_contractors/update_appointment_status') ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Appointment Status</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="statusAppointmentId">
                    <div class="form-group">
                        <label for="statusSelect">New Status</label>
                        <select class="form-control" name="status" id="statusSelect" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this appointment? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Filter functionality
    $('.filter-btn').click(function() {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        var filter = $(this).data('filter');
        
        if (filter === 'all') {
            $('.appointment-row').show();
        } else {
            $('.appointment-row').hide();
            $('.appointment-row[data-status="' + filter + '"]').show();
        }
    });
    
    // Contract filter
    $('#contractFilter').change(function() {
        var contractId = $(this).val();
        
        if (contractId === '') {
            $('.appointment-row').show();
        } else {
            $('.appointment-row').hide();
            $('.appointment-row[data-contract="' + contractId + '"]').show();
        }
    });
});

function updateStatus(appointmentId, status) {
    $('#statusAppointmentId').val(appointmentId);
    $('#statusSelect').val(status);
    $('#statusModal').modal('show');
}

function deleteAppointment(appointmentId) {
    $('#confirmDelete').attr('href', '<?= admin_url('ella_contractors/delete_appointment/') ?>' + appointmentId);
    $('#deleteModal').modal('show');
}
</script>

    </div>
</div>

<?php init_tail(); ?>
