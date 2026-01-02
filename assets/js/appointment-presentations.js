/**
 * Centralized Appointment Presentations Handler
 * Used by: view.php, modal.php, and appointment.js (global)
 */

// Global array to store currently selected presentations (for create/edit modal)
var selectedPresentationsInModal = [];

// Global object to cache all presentation data (includes file URLs)
var allPresentationsCache = {};

/**
 * Get CSRF token helper (works with multiple sources)
 */
function getPresentationCSRFToken() {
    // Try global csrfData object (from manage_leads.php - most reliable)
    if (typeof csrfData !== 'undefined') {
        if (csrfData.token_name && csrfData.hash) {
            return { 
                name: csrfData.token_name, 
                hash: csrfData.hash 
            };
        } else if (csrfData.formatted) {
            for (var key in csrfData.formatted) {
                if (csrfData.formatted.hasOwnProperty(key)) {
                    return { 
                        name: key, 
                        hash: csrfData.formatted[key] 
                    };
                }
            }
        }
    }
    
    // Try global variables (from init_tail)
    if (typeof window.csrf_token_name !== 'undefined' && typeof window.csrf_hash !== 'undefined') {
        return { name: window.csrf_token_name, hash: window.csrf_hash };
    }
    
    // Try meta tags
    var metaTokenName = $('meta[name="csrf-token-name"]').attr('content');
    var metaTokenHash = $('meta[name="csrf-token-hash"]').attr('content');
    if (metaTokenName && metaTokenHash) {
        return { name: metaTokenName, hash: metaTokenHash };
    }
    
    // Fallback
    return { name: 'csrf_token_name', hash: '' };
}

/**
 * Load all available presentations for dropdown
 * @param {string} selectId - ID of the select element
 * @param {function} callback - Optional callback after loading (for backward compatibility)
 * @returns {Promise} Promise that resolves when presentations are loaded
 */
function loadPresentationsForDropdown(selectId, callback) {
    return new Promise(function(resolve, reject) {
        var csrfToken = getPresentationCSRFToken();
        
        $.ajax({
            url: admin_url + 'ella_contractors/presentations/get_all',
            type: 'GET',
            data: {
                [csrfToken.name]: csrfToken.hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data && response.data.length > 0) {
                    var options = '<option value="" disabled>-- Select Presentation --</option>';
                    
                    // Cache all presentation data for later use
                    allPresentationsCache = {};
                    
                    response.data.forEach(function(presentation) {
                        var presentationName = presentation.original_name || presentation.file_name;
                        options += '<option value="' + presentation.id + '">' + presentationName + '</option>';
                        
                        // Cache presentation data including file info
                        allPresentationsCache[presentation.id] = presentation;
                    });
                    
                    $('#' + selectId).html(options);
                    $('#' + selectId).selectpicker('refresh');
                } else {
                    $('#' + selectId).html('<option value="">No presentations available</option>');
                    $('#' + selectId).selectpicker('refresh');
                }
                if (callback) callback(response);
                resolve(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading presentations:', error);
                $('#' + selectId).html('<option value="">Error loading presentations</option>');
                $('#' + selectId).selectpicker('refresh');
                var errorResponse = { success: false, error: error };
                if (callback) callback(errorResponse);
                reject(new Error('Error loading presentations: ' + error));
            }
        });
    });
}

/**
 * Load attached presentations for an appointment
 * @param {number} appointmentId - Appointment ID
 * @param {string} containerId - Container ID where to render the list
 * @param {function} callback - Optional callback after loading (for backward compatibility)
 * @returns {Promise} Promise that resolves when presentations are loaded
 */
