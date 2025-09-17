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

        <!-- Tabs Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#measurements-tab" aria-controls="measurements-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-square-o"></i> Measurements
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#estimates-tab" aria-controls="estimates-tab" role="tab" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> Estimates
                                    <span class="label label-info" style="display: none;" id="estimates-count">0</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Measurements Tab -->
                            <div role="tabpanel" class="tab-pane active" id="measurements-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-info btn-sm" onclick="openMeasurementModal()">
                                                <i class="fa fa-plus"></i> Add Measurement
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr class="hr-panel-heading" />
                                        
                                        <div id="measurements-container">
                                            <!-- Measurements will be loaded here via AJAX -->
                                            <div class="text-center">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading measurements...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimates Tab -->
                            <div role="tabpanel" class="tab-pane" id="estimates-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-info btn-sm" onclick="openEstimateModal()">
                                                <i class="fa fa-plus"></i> New Estimate
                                            </button>
                                        </div>
                                        <div class="clearfix"></div>
                                        <hr class="hr-panel-heading" />
                                        
                                        <div id="estimates-container">
                                            <!-- Estimates will be loaded here via AJAX -->
                                            <div class="text-center">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Loading estimates...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Measurement Modal -->
<div class="modal fade" id="measurementModal" tabindex="-1" role="dialog" aria-labelledby="measurementModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="measurementModalLabel">Add Measurement</h4>
            </div>
            <form id="measurementForm" method="post" action="javascript:void(0);" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" id="measurement_id" name="id" value="">
                    <input type="hidden" name="rel_type" value="appointment">
                    <input type="hidden" name="rel_id" value="<?php echo $appointment['id']; ?>">
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="category-tabs">
                        <li class="active">
                            <a href="#siding-tab" data-toggle="tab" data-category="siding">Siding</a>
                        </li>
                        <li>
                            <a href="#roofing-tab" data-toggle="tab" data-category="roofing">Roofing</a>
                        </li>
                        <li>
                            <a href="#windows-tab" data-toggle="tab" data-category="windows">Windows</a>
                        </li>
                        <li>
                            <a href="#doors-tab" data-toggle="tab" data-category="doors">Doors</a>
                        </li>
                    </ul>
                    <input type="hidden" name="category" id="selected-category" value="siding">
                    
                    <div class="tab-content">
                        <!-- Siding Tab -->
                        <div class="tab-pane active" id="siding-tab">
                            <?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'siding', 'row' => null]); ?>
                        </div>

                        <!-- Roofing Tab -->
                        <div class="tab-pane" id="roofing-tab">
                            <?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'roofing', 'row' => null]); ?>
                        </div>

                        <!-- Windows Tab -->
                        <div class="tab-pane" id="windows-tab">
                            <?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'windows', 'row' => null]); ?>
                        </div>

                        <!-- Doors Tab -->
                        <div class="tab-pane" id="doors-tab">
                            <?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'doors', 'row' => null]); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveMeasurement">Save Measurement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inline editing is used for Windows and Doors instead of nested modals -->

<?php 
// Include estimate modal data
$data['appointment'] = $appointment;
$this->load->view('appointments/estimate_modal', $data);
?>

<?php init_tail(); ?>

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
var appointmentId = <?php echo $appointment['id']; ?>;

$(document).ready(function() {
    // Load measurements when page loads
    loadMeasurements();
    
    // Load estimates when page loads
    loadEstimates();
    
    // Reload measurements when measurement modal is closed
    $('#measurementModal').on('hidden.bs.modal', function() {
        // Small delay to ensure any pending operations complete
        setTimeout(function() {
            loadMeasurements();
        }, 100);
    });
    
    // Reload estimates when estimates tab is shown
    $('a[href="#estimates-tab"]').on('click', function() {
        loadEstimates();
    });
});

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

// Measurements Functions
function loadMeasurements() {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_measurements/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayMeasurements(response.data);
            } else {
                $('#measurements-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found for this appointment.</p></div>');
            }
        },
        error: function() {
            $('#measurements-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading measurements.</p></div>');
        }
    });
}

