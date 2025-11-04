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
                                    <button type="button" class="btn btn-info" id="new-appointment">
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
                                        <th>
                                            <span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="ella_appointments"><label></label></div>
                                        </th>
                                        <th class="text-center"><?php echo _l('id'); ?></th>
                                        <th class="text-center" style="min-width: 250px;">Appointment</th>
                                        <th class="text-center">Lead</th>
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

/* Fix checkbox alignment - center the checkmark icon */
.table-ella_appointments .checkbox label::after {
    padding-left: 3.5px !important;
    padding-top: 2px !important;
}

/* Ensure checkbox column width matches leads table */
.table-ella_appointments thead th:first-child,
.table-ella_appointments tbody td:first-child {
    width: 30px;
    text-align: left;
}

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

/* Bulk delete button styling in DataTable toolbar */
#bulk-delete-appointments {
    display: inline-block;
    vertical-align: middle;
    margin-left: 5px !important;
}
#bulk-delete-appointments.hide {
    display: none !important;
}
/* Ensure button appears in same line as other DataTable controls */
.dataTables_length, .dt-buttons, #bulk-delete-appointments {
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
}


/* Match hover of Delete All button with listing delete button */
#bulk-delete-appointments.btn-danger {
    background-color: #dc3545; /* same as row delete */
    border-color: #dc3545;
    color: #fff;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

#bulk-delete-appointments.btn-danger:hover,
.table-ella_appointments .btn-danger:hover {
    background-color: #bb2d3b; /* darker red like Bootstrap hover */
    border-color: #b02a37;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
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

/* Lead link hover effects */
.table-ella_appointments .lead-link {
    color: #03a9f4;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
}

.table-ella_appointments .lead-link:hover {
    color: #0288d1;
    text-decoration: none;
    transform: translateX(2px);
}

.table-ella_appointments .lead-link i {
    transition: all 0.2s ease;
}

.table-ella_appointments .lead-link:hover i {
    opacity: 1 !important;
    transform: translateX(2px);
}

/* Hide dropdown menu from print/export */
@media print {
    .status-dropdown,
    .status-option,
    .table-export-exclude {
        display: none !important;
    }
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
    // Sort by column 4 (Scheduled datetime - combined date+time for proper chronological sorting) descending by default
    // Columns: 0=checkbox, 1=ID, 2=Appointment, 3=Lead, 4=Scheduled(datetime), 5=Status, 6=Measurements, 7=Estimates, 8=Options
    // Disable sorting on: column 0 (checkbox), column 8 (options)
    // Backend column 4 now uses CONCAT(date, time) for proper server-side sorting
    initDataTable('.table-ella_appointments', admin_url + 'ella_contractors/appointments/table', undefined, [0, 8], {}, [4, 'desc']);
    
    // Function to add bulk delete button to DataTable toolbar
    function addBulkDeleteButton() {
        if ($('.table-ella_appointments').length && $('#bulk-delete-appointments').length === 0) {
            // Find the DataTable wrapper
            var $wrapper = $('.table-ella_appointments').closest('.dataTables_wrapper');
            
            if ($wrapper.length) {
                // Try to find the buttons container first (if Export button exists)
                var $buttonsContainer = $wrapper.find('.dt-buttons');
                
                if ($buttonsContainer.length) {
                    // Add bulk delete button after export button - matching delete button style
                    $buttonsContainer.append('<button type="button" class="btn btn-danger btn-xs hide" id="bulk-delete-appointments">' +
                                            '<i class="fa fa-trash"></i> Delete All (<span id="selected-count">0</span>)' +
                                         '</button>');
                } else {
                    // Fallback: add to the left side with length dropdown
                    var $lengthContainer = $wrapper.find('.dataTables_length');
                    if ($lengthContainer.length) {
                        $lengthContainer.after('<button type="button" class="btn btn-danger btn-xs hide" id="bulk-delete-appointments" style="margin-left: 10px;">' +
                            '<i class="fa fa-trash"></i> Delete (<span id="selected-count">0</span>)' +
                        '</button>');
                    } else {
                        // Last resort: add to the top of the wrapper
                        $wrapper.prepend('<div style="display: inline-block; margin-right: 10px;"><button type="button" class="btn btn-danger btn-xs hide" id="bulk-delete-appointments">' +
                            '<i class="fa fa-trash"></i> Delete (<span id="selected-count">0</span>)' +
                        '</button></div>');
                    }
                }
            }
        }
    }
    
    // Add bulk delete button to DataTable toolbar after initialization
    setTimeout(addBulkDeleteButton, 800);
    
    // Re-add button after table draws (if needed)
    if ($('.table-ella_appointments').length) {
        $('.table-ella_appointments').on('draw.dt', function() {
            setTimeout(addBulkDeleteButton, 100);
        });
    }
    
    // Initialize selectpicker
    $('.selectpicker').selectpicker();
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize AJAX search for leads and clients
    init_combined_ajax_search('#contact_id.ajax-search');
    
    // New appointment button click handler
    $('#new-appointment').on('click', function() {
        openNewAppointmentModal();
        $('#appointmentModal').modal('show');
    });
    
    // Check for edit parameter in URL
    var urlParams = new URLSearchParams(window.location.search);
    var editId = urlParams.get('edit');
    if (editId) {
        // Open modal for editing
        openNewAppointmentModal(editId);
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
                        
                        // Auto-fill Appointment Name if empty
                        if (!$('#subject').val()) {
                            var contactName = data.to || '';
                            if (contactName && contactName.trim() !== '') {
                                $('#subject').val('Appointment with ' + contactName.trim());
                            } else {
                                $('#subject').val('Appointment with ' + relType.charAt(0).toUpperCase() + relType.slice(1) + ' ID: ' + relId);
                            }
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
        
        // Save current sort order
        var currentOrder = table.order();
        
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
        
        // Update table URL and reload while maintaining sort order
        table.ajax.url(filterUrl);
        table.ajax.reload(function() {
            // Restore sort order after filter
            table.order(currentOrder).draw(false);
        });
    });
});