function loadAttachedPresentations(appointmentId, containerId, callback) {
    return new Promise(function(resolve, reject) {
        if (!appointmentId || appointmentId <= 0) {
            if (containerId) {
                $('#' + containerId).html('<p class="text-muted">No presentations attached</p>');
            }
            var response = { success: true, data: [] };
            if (callback) callback(response);
            resolve(response);
            return;
        }
        
        var csrfToken = getPresentationCSRFToken();
        
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/get_attached_presentations',
            type: 'GET',
            data: {
                appointment_id: appointmentId,
                [csrfToken.name]: csrfToken.hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    if (containerId) {
                        renderAttachedPresentations(response.data, containerId);
                    }
                } else {
                    if (containerId) {
                        $('#' + containerId).html('<p class="text-muted">No presentations attached</p>');
                    }
                }
                if (callback) callback(response);
                resolve(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading attached presentations:', error);
                if (containerId) {
                    $('#' + containerId).html('<p class="text-danger">Error loading presentations</p>');
                }
                var errorResponse = { success: false, error: error };
                if (callback) callback(errorResponse);
                reject(new Error('Error loading attached presentations: ' + error));
            }
        });
    });
}

/**
 * Render attached presentations list
 * @param {array} presentations - Array of presentation objects
 * @param {string} containerId - Container ID where to render
 */
function renderAttachedPresentations(presentations, containerId) {
    var html = '';
    
    // Get site URL - try multiple sources
    var siteUrl = '';
    if (typeof site_url !== 'undefined') {
        siteUrl = site_url;
    } else if (typeof window.site_url !== 'undefined') {
        siteUrl = window.site_url;
    } else {
        // Fallback: construct from current location
        siteUrl = window.location.protocol + '//' + window.location.host + '/';
    }
    
    if (presentations && presentations.length > 0) {
        html = '<ul class="list-unstyled" style="margin-top: 10px;">';
        presentations.forEach(function(presentation) {
            var publicUrl = presentation.public_url;
            if (!publicUrl && presentation.file_name) {
                publicUrl = siteUrl + 'uploads/ella_presentations/' + presentation.file_name;
            }
            
            html += '<li style="margin-bottom: 8px; padding: 8px; background-color: #f8f9fa; border-radius: 4px;">';
            html += '<i class="fa fa-file-powerpoint-o" style="color: #e67e22; margin-right: 8px;"></i> ';
            if (publicUrl) {
                html += '<a href="' + publicUrl + '" target="_blank" title="Public URL - Share with customer">';
                html += presentation.original_name || presentation.file_name || ('Presentation #' + presentation.id);
                html += '</a>';
            } else {
                html += presentation.original_name || presentation.file_name || ('Presentation #' + presentation.id);
            }
            html += ' <button class="btn btn-xs btn-danger pull-right" onclick="detachPresentationFromAppointment(' + presentation.id + ')" title="Remove" style="margin-left: 10px;">';
            html += '<i class="fa fa-times"></i></button>';
            html += '</li>';
        });
        html += '</ul>';
    } else {
        html = '<p class="text-muted" style="margin-top: 10px;">No presentations attached</p>';
    }
    
    $('#' + containerId).html(html);
}

/**
 * Attach presentation to appointment
 * @param {number} appointmentId - Appointment ID
 * @param {number} presentationId - Presentation ID
 * @param {function} callback - Optional callback after attaching
 */
function attachPresentationToAppointment(appointmentId, presentationId, callback) {
    if (!presentationId) {
        alert_float('warning', 'Please select a presentation');
        if (callback) callback({ success: false, message: 'No presentation selected' });
        return;
    }
    
    var csrfToken = getPresentationCSRFToken();
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/attach_presentation',
        type: 'POST',
        data: {
            appointment_id: appointmentId,
            presentation_id: presentationId,
            [csrfToken.name]: csrfToken.hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Presentation attached successfully');
                if (callback) callback(response);
            } else {
                alert_float('danger', response.message || 'Failed to attach presentation');
                if (callback) callback(response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error attaching presentation:', error);
            alert_float('danger', 'Error attaching presentation: ' + error);
            if (callback) callback({ success: false, error: error });
        }
    });
}

/**
 * Detach presentation from appointment
 * @param {number} presentationId - Presentation ID
 * @param {number} appointmentId - Appointment ID (optional, will try to get from context)
 * @param {string} containerId - Container ID to refresh after detaching (optional)
 * @param {function} callback - Optional callback after detaching
 */