// Estimates Functions
function loadEstimates() {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_estimates/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayEstimates(response.data);
            } else {
                $('#estimates-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No estimates found for this appointment.</p></div>');
            }
        },
        error: function() {
            $('#estimates-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading estimates.</p></div>');
        }
    });
}

function displayEstimates(estimates) {
    if (estimates.length === 0) {
        $('#estimates-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No estimates found for this appointment.</p></div>');
        return;
    }

    var html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>Estimate Name</th>';
    html += '<th>Status</th>';
    html += '<th>Line Items</th>';
    html += '<th>Total Amount</th>';
    html += '<th>Created By</th>';
    html += '<th>Created Date</th>';
    html += '<th width="120px">Actions</th>';
    html += '</tr></thead><tbody>';

    estimates.forEach(function(estimate) {
        var statusClass = '';
        var statusText = estimate.status;
        
        switch(estimate.status) {
            case 'draft':
                statusClass = 'label-warning';
                break;
            case 'sent':
                statusClass = 'label-info';
                break;
            case 'accepted':
                statusClass = 'label-success';
                break;
            case 'rejected':
                statusClass = 'label-danger';
                break;
            case 'expired':
                statusClass = 'label-default';
                break;
        }
        
        var totalAmount = estimate.total_amount ? parseFloat(estimate.total_amount).toFixed(2) : '0.00';
        var createdDate = estimate.created_at ? new Date(estimate.created_at).toLocaleDateString() : '-';

        html += '<tr>';
        html += '<td><strong>' + estimate.estimate_name + '</strong></td>';
        html += '<td><span class="label ' + statusClass + '">' + statusText.toUpperCase() + '</span></td>';
        html += '<td>' + (estimate.line_items_count || 0) + '</td>';
        html += '<td>$' + totalAmount + '</td>';
        html += '<td>' + (estimate.created_by_name || '-') + '</td>';
        html += '<td>' + createdDate + '</td>';
        html += '<td>';
        html += '<button class="btn btn-default btn-xs" onclick="openEstimateModal(' + estimate.id + ')" title="Edit"><i class="fa fa-edit"></i></button> ';
        html += '<button class="btn btn-danger btn-xs" onclick="deleteEstimate(' + estimate.id + ')" title="Delete"><i class="fa fa-trash"></i></button>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    $('#estimates-container').html(html);
}

function deleteEstimate(estimateId) {
    if (confirm('Are you sure you want to delete this estimate?')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_estimate/' + appointmentId + '/' + estimateId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Estimate deleted successfully');
                    loadEstimates(); // Reload estimates
                } else {
                    alert_float('danger', response.message || 'Failed to delete estimate');
                }
            },
            error: function() {
                alert_float('danger', 'Error deleting estimate');
            }
        });
    }
}

function displayMeasurements(measurements) {
    if (measurements.length === 0) {
        $('#measurements-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found for this appointment.</p></div>');
        return;
    }

    var html = '<div class="table-responsive"><table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>Category</th>';
    html += '<th>Name</th>';
    html += '<th>Designator</th>';
    html += '<th>Dimensions</th>';
    html += '<th>Area</th>';
    html += '<th>Quantity</th>';
    html += '<th>Location</th>';
    html += '<th width="120px">Actions</th>';
    html += '</tr></thead><tbody>';

    measurements.forEach(function(measurement) {
        var dimensions = '';
        if (measurement.width_val && measurement.height_val) {
            dimensions = measurement.width_val + '" × ' + measurement.height_val + '"';
        }
        
        var area = '';
        if (measurement.area_val) {
            area = parseFloat(measurement.area_val).toFixed(2) + ' sq ft';
        }

        html += '<tr>';
        html += '<td><span class="label label-info">' + measurement.category.toUpperCase() + '</span></td>';
        html += '<td>' + measurement.name + '</td>';
        html += '<td>' + (measurement.designator || '-') + '</td>';
        html += '<td>' + dimensions + '</td>';
        html += '<td>' + area + '</td>';
        html += '<td>' + measurement.quantity + '</td>';
        html += '<td>' + (measurement.location_label || '-') + '</td>';
        html += '<td>';
        html += '<button class="btn btn-default btn-xs" onclick="editMeasurement(' + measurement.id + ')" title="Edit"><i class="fa fa-edit"></i></button> ';
        html += '<button class="btn btn-danger btn-xs" onclick="deleteMeasurement(' + measurement.id + ')" title="Delete"><i class="fa fa-trash"></i></button>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    $('#measurements-container').html(html);
}

function openMeasurementModal(measurementId = null) {
    // Reset form
    $('#measurementForm')[0].reset();
    $('#measurement_id').val('');
    $('#measurementModalLabel').text('Add Measurement');
    $('#selected-category').val('siding');
    
    // Clear windows and doors tables for new measurements
    if (!measurementId) {
        $('#windows-tbody').html('');
        $('#doors-tbody').html('');
    }
    
    if (measurementId) {
        // Load measurement data for editing
        loadMeasurementData(measurementId);
    }
    
    $('#measurementModal').modal('show');
}

function loadMeasurementData(measurementId) {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_measurement/' + appointmentId + '/' + measurementId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var data = response.data;
                
                // Populate form fields
                $('#measurement_id').val(data.id);
                $('#selected-category').val(data.category);
                
                // Switch to the appropriate tab
                $('#category-tabs a[data-category="' + data.category + '"]').click();
                
                // Load the measurement data into the form
                setTimeout(function() {
                    populateMeasurementForm(data);
                }, 100);
                
                $('#measurementModalLabel').text('Edit Measurement');
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function() {
            alert_float('danger', 'Error loading measurement data');
        }
    });
}

