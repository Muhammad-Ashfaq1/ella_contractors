<?php
/**
 * Attachments JavaScript functionality for appointment view
 * This file contains all JavaScript functions related to appointment attachments
 */
?>

<script>
// Define appointment ID for this file (available from parent view)
var attachmentAppointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;

// Disable Dropzone auto-discovery to prevent automatic initialization
if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
}

$(document).ready(function() {
    // Load attachments when attachments tab is clicked
    $('a[href="#attachments-tab"]').on('click', function() {
        loadAttachments(true); // Force refresh when tab is clicked
    });
    
    // Initialize custom dropzone when upload modal is opened
    $('#attachmentUploadModal').on('shown.bs.modal', function() {
        // Always reinitialize to ensure clean state
        initializeAttachmentDropzone();
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
        url: admin_url + 'ella_contractors/appointments/get_appointment_attachments/' + attachmentAppointmentId,
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
            var isImage = attachment.file_type.startsWith('image/');
            
            html += '<div class="col-md-4 col-sm-6 col-xs-12 mbot15">';
            html += '<div class="panel panel-default attachment-card">';
            html += '<div class="panel-body text-center">';
            
            // Show actual image preview for images, icon for other files
            if (isImage) {
                var imageUrl = admin_url + 'ella_contractors/appointments/download_attachment/' + attachment.id;
                html += '<div class="attachment-preview-container">';
                html += '<img src="' + imageUrl + '" class="attachment-preview-image" alt="' + escapeHtml(attachment.original_name) + '" onclick="previewImageAttachment(\'' + imageUrl + '\', \'' + escapeHtml(attachment.original_name) + '\')" />';
                html += '</div>';
            } else {
                html += '<div class="attachment-icon-container">';
                html += '<i class="fa ' + fileIcon + ' fa-3x text-muted mbot10"></i>';
                html += '</div>';
            }
            
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
            
            // Add Preview button for previewable files (PDF, PPT, PPTX, Images)
            if (canPreview) {
                html += '<button class="btn btn-info btn-sm attachment-btn" onclick="previewAttachment(' + attachment.id + ', \'' + escapeHtml(attachment.original_name) + '\', \'' + fileExt.toLowerCase() + '\')" title="Preview File">';
                html += '<i class="fa fa-eye"></i></button>';
            } else if (isImage) {
                // For images, clicking preview shows full size in modal
                html += '<button class="btn btn-info btn-sm attachment-btn" onclick="previewImageAttachment(\'' + admin_url + 'ella_contractors/appointments/download_attachment/' + attachment.id + '\', \'' + escapeHtml(attachment.original_name) + '\')" title="View Full Size">';
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
 * CUSTOM DROPZONE FOR ATTACHMENT UPLOADS (BATCH UPLOAD)
 * Replicates appointment modal dropzone behavior
 * - Drag/drop and click to browse
 * - Preview thumbnails
 * - Remove files before upload
 * - Batch upload (not immediate)
 */

var attachmentViewFiles = []; // Store files in memory
var MAX_ATTACHMENT_FILES = 10;

function initializeAttachmentDropzone() {
    const dropZoneElement = document.querySelector("#attachmentViewDropzone");
    const inputElement = document.querySelector("#attachment_files");
    const thumbnailsContainer = document.querySelector("#attachmentViewThumbnails");
    const promptElement = document.querySelector("#attachmentViewDropzone .drop-zone__prompt");
    const uploadBtn = document.querySelector("#uploadAttachmentsBtn");
    const fileCountBadge = document.querySelector("#fileCountBadge");
    
    if (!dropZoneElement || !inputElement) {
        console.warn('Attachment dropzone elements not found');
        return; // Elements not found, exit
    }
    
    // Reset files array on initialization
    attachmentViewFiles = [];
    
    // Click to browse
    dropZoneElement.addEventListener("click", function(e) {
        if (e.target === inputElement || e.target.closest('.removeimage')) {
            return; // Don't trigger if clicking input or remove button
        }
        inputElement.click();
    });
    
    // File selection via input
    inputElement.addEventListener("change", function(e) {
        if (inputElement.files.length > 0) {
            handleAttachmentFiles(inputElement.files);
        }
    });
    
    // Drag & Drop events
    dropZoneElement.addEventListener("dragover", function(e) {
        e.preventDefault();
        dropZoneElement.classList.add("drop-zone--over");
    });
    
    ["dragleave", "dragend"].forEach(function(type) {
        dropZoneElement.addEventListener(type, function(e) {
            dropZoneElement.classList.remove("drop-zone--over");
        });
    });
    
    dropZoneElement.addEventListener("drop", function(e) {
        e.preventDefault();
        dropZoneElement.classList.remove("drop-zone--over");
        
        if (e.dataTransfer.files.length > 0) {
            handleAttachmentFiles(e.dataTransfer.files);
        }
    });
    
    // Handle files (add to array and show preview)
    function handleAttachmentFiles(files) {
        const remainingSlots = MAX_ATTACHMENT_FILES - attachmentViewFiles.length;
        
        if (files.length > remainingSlots) {
            return;
        }
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            
            // Validate file size (50MB)
            if (file.size > 50 * 1024 * 1024) {
                alert_float('danger', 'File "' + file.name + '" is too large. Maximum size is 50MB.');
                continue;
            }
            
            // Add to array
            attachmentViewFiles.push(file);
            
            // Create thumbnail
            createAttachmentThumbnail(file, attachmentViewFiles.length - 1);
        }
        
        updateAttachmentDropzoneUI();
    }
    
    // Create thumbnail preview
    function createAttachmentThumbnail(file, index) {
        const thumbnailElement = document.createElement("div");
        thumbnailElement.classList.add("drop-zone__thumb");
        thumbnailElement.dataset.index = index;
        
        // File icon or image preview
        if (file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                thumbnailElement.style.backgroundImage = "url('" + reader.result + "')";
                thumbnailElement.style.backgroundSize = "cover";
            };
        } else {
            // Show file icon for non-images with proper colors (matching appointment modal)
            const iconData = getFileIconWithColor(file.type);
            thumbnailElement.innerHTML = '<div style="text-align: center; padding: 20px;">' +
                '<i class="' + iconData.icon + '" style="font-size: 48px; color: ' + iconData.color + ';"></i>' +
                '</div>';
        }
        
        // File name label
        const fileNameDiv = document.createElement("div");
        fileNameDiv.textContent = file.name;
        fileNameDiv.className = "file-name-label";
        thumbnailElement.appendChild(fileNameDiv);
        
        // Remove button
        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.classList.add("removeimage");
        removeBtn.innerHTML = "×";
        removeBtn.onclick = function(e) {
            e.stopPropagation();
            removeAttachmentFile(index);
        };
        thumbnailElement.appendChild(removeBtn);
        
        thumbnailsContainer.appendChild(thumbnailElement);
    }
    
    // Remove file from array
    function removeAttachmentFile(index) {
        attachmentViewFiles.splice(index, 1);
        
        // Clear and rebuild thumbnails
        thumbnailsContainer.innerHTML = '';
        attachmentViewFiles.forEach(function(file, idx) {
            createAttachmentThumbnail(file, idx);
        });
        
        updateAttachmentDropzoneUI();
    }
    
    // Update UI based on file count
    function updateAttachmentDropzoneUI() {
        const fileCount = attachmentViewFiles.length;
        
        // Update badge
        fileCountBadge.textContent = fileCount;
        
        // Enable/disable upload button
        if (fileCount > 0) {
            uploadBtn.disabled = false;
            uploadBtn.classList.remove('btn-default');
            uploadBtn.classList.add('btn-info');
            promptElement.style.display = 'none';
        } else {
            uploadBtn.disabled = true;
            uploadBtn.classList.add('btn-default');
            uploadBtn.classList.remove('btn-info');
            promptElement.style.display = 'block';
        }
        
        // Update hidden count field
        document.querySelector("#attachment_files_count").value = fileCount;
    }
    
    // Get icon and color based on file type (matching appointment modal)
    function getFileIconWithColor(mimeType) {
        if (mimeType.includes('pdf')) {
            return { icon: 'fa fa-file-pdf-o', color: '#dc3545' }; // Red like Adobe PDF
        }
        if (mimeType.includes('word') || mimeType.includes('document')) {
            return { icon: 'fa fa-file-word-o', color: '#2b579a' }; // Blue like Word
        }
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) {
            return { icon: 'fa fa-file-excel-o', color: '#217346' }; // Green like Excel
        }
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) {
            return { icon: 'fa fa-file-powerpoint-o', color: '#d24726' }; // Orange like PowerPoint
        }
        return { icon: 'fa fa-file-o', color: '#666' }; // Grey for unknown types
    }
}

// Upload button click handler
$(document).on('click', '#uploadAttachmentsBtn', function() {
    if (attachmentViewFiles.length === 0) {
        alert_float('warning', 'Please select files to upload');
        return;
    }
    
    // Verify appointment ID is available
    if (!attachmentAppointmentId || attachmentAppointmentId === 0) {
        alert_float('danger', 'Error: Appointment ID not found. Please refresh the page.');
        console.error('attachmentAppointmentId is not defined or is 0');
        return;
    }
    
    // Show progress
    const $btn = $(this);
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
    
    // Create FormData and append all files with array notation
    const formData = new FormData();
    
    // Debug: Log files being uploaded
    console.log('Uploading ' + attachmentViewFiles.length + ' file(s):', attachmentViewFiles.map(f => f.name));
    
    // Append each file - PHP will receive them as $_FILES['file']['name'][0], $_FILES['file']['name'][1], etc.
    attachmentViewFiles.forEach(function(file, index) {
        formData.append('file[]', file); // Use array notation for multiple files
        console.log('Appended file[' + index + ']:', file.name, '(' + file.size + ' bytes)');
    });
    
    // Add CSRF token
    formData.append(csrf_token_name, csrf_hash);
    
    // Debug: Log FormData entries
    console.log('FormData entries:');
    for (var pair of formData.entries()) {
        if (pair[1] instanceof File) {
            console.log('  ' + pair[0] + ': [File] ' + pair[1].name);
        } else {
            console.log('  ' + pair[0] + ': ' + pair[1]);
        }
    }
    
    // AJAX upload
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/upload_attachment/' + attachmentAppointmentId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', response.message);
                
                // Clear files array and thumbnails
                attachmentViewFiles = [];
                const thumbnailsContainer = document.querySelector("#attachmentViewThumbnails");
                if (thumbnailsContainer) {
                    thumbnailsContainer.innerHTML = '';
                }
                const promptElement = document.querySelector("#attachmentViewDropzone .drop-zone__prompt");
                if (promptElement) {
                    promptElement.style.display = 'block';
                }
                
                // Reset button
                $btn.removeClass('btn-info').addClass('btn-default');
                $btn.prop('disabled', true).html(originalText);
                const fileCountBadge = document.querySelector("#fileCountBadge");
                if (fileCountBadge) {
                    fileCountBadge.textContent = '0';
                }
                
                // Close modal
                $('#attachmentUploadModal').modal('hide');
                
                // Reload attachments grid
                if (typeof loadAttachments === 'function') {
                    loadAttachments(true);
                }
            } else {
                alert_float('danger', response.message || 'Upload failed');
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error uploading files: ' + error);
            $btn.prop('disabled', false).html(originalText);
        }
    });
});