// Global functions for modal operations
function openNewAppointmentModal(appointmentId = null) {
    if ($('#appointmentForm').length === 0) {
        return;
    }
    
    // Reset form
    resetAppointmentModal();
    
    // Show modal immediately for new appointments
    if (!appointmentId) {
        // Reload staff members for attendees dropdown
        loadStaffForAttendees();
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
                
                // Handle reminder checkboxes
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                $('#reminder_48h').prop('checked', data.reminder_48h == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                
                // Update modal title and button text for editing
                $('#appointmentModalLabel').text('Edit Appointment');
                $('#saveAppointment').text('Save Appointment');
                
                // Reload staff and set attendees using centralized function (same as view.php)
                reloadStaffAndSetAttendees(data.attendees);
                
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
                
                // Handle reminder checkboxes
                $('#send_reminder').prop('checked', data.send_reminder == 1);
                $('#reminder_48h').prop('checked', data.reminder_48h == 1);
                
                // Set status dropdown
                var status = data.appointment_status || 'scheduled';
                $('#status').val(status);
                
                // Set appointment type (types already loaded from PHP in modal.php)
                $('#type_id').val(data.type_id);
                
                // Update modal title and button text for editing
                $('#appointmentModalLabel').text('Edit Appointment');
                $('#saveAppointment').text('Save Appointment');
                
                // Reload staff and set attendees using centralized function (same as view.php)
                reloadStaffAndSetAttendees(data.attendees);
                
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
    $('#saveAppointment').text('Create Appointment'); // Update button text for creation
    $('#contact_id').html('<option value="">Select Client/Lead</option>');
    $('#contact_id').selectpicker('val', '');
    
    // Reload staff members for attendees dropdown
    loadStaffForAttendees();
    
    $('.selectpicker').selectpicker('refresh');
    
    // Clear uploaded files using shared function
    clearAppointmentDropzone();
    
    // Clear presentation selections
    if (typeof $('#presentation_select').selectpicker !== 'undefined') {
        $('#presentation_select').selectpicker('deselectAll');
    }
    $('#selected_presentation_ids').val('');
    
    // Clear presentation preview using centralized function
    if (typeof clearPresentationSelectionPreview === 'function') {
        clearPresentationSelectionPreview();
    } else {
        $('#modal-presentation-list').html('');
    }
    
    // Reset reminder checkboxes to default (checked)
    $('#send_reminder').prop('checked', true);
    $('#reminder_48h').prop('checked', true);
}

function deleteAppointment(appointmentId) {
    if (confirm('Are you sure you want to delete this appointment?')) {
        var table = $('.table-ella_appointments').DataTable();
        var currentOrder = table.order();
        
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
                    // Reload table maintaining sort order
                    table.ajax.reload(function() {
                        table.order(currentOrder).draw(false);
                    });
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
        alert_float('danger', 'Appointment name is required');
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
    
    // Validate end time is greater than start time
    var startDateTimeObj = new Date($('#start_datetime').val());
    var endDateTimeObj = new Date($('#end_datetime').val());
    
    if (endDateTimeObj <= startDateTimeObj) {
        alert_float('danger', 'End date & time must be greater than start date & time');
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
    
    // Show loader and disable button
    var $saveBtn = $('#saveAppointment');
    var originalBtnText = $saveBtn.text();
    $saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    
    // Get form data using FormData to handle file uploads
    var formData = new FormData($('#appointmentForm')[0]);
    
    // Add appointment files to FormData
    if (appointmentFiles.length > 0) {
        appointmentFiles.forEach(function(file, index) {
            formData.append('appointment_files[]', file);
        });
    }
    
    // Add CSRF token
    formData.append(csrf_token_name, csrf_hash);
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/save_ajax',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var appointmentId = response.appointment_id || $('#appointment_id').val();
                
                // Handle presentations if selected and centralized function exists
                if (typeof getSelectedPresentationIds === 'function' && typeof attachMultiplePresentationsToAppointment === 'function') {
                    var selectedPresentationIds = getSelectedPresentationIds('presentation_select', 'selected_presentation_ids');
                    if (selectedPresentationIds && selectedPresentationIds.length > 0 && appointmentId) {
                        attachMultiplePresentationsToAppointment(appointmentId, selectedPresentationIds, function(attachResponse) {
                            alert_float('success', response.message);
                            
                            $('#appointmentModal').modal('hide');
                            resetAppointmentModal();
                            
                            // Reload table maintaining sort order
                            var table = $('.table-ella_appointments').DataTable();
                            var currentOrder = table.order();
                            table.ajax.reload(function() {
                                table.order(currentOrder).draw(false);
                            });
                        });
                    } else {
                        alert_float('success', response.message);
                        $('#appointmentModal').modal('hide');
                        resetAppointmentModal();
                        
                        // Reload table maintaining sort order
                        var table = $('.table-ella_appointments').DataTable();
                        var currentOrder = table.order();
                        table.ajax.reload(function() {
                            table.order(currentOrder).draw(false);
                        });
                    }
                } else {
                    alert_float('success', response.message);
                    $('#appointmentModal').modal('hide');
                    resetAppointmentModal();
                    
                    // Reload table maintaining sort order
                    var table = $('.table-ella_appointments').DataTable();
                    var currentOrder = table.order();
                    table.ajax.reload(function() {
                        table.order(currentOrder).draw(false);
                    });
                }
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error saving appointment: ' + error);
        },
        complete: function() {
            // Hide loader and re-enable button
            $saveBtn.prop('disabled', false).text(originalBtnText);
        }
    });
});

