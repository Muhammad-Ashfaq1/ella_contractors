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
    
    // Reset flags
    if (forceReload) {
        forceReload = false;
    }
    if (modalIsOpening) {
        modalIsOpening = false;
    }
    
    $('#measurements-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading measurements...</p></div>');
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/get_appointment_measurements/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayMeasurements(response.data);
            } else {
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
 
    
    if (measurements.length === 0) {
        $('#measurements-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found for this appointment.</p></div>');
        return;
    }

    var html = '<div class="table-responsive"><table class="table table-hover" style="margin-bottom: 0;">';
    html += '<thead style="background-color: #2c3e50; color: white;">';
    html += '<tr>';
    html += '<th style="text-align: center; padding: 12px 8px; font-weight: 600;">Record</th>';
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
        
        var sidingCount = (attrs.siding_measurements && Array.isArray(attrs.siding_measurements)) ? attrs.siding_measurements.length : 0;
        var roofingCount = (attrs.roofing_measurements && Array.isArray(attrs.roofing_measurements)) ? attrs.roofing_measurements.length : 0;

        var rowClass = (idx % 2 === 0) ? 'style="background-color: #f8f9fa;"' : 'style="background-color: white;"';
        
        html += '<tr ' + rowClass + '>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;">';
        var categoryDisplay = (measurement.category === 'other') ? 'COMBINED' : (measurement.category || 'COMBINED').toUpperCase();
        html += '<span style="background-color: #3498db; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">' + categoryDisplay + '</span>';
        html += ' <strong>#' + measurement.id + '</strong>';
        html += '</td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + sidingCount + ' items</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;"><strong>' + roofingCount + ' items</strong></td>';
        html += '<td style="text-align: center; padding: 12px 8px; vertical-align: middle;">';
        html += '<div style="display: flex; flex-direction: row; gap: 4px; align-items: center; justify-content: center;">';
        html += '<button class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="editMeasurement(' + measurement.id + ')" title="Edit Measurement"><i class="fa fa-edit"></i></button>';
        html += '<button class="btn btn-sm" style="background-color: #dc3545; border: 1px solid #dc3545; color: white; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteMeasurement(' + measurement.id + ')" title="Delete Measurement"><i class="fa fa-trash"></i></button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    
    // Store the content before displaying it
    lastMeasurementsContent = html;
    
    $('#measurements-container').html(html);
}

function openMeasurementModal(measurementId = null) {    
    // Set flag to indicate modal is opening
    modalIsOpening = true;
    
    // Reset the flags when opening modal
    measurementSaved = false;
    modalJustClosed = false;
    
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
    
    if (measurementId) {
        // Load measurement data for editing
        loadMeasurementData(measurementId);
    }
    
    $('#measurementModal').modal('show');
    
    // Reset modal opening flag after a short delay in case modal doesn't open
    setTimeout(function() {
        if (modalIsOpening) {
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
        
        // Store current measurements content before opening modal
        var currentContent = $('#measurements-container').html();
        if (currentContent && currentContent.length > 0 && 
            !currentContent.includes('Loading measurements') &&
            !currentContent.includes('fa-spinner')) {
            lastMeasurementsContent = currentContent;
        }
        
        // Ensure measurements tab is visible
        if (!$('#measurements-tab').hasClass('active')) {
            $('a[href="#measurements-tab"]').tab('show');
        }
    });
    
    $('#measurementModal').on('hidden.bs.modal', function() {
      // Reset modal opening flag
        modalIsOpening = false;
        
        // Set flag to indicate modal just closed
        modalJustClosed = true;
        
        if (measurementSaved) {
            // Only reload if measurement was actually saved
            
            // Ensure measurements tab is shown
            setTimeout(function() {
                // Force show measurements tab
                $('a[href="#measurements-tab"]').tab('show');
                
                // Reload measurements data
                if (typeof refreshAppointmentData === 'function') {
                    refreshAppointmentData('measurements');
                } else {
                    forceReload = true;
                    loadMeasurements();
                }
            }, 150);
            
            measurementSaved = false; // Reset flag
        } else {
            // Force show measurements tab and restore data
            setTimeout(function() {
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
                    $('#measurements-container').html(lastMeasurementsContent);
                } else {
                    forceReload = true;
                    loadMeasurements();
                }
            }, 150);
        }
        
        // Reset modal flag after a short delay
        setTimeout(function() {
            modalJustClosed = false;
        }, 1000);
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
                if (category !== 'siding_measurements' && category !== 'roofing_measurements') {
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
    ['siding', 'roofing'].forEach(function(category) {
        var categoryData = {};
        
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
    });
    
    return allData;
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
});

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
    
    
    // Collect data from all tabs
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
    
    // Save via AJAX using measurements controller
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/save',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            // Reset button
            submitBtn.prop('disabled', false).text(originalText);
            
            if (response.success) {
                measurementSaved = true; // Set flag to indicate successful save
                alert_float('success', 'Measurement saved successfully!');
                $('#measurementModal').modal('hide');
                // Use global refresh function to maintain current tab
                if (typeof refreshAppointmentData === 'function') {
                    refreshAppointmentData();
                } else {
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

// AJAX save functionality for measurements
function saveMeasurementAjax(formData, callback) {
    // Get CSRF token
    var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
    
    // Add CSRF token to form data
    formData[csrfData.token_name] = csrfData.hash;
    
    // Debug logging
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/save',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
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
</script>

