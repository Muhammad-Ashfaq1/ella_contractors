<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Generic measurement system - fully dynamic tabs
var tabCounter = 0;
var measurementRowCounters = {};
var measurementSaved = false;
var originalHash = '';

// Global function to refresh measurements - can be called from anywhere
window.refreshMeasurements = function() {
    if (typeof loadMeasurements === 'function') {
        loadMeasurements();
    }
};

// Units configuration
var measurementUnits = [
    {value: '', label: 'Select Unit', isPlaceholder: true},
    {value: 'cm', label: 'Centimeters (cm)'},
    {value: 'ft', label: 'Feet (ft)'},
    {value: 'in', label: 'Inches (in)'},
    {value: 'm', label: 'Meters (m)'},
    {value: 'mm', label: 'Millimeters (mm)'},
    {value: 'sqft', label: 'Square Feet (sqft)'},
    {value: 'yd', label: 'Yards (yd)'}
];

/**
 * Enforce decimal limit on number inputs
 * Limits the input to specified number of decimal places (e.g., 2 for 123.45)
 */
function enforceDecimalLimit(input, decimalPlaces) {
    var value = input.value;
    
    if (value === '' || value === null) {
        return;
    }
    
    // Check if value contains a decimal point
    if (value.includes('.')) {
        var parts = value.split('.');
        
        // If decimal part exceeds the limit, truncate it
        if (parts[1] && parts[1].length > decimalPlaces) {
            input.value = parts[0] + '.' + parts[1].substring(0, decimalPlaces);
        }
    }
}

/**
 * Generic function to create measurement row HTML
 */
function createMeasurementRow(tabId, rowIndex) {
    var unitsHTML = '';
    measurementUnits.forEach(function(unit) {
        var optionClass = unit.isPlaceholder ? ' placeholder-option' : '';
        var disabledAttr = unit.isPlaceholder ? ' disabled selected' : '';
        unitsHTML += '<option value="' + unit.value + '" class="' + optionClass + '"' + disabledAttr + '>' + unit.label + '</option>';
    });
    
    var rowHtml = '<div class="row measurement-row" data-row="' + rowIndex + '" style="margin-bottom: 15px;">' +
        '<div class="col-md-4">' +
            '<div class="form-group">' +
                '<label for="name_' + tabId + '_' + rowIndex + '" style="color: #333; font-weight: 500; margin-bottom: 5px; font-size: 14px;">Name</label>' +
                '<input type="text" class="form-control" id="name_' + tabId + '_' + rowIndex + '" name="tab_measurements_' + tabId + '[' + rowIndex + '][name]" placeholder="Enter measurement name" style="border-radius: 4px; border: 1px solid #ddd;">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="value_' + tabId + '_' + rowIndex + '" style="color: #333; font-weight: 500; margin-bottom: 5px; font-size: 14px;">Value</label>' +
                '<input type="number" step="0.01" class="form-control measurement-value-input" id="value_' + tabId + '_' + rowIndex + '" name="tab_measurements_' + tabId + '[' + rowIndex + '][value]" placeholder="0.00" style="border-radius: 4px; border: 1px solid #ddd;" oninput="enforceDecimalLimit(this, 2)">' +
            '</div>' +
        '</div>' +
        '<div class="col-md-3">' +
            '<div class="form-group">' +
                '<label for="unit_' + tabId + '_' + rowIndex + '" style="color: #333; font-weight: 500; margin-bottom: 5px; font-size: 14px;">Unit</label>' +
                '<select class="form-control" id="unit_' + tabId + '_' + rowIndex + '" name="tab_measurements_' + tabId + '[' + rowIndex + '][unit]" style="border-radius: 4px; border: 1px solid #ddd;" required onchange="updateSelectPlaceholder(this)">' +
                    unitsHTML +
                '</select>' +
            '</div>' +
        '</div>' +
        '<div class="col-md-2">' +
            '<div class="form-group" style="padding-top: 25px;">' +
                '<button type="button" class="btn btn-success btn-sm" onclick="addMeasurementRow(\'' + tabId + '\')" title="Add Row" style="width: 35px; height: 35px; border-radius: 4px; padding: 0; display: inline-flex; align-items: center; justify-content: center; margin-right: 5px;">' +
                        '<i class="fa fa-plus"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-danger btn-sm remove-row-btn" onclick="removeMeasurementRow(this)" title="Remove Row" style="width: 35px; height: 35px; border-radius: 4px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">' +
                        '<i class="fa fa-minus"></i>' +
                    '</button>' +
            '</div>' +
        '</div>' +
    '</div>';
    
    return rowHtml;
}

