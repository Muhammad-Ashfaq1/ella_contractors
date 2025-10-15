/**
 * Centralized JavaScript for Appointment Attendees Functionality
 * Used by: appointments index, view pages, and leads page
 * 
 * This is the SINGLE SOURCE OF TRUTH for loading attendees across the application
 */

// Load staff members for attendees dropdown
function loadStaffForAttendees() {
    // Get CSRF token dynamically to handle all contexts
    var csrfTokenName = '';
    var csrfTokenHash = '';
    
    // Try to get CSRF token from multiple sources
    if (typeof window.csrf_token_name !== 'undefined' && typeof window.csrf_hash !== 'undefined') {
        csrfTokenName = window.csrf_token_name;
        csrfTokenHash = window.csrf_hash;
    } else if (typeof csrfData !== 'undefined' && csrfData.token_name && csrfData.hash) {
        csrfTokenName = csrfData.token_name;
        csrfTokenHash = csrfData.hash;
    } else {
        // Try to get from page variables (appointments index/view pages)
        csrfTokenName = csrf_token_name || 'csrf_token_name';
        csrfTokenHash = csrf_hash || '';
    }
    
    var ajaxData = {};
    if (csrfTokenName && csrfTokenHash) {
        ajaxData[csrfTokenName] = csrfTokenHash;
    }
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_staff',
        type: 'GET',
        data: ajaxData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                var options = '';
                response.data.forEach(function(staff) {
                    options += `<option value="${staff.staffid}">${staff.firstname} ${staff.lastname}</option>`;
                });
                $('#attendees').html(options);
                $('#attendees').selectpicker('refresh');
                console.log('✓ Staff members loaded successfully:', response.data.length, 'members');
            } else {
                $('#attendees').html('<option value="">Error loading staff members</option>');
                $('#attendees').selectpicker('refresh');
                console.warn('Invalid response format from get_staff endpoint');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading staff members:', error);
            console.error('Status:', xhr.status, 'Response:', xhr.responseText);
            $('#attendees').html('<option value="">Error loading staff members</option>');
            $('#attendees').selectpicker('refresh');
        }
    });
}

/**
 * Set attendees in the dropdown from data
 * @param {Array} attendees - Array of attendee objects with staffid property
 */
function setAppointmentAttendees(attendees) {
    var attendeeIds = [];
    if (attendees && attendees.length > 0) {
        $.each(attendees, function(index, attendee) {
            // Handle both staffid and staff_id properties for compatibility
            var staffId = attendee.staffid || attendee.staff_id;
            if (staffId) {
                attendeeIds.push(staffId);
            }
        });
    }
    $('#attendees').val(attendeeIds);
    $('#attendees').selectpicker('refresh');
}

/**
 * Reload staff and set attendees for appointment modal
 * @param {Array} attendees - Optional array of attendee objects to pre-select
 * @param {number} delay - Optional delay in milliseconds before setting attendees
 */
function reloadStaffAndSetAttendees(attendees, delay = 500) {
    // Reload staff members first
    loadStaffForAttendees();
    
    // Set attendees after a delay to ensure dropdown is populated
    if (attendees) {
        setTimeout(function() {
            setAppointmentAttendees(attendees);
        }, delay);
    }
}

/**
 * Initialize attendees functionality for appointment modals
 * Call this when the modal is opened or page is loaded
 */
function initAppointmentAttendees() {
    // Load staff members on initialization
    loadStaffForAttendees();
    
    // Ensure attendees dropdown is refreshed when modal is shown
    $('#appointmentModal').on('shown.bs.modal', function() {
        // Refresh selectpicker to ensure proper display
        $('#attendees').selectpicker('refresh');
    });
}
