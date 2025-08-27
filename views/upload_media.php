<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Ensure jQuery is loaded before any CSRF setup
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
}

// Override the problematic CSRF function to prevent errors
window.csrf_jquery_ajax_setup = function() {
    // Do nothing - prevent the error from general_helper.php
    return false;
};

// File preview functionality
$(document).ready(function() {
    $('#media_file').change(function() {
        var file = this.files[0];
        if (file) {
            showFilePreview(file);
        } else {
            hideFilePreview();
        }
    });

    // Form validation
    $('#upload-form').submit(function(e) {
        var file = $('#media_file')[0].files[0];
        var category = $('#media_category').val();
        
        if (!file) {
            e.preventDefault();
            toastr.error('Please select a file to upload.');
            return false;
        }
        
        if (!category) {
            e.preventDefault();
            toastr.error('Please select a media category.');
            return false;
        }
        
        // Check file size (50MB limit)
        if (file.size > 50 * 1024 * 1024) {
            e.preventDefault();
            toastr.error('File size exceeds 50MB limit. Please choose a smaller file.');
            return false;
        }
        
        // Show loading state
        $('#upload-form button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        
        return true;
    });
});

function showFilePreview(file) {
    var icon = getFileIcon(file.name);
    var size = formatFileSize(file.size);
    var type = getFileType(file.name);
    
    $('#preview-icon').attr('class', 'fa ' + icon);
    $('#preview-filename').text(file.name);
    $('#preview-filesize').text(size);
    $('#preview-filetype').text(type);
    
    $('#file-preview-section').show();
}

function hideFilePreview() {
    $('#file-preview-section').hide();
}

function getFileIcon(filename) {
    var ext = filename.split('.').pop().toLowerCase();
    var iconMap = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word', 'docx': 'fa-file-word',
        'xls': 'fa-file-excel', 'xlsx': 'fa-file-excel',
        'ppt': 'fa-file-powerpoint', 'pptx': 'fa-file-powerpoint',
        'jpg': 'fa-file-image', 'jpeg': 'fa-file-image', 'png': 'fa-file-image', 'gif': 'fa-file-image', 'bmp': 'fa-file-image',
        'mp4': 'fa-file-video', 'avi': 'fa-file-video', 'mov': 'fa-file-video', 'wmv': 'fa-file-video',
        'mp3': 'fa-file-audio', 'wav': 'fa-file-audio',
        'zip': 'fa-file-archive', 'rar': 'fa-file-archive', '7z': 'fa-file-archive'
    };
    return iconMap[ext] || 'fa-file';
}