/**
 * Add measurement row to specific tab
 */
function addMeasurementRow(tabId) {
    if (!measurementRowCounters[tabId]) {
        measurementRowCounters[tabId] = 0;
    }
    
    measurementRowCounters[tabId]++;
    var rowIndex = measurementRowCounters[tabId];
    var containerId = '#measurements-container-' + tabId;
    
    $(containerId).append(createMeasurementRow(tabId, rowIndex));
    
    // Initialize placeholder styling for the new select
    var newSelect = $(containerId + ' select').last()[0];
    updateSelectPlaceholder(newSelect);
    
    // Show/hide remove buttons
    updateRemoveButtons(tabId);
}

/**
 * Remove measurement row
 */
function removeMeasurementRow(button) {
    var container = $(button).closest('[id^="measurements-container-"]');
    $(button).closest('.measurement-row').remove();
    
    // Update remove buttons
    var tabId = container.attr('id').replace('measurements-container-', '');
    updateRemoveButtons(tabId);
}

/**
 * Update remove button visibility
 */
function updateRemoveButtons(tabId) {
    var containerId = '#measurements-container-' + tabId;
    var rowCount = $(containerId + ' .measurement-row').length;
    
    if (rowCount <= 1) {
        $(containerId + ' .remove-row-btn').hide();
    } else {
        $(containerId + ' .remove-row-btn').show();
    }
}

/**
 * Update select placeholder styling
 */
function updateSelectPlaceholder(selectElement) {
    var placeholderOption = selectElement.querySelector('option[value=""]');
    
    if (selectElement.value === '') {
        selectElement.classList.add('placeholder-active');
        // Keep placeholder disabled and selected when no value is selected
        placeholderOption.disabled = true;
        placeholderOption.selected = true;
    } else {
        selectElement.classList.remove('placeholder-active');
        // Disable placeholder option when a value is selected
        placeholderOption.disabled = true;
        placeholderOption.selected = false;
    }
}

/**
 * Add new tab - show inline input instead of prompt
 */
