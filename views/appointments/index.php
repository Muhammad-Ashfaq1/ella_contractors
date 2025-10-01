<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- Load module CSS -->
<!-- <link rel="stylesheet" href="<?php echo module_dir_url('ella_contractors', 'assets/css/ella-contractors.css'); ?>"> -->

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
                                        <i class="fa fa-plus" style="margin-right: 2% !important;"></i> New Appointment
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
                                                <li><a href="#" data-filter="scheduled"><?php echo strtoupper(_l('scheduled')); ?></a></li>
                                                <li><a href="#" data-filter="complete"><?php echo strtoupper(_l('complete')); ?></a></li>
                                                <li><a href="#" data-filter="cancelled"><?php echo strtoupper(_l('cancelled')); ?></a></li>
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
                                        <th class="text-center"><?php echo _l('id'); ?></th>
                                        <th class="text-center">Lead</th>
                                        <th class="text-center" style="min-width: 250px;">Appointment Name</th>
                                        <th class="text-center">Scheduled</th>
                                        <th class="text-center"><?php echo _l('appointment_status'); ?></th>
                                        <th class="text-center" width="100px">Measurements</th>
                                        <th class="text-center" width="100px">Estimates</th>
                                        <th class="text-center" width="120px"><?php echo _l('options'); ?></th>
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

/* Simple Status Dropdown Styling */
.status-wrapper {
    position: relative;
    display: inline-block;
}

