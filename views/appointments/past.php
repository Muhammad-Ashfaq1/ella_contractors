<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin">Past Appointments</h4>
                <hr class="hr-panel-heading" />
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-ella_appointments_past">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Date & Time</th>
                                        <th>Client</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

$(document).ready(function() {
    // Initialize DataTable for past appointments
    initDataTable('.table-ella_appointments_past', admin_url + 'ella_contractors/appointments/table', undefined, undefined, {past: 1}, [2, 'desc']);
    
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
                
                // Set checkboxes
                $('#approved').prop('checked', data.approved == 1);
                $('#finished').prop('checked', data.finished == 1);
                $('#cancelled').prop('checked', data.cancelled == 1);
                
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
                    $('.table-ella_appointments_past').DataTable().ajax.reload();
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
                $('.table-ella_appointments_past').DataTable().ajax.reload();
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