<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info" onclick="console.log('Button clicked'); openAppointmentModal(); $('#appointmentModal').modal('show');">
                                        <i class="fa fa-plus"></i>New Appointment
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pull-right">
                                        <!-- <label for="statusFilter" style="margin-right: 10px;">Filter by Status:</label> -->
                                        <select id="statusFilter" class="form-control selectpicker" data-live-search="true" style="width: 200px; display: inline-block;">
                                            <option value="">All Appointments</option>
                                            <option value="scheduled">Scheduled</option>
                                            <option value="complete">Complete</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
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
    
    // Handle status filter change
    $('#statusFilter').on('change', function() {
        var status = $(this).val();
        var table = $('.table-ella_appointments').DataTable();
        
        console.log('Status filter changed to:', status);
        
        // Clear existing column searches
        table.columns().search('');
        
        // Apply status filter using custom parameter approach
        if (status !== '') {
            // Use the custom parameter approach instead of column search
            table.ajax.url(admin_url + 'ella_contractors/appointments/table?status_filter=' + encodeURIComponent(status));
            console.log('Applied status filter "' + status + '" via custom parameter');
        } else {
            // Reset to original URL without filter
            table.ajax.url(admin_url + 'ella_contractors/appointments/table');
            console.log('Cleared status filter');
        }
        
        // Reload the table with new URL
        table.ajax.reload();
    });
});

// Global functions for modal operations
function openAppointmentModal(appointmentId = null) {
    if ($('#appointmentForm').length === 0) {
        console.error('Appointment form not found!');
        return;
    }
    
    // Reset form
    $('#appointmentForm')[0].reset();
    $('#appointment_id').val('');
    $('#appointmentModalLabel').text('Create Appointment');
    
    // Set today's date as default (only for new appointments)
    if (!appointmentId) {
        $('#date').val('<?php echo date('Y-m-d'); ?>');
    }
    
    // Refresh selectpicker
    $('.selectpicker').selectpicker('refresh');
    
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
            console.log('Appointment data response:', response);
            
            if (response.success) {
                var data = response.data;
                console.log('Appointment data:', data);
                
                // Populate form fields
                $('#appointment_id').val(data.id);
                $('#subject').val(data.subject);
                $('#date').val(data.date);
                $('#start_hour').val(data.start_hour);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#description').val(data.description);
                $('#notes').val(data.notes);
                $('#type_id').val(data.type_id);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Handle contact selection - determine if it's a client or lead
                if (data.contact_id) {
                    if (data.client_name) {
                        // It's a client - format: client_userid
                        var clientValue = 'client_' + data.contact_id;
                        $('#contact_id').val(clientValue);
                        console.log('Setting client value:', clientValue);
                    } else if (data.lead_name) {
                        // It's a lead - format: lead_id
                        var leadValue = 'lead_' + data.contact_id;
                        $('#contact_id').val(leadValue);
                        console.log('Setting lead value:', leadValue);
                    } else {
                        // Fallback - try to find the contact_id in the dropdown
                        // Check if it exists as client or lead
                        var clientOption = $('#contact_id option[value="client_' + data.contact_id + '"]');
                        var leadOption = $('#contact_id option[value="lead_' + data.contact_id + '"]');
                        
                        if (clientOption.length > 0) {
                            $('#contact_id').val('client_' + data.contact_id);
                            console.log('Found client option, setting:', 'client_' + data.contact_id);
                        } else if (leadOption.length > 0) {
                            $('#contact_id').val('lead_' + data.contact_id);
                            console.log('Found lead option, setting:', 'lead_' + data.contact_id);
                        } else {
                            $('#contact_id').val('');
                            console.log('No matching option found, clearing contact');
                        }
                    }
                } else {
                    $('#contact_id').val('');
                    console.log('No contact_id, clearing contact');
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

function loadAppointmentDataAndShowModal(appointmentId) {
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
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#description').val(data.description);
                $('#notes').val(data.notes);
                $('#type_id').val(data.type_id);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Handle contact selection - determine if it's a client or lead
                console.log('Contact data:', {
                    contact_id: data.contact_id,
                    client_name: data.client_name,
                    lead_name: data.lead_name
                });
                
                if (data.contact_id) {
                    if (data.client_name) {
                        // It's a client - format: client_userid
                        var clientValue = 'client_' + data.contact_id;
                        $('#contact_id').val(clientValue);
                        console.log('Setting client value:', clientValue);
                    } else if (data.lead_name) {
                        // It's a lead - format: lead_id
                        var leadValue = 'lead_' + data.contact_id;
                        $('#contact_id').val(leadValue);
                        console.log('Setting lead value:', leadValue);
                    } else {
                        // Fallback - try to find the contact_id in the dropdown
                        // Check if it exists as client or lead
                        var clientOption = $('#contact_id option[value="client_' + data.contact_id + '"]');
                        var leadOption = $('#contact_id option[value="lead_' + data.contact_id + '"]');
                        
                        if (clientOption.length > 0) {
                            $('#contact_id').val('client_' + data.contact_id);
                            console.log('Found client option, setting:', 'client_' + data.contact_id);
                        } else if (leadOption.length > 0) {
                            $('#contact_id').val('lead_' + data.contact_id);
                            console.log('Found lead option, setting:', 'lead_' + data.contact_id);
                        } else {
                            $('#contact_id').val('');
                            console.log('No matching option found, clearing contact');
                        }
                    }
                } else {
                    $('#contact_id').val('');
                    console.log('No contact_id, clearing contact');
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
                console.log('Data loaded successfully, showing modal');
                $('#appointmentModal').modal('show');
                
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
    if (!appointmentId) {
        alert_float('danger', 'Invalid appointment ID');
        return;
    }
    
    console.log('Edit appointment called with ID:', appointmentId);
    
    // Reset form first
    $('#appointmentForm')[0].reset();
    $('#appointment_id').val('');
    $('#appointmentModalLabel').text('Edit Appointment');
    
    // Set today's date as default
    $('#date').val('<?php echo date('Y-m-d'); ?>');
    
    // Refresh selectpicker
    $('.selectpicker').selectpicker('refresh');
    
    // Load appointment data and then show modal
    loadAppointmentDataAndShowModal(appointmentId);
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
    
    // Debug: Log form data and status specifically
    console.log('Form data:', formData);
    console.log('Status field value:', $('#status').val());
    console.log('Status field selected option:', $('#status option:selected').val());
    
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

<!-- Include global appointment.js for lead modal functionality -->
<script src="<?php echo base_url('assets/js/global/appointment.js'); ?>"></script>