.status-button {
    cursor: pointer !important;
    transition: opacity 0.2s ease;
    font-size: 13px;
    padding: 10px 18px !important;
    font-weight: 700;
    min-width: 110px;
    text-align: center;
    display: inline-block;
    border-radius: 4px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.status-button:hover {
    cursor: pointer !important;
    opacity: 0.8;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}

.status-dropdown {
    position: absolute;
    top: 0;
    right: 100%;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    min-width: 120px;
}

.status-option {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s ease;
}

.status-option:hover {
    background-color: #f5f5f5;
}

.status-option:last-child {
    border-bottom: none;
}
.table-ella_appointments td {
    vertical-align: middle;
}
/* Clickable badge styling */
.table-ella_appointments .label a,
.table-ella_appointments .text-muted a {
    color: inherit;
    text-decoration: none;
    cursor: pointer !important;
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    transition: all 0.2s ease;
}

.table-ella_appointments .label a:hover,
.table-ella_appointments .text-muted a:hover {
    color: inherit;
    text-decoration: none;
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
    background-color: rgba(0,0,0,0.1);
}

/* Specific styling for measurements and estimates blocks */
.table-ella_appointments .label.label-info a,
.table-ella_appointments .label.label-success a {
    cursor: pointer !important;
}

.table-ella_appointments .text-muted a {
    cursor: pointer !important;
}

/* Ensure the entire badge area is clickable */
.table-ella_appointments .text-center a {
    cursor: pointer !important;
    display: inline-block;
    width: 100%;
    text-align: center;
}

/* Additional hover effects for better UX */
.table-ella_appointments .label.label-info:hover,
.table-ella_appointments .label.label-success:hover {
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.table-ella_appointments .text-muted:hover {
    cursor: pointer;
    opacity: 0.7;
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
                    data.type = 'lead'; // Search leads
                    data.rel_id = '';
                    data.q = '{{{q}}}';
                    data[csrf_token_name] = csrf_hash; // Add CSRF token
                    return data;
                }
            },
            locale: {
                emptyTitle: 'Search for leads...',
                statusInitialized: 'Ready to search',
                statusSearching: 'Searching...',
                statusNoResults: 'No results found',
                searchPlaceholder: 'Type to search leads...',
                currentlySelected: 'Currently selected'
            },
            requestDelay: 500,
            cache: false,
            preprocessData: function (processData) {
                var bs_data = [];
                var len = processData.length;
                for (var i = 0; i < len; i++) {
                    var tmp_data = {
                        'value': 'lead_' + processData[i].id, // Add lead_ prefix
                        'text': processData[i].name + ' (Lead)',
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
    
    // Check for edit parameter in URL
    var urlParams = new URLSearchParams(window.location.search);
    var editId = urlParams.get('edit');
    if (editId) {
        // Open modal for editing
        openAppointmentModal(editId);
        // Clean up URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Handle contact selection and populate form fields with lead/client data
    $('#contact_id').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue) {
            var splitValue = selectedValue.split('_');
            var relType = splitValue[0]; // 'lead' or 'client'
            var relId = splitValue[1];   // The ID number
            
            if (relId) {
                $.ajax({
                    url: admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            alert_float('danger', data.error);
                            return;
                        }
                        
                        // Only populate form fields if they are empty
                        if (!$('#email').val() && data.email) {
                            $('#email').val(data.email);
                        }
                        if (!$('#phone').val() && data.phone) {
                            $('#phone').val(data.phone);
                        }
                        if (!$('#address').val() && data.address) {
                            $('#address').val(data.address);
                        }
                        
                        // Store validation status in hidden fields
                        if (typeof data.emailValidaionStatus !== 'undefined') {
                            $('#email_validated').val(data.emailValidaionStatus);
                        }
                        
                        if (typeof data.phoneNumberValid !== 'undefined') {
                            $('#phone_validated').val(data.phoneNumberValid);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert_float('danger', 'Error loading lead information');
                    }
                });
            }
        } else {
            // Clear form fields if no contact is selected
            $('#email').val('');
            $('#phone').val('');
            $('#address').val('');
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
    resetAppointmentModal();
    
    // Set today's date as default (only for new appointments)
    if (!appointmentId) {
        var today = '<?php echo date('Y-m-d\TH:i'); ?>';
        $('#start_datetime').val(today);
        $('#end_datetime').val(today);
    }
    
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
                
                // Re-initialize AJAX search for contact dropdown
                $('#contact_id').off('change'); // Remove existing change handler
                init_combined_ajax_search('#contact_id.ajax-search');
                
                // Re-add the change handler
                $('#contact_id').on('change', function() {
                    var selectedValue = $(this).val();
                    if (selectedValue) {
                        var splitValue = selectedValue.split('_');
                        var relType = splitValue[0]; // 'lead' or 'client'
                        var relId = splitValue[1];   // The ID number
                        
                        if (relId) {
                            $.ajax({
                                url: admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if (data.error) {
                                        alert_float('danger', data.error);
                                        return;
                                    }
                                    
                                    // Only populate form fields if they are empty
                                    if (!$('#email').val() && data.email) {
                                        $('#email').val(data.email);
                                    }
                                    if (!$('#phone').val() && data.phone) {
                                        $('#phone').val(data.phone);
                                    }
                                    if (!$('#address').val() && data.address) {
                                        $('#address').val(data.address);
                                    }
                                    
                                    // Store validation status in hidden fields
                                    if (typeof data.emailValidaionStatus !== 'undefined') {
                                        $('#email_validated').val(data.emailValidaionStatus);
                                    }
                                    
                                    if (typeof data.phoneNumberValid !== 'undefined') {
                                        $('#phone_validated').val(data.phoneNumberValid);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert_float('danger', 'Error loading lead information');
                                }
                            });
                        }
                    } else {
                        // Clear form fields if no contact is selected
                        $('#email').val('');
                        $('#phone').val('');
                        $('#address').val('');
                    }
                });
                
                // Re-set the contact value after AJAX search is initialized
                if (data.contact_dropdown_value && data.contact_display_name) {
                    setTimeout(function() {
                        var contactOption = '<option value="' + data.contact_dropdown_value + '">' + data.contact_display_name + ' (' + data.contact_type + ')</option>';
                        $('#contact_id').append(contactOption);
                        $('#contact_id').selectpicker('val', data.contact_dropdown_value);
                    }, 100);
                }
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
                
                // Re-initialize AJAX search for contact dropdown
                $('#contact_id').off('change'); // Remove existing change handler
                init_combined_ajax_search('#contact_id.ajax-search');
                
                // Re-add the change handler
                $('#contact_id').on('change', function() {
                    var selectedValue = $(this).val();
                    if (selectedValue) {
                        var splitValue = selectedValue.split('_');
                        var relType = splitValue[0]; // 'lead' or 'client'
                        var relId = splitValue[1];   // The ID number
                        
                        if (relId) {
                            $.ajax({
                                url: admin_url + 'ella_contractors/appointments/get_relation_data_values/' + relId + '/' + relType,
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if (data.error) {
                                        alert_float('danger', data.error);
                                        return;
                                    }
                                    
                                    // Only populate form fields if they are empty
                                    if (!$('#email').val() && data.email) {
                                        $('#email').val(data.email);
                                    }
                                    if (!$('#phone').val() && data.phone) {
                                        $('#phone').val(data.phone);
                                    }
                                    if (!$('#address').val() && data.address) {
                                        $('#address').val(data.address);
                                    }
                                    
                                    // Store validation status in hidden fields
                                    if (typeof data.emailValidaionStatus !== 'undefined') {
                                        $('#email_validated').val(data.emailValidaionStatus);
                                    }
                                    
                                    if (typeof data.phoneNumberValid !== 'undefined') {
                                        $('#phone_validated').val(data.phoneNumberValid);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert_float('danger', 'Error loading lead information');
                                }
                            });
                        }
                    } else {
                        // Clear form fields if no contact is selected
                        $('#email').val('');
                        $('#phone').val('');
                        $('#address').val('');
                    }
                });
                
                // Re-set the contact value after AJAX search is initialized
                if (data.contact_dropdown_value && data.contact_display_name) {
                    setTimeout(function() {
                        var contactOption = '<option value="' + data.contact_dropdown_value + '">' + data.contact_display_name + ' (' + data.contact_type + ')</option>';
                        $('#contact_id').append(contactOption);
                        $('#contact_id').selectpicker('val', data.contact_dropdown_value);
                    }, 100);
                }
                
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

// Reset appointment modal to default state
function resetAppointmentModal() {
    $('#appointmentForm')[0].reset();
    $('#appointment_id').val('');
    $('#appointmentModalLabel').text('Create Appointment');
    $('#contact_id').html('<option value="">Select Client/Lead</option>');
    $('#contact_id').selectpicker('val', '');
    $('.selectpicker').selectpicker('refresh');
    
    // Clear uploaded files
    appointmentFiles = [];
    appointmentId = null;
    $('#appointment_uploaded_files').val('');
    
    // Clear thumbnails
    $('#appointmentThumbnails').empty();
    $('.drop-zone__thumb').remove();
    $('.drop-zone__prompt').show();
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
    // Client-side validation
    if (!$('#subject').val()) {
        alert_float('danger', 'Subject is required');
        return;
    }
    
    if (!$('#start_datetime').val()) {
        alert_float('danger', 'Start date & time is required');
        return;
    }
    
    if (!$('#end_datetime').val()) {
        alert_float('danger', 'End date & time is required');
        return;
    }
    
    if (!$('#contact_id').val()) {
        alert_float('danger', 'Please select a lead or client');
        return;
    }
    
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
            console.log('response', response);
            if (response.success) {
                // Store appointment ID for file uploads
                appointmentId = response.appointment_id || response.data?.id;
                
                // Upload files if any are selected
                if (appointmentFiles.length > 0 && appointmentId) {
                    uploadAppointmentFiles(appointmentId);
                } else {
                    // No files to upload, show success message
                    alert_float('success', response.message);
                    $('#appointmentModal').modal('hide');
                    resetAppointmentModal();
                    $('.table-ella_appointments').DataTable().ajax.reload();
                }
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error saving appointment: ' + error);
        }
    });
});

// ========================================
// APPOINTMENT DROPZONE FUNCTIONALITY START
// ========================================

// Global variables for file management
var appointmentFiles = [];
var appointmentId = null;

// Function to upload appointment files
function uploadAppointmentFiles(appointmentId) {
    if (appointmentFiles.length === 0) {
        return;
    }
    
    var formData = new FormData();
    formData.append('appointment_id', appointmentId);
    formData.append(csrf_token_name, csrf_hash);
    
    // Add each file to FormData
    appointmentFiles.forEach(function(file, index) {
        formData.append('appointment_files[]', file);
    });
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/upload_files',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
            } else {
                alert_float('warning', 'Appointment saved but files upload failed: ' + response.message);
            }
            $('#appointmentModal').modal('hide');
            resetAppointmentModal();
            $('.table-ella_appointments').DataTable().ajax.reload();
        },
        error: function(xhr, status, error) {
            alert_float('warning', 'Appointment saved but files upload failed: ' + error);
            $('#appointmentModal').modal('hide');
            resetAppointmentModal();
            $('.table-ella_appointments').DataTable().ajax.reload();
        }
    });
}

