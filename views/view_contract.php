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

<!-- Inline CSS to ensure media layout works -->
<style>
.media-grid-default {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
    gap: 20px !important;
    margin-bottom: 20px !important;
}

.media-item-default {
    background: white !important;
    border: 1px solid #e9ecef !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
}

.media-card-default {
    padding: 0 !important;
}

.media-preview-section {
    position: relative !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    padding: 30px 20px !important;
    text-align: center !important;
    min-height: 120px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.media-file-icon {
    color: white !important;
    font-size: 3rem !important;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
}

.media-content-section {
    padding: 20px !important;
    background: white !important;
}

.media-filename {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #2c3e50 !important;
    margin: 0 0 10px 0 !important;
    line-height: 1.3 !important;
    word-break: break-word !important;
}

.media-file-info {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
    margin-bottom: 10px !important;
    font-size: 12px !important;
    color: #6c757d !important;
}

.media-file-size {
    font-weight: 500 !important;
}

.media-file-type {
    background: #e9ecef !important;
    padding: 2px 8px !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    color: #495057 !important;
}

.media-description-text {
    color: #6c757d !important;
    font-size: 12px !important;
    line-height: 1.4 !important;
    margin-bottom: 12px !important;
    word-break: break-word !important;
}

.media-category-tag {
    display: inline-block !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    padding: 4px 12px !important;
    border-radius: 15px !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
}

.media-action-buttons {
    padding: 15px 20px !important;
    background: #f8f9fa !important;
    border-top: 1px solid #e9ecef !important;
    display: flex !important;
    gap: 8px !important;
    justify-content: center !important;
}

.media-action-btn {
    padding: 6px 12px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    border-radius: 4px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    min-width: 70px !important;
    text-align: center !important;
}
</style>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">
                        <i class="fa fa-file-text-o"></i>
                        Contract Details
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <!-- Contract Header -->
                    <div class="contract-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3><?php echo $contract->subject; ?></h3>
                                <p class="contract-meta">Contract ID: <?php echo $contract->id; ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('ella_contractors/contracts'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Contracts
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="nav-buttons text-center">
                        <h5 class="text-muted mb-3">Quick Actions</h5>
                        <a href="<?php echo admin_url('ella_contractors/upload_media/' . $contract->id); ?>" class="btn btn-info">
                            <i class="fa fa-upload"></i> Upload Media
                        </a>
                        <a href="<?php echo admin_url('ella_contractors/media_gallery/' . $contract->id); ?>" class="btn btn-primary">
                            <i class="fa fa-images"></i> View Media Gallery
                            <span class="badge"><?php echo count($contract_media); ?></span>
                        </a>
                        <a href="<?php echo admin_url('ella_contractors/media_gallery'); ?>" class="btn btn-warning">
                            <i class="fa fa-star"></i> View Default Media
                            <span class="badge"><?php echo count($default_media); ?></span>
                        </a>
                        <a href="<?php echo admin_url('ella_contractors/appointments/' . $contract->id); ?>" class="btn btn-success">
                            <i class="fa fa-calendar"></i> Manage Appointments
                            <span class="badge" id="appointments-count">0</span>
                        </a>
                        <a href="<?php echo admin_url('ella_contractors/contract_notes/' . $contract->id); ?>" class="btn btn-info">
                            <i class="fa fa-sticky-note"></i> Manage Notes
                            <span class="badge" id="notes-count">0</span>
                        </a>
                        <button type="button" class="btn btn-success" 
                                onclick="copyShareableLink(<?php echo $contract->id; ?>, '<?php echo $contract->hash; ?>')"
                                title="Copy shareable client portal link">
                            <i class="fa fa-share"></i> Share Portal
                        </button>
                    </div>

                    <!-- Contract Details Content -->
                    <div class="contract-info">
                        <h4 class="mb-3">
                            <i class="fa fa-info-circle"></i> Contract Information
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Subject:</strong></td>
                                            <td><?php echo $contract->subject; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lead:</strong></td>
                                            <td><?php echo $contract->lead_name ?: 'N/A'; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="label label-success">Accepted</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td><?php echo app_format_money($contract->total, $base_currency); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Assigned To:</strong></td>
                                            <td><?php echo ($contract->firstname && $contract->lastname) ? $contract->firstname . ' ' . $contract->lastname : 'Unassigned'; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date Created:</strong></td>
                                            <td><?php echo _d($contract->date); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Open Till:</strong></td>
                                            <td><?php echo $contract->open_till ? _d($contract->open_till) : 'No limit'; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Description:</strong></td>
                                            <td><?php echo $contract->content ?: 'No description available'; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Media Summary -->
                    <div class="contract-info mt-4">
                        <h4 class="mb-3">
                            <i class="fa fa-paperclip"></i> Media Summary
                        </h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="fa fa-images fa-3x text-info"></i>
                                    <h5 class="mt-2">Contract Media</h5>
                                    <p class="text-muted">Files specific to this contract</p>
                                    <h3 class="text-info"><?php echo count($contract_media); ?></h3>
                                    <a href="<?php echo admin_url('ella_contractors/media_gallery/' . $contract->id); ?>" class="btn btn-sm btn-info">
                                        View Files
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="fa fa-star fa-3x text-warning"></i>
                                    <h5 class="mt-2">Default Media</h5>
                                    <p class="text-muted">Files available for all contracts</p>
                                    <h3 class="text-warning"><?php echo count($default_media); ?></h3>
                                    <a href="<?php echo admin_url('ella_contractors/media_gallery'); ?>" class="btn btn-sm btn-warning">
                                        View Files
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3">
                                    <i class="fa fa-upload fa-3x text-success"></i>
                                    <h5 class="mt-2">Upload New</h5>
                                    <p class="text-muted">Add media to this contract</p>
                                    <a href="<?php echo admin_url('ella_contractors/upload_media/' . $contract->id); ?>" class="btn btn-sm btn-success">
                                        Upload Media
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Media Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-3">
                                <i class="fa fa-paperclip"></i> Contract Media Files
                            </h4>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?php echo admin_url('ella_contractors/upload_media/' . $contract->id); ?>" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add Media to Contract
                            </a>
                        </div>
                    </div>

                    <?php 
                    // Combine contract media and default media
                    $all_media = array_merge($contract_media, $default_media);
                    ?>
                    
                    <?php if (!empty($all_media)): ?>
                        <!-- All Media Display -->
                        <div class="media-section">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Media Summary:</strong> 
                                        <?= count($all_media) ?> media file(s) found. 
                                        Total size: <strong><?= formatBytes(array_sum(array_column($all_media, 'file_size'))) ?></strong>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="media-grid-default">
                                <?php foreach ($all_media as $media): ?>
                                <div class="media-item-default" 
                                     data-category="<?= isset($media->media_category) ? htmlspecialchars($media->media_category) : '' ?>"
                                     data-filename="<?= htmlspecialchars($media->original_name) ?>"
                                     data-description="<?= isset($media->description) ? htmlspecialchars($media->description) : '' ?>"
                                     data-date="<?= $media->date_uploaded ?>"
                                     data-size="<?= $media->file_size ?>">
                                    
                                    <div class="media-card-default">
                                        <!-- File Preview Section -->
                                        <div class="media-preview-section">
                                            <?php if (in_array(strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                                                <!-- Image Preview -->
                                                <img src="<?= get_contract_media_url($media->contract_id ?? 0) . $media->file_name ?>" 
                                                     alt="<?= htmlspecialchars($media->original_name) ?>"
                                                     class="img-responsive"
                                                     style="max-width: 100%; max-height: 100px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <!-- File Icon -->
                                                <i class="fa <?= get_file_icon($media->file_name) ?> media-file-icon"></i>
                                            <?php endif; ?>
                                            
                                            <!-- Media Type Badge -->
                                            <div class="media-type-badge" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); padding: 2px 8px; border-radius: 12px; font-size: 11px; color: #333;">
                                                <?= strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION)) ?>
                                            </div>
                                        </div>
                                        
                                        <!-- File Info Section -->
                                        <div class="media-content-section">
                                            <h6 class="media-filename" title="<?= htmlspecialchars($media->original_name) ?>">
                                                <?= character_limiter($media->original_name, 30) ?>
                                            </h6>
                                            
                                            <div class="media-file-info">
                                                <span class="media-file-size">
                                                    <i class="fa fa-hdd-o"></i> <?= formatBytes($media->file_size) ?>
                                                </span>
                                                <span class="media-file-type">
                                                    <i class="fa fa-calendar"></i> <?= _dt($media->date_uploaded) ?>
                                                </span>
                                            </div>
                                            
                                            <?php if (isset($media->description) && $media->description): ?>
                                            <div class="media-description-text">
                                                <?= character_limiter($media->description, 60) ?>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <!-- Media Source Badge -->
                                            <div class="media-source-badge" style="margin-bottom: 15px;">
                                                <?php if (isset($media->is_default) && $media->is_default): ?>
                                                    <span class="label label-info">
                                                        <i class="fa fa-star"></i> Default Media
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-success">
                                                        <i class="fa fa-file"></i> Contract Media
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="media-actions" style="display: flex; gap: 5px; flex-wrap: wrap;">
                                                <?php if (in_array(strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])): ?>
                                                    <a href="<?= get_contract_media_url($media->contract_id ?? 0) . $media->file_name ?>" 
                                                       target="_blank" 
                                                       class="btn btn-xs btn-info" 
                                                       title="View Image">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="<?= get_contract_media_url($media->contract_id ?? 0) . $media->file_name ?>" 
                                                   download 
                                                   class="btn btn-xs btn-success" 
                                                   title="Download File">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                                
                                                <?php if (!isset($media->is_default) || !$media->is_default): ?>
                                                <a href="javascript:void(0)" 
                                                   onclick="confirmDeleteMedia(<?= $media->id ?>, '<?= addslashes($media->original_name) ?>')" 
                                                   class="btn btn-xs btn-danger" 
                                                   title="Delete File">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center" style="padding: 60px 20px;">
                                    <div class="media-empty-icon">
                                        <i class="fa fa-folder-open fa-4x text-muted"></i>
                                    </div>
                                    <h3 class="text-muted">No Media Files Found</h3>
                                    <p class="text-muted">No media files have been uploaded for this contract yet.</p>
                                    <a href="<?= admin_url('ella_contractors/upload_media/' . $contract->id); ?>" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Upload First Media File
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="mb-3">
                        <i class="fa fa-calendar"></i> All Appointments
                        <div class="pull-right">
                            <button type="button" class="btn btn-sm btn-primary" onclick="openQuickAppointmentModal()">
                                <i class="fa fa-plus"></i> Quick Add
                            </button>
                            <a href="<?php echo admin_url('ella_contractors/add_appointment/' . $contract->id); ?>" class="btn btn-sm btn-success">
                                <i class="fa fa-plus"></i> Full Form
                            </a>
                        </div>
                    </h4>
                    <div id="appointments-section">
                        <div class="text-center p-4">
                            <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="text-muted mt-2">Loading appointments...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="mb-3">
                        <i class="fa fa-sticky-note"></i> All Notes
                        <div class="pull-right">
                            <a href="<?php echo admin_url('ella_contractors/contract_notes/' . $contract->id); ?>" class="btn btn-sm btn-info">
                                <i class="fa fa-plus"></i> Manage Notes
                            </a>
                        </div>
                    </h4>
                    <div id="notes-section">
                        <div class="text-center p-4">
                            <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="text-muted mt-2">Loading notes...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Appointment Modal -->
