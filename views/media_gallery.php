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
                        <div class="media-page-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="header-content">
                                        <h4 class="page-title"><?= $title ?></h4>
                                        <?php if ($contract_id): ?>
                                        <p class="page-subtitle">Media files for this specific contract</p>
                                        <?php else: ?>
                                        <p class="page-subtitle">Default media files available for all contracts</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="header-actions">
                                        <?php if ($contract_id): ?>
                                        <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract_id) ?>" class="btn btn-outline-secondary">
                                            <i class="fa fa-arrow-left"></i> Back to Contract
                                        </a>
                                        <a href="<?= admin_url('ella_contractors/upload_media/' . $contract_id) ?>" class="btn btn-gradient-primary">
                                            <i class="fa fa-upload"></i> Upload Media
                                        </a>
                                        <?php else: ?>
                                        <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-outline-secondary">
                                            <i class="fa fa-arrow-left"></i> Back to Contracts
                                        </a>
                                        <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-gradient-primary">
                                            <i class="fa fa-upload"></i> Upload Default Media
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Gallery -->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Error Display -->
                                <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <strong>Error:</strong> <?= $error ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($media_files)): ?>
                                <div class="media-grid">
                                    <?php foreach ($media_files as $media): ?>
                                    <div class="media-grid-item">
                                        <div class="media-card">
                                            
                                            <!-- File Icon/Preview -->
                                            <div class="media-icon-section">
                                                <div class="media-icon">
                                                    <i class="fa <?= get_file_icon($media->file_type) ?>"></i>
                                                </div>
                                            </div>
                                            
                                            <!-- File Info -->
                                            <div class="media-info-section">
                                                <h6 class="media-title">
                                                    <?= character_limiter($media->original_name, 30) ?>
                                                </h6>
                                                
                                                <div class="media-meta">
                                                    <div class="media-meta-item">
                                                        <i class="fa fa-hdd-o"></i>
                                                        <?= format_file_size($media->file_size) ?>
                                                    </div>
                                                    <div class="media-meta-item">
                                                        <i class="fa fa-calendar"></i>
                                                        <?= _dt($media->date_uploaded) ?>
                                                    </div>
                                                </div>
                                                
                                                <?php if ($media->description): ?>
                                                <div class="media-description">
                                                    <?= character_limiter($media->description, 60) ?>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($media->is_default): ?>
                                                <span class="media-default-badge">
                                                    <i class="fa fa-star"></i> Default
                                                </span>
                                                <?php endif; ?>
                                                
                                                <!-- Action Buttons -->
                                                <div class="media-actions">
                                                    <div class="media-btn-group">
                                                        <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                           target="_blank" class="media-btn media-btn-view" title="View File">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                           download class="media-btn media-btn-download" title="Download File">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" 
                                                           onclick="confirmDeleteMedia(<?= $media->id ?>, '<?= addslashes($media->original_name) ?>', '<?= urlencode(current_url()) ?>')" 
                                                           class="media-btn media-btn-delete" title="Delete File">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Summary -->
                                <div class="media-summary">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Summary:</strong> 
                                        <?= count($media_files) ?> media file(s) found. 
                                        Total size: <strong><?= format_file_size(array_sum(array_column($media_files, 'file_size'))) ?></strong>
                                        <?php if ($contract_id): ?>
                                        for this contract.
                                        <?php else: ?>
                                        in default gallery.
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php else: ?>
                                <!-- Empty State -->
                                <div class="media-empty-state">
                                    <div class="media-empty-icon">
                                        <i class="fa fa-folder-open"></i>
                                    </div>
                                    <h3 class="media-empty-title">No Media Files Found</h3>
                                    <?php if ($contract_id): ?>
                                    <p class="media-empty-description">No media files have been uploaded for this contract yet.</p>
                                    <a href="<?= admin_url('ella_contractors/upload_media/' . $contract_id) ?>" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Upload First Media File
                                    </a>
                                    <?php else: ?>
                                    <p class="media-empty-description">No default media files have been uploaded yet.</p>
                                    <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Upload Default Media
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<?php init_tail(); ?>

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

// Configure SweetAlert2 options
const swalConfig = {
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true
};

// Function to confirm media deletion with SweetAlert
function confirmDeleteMedia(mediaId, fileName, redirectUrl) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete:<br><strong>"${fileName}"</strong><br><br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-custom-popup',
            title: 'swal2-custom-title',
            htmlContainer: 'swal2-custom-html',
            confirmButton: 'swal2-custom-confirm',
            cancelButton: 'swal2-custom-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                html: 'Please wait while we delete the file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Perform the deletion
            window.location.href = '<?= admin_url('ella_contractors/delete_media/') ?>' + mediaId + '?redirect=' + redirectUrl;
        }
    });
}

// Function to show success toastr notification
function showSuccessToast(message, title = 'Success!') {
    toastr.success(message, title, {
        timeOut: 4000,
        extendedTimeOut: 2000,
        progressBar: true,
        closeButton: true
    });
}

// Function to show error toastr notification
function showErrorToast(message, title = 'Error!') {
    toastr.error(message, title, {
        timeOut: 6000,
        extendedTimeOut: 3000,
        progressBar: true,
        closeButton: true
    });
}

// Function to show info toastr notification
function showInfoToast(message, title = 'Info') {
    toastr.info(message, title, {
        timeOut: 5000,
        extendedTimeOut: 2000,
        progressBar: true,
        closeButton: true
    });
}

// Function to show warning toastr notification
function showWarningToast(message, title = 'Warning!') {
    toastr.warning(message, title, {
        timeOut: 5000,
        extendedTimeOut: 2000,
        progressBar: true,
        closeButton: true
    });
}

// Check for URL parameters to show appropriate notifications
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Check for success message
    if (urlParams.get('delete_success') === '1') {
        showSuccessToast('Media file has been deleted successfully!', 'File Deleted');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Check for error message
    if (urlParams.get('delete_error') === '1') {
        showErrorToast('There was an error deleting the media file. Please try again.', 'Delete Failed');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Check for upload success message
    if (urlParams.get('upload_success') === '1') {
        showSuccessToast('Media file has been uploaded successfully!', 'Upload Complete');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Check for upload error message
    if (urlParams.get('upload_error') === '1') {
        showErrorToast('There was an error uploading the media file. Please try again.', 'Upload Failed');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Check for general success message
    if (urlParams.get('success') === '1') {
        const message = urlParams.get('message') || 'Operation completed successfully!';
        showSuccessToast(message, 'Success');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
    // Check for general error message
    if (urlParams.get('error') === '1') {
        const message = urlParams.get('message') || 'An error occurred. Please try again.';
        showErrorToast(message, 'Error');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

// Add click event listeners for better user feedback
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to action buttons
    const actionButtons = document.querySelectorAll('.media-btn');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('media-btn-delete')) {
                return; // Don't add loading for delete buttons (handled by SweetAlert)
            }
            
            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            this.style.pointerEvents = 'none';
            
            // Remove loading state after a short delay (for view/download buttons)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 1000);
        });
    });
});
</script>