function getFileIdentifier(file) {
    return file.name + '-' + file.size + '-' + file.lastModified;
}

function updateInputFiles(inputElement) {
    const dt = new DataTransfer();
    if (appointmentFiles.length > 0) {
        appointmentFiles.forEach(file => {
            dt.items.add(file);
        });
    }
    inputElement.files = dt.files;
}

function addNewFiles(newFiles, inputElement, dropZoneElement) {
    // Check file types (allow common document and image types)
    const allowedFileTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];
    
    const invalidFiles = newFiles.filter(file => !allowedFileTypes.includes(file.type));
    
    if (invalidFiles.length > 0) {
        showMessage('Invalid file type. Only images, PDFs, and Office documents are allowed.', dropZoneElement);
        return;
    }
    
    // Check total file size (max 50MB for appointments)
    const maxSize = 50 * 1024 * 1024; // 50 MB in bytes
    const totalSize = appointmentFiles.reduce((acc, file) => acc + file.size, 0) +
        newFiles.reduce((acc, file) => acc + file.size, 0);
    
    if (totalSize > maxSize) {
        showMessage('Total file size exceeds the maximum limit of 50 MB.', dropZoneElement);
        return;
    }
    
    // Add new files
    appointmentFiles = appointmentFiles.concat(newFiles);
    updateInputFiles(inputElement);
    newFiles.forEach(file => {
        updateThumbnail(dropZoneElement, file, inputElement);
    });
    
    // Update the hidden field with file count
    $('#appointment_uploaded_files').val(appointmentFiles.length);
}