function addNewTab() {
    // Check if we're already in "add tab" mode
    if ($('#tab-name-input').length > 0) {
        return; // Already showing input
    }
    
    tabCounter++;
    var tabId = 'measurement_tab' + tabCounter; // Use unique prefix to avoid URL conflicts
    var tabName = 'Category';
    
    // Create inline input tab that appears after existing tabs
    var tabHtml = '<li class="active" data-tab-id="' + tabId + '" data-edit-mode="true">' +
        '<a href="#' + tabId + '-content" data-toggle="tab" data-tab-id="' + tabId + '">' +
            '<input type="text" class="custom-tab-name-input" id="tab-name-input" value="' + tabName + '" placeholder="Enter category name" onkeypress="if(event.key===\'Enter\'){saveTabName(\'' + tabId + '\');}">' +
            '<button type="button" class="btn btn-xs btn-success" onclick="saveTabName(\'' + tabId + '\')" title="Save Category" style="margin-left: 5px; padding: 2px 6px;">' +
                '<i class="fa fa-check"></i>' +
            '</button>' +
            '<button type="button" class="btn btn-xs btn-link" onclick="removeTab(\'' + tabId + '\')" title="Remove Tab" style="display: none;">' +
                '<i class="fa fa-times text-danger"></i>' +
            '</button>' +
        '</a>' +
    '</li>';
    
    $('#dynamic-tabs').append(tabHtml);
    
    // Create tab content with one measurement line by default
    var contentHtml = '<div class="tab-pane active" id="' + tabId + '-content" data-tab-id="' + tabId + '">' +
        '<div id="measurements-container-' + tabId + '">' +
            '<div class="alert alert-info" id="category-info-' + tabId + '">' +
                '<i class="fa fa-info-circle"></i> Enter category name and add measurements below.' +
            '</div>' +
        '</div>' +
    '</div>';
    
    $('#dynamic-tab-content').append(contentHtml);
    
    // Switch to new tab
    $('#dynamic-tabs li').removeClass('active');
    $('[data-tab-id="' + tabId + '"]').parent('li').addClass('active');
    $('.tab-pane').removeClass('active');
    $('#' + tabId + '-content').addClass('active');
    
    // Initialize with one measurement row
    measurementRowCounters[tabId] = 0;
    addMeasurementRow(tabId);
    
    // Prevent this tab's events from bubbling to main page
    $('[data-tab-id="' + tabId + '"]').off('shown.bs.tab.modalprevent').on('shown.bs.tab.modalprevent', function(e) {
        e.stopPropagation();
    });
    
    // Focus on input and hide Add Tab button
    setTimeout(function() {
        $('#tab-name-input').focus().select();
    }, 100);
    
    // Hide the add tab button
    $('#addTabBtn').hide();
}

/**
 * Save tab name and enable measurements
 */
function saveTabName(tabId) {
    var tabName = $('#tab-name-input').val().trim();
    
    if (!tabName) {
        alert_float('warning', 'Please enter a category name');
        $('#tab-name-input').focus();
        return;
    }

    // Check for duplicate category name within current measurement (same modal)
    var isDuplicateInModal = false;
    $('#dynamic-tabs li').not('[data-tab-id="' + tabId + '"]').each(function() {
        var existingTabName = $(this).find('.tab-title').text().trim();
        if (existingTabName.toLowerCase() === tabName.toLowerCase()) {
            isDuplicateInModal = true;
            return false; // break loop
        }
    });
    
    if (isDuplicateInModal) {
        alert_float('danger', 'Category name "' + tabName + '" already exists in this measurement. Please use a different name.');
        $('#tab-name-input').focus().select();
        return;
    }

    // Convert input to tab title span with pencil edit functionality
    var tabLink = $('[data-tab-id="' + tabId + '"] a');
    tabLink.html('<span class="tab-title">' + 
        tabName + 
        ' <i class="fa fa-pencil" onclick="editTabName(\'' + tabId + '\'); event.stopPropagation();" style="font-size: 11px; margin-left: 4px; opacity: 0.6; cursor: pointer;" title="Edit category name"></i>' +
        '</span>' +
        '<button type="button" class="btn btn-xs btn-link tab-remove-btn" onclick="removeTab(\'' + tabId + '\')" title="Remove Tab">' +
            '<i class="fa fa-times text-danger"></i>' +
        '</button>');
    
    // Add hidden field for form submission
    var hiddenField = '<input type="hidden" name="tab_name_' + tabId + '" id="tab_name_' + tabId + '" value="' + tabName + '">';
    $('#' + tabId + '-content').append(hiddenField);
    
    // Remove the info alert div
    $('#category-info-' + tabId).remove();
    
    // Show measurements container (already has content from creation)
    $('#measurements-container-' + tabId).show();
    
    // Remove edit mode flag
    $('[data-tab-id="' + tabId + '"]').removeAttr('data-edit-mode');
    
    // Show add tab button again
    $('#addTabBtn').show();
    
    alert_float('success', 'Category "' + tabName + '" created successfully');
}

/**
 * Edit tab name (category) by double-clicking
 */
