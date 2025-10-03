/**
 * Measurements JavaScript Functions
 * Handles all measurement-related functionality for appointments
 */

// Global variables for measurement functionality
var measurementRowCounter = 0;
var estimateRowCounter = 0;
var estimateRowCounterRoofing = 0;

// Initialize measurement functionality
function initMeasurements() {
    // Load measurements when page loads
    loadMeasurements();
    
    // Measurement modal event handlers
    $('#measurementModal').on('hidden.bs.modal', function() {
        // Small delay to ensure any pending operations complete
        setTimeout(function() {
            loadMeasurements();
            // Switch to measurements tab to show updated data
            $('a[href="#measurements-tab"]').tab('show');
        }, 100);
    });
    
    // Initialize measurement form handlers
    initMeasurementFormHandlers();
}

// Initialize measurement form event handlers
function initMeasurementFormHandlers() {
    // Add estimate row buttons
    $(document).on('click', '.add-estimate-row', function() {
        var category = $(this).data('category');
        addEstimateRow(category);
    });
    
    // Remove estimate row buttons
    $(document).on('click', '.remove-estimate-row', function() {
        $(this).closest('.estimate-row').remove();
    });
    
    // Save measurement button
    $('#saveMeasurement').on('click', function() {
        var formData = collectMeasurementFormData();
        if (formData) {
            saveMeasurementAjax(formData, function(success) {
                if (success) {
                    alert_float('success', 'Measurement saved successfully!');
                    $('#measurementModal').modal('hide');
                    // Use global refresh function to reload data and switch to measurements tab
                    if (typeof refreshAppointmentData === 'function') {
                        refreshAppointmentData('measurements-tab');
                    } else {
                        loadMeasurements(); // Fallback to old method
                    }
                }
            });
        }
    });
    
    // Windows/Doors save buttons
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
                        refreshAppointmentData('measurements-tab');
                    } else {
                        loadMeasurements(); // Fallback to old method
                    }
                } else {
                    alert_float('danger', (resp && resp.message) ? resp.message : 'Failed to save');
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while saving');
            }
        });
    });
}

// Load measurements for the appointment
function loadMeasurements() {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_measurements/' + appointmentId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayMeasurements(response.data);
            } else {
                console.error('Failed to load measurements:', response.message);
            }
        },
        error: function() {
            console.error('Error loading measurements');
        }
    });
}

// Display measurements in the measurements tab
function displayMeasurements(measurements) {
    var container = $('#measurements-container');
    if (!container.length) {
        console.error('Measurements container not found');
        return;
    }
    
    if (!measurements || measurements.length === 0) {
        container.html('<p class="text-muted">No measurements found.</p>');
        return;
    }
    
    // Create table structure
    var html = '<div class="table-responsive">';
    html += '<table class="table table-striped table-hover">';
    html += '<thead class="thead-dark">';
    html += '<tr>';
    html += '<th>Record</th>';
    html += '<th>Windows</th>';
    html += '<th>Doors</th>';
    html += '<th>Siding Measurements</th>';
    html += '<th>Roofing Measurements</th>';
    html += '<th>Actions</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    measurements.forEach(function(measurement, idx) {
        var category = measurement.category || 'general';
        var name = measurement.name || 'Unnamed Measurement';
        var recordId = 'COMBINED #' + measurement.id;
        
        // Initialize counts
        var windowsCount = 0;
        var doorsCount = 0;
        var sidingCount = 0;
        var roofingCount = 0;
        
        // Parse attributes_json for counts
        if (measurement.attributes_json) {
            try {
                var attributes = JSON.parse(measurement.attributes_json);
                
                // Count windows and doors
                windowsCount = (attributes.windows || []).length;
                doorsCount = (attributes.doors || []).length;
                
                // Count siding and roofing measurements
                sidingCount = (attributes.siding || []).length;
                roofingCount = (attributes.roofing || []).length;
            } catch (e) {
                console.error('Error parsing attributes_json:', e);
            }
        }
        
        html += '<tr data-id="' + measurement.id + '">';
        html += '<td><span class="badge badge-primary">' + recordId + '</span></td>';
        html += '<td>' + windowsCount + '</td>';
        html += '<td>' + doorsCount + '</td>';
        html += '<td>' + sidingCount + ' items</td>';
        html += '<td>' + roofingCount + ' items</td>';
        html += '<td>';
        html += '<button class="btn btn-sm btn-info" onclick="editMeasurement(' + measurement.id + ')" title="Edit">';
        html += '<i class="fa fa-edit"></i>';
        html += '</button> ';
        html += '<button class="btn btn-sm btn-danger" onclick="deleteMeasurement(' + measurement.id + ')" title="Delete">';
        html += '<i class="fa fa-trash"></i>';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
    });
    
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    
    container.html(html);
}