function populateMeasurementForm(data) {
    // Populate basic fields if they exist
    if (data.designator) $('input[name="designator"]').val(data.designator);
    if (data.name) $('input[name="name"]').val(data.name);
    if (data.location_label) $('select[name="location_label"]').val(data.location_label);
    if (data.level_label) $('select[name="level_label"]').val(data.level_label);
    if (data.width_val) $('input[name="width_val"]').val(data.width_val);
    if (data.height_val) $('input[name="height_val"]').val(data.height_val);
    if (data.quantity) $('input[name="quantity"]').val(data.quantity);
    if (data.united_inches_val) $('input[name="united_inches_val"]').val(data.united_inches_val);
    if (data.area_val) $('input[name="area_val"]').val(data.area_val);
    if (data.notes) $('input[name="notes"]').val(data.notes);
    
    // Populate category-specific attributes
    if (data.attributes_json) {
        try {
            var attributes = JSON.parse(data.attributes_json);
            
            // Display windows and doors data in their respective tables
            displayExistingWindowsDoorsData(attributes);
            
            // Handle siding and roofing data
            Object.keys(attributes).forEach(function(category) {
                if (category !== 'windows' && category !== 'doors') {
                    Object.keys(attributes[category]).forEach(function(field) {
                        $('input[name="' + category + '[' + field + ']"]').val(attributes[category][field]);
                    });
                }
            });
        } catch (e) {
            console.error('Error parsing attributes:', e);
        }
    }
}

// Populate windows and doors tables with data
function populateWindowsDoorsTables(category, data) {
    var tbody = $('#' + category + '-tbody');
    tbody.html(''); // Clear existing data
    
    if (Array.isArray(data)) {
        data.forEach(function(item) {
            if (category === 'windows') {
                addToWindowsTable(item);
            } else if (category === 'doors') {
                addToDoorsTable(item);
            }
        });
    }
}

// Display existing windows and doors data in the measurement modal
function displayExistingWindowsDoorsData(attributes) {
    // Clear existing data first
    $('#windows-tbody').html('');
    $('#doors-tbody').html('');
    
    if (attributes.windows && Array.isArray(attributes.windows)) {
        attributes.windows.forEach(function(window, index) {
            // Add rowId to track existing data
            window.rowId = 'existing_window_' + index;
            addToWindowsTable(window, true);
        });
    }
    
    if (attributes.doors && Array.isArray(attributes.doors)) {
        attributes.doors.forEach(function(door, index) {
            // Add rowId to track existing data
            door.rowId = 'existing_door_' + index;
            addToDoorsTable(door, true);
        });
    }
}

function editMeasurement(measurementId) {
    openMeasurementModal(measurementId);
}

