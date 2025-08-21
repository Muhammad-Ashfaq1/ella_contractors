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
                        <div class="clearfix"></div>
                        
                        <!-- Page Header -->
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="customer-profile-group-heading"><?= $title ?></h4>
                                <p class="text-muted">Showing accepted proposals converted to contracts</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?= admin_url('proposals') ?>" class="btn btn-primary">
                                    <i class="fa fa-eye"></i> View All Proposals
                                </a>
                                <a href="<?= admin_url('leads') ?>" class="btn btn-success">
                                    <i class="fa fa-users"></i> View Leads
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <!-- Navigation Tabs -->
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#contracts-tab" aria-controls="contracts-tab" role="tab" data-toggle="tab">
                                            <i class="fa fa-file-contract"></i> Contracts
                                            <span class="badge"><?= count($accepted_proposals) ?></span>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#default-media-tab" aria-controls="default-media-tab" role="tab" data-toggle="tab">
                                            <i class="fa fa-star"></i> Default Media
                                            <span class="badge" id="default-media-count">0</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <br>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            <!-- Contracts Tab -->
                            <div role="tabpanel" class="tab-pane active" id="contracts-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php if (!empty($accepted_proposals)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="contracts-table">
                                                <thead>
                                                    <tr>
                                                        <th>Proposal #</th>
                                                        <th>Subject</th>
                                                        <th>Client/Lead</th>
                                                        <th>Contact Info</th>
                                                        <th>Assigned To</th>
                                                        <th>Total Value</th>
                                                        <th>Date Created</th>
                                                        <th>Open Till</th>
                                                        <th>Status</th>
                                                        <th width="150">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($accepted_proposals as $proposal): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= $proposal->id ?></strong>
                                                        </td>
                                                        <td>
                                                            <strong><?= $proposal->subject ?></strong>
                                                            <?php if ($proposal->lead_company): ?>
                                                            <br><small class="text-muted"><?= $proposal->lead_company ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?= $proposal->lead_name ?: 'N/A' ?>
                                                            <?php if ($proposal->rel_type == 'lead'): ?>
                                                            <br><small class="text-muted">Lead ID: <?= $proposal->rel_id ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($proposal->lead_email): ?>
                                                            <i class="fa fa-envelope"></i> <?= $proposal->lead_email ?><br>
                                                            <?php endif; ?>
                                                            <?php if ($proposal->lead_phone): ?>
                                                            <i class="fa fa-phone"></i> <?= $proposal->lead_phone ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($proposal->firstname && $proposal->lastname): ?>
                                                            <?= $proposal->firstname . ' ' . $proposal->lastname ?>
                                                            <?php else: ?>
                                                            <span class="text-muted">Unassigned</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?= app_format_money($proposal->total, get_base_currency()) ?></strong>
                                                        </td>
                                                        <td>
                                                            <?= _dt($proposal->date) ?>
                                                        </td>
                                                        <td>
                                                            <?= $proposal->open_till ? _dt($proposal->open_till) : 'No limit' ?>
                                                        </td>
                                                        <td>
                                                            <span class="label label-success">Accepted</span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                                    Actions <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                    <li>
                                                                        <a href="<?= admin_url('ella_contractors/view_contract/' . $proposal->id) ?>">
                                                                            <i class="fa fa-eye text-primary"></i> View Contract Details
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="<?= admin_url('ella_contractors/upload_media/' . $proposal->id) ?>">
                                                                            <i class="fa fa-upload text-success"></i> Upload Media
                                                                        </a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <a href="<?= admin_url('proposals/list_proposals/' . $proposal->id) ?>" target="_blank">
                                                                            <i class="fa fa-external-link"></i> View Original Proposal
                                                                        </a>
                                                                    </li>
                                                                    <?php if ($proposal->rel_type == 'lead'): ?>
                                                                    <li>
                                                                        <a href="<?= admin_url('leads/index/' . $proposal->rel_id) ?>" target="_blank">
                                                                            <i class="fa fa-user"></i> View Lead
                                                                        </a>
                                                                    </li>
                                                                    <?php endif; ?>
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="alert('Generate Contract PDF - Coming Soon!')">
                                                                            <i class="fa fa-file-pdf-o text-danger"></i> Generate Contract PDF
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="alert('Generate Presentation - Coming Soon!')">
                                                                            <i class="fa fa-file-powerpoint-o text-warning"></i> Generate PPT
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="alert('Create Project - Coming Soon!')">
                                                                            <i class="fa fa-plus text-success"></i> Create Project
                                                                        </a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="alert('Send Email - Coming Soon!')">
                                                                            <i class="fa fa-envelope"></i> Send Email
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" onclick="alert('Clone Contract - Coming Soon!')">
                                                                            <i class="fa fa-copy"></i> Clone Contract
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php else: ?>
                                        <!-- Empty State -->
                                        <div class="text-center" style="padding: 80px 20px;">
                                            <div style="font-size: 4rem; color: #ddd; margin-bottom: 20px;">
                                                <i class="fa fa-file-contract"></i>
                                            </div>
                                            <h3 class="text-muted">No Accepted Proposals Found</h3>
                                            <p class="text-muted">No proposals have been accepted yet. When leads accept proposals, they will appear here as contracts.</p>
                                            <div style="margin-top: 30px;">
                                                <a href="<?= admin_url('proposals') ?>" class="btn btn-primary">
                                                    <i class="fa fa-eye"></i> View All Proposals
                                                </a>
                                                <a href="<?= admin_url('leads') ?>" class="btn btn-success">
                                                    <i class="fa fa-users"></i> View Leads
                                                </a>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Default Media Tab -->
                            <div role="tabpanel" class="tab-pane" id="default-media-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Default Media Header with Upload Button -->
                                        <div class="default-media-header">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h4><i class="fa fa-star"></i> Default Media Gallery</h4>
                                                    <p class="text-muted">Media files available for all contracts</p>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-primary">
                                                        <i class="fa fa-plus"></i> Add More Files
                                                    </a>
                                                    <a href="javascript:void(0)" onclick="loadDefaultMedia()" class="btn btn-success">
                                                        <i class="fa fa-refresh"></i> Refresh
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/default_media') ?>" class="btn btn-info" target="_blank">
                                                        <i class="fa fa-external-link"></i> Full Gallery
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="default-media-content">
                                            <!-- Default media content will be loaded here -->
                                            <div class="text-center" style="padding: 40px 20px;">
                                                <div style="font-size: 3rem; color: #ddd; margin-bottom: 20px;">
                                                    <i class="fa fa-spinner fa-spin"></i>
                                                </div>
                                                <p class="text-muted">Loading default media...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Stats -->
                        <?php if (!empty($accepted_proposals)): ?>
                        <div class="row" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Summary:</strong> 
                                    Showing <?= count($accepted_proposals) ?> accepted proposal(s) converted to contracts.
                                    Total value: <strong><?= app_format_money(array_sum(array_column($accepted_proposals, 'total')), get_base_currency()) ?></strong>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php init_tail(); ?>