function showMessage(message, dropZoneElement) {
    let messageElement = document.createElement('div');
    messageElement.textContent = message;
    messageElement.classList.add('upload-message');
    
    dropZoneElement.appendChild(messageElement);
    setTimeout(() => {
        messageElement.remove();
    }, 5000);
}

function updateThumbnail(dropZoneElement, file, inputElement) {
    let thumbnailElement = document.createElement("div");
    thumbnailElement.classList.add("drop-zone__thumb");
    
    let thumbnailLabel = document.createElement("div");
    thumbnailLabel.textContent = file.name;
    thumbnailElement.appendChild(thumbnailLabel);
    
    // Show thumbnail for image files
    if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            let img = document.createElement("img");
            img.src = reader.result;
            img.alt = file.name;
            thumbnailElement.appendChild(img);
        };
    }
    // Show icon for PDF files
    else if (file.type === "application/pdf") {
        let pdfIcon = document.createElement("i");
        pdfIcon.classList.add("fa", "fa-file-pdf-o");
        pdfIcon.style.fontSize = "48px";
        pdfIcon.style.color = "#dc3545";
        pdfIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(pdfIcon);
    }
    // Show icon for Office documents
    else if (file.type.includes("word") || file.type.includes("document")) {
        let docIcon = document.createElement("i");
        docIcon.classList.add("fa", "fa-file-word-o");
        docIcon.style.fontSize = "48px";
        docIcon.style.color = "#2b579a";
        docIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(docIcon);
    }
    else if (file.type.includes("excel") || file.type.includes("spreadsheet")) {
        let xlsIcon = document.createElement("i");
        xlsIcon.classList.add("fa", "fa-file-excel-o");
        xlsIcon.style.fontSize = "48px";
        xlsIcon.style.color = "#217346";
        xlsIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(xlsIcon);
    }
    else if (file.type.includes("powerpoint") || file.type.includes("presentation")) {
        let pptIcon = document.createElement("i");
        pptIcon.classList.add("fa", "fa-file-powerpoint-o");
        pptIcon.style.fontSize = "48px";
        pptIcon.style.color = "#d24726";
        pptIcon.style.marginTop = "30px";
        thumbnailElement.appendChild(pptIcon);
    }
    
    addDeleteButton(thumbnailElement, file, dropZoneElement, inputElement);
    
    // Add to thumbnails container
    const thumbnailsContainer = dropZoneElement.querySelector("#appointmentThumbnails");
    if (thumbnailsContainer) {
        thumbnailsContainer.appendChild(thumbnailElement);
    } else {
        // Fallback to old method
        const uploadPrompt = dropZoneElement.querySelector(".drop-zone__prompt");
        dropZoneElement.insertBefore(thumbnailElement, uploadPrompt);
    }
}