function deleteMeasurement(measurementId) {
    if (confirm('Are you sure you want to delete this measurement?')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_measurement/' + appointmentId + '/' + measurementId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    loadMeasurements(); // Reload measurements
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Error deleting measurement');
            }
        });
    }
}

// Collect all tabs data function (from original measurements form)
function collectAllTabsData() {
    var allData = {};
    
    // Collect data from each category tab
    ['siding', 'roofing', 'windows', 'doors'].forEach(function(category) {
        var categoryData = {};
        
        if (category === 'windows' || category === 'doors') {
            // Handle windows and doors from tables
            var tableData = collectTableData(category);
            if (Object.keys(tableData).length > 0) {
                allData[category] = tableData;
            }
        } else {
            // Get all inputs for this category (siding, roofing)
            $('input[name^="' + category + '["]').each(function() {
                var name = $(this).attr('name');
                var value = $(this).val();
                if (value !== '' && value !== null && value !== undefined) {
                    // Extract field name from name attribute like "siding[siding_total_area]"
                    var fieldName = name.match(/\[([^\]]+)\]/)[1];
                    categoryData[fieldName] = value;
                }
            });
            
            if (Object.keys(categoryData).length > 0) {
                allData[category] = categoryData;
            }
        }
    });
    
    return allData;
}

// Collect data from windows and doors tables
function collectTableData(category) {
    var tableData = [];
    var tbody = $('#' + category + '-tbody');
    if (tbody.length === 0) { return tableData; }

    tbody.find('tr').each(function() {
        var row = $(this);
        var isInline = row.hasClass('inline-measure-row');
        var data = {};

        if (isInline) {
            data.designator = row.find('.cell-designator').val() || '';
            data.name = row.find('.cell-name').val() || '';
            data.location_label = row.find('.cell-location').val() || '';
            data.level_label = row.find('.cell-level').val() || '';
            data.width_val = row.find('.cell-width').val() || '';
            data.height_val = row.find('.cell-height').val() || '';
            data.united_inches_val = row.find('.cell-ui-text').text() || '';
            data.area_val = row.find('.cell-area-text').text() || '';
        } else {
            var cells = row.find('td');
            data.designator = cells.eq(0).text().trim();
            data.name = cells.eq(1).text().trim();
            data.location_label = cells.eq(2).text().trim();
            data.level_label = cells.eq(3).text().trim();
            data.width_val = cells.eq(4).text().trim();
            data.height_val = cells.eq(5).text().trim();
            data.united_inches_val = cells.eq(6).text().trim();
            data.area_val = cells.eq(7).text().trim();
        }

        if (data.name) { tableData.push(data); }
    });

    return tableData;
}

// Add window to windows table
function addToWindowsTable(data, isExisting = false) {
    var tbody = $('#windows-tbody');
    var rowId = isExisting ? (data.rowId || 'window_' + Date.now()) : 'window_' + Date.now();
    
    var row = '<tr id="' + rowId + '">';
    row += '<td>' + (data.designator || '') + '</td>';
    row += '<td>' + (data.name || '') + '</td>';
    row += '<td>' + (data.location_label || '') + '</td>';
    row += '<td>' + (data.level_label || '') + '</td>';
    row += '<td>' + (data.width_val || '') + '</td>';
    row += '<td>' + (data.height_val || '') + '</td>';
    row += '<td>' + (data.united_inches_val || '') + '</td>';
    row += '<td>' + (data.area_val || '') + '</td>';
    row += '<td>';
    row += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'windows\')" title="Edit"><i class="fa fa-edit"></i></button> ';
    row += '<button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button>';
    row += '</td>';
    row += '</tr>';
    
    tbody.append(row);
}

// Add door to doors table
function addToDoorsTable(data, isExisting = false) {
    var tbody = $('#doors-tbody');
    var rowId = isExisting ? (data.rowId || 'door_' + Date.now()) : 'door_' + Date.now();
    
    var row = '<tr id="' + rowId + '">';
    row += '<td>' + (data.designator || '') + '</td>';
    row += '<td>' + (data.name || '') + '</td>';
    row += '<td>' + (data.location_label || '') + '</td>';
    row += '<td>' + (data.level_label || '') + '</td>';
    row += '<td>' + (data.width_val || '') + '</td>';
    row += '<td>' + (data.height_val || '') + '</td>';
    row += '<td>' + (data.united_inches_val || '') + '</td>';
    row += '<td>' + (data.area_val || '') + '</td>';
    row += '<td>';
    row += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'doors\')" title="Edit"><i class="fa fa-edit"></i></button> ';
    row += '<button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button>';
    row += '</td>';
    row += '</tr>';
    
    tbody.append(row);
}

