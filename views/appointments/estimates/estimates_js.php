<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Load estimates for appointment
function loadEstimates() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    if (!appointmentId) {
        $('#estimates-list-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Invalid appointment ID.</p></div>');
        return;
    }
    
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
    var html = '';
    
    if (!estimates || estimates.length === 0) {
        html = '<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No estimates found.</p></div>';
    } else {
        html = '<div class="table-responsive"><table class="table table-hover" style="margin-bottom: 0;">';
        html += '<thead style="background-color: #2c3e50; color: white;">';
        html += '<tr>';
        html += '<th style="text-align: center; padding: 12px;">Subject</th>';
        html += '<th style="text-align: center; padding: 12px;">To</th>';
        html += '<th style="text-align: center; padding: 12px;">Total</th>';
        html += '<th style="text-align: center; padding: 12px;">Status</th>';
        html += '<th style="text-align: center; padding: 12px;">Created</th>';
        html += '<th style="text-align: center; padding: 12px; width: 120px;">Actions</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        estimates.forEach(function(estimate, idx) {
            var rowClass = (idx % 2 === 0) ? 'style="background-color: #f8f9fa;"' : 'style="background-color: white;"';
            
            html += '<tr ' + rowClass + '>';
            html += '<td style="text-align: center; padding: 12px;"><strong>' + htmlEscape(estimate.subject) + '</strong></td>';
            html += '<td style="text-align: center; padding: 12px;">' + htmlEscape(estimate.proposal_to) + '</td>';
            html += '<td style="text-align: center; padding: 12px;"><strong style="color: #2ecc71;">$' + formatMoney(estimate.total) + '</strong></td>';
            html += '<td style="text-align: center; padding: 12px;">' + estimate.status_formatted + '</td>';
            html += '<td style="text-align: center; padding: 12px;">' + formatDate(estimate.date) + '</td>';
            html += '<td style="text-align: center; padding: 12px; vertical-align: middle;">';
            html += '<div style="display: flex; flex-direction: row; gap: 4px; align-items: center; justify-content: center;">';
            html += '<a href="' + estimate.view_url + '" class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" title="View Proposal"><i class="fa fa-eye"></i></a>';
            
            <?php if (has_permission('proposals', '', 'edit')): ?>
            html += '<a href="' + estimate.edit_url + '" class="btn btn-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; color: #495057; padding: 4px 8px; border-radius: 4px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" title="Edit Proposal"><i class="fa fa-edit"></i></a>';
            <?php endif; ?>
            
            html += '</div>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';
    }
    
    $('#estimates-list-container').html(html);
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



