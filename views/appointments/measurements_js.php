<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Estimate Row Management Functions
var estimateRowCounter = 0;
var estimateRowCounterRoofing = 0;

// Flag to track if measurement was successfully saved
var measurementSaved = false;
var modalJustClosed = false;
var modalIsOpening = false;
var lastMeasurementsContent = '';
var forceReload = false;

// Monitor measurements container changes
function monitorMeasurementsContainer() {
    var container = $('#measurements-container');
    var lastContent = container.html();
    
    setInterval(function() {
        var currentContent = container.html();
        if (currentContent !== lastContent) {
            console.log('🚨 MEASUREMENTS CONTAINER CHANGED!');
            console.log('Previous length:', lastContent.length);
            console.log('New length:', currentContent.length);
            console.log('Call stack:', new Error().stack);
            lastContent = currentContent;
        }
    }, 100);
}

function addEstimateRow(category = 'siding') {
    var containerId = category === 'roofing' ? '#estimate-rows-container-roofing' : '#estimate-rows-container';
    var prefix = category === 'roofing' ? 'roofing_' : '';
    var namePrefix = category === 'roofing' ? 'measurements_roofing' : 'measurements';
    
    if (category === 'roofing') {
        estimateRowCounterRoofing++;
        var counter = estimateRowCounterRoofing;
    } else {
        estimateRowCounter++;
        var counter = estimateRowCounter;
    }
    
    var rowHtml = '<div class="row estimate-row" data-row="' + counter + '">' +
        '<div class="col-md-4">' +
            '<div class="form-group">' +
                '<label for="measurement_name_' + prefix + counter + '">Name</label>' +
                '<input type="text" class="form-control" name="' + namePrefix + '[' + counter + '][name]" id="measurement_name_' + prefix + counter + '" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_' + prefix + counter + '">Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="' + namePrefix + '[' + counter + '][value]" id="measurement_value_' + prefix + counter + '" placeholder="0.00">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_' + prefix + counter + '">Unit</label>' +
                '<select class="form-control selectpicker-unit" name="' + namePrefix + '[' + counter + '][unit]" id="measurement_unit_' + prefix + counter + '">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                '</select>' +
            '</div>' +
        '</div>' +
        '<div class="col-md-2">' +
            '<div class="form-group">' +
                '<label>&nbsp;</label>' +
                '<div>' +
                    '<button type="button" class="btn btn-success btn-sm" onclick="addEstimateRow(\'' + category + '\')" title="Add Estimate">' +
                        '<i class="fa fa-plus"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="removeEstimateRow(this)" title="Remove Row">' +
                        '<i class="fa fa-minus"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    $(containerId).append(rowHtml);
    
    // Show remove buttons for all rows in this category if there's more than one
    var rowCount = $(containerId + ' .estimate-row').length;
    if (rowCount > 1) {
        $(containerId + ' .estimate-row .btn-danger').show();
    }
}

function removeEstimateRow(button) {
    $(button).closest('.estimate-row').remove();
    
    // Hide remove buttons if only one row left in the container
    var container = $(button).closest('[id^="estimate-rows-container"]');
    var rowCount = container.find('.estimate-row').length;
    if (rowCount <= 1) {
        container.find('.estimate-row .btn-danger').hide();
    }
}

// Measurements Functions
function loadMeasurements() {
    console.log('=== LOAD MEASUREMENTS CALLED ===');
    console.log('Appointment ID:', appointmentId);
    console.log('Modal just closed:', modalJustClosed);
    console.log('Modal is opening:', modalIsOpening);
    console.log('Measurement saved:', measurementSaved);
    console.log('Force reload:', forceReload);
    console.log('Current measurements container content length:', $('#measurements-container').html().length);
    
    // Reset flags
    if (forceReload) {
        console.log('🚀 Force reload requested, proceeding with normal load');
        forceReload = false;
    }
    if (modalIsOpening) {
        console.log('✅ Modal is opening, resetting flag');
        modalIsOpening = false;
    }
    
    console.log('✅ Loading measurements for appointment:', appointmentId);
    
    // Show loading indicator
    console.log('🔄 Setting loading indicator in measurements container');
    $('#measurements-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading measurements...</p></div>');
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/get_appointment_measurements/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            console.log('Measurements response:', response);
            if (response && response.success) {
                displayMeasurements(response.data);
            } else {
                console.log('No measurements found or response failed');
                var emptyHtml = '<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found for this appointment.</p></div>';
                $('#measurements-container').html(emptyHtml);
                // Store empty state as well
                lastMeasurementsContent = emptyHtml;
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading measurements:', error, xhr.responseText);
            $('#measurements-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading measurements. Please try again.</p></div>');
        }
    });
}

