<?php
/**
 * Attachments JavaScript functionality for appointment view
 * This file contains all JavaScript functions related to appointment attachments
 */
?>

<script>
// Disable Dropzone auto-discovery to prevent automatic initialization
if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
}

// Track if Dropzone has been initialized (global flag)
var attachmentDropzoneInitialized = false;

$(document).ready(function() {
    // Load attachments when attachments tab is clicked
    $('a[href="#attachments-tab"]').on('click', function() {
        loadAttachments(true); // Force refresh when tab is clicked
    });
    
    // Initialize Dropzone when upload modal is opened (lazy initialization)
    $('#attachmentUploadModal').on('shown.bs.modal', function() {
        if (!attachmentDropzoneInitialized) {
            initializeAttachmentDropzone();
            attachmentDropzoneInitialized = true;
        }
    });
    
    // Check if we're on attachments tab on page load (integrate with main tab system)
    setTimeout(function() {
        var urlParams = new URLSearchParams(window.location.search);
        var tabParam = urlParams.get('tab');
        
        // If attachments tab is active via URL parameter
        if (tabParam === 'attachments') {
            loadAttachments(true);
        }
        
        // Also check if attachments tab is currently visible/active
        if ($('#attachments-tab').hasClass('active') || $('#attachments-tab').is(':visible')) {
            loadAttachments(true);
        }
        
        // Check if attachments tab link is active
        if ($('a[href="#attachments-tab"]').parent().hasClass('active')) {
            loadAttachments(true);
        }
    }, 800); // Slightly longer delay to ensure main tab system is initialized
});

