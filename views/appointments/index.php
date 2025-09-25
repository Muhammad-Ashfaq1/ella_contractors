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
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-filter"></i> All Appointments <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="#" data-filter="all">All Appointments</a></li>
                                                <li><a href="#" data-filter="scheduled">Scheduled</a></li>
                                                <li><a href="#" data-filter="complete">Complete</a></li>
                                                <li><a href="#" data-filter="cancelled">Cancelled</a></li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="#" data-filter="today">Today</a></li>
                                                <li><a href="#" data-filter="this_week">This Week</a></li>
                                                <li><a href="#" data-filter="this_month">This Month</a></li>
                                            </ul>
                                        </div>
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
                                        <th style="min-width: 250px;"><?php echo _l('appointment_subject'); ?></th>
                                        <th>Scheduled Date</th>
                                        <th><?php echo _l('appointment_status'); ?></th>
                                        <th width="100px">Measurements</th>
                                        <th width="100px">Estimates</th>
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

// Custom function to initialize combined AJAX search for leads and clients
function init_combined_ajax_search(selector) {
    var ajaxSelector = $(selector);
    
    if (ajaxSelector.length) {
        var options = {
            ajax: {
                url: admin_url + 'misc/get_relation_data',
                data: function () {
                    var data = {};
                    data.type = 'lead'; // Use lead type for search
                    data.rel_id = '';
                    data.q = '{{{q}}}';
                    data[csrf_token_name] = csrf_hash; // Add CSRF token
                    return data;
                }
            },
            locale: {
                emptyTitle: 'Search for clients or leads...',
                statusInitialized: 'Ready to search',
                statusSearching: 'Searching...',
                statusNoResults: 'No results found',
                searchPlaceholder: 'Type to search clients or leads...',
                currentlySelected: 'Currently selected'
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function (processData) {
                var bs_data = [];
                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        'value': processData[i].id,
                        'text': processData[i].name,
                    };
                    if (processData[i].subtext) {
                        tmp_data.data = {
                            subtext: processData[i].subtext
                        };
                    }
                    bs_data.push(tmp_data);
                }
                return bs_data;
            },
            preserveSelectedPosition: 'after',
            preserveSelected: false
        };
        
        ajaxSelector.selectpicker().ajaxSelectPicker(options);
        ajaxSelector.selectpicker('val', '');
    }
}

$(document).ready(function() {
    // Initialize DataTable for appointments
    initDataTable('.table-ella_appointments', admin_url + 'ella_contractors/appointments/table', undefined, undefined, {}, [2, 'desc']);
    
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize AJAX search for leads and clients
    init_combined_ajax_search('#contact_id.ajax-search');
    
    // Handle contact selection and populate form fields with lead/client data
    $('#contact_id').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            var splitValue = selectedValue.split('_');
            var relType = splitValue[0]; // 'lead' or 'client'
            var relId = splitValue[1];   // The ID number
            
            if (relId) {
                $.get(admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType, function(response) {
                    // Parse the JSON response
                    var data = JSON.parse(response);
                    
                    // Populate form fields with the returned data
                    $('#email').val(data.email || '');
                    $('#phone').val(data.phone || '');
                    $('#address').val(data.address || '');
                    
                    // Store validation status in hidden fields
                    if (typeof data.emailValidaionStatus !== 'undefined') {
                        $('#email_validated').val(data.emailValidaionStatus);
                    }
                    
                    if (typeof data.phoneNumberValid !== 'undefined') {
                        $('#phone_validated').val(data.phoneNumberValid);
                    }
                });
            }
        }
    });
    
    // Handle filter dropdown clicks
    $('.dropdown-menu a[data-filter]').on('click', function(e) {
        e.preventDefault();
        var filter = $(this).data('filter');
        var table = $('.table-ella_appointments').DataTable();
        
        // Update dropdown button text
        $(this).closest('.dropdown-menu').prev('.dropdown-toggle').html('<i class="fa fa-filter"></i> ' + $(this).text() + ' <span class="caret"></span>');
        
        // Clear existing column searches
        table.columns().search('');
        
        // Build filter URL
        var filterUrl = admin_url + 'ella_contractors/appointments/table';
        var params = [];
        
        if (filter !== 'all') {
            if (['scheduled', 'complete', 'cancelled'].indexOf(filter) !== -1) {
                params.push('status_filter=' + encodeURIComponent(filter));
            } else if (filter === 'today') {
                params.push('date_filter=today');
            } else if (filter === 'this_week') {
                params.push('date_filter=this_week');
            } else if (filter === 'this_month') {
                params.push('date_filter=this_month');
            }
        }
        
        if (params.length > 0) {
            filterUrl += '?' + params.join('&');
        }
        
        // Update table URL and reload
        table.ajax.url(filterUrl);
        table.ajax.reload();
    });
});