function addDeleteButton(thumbnailElement, file, dropZoneElement, inputElement) {
    let deleteIcon = document.createElement("i");
    deleteIcon.classList.add("fa", "fa-close");
    
    let deleteButton = document.createElement("button");
    deleteButton.classList.add("delete-btn");
    deleteButton.appendChild(deleteIcon);
    
    thumbnailElement.appendChild(deleteButton);
    
    deleteButton.addEventListener('click', function(event) {
        event.stopPropagation();
        
        // Remove file from appointmentFiles array
        appointmentFiles = appointmentFiles.filter(f => f !== file);
        updateInputFiles(inputElement);
        thumbnailElement.remove();
        
        // Update the hidden field with file count
        $('#appointment_uploaded_files').val(appointmentFiles.length);
        
        // Show prompt if no more thumbnails
        const thumbnailsContainer = dropZoneElement.querySelector("#appointmentThumbnails");
        if (thumbnailsContainer && thumbnailsContainer.children.length === 0) {
            const prompt = dropZoneElement.querySelector(".drop-zone__prompt");
            if (prompt) {
                prompt.style.display = 'block';
            }
        }
    });
}

function applyAppointmentEventListeners() {
    document.querySelectorAll("#appointment_files").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");
        
        // Click event to trigger file select
        dropZoneElement.addEventListener("click", () => {
            inputElement.click();
        });
        
        // File input change event
        inputElement.addEventListener("change", () => {
            const existingFileIdentifiers = appointmentFiles.map(getFileIdentifier);
            const newFiles = Array.from(inputElement.files).filter(
                file => !existingFileIdentifiers.includes(getFileIdentifier(file))
            );
            if (newFiles.length) {
                addNewFiles(newFiles, inputElement, dropZoneElement);
            }
        });
        
        // Drag events
        dropZoneElement.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });
        
        ["dragleave", "dragend"].forEach((type) => {
            dropZoneElement.addEventListener(type, () => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });
        
        // Drop event
        dropZoneElement.addEventListener("drop", (e) => {
            e.preventDefault();
            dropZoneElement.classList.remove("drop-zone--over");
            
            const droppedFiles = Array.from(e.dataTransfer.files);
            const existingFileIdentifiers = appointmentFiles.map(getFileIdentifier);
            const newFiles = droppedFiles.filter(
                file => !existingFileIdentifiers.includes(getFileIdentifier(file))
            );
            
            if (newFiles.length) {
                addNewFiles(newFiles, inputElement, dropZoneElement);
            }
        });
    });
}