// Open measurement modal
function openMeasurementModal(measurementId = null) {
    // Set measurement ID first before resetting form
    $('#measurement_id').val(measurementId || '');
    
    // Reset form
    $('#measurementForm')[0].reset();
    
    // Restore measurement ID after reset
    $('#measurement_id').val(measurementId || '');
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
                '<label for="measurement_name_0">Measurement Name</label>' +
                '<input type="text" class="form-control" name="measurements[0][name]" id="measurement_name_0" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_0">Measurement Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="measurements[0][value]" id="measurement_value_0" placeholder="0.0000">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_0">Unit</label>' +
                '<select class="form-control" name="measurements[0][unit]" id="measurement_unit_0">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="lf">Linear Feet (lf)</option>' +
                    '<option value="ea">Each (ea)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="gal">Gallons (gal)</option>' +
                    '<option value="lb">Pounds (lb)</option>' +
                    '<option value="kg">Kilograms (kg)</option>' +
                    '<option value="ton">Tons (ton)</option>' +
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
                '<label for="measurement_name_roofing_0">Measurement Name</label>' +
                '<input type="text" class="form-control" name="measurements_roofing[0][name]" id="measurement_name_roofing_0" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_roofing_0">Measurement Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="measurements_roofing[0][value]" id="measurement_value_roofing_0" placeholder="0.0000">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_roofing_0">Unit</label>' +
                '<select class="form-control" name="measurements_roofing[0][unit]" id="measurement_unit_roofing_0">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="lf">Linear Feet (lf)</option>' +
                    '<option value="ea">Each (ea)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="gal">Gallons (gal)</option>' +
                    '<option value="lb">Pounds (lb)</option>' +
                    '<option value="kg">Kilograms (kg)</option>' +
                    '<option value="ton">Tons (ton)</option>' +
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
}

// Load measurement data for editing
function loadMeasurementData(measurementId) {
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/get_measurement/' + measurementId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Small delay to ensure modal is fully loaded
                setTimeout(function() {
                    populateMeasurementForm(response.data);
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

// Populate measurement form with data
function populateMeasurementForm(data) {
    if (!data) return;
    
    // Handle attributes_json for combined measurements (Windows/Doors)
    if (data.attributes_json) {
        try {
            var attributes = JSON.parse(data.attributes_json);
            
            // Handle siding measurements
            if (attributes.siding_measurements && attributes.siding_measurements.length > 0) {
                $('#selected-category').val('siding');
                $('#category-tabs li').removeClass('active');
                $('#category-tabs li:first').addClass('active');
                $('#category-tabs li:first a').tab('show');
                
                // Clear existing rows
                $('#siding-tab').html('');
                attributes.siding_measurements.forEach(function(measurement, index) {
                    addEstimateRow('siding', measurement);
                });
            }
            
            // Handle roofing measurements
            if (attributes.roofing_measurements && attributes.roofing_measurements.length > 0) {
                $('#selected-category').val('roofing');
                $('#category-tabs li').removeClass('active');
                $('#category-tabs li:eq(1)').addClass('active');
                $('#category-tabs li:eq(1) a').tab('show');
                
                // Clear existing rows
                $('#roofing-tab').html('');
                attributes.roofing_measurements.forEach(function(measurement, index) {
                    addEstimateRow('roofing', measurement);
                });
            }
            
            // Handle Windows and Doors data
            displayExistingWindowsDoorsData(attributes);
            
        } catch (e) {
            console.error('Error parsing attributes_json:', e);
        }
    }
}

// Display existing windows and doors data in the measurement modal
function displayExistingWindowsDoorsData(attributes) {
    // Clear existing data first
    $('#windows-tbody').html('');
    $('#doors-tbody').html('');
    
    // Display windows data
    if (attributes.windows && attributes.windows.length > 0) {
        attributes.windows.forEach(function(window) {
            appendInlineRow('windows', window);
        });
    }
    
    // Display doors data
    if (attributes.doors && attributes.doors.length > 0) {
        attributes.doors.forEach(function(door) {
            appendInlineRow('doors', door);
        });
    }
}

// Edit measurement
function editMeasurement(measurementId) {
    openMeasurementModal(measurementId);
}

// Delete measurement
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
                    alert_float('success', 'Measurement deleted successfully!');
                    // Use global refresh function to reload data and switch to measurements tab
                    if (typeof refreshAppointmentData === 'function') {
                        refreshAppointmentData('measurements-tab');
                    } else {
                        loadMeasurements(); // Fallback to old method
                    }
                } else {
                    alert_float('danger', response.message || 'Failed to delete measurement');
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while deleting the measurement');
            }
        });
    }
}