function editTabName(tabId) {
    var tabLink = $('[data-tab-id="' + tabId + '"] a');
    var currentName = $('#tab_name_' + tabId).val();
    
    // Replace tab title with inline input
    tabLink.html(
        '<input type="text" class="custom-tab-name-input" id="tab-name-edit-input-' + tabId + '" value="' + currentName + '" placeholder="Enter category name" onkeypress="if(event.key===\'Enter\'){updateTabName(\'' + tabId + '\');}" style="width: 150px;">' +
        '<button type="button" class="btn btn-xs btn-success" onclick="updateTabName(\'' + tabId + '\')" title="Save Category" style="margin-left: 5px; padding: 2px 6px;">' +
            '<i class="fa fa-check"></i>' +
        '</button>' +
        '<button type="button" class="btn btn-xs btn-default" onclick="cancelEditTabName(\'' + tabId + '\')" title="Cancel" style="margin-left: 3px; padding: 2px 6px;">' +
            '<i class="fa fa-times"></i>' +
        '</button>'
    );
    
    // Focus and select the input
    setTimeout(function() {
        $('#tab-name-edit-input-' + tabId).focus().select();
    }, 100);
}

/**
 * Update tab name after editing
 */
function updateTabName(tabId) {
    var newTabName = $('#tab-name-edit-input-' + tabId).val().trim();
    var oldTabName = $('#tab_name_' + tabId).val();
    
    if (!newTabName) {
        alert_float('warning', 'Please enter a category name');
        $('#tab-name-edit-input-' + tabId).focus();
        return;
    }
    
    // Check for duplicate category name within current measurement (same modal only)
    var isDuplicate = false;
    $('#dynamic-tabs li').not('[data-tab-id="' + tabId + '"]').each(function() {
        var existingTabId = $(this).attr('data-tab-id');
        var existingName = $('#tab_name_' + existingTabId).val();
        if (existingName && existingName.toLowerCase() === newTabName.toLowerCase()) {
            isDuplicate = true;
            return false; // break loop
        }
    });
    
    if (isDuplicate) {
        alert_float('danger', 'Category name "' + newTabName + '" already exists in this measurement. Please use a different name.');
        $('#tab-name-edit-input-' + tabId).focus().select();
        return;
    }
    
    // Update tab name
    var tabLink = $('[data-tab-id="' + tabId + '"] a');
    tabLink.html('<span class="tab-title">' + 
        newTabName + 
        ' <i class="fa fa-pencil" onclick="editTabName(\'' + tabId + '\'); event.stopPropagation();" style="font-size: 11px; margin-left: 4px; opacity: 0.6; cursor: pointer;" title="Edit category name"></i>' +
        '</span>' +
        '<button type="button" class="btn btn-xs btn-link tab-remove-btn" onclick="removeTab(\'' + tabId + '\')" title="Remove Tab">' +
            '<i class="fa fa-times text-danger"></i>' +
        '</button>');
    
    // Save new tab name in hidden field
    $('#tab_name_' + tabId).val(newTabName);
    
    if (oldTabName !== newTabName) {
        alert_float('success', 'Category renamed from "' + oldTabName + '" to "' + newTabName + '"');
    }
}

/**
 * Cancel editing tab name
 */
function cancelEditTabName(tabId) {
    var currentName = $('#tab_name_' + tabId).val();
    
    // Restore original tab title
    var tabLink = $('[data-tab-id="' + tabId + '"] a');
    tabLink.html('<span class="tab-title">' + 
        currentName + 
        ' <i class="fa fa-pencil" onclick="editTabName(\'' + tabId + '\'); event.stopPropagation();" style="font-size: 11px; margin-left: 4px; opacity: 0.6; cursor: pointer;" title="Edit category name"></i>' +
        '</span>' +
        '<button type="button" class="btn btn-xs btn-link tab-remove-btn" onclick="removeTab(\'' + tabId + '\')" title="Remove Tab">' +
            '<i class="fa fa-times text-danger"></i>' +
        '</button>');
}

