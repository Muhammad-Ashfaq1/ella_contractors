<?php
/**
 * Attachments JavaScript functionality for appointment view
 * This file contains all JavaScript functions related to appointment attachments
 */
?>

<script>
$(document).ready(function() {
    // Load attachments when attachments tab is clicked
    $('a[href="#attachments-tab"]').on('click', function() {
        loadAttachments();
    });
});

// Global function for loading attachments
window.loadAttachments = function() {
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_attachments/' + <?php echo $appointment['id']; ?>,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayAttachments(response.attachments);
            } else {
                $('#attachments-container').html('<div class="text-center text-muted"><p>No attachments found</p></div>');
            }
        },
        error: function(xhr, status, error) {
            $('#attachments-container').html('<div class="text-center text-danger"><p>Error loading attachments</p></div>');
        }
    });
};

// Global function for displaying attachments
window.displayAttachments = function(attachments) {
    var html = '';
    
    if (attachments.length === 0) {
        html = '<div class="text-center text-muted"><p>No attachments found</p></div>';
    } else {
        html = '<div class="row">';
        
        attachments.forEach(function(attachment) {
            var fileIcon = getFileIcon(attachment.file_type);
            var fileSize = formatFileSize(attachment.file_size);
            var uploadDate = new Date(attachment.date_uploaded).toLocaleDateString();
            
            html += '<div class="col-md-4 col-sm-6 col-xs-12 mbot15">';
            html += '<div class="panel panel-default">';
            html += '<div class="panel-body text-center">';
            html += '<i class="fa ' + fileIcon + ' fa-3x text-muted mbot10"></i>';
            html += '<h5 class="text-ellipsis" title="' + attachment.original_name + '">' + attachment.original_name + '</h5>';
            html += '<p class="text-muted small">' + fileSize + ' • ' + uploadDate + '</p>';
            html += '<div class="btn-group btn-group-sm">';
            html += '<a href="' + admin_url + 'ella_contractors/appointments/download_attachment/' + attachment.id + '" class="btn btn-info btn-sm" target="_blank">';
            html += '<i class="fa fa-download"></i> Download</a>';
            html += '<button class="btn btn-danger btn-sm" onclick="deleteAttachment(' + attachment.id + ')">';
            html += '<i class="fa fa-trash"></i> Delete</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
    }
    
    $('#attachments-container').html(html);
};

// Global function for getting file icon based on file type
window.getFileIcon = function(fileType) {
    var iconMap = {
        'application/pdf': 'fa-file-pdf-o',
        'application/msword': 'fa-file-word-o',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'fa-file-word-o',
        'application/vnd.ms-excel': 'fa-file-excel-o',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'fa-file-excel-o',
        'application/vnd.ms-powerpoint': 'fa-file-powerpoint-o',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'fa-file-powerpoint-o',
        'image/jpeg': 'fa-file-image-o',
        'image/png': 'fa-file-image-o',
        'image/gif': 'fa-file-image-o',
        'image/webp': 'fa-file-image-o',
        'text/plain': 'fa-file-text-o',
        'application/zip': 'fa-file-archive-o',
        'application/x-rar-compressed': 'fa-file-archive-o'
    };
    
    return iconMap[fileType] || 'fa-file-o';
};

// Global function for formatting file size
window.formatFileSize = function(bytes) {
    if (bytes === 0) return '0 Bytes';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Global function for deleting attachments (accessible from onclick)
function deleteAttachment(attachmentId) {
    if (confirm('Are you sure you want to delete this attachment?')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_appointment_attachment/' + attachmentId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    loadAttachments(); // Reload attachments
                } else {
                    alert_float('danger', response.message);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error deleting attachment');
            }
        });
    }
}
</script>