// Edit table row
function editTableRow(rowId, category) {
    var row = $('#' + rowId);
    var cells = row.find('td');
    
    // Extract data from row
    var data = {
        designator: cells.eq(0).text(),
        name: cells.eq(1).text(),
        location_label: cells.eq(2).text(),
        level_label: cells.eq(3).text(),
        width_val: cells.eq(4).text(),
        height_val: cells.eq(5).text(),
        united_inches_val: cells.eq(6).text(),
        area_val: cells.eq(7).text()
    };
    
    // Open appropriate modal with data
    if (category === 'windows') {
        openWindowModal(data);
    } else if (category === 'doors') {
        openDoorModal(data);
    }
    
    // Mark row for deletion when new data is saved
    row.attr('data-to-delete', 'true');
}

// Remove table row
function removeTableRow(rowId) {
    if (confirm('Are you sure you want to remove this item?')) {
        $('#' + rowId).remove();
    }
}

// Open window modal with data
function openWindowModal(data) {
    $('#window-form')[0].reset();
    $('#windowModal .modal-title').text('Edit Window');
    
    // Populate form with data
    if (data) {
        $('input[name="designator"]').val(data.designator || '');
        $('input[name="name"]').val(data.name || '');
        $('select[name="location_label"]').val(data.location_label || '');
        $('select[name="level_label"]').val(data.level_label || '');
        $('input[name="width_val"]').val(data.width_val || '');
        $('input[name="height_val"]').val(data.height_val || '');
        $('input[name="united_inches_val"]').val(data.united_inches_val || '');
        $('input[name="area_val"]').val(data.area_val || '');
    }
    
    $('#windowModal').modal('show');
}

// Open door modal with data
function openDoorModal(data) {
    $('#door-form')[0].reset();
    $('#doorModal .modal-title').text('Edit Door');
    
    // Populate form with data
    if (data) {
        $('input[name="designator"]').val(data.designator || '');
        $('input[name="name"]').val(data.name || '');
        $('select[name="location_label"]').val(data.location_label || '');
        $('select[name="level_label"]').val(data.level_label || '');
        $('input[name="width_val"]').val(data.width_val || '');
        $('input[name="height_val"]').val(data.height_val || '');
        $('input[name="united_inches_val"]').val(data.united_inches_val || '');
        $('input[name="area_val"]').val(data.area_val || '');
    }
    
    $('#doorModal').modal('show');
}

// Update windows table row
function updateWindowsTableRow(row, data) {
    var cells = row.find('td');
    cells.eq(0).text(data.designator || '');
    cells.eq(1).text(data.name || '');
    cells.eq(2).text(data.location_label || '');
    cells.eq(3).text(data.level_label || '');
    cells.eq(4).text(data.width_val || '');
    cells.eq(5).text(data.height_val || '');
    cells.eq(6).text(data.united_inches_val || '');
    cells.eq(7).text(data.area_val || '');
}

// Update doors table row
function updateDoorsTableRow(row, data) {
    var cells = row.find('td');
    cells.eq(0).text(data.designator || '');
    cells.eq(1).text(data.name || '');
    cells.eq(2).text(data.location_label || '');
    cells.eq(3).text(data.level_label || '');
    cells.eq(4).text(data.width_val || '');
    cells.eq(5).text(data.height_val || '');
    cells.eq(6).text(data.united_inches_val || '');
    cells.eq(7).text(data.area_val || '');
}