/**
 * Cancel tab creation
 */
function cancelTabCreation(tabId) {
    // Remove the tab
    removeTab(tabId);
    
    // Show add tab button again
    $('#addTabBtn').show();
}

/**
 * Remove tab
 */
function removeTab(tabId) {
    // Check if this is a tab in edit mode
    var isEditMode = $('[data-tab-id="' + tabId + '"]').attr('data-edit-mode');
    
    if (isEditMode) {
        // Just remove without confirmation for edit mode
        $('[data-tab-id="' + tabId + '"]').parent('li').remove();
        $('#' + tabId + '-content').remove();
        $('#addTabBtn').show();
    } else {
        // Confirm for regular tabs
        if (!confirm('Are you sure you want to remove this tab and all its measurements?')) {
            return;
        }
        
        // Remove tab and content
        $('[data-tab-id="' + tabId + '"]').parent('li').remove();
        $('#' + tabId + '-content').remove();
    }
    
    // If removed active tab, activate first tab
    if ($('#dynamic-tabs li.active').length === 0) {
        $('#dynamic-tabs li:first').addClass('active');
        $('.tab-pane:first').addClass('active');
    }
    
    delete measurementRowCounters[tabId];
}

/**
 * Open measurement modal
 */
function openMeasurementModal(measurementId = null) {    
    measurementSaved = false;
    
    // Reset form
    $('#measurementForm')[0].reset();
    $('#measurement_id').val(measurementId || '');
    
    // Clear tabs
    $('#dynamic-tabs').empty();
    $('#dynamic-tab-content').empty();
    tabCounter = 0;
    measurementRowCounters = {};
    
    // Reset button states
    $('#addTabBtn').show();
    
    if (measurementId) {
        // Load existing measurement
        loadMeasurementData(measurementId);
        $('#measurementModalLabel').text('Edit Measurement Category');
    } else {
        // Show message and add tab button
        $('#measurementModalLabel').text('Add Measurement Category');
        
        // Auto-trigger add category if no tabs exist (for new measurements)
        setTimeout(function() {
            if ($('#dynamic-tabs li').length === 0) {
                addNewTab();
            }
        }, 100);
    }
    
    $('#measurementModal').modal('show');
    
    // Prevent modal tab events from bubbling to main page
    setTimeout(function() {
        $('#measurementModal a[data-toggle="tab"]').off('shown.bs.tab.modalprevent').on('shown.bs.tab.modalprevent', function(e) {
            e.stopPropagation();
        });
    }, 200);
}

/**
 * Load measurement data for editing
 */