function detachPresentationFromAppointment(presentationId, appointmentId, containerId, callback) {
    // Confirm dialog is now handled by caller (view.php) to avoid double confirmation
    // if (!confirm('Are you sure you want to remove this presentation?')) {
    //     if (callback) callback({ success: false, cancelled: true });
    //     return;
    // }
    
    // Try to get appointment ID from various sources if not provided
    if (!appointmentId || appointmentId <= 0) {
        // Try to get from hidden field
        appointmentId = $('#appointment_id').val();
        
        // Try to get from URL or context
        if (!appointmentId || appointmentId <= 0) {
            var urlMatch = window.location.href.match(/\/appointments\/view\/(\d+)/);
            if (urlMatch) {
                appointmentId = urlMatch[1];
            }
        }
    }
    
    if (!appointmentId || appointmentId <= 0) {
        alert_float('danger', 'Unable to determine appointment ID');
        if (callback) callback({ success: false, message: 'Appointment ID not found' });
        return;
    }
    
    var csrfToken = getPresentationCSRFToken();
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/detach_presentation',
        type: 'POST',
        data: {
            appointment_id: appointmentId,
            presentation_id: presentationId,
            [csrfToken.name]: csrfToken.hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Presentation removed successfully');
                
                // Empty container and reload
                var targetContainer = containerId || 'attached-presentations-container';
                $('#' + targetContainer).html('');
                loadAttachedPresentations(appointmentId, targetContainer);
                
                if (callback) callback(response);
            } else {
                alert_float('danger', response.message || 'Failed to remove presentation');
                if (callback) callback(response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error detaching presentation:', error);
            alert_float('danger', 'Error removing presentation: ' + error);
            if (callback) callback({ success: false, error: error });
        }
    });
}

/**
 * Handle presentation selection from dropdown (for create/edit modal)
 * Stores selected presentation IDs in a hidden field or array
 * @param {string} selectId - ID of the select element
 * @param {string} hiddenFieldId - ID of hidden field to store selected IDs (optional)
 * @returns {array} Array of selected presentation IDs
 */
function getSelectedPresentationIds(selectId, hiddenFieldId) {
    var selectedIds = $('#' + selectId).val();
    if (!selectedIds) {
        selectedIds = [];
    } else if (!Array.isArray(selectedIds)) {
        selectedIds = [selectedIds];
    }
    
    // Store in hidden field if provided
    if (hiddenFieldId) {
        $('#' + hiddenFieldId).val(JSON.stringify(selectedIds));
    }
    
    return selectedIds;
}

/**
 * Attach multiple presentations to appointment (for create/edit modal)
 * @param {number} appointmentId - Appointment ID
 * @param {array} presentationIds - Array of presentation IDs
 * @param {function} callback - Optional callback after all attachments complete
 */
function attachMultiplePresentationsToAppointment(appointmentId, presentationIds, callback) {
    if (!presentationIds || presentationIds.length === 0) {
        if (callback) callback({ success: true, attached: 0 });
        return;
    }
    
    var attachedCount = 0;
    var failedCount = 0;
    var totalCount = presentationIds.length;
    
    presentationIds.forEach(function(presentationId) {
        attachPresentationToAppointment(appointmentId, presentationId, function(response) {
            attachedCount++;
            if (response.success) {
                // Success - do nothing, already showed alert
            } else {
                failedCount++;
            }
            
            // All done
            if (attachedCount === totalCount) {
                if (callback) {
                    callback({
                        success: failedCount === 0,
                        attached: attachedCount - failedCount,
                        failed: failedCount,
                        total: totalCount
                    });
                }
            }
        });
    });
}

/**
 * Initialize presentation selection preview in modal
 * Shows selected presentations immediately with remove option
 * @param {string} selectId - ID of the select dropdown
 * @param {string} previewContainerId - ID of container to show preview
 */