// Initialize dropzone when document is ready
$(document).ready(function() {
    // Initialize selectpickers
    $('.selectpicker').selectpicker();
    
    // Initialize dropzone event listeners
    applyAppointmentEventListeners();
    
    // Handle file upload form submission
    $('#appointmentFileUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var send_url = admin_url + 'upload_image/appointment_upload';
        
        $.ajax({
            type: 'POST',
            url: send_url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log('File upload success:', data);
                var currentImagesResponse = $('.appointment_imagesresponse').val();
                
                if (currentImagesResponse) {
                    currentImagesResponse += ',' + data;
                } else {
                    currentImagesResponse = data;
                }
                
                $('.appointment_imagesresponse').val(currentImagesResponse);
            },
            error: function(data) {
                console.log('File upload error:', data);
                showMessage('Error uploading file. Please try again.', $('.drop-zone')[0]);
            }
        });
    });
    
    // Handle file input change for immediate upload
    $("#appointment_media_image").on("change", function() {
        $("#appointmentFileUploadForm").submit();
        $('.campaign_type').val('appointment');
    });
    
    // Re-initialize dropzone when modal is shown (in case it's loaded dynamically)
    $('#appointmentModal').on('shown.bs.modal', function() {
        applyAppointmentEventListeners();
        
        // Initialize selectpickers
        $('.selectpicker').selectpicker('refresh');
    });
});

// ========================================
// APPOINTMENT DROPZONE FUNCTIONALITY END
// ========================================

// ========================================
// APPOINTMENT STATUS DROPDOWN FUNCTIONALITY
// ========================================

/**
 * Update appointment status via AJAX (similar to lead_mark_as function)
 * @param {string} status - The new status (scheduled, complete, cancelled)
 * @param {number} appointment_id - The appointment ID
 */
function appointment_mark_as(status, appointment_id) {
    var data = {};
    data.status = status;
    data.appointment_id = appointment_id;
    
    // Show loading indicator
    var statusElement = $('#status-btn-' + appointment_id);
    var originalContent = statusElement.html();
    statusElement.html('<i class="fa fa-spinner fa-spin"></i> Updating...');
    
    // Hide status menu immediately
    $('#status-menu-' + appointment_id).hide();
    
    $.post(admin_url + 'ella_contractors/appointments/update_appointment_status', data)
    .done(function (response) {
        var result = JSON.parse(response);
        
        if (result.success) {
            // Show success message
            alert_float(result.class, result.message);
            
            // Update the status cell in place instead of reloading entire table
            updateAppointmentStatusInPlace(appointment_id, status);
        } else {
            // Show error message
            alert_float(result.class, result.message);
            
            // Restore original content
            statusElement.html(originalContent);
        }
    })
    .fail(function (xhr, status, error) {
        // Show error message
        alert_float('danger', 'Failed to update appointment status. Please try again.');
        
        // Restore original content
        statusElement.html(originalContent);
        
        console.error('Appointment status update failed:', error);
    });
}

/**
 * Update appointment status display in place without reloading the table
 * @param {number} appointment_id - The appointment ID
 * @param {string} newStatus - The new status value
 */
function updateAppointmentStatusInPlace(appointment_id, newStatus) {
    // Get the DataTable instance
    var table = $('.table-ella_appointments').DataTable();
    
    // Check if DataTable is properly initialized
    if (!table || typeof table.rows === 'undefined') {
        console.warn('DataTable not properly initialized, reloading table...');
        location.reload();
        return;
    }
    
    // Find the row with the matching appointment ID
    var rowIndex = -1;
    table.rows().every(function(rowIdx, data, node) {
        if (data[1] == appointment_id) { // Column 1 is the ID column
            rowIndex = rowIdx;
            return false; // Break the loop
        }
    });
    
    if (rowIndex !== -1) {
        // Get the current row data
        var rowData = table.row(rowIndex).data();
        
        // Update the status in the row data (column 5 is the status column)
        rowData[5] = newStatus;
        
        // Generate new status HTML
        var statusHtml = generateStatusHtml(newStatus, appointment_id);
        
        // Update the status cell directly in the DOM
        var statusCell = table.cell(rowIndex, 5).node();
        $(statusCell).html('<div class="text-center">' + statusHtml + '</div>');
        
        // Update the row data in DataTable
        table.row(rowIndex).data(rowData).draw(false);
        
        // Reinitialize tooltips for the updated status element
        $('[data-toggle="tooltip"]').tooltip();
    } else {
        // Fallback: if row not found, reload the table
        console.warn('Row not found for appointment ID:', appointment_id, 'reloading table...');
        table.ajax.reload(null, false);
    }
}