// Global function for loading attachments
window.loadAttachments = function(forceRefresh = false) {
    // Show loading indicator if not already showing content
    var currentContent = $('#attachments-container').html();
    if (forceRefresh || currentContent.includes('Loading attachments') || currentContent === '') {
        $('#attachments-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br><p>Loading attachments...</p></div>');
    }
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_appointment_attachments/' + <?php echo $appointment->id; ?>,
        type: 'GET',
        dataType: 'json',
        cache: false, // Prevent caching to ensure fresh data
        success: function(response) {
            if (response.success) {
                displayAttachments(response.attachments);
            } else {
                $('#attachments-container').html('<div class="text-center text-muted"><p>No attachments found</p></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading attachments:', error);
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
            var fileExt = getFileExtension(attachment.original_name);
            var canPreview = ['pdf', 'ppt', 'pptx'].indexOf(fileExt.toLowerCase()) !== -1;
            
            html += '<div class="col-md-4 col-sm-6 col-xs-12 mbot15">';
            html += '<div class="panel panel-default attachment-card">';
            html += '<div class="panel-body text-center">';
            html += '<i class="fa ' + fileIcon + ' fa-3x text-muted mbot10"></i>';
            html += '<h5 class="text-ellipsis" title="' + attachment.original_name + '">' + attachment.original_name + '</h5>';
            html += '<p class="text-muted small">' + fileSize + ' • ' + uploadDate + '</p>';
            
            // Button container with consistent layout - ALL buttons in single row
            html += '<div class="attachment-buttons" style="margin-top: 10px;">';
            html += '<div class="btn-row">';
            
            // Always show Download and Delete buttons
            html += '<a href="' + admin_url + 'ella_contractors/appointments/download_attachment/' + attachment.id + '" class="btn btn-info btn-sm attachment-btn" target="_blank" title="Download File">';
            html += '<i class="fa fa-download"></i></a>';
            html += '<button class="btn btn-danger btn-sm attachment-btn" onclick="deleteAttachment(' + attachment.id + ')" title="Delete File">';
            html += '<i class="fa fa-trash"></i></button>';
            
            // Add Preview button for previewable files (PDF, PPT, PPTX)
            if (canPreview) {
                html += '<button class="btn btn-info btn-sm attachment-btn" onclick="previewAttachment(' + attachment.id + ', \'' + escapeHtml(attachment.original_name) + '\', \'' + fileExt.toLowerCase() + '\')" title="Preview File">';
                html += '<i class="fa fa-eye"></i></button>';
            }
            
            html += '</div>'; // Close btn-row
            html += '</div>'; // Close attachment-buttons
            html += '</div>'; // Close panel-body
            html += '</div>'; // Close panel
            html += '</div>'; // Close col
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

// Helper function to get file extension
window.getFileExtension = function(filename) {
    return filename.split('.').pop();
};

// Helper function to escape HTML for safe display
window.escapeHtml = function(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
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
                    loadAttachments(true); // Force reload attachments
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

/**
 * Initialize Dropzone for attachment uploads
 * Following the CRM pattern from projects and other modules
 */
function initializeAttachmentDropzone() {
    var dropzoneElement = document.querySelector('#appointment-attachment-upload');
    
    // Check if element exists
    if (!dropzoneElement) {
        return;
    }
    
    // Check if Dropzone is already attached (prevent re-initialization)
    if (dropzoneElement.dropzone) {
        dropzoneElement.dropzone.destroy();
    }
    
    // Create new Dropzone instance
    if ($('#appointment-attachment-upload').length > 0) {
        new Dropzone('#appointment-attachment-upload', appCreateDropzoneOptions({
            paramName: "file",
            uploadMultiple: true,
            parallelUploads: 10,
            maxFiles: 20,
            accept: function(file, done) {
                // Additional client-side validation if needed
                var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp',
                                    'application/pdf',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'application/vnd.ms-powerpoint',
                                    'application/vnd.openxmlformats-officedocument.presentationml.presentation'];
                
                if (allowedTypes.indexOf(file.type) === -1) {
                    done('File type not allowed: ' + file.type);
                } else {
                    done();
                }
            },
            init: function() {
                this.on("queuecomplete", function() {
                    // Reload attachments list after all uploads complete
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        setTimeout(function() {
                            // Force reload attachments
                            loadAttachments(true);
                            $('#attachmentUploadModal').modal('hide');
                            alert_float('success', 'Files uploaded successfully');
                        }, 300);
                    }
                });
                
                this.on("error", function(file, errorMessage) {
                    alert_float('danger', 'Upload failed: ' + errorMessage);
                });
                
                this.on("success", function(file, response) {
                    // Handle individual file success
                });
            },
            sending: function(file, xhr, formData) {
                // Add CSRF token to each upload request
                formData.append(csrf_token_name, csrf_hash);
            }
        }));
    }
}

/**
 * Preview attachment file (PDF, PPT, PPTX)
 * Similar to presentations module preview functionality
 */
function previewAttachment(attachmentId, fileName, fileExt) {
    // Set modal title
    $('#attachmentPreviewModalLabel').text('Preview: ' + fileName);
    
    // Clear previous content
    $('#attachmentPreviewContent').html('');
    
    // Show loading
    $('#attachmentPreviewContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br><br><p>Loading preview...</p></div>');
    
    // Show modal
    $('#attachmentPreviewModal').modal({show: true, backdrop: 'static', keyboard: false});
    
    // Set download link
    var downloadUrl = admin_url + 'ella_contractors/appointments/download_attachment/' + attachmentId;
    $('#downloadAttachmentBtn').attr('href', downloadUrl);
    
    // Generate preview content based on file type
    var previewContent = '';
    
    if (fileExt === 'pdf') {
        // Direct PDF preview
        var pdfUrl = admin_url + 'ella_contractors/appointments/preview_attachment/' + attachmentId;
        previewContent = '<iframe src="' + pdfUrl + '" width="100%" height="600px" frameborder="0" style="border: none;"></iframe>';
    } else if (fileExt === 'ppt' || fileExt === 'pptx') {
        // PPT/PPTX preview - use get_preview_pdf endpoint
        var pptPreviewUrl = admin_url + 'ella_contractors/appointments/preview_attachment/' + attachmentId;
        previewContent = '<iframe src="' + pptPreviewUrl + '" width="100%" height="600px" frameborder="0" style="border: none;"></iframe>';
    } else {
        previewContent = '<div class="alert alert-info text-center">' +
            '<h5><i class="fa fa-info-circle"></i> Preview Not Available</h5>' +
            '<p>Preview is not available for this file type (' + fileExt.toUpperCase() + ').</p>' +
            '<p><strong>File:</strong> ' + fileName + '</p>' +
            '<a href="' + downloadUrl + '" class="btn btn-primary" target="_blank">' +
            '<i class="fa fa-external-link"></i> Open in New Tab</a>' +
            '</div>';
    }
    
    // Set preview content after a short delay to show loading
    setTimeout(function() {
        $('#attachmentPreviewContent').html(previewContent);
    }, 300);
    
    // Refresh attachments when preview modal is closed (in case files were added externally)
    $('#attachmentPreviewModal').off('hidden.bs.modal.attachments').on('hidden.bs.modal.attachments', function() {
        setTimeout(function() {
            loadAttachments(true);
        }, 100);
    });
}

// Handle modal close events
$('#attachmentPreviewModal').on('hidden.bs.modal', function () {
    $('#attachmentPreviewContent').html('');
});

// Handle upload modal close - ensure attachments refresh
$('#attachmentUploadModal').on('hidden.bs.modal', function () {
    // Small delay to ensure any pending uploads are processed
    setTimeout(function() {
        if (typeof loadAttachments === 'function') {
            loadAttachments(true);
        }
    }, 200);
});

// Handle iframe load errors
$(document).on('error', 'iframe', function() {
    var iframe = $(this);
    iframe.parent().html('<div class="alert alert-warning text-center">' +
        '<h5><i class="fa fa-exclamation-triangle"></i> Preview Error</h5>' +
        '<p>Unable to load preview. This may be due to:</p>' +
        '<ul class="text-left" style="display: inline-block; text-align: left;">' +
        '<li>File format compatibility issues</li>' +
        '<li>Large file size</li>' +
        '<li>Browser security restrictions</li>' +
        '</ul><br>' +
        '<a href="' + $('#downloadAttachmentBtn').attr('href') + '" class="btn btn-primary" target="_blank">' +
        '<i class="fa fa-download"></i> Download File</a>' +
        '</div>');
});
</script>

<style>
/* Attachment Button Styles - Consistent with CRM UI */
.attachment-buttons {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.btn-row {
    display: flex;
    justify-content: center;
    gap: 5px;
}

.attachment-btn {
    width: 38px !important;
    height: 35px !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 4px !important;
    font-size: 14px !important;
    border: none !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12) !important;
    transition: all 0.2s ease !important;
    margin: 0 2px !important;
}

.attachment-btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
}

/* Removed .attachment-btn-full since all buttons are now in single row */

/* Preview button - Blue like download */
.attachment-btn.btn-info {
    background-color: #3498db !important;
    color: white !important;
}

.attachment-btn.btn-info:hover {
    background-color: #2980b9 !important;
}

/* Delete button - Red like the reference image */
.attachment-btn.btn-danger {
    background-color: #e74c3c !important;
    color: white !important;
}

.attachment-btn.btn-danger:hover {
    background-color: #c0392b !important;
}

/* Icon styling */
.attachment-btn i {
    margin: 0 !important;
    font-size: 14px !important;
}

/* Consistent card sizing */
.attachment-card {
    min-height: 200px !important;
    display: flex !important;
    flex-direction: column !important;
}

.attachment-card .panel-body {
    flex: 1 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    padding: 15px !important;
}

.attachment-card .attachment-buttons {
    margin-top: auto !important;
}
</style>
