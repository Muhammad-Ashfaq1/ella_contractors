<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#appointmentModal" onclick="openAppointmentModal()">
                                <i class="fa fa-plus"></i>New Appointment
                            </button>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-ella_appointments">
                                <thead>
                                    <tr>
                                        <th width="50px"></th>
                                        <th><?php echo _l('id'); ?></th>
                                        <th>Lead</th>
                                        <th><?php echo _l('appointment_subject'); ?></th>
                                        <th>Scheduled Date</th>
                                        <th><?php echo _l('appointment_status'); ?></th>
                                        <th width="100px"><i class="fa fa-square-o"></i> Measurements</th>
                                        <th width="100px"><i class="fa fa-file-text-o"></i> Estimates</th>
                                        <th width="120px"><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Include modal data
$data['staff'] = $this->staff_model->get();
$data['clients'] = $this->clients_model->get();
$data['leads'] = $this->leads_model->get();
$data['appointment_types'] = $this->appointments_model->get_appointment_types();
$this->load->view('appointments/modal', $data);
?>

<?php init_tail(); ?>

<style>
/* Custom styling for appointment count badges */
.table-ella_appointments .label {
    font-size: 11px;
    padding: 4px 8px;
    margin: 2px;
    display: inline-block;
}
.table-ella_appointments .text-muted {
    font-size: 11px;
    padding: 4px 8px;
    margin: 2px;
    display: inline-block;
    opacity: 0.6;
}
.table-ella_appointments th {
    text-align: center;
    vertical-align: middle;
}
.table-ella_appointments td {
    vertical-align: middle;
}
/* Clickable badge styling */
.table-ella_appointments .label a,
.table-ella_appointments .text-muted a {
    color: inherit;
    text-decoration: none;
    cursor: pointer;
}
.table-ella_appointments .label a:hover,
.table-ella_appointments .text-muted a:hover {
    color: inherit;
    text-decoration: none;
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
}
</style>

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

$(document).ready(function() {
    // Initialize DataTable for appointments
    initDataTable('.table-ella_appointments', admin_url + 'ella_contractors/appointments/table', undefined, undefined, {}, [2, 'desc']);
    
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
});

// Global functions for modal operations
function openAppointmentModal(appointmentId = null) {
    // Reset form
    $('#appointmentForm')[0].reset();
    $('#appointment_id').val('');
    $('#appointmentModalLabel').text('Create Appointment');
    
    // Set today's date as default
    $('#date').val('<?php echo date('Y-m-d'); ?>');
    
    // Refresh selectpicker
    $('.selectpicker').selectpicker('refresh');
    
    if (appointmentId) {
        // Load appointment data for editing
        loadAppointmentData(appointmentId);
    }
}

function loadAppointmentData(appointmentId) {
    console.log('Loading appointment data for ID:', appointmentId);
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_data',
        type: 'POST',
        data: {
            id: appointmentId,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            console.log('Appointment data response:', response);
            
            if (response.success) {
                var data = response.data;
                console.log('Appointment data:', data);
                
                // Populate form fields
                $('#appointment_id').val(data.id);
                $('#subject').val(data.subject);
                $('#date').val(data.date);
                $('#start_hour').val(data.start_hour);
                $('#contact_id').val(data.contact_id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#description').val(data.description);
                $('#notes').val(data.notes);
                $('#type_id').val(data.type_id);
                
                // Set status dropdown
                var status = 'scheduled';
                if (parseInt(data.cancelled) === 1) status = 'cancelled';
                else if (parseInt(data.finished) === 1) status = 'complete';
                else if (parseInt(data.approved) === 1) status = 'complete';
                $('#status').val(status);
                
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
                console.log('Error loading appointment:', response.message);
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error loading appointment:', xhr.responseText);
            alert_float('danger', 'Error loading appointment data: ' + error);
        }
    });
}

function editAppointment(appointmentId) {
    openAppointmentModal(appointmentId);
    $('#appointmentModal').modal('show');
}

function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
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
                    $('.table-ella_appointments').DataTable().ajax.reload();
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Error deleting appointment');
            }
        });
    }
}

// Save appointment
$('#saveAppointment').on('click', function() {
    var formData = $('#appointmentForm').serialize();
    
    // Debug: Log form data
    console.log('Form data:', formData);
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/save_ajax',
        type: 'POST',
        data: formData + '&' + csrf_token_name + '=' + csrf_hash,
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            if (response.success) {
                alert_float('success', response.message);
                $('#appointmentModal').modal('hide');
                $('.table-ella_appointments').DataTable().ajax.reload();
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', xhr.responseText);
            alert_float('danger', 'Error saving appointment: ' + error);
        }
    });
});
</script>