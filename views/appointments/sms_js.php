<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// SMS functionality for appointments - matching leads interface exactly
var admin_url = '<?php echo admin_url(); ?>';
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Track last focused element for template insertion
var lastFocused = null;

$(document).ready(function() {
    // Track focus on textareas and inputs
    $('textarea, input[type="text"]').on('focus', function() {
        lastFocused = $(this);
    });
    
    // Initialize emoji picker if available
    if (typeof $.fn.emojiarea !== 'undefined') {
        $('textarea[data-emojiable="true"]').emojiarea();
    }
    
    // Initialize drag and drop functionality when modal is shown
    $('#smsModal').on('shown.bs.modal', function() {
        initDropzone();
    });
    
    // Template button click handler - exactly like leads
    $(document).on("click", ".typos ul li a", function(e) {
        e.preventDefault();
        var typo_txt = $(this).attr('data-quick-typo-text');
        
        // Find the textarea
        var smsTextarea = $('#smsModal textarea[name="sms_body_textarea"]');
        if (smsTextarea.length === 0) {
            smsTextarea = $('#smsModal textarea[data-emojiable="true"]');
        }
        if (smsTextarea.length === 0) {
            smsTextarea = $('#smsModal textarea');
        }
        
        if (smsTextarea.length > 0) {
            var currentValue = smsTextarea.val();
            var newValue = currentValue + (currentValue ? " " : "") + typo_txt;
            
            // Insert text using multiple methods for compatibility
            smsTextarea[0].value = newValue;
            smsTextarea.val(newValue);
            smsTextarea.focus();
            
            // Use setRangeText if available
            if (smsTextarea[0].setRangeText) {
                smsTextarea[0].setRangeText(typo_txt, smsTextarea[0].selectionStart, smsTextarea[0].selectionEnd, 'end');
            }
            
            // Trigger events
            smsTextarea.trigger('input');
            smsTextarea.trigger('change');
            
            // Set cursor to end
            var length = smsTextarea.val().length;
            smsTextarea[0].setSelectionRange(length, length);
        }
    });
    
    // SMS send button handler
    $(document).on("click", "#appointment_send_sms", function(e) {
        e.preventDefault();
        sendSMS();
    });
});

function openSMSModal(leadId, contactNumber) {
    $('#sms_lead_id').val(leadId);
    $('#sms_contact_number').val(contactNumber);
    $('#smsModalLabel').text('Send SMS to ' + contactNumber);
    $('textarea[name="sms_body_textarea"]').val('');
    $('#media_image').val('');
    $('#media_url').val('');
    $('#sms_activity_feed').empty();
    
    loadSMSHistory(leadId, contactNumber);
    $('#smsModal').modal('show');
}

function loadSMSHistory(leadId, contactNumber) {
    $.post(admin_url + 'ella_contractors/appointments/get_sms_logs', {
        lead_id: leadId,
        contact_number: contactNumber,
        [csrf_token_name]: csrf_hash
    }).done(function(response) {
        var data = JSON.parse(response);
        if (data.success && data.data.length > 0) {
            displaySMSHistory(data.data);
        } else {
            $('#sms_activity_feed').html('<p>No SMS history found.</p>');
        }
    }).fail(function(xhr, status, error) {
        alert_float('danger', 'Error loading SMS history: ' + error);
    });
}

function displaySMSHistory(smsLogs) {
    var html = '';
    $.each(smsLogs, function(index, log) {
        var senderName = log.firstname + ' ' + log.lastname;
        var messageBody = log.msg_body;
        var timeAgo = log.time_ago;
        
        html += '<div class="feed-item">';
        html += '<div class="date">';
        html += '<span class="text-has-action" data-toggle="tooltip" data-title="' + log.date_created + '">' + timeAgo + '</span>';
        html += '</div>';
        html += '<div class="text">';
        if (log.sender_type == 'twilio') {
            html += '<a href="#"><img class="staff-profile-xs-image pull-left mright5" src="' + admin_url + 'assets/images/user-placeholder.jpg" /></a>';
            html += '<b>' + log.lead_name + '</b> - ' + messageBody;
        } else {
            html += '<a href="' + admin_url + 'admin/profile/' + log.sender_id + '">' + log.profile_img + '</a>';
            html += '<b>' + senderName + '</b> - ' + messageBody;
        }
        html += '</div>';
        html += '</div>';
    });
    
    $('#sms_activity_feed').html(html);
}