function loadMeasurementData(measurementId) {
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/get_measurement/' + measurementId,
        type: 'GET',
            data: csrf_token_name + '=' + csrf_hash,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var data = response.data;
                $('#measurement_id').val(data.id);
                
                // Create tab for this measurement
                tabCounter++;
                var tabId = 'measurement_tab' + tabCounter; // Use unique prefix to avoid URL conflicts
                var tabName = data.tab_name || 'Measurement';
                
                // Create tab with pencil edit functionality
                var tabHtml = '<li class="active" data-tab-id="' + tabId + '">' +
                    '<a href="#' + tabId + '-content" data-toggle="tab" data-tab-id="' + tabId + '">' +
                        '<span class="tab-title">' + 
                            tabName + 
                            ' <i class="fa fa-pencil" onclick="editTabName(\'' + tabId + '\'); event.stopPropagation();" style="font-size: 11px; margin-left: 4px; opacity: 0.6; cursor: pointer;" title="Edit category name"></i>' +
                        '</span>' +
                        '<button type="button" class="btn btn-xs btn-link tab-remove-btn" onclick="removeTab(\'' + tabId + '\')" title="Remove Tab">' +
                            '<i class="fa fa-times text-danger"></i>' +
                        '</button>' +
                    '</a>' +
                '</li>';
                
                $('#dynamic-tabs').append(tabHtml);
                
                // Create tab content
                var contentHtml = '<div class="tab-pane active" id="' + tabId + '-content" data-tab-id="' + tabId + '">' +
                    '<input type="hidden" name="tab_name_' + tabId + '" id="tab_name_' + tabId + '" value="' + tabName + '">' +
                    '<div id="measurements-container-' + tabId + '"></div>' +
                '</div>';
                
                $('#dynamic-tab-content').append(contentHtml);
                
                // Prevent this tab's events from bubbling to main page
                $('[data-tab-id="' + tabId + '"]').off('shown.bs.tab.modalprevent').on('shown.bs.tab.modalprevent', function(e) {
                    e.stopPropagation();
                });
                
                // Populate items
                measurementRowCounters[tabId] = -1;
                if (data.items && data.items.length > 0) {
                    data.items.forEach(function(item, index) {
                        measurementRowCounters[tabId]++;
                        var rowIndex = measurementRowCounters[tabId];
                        $('#measurements-container-' + tabId).append(createMeasurementRow(tabId, rowIndex));
                        
                        // Set values
                        $('input[name="tab_measurements_' + tabId + '[' + rowIndex + '][name]"]').val(item.name);
                        $('input[name="tab_measurements_' + tabId + '[' + rowIndex + '][value]"]').val(item.value);
                        var selectElement = $('select[name="tab_measurements_' + tabId + '[' + rowIndex + '][unit]"]')[0];
                        selectElement.value = item.unit;
                        updateSelectPlaceholder(selectElement);
                    });
                } else {
                    // Add empty row
                    addMeasurementRow(tabId);
                }
                
                updateRemoveButtons(tabId);
                
                // Ensure proper button states for editing
                $('#addTabBtn').show();
            } else {
                alert_float('danger', response.message || 'Failed to load measurement');
            }
        },
        error: function() {
            alert_float('danger', 'Error loading measurement data');
        }
    });
}

/**
 * Save measurement - wrapped in document ready to ensure modal exists
 */
$(document).ready(function() {
$('#saveMeasurement').on('click', function() {
    // Check if at least one tab exists
    if ($('#dynamic-tabs li').length === 0) {
        alert_float('warning', 'Please add at least one tab with measurements');
        return;
    }
    
    // Check if any tab is still in edit mode
    if ($('#dynamic-tabs li[data-edit-mode="true"]').length > 0) {
        alert_float('warning', 'Please save the tab name first before saving measurements');
        return;
    }
    
    // Show loading
    var btn = $(this);
    var originalText = btn.text();
    btn.prop('disabled', true).text('Saving...');
    
    // Get form data and add CSRF token properly
    var formData = $('#measurementForm').serialize();
    formData += '&' + csrf_token_name + '=' + csrf_hash;
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/save',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            btn.prop('disabled', false).text(originalText);
            
            if (response.success) {
                measurementSaved = true;

                alert_float('success', response.message || 'Measurement saved successfully');
                
                // Immediately reload measurements (like Notes and Attachments tabs)
                loadMeasurements();
                
                // Close modal - measurements already reloaded
                $('#measurementModal').modal('hide');
            } else {
                alert_float('danger', response.message || 'Failed to save measurement');
            }
        },
        error: function(xhr) {
            btn.prop('disabled', false).text(originalText);
            
            // Check if it's a CSRF token error
            if (xhr.status === 403 || xhr.responseText.includes('expired') || xhr.responseText.includes('csrf')) {
                alert_float('danger', 'Session expired. Please refresh the page and try again.');
                // Optionally refresh the page
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            } else {
                alert_float('danger', 'Error saving measurement: ' + (xhr.responseText || 'Unknown error'));
            }
        }
    });
});

// Save original hash when modal opens
$('#measurementModal').on('show.bs.modal', function() {
    
    // Store the current tab from URL (not hash) for restoration after modal closes
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    originalHash = ''; // Don't use hash - we'll rely on URL params only
    
    
    // Don't manipulate main page tab visibility - let the main page handle it
    // The modal is independent of the main page tabs
});