// Add estimate row
function addEstimateRow(category, data = null) {
    var container = $('#' + category + '-tab');
    if (!container.length) {
        console.error('Container not found for category:', category);
        return;
    }
    
    measurementRowCounter++;
    var rowId = category + '_row_' + measurementRowCounter;
    
    var html = '<div class="estimate-row" id="' + rowId + '">';
    html += '<div class="row">';
    html += '<div class="col-md-4">';
    html += '<div class="form-group">';
    html += '<label>Measurement Name</label>';
    html += '<input type="text" class="form-control measurement-name" name="measurement_name[]" value="' + (data ? (data.measurement_name || '') : '') + '" required>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Measurement Value</label>';
    html += '<input type="number" step="0.01" class="form-control measurement-value" name="measurement_value[]" value="' + (data ? (data.measurement_value || '') : '') + '" required>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-3">';
    html += '<div class="form-group">';
    html += '<label>Unit</label>';
    html += '<select class="form-control measurement-unit" name="measurement_unit[]" required>';
    html += '<option value="ft"' + (data && data.measurement_unit === 'ft' ? ' selected' : '') + '>Feet (ft)</option>';
    html += '<option value="in"' + (data && data.measurement_unit === 'in' ? ' selected' : '') + '>Inches (in)</option>';
    html += '<option value="m"' + (data && data.measurement_unit === 'm' ? ' selected' : '') + '>Meters (m)</option>';
    html += '<option value="cm"' + (data && data.measurement_unit === 'cm' ? ' selected' : '') + '>Centimeters (cm)</option>';
    html += '</select>';
    html += '</div>';
    html += '</div>';
    html += '<div class="col-md-2">';
    html += '<div class="form-group">';
    html += '<label>&nbsp;</label>';
    html += '<button type="button" class="btn btn-danger btn-block remove-estimate-row" data-row="' + rowId + '">';
    html += '<i class="fa fa-trash"></i>';
    html += '</button>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    container.append(html);
}

// Collect measurement form data
function collectMeasurementFormData() {
    var category = $('#selected-category').val();
    var measurements = [];
    
    // Collect siding measurements
    $('#siding-tab .estimate-row').each(function() {
        var row = $(this);
        var name = row.find('.measurement-name').val();
        var value = row.find('.measurement-value').val();
        var unit = row.find('.measurement-unit').val();
        
        if (name && value) {
            measurements.push({
                measurement_name: name,
                measurement_value: parseFloat(value),
                measurement_unit: unit
            });
        }
    });
    
    // Collect roofing measurements
    $('#roofing-tab .estimate-row').each(function() {
        var row = $(this);
        var name = row.find('.measurement-name').val();
        var value = row.find('.measurement-value').val();
        var unit = row.find('.measurement-unit').val();
        
        if (name && value) {
            measurements.push({
                measurement_name: name,
                measurement_value: parseFloat(value),
                measurement_unit: unit
            });
        }
    });
    
    if (measurements.length === 0) {
        alert_float('warning', 'Please add at least one measurement');
        return null;
    }
    
    return {
        category: category,
        measurements: measurements,
        appointment_id: appointmentId,
        rel_type: 'appointment',
        rel_id: appointmentId
    };
}