// Tab handling
$('#category-tabs a[data-toggle="tab"]').on('click', function(e) {
    e.preventDefault();
    var category = $(this).data('category');
    $('#selected-category').val(category);

    // Show the corresponding tab content
    $('.tab-pane').removeClass('active');
    $('#' + category + '-tab').addClass('active');

    // Update active tab
    $('#category-tabs li').removeClass('active');
    $(this).parent().addClass('active');

    // Only load dynamic data for windows and doors tabs if we're not editing an existing measurement
    if ((category === 'windows' || category === 'doors') && !$('#measurement_id').val()) {
        loadMeasurementsByCategory(category);
    }
});

// Load measurements by category for windows and doors
function loadMeasurementsByCategory(category) {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_measurements/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash,
            category: category
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                populateMeasurementsTable(category, response.data);
            } else {
                // Clear the table if no data
                $('#' + category + '-tbody').html('');
            }
        },
        error: function() {
            console.error('Error loading ' + category + ' measurements');
        }
    });
}

// Populate measurements table for windows and doors
function populateMeasurementsTable(category, measurements) {
    var tbody = $('#' + category + '-tbody');
    tbody.html('');
    
    measurements.forEach(function(measurement) {
        if (measurement.category === category) {
            var row = '<tr>';
            row += '<td>' + (measurement.designator || '') + '</td>';
            row += '<td>' + (measurement.name || '') + '</td>';
            row += '<td>' + (measurement.location_label || '') + '</td>';
            row += '<td>' + (measurement.level_label || '') + '</td>';
            row += '<td>' + (measurement.width_val || '') + '</td>';
            row += '<td>' + (measurement.height_val || '') + '</td>';
            row += '<td>' + (measurement.united_inches_val || '') + '</td>';
            row += '<td>' + (measurement.area_val || '') + '</td>';
            row += '<td>';
            row += '<button class="btn btn-default btn-xs" onclick="editMeasurement(' + measurement.id + ')" title="Edit"><i class="fa fa-edit"></i></button> ';
            row += '<button class="btn btn-danger btn-xs" onclick="deleteMeasurement(' + measurement.id + ')" title="Delete"><i class="fa fa-trash"></i></button>';
            row += '</td>';
            row += '</tr>';
            tbody.append(row);
        }
    });
}

// Auto-calculate UI and Area when width/height change
function calculateMeasurements() {
    var width = parseFloat($('input[name="width_val"]').val()) || 0;
    var height = parseFloat($('input[name="height_val"]').val()) || 0;
    var lengthUnit = $('input[name="length_unit"]').val() || 'in';
    var areaUnit = $('input[name="area_unit"]').val() || 'sqft';

    if (width > 0 && height > 0) {
        // Calculate United Inches (width + height)
        $('input[name="united_inches_val"]').val((width + height).toFixed(2));

        // Calculate Area (convert to sqft if inches)
        if (lengthUnit === 'in' && areaUnit === 'sqft') {
            var area = (width * height) / 144.0;
            $('input[name="area_val"]').val(area.toFixed(4));
        }
    }
}

// Bind calculation to width/height inputs
$(document).on('input change', 'input[name="width_val"], input[name="height_val"], input[name="length_unit"], input[name="area_unit"]', calculateMeasurements);

// Save measurement using original measurements system
$('#saveMeasurement').on('click', function() {
    var formData = $('#measurementForm').serializeArray();
    var data = {};
    
    // Convert form data to object
    $.each(formData, function(i, field) {
        data[field.name] = field.value;
    });
    
    // Collect data from all tabs (roofing, siding, windows, doors)
    var allTabsData = collectAllTabsData();
    
    // Merge all data
    $.extend(data, allTabsData);
    
    // Set category to 'combined' since we're saving all tabs
    data.category = 'combined';
    
    // Validation
    if (Object.keys(allTabsData).length === 0) {
        alert('Please enter at least one measurement in any category before saving.');
        return false;
    }
    
    // Show loading indicator
    var submitBtn = $(this);
    var originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Saving...');
    
    // Save via AJAX using appointments controller
    saveMeasurementAjax(data, function(success, response) {
        // Reset button
        submitBtn.prop('disabled', false).text(originalText);
        
        if (success) {
            alert_float('success', 'Measurement saved successfully!');
            $('#measurementModal').modal('hide');
            loadMeasurements(); // Reload measurements list
        } else {
            alert_float('danger', 'Error saving measurement: ' + (response.message || 'Unknown error'));
        }
    });
});