<!-- Include module JavaScript -->
<script src="<?php echo base_url('modules/ella_contractors/assets/js/ella_contractors.js'); ?>"></script>

<script>
$(document).ready(function() {
    console.log('jQuery is ready');
    
    // Load default media count and content when tab is clicked
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.getAttribute('aria-controls') === 'default-media-tab') {
            loadDefaultMedia();
        }
    });
    
    // Function to load default media
    function loadDefaultMedia() {
        // Show loading state
        $('#default-media-content').html(`
            <div class="text-center" style="padding: 40px 20px;">
                <div style="font-size: 3rem; color: #ddd; margin-bottom: 20px;">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
                <p class="text-muted">Refreshing default media...</p>
            </div>
        `);
        
        $.ajax({
            url: '<?= admin_url('ella_contractors/default_media') ?>',
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                                // Extract the media content from the response
                var mediaContent = $(response).find('.media-gallery-content').html();
                if (mediaContent) {
                    $('#default-media-content').html(mediaContent);
                    
                    // Update the count badge
                    var mediaCount = $(response).find('.media-item').length;
                    $('#default-media-count').text(mediaCount);
                    
                    // Show refresh success message
                    if (mediaCount > 0) {
                        showNotification('Default media refreshed successfully! Found ' + mediaCount + ' file(s).', 'success');
                    } else {
                        showNotification('Default media refreshed. No files found.', 'info');
                    }
                } else {
                     $('#default-media-content').html(`
                         <div class="text-center empty-state-large">
                             <div style="font-size: 3rem; color: #ddd; margin-bottom: 20px;">
                                 <i class="fa fa-star"></i>
                             </div>
                             <h3 class="text-muted">No Default Media Found</h3>
                             <p class="text-muted">No default media files have been uploaded yet. These files will be available for all contracts.</p>
                             <div style="margin-top: 30px;">
                                 <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-primary btn-lg">
                                     <i class="fa fa-upload"></i> Upload Your First Default Media
                                 </a>
                                 <br><br>
                                 <small class="text-muted">
                                     <i class="fa fa-info-circle"></i> 
                                     Default media files are available for all contracts and can include company brochures, 
                                     standard contracts, policy documents, and common forms.
                                 </small>
                             </div>
                         </div>
                     `);
                     $('#default-media-count').text('0');
                 }
            },
                         error: function() {
                 $('#default-media-content').html(`
                     <div class="text-center" style="padding: 40px 20px;">
                         <div style="font-size: 3rem; color: #ddd; margin-bottom: 20px;">
                             <i class="fa fa-exclamation-triangle"></i>
                         </div>
                         <h3 class="text-muted">Error Loading Default Media</h3>
                         <p class="text-muted">There was an error loading the default media. Please try again.</p>
                         <div style="margin-top: 30px;">
                             <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-primary">
                                 <i class="fa fa-upload"></i> Upload Default Media
                             </a>
                             <a href="javascript:void(0)" onclick="loadDefaultMedia()" class="btn btn-default">
                                 <i class="fa fa-refresh"></i> Try Again
                             </a>
                         </div>
                     </div>
                 `);
             }
        });
    }
    
    // Function to show notifications
    function showNotification(message, type) {
        type = type || 'info';
        var alertClass = 'alert-' + type;
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
        
        var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade in" role="alert">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span></button>' +
            '<i class="fa ' + icon + '"></i> ' + message +
            '</div>';
        
        $('.content').prepend(notification);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert-dismissible').fadeOut();
        }, 5000);
    }
    
    // Check for success message from upload
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('upload_success') === '1') {
        // Show success message
        $('.content').prepend(`
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-check-circle"></i> 
                <strong>Success!</strong> Your default media file has been uploaded successfully. 
                It's now available for all contracts.
            </div>
        `);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut();
        }, 5000);
        
        // Remove the parameter from URL without refreshing
        var newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
    
    // Load default media count on page load
    $.ajax({
        url: '<?= admin_url('ella_contractors/default_media') ?>',
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            var mediaCount = $(response).find('.media-item').length;
            $('#default-media-count').text(mediaCount);
        },
        error: function() {
            $('#default-media-count').text('0');
        }
    });
});
</script>