/**
 * Generate status HTML for the given status value
 * @param {string} status - The status value
 * @param {number} appointment_id - The appointment ID
 * @returns {string} HTML string for the status display
 */
function generateStatusHtml(status, appointment_id) {
    var statusClass = '';
    var statusLabel = '';
    var hasPermission = <?php echo has_permission('ella_contractors', '', 'edit') ? 'true' : 'false'; ?>;
    
    // Determine status class and label based on status value
    switch (status) {
        case 'cancelled':
            statusClass = 'label-danger';
            statusLabel = '<?php echo strtoupper(_l('cancelled')); ?>';
            break;
        case 'complete':
            statusClass = 'label-success';
            statusLabel = '<?php echo strtoupper(_l('complete')); ?>';
            break;
        case 'scheduled':
            statusClass = 'label-info';
            statusLabel = '<?php echo strtoupper(_l('scheduled')); ?>';
            break;
        default:
            statusClass = 'label-warning';
            statusLabel = status.toUpperCase();
    }
    
    // Create status display HTML - simple approach
    var statusHtml = '<div class="status-wrapper" style="position: relative; display: inline-block;">';
    statusHtml += '<span class="status-button label ' + statusClass + '" id="status-btn-' + appointment_id + '" style="cursor: pointer !important;">';
    statusHtml += statusLabel;
    statusHtml += '</span>';
    
    // Dropdown menu positioned on the left side
    if (hasPermission) {
        statusHtml += '<div id="status-menu-' + appointment_id + '" class="status-dropdown" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 120px;">';
        
        var availableStatuses = [
            {value: 'scheduled', label: '<?php echo strtoupper(_l('scheduled')); ?>'},
            {value: 'complete', label: '<?php echo strtoupper(_l('complete')); ?>'},
            {value: 'cancelled', label: '<?php echo strtoupper(_l('cancelled')); ?>'}
        ];
        
        for (var i = 0; i < availableStatuses.length; i++) {
            var statusOption = availableStatuses[i];
            if (status !== statusOption.value) {
                statusHtml += '<div class="status-option" onclick="appointment_mark_as(\'' + statusOption.value + '\', ' + appointment_id + '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
                statusHtml += statusOption.label;
                statusHtml += '</div>';
            }
        }
        
        statusHtml += '</div>';
    }
    
    statusHtml += '</div>';
    
    return statusHtml;
}

// Add click handler for status button
$(document).on('click', '.status-button', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var appointmentId = $(this).attr('id').replace('status-btn-', '');
    var $statusMenu = $('#status-menu-' + appointmentId);
    
    // Hide all other status menus first
    $('.status-dropdown').not($statusMenu).hide();
    
    // Toggle current status menu
    if ($statusMenu.is(':visible')) {
        $statusMenu.hide();
    } else {
        $statusMenu.show();
    }
});

// Hide status menus when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('.status-wrapper').length) {
        $('.status-dropdown').hide();
    }
});

// ========================================
// APPOINTMENT STATUS DROPDOWN FUNCTIONALITY END
// ========================================

</script>

<!-- Include global appointment.js for lead modal functionality -->
<script src="<?php echo base_url('assets/js/global/appointment.js'); ?>"></script>