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
</script>
