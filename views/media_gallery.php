<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
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
                                <p class="text-muted">Media files for this specific contract</p>
                                <?php else: ?>
                                <p class="text-muted">Default media files available for all contracts</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($contract_id): ?>
                                <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract_id) ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Contract
                                </a>
                                <a href="<?= admin_url('ella_contractors/upload_media/' . $contract_id) ?>" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> Upload Media
                                </a>
                                <?php else: ?>
                                <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Contracts
                                </a>
                                <a href="<?= admin_url('ella_contractors/upload_media') ?>" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> Upload Default Media
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <!-- Media Gallery -->
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (!empty($media_files)): ?>
                                <div class="row">
                                    <?php foreach ($media_files as $media): ?>
                                    <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 25px;">
                                        <div class="media-card" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                                            
                                            <!-- File Icon/Preview -->
                                            <div style="background: #f8f9fa; padding: 30px; text-align: center; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                                <div style="font-size: 4rem;">
                                                    <i class="fa <?= get_file_icon($media->file_type) ?>"></i>
                                                </div>
                                            </div>
                                            
                                            <!-- File Info -->
                                            <div style="padding: 15px;">
                                                <h6 style="margin-bottom: 8px; font-weight: bold; word-wrap: break-word;">
                                                    <?= character_limiter($media->original_name, 25) ?>
                                                </h6>
                                                
                                                <div style="margin-bottom: 10px;">
                                                    <small class="text-muted">
                                                        <i class="fa fa-hdd-o"></i> <?= format_file_size($media->file_size) ?>
                                                    </small><br>
                                                    <small class="text-muted">
                                                        <i class="fa fa-calendar"></i> <?= _dt($media->date_uploaded) ?>
                                                    </small>
                                                </div>
                                                
                                                <?php if ($media->description): ?>
                                                <p style="font-size: 12px; color: #666; margin-bottom: 10px;">
                                                    <?= character_limiter($media->description, 50) ?>
                                                </p>
                                                <?php endif; ?>
                                                
                                                <?php if ($media->is_default): ?>
                                                <span class="label label-info" style="margin-bottom: 10px; display: inline-block;">
                                                    <i class="fa fa-star"></i> Default
                                                </span>
                                                <?php endif; ?>
                                                
                                                <!-- Action Buttons -->
                                                <div class="btn-group btn-group-justified">
                                                    <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                       target="_blank" class="btn btn-primary btn-sm" title="View File">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" 
                                                       download class="btn btn-success btn-sm" title="Download File">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/delete_media/' . $media->id . '?redirect=' . urlencode(current_url())) ?>" 
                                                       onclick="return confirm('Are you sure you want to delete this file?')" 
                                                       class="btn btn-danger btn-sm" title="Delete File">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Summary -->
                                <div class="row" style="margin-top: 30px;">
                                    <div class="col-md-12">
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
                                </div>
                                
                                <?php else: ?>
                                <!-- Empty State -->
                                <div class="text-center" style="padding: 80px 20px;">
                                    <div style="font-size: 5rem; color: #ddd; margin-bottom: 30px;">
                                        <i class="fa fa-folder-open"></i>
                                    </div>
                                    <h3 class="text-muted">No Media Files Found</h3>
                                    <?php if ($contract_id): ?>
                                    <p class="text-muted">No media files have been uploaded for this contract yet.</p>
                                    <a href="<?= admin_url('ella_contractors/upload_media/' . $contract_id) ?>" class="btn btn-primary btn-lg">
                                        <i class="fa fa-upload"></i> Upload First Media File
                                    </a>
                                    <?php else: ?>
                                    <p class="text-muted">No default media files have been uploaded yet.</p>
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
</div>

<style>
.media-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    transform: translateY(-2px);
}

.btn-group-justified .btn {
    border-radius: 0;
}

.btn-group-justified .btn:first-child {
    border-radius: 4px 0 0 4px;
}

.btn-group-justified .btn:last-child {
    border-radius: 0 4px 4px 0;
}
</style>

<?php init_tail(); ?>
