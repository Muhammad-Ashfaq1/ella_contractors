<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// SMS functionality for appointments
var admin_url = '<?php echo admin_url(); ?>';
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Initialize SMS functionality
function initSMS() {
    // Handle send SMS button
    $('#lead_send_sms').on('click', function() {
        sendSMS();
    });
    
    // Handle image upload
    initImageUpload();
    
    // Handle vCalendar button
    $('#smsVCalanderopenModalButton').on('click', function() {
        // TODO: Implement vCalendar functionality if needed
        alert_float('info', 'vCalendar functionality not implemented yet');
    });
}

// Send SMS function
function sendSMS() {
    var lead_id = $('#sms_lead_id').val();
    var contact_number = $('#sms_contact_number').val();
    var sender_id = $('#sms_sender_id').val();
    var sms_body = $('#sms_body_textarea').val();
    var media_url = $('#media_url').val();
    
    if (!lead_id) {
        alert_float('danger', 'Lead ID is required');
        return;
    }
    
    if (!contact_number) {
        alert_float('danger', 'Contact number is required');
        return;
    }
    
    if (!sms_body.trim()) {
        alert_float('danger', 'SMS body is required');
        return;
    }
    
    // Show spinner
    $('#spinner').show();
    $('#lead_send_sms').prop('disabled', true);
    
    // Prepare data
    var formData = {
        lead_id: lead_id,
        contact_number: contact_number,
        sender_id: sender_id,
        sms_body: sms_body,
        media_url: media_url,
        [csrf_token_name]: csrf_hash
    };
    
    // Send AJAX request
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/send_sms',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            $('#spinner').hide();
            $('#lead_send_sms').prop('disabled', false);
            
            if (response.success) {
                alert_float('success', 'SMS sent successfully');
                // Clear form
                $('#sms_body_textarea').val('');
                $('#media_url').val('');
                // Reload SMS history
                loadSMSHistory();
            } else {
                alert_float('danger', response.message || 'Failed to send SMS');
            }
        },
        error: function(xhr, status, error) {
            $('#spinner').hide();
            $('#lead_send_sms').prop('disabled', false);
            alert_float('danger', 'Error sending SMS: ' + error);
        }
    });
}

// Load SMS history
function loadSMSHistory() {
    var lead_id = $('#sms_lead_id').val();
    var contact_number = $('#sms_contact_number').val();
    
    if (!lead_id && !contact_number) {
        return;
    }
    
    var url = admin_url + 'ella_contractors/appointments/get_sms_logs';
    var data = {
        [csrf_token_name]: csrf_hash
    };
    
    if (lead_id) {
        data.lead_id = lead_id;
    } else if (contact_number) {
        data.contact_number = contact_number;
    }
    
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displaySMSHistory(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading SMS history:', error);
        }
    });
}

// Display SMS history
function displaySMSHistory(smsLogs) {
    var html = '';
    
    if (smsLogs && smsLogs.length > 0) {
        $.each(smsLogs, function(index, log) {
            html += '<div class="feed-item">';
            html += '<div class="date">';
            html += '<span class="text-has-action" data-toggle="tooltip" data-title="' + log.date_created + '">';
            html += log.time_ago;
            html += '</span>';
            html += '</div>';
            html += '<div class="text">';
            
            if (log.sender_type === 'staff') {
                html += '<a href="' + admin_url + 'profile/' + log.staffid + '">';
                html += log.profile_img;
                html += '</a>';
                html += log.firstname + ' ' + log.lastname + ' - ';
            } else {
                html += '<strong>Customer:</strong> ';
            }
            
            html += log.msg_body;
            html += '</div>';
            html += '</div>';
        });
    } else {
        html = '<div class="feed-item"><div class="text">No SMS history found</div></div>';
    }
    
    $('#sms_activity_feed').html(html);
}

// Initialize image upload
function initImageUpload() {
    // Handle file input change
    $('#media_image').on('change', function() {
        var file = this.files[0];
        if (file) {
            uploadImage(file);
        }
    });
    
    // Handle drop zone
    $('.drop-zone').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    $('.drop-zone').on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });
    
    $('.drop-zone').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            uploadImage(files[0]);
        }
    });
    
    // Handle click to upload
    $('.drop-zone').on('click', function() {
        $('#media_image').click();
    });
}

// Upload image function
function uploadImage(file) {
    var formData = new FormData();
    formData.append('media_image', file);
    formData.append('campaign_type', 'sms');
    formData.append(csrf_token_name, csrf_hash);
    
    $.ajax({
        url: admin_url + 'upload_image/upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#media_url').val(response.url);
                alert_float('success', 'Image uploaded successfully');
            } else {
                alert_float('danger', response.message || 'Failed to upload image');
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error uploading image: ' + error);
        }
    });
}

// Open SMS modal with lead data
function openSMSModal(leadId, contactNumber) {
    $('#sms_lead_id').val(leadId);
    $('#sms_contact_number').val(contactNumber);
    
    // Load SMS history
    loadSMSHistory();
    
    // Show modal
    $('#smsModal').modal('show');
}

// Initialize when document is ready
$(document).ready(function() {
    initSMS();
});
</script>