function initPresentationSelectionPreview(selectId, previewContainerId) {
    // Clear any existing event handlers to prevent duplicates
    $('#' + selectId).off('change.presentationPreview');
    
    // Initialize the selected presentations array - ALWAYS start empty
    selectedPresentationsInModal = [];
    
    // Render initial state FIRST (shows "None" since array is empty)
    renderPresentationSelectionPreview(previewContainerId);
    
    // Handle dropdown change event
    $('#' + selectId).on('change.presentationPreview', function() {
        var selectedValues = $(this).val() || [];
        
        // Clear and rebuild the array
        selectedPresentationsInModal = [];
        
        // Get presentation details from cache (includes file info)
        selectedValues.forEach(function(presentationId) {
            var cachedPresentation = allPresentationsCache[presentationId];
            if (cachedPresentation) {
                selectedPresentationsInModal.push({
                    id: presentationId,
                    name: cachedPresentation.original_name || cachedPresentation.file_name,
                    file_name: cachedPresentation.file_name,
                    public_url: cachedPresentation.public_url || ''
                });
            } else {
                // Fallback if cache not available
                var optionText = $('#' + selectId + ' option[value="' + presentationId + '"]').text();
                selectedPresentationsInModal.push({
                    id: presentationId,
                    name: optionText,
                    file_name: '',
                    public_url: ''
                });
            }
        });
        
        // Render the preview
        renderPresentationSelectionPreview(previewContainerId);
    });
}

/**
 * Render presentation selection preview (before saving)
 * @param {string} containerId - Container ID to render the preview
 */
function renderPresentationSelectionPreview(containerId) {
    // Ensure array exists and is properly checked
    if (!selectedPresentationsInModal) {
        selectedPresentationsInModal = [];
    }
    
    var html = '';
    
    // Get site URL
    var siteUrl = '';
    if (typeof site_url !== 'undefined') {
        siteUrl = site_url;
    } else if (typeof window.site_url !== 'undefined') {
        siteUrl = window.site_url;
    } else {
        siteUrl = window.location.protocol + '//' + window.location.host + '/';
    }
    
    // Check if array has items - use strict length check
    if (Array.isArray(selectedPresentationsInModal) && selectedPresentationsInModal.length > 0) {
        html = '<ul class="list-unstyled" style="margin-top: 10px;">';
        selectedPresentationsInModal.forEach(function(presentation) {
            var publicUrl = presentation.public_url;
            if (!publicUrl && presentation.file_name) {
                publicUrl = siteUrl + 'uploads/ella_presentations/' + presentation.file_name;
            }
            
            html += '<li style="margin-bottom: 8px; padding: 8px; background-color: #f8f9fa; border-radius: 4px; display: flex; align-items: center; justify-content: space-between;">';
            html += '<div>';
            html += '<i class="fa fa-file-powerpoint-o" style="color: #e67e22; margin-right: 8px;"></i> ';
            if (publicUrl) {
                html += '<a href="' + publicUrl + '" target="_blank" style="color: #007bff; text-decoration: none;" title="Click to view in new tab">' + (presentation.name || ('Presentation #' + presentation.id)) + '</a>';
            } else {
                html += presentation.name || ('Presentation #' + presentation.id);
            }
            html += '</div>';
            html += '<button class="btn btn-xs btn-danger" onclick="removePresentationFromPreview(' + presentation.id + ')" title="Remove" type="button">';
            html += '<i class="fa fa-times"></i>';
            html += '</button>';
            html += '</li>';
        });
        html += '</ul>';
    } else {
        // ALWAYS show "None" when array is empty or undefined
        html = '<p style="text-align: center; color: #778485; margin: 10px 0;">None</p>';
    }
    
    $('#' + containerId).html(html);
}

/**
 * Remove presentation from preview (before saving)
 * @param {number} presentationId - Presentation ID to remove
 */
function removePresentationFromPreview(presentationId) {
    // Remove from array
    selectedPresentationsInModal = selectedPresentationsInModal.filter(function(p) {
        return p.id != presentationId;
    });
    
    // Update dropdown selection
    var currentValues = $('#presentation_select').val() || [];
    var newValues = currentValues.filter(function(id) {
        return id != presentationId.toString();
    });
    $('#presentation_select').selectpicker('val', newValues);
    
    // Re-render preview
    renderPresentationSelectionPreview('modal-presentation-list');
}

/**
 * Clear presentation selection preview
 */
function clearPresentationSelectionPreview() {
    selectedPresentationsInModal = [];
    $('#modal-presentation-list').html('<p style="text-align: center; color: #778485; margin: 10px 0;">None</p>');
}