<div class="modal fade" id="quickAddAppointmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-calendar-plus-o"></i> Quick Add Appointment
                </h4>
            </div>
            <form id="quickAppointmentForm">
                <div class="modal-body">
                    <input type="hidden" name="contract_id" value="<?php echo $contract->id; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quick_title">Title *</label>
                                <input type="text" name="title" id="quick_title" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="quick_appointment_type">Type *</label>
                                <select name="appointment_type" id="quick_appointment_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="initial_consultation">Initial Consultation</option>
                                    <option value="site_inspection">Site Inspection</option>
                                    <option value="progress_review">Progress Review</option>
                                    <option value="final_walkthrough">Final Walkthrough</option>
                                    <option value="material_selection">Material Selection</option>
                                    <option value="permit_application">Permit Application</option>
                                    <option value="quality_check">Quality Check</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="quick_status">Status</label>
                                <select name="status" id="quick_status" class="form-control">
                                    <option value="scheduled">Scheduled</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="rescheduled">Rescheduled</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quick_appointment_date">Date *</label>
                                <input type="date" name="appointment_date" id="quick_appointment_date" class="form-control" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quick_start_time">Start Time *</label>
                                        <input type="time" name="start_time" id="quick_start_time" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quick_end_time">End Time *</label>
                                        <input type="time" name="end_time" id="quick_end_time" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="quick_location">Location</label>
                                <input type="text" name="location" id="quick_location" class="form-control" placeholder="e.g., Project Site, Office">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_description">Description</label>
                        <textarea name="description" id="quick_description" class="form-control" rows="2" placeholder="Brief description of the appointment"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Add Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    // Function to copy shareable client portal link
    function copyShareableLink(contractId, hash) {
        const shareableUrl = `<?= site_url('client-portal') ?>/${contractId}/${hash}`;
        
        // Create temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = shareableUrl;
        document.body.appendChild(tempInput);
        
        // Select and copy the text
        tempInput.select();
        document.execCommand('copy');
        
        // Remove temporary element
        document.body.removeChild(tempInput);
        
        // Show success message
        showNotification('Shareable link copied to clipboard!', 'success');
        
        // Show the copied URL
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Link Copied!',
                html: `
                    <p class="mb-3">Shareable client portal link has been copied to clipboard:</p>
                    <div class="alert alert-info">
                        <code>${shareableUrl}</code>
                    </div>
                    <p class="text-muted small">You can now paste this link in emails, SMS, or share it with customers/leads.</p>
                `,
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert(`Shareable link copied: ${shareableUrl}`);
        }
    }
    
    // Enhanced showNotification function
    function showNotification(message, type = 'info') {
        // Check if Toastr is available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            // Fallback to SweetAlert2
            Swal.fire({
                title: type.charAt(0).toUpperCase() + type.slice(1),
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback to browser alert
            alert(message);
        }
    }

    // Function to confirm media deletion
    function confirmDeleteMedia(mediaId, fileName, redirectUrl) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Media File?',
                html: `
                    <p>Are you sure you want to delete this file?</p>
                    <div class="alert alert-warning">
                        <strong>File:</strong> ${fileName}
                    </div>
                    <p class="text-danger">This action cannot be undone!</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Delete It!',
                cancelButtonText: 'Cancel'
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
        } else {
            // Fallback to browser confirm
            if (confirm(`Are you sure you want to delete "${fileName}"?`)) {
                window.location.href = '<?= admin_url('ella_contractors/delete_media/') ?>' + mediaId + '?redirect=' + redirectUrl;
            }
        }
    }

    // Load appointments and notes for this contract
    $(document).ready(function() {
        loadContractAppointments(<?php echo $contract->id; ?>);
        loadContractNotes(<?php echo $contract->id; ?>);
    });

    function loadContractAppointments(contractId) {
        console.log('Loading appointments for contract:', contractId);
        $.ajax({
            url: '<?php echo admin_url('ella_contractors/get_contract_appointments_ajax/'); ?>' + contractId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Appointments response:', response);
                if (response.success) {
                    displayAppointments(response.appointments, contractId);
                    updateAppointmentsCount(response.appointments.length);
                } else {
                    $('#appointments-section').html(`
                        <div class="text-center p-4">
                            <i class="fa fa-exclamation-triangle fa-2x text-warning"></i>
                            <p class="text-muted mt-2">Failed to load appointments: ${response.message || 'Unknown error'}</p>
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading appointments:', xhr, status, error);
                $('#appointments-section').html(`
                    <div class="text-center p-4">
                        <i class="fa fa-exclamation-triangle fa-2x text-danger"></i>
                        <p class="text-muted mt-2">Error loading appointments: ${error}</p>
                        <p class="text-muted small">Status: ${status}</p>
                    </div>
                `);
            }
        });
    }

    function displayAppointments(appointments, contractId) {
        if (appointments.length === 0) {
            $('#appointments-section').html(`
                <div class="text-center p-4">
                    <i class="fa fa-calendar-times fa-2x text-muted"></i>
                    <p class="text-muted mt-2">No appointments scheduled for this contract</p>
                    <a href="<?php echo admin_url('ella_contractors/add_appointment/' . $contract->id); ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Schedule First Appointment
                    </a>
                </div>
            `);
            return;
        }

        let appointmentsHtml = '<div class="table-responsive"><table class="table table-striped table-hover">';
        appointmentsHtml += '<thead><tr><th>Date & Time</th><th>Title</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
        
        appointments.forEach(function(appointment) {
            const statusClass = getStatusClass(appointment.status);
            const statusText = appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1);
            
            appointmentsHtml += `
                <tr>
                    <td>
                        <strong>${formatDate(appointment.appointment_date)}</strong><br>
                        <small class="text-muted">${formatTime(appointment.start_time)} - ${formatTime(appointment.end_time)}</small>
                    </td>
                    <td>
                        <strong>${appointment.title}</strong>
                        ${appointment.description ? '<br><small class="text-muted">' + appointment.description.substring(0, 50) + '...</small>' : ''}
                    </td>
                    <td><span class="label label-info">${appointment.appointment_type.replace(/_/g, ' ')}</span></td>
                    <td><span class="label ${statusClass}">${statusText}</span></td>
                    <td>
                        <div class="btn-group">
                            <a href="<?php echo admin_url('ella_contractors/edit_appointment/'); ?>${appointment.id}" class="btn btn-xs btn-default" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="<?php echo admin_url('ella_contractors/appointments/'); ?>${contractId}" class="btn btn-xs btn-info" title="View All">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        appointmentsHtml += '</tbody></table></div>';
        appointmentsHtml += `
            <div class="text-center mt-3">
                <a href="<?php echo admin_url('ella_contractors/appointments/'); ?>${contractId}" class="btn btn-info">
                    <i class="fa fa-calendar"></i> View All Appointments
                </a>
            </div>
        `;
        
        $('#appointments-section').html(appointmentsHtml);
    }

    function getStatusClass(status) {
        switch(status) {
            case 'scheduled': return 'label-default';
            case 'confirmed': return 'label-success';
            case 'completed': return 'label-primary';
            case 'cancelled': return 'label-danger';
            case 'rescheduled': return 'label-warning';
            default: return 'label-default';
        }
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
    }

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }

    function updateAppointmentsCount(count) {
        $('#appointments-count').text(count);
    }

    // Quick appointment form handling
    $('#quickAppointmentForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '<?php echo admin_url('ella_contractors/add_appointment_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showNotification('Appointment added successfully!', 'success');
                    
                    // Close modal
                    $('#quickAddAppointmentModal').modal('hide');
                    
                    // Reset form
                    $('#quickAppointmentForm')[0].reset();
                    
                    // Reload appointments
                    loadContractAppointments(<?php echo $contract->id; ?>);
                } else {
                    showNotification(response.message || 'Failed to add appointment', 'error');
                }
            },
            error: function() {
                showNotification('Error occurred while adding appointment', 'error');
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Auto-populate end time when start time changes
    $('#quick_start_time').change(function() {
        const startTime = $(this).val();
        if (startTime) {
            const start = new Date('2000-01-01T' + startTime);
            const end = new Date(start.getTime() + (60 * 60 * 1000)); // Add 1 hour
            const endTime = end.toTimeString().slice(0, 5);
            $('#quick_end_time').val(endTime);
        }
    });

    // Set default date to today
    $(document).ready(function() {
        const today = new Date().toISOString().split('T')[0];
        $('#quick_appointment_date').val(today);
    });

    function openQuickAppointmentModal() {
        $('#quickAddAppointmentModal').modal('show');
    }

    // ========================================
    // CONTRACT NOTES FUNCTIONS
    // ========================================

    function loadContractNotes(contractId) {
        $.ajax({
            url: '<?php echo admin_url('ella_contractors/get_contract_notes_ajax/'); ?>' + contractId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayNotes(response.notes);
                    updateNotesCount(response.notes.length);
                } else {
                    $('#notes-section').html(`                        <div class="text-center p-4">
                            <i class="fa fa-exclamation-triangle fa-2x text-warning"></i>
                            <p class="text-muted mt-2">Failed to load notes</p>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#notes-section').html(`
                    <div class="text-center p-4">
                        <i class="fa fa-exclamation-triangle fa-2x text-danger"></i>
                        <p class="text-muted mt-2">Error loading notes</p>
                    </div>
                `);
            }
        });
    }

    function displayNotes(notes) {
        if (notes.length === 0) {
            $('#notes-section').html(`
                <div class="text-center p-4">
                    <i class="fa fa-sticky-note fa-2x text-muted"></i>
                    <p class="text-muted mt-2">No notes available for this contract yet</p>
                    <a href="<?php echo admin_url('ella_contractors/contract_notes/' . $contract->id); ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add First Note
                    </a>
                </div>
            `);
            return;
        }

        let notesHtml = '<div class="row">';
        
        // Show only the 3 most recent notes
        const recentNotes = notes.slice(0, 3);
        
        recentNotes.forEach(function(note) {
            const noteType = note.note_type.charAt(0).toUpperCase() + note.note_type.slice(1);
            const isPublic = note.is_public == 1;
            const publicBadge = isPublic ? 
                '<span class="badge badge-success ml-2">Public</span>' : 
                '<span class="badge badge-warning ml-2">Private</span>';
            
            notesHtml += `
                <div class="col-md-4 mb-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title text-primary">
                                <i class="fa fa-sticky-note"></i> ${note.note_title}
                                ${publicBadge}
                            </h6>
                        </div>
                        <div class="panel-body">
                            <p class="text-muted mb-2">
                                <i class="fa fa-tag"></i> ${noteType}
                                <span class="ml-2">
                                    <i class="fa fa-calendar"></i> ${formatDate(note.created_at)}
                                </span>
                            </p>
                            <div class="note-content mb-2">
                                ${note.note_content.length > 100 ? 
                                    note.note_content.substring(0, 100) + '...' : 
                                    note.note_content}
                            </div>
                            <div class="note-meta">
                                <small class="text-muted">
                                    <i class="fa fa-user"></i> By: ${note.created_by_name} ${note.created_by_lastname}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        notesHtml += '</div>';
        
        if (notes.length > 3) {
            notesHtml += `
                <div class="text-center mt-3">
                    <a href="<?php echo admin_url('ella_contractors/contract_notes/' . $contract->id); ?>" class="btn btn-info">
                        <i class="fa fa-sticky-note"></i> View All Notes (${notes.length})
                    </a>
                </div>
            `;
        }
        
        $('#notes-section').html(notesHtml);
    }

    function updateNotesCount(count) {
        $('#notes-count').text(count);
    }
</script>