function displayMeasurements(measurements) {
    console.log('=== DISPLAY MEASUREMENTS CALLED ===');
    console.log('Measurements count:', measurements.length);
    console.log('Call stack:', new Error().stack);
    
    if (measurements.length === 0) {
        console.log('📝 No measurements found, showing empty state');
        $('#measurements-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found for this appointment.</p></div>');
        return;
    }

    var html = '<div class="table-responsive"><table class="table table-hover" style="margin-bottom: 0;">';
    html += '<thead style="background-color: #2c3e50; color: white;">';
    html += '<tr>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Record</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Windows</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Doors</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Siding Measurements</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Roofing Measurements</th>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600; width: 140px;">Actions</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';

    measurements.forEach(function(measurement, idx) {
        var attrs = {};
        try { 
            attrs = measurement.attributes || JSON.parse(measurement.attributes_json || '{}'); 
        } catch(e) {
            console.error('Error parsing attributes:', e);
            attrs = {};
        }
        
        var windowsCount = (attrs.windows && Array.isArray(attrs.windows)) ? attrs.windows.length : 0;
        var doorsCount = (attrs.doors && Array.isArray(attrs.doors)) ? attrs.doors.length : 0;
        var sidingCount = (attrs.siding_measurements && Array.isArray(attrs.siding_measurements)) ? attrs.siding_measurements.length : 0;
        var roofingCount = (attrs.roofing_measurements && Array.isArray(attrs.roofing_measurements)) ? attrs.roofing_measurements.length : 0;

        var rowClass = (idx % 2 === 0) ? 'style="background-color: #f8f9fa;"' : 'style="background-color: white;"';
        
        html += '<tr ' + rowClass + '>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;">';
        var categoryDisplay = (measurement.category === 'other') ? 'COMBINED' : (measurement.category || 'COMBINED').toUpperCase();
        html += '<span style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">' + categoryDisplay + '</span>';
        html += ' <strong>#' + measurement.id + '</strong>';
        html += '</td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + windowsCount + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + doorsCount + '</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + sidingCount + ' items</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + roofingCount + ' items</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;">';
        html += '<div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">';
        html += '<button class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="editMeasurement(' + measurement.id + ')" title="Edit Measurement"><i class="fa fa-edit"></i></button>';
        html += '<button class="btn btn-sm" style="background-color: #dc3545; border: 1px solid #dc3545; color: white; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteMeasurement(' + measurement.id + ')" title="Delete Measurement"><i class="fa fa-trash"></i></button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    console.log('📝 Displaying measurements HTML, length:', html.length);
    
    // Store the content before displaying it
    lastMeasurementsContent = html;
    console.log('💾 Stored measurements content for potential restoration');
    
    $('#measurements-container').html(html);
    console.log('✅ Measurements container updated with data');
}

function openMeasurementModal(measurementId = null) {
    console.log('=== OPENING MEASUREMENT MODAL ===');
    console.log('Measurement ID:', measurementId);
    
    // Set flag to indicate modal is opening
    modalIsOpening = true;
    
    // Reset the flags when opening modal
    measurementSaved = false;
    modalJustClosed = false;
    console.log('Reset measurementSaved flag to:', measurementSaved);
    console.log('Reset modalJustClosed flag to:', modalJustClosed);
    console.log('Set modalIsOpening flag to:', modalIsOpening);
    
    // Store appointment ID before resetting form
    var appointmentId = $('input[name="appointment_id"]').val();
    var relId = $('input[name="rel_id"]').val();
    var relType = $('input[name="rel_type"]').val();
    
    // Set measurement ID first before resetting form
    $('#measurement_id').val(measurementId || '');
    
    // Reset form
    $('#measurementForm')[0].reset();
    
    // Restore all important values after reset
    $('#measurement_id').val(measurementId || '');
    $('input[name="appointment_id"]').val(appointmentId);
    $('input[name="rel_id"]').val(relId);
    $('input[name="rel_type"]').val(relType);
    
    $('#measurementModalLabel').text(measurementId ? 'Edit Measurement' : 'Add Measurement');
    $('#selected-category').val('siding');
    
    // Reset tabs to siding
    $('#category-tabs li').removeClass('active');
    $('#category-tabs li:first').addClass('active');
    $('.tab-pane').removeClass('active');
    $('#siding-tab').addClass('active');
    
    // Reset estimate rows for siding tab
    $('#estimate-rows-container').html('<div class="row estimate-row" data-row="0">' +
        '<div class="col-md-4">' +
            '<div class="form-group">' +
                '<label for="measurement_name_0">Name</label>' +
                '<input type="text" class="form-control" name="measurements[0][name]" id="measurement_name_0" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_0">Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="measurements[0][value]" id="measurement_value_0" placeholder="0.00">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_0">Unit</label>' +
                '<select class="form-control selectpicker-unit" name="measurements[0][unit]" id="measurement_unit_0">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                '</select>' +
            '</div>' +
        '</div>' +
        '<div class="col-md-2">' +
            '<div class="form-group">' +
                '<label>&nbsp;</label>' +
                '<div>' +
                    '<button type="button" class="btn btn-success btn-sm" onclick="addEstimateRow()" title="Add Estimate">' +
                        '<i class="fa fa-plus"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="removeEstimateRow(this)" title="Remove Row" style="display: none;">' +
                        '<i class="fa fa-minus"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>');
    
    // Reset estimate rows for roofing tab
    $('#estimate-rows-container-roofing').html('<div class="row estimate-row" data-row="0">' +
        '<div class="col-md-4">' +
            '<div class="form-group">' +
                '<label for="measurement_name_roofing_0">Name</label>' +
                '<input type="text" class="form-control" name="measurements_roofing[0][name]" id="measurement_name_roofing_0" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_roofing_0">Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="measurements_roofing[0][value]" id="measurement_value_roofing_0" placeholder="0.00">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_roofing_0">Unit</label>' +
                '<select class="form-control selectpicker-unit" name="measurements_roofing[0][unit]" id="measurement_unit_roofing_0">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                '</select>' +
            '</div>' +
        '</div>' +
        '<div class="col-md-2">' +
            '<div class="form-group">' +
                '<label>&nbsp;</label>' +
                '<div>' +
                    '<button type="button" class="btn btn-success btn-sm" onclick="addEstimateRow(\'roofing\')" title="Add Estimate">' +
                        '<i class="fa fa-plus"></i>' +
                    '</button>' +
                    '<button type="button" class="btn btn-danger btn-sm" onclick="removeEstimateRow(this)" title="Remove Row" style="display: none;">' +
                        '<i class="fa fa-minus"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>');
    
    // Reset counters
    estimateRowCounter = 0;
    estimateRowCounterRoofing = 0;
    
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
    
    // Reset modal opening flag after a short delay in case modal doesn't open
    setTimeout(function() {
        if (modalIsOpening) {
            console.log('⚠️ Modal opening flag still true after timeout, resetting');
            modalIsOpening = false;
        }
    }, 2000);
}

// Smart modal close handler - only reload if measurement was actually saved
$(document).ready(function() {
    // Start monitoring measurements container changes
    monitorMeasurementsContainer();
    
    // Remove any existing handlers to prevent duplicates
    $('#measurementModal').off('hidden.bs.modal').off('show.bs.modal');
    
    // Handle modal show event - capture current state
    $('#measurementModal').on('show.bs.modal', function() {
        console.log('=== MEASUREMENT MODAL SHOWING ===');
        
        // Store current measurements content before opening modal
        var currentContent = $('#measurements-container').html();
        if (currentContent && currentContent.length > 0 && 
            !currentContent.includes('Loading measurements') &&
            !currentContent.includes('fa-spinner')) {
            lastMeasurementsContent = currentContent;
            console.log('💾 Stored current measurements content (length:', currentContent.length, ')');
        }
        
        // Ensure measurements tab is visible
        if (!$('#measurements-tab').hasClass('active')) {
            console.log('⚠️ Measurements tab not active, activating it');
            $('a[href="#measurements-tab"]').tab('show');
        }
    });
    
    $('#measurementModal').on('hidden.bs.modal', function() {
        console.log('=== MEASUREMENT MODAL CLOSED ===');
        console.log('Measurement saved flag:', measurementSaved);
        console.log('Modal is opening flag:', modalIsOpening);
        console.log('Current active tab:', typeof currentActiveTab !== 'undefined' ? currentActiveTab : 'undefined');
        
        // Reset modal opening flag
        modalIsOpening = false;
        
        // Set flag to indicate modal just closed
        modalJustClosed = true;
        
        if (measurementSaved) {
            // Only reload if measurement was actually saved
            console.log('✅ Measurement was saved, reloading...');
            
            // Ensure measurements tab is shown
            setTimeout(function() {
                // Force show measurements tab
                $('a[href="#measurements-tab"]').tab('show');
                
                // Reload measurements data
                if (typeof refreshAppointmentData === 'function') {
                    console.log('Using refreshAppointmentData()');
                    refreshAppointmentData('measurements');
                } else {
                    console.log('Using loadMeasurements()');
                    forceReload = true;
                    loadMeasurements();
                }
            }, 150);
            
            measurementSaved = false; // Reset flag
        } else {
            console.log('❌ Modal was cancelled/closed without saving');
            console.log('🔄 Ensuring measurements tab is visible and data is restored');
            
            // Force show measurements tab and restore data
            setTimeout(function() {
                console.log('🔄 Activating measurements tab after modal cancel');
                
                // Ensure the measurements tab is active and visible
                $('ul.nav-tabs li').removeClass('active');
                $('ul.nav-tabs li:first').addClass('active');
                $('.tab-pane').removeClass('active');
                $('#measurements-tab').addClass('active').show();
                
                // Make sure container is visible
                $('#measurements-container').show();
                
                // Restore previous content if available, otherwise reload
                if (lastMeasurementsContent && lastMeasurementsContent.length > 0 && 
                    !lastMeasurementsContent.includes('Loading measurements')) {
                    console.log('✅ Restoring previous measurements content');
                    $('#measurements-container').html(lastMeasurementsContent);
                } else {
                    console.log('🔄 No valid content to restore, reloading measurements');
                    forceReload = true;
                    loadMeasurements();
                }
            }, 150);
        }
        
        // Reset modal flag after a short delay
        setTimeout(function() {
            modalJustClosed = false;
        }, 1000);
        
        console.log('=== END MODAL CLOSE ===');
    });
});

function loadMeasurementData(measurementId) {
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/get_measurement/' + measurementId,
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
            
            // Handle new siding measurements
            if (attributes.siding_measurements && Array.isArray(attributes.siding_measurements)) {
                // Clear existing rows
                $('#estimate-rows-container').html('');
                estimateRowCounter = 0;
                
                // Add rows for each siding measurement
                attributes.siding_measurements.forEach(function(measurement, index) {
                    addEstimateRow('siding');
                    var rowIndex = estimateRowCounter;
                    $('#measurement_name_' + rowIndex).val(measurement.name || '');
                    $('#measurement_value_' + rowIndex).val(measurement.value || '');
                    $('#measurement_unit_' + rowIndex).val(measurement.unit || '');
                });
                
                // Show remove buttons if more than one row
                if (attributes.siding_measurements.length > 1) {
                    $('#estimate-rows-container .estimate-row .btn-danger').show();
                }
            }
            
            // Handle new roofing measurements
            if (attributes.roofing_measurements && Array.isArray(attributes.roofing_measurements)) {
                // Clear existing rows
                $('#estimate-rows-container-roofing').html('');
                estimateRowCounterRoofing = 0;
                
                // Add rows for each roofing measurement
                attributes.roofing_measurements.forEach(function(measurement, index) {
                    addEstimateRow('roofing');
                    var rowIndex = estimateRowCounterRoofing;
                    $('#measurement_name_roofing_' + rowIndex).val(measurement.name || '');
                    $('#measurement_value_roofing_' + rowIndex).val(measurement.value || '');
                    $('#measurement_unit_roofing_' + rowIndex).val(measurement.unit || '');
                });
                
                // Show remove buttons if more than one row
                if (attributes.roofing_measurements.length > 1) {
                    $('#estimate-rows-container-roofing .estimate-row .btn-danger').show();
                }
            }
            
            // Handle other category data (legacy)
            Object.keys(attributes).forEach(function(category) {
                if (category !== 'windows' && category !== 'doors' && category !== 'siding_measurements' && category !== 'roofing_measurements') {
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
            url: admin_url + 'ella_contractors/measurements/delete/' + measurementId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    loadMeasurements(); // Reload measurements after deletion
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

    // Preserve appointment ID when switching tabs
    var appointmentId = $('input[name="appointment_id"]').val();
    var relId = $('input[name="rel_id"]').val();
    var relType = $('input[name="rel_type"]').val();
    
    // Ensure appointment ID is preserved in all hidden fields
    $('input[name="appointment_id"]').val(appointmentId);
    $('input[name="rel_id"]').val(relId);
    $('input[name="rel_type"]').val(relType);

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
            var rowId = category + '_row_' + measurement.id;
            var row = '<tr id="' + rowId + '" data-measurement-id="' + measurement.id + '">';
            row += '<td>' + (measurement.designator || '') + '</td>';
            row += '<td>' + (measurement.name || '') + '</td>';
            row += '<td>' + (measurement.location_label || '') + '</td>';
            row += '<td>' + (measurement.level_label || '') + '</td>';
            row += '<td>' + (measurement.width_val || '') + '</td>';
            row += '<td>' + (measurement.height_val || '') + '</td>';
            row += '<td>' + (measurement.united_inches_val || '') + '</td>';
            row += '<td>' + (measurement.area_val || '') + '</td>';
            row += '<td>';
            row += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'' + category + '\')" title="Edit"><i class="fa fa-edit"></i></button> ';
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

// Save measurement using new simplified structure
$('#saveMeasurement').on('click', function() {
    var formData = $('#measurementForm').serializeArray();
    var data = {};
    
    // Convert form data to object
    $.each(formData, function(i, field) {
        data[field.name] = field.value;
    });
    
    // Debug: Log measurement ID
    console.log('Main Save - Measurement ID from form:', data.id);
    
    // Collect data from siding and roofing estimate rows
    var sidingMeasurements = [];
    var roofingMeasurements = [];
    var hasValidMeasurement = false;
    
    // Collect siding measurements
    $('#estimate-rows-container .estimate-row').each(function() {
        var name = $(this).find('input[name*="[name]"]').val().trim();
        var value = $(this).find('input[name*="[value]"]').val().trim();
        var unit = $(this).find('select[name*="[unit]"]').val();
        
        if (name && value && unit) {
            sidingMeasurements.push({
                name: name,
                value: parseFloat(value),
                unit: unit
            });
            hasValidMeasurement = true;
        }
    });
    
    // Collect roofing measurements
    $('#estimate-rows-container-roofing .estimate-row').each(function() {
        var name = $(this).find('input[name*="[name]"]').val().trim();
        var value = $(this).find('input[name*="[value]"]').val().trim();
        var unit = $(this).find('select[name*="[unit]"]').val();
        
        if (name && value && unit) {
            roofingMeasurements.push({
                name: name,
                value: parseFloat(value),
                unit: unit
            });
            hasValidMeasurement = true;
        }
    });
    
    
    // Collect data from all tabs (windows, doors) - keep existing functionality
    var allTabsData = collectAllTabsData();
    
    // Add new measurements to the data
    if (sidingMeasurements.length > 0) {
        allTabsData.siding_measurements = sidingMeasurements;
    }
    if (roofingMeasurements.length > 0) {
        allTabsData.roofing_measurements = roofingMeasurements;
    }
    
    // Merge all data
    $.extend(data, allTabsData);
    
    // Set category to 'other' since we're saving all tabs (combined measurements)
    data.category = 'other';
    
    // Validation
    if (Object.keys(allTabsData).length === 0 && !hasValidMeasurement) {
        alert('Please enter at least one measurement in any category before saving.');
        return false;
    }
    
    // Show loading indicator
    var submitBtn = $(this);
    var originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('Saving...');
    
    // Add CSRF token to data
    data[csrf_token_name] = csrf_hash;
    
    // Debug: Log the data being sent
    console.log('Sending measurement data:', data);
    
    // Save via AJAX using measurements controller
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/save',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            // Reset button
            submitBtn.prop('disabled', false).text(originalText);
            
            // Debug: Log the response
            console.log('Save measurement response:', response);
            
            if (response.success) {
                console.log('=== MEASUREMENT SAVE SUCCESS ===');
                console.log('Setting measurementSaved flag to true');
                measurementSaved = true; // Set flag to indicate successful save
                console.log('measurementSaved flag is now:', measurementSaved);
                alert_float('success', 'Measurement saved successfully!');
                $('#measurementModal').modal('hide');
                // Use global refresh function to maintain current tab
                if (typeof refreshAppointmentData === 'function') {
                    console.log('Using refreshAppointmentData() for save success');
                    refreshAppointmentData();
                } else {
                    console.log('Using loadMeasurements() for save success');
                    loadMeasurements();
                }
            } else {
                alert_float('danger', 'Error saving measurement: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            // Reset button
            submitBtn.prop('disabled', false).text(originalText);
            console.error('AJAX Error:', error);
            console.error('Response:', xhr.responseText);
            console.error('Status:', xhr.status);
            alert_float('danger', 'Error saving measurement: ' + error + ' (Status: ' + xhr.status + ')');
        }
    });
});

// Save only current category's inline rows (called by per-tab Save buttons)
$(document).on('click', '#js-save-windows, #js-save-doors', function() {
    var which = $(this).attr('id') === 'js-save-windows' ? 'windows' : 'doors';
    var bulk = { windows: [], doors: [] };
    var category = which;
    var tbody = $('#' + category + '-tbody');
    tbody.find('tr').each(function() {
        var row = $(this);
        var isInline = row.hasClass('inline-measure-row');
        var item = { category: category, rel_type: 'appointment', rel_id: appointmentId, appointment_id: appointmentId, length_unit: 'in', area_unit: 'sqft', ui_unit: 'in' };
        if (isInline) {
            item.designator = row.find('.cell-designator').val() || '';
            item.name = row.find('.cell-name').val() || '';
            item.location_label = row.find('.cell-location').val() || '';
            item.level_label = row.find('.cell-level').val() || '';
            item.quantity = 1;
            item.width_val = row.find('.cell-width').val() || '';
            item.height_val = row.find('.cell-height').val() || '';
            item.united_inches_val = row.find('.cell-ui-text').text() || '';
            item.area_val = row.find('.cell-area-text').text() || '';
        } else {
            var cells = row.find('td');
            item.designator = cells.eq(0).text().trim();
            item.name = cells.eq(1).text().trim();
            item.location_label = cells.eq(2).text().trim();
            item.level_label = cells.eq(3).text().trim();
            item.width_val = cells.eq(4).text().trim();
            item.height_val = cells.eq(5).text().trim();
            item.united_inches_val = cells.eq(6).text().trim();
            item.area_val = cells.eq(7).text().trim();
        }
        if (row.data('measurement-id')) { item.id = row.data('measurement-id'); }
        if (item.name) { 
            console.log('Collected item for', category, ':', item);
            bulk[category].push(item); 
        }
    });

    var payload = { 
        bulk: bulk,
        appointment_id: appointmentId,
        category: 'combined'
    };
    
    // Include measurement ID if editing existing measurement
    var measurementId = $('#measurement_id').val();
    console.log('Windows/Doors Save - Measurement ID:', measurementId);
    if (measurementId) {
        payload.id = measurementId;
    }
    
    payload[csrf_token_name] = csrf_hash;
    
    console.log('Sending payload for', which, ':', payload);

    $.ajax({
        url: admin_url + 'ella_contractors/measurements/save',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(resp) {
            if (resp && resp.success) {
                alert_float('success', (which === 'windows' ? 'Windows' : 'Doors') + ' saved');
                
                // Parse the attributes from the response
                var savedList = [];
                console.log('Response data:', resp.data);
                if (resp.data && resp.data.attributes) {
                    // New response format with attributes directly
                    savedList = resp.data.attributes[which] || [];
                    console.log('Saved list for', which, ':', savedList);
                } else if (resp.data && resp.data.attributes_json) {
                    // Fallback to old format
                    try {
                        var attributes = JSON.parse(resp.data.attributes_json);
                        console.log('Parsed attributes:', attributes);
                        savedList = attributes[which] || [];
                        console.log('Saved list for', which, ':', savedList);
                    } catch (e) {
                        console.error('Error parsing attributes_json:', e);
                    }
                }
                
                // Update the table with saved data
                var tbody = $('#' + which + '-tbody');
                tbody.html('');
                console.log('Updating table for', which, 'with', savedList.length, 'items');
                savedList.forEach(function(item) { 
                    console.log('Adding item to table:', item);
                    appendInlineRow(which, item); 
                });
                
                // Also refresh the main measurements list
                if (typeof refreshAppointmentData === 'function') {
                    refreshAppointmentData(); // Don't force tab switch, maintain current tab
                } else {
                    loadMeasurements(); // Fallback to old method
                }
            } else {
                alert_float('danger', (resp && resp.message) ? resp.message : 'Failed to save');
            }
        },
        error: function(xhr) {
            alert_float('danger', 'Error saving: ' + (xhr.statusText || 'Unknown'));
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
        url: admin_url + 'ella_contractors/measurements/save',
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

function buildLocationOptions(selected) {
    var html = '<option value="">Select Location</option>';
    for (var i = 1; i <= 10; i++) {
        var val = 'Bedroom ' + i;
        var sel = (String(selected) === String(val)) ? ' selected' : '';
        html += '<option value="' + val + '"' + sel + '>' + val + '</option>';
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
    console.log('appendInlineRow called with category:', category, 'data:', existingData);
    var tbody = $('#' + category + '-tbody');
    console.log('Found tbody:', tbody.length, 'tbody element:', tbody[0]);
    
    if (tbody.length === 0) {
        console.error('Could not find tbody for category:', category);
        return;
    }
    
    var rowId = category + '_inline_' + Date.now();
    var d = existingData || {};
    var row = '<tr id="' + rowId + '" class="inline-measure-row" data-category="' + category + '"' + (d.id ? ' data-measurement-id="' + d.id + '"' : '') + '>';
    row += '<td><input type="text" class="form-control input-sm cell-designator" value="' + (d.designator || '') + '"></td>';
    row += '<td><input type="text" class="form-control input-sm cell-name" value="' + (d.name || '') + '" required></td>';
    row += '<td><select class="form-control input-sm cell-location">' + buildLocationOptions(d.location_label) + '</select></td>';
    row += '<td><select class="form-control input-sm cell-level">' + buildLevelOptions(d.level_label) + '</select></td>';
    row += '<td><input type="number" step="0.01" class="form-control input-sm cell-width" value="' + (d.width_val || '') + '"></td>';
    row += '<td><input type="number" step="0.01" class="form-control input-sm cell-height" value="' + (d.height_val || '') + '"></td>';
    row += '<td><span class="cell-ui-text">' + (d.united_inches_val || '') + '</span></td>';
    row += '<td><span class="cell-area-text">' + (d.area_val || '') + '</span></td>';
    row += '<td><button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button></td>';
    row += '</tr>';
    
    console.log('Appending row:', row);
    tbody.append(row);
    console.log('Row appended successfully. Tbody now has', tbody.find('tr').length, 'rows');
}

// Convert existing text row into inline editable inputs
function editTableRow(rowId, category) {
    var row = $('#' + rowId);
    var cells = row.find('td');
    var existingId = row.data('measurement-id') || '';
    var data = {
        designator: cells.eq(0).text(),
        name: cells.eq(1).text(),
        location_label: cells.eq(2).text(),
        level_label: cells.eq(3).text(),
        width_val: cells.eq(4).text(),
        height_val: cells.eq(5).text(),
        united_inches_val: cells.eq(6).text(),
        area_val: cells.eq(7).text(),
        id: existingId
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

function renderSavedRow(row, category, data, id) {
    var rowId = row.attr('id');
    var html = '';
    html += '<td>' + (data.designator || '') + '</td>';
    html += '<td>' + (data.name || '') + '</td>';
    html += '<td>' + (data.location_label || '') + '</td>';
    html += '<td>' + (data.level_label || '') + '</td>';
    html += '<td>' + (data.width_val || '') + '</td>';
    html += '<td>' + (data.height_val || '') + '</td>';
    html += '<td>' + (data.united_inches_val || '') + '</td>';
    html += '<td>' + (data.area_val || '') + '</td>';
    var actions = '';
    actions += '<button class="btn btn-default btn-xs" onclick="editTableRow(\'' + rowId + '\', \'' + category + '\')" title="Edit"><i class="fa fa-edit"></i></button> ';
    if (id) {
        actions += '<button class="btn btn-danger btn-xs" onclick="deleteMeasurement(' + id + ')" title="Delete"><i class="fa fa-trash"></i></button>';
    } else {
        actions += '<button class="btn btn-danger btn-xs" onclick="removeTableRow(\'' + rowId + '\')" title="Remove"><i class="fa fa-trash"></i></button>';
    }
    html += '<td>' + actions + '</td>';
    row.removeClass('inline-measure-row').attr('data-measurement-id', id || '').html(html);
}
</script>