// AJAX save functionality for measurements
function saveMeasurementAjax(formData, callback) {
    // Get CSRF token
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
    
    // Add CSRF token to form data
    formData[csrfData.token_name] = csrfData.hash;
    
    // Debug logging
    console.log('Sending AJAX request with data:', formData);
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/save_measurement/' + appointmentId,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('AJAX Response:', response);
            if (response.success) {
                if (typeof callback === 'function') {
                    callback(true, response);
                }
            } else {
                if (typeof callback === 'function') {
                    callback(false, response);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Response Text:', xhr.responseText);
            if (typeof callback === 'function') {
                callback(false, {error: error, responseText: xhr.responseText});
            }
        }
    });
}

// Inline add row handlers for Windows and Doors
$(document).on('click', '#js-add-window-row', function(e) {
    e.preventDefault();
    appendInlineRow('windows');
});

$(document).on('click', '#js-add-door-row', function(e) {
    e.preventDefault();
    appendInlineRow('doors');
});

function buildLocationOptions() {
    var html = '<option value="">Select Location</option>';
    for (var i = 1; i <= 10; i++) {
        html += '<option value="Bedroom ' + i + '">Bedroom ' + i + '</option>';
    }
    return html;
}

function buildLevelOptions(selected) {
    var html = '<option value="">Select Level</option>';
    for (var i = 1; i <= 10; i++) {
        var sel = (String(selected) === String(i)) ? ' selected' : '';
        html += '<option value="' + i + '"' + sel + '>' + i + '</option>';
    }
    return html;
}

function appendInlineRow(category, existingData) {
    var tbody = $('#' + category + '-tbody');
    var rowId = category + '_inline_' + Date.now();
    var d = existingData || {};
    var row = '<tr id="' + rowId + '" class="inline-measure-row" data-category="' + category + '">';
    row += '<td><input type="text" class="form-control input-sm cell-designator" value="' + (d.designator || '') + '"></td>';
    row += '<td><input type="text" class="form-control input-sm cell-name" value="' + (d.name || '') + '" required></td>';
    row += '<td><select class="form-control input-sm cell-location">' + buildLocationOptions() + '</select></td>';
    row += '<td><select class="form-control input-sm cell-level">' + buildLevelOptions(d.level_label) + '</select></td>';
    row += '<td><input type="number" step="0.01" class="form-control input-sm cell-width" value="' + (d.width_val || '') + '"></td>';
    row += '<td><input type="number" step="0.01" class="form-control input-sm cell-height" value="' + (d.height_val || '') + '"></td>';
    row += '<td><span class="cell-ui-text">' + (d.united_inches_val || '') + '</span></td>';
    row += '<td><span class="cell-area-text">' + (d.area_val || '') + '</span></td>';
    row += '<td><button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button></td>';
    row += '</tr>';
    tbody.append(row);
}

// Convert existing text row into inline editable inputs
function editTableRow(rowId, category) {
    var row = $('#' + rowId);
    var cells = row.find('td');
    var data = {
        designator: cells.eq(0).text(),
        name: cells.eq(1).text(),
        location_label: cells.eq(2).text(),
        level_label: cells.eq(3).text(),
        width_val: cells.eq(4).text(),
        height_val: cells.eq(5).text(),
        united_inches_val: cells.eq(6).text(),
        area_val: cells.eq(7).text()
    };
    row.remove();
    appendInlineRow(category, data);
}

// Auto-calc UI & Area inside inline rows
$(document).on('input change', '.inline-measure-row .cell-width, .inline-measure-row .cell-height', function() {
    var row = $(this).closest('tr');
    var width = parseFloat(row.find('.cell-width').val()) || 0;
    var height = parseFloat(row.find('.cell-height').val()) || 0;
    if (width > 0 && height > 0) {
        var ui = width + height;
        var area = (width * height) / 144.0;
        row.find('.cell-ui-text').text(ui.toFixed(2));
        row.find('.cell-area-text').text(area.toFixed(2));
    } else {
        row.find('.cell-ui-text').text('');
        row.find('.cell-area-text').text('');
    }
});
</script>