function sendSMS() {
    var leadId = $('#sms_lead_id').val();
    var contactNumber = $('#sms_contact_number').val();
    var senderId = $('#sms_sender_id').val();
    var smsBody = $('textarea[name="sms_body_textarea"]').val();
    var mediaUrl = $('#media_url').val();
    
    // Check if contact number is empty or null
    if (contactNumber == null || contactNumber == '') {
        alert_float('danger', 'Phone Number can\'t be empty!');
        $('#appointment_send_sms').prop('disabled', false);
        return false;
    }
    
    // Check if SMS body is empty or image isn't attached
    if ((smsBody == null || smsBody == '') && mediaUrl == '') {
        alert_float('danger', 'Message body can\'t be empty!');
        $('#appointment_send_sms').prop('disabled', false);
        return false;
    }
    
    // Prepare data exactly like leads
    var data = {
        lead_id: leadId,
        contact_number: contactNumber,
        sender_id: senderId,
        sms_body: smsBody,
        media_url: mediaUrl,
        vc_fromdate: $("#vc_fromdate").val(),
        vc_todate: $("#vc_todate").val(),
        vc_summary: $("#vc_summary").val(),
        vc_description: $("#vc_description").val(),
        vc_location: $("#vc_location").val()
    };
    
    // Show spinner
    $('#spinner').show();
    $('#appointment_send_sms').prop('disabled', true);
    
    // POST request to send the SMS - exactly like leads
    $.post(admin_url + 'ella_contractors/appointments/send_sms', data).done(function(response) {
        // Log SMS
        response = JSON.parse(response);
        if (response.success == true) {
            var message = response.message;
            if (Array.isArray(message)) {
                for (let index = 0; index < message.length; ++index) {
                    var activity = message[index];
                    var feed_item = '<div class="feed-item"><div class="date">';
                    feed_item += '<span class="text-has-action" data-toggle="tooltip" data-title="' + activity.date_created + '" data-original-title="" title="">' + activity.time_ago + '</span></div>';
                    feed_item += '<div class="text">';
                    if (activity.sender_type == 'twilio') {
                        feed_item += '<a href="#"><img class="staff-profile-xs-image pull-left mright5" src="' + admin_url + 'assets/images/user-placeholder.jpg" /></a>';
                        feed_item += '<b>' + activity.lead_name + '</b> - ' + activity.msg_body;
                    } else {
                        feed_item += '<a href="' + admin_url + 'admin/profile/' + activity.sender_id + '">' + activity.profile_img + '</a>';
                        feed_item += '<b>' + activity.firstname + ' ' + activity.lastname + '</b> - ' + activity.msg_body;
                    }
                    feed_item += '</div></div>';
                    $('#sms_activity_feed').prepend(feed_item);
                }
            }
            
            // Clear the SMS body textarea and show success message
            alert_float('success', 'SMS sent successfully!');
            $("textarea[name='sms_body_textarea']").val('');
            $(".lead_sms_description .emoji-wysiwyg-editor").text('');
            $('.imagesresponse').val('');
            $('#media_image').val('');
            
            // Clear vCalendar fields
            $("#vc_fromdate").val('');
            $("#vc_todate").val('');
            $("#vc_summary").val('');
            $("#vc_description").val('');
            $("#vc_location").val('');
            
            removeFileFromDropZone();
            $('#appointment_send_sms').prop('disabled', false);
            
        } else if (response.success == false) {
            alert_float('danger', response.message);
            $('#appointment_send_sms').prop('disabled', false);
        }
    }).fail(function(data) {
        alert_float('danger', data.responseText);
        $('#appointment_send_sms').prop('disabled', false);
    }).always(function() {
        $('#spinner').hide();
    });
}

// Global variable for tracking uploaded files
var allFiles = [];