// AJAX save functionality for measurements
function saveMeasurementAjax(formData, callback) {
    var measurementId = $('#measurement_id').val();
    
    var payload = {
        category: formData.category,
        measurements: formData.measurements,
        appointment_id: formData.appointment_id,
        rel_type: formData.rel_type,
        rel_id: formData.rel_id
    };
    
    if (measurementId) {
        payload.id = measurementId;
    }
    
    payload[csrf_token_name] = csrf_hash;
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/save',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                callback(true);
            } else {
                alert_float('danger', response.message || 'Failed to save measurement');
                callback(false);
            }
        },
        error: function() {
            alert_float('danger', 'An error occurred while saving the measurement');
            callback(false);
        }
    });
}

// Helper functions for Windows/Doors
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

// Remove table row
function removeTableRow(rowId) {
    $('#' + rowId).remove();
}

// Add estimate row function
function addEstimateRow(category = 'siding') {
    var containerId = category === 'roofing' ? '#estimate-rows-container-roofing' : '#estimate-rows-container';
    var counter = category === 'roofing' ? estimateRowCounterRoofing : estimateRowCounter;
    var prefix = category === 'roofing' ? 'measurements_roofing' : 'measurements';
    var idPrefix = category === 'roofing' ? 'measurement_name_roofing_' : 'measurement_name_';
    
    counter++;
    
    var rowHtml = '<div class="row estimate-row" data-row="' + counter + '">' +
        '<div class="col-md-4">' +
            '<div class="form-group">' +
                '<label for="' + idPrefix + counter + '">Measurement Name</label>' +
                '<input type="text" class="form-control" name="' + prefix + '[' + counter + '][name]" id="' + idPrefix + counter + '" placeholder="Enter measurement name">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_value_' + (category === 'roofing' ? 'roofing_' : '') + counter + '">Measurement Value</label>' +
                '<input type="number" step="0.0001" class="form-control" name="' + prefix + '[' + counter + '][value]" id="measurement_value_' + (category === 'roofing' ? 'roofing_' : '') + counter + '" placeholder="0.0000">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="measurement_unit_' + (category === 'roofing' ? 'roofing_' : '') + counter + '">Unit</label>' +
                '<select class="form-control" name="' + prefix + '[' + counter + '][unit]" id="measurement_unit_' + (category === 'roofing' ? 'roofing_' : '') + counter + '">' +
                    '<option value="">Select Unit</option>' +
                    '<option value="sqft">Square Feet (sqft)</option>' +
                    '<option value="lf">Linear Feet (lf)</option>' +
                    '<option value="ea">Each (ea)</option>' +
                    '<option value="in">Inches (in)</option>' +
                    '<option value="ft">Feet (ft)</option>' +
                    '<option value="yd">Yards (yd)</option>' +
                    '<option value="m">Meters (m)</option>' +
                    '<option value="cm">Centimeters (cm)</option>' +
                    '<option value="mm">Millimeters (mm)</option>' +
                    '<option value="gal">Gallons (gal)</option>' +
                    '<option value="lb">Pounds (lb)</option>' +
                    '<option value="kg">Kilograms (kg)</option>' +
                    '<option value="ton">Tons (ton)</option>' +
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
    
    // Update counter
    if (category === 'roofing') {
        estimateRowCounterRoofing = counter;
    } else {
        estimateRowCounter = counter;
    }
    
    // Show remove buttons for all rows except the first one
    $(containerId + ' .estimate-row').each(function(index) {
        if (index > 0) {
            $(this).find('.btn-danger').show();
        }
    });
}

// Remove estimate row function
function removeEstimateRow(button) {
    $(button).closest('.estimate-row').remove();
    
    // Hide remove buttons if only one row left
    var container = $(button).closest('.estimate-rows-container, #estimate-rows-container, #estimate-rows-container-roofing');
    if (container.find('.estimate-row').length <= 1) {
        container.find('.btn-danger').hide();
    }
}

// Initialize when document is ready
$(document).ready(function() {
    initMeasurements();
});