// Initialize dropzone when document is ready
$(document).ready(function() {
    // Initialize selectpickers
    $('.selectpicker').selectpicker();
    
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
                var currentImagesResponse = $('.appointment_imagesresponse').val();
                
                if (currentImagesResponse) {
                    currentImagesResponse += ',' + data;
                } else {
                    currentImagesResponse = data;
                }
                
                $('.appointment_imagesresponse').val(currentImagesResponse);
            },
            error: function(data) {
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
        
        // Load presentations for dropdown if centralized function exists
        if (typeof loadPresentationsForDropdown === 'function') {
            loadPresentationsForDropdown('presentation_select', function() {
                // Initialize presentation selection preview after dropdown is loaded
                if (typeof initPresentationSelectionPreview === 'function') {
                    initPresentationSelectionPreview('presentation_select', 'modal-presentation-list');
                }
            });
        }
        
        // Load attached presentations if editing (appointment_id exists)
        var appointmentId = $('#appointment_id').val();
        if (appointmentId && appointmentId > 0 && typeof loadAttachedPresentations === 'function') {
            loadAttachedPresentations(appointmentId, null, function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    // Pre-select in dropdown (this will trigger the preview rendering)
                    var selectedIds = response.data.map(function(p) { return p.id.toString(); });
                    
                    // Update the global array with actual presentation details
                    selectedPresentationsInModal = response.data.map(function(p) {
                        return {
                            id: p.id.toString(),
                            name: p.original_name || p.file_name
                        };
                    });
                    
                    // Set dropdown values and render preview
                    $('#presentation_select').selectpicker('val', selectedIds);
                    if (typeof renderPresentationSelectionPreview === 'function') {
                        renderPresentationSelectionPreview('modal-presentation-list');
                    }
                }
            });
        }
    });
});

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
    
    // Store current order before any updates
    var currentOrder = table.order();
    
    // Find the row with the matching appointment ID
    var rowIndex = -1;
    table.rows().every(function(rowIdx, data, node) {
        if (data[1] == appointment_id) { // Column 1 is the ID column
            rowIndex = rowIdx;
            return false; // Break the loop
        }
    });
    
    if (rowIndex !== -1) {
        // Get status label for data attributes
        var statusLabel = '';
        switch (newStatus) {
            case 'cancelled':
                statusLabel = '<?php echo strtoupper(_l('cancelled')); ?>';
                break;
            case 'complete':
                statusLabel = '<?php echo strtoupper(_l('complete')); ?>';
                break;
            case 'scheduled':
                statusLabel = '<?php echo strtoupper(_l('scheduled')); ?>';
                break;
            default:
                statusLabel = newStatus.toUpperCase();
        }
        
        // Generate new status HTML
        var statusHtml = generateStatusHtml(newStatus, appointment_id);
        
        // Update the status cell directly in the DOM
        var statusCell = table.cell(rowIndex, 5).node();
        $(statusCell).html('<div class="text-center" data-order="' + statusLabel + '">' + statusHtml + '</div>');
        
        // Maintain the sort order - redraw without changing sort
        table.order(currentOrder).draw(false);
        
        // Reinitialize tooltips for the updated status element
        $('[data-toggle="tooltip"]').tooltip();
    } else {
        // Fallback: if row not found, reload the table maintaining current page and sort
        console.warn('Row not found for appointment ID:', appointment_id, 'reloading table...');
        table.ajax.reload(function() {
            // Restore sort order after reload
            table.order(currentOrder).draw(false);
        }, false); // false = maintain current page
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
    
    // Create status display HTML matching backend structure (with wrapper for positioning)
    var statusHtml = '<div class="status-wrapper" style="position: relative; display: inline-block;">';
    
    // Main status text for display and export
    statusHtml += '<span class="status-button label ' + statusClass + '" id="status-btn-' + appointment_id + '" style="cursor: pointer !important;">';
    statusHtml += statusLabel;
    statusHtml += '</span>';
    
    // Dropdown menu positioned on the left side (excluded from export via table-export-exclude class)
    if (hasPermission) {
        statusHtml += '<div id="status-menu-' + appointment_id + '" class="status-dropdown table-export-exclude" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 120px;">';
        
        var availableStatuses = [
            {value: 'scheduled', label: '<?php echo strtoupper(_l('scheduled')); ?>'},
            {value: 'complete', label: '<?php echo strtoupper(_l('complete')); ?>'},
            {value: 'cancelled', label: '<?php echo strtoupper(_l('cancelled')); ?>'}
        ];
        
        for (var i = 0; i < availableStatuses.length; i++) {
            var statusOption = availableStatuses[i];
            if (status !== statusOption.value) {
                statusHtml += '<div class="status-option table-export-exclude" onclick="appointment_mark_as(\'' + statusOption.value + '\', ' + appointment_id + '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
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

// Attendees functionality is now handled by the centralized appointment-attendees.js file

$(document).ready(function() {
    // Initialize attendees functionality
    initAppointmentAttendees();
    
    // Check if auto_open parameter is present in URL
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_open') === '1') {
        // Wait a bit for the page to fully load, then trigger the modal
        setTimeout(function() {
            // Trigger the "New Appointment" button click to open the modal
            $('#new-appointment').click();
        }, 500);
        
        // Clean up the URL parameter without reloading the page
        var newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
    
    // ========================================
    // BULK DELETE FUNCTIONALITY
    // ========================================
    
    // Handle individual checkbox changes
    $(document).on('change', '.table-ella_appointments tbody input[type="checkbox"]', function() {
        updateBulkDeleteButton();
    });
    
    // Handle select all checkbox
    $(document).on('change', '#mass_select_all', function() {
        var isChecked = $(this).prop('checked');
        $('.table-ella_appointments tbody input[type="checkbox"]').prop('checked', isChecked);
        updateBulkDeleteButton();
    });
    
    // Update bulk delete button visibility and count
    function updateBulkDeleteButton() {
        var selectedCount = $('.table-ella_appointments tbody input[type="checkbox"]:checked').length;
        $('#selected-count').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#bulk-delete-appointments').removeClass('hide');
        } else {
            $('#bulk-delete-appointments').addClass('hide');
        }
    }
    
    // Handle bulk delete button click (using delegation since button is added dynamically)
    $(document).on('click', '#bulk-delete-appointments', function() {
        var selectedIds = [];
        $('.table-ella_appointments tbody input[type="checkbox"]:checked').each(function() {
            var appointmentId = $(this).val();
            if (appointmentId) {
                selectedIds.push(appointmentId);
            }
        });
        
        if (selectedIds.length === 0) {
            alert_float('warning', 'No appointments selected');
            return;
        }
        
        // Confirm deletion
        var confirmMessage = 'Are you sure you want to delete ' + selectedIds.length + ' appointment(s)? This action cannot be undone.';
        if (!confirm(confirmMessage)) {
            return;
        }
        
        // Save current sort order
        var table = $('.table-ella_appointments').DataTable();
        var currentOrder = table.order();
        
        // Show loading state
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
        
        // Send AJAX request
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/bulk_delete',
            type: 'POST',
            data: {
                ids: selectedIds,
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    
                    // Uncheck mass select all
                    $('#mass_select_all').prop('checked', false);
                    
                    // Hide bulk delete button
                    $('#bulk-delete-appointments').addClass('hide');
                    
                    // Reload table maintaining sort order
                    table.ajax.reload(function() {
                        table.order(currentOrder).draw(false);
                    });
                } else {
                    alert_float('danger', response.message || 'Failed to delete appointments');
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error deleting appointments: ' + error);
                console.error('Bulk delete error:', error);
            },
            complete: function() {
                // Restore button state
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // ========================================
    // END BULK DELETE FUNCTIONALITY
    // ========================================
});

// ========================================
// LEAD MODAL FUNCTIONALITY
// ========================================

/**
 * Fallback function to open lead modal if init_lead doesn't work
 * This ensures lead modal always opens on the same page
 */
$(document).ready(function() {
    // Event delegation for dynamically loaded lead links
    $(document).on('click', '.lead-link', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var leadId = $(this).data('lead-id');
        
        if (!leadId) {
            return false;
        }
        
        // Try Perfex's built-in init_lead function first
        if (typeof init_lead === 'function') {
            init_lead(leadId);
        } else {
            // Fallback: Show loading and trigger AJAX
            alert_float('info', 'Loading lead details...');
            
            // Attempt to load via AJAX
            $.ajax({
                url: admin_url + 'leads/client/' + leadId,
                type: 'GET',
                success: function(response) {
                    // Check if response contains modal HTML
                    if (response && response.indexOf('lead-modal') !== -1) {
                        // Remove existing modal
                        $('#lead-modal').remove();
                        
                        // Append and show
                        $('body').append(response);
                        $('#lead-modal').modal('show');
                    } else {
                        // If no modal HTML, fallback to page load
                        window.location.href = admin_url + 'leads/index/' + leadId;
                    }
                },
                error: function() {
                    alert_float('danger', 'Failed to load lead details');
                }
            });
        }
        
        return false;
    });
});

// ========================================
// END LEAD MODAL FUNCTIONALITY
// ========================================
</script>

<!-- Include shared appointment dropzone functionality -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-dropzone.js'); ?>"></script>

<!-- Include centralized appointment attendees functionality -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-attendees.js'); ?>"></script>

<!-- Include centralized appointment presentations functionality -->
<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/appointment-presentations.js'); ?>"></script>

<!-- Include global appointment.js for lead modal functionality -->
<script src="<?php echo base_url('assets/js/global/appointment.js'); ?>"></script>



<!-- create_appointment -->