// Global functions for modal operations
function openAppointmentModal(appointmentId = null) {
    if ($('#appointmentForm').length === 0) {
        return;
    }
    
    // Reset form
    $('#appointmentForm')[0].reset();
    $('#appointment_id').val('');
    $('#appointmentModalLabel').text('Create Appointment');
    
    // Set today's date as default (only for new appointments)
    if (!appointmentId) {
        var today = '<?php echo date('Y-m-d\TH:i'); ?>';
        $('#start_datetime').val(today);
        $('#end_datetime').val(today);
    }
    
    // Refresh selectpicker
    $('.selectpicker').selectpicker('refresh');
    
    // Refresh AJAX search
    $('#contact_id.ajax-search').selectpicker('refresh');
    
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
            if (response.success) {
                var data = response.data;
                
                // Populate form fields
                $('#appointment_id').val(data.id);
                $('#subject').val(data.subject);
                
                // Handle combined datetime fields
                var startDateTime = '';
                var endDateTime = '';
                
                if (data.start_date && data.start_time) {
                    startDateTime = data.start_date + 'T' + data.start_time;
                } else if (data.date && data.start_hour) {
                    startDateTime = data.date + 'T' + data.start_hour;
                }
                
                if (data.end_date && data.end_time) {
                    endDateTime = data.end_date + 'T' + data.end_time;
                } else if (data.date && data.start_hour) {
                    // For end time, add 1 hour to start time as default
                    var endTime = new Date(data.date + 'T' + data.start_hour);
                    endTime.setHours(endTime.getHours() + 1);
                    endDateTime = endTime.toISOString().slice(0, 16);
                }
                
                $('#start_datetime').val(startDateTime);
                $('#end_datetime').val(endDateTime);
                
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#notes').val(data.notes);
                $('#type_id').val(data.type_id);
                
                // Handle reminder checkbox
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Handle contact selection - determine if it's a client or lead
                if (data.contact_id) {
                    if (data.client_name) {
                        // It's a client - format: client_userid
                        var clientValue = 'client_' + data.contact_id;
                        $('#contact_id').val(clientValue);
                    } else if (data.lead_name) {
                        // It's a lead - format: lead_id
                        var leadValue = 'lead_' + data.contact_id;
                        $('#contact_id').val(leadValue);
                    } else {
                        // Fallback - try to find the contact_id in the dropdown
                        // Check if it exists as client or lead
                        var clientOption = $('#contact_id option[value="client_' + data.contact_id + '"]');
                        var leadOption = $('#contact_id option[value="lead_' + data.contact_id + '"]');
                        
                        if (clientOption.length > 0) {
                            $('#contact_id').val('client_' + data.contact_id);
                        } else if (leadOption.length > 0) {
                            $('#contact_id').val('lead_' + data.contact_id);
                        } else {
                            $('#contact_id').val('');
                        }
                    }
                } else {
                    $('#contact_id').val('');
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
                
                // Refresh AJAX search after setting value
                $('#contact_id.ajax-search').selectpicker('refresh');
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error loading appointment data: ' + error);
        }
    });
}

function loadAppointmentDataAndShowModal(appointmentId) {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_data',
        type: 'POST',
        data: {
            id: appointmentId,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                
                // Populate form fields
                $('#appointment_id').val(data.id);
                $('#subject').val(data.subject);
                
                // Handle combined datetime fields
                var startDateTime = '';
                var endDateTime = '';
                
                if (data.start_date && data.start_time) {
                    startDateTime = data.start_date + 'T' + data.start_time;
                } else if (data.date && data.start_hour) {
                    startDateTime = data.date + 'T' + data.start_hour;
                }
                
                if (data.end_date && data.end_time) {
                    endDateTime = data.end_date + 'T' + data.end_time;
                } else if (data.date && data.start_hour) {
                    // For end time, add 1 hour to start time as default
                    var endTime = new Date(data.date + 'T' + data.start_hour);
                    endTime.setHours(endTime.getHours() + 1);
                    endDateTime = endTime.toISOString().slice(0, 16);
                }
                
                $('#start_datetime').val(startDateTime);
                $('#end_datetime').val(endDateTime);
                
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#notes').val(data.notes);
                $('#type_id').val(data.type_id);
                
                // Handle reminder checkbox
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Handle contact selection - determine if it's a client or lead
                if (data.contact_id) {
                    if (data.client_name) {
                        // It's a client - format: client_userid
                        var clientValue = 'client_' + data.contact_id;
                        $('#contact_id').val(clientValue);
                    } else if (data.lead_name) {
                        // It's a lead - format: lead_id
                        var leadValue = 'lead_' + data.contact_id;
                        $('#contact_id').val(leadValue);
                    } else {
                        // Fallback - try to find the contact_id in the dropdown
                        // Check if it exists as client or lead
                        var clientOption = $('#contact_id option[value="client_' + data.contact_id + '"]');
                        var leadOption = $('#contact_id option[value="lead_' + data.contact_id + '"]');
                        
                        if (clientOption.length > 0) {
                            $('#contact_id').val('client_' + data.contact_id);
                        } else if (leadOption.length > 0) {
                            $('#contact_id').val('lead_' + data.contact_id);
                        } else {
                            $('#contact_id').val('');
                        }
                    }
                } else {
                    $('#contact_id').val('');
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
                $('#appointmentModal').modal('show');
                
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error loading appointment data: ' + error);
        }
    });
}

function editAppointment(appointmentId) {
    if (!appointmentId) {
        alert_float('danger', 'Invalid appointment ID');
        return;
    }
    
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
    // Split datetime fields into separate date and time fields
    var startDateTime = $('#start_datetime').val();
    var endDateTime = $('#end_datetime').val();
    
    if (startDateTime) {
        var startParts = startDateTime.split('T');
        $('#start_date').val(startParts[0]);
        $('#start_time').val(startParts[1]);
    }
    
    if (endDateTime) {
        var endParts = endDateTime.split('T');
        $('#end_date').val(endParts[0]);
        $('#end_time').val(endParts[1]);
    }
    
    // Get form data
    var formData = $('#appointmentForm').serialize();
    
    // Add CSRF token
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/save_ajax',
        type: 'POST',
        data: formData + '&' + csrf_token_name + '=' + csrf_hash,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                $('#appointmentModal').modal('hide');
                $('.table-ella_appointments').DataTable().ajax.reload();
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error saving appointment: ' + error);
        }
    });
});
</script>

<!-- Include global appointment.js for lead modal functionality -->
<script src="<?php echo base_url('assets/js/global/appointment.js'); ?>"></script>