// Dropzone initialization for file uploads - exactly like leads
function initDropzone() {
    // Initialize dropzone functionality exactly like leads
    const dropZoneElement = document.querySelector('.drop-zone');
    const inputElement = document.querySelector('.drop-zone__input');
    const promptElement = document.querySelector('.drop-zone__prompt');

    if (!dropZoneElement || !inputElement) {
        return;
    }

    // Click to upload
    dropZoneElement.addEventListener('click', () => inputElement.click());

    // Handle file selection
    inputElement.addEventListener('change', (e) => {
        if (e.target.files.length) {
            updateThumbnail(dropZoneElement, e.target.files[0]);
            allFiles = Array.from(e.target.files);
        }
    });

    // Drag and drop functionality
    dropZoneElement.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZoneElement.classList.add('drop-zone--over');
    });

    ['dragleave', 'dragend'].forEach(type => {
        dropZoneElement.addEventListener(type, (e) => {
            dropZoneElement.classList.remove('drop-zone--over');
        });
    });

    dropZoneElement.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZoneElement.classList.remove('drop-zone--over');
        
        if (e.dataTransfer.files.length) {
            inputElement.files = e.dataTransfer.files;
            updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
            allFiles = Array.from(e.dataTransfer.files);
        }
    });

    // Auto-submit form when file is selected (like leads)
    $("#media_image").on("change", function() {
        $("#imageUploadForm").submit();
        $('.campaign_type').val('sms');
    });

    // Handle form submission for file upload
    $('#imageUploadForm').on('submit', function(e) {
        e.preventDefault();
        $('#appointment_send_sms').prop('disabled', true);
        $('#spinner').show();
        
        var formData = new FormData(this);
        var send_url = admin_url + 'upload_image/upload';
        
        $.ajax({
            type: 'POST',
            url: send_url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                var imageurl = $('.imagesresponse').val();
                if (imageurl != '') {
                    // Already has image
                } else {
                    $('.imagesresponse').val(data);
                }
                
                $('#spinner').hide();
                $('#appointment_send_sms').prop('disabled', false);
            },
            error: function(data) {
                $('#spinner').hide();
                $('#appointment_send_sms').prop('disabled', false);
                alert_float('danger', 'Error uploading image');
            }
        });
    });
}

// Update thumbnail function - exactly like leads
function updateThumbnail(dropZoneElement, file) {
    let thumbnailElement = dropZoneElement.querySelector('.drop-zone__thumb');
    
    // First time - remove the prompt
    if (dropZoneElement.querySelector('.drop-zone__prompt')) {
        dropZoneElement.querySelector('.drop-zone__prompt').remove();
    }
    
    // First time - there is no thumbnail element, so let's create it
    if (!thumbnailElement) {
        thumbnailElement = document.createElement('div');
        thumbnailElement.classList.add('drop-zone__thumb');
        dropZoneElement.appendChild(thumbnailElement);
    }
    
    thumbnailElement.dataset.label = file.name;
    
    // Show thumbnail for image files
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
        };
    } else {
        thumbnailElement.style.backgroundImage = null;
    }
    
    // Show prompt if no more thumbnails
    if (!dropZoneElement.querySelector(".drop-zone__thumb")) {
        const prompt = dropZoneElement.querySelector(".drop-zone__prompt");
        if (prompt) {
            prompt.style.display = 'block';
        }
    }
}

// Remove file from dropzone function - exactly like leads
function removeFileFromDropZone() {
    if (allFiles.length > 0) {
        const dropZoneElement = document.querySelector('.drop-zone');
        const inputElement = document.querySelector('.drop-zone__input');

        // Remove file from the allFiles array
        allFiles = [];

        // Update input files
        updateInputFiles(inputElement);

        // Remove the thumbnail
        const thumbnail = dropZoneElement.querySelector(".drop-zone__thumb");
        if (thumbnail) {
            thumbnail.remove();
        }

        // Show the upload prompt again
        const prompt = dropZoneElement.querySelector(".drop-zone__prompt");
        if (prompt) {
            prompt.style.display = 'block';
        }
    }
}

// Update input files function
function updateInputFiles(inputElement) {
    const dataTransfer = new DataTransfer();
    allFiles.forEach(file => dataTransfer.items.add(file));
    inputElement.files = dataTransfer.files;
}

// vCalendar functionality
$(document).on('click', '#smsVCalanderopenModalButton', function() {
    // Implement vCalendar modal if needed
    alert_float('info', 'vCalendar functionality coming soon');
});
</script>