// Modal shown event
$('#measurementModal').on('shown.bs.modal', function() {

});

// Modal close handler - reload measurements after modal is fully hidden
$('#measurementModal').on('hidden.bs.modal', function() {
    
    // Clean up any hash fragments from URL (remove hash completely to avoid conflicts)
    var currentHash = window.location.hash;
    if (currentHash) {
        if (history.replaceState) {
            // Remove hash entirely, keep only pathname and search params
            history.replaceState(null, null, window.location.pathname + window.location.search);
        } else {
            window.location.hash = '';
        }
    }
    
    // Only reload measurements if saved
    if (measurementSaved) {
        
        // Use the global function that respects current tab state
        setTimeout(function() {
            if (typeof window.reloadMeasurementsIfActive === 'function') {
                window.reloadMeasurementsIfActive();
            } else {
                // Fallback: check tab and reload if on measurements
                var urlParams = new URLSearchParams(window.location.search);
                var tabParam = urlParams.get('tab');
                var currentTab = tabParam || 'measurements';
                
                if (currentTab === 'measurements') {
                    loadMeasurements();
                }
            }
            measurementSaved = false;
            
            // Force ensure only the correct tab is visible
            ensureCorrectTabVisible();
        }, 150);
    } else {
        // Even if not saved, ensure correct tab is visible
        setTimeout(function() {
            ensureCorrectTabVisible();
        }, 100);
    }
});
}); // End document.ready

/**
 * Load measurements for appointment
 */
function loadMeasurements() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    if (!appointmentId) {
        $('#measurements-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Invalid appointment ID.</p></div>');
        return;
    }
    
    $('#measurements-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading measurements...</p></div>');
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/get_appointment_measurements/' + appointmentId,
        type: 'GET',
        data: csrf_token_name + '=' + csrf_hash,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayMeasurements(response.data);
            } else {
                $('#measurements-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found.</p></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading measurements:', error, xhr.responseText);
            $('#measurements-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading measurements.</p></div>');
        }
    });
}

/**
 * Display measurements
 */
function displayMeasurements(measurements) {
    var html = '';
    
    if (!measurements || measurements.length === 0) {
        html = '<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No measurements found.</p></div>';
    } else {
        html = '<div class="table-responsive"><table class="table table-hover" style="margin-bottom: 0;">';
        html += '<thead style="background-color: #2c3e50; color: white;">';
        html += '<tr>';
        html += '<th style="text-align: center; padding: 12px;">Category</th>';
        html += '<th style="text-align: center; padding: 12px;">Measurements</th>';
        html += '<th style="text-align: center; padding: 12px;">Last Updated</th>';
        html += '<th style="text-align: center; padding: 12px; width: 120px;">Actions</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        measurements.forEach(function(measurement, idx) {
            var rowClass = (idx % 2 === 0) ? 'style="background-color: #f8f9fa;"' : 'style="background-color: white;"';
            
            // Format updated info: "October 21st, 2025  |  9:45am  |  By: Tanner"
            var updatedInfo = '';
            if (measurement.formatted_updated_date && measurement.formatted_updated_date !== '-') {
                updatedInfo = measurement.formatted_updated_date;
                if (measurement.updated_by_name) {
                    updatedInfo += '  <span style="color: #6c757d;">|</span>  By: <strong>' + measurement.updated_by_name + '</strong>';
                }
            } else if (measurement.formatted_date && measurement.formatted_date !== '-') {
                updatedInfo = measurement.formatted_date;
                if (measurement.updated_by_name) {
                    updatedInfo += '  <span style="color: #6c757d;">|</span>  By: <strong>' + measurement.updated_by_name + '</strong>';
                }
            } else {
                updatedInfo = '-';
            }
            
            // Format measurements list: "Name | Value Unit"
            var measurementsList = '';
            if (measurement.items && measurement.items.length > 0) {
                measurement.items.forEach(function(item, itemIdx) {
                    if (itemIdx > 0) {
                        measurementsList += '<br>';
                    }
                    var itemName = item.name || 'Unnamed';
                    var itemValue = parseFloat(item.value).toFixed(2);
                    var itemUnit = item.unit || '';
                    measurementsList += '<span style="color: #495057;">' + itemName + '</span> <span style="color: #6c757d;">|</span> <strong>' + itemValue + ' ' + itemUnit + '</strong>';
                });
            } else {
                measurementsList = '<span style="color: #999;">No measurements</span>';
            }
            
            html += '<tr ' + rowClass + '>';
            html += '<td style="text-align: center; padding: 12px;"><strong>' + (measurement.tab_name || 'Untitled') + '</strong></td>';
            html += '<td style="text-align: left; padding: 12px;">' + measurementsList + '</td>';
            html += '<td style="text-align: center; padding: 12px;">' + updatedInfo + '</td>';
            html += '<td style="text-align: center; padding: 12px; vertical-align: middle;">';
            html += '<div style="display: flex; flex-direction: row; gap: 4px; align-items: center; justify-content: center;">';
            html += '<button class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="editMeasurement(' + measurement.id + ')" title="Edit Measurement"><i class="fa fa-edit"></i></button>';
            html += '<button class="btn btn-sm" style="background-color: #dc3545; border: 1px solid #dc3545; color: white; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" onclick="deleteMeasurement(' + measurement.id + ')" title="Delete Measurement"><i class="fa fa-trash"></i></button>';
            html += '</div>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
    }
    
    $('#measurements-container').html(html);
}