// Reset dropzone when modal is closed
$('#attachmentUploadModal').on('hidden.bs.modal', function() {
    attachmentViewFiles = [];
    const thumbnailsContainer = document.querySelector("#attachmentViewThumbnails");
    if (thumbnailsContainer) {
        thumbnailsContainer.innerHTML = '';
    }
    const promptElement = document.querySelector("#attachmentViewDropzone .drop-zone__prompt");
    if (promptElement) {
        promptElement.style.display = 'block';
    }
    const uploadBtn = document.querySelector("#uploadAttachmentsBtn");
    if (uploadBtn) {
        uploadBtn.disabled = true;
        uploadBtn.classList.remove('btn-info');
        uploadBtn.classList.add('btn-default');
        uploadBtn.innerHTML = '<i class="fa fa-upload"></i> Upload Files (<span id="fileCountBadge">0</span>)';
    }
});

/**
 * Preview image attachment in modal
 */
function previewImageAttachment(imageUrl, fileName) {
    // Set modal title
    $('#attachmentPreviewModalLabel').text('Preview: ' + fileName);
    
    // Clear previous content
    $('#attachmentPreviewContent').html('');
    
    // Show loading
    $('#attachmentPreviewContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br><br><p>Loading image...</p></div>');
    
    // Show modal
    $('#attachmentPreviewModal').modal({show: true, backdrop: 'static', keyboard: false});
    
    // Set download link
    $('#downloadAttachmentBtn').attr('href', imageUrl);
    
    // Show full-size image
    var imagePreview = '<div class="text-center" style="padding: 20px;">' +
        '<img src="' + imageUrl + '" style="max-width: 100%; height: auto; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" alt="' + fileName + '" />' +
        '</div>';
    
    // Set preview content after a short delay
    setTimeout(function() {
        $('#attachmentPreviewContent').html(imagePreview);
    }, 300);
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
    min-height: 250px !important;
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

/* Image preview styling */
.attachment-preview-container {
    width: 100%;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    overflow: hidden;
}

.attachment-preview-image {
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.attachment-preview-image:hover {
    transform: scale(1.05);
}

/* Icon container for non-image files */
.attachment-icon-container {
    width: 100%;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}
</style>
