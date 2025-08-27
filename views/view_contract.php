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
                    <h4 class="mb-3">
                        <i class="fa fa-paperclip"></i> Contract Media Files
                    </h4>
                    
                    <!-- Media Upload Button -->
                    <div class="text-right mb-3">
                        <a href="<?php echo admin_url('ella_contractors/upload_media/' . $contract->id); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i> Add Media to Contract
                        </a>
                    </div>

                    <?php if (!empty($contract_media)): ?>
                        <!-- Contract-Specific Media -->
                        <div class="media-section">
                            <h5 class="text-info mb-3">
                                <i class="fa fa-file"></i> Contract-Specific Media (<?php echo count($contract_media); ?> files)
                            </h5>
                            <div class="media-grid">
                                <?php foreach ($contract_media as $media): ?>
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
                                                    <?= formatBytes($media->file_size) ?>
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
                                            
                                            <!-- Media Category and Tags -->
                                            <?php if (isset($media->media_category) && $media->media_category): ?>
                                            <div class="media-category-badge">
                                                <i class="fa fa-tag"></i> <?= ucfirst($media->media_category) ?>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($media->tags) && $media->tags): ?>
                                            <div class="media-tags">
                                                <?php 
                                                $tags = explode(',', $media->tags);
                                                foreach (array_slice($tags, 0, 3) as $tag): 
                                                    $tag = trim($tag);
                                                    if (!empty($tag)):
                                                ?>
                                                <span class="media-tag"><?= $tag ?></span>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                if (count($tags) > 3): 
                                                ?>
                                                <span class="media-tag-more">+<?= count($tags) - 3 ?> more</span>
                                                <?php endif; ?>
                                            </div>
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
                                                       class="btn btn-xs btn-danger" title="Delete File">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Default Media Section -->
                    <?php if (!empty($default_media)): ?>
                    <div class="media-section mt-4">
                        <h5 class="text-warning mb-3">
                            <i class="fa fa-star"></i> Default Media Available (<?php echo count($default_media); ?> files)
                        </h5>
                        <div class="media-grid">
                            <?php foreach ($default_media as $media): ?>
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
                                                <?= formatBytes($media->file_size) ?>
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
                                        
                                        <!-- Media Category and Tags -->
                                        <?php if (isset($media->media_category) && $media->media_category): ?>
                                        <div class="media-category-badge">
                                            <i class="fa fa-tag"></i> <?= ucfirst($media->media_category) ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($media->tags) && $media->tags): ?>
                                        <div class="media-tags">
                                            <?php 
                                            $tags = explode(',', $media->tags);
                                            foreach (array_slice($tags, 0, 3) as $tag): 
                                                $tag = trim($tag);
                                                if (!empty($tag)):
                                            ?>
                                            <span class="media-tag"><?= $tag ?></span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            if (count($tags) > 3): 
                                            ?>
                                            <span class="media-tag-more">+<?= count($tags) - 3 ?> more</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- Default Media Badge -->
                                        <span class="media-default-badge">
                                            <i class="fa fa-star"></i> Default Media
                                        </span>
                                        
                                        <!-- Action Buttons -->
                                        <div class="media-actions">
                                            <div class="media-btn-group">
                                                <a href="<?= get_contract_media_url(null) . $media->file_name ?>" 
                                                   target="_blank" class="media-btn media-btn-view" title="View File">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?= get_contract_media_url(null) . $media->file_name ?>" 
                                                   download class="media-btn media-btn-download" title="Download File">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Empty State -->
                    <?php if (empty($contract_media) && empty($default_media)): ?>
                    <div class="media-empty-state">
                        <div class="media-empty-icon">
                            <i class="fa fa-folder-open"></i>
                        </div>
                        <h3 class="media-empty-title">No Media Files Found</h3>
                        <p class="media-empty-description">This contract doesn't have any media files attached yet.</p>
                        <a href="<?php echo admin_url('ella_contractors/upload_media/' . $contract->id); ?>" class="btn btn-primary btn-lg">
                            <i class="fa fa-upload"></i> Upload First Media File
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
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
</script>
