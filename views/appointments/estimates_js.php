<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Load estimates for appointment
function loadEstimates() {
    var appointmentId = <?php echo isset($appointment) ? $appointment->id : 'appointmentId'; ?>;
    
    // Show loading indicator
    $('#estimates-list-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading estimates...</p></div>');
    
    $.ajax({
        url: admin_url + 'ella_contractors/estimates/get_appointment_estimates/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayEstimates(response.data);
            } else {
                $('#estimates-list-container').html(
                    '<div class="estimate-empty">' +
                    '<i class="fa fa-file-text-o"></i>' +
                    '<p>No estimates found for this appointment.</p>' +
                    '<p class="text-muted">Click "New Estimate" to create one.</p>' +
                    '</div>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading estimates:', error, xhr.responseText);
            $('#estimates-list-container').html(
                '<div class="text-center text-danger">' +
                '<i class="fa fa-exclamation-triangle fa-2x"></i>' +
                '<p>Error loading estimates. Please try again.</p>' +
                '</div>'
            );
        }
    });
}

// Auto-load estimates when page loads if estimates tab is active
$(document).ready(function() {
    // Check if we should auto-load estimates (returning from creating one)
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    
    if (tabParam === 'estimates') {
        // Small delay to ensure tab is visible
        setTimeout(function() {
            if (typeof window.tabsLoaded !== 'undefined') {
                window.tabsLoaded.estimates = false; // Force reload
            }
            loadEstimates();
        }, 300);
    }
});

function displayEstimates(estimates) {
    if (!estimates || estimates.length === 0) {
        $('#estimates-list-container').html(
            '<div class="estimate-empty">' +
            '<i class="fa fa-file-text-o"></i>' +
            '<p>No estimates found for this appointment.</p>' +
            '<p class="text-muted">Click "New Estimate" to create one.</p>' +
            '</div>'
        );
        return;
    }

    var html = '';
    
    estimates.forEach(function(estimate) {
        html += '<div class="estimate-card">';
        
        // Header
        html += '<div class="estimate-header">';
        html += '<div>';
        html += '<h4 class="estimate-title">' + htmlEscape(estimate.subject) + '</h4>';
        html += '<span class="estimate-id">#' + estimate.id + '</span>';
        html += '</div>';
        html += '<div>' + estimate.status_formatted + '</div>';
        html += '</div>';
        
        // Body
        html += '<div class="estimate-body">';
        
        html += '<div class="estimate-info-row">';
        html += '<span class="estimate-info-label">To:</span>';
        html += '<span class="estimate-info-value">' + htmlEscape(estimate.proposal_to) + '</span>';
        html += '</div>';
        
        html += '<div class="estimate-info-row">';
        html += '<span class="estimate-info-label">Date:</span>';
        html += '<span class="estimate-info-value">' + formatDate(estimate.date) + '</span>';
        html += '</div>';
        
        html += '<div class="estimate-info-row">';
        html += '<span class="estimate-info-label">Valid Until:</span>';
        html += '<span class="estimate-info-value">' + formatDate(estimate.open_till) + '</span>';
        html += '</div>';
        
        html += '<div class="estimate-info-row">';
        html += '<span class="estimate-info-label">Total:</span>';
        html += '<span class="estimate-info-value estimate-total">$' + formatMoney(estimate.total) + '</span>';
        html += '</div>';
        
        html += '</div>';
        
        // Footer
        html += '<div class="estimate-footer">';
        html += '<div class="estimate-meta">';
        html += 'Created by ' + htmlEscape(estimate.created_by);
        html += '<br>on ' + formatDateTime(estimate.date_created);
        html += '</div>';
        html += '<div class="estimate-actions">';
        html += '<a href="' + estimate.view_url + '" class="btn btn-sm btn-default" title="View Proposal" target="_blank">';
        html += '<i class="fa fa-eye"></i> View';
        html += '</a>';
        
        <?php if (has_permission('proposals', '', 'edit')): ?>
        html += '<a href="' + estimate.edit_url + '" class="btn btn-sm btn-info" title="Edit Proposal" target="_blank">';
        html += '<i class="fa fa-edit"></i> Edit';
        html += '</a>';
        <?php endif; ?>
        
        <?php if (has_permission('proposals', '', 'delete')): ?>
        html += '<button onclick="unlinkEstimate(' + estimate.id + ')" class="btn btn-sm btn-warning" title="Unlink from Appointment">';
        html += '<i class="fa fa-unlink"></i>';
        html += '</button>';
        <?php endif; ?>
        
        html += '</div>';
        html += '</div>';
        
        html += '</div>'; // Close estimate-card
    });
    
    $('#estimates-list-container').html(html);
}

// Unlink estimate from appointment
function unlinkEstimate(proposalId) {
    if (!confirm('Are you sure you want to unlink this estimate from the appointment? The proposal will not be deleted, just the link to this appointment will be removed.')) {
        return;
    }
    
    $.ajax({
        url: admin_url + 'ella_contractors/estimates/unlink_from_appointment/' + proposalId,
        type: 'POST',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message || 'Estimate unlinked successfully');
                loadEstimates(); // Reload the list
            } else {
                alert_float('danger', response.message || 'Failed to unlink estimate');
            }
        },
        error: function() {
            alert_float('danger', 'Error unlinking estimate');
        }
    });
}

// Helper functions
function htmlEscape(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function formatMoney(amount) {
    if (!amount) return '0.00';
    return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    var date = new Date(dateStr);
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return 'N/A';
    var date = new Date(dateTimeStr);
    var options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('en-US', options);
}
</script>