/**
 * Edit measurement
 */
function editMeasurement(measurementId) {
    openMeasurementModal(measurementId);
}

/**
 * Delete measurement
 */
function deleteMeasurement(measurementId) {
    if (!confirm('Are you sure you want to delete this measurement?')) {
        return;
    }
    
    $.ajax({
        url: admin_url + 'ella_contractors/measurements/delete/' + measurementId,
        type: 'POST',
        data: csrf_token_name + '=' + csrf_hash,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                
                // Immediately reload measurements
                loadMeasurements();
            } else {
                alert_float('danger', response.message);
            }
        },
        error: function() {
            alert_float('danger', 'Error deleting measurement');
        }
    });
}
</script>

<style>
.tab-title {
    display: inline-block;
    margin-right: 5px;
}

.tab-title:hover .fa-pencil {
    opacity: 1 !important;
    transition: opacity 0.2s ease;
}

#dynamic-tabs li a {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

#dynamic-tabs li a .btn-link {
    padding: 0 5px;
    margin-left: 5px;
}

.measurement-row {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.remove-row-btn {
    margin-left: 5px;
}

/* Tab input styling */
#tab-name-input {
    border: 2px solid #3498db;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
}

#tab-name-input:focus {
    border-color: #2980b9;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
}

/* Edit mode tab styling */
#dynamic-tabs li[data-edit-mode="true"] a {
    background-color: #f8f9fa;
    border-color: #3498db;
    color: #3498db;
}

/* Add tab button styling */
#addTabBtn {
    background-color: #3498db;
    border-color: #3498db;
    color: white;
    border-radius: 4px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
}

#addTabBtn:hover {
    background-color: #2980b9;
    border-color: #2980b9;
}

/* Empty state styling */
.text-center .fa-plus-circle {
    color: #bdc3c7 !important;
}

/* Save/Cancel button styling */
.btn-success.btn-sm {
    background-color: #27ae60;
    border-color: #27ae60;
}

.btn-default.btn-sm {
    background-color: #95a5a6;
    border-color: #95a5a6;
    color: white;
}

/* Action button styling - using standard Perfex CRM classes */
/* No custom styling needed - using btn-default btn-xs and btn-danger btn-xs */

</style>