function getFileType(filename) {
    var ext = filename.split('.').pop().toLowerCase();
    var typeMap = {
        'pdf': 'PDF Document',
        'doc': 'Word Document', 'docx': 'Word Document',
        'xls': 'Excel Spreadsheet', 'xlsx': 'Excel Spreadsheet',
        'ppt': 'PowerPoint Presentation', 'pptx': 'PowerPoint Presentation',
        'jpg': 'JPEG Image', 'jpeg': 'JPEG Image', 'png': 'PNG Image', 'gif': 'GIF Image', 'bmp': 'BMP Image',
        'mp4': 'MP4 Video', 'avi': 'AVI Video', 'mov': 'MOV Video', 'wmv': 'WMV Video',
        'mp3': 'MP3 Audio', 'wav': 'WAV Audio',
        'zip': 'ZIP Archive', 'rar': 'RAR Archive', '7z': '7-Zip Archive'
    };
    return typeMap[ext] || 'Unknown File Type';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    var k = 1024;
    var sizes = ['Bytes', 'KB', 'MB', 'GB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
</script>

<?php init_head(); ?>

<!-- Include module CSS -->
<link rel="stylesheet" href="<?php echo base_url('modules/ella_contractors/assets/css/ella_contractors.css'); ?>">

<!-- Include Toastr CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Include SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.min.js"></script>

<div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        
                        <!-- Page Header -->
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="customer-profile-group-heading"><?= $title ?></h4>
                                <?php if ($contract_id): ?>
                                <div class="contract-info-box">
                                    <div class="contract-header">
                                        <i class="fa fa-file-contract"></i>
                                        <strong>Contract: <?= isset($contract_subject) ? $contract_subject : 'Contract #' . $contract_id ?></strong>
                                    </div>
                                    <p class="text-muted mb-0">Upload media files specifically for this contract. These files will only be visible for this contract.</p>
                                </div>
                                <?php else: ?>
                                <div class="default-media-info-box">
                                    <div class="default-media-header">
                                        <i class="fa fa-star"></i>
                                        <strong>Default Media Gallery</strong>
                                    </div>
                                    <p class="text-muted mb-0">Upload default media files that will be available for all contracts. These files will be visible in every contract's media section.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($contract_id): ?>
                                <a href="<?= admin_url('ella_contractors/view_contract/' . $contract_id) ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Contract
                                </a>
                                <?php else: ?>
                                <a href="<?= admin_url('ella_contractors/media_gallery') ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Gallery
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <!-- Upload Form -->
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel_s">
                                    <div class="panel-heading">
                                        <h5>Upload Media File</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?= form_open_multipart('', ['id' => 'upload-form']) ?>
                                        
                                        <div class="form-group">
                                            <label for="media_file">Select File <span class="text-danger">*</span></label>
                                            <input type="file" name="media_file" id="media_file" class="form-control" required 
                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.bmp,.mp4,.avi,.mov,.wmv,.mp3,.wav,.zip,.rar,.7z">
                                            <small class="help-block">
                                                <strong>Allowed formats:</strong> PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, BMP, MP4, AVI, MOV, WMV, MP3, WAV, ZIP, RAR, 7Z<br>
                                                <strong>Maximum file size:</strong> 50MB<br>
                                                <strong>Recommended:</strong> Use descriptive filenames for better organization
                                            </small>
                                        </div>

                                        <!-- File Preview Section -->
                                        <div class="form-group" id="file-preview-section" style="display: none;">
                                            <label>File Preview</label>
                                            <div class="file-preview-container">
                                                <div class="file-preview-info">
                                                    <div class="file-preview-icon">
                                                        <i class="fa fa-file" id="preview-icon"></i>
                                                    </div>
                                                    <div class="file-preview-details">
                                                        <h6 id="preview-filename">filename.ext</h6>
                                                        <p id="preview-filesize">0 KB</p>
                                                        <p id="preview-filetype">File Type</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="media_category">Media Category <span class="text-danger">*</span></label>
                                            <select name="media_category" id="media_category" class="form-control" required>
                                                <option value="">Select Category</option>
                                                <option value="documents">Documents (PDF, Word, Excel)</option>
                                                <option value="images">Images (Photos, Screenshots)</option>
                                                <option value="presentations">Presentations (PowerPoint)</option>
                                                <option value="contracts">Contract Files</option>
                                                <option value="invoices">Invoices & Financial</option>
                                                <option value="blueprints">Blueprints & Plans</option>
                                                <option value="videos">Videos & Animations</option>
                                                <option value="audio">Audio Files</option>
                                                <option value="archives">Archives (ZIP, RAR)</option>
                                                <option value="other">Other Files</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="3" 
                                                      placeholder="Detailed description of this file (e.g., 'Project blueprint for Phase 1', 'Client invoice for March 2024')"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="tags">Tags (Optional)</label>
                                            <input type="text" name="tags" id="tags" class="form-control" 
                                                   placeholder="Enter tags separated by commas (e.g., blueprint, phase1, construction)">
                                            <small class="help-block">Tags help organize and search media files</small>
                                        </div>

                                        <!-- Dynamic Default Media Checkbox -->
                                        <div class="form-group">
                                                                                            <div class="checkbox checkbox-default-media">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="is_default" 
                                                           value="1" 
                                                           id="is_default_checkbox"
                                                           <?php 
                                                           $should_check = (empty($contract_id) || $contract_id === null || $contract_id === '' || $contract_id === 0);
                                                           echo $should_check ? 'checked' : ''; 
                                                           ?>>
                                                    <strong id="checkbox_label">
                                                        <?php if ($should_check): ?>
                                                            Use as default media for all contracts
                                                        <?php else: ?>
                                                            Also use as default media for all contracts
                                                        <?php endif; ?>
                                                    </strong>
                                                    <br><small class="text-muted" id="checkbox_description">
                                                        <?php if ($should_check): ?>
                                                            This file will be available in all contract media galleries
                                                        <?php else: ?>
                                                            This file will be available in all contract media galleries, not just this contract
                                                        <?php endif; ?>
                                                    </small>
                                                </label>
                                            </div>
                                            
                                            <!-- Additional Info Panel -->
                                            <div class="alert alert-info checkbox-info-panel" id="default_info_panel">
                                                <strong>Default Media Files:</strong>
                                                <p>These files will be used for all contracts unless overridden by contract-specific uploads.</p>
                                                <ul class="list-unstyled media-info-list">
                                                    <li><i class="fa fa-check text-success"></i> Contract templates</li>
                                                    <li><i class="fa fa-check text-success"></i> Standard forms</li>
                                                    <li><i class="fa fa-check text-success"></i> Legal documents</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fa fa-upload"></i> Upload File
                                            </button>
                                            <?php if ($contract_id): ?>
                                            <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract_id) ?>" class="btn btn-default btn-lg">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                            <?php else: ?>
                                            <a href="<?= admin_url('ella_contractors/media_gallery') ?>" class="btn btn-default btn-lg">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                            <?php endif; ?>
                                        </div>

                                        <?= form_close() ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Type Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="file-type-info">
                                    <h5><i class="fa fa-info-circle"></i> Supported File Types</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Documents:</strong><br>
                                            <i class="fa fa-file-pdf-o"></i> PDF<br>
                                            <i class="fa fa-file-word-o"></i> DOC, DOCX
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Spreadsheets:</strong><br>
                                            <i class="fa fa-file-excel-o"></i> XLS, XLSX
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Presentations:</strong><br>
                                            <i class="fa fa-file-powerpoint-o"></i> PPT, PPTX
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Images & Archives:</strong><br>
                                            <i class="fa fa-file-image-o"></i> JPG, PNG, GIF<br>
                                            <i class="fa fa-file-archive-o"></i> ZIP, RAR
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Include module JavaScript -->
<script src="<?php echo base_url('modules/ella_contractors/assets/js/ella_contractors.js'); ?>"></script>

<script>
// Configure Toastr options
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Form validation and submission handling
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('upload-form');
    const fileInput = document.getElementById('media_file');
    const submitButton = uploadForm.querySelector('button[type="submit"]');
    
    // File size validation (50MB = 50 * 1024 * 1024 bytes)
    const maxFileSize = 50 * 1024 * 1024;
    
    // File type validation
    const allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'application/zip',
        'application/x-rar-compressed'
    ];
    
    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size
            if (file.size > maxFileSize) {
                showErrorToast('File size exceeds 50MB limit. Please select a smaller file.', 'File Too Large');
                fileInput.value = '';
                return;
            }
            
            // Validate file type
            if (!allowedTypes.includes(file.type)) {
                showErrorToast('File type not supported. Please select a valid file format.', 'Invalid File Type');
                fileInput.value = '';
                return;
            }
            
            // Show success message for valid file
            showSuccessToast(`File "${file.name}" selected successfully!`, 'File Selected');
            
            // Update submit button text
            updateSubmitButtonText();
        }
    });
    
    // Form submission handler
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate file is selected
        if (!fileInput.files[0]) {
            showErrorToast('Please select a file to upload.', 'No File Selected');
            return;
        }
        
        // Show loading state
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Uploading...';
        submitButton.disabled = true;
        
        // Submit form
        uploadForm.submit();
    });
    
    // Function to update submit button text based on checkbox state
    function updateSubmitButtonText() {
        const isDefaultCheckbox = document.getElementById('is_default_checkbox');
        const submitButton = document.querySelector('button[type="submit"]');
        
        if (isDefaultCheckbox.checked) {
            submitButton.innerHTML = '<i class="fa fa-upload"></i> Upload as Default Media';
        } else {
            submitButton.innerHTML = '<i class="fa fa-upload"></i> Upload File';
        }
    }
    
    // Add event listener to checkbox
    const isDefaultCheckbox = document.getElementById('is_default_checkbox');
    const defaultInfoPanel = document.getElementById('default_info_panel');
    

    
    if (isDefaultCheckbox) {
        isDefaultCheckbox.addEventListener('change', function() {
            updateSubmitButtonText();
            toggleDefaultInfoPanel();
        });
        
        // Initial calls
        updateSubmitButtonText();
        toggleDefaultInfoPanel();
        
        // Ensure proper initial state
        setTimeout(function() {
            toggleDefaultInfoPanel();
        }, 100);
    }
    
    // Function to toggle default info panel visibility
    function toggleDefaultInfoPanel() {
        if (defaultInfoPanel && isDefaultCheckbox) {
            if (isDefaultCheckbox.checked) {
                defaultInfoPanel.style.display = 'block';
            } else {
                defaultInfoPanel.style.display = 'none';
            }
        }
    }
    
    // Check for URL parameters to show notifications
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('upload_success') === '1') {
        showSuccessToast('Media file uploaded successfully!', 'Upload Complete');
        // Remove parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    if (urlParams.get('upload_error') === '1') {
        const errorMessage = urlParams.get('message') || 'An error occurred during upload. Please try again.';
        showErrorToast(errorMessage, 'Upload Failed');
        // Remove parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Toastr notification functions
function showSuccessToast(message, title = 'Success!') {
    toastr.success(message, title, {
        timeOut: 4000,
        extendedTimeOut: 2000,
        progressBar: true,
        closeButton: true
    });
}

function showErrorToast(message, title = 'Error!') {
    toastr.error(message, title, {
        timeOut: 6000,
        extendedTimeOut: 3000,
        progressBar: true,
        closeButton: true
    });
}

function showInfoToast(message, title = 'Info') {
    toastr.info(message, title, {
        timeOut: 5000,
        extendedTimeOut: 2000,
        progressBar: true,
        closeButton: true
    });
}

function showWarningToast(message, title = 'Warning!') {
    toastr.warning(message, title, {
        timeOut: 5000,
        extendedTimeOut: 2000,
        progressBar: true,
        closeButton: true
    });
}
</script>

<?php init_tail(); ?>
