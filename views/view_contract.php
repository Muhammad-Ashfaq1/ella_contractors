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
                                <p class="text-muted">Contract details and media gallery</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Contracts
                                </a>
                                <a href="<?= admin_url('ella_contractors/upload_media/' . $contract->id) ?>" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> Upload Media
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <!-- Contract Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="panel_s">
                                    <div class="panel-heading">
                                        <h5>Contract Information</h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td><strong>Contract ID:</strong></td>
                                                        <td><?= $contract->id ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Subject:</strong></td>
                                                        <td><?= $contract->subject ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Total Value:</strong></td>
                                                        <td><strong class="text-success"><?= app_format_money($contract->total, get_base_currency()) ?></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Status:</strong></td>
                                                        <td><span class="label label-success">Accepted</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Date Created:</strong></td>
                                                        <td><?= _dt($contract->date) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Open Till:</strong></td>
                                                        <td><?= $contract->open_till ? _dt($contract->open_till) : 'No limit' ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <td><strong>Client/Lead:</strong></td>
                                                        <td><?= $contract->lead_name ?: 'N/A' ?></td>
                                                    </tr>
                                                    <?php if ($contract->lead_company): ?>
                                                    <tr>
                                                        <td><strong>Company:</strong></td>
                                                        <td><?= $contract->lead_company ?></td>
                                                    </tr>
                                                    <?php endif; ?>
                                                    <tr>
                                                        <td><strong>Email:</strong></td>
                                                        <td><?= $contract->lead_email ?: 'N/A' ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Phone:</strong></td>
                                                        <td><?= $contract->lead_phone ?: 'N/A' ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Assigned To:</strong></td>
                                                        <td><?= ($contract->firstname && $contract->lastname) ? $contract->firstname . ' ' . $contract->lastname : 'Unassigned' ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Lead ID:</strong></td>
                                                        <td>
                                                            <a href="<?= admin_url('leads/index/' . $contract->rel_id) ?>" target="_blank">
                                                                <?= $contract->rel_id ?>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        <?php if ($contract->proposal_content): ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5>Proposal Content</h5>
                                                <div class="well">
                                                    <?= $contract->proposal_content ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="panel_s">
                                    <div class="panel-heading">
                                        <h5>Quick Actions</h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="list-group">
                                            <a href="<?= admin_url('proposals/list_proposals/' . $contract->id) ?>" target="_blank" class="list-group-item">
                                                <i class="fa fa-external-link"></i> View Original Proposal
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/upload_media/' . $contract->id) ?>" class="list-group-item">
                                                <i class="fa fa-upload text-success"></i> Upload New Media
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/media_gallery/' . $contract->id) ?>" class="list-group-item">
                                                <i class="fa fa-images text-info"></i> View All Media
                                            </a>
                                            <a href="javascript:void(0)" onclick="alert('Generate PDF - Coming Soon!')" class="list-group-item">
                                                <i class="fa fa-file-pdf-o text-danger"></i> Generate Contract PDF
                                            </a>
                                            <a href="javascript:void(0)" onclick="alert('Send Email - Coming Soon!')" class="list-group-item">
                                                <i class="fa fa-envelope text-primary"></i> Send Email to Client
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Gallery Section -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h5>Media Gallery</h5>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <a href="<?= admin_url('ella_contractors/upload_media/' . $contract->id) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-upload"></i> Upload Media
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php if (!empty($media_files)): ?>
                                        <div class="row">
                                            <?php foreach ($media_files as $media): ?>
                                            <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 20px;">
                                                <div class="media-item" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; height: 200px; display: flex; flex-direction: column; justify-content: space-between;">
                                                    <div>
                                                        <div style="font-size: 3rem; margin-bottom: 10px;">
                                                            <i class="fa <?= get_file_icon($media->file_type) ?>"></i>
                                                        </div>
                                                        <h6 style="margin-bottom: 5px; word-wrap: break-word;"><?= character_limiter($media->original_name, 20) ?></h6>
                                                        <small class="text-muted"><?= format_file_size($media->file_size) ?></small><br>
                                                        <?php if ($media->is_default): ?>
                                                        <span class="label label-info">Default</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" style="margin-top: 10px;">
                                                        <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" target="_blank" class="btn btn-primary btn-xs">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="<?= get_contract_media_url($media->contract_id) . $media->file_name ?>" download class="btn btn-success btn-xs">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="<?= admin_url('ella_contractors/delete_media/' . $media->id . '?redirect=' . urlencode(current_url())) ?>" 
                                                           onclick="return confirm('Are you sure you want to delete this file?')" class="btn btn-danger btn-xs">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="text-center" style="padding: 40px;">
                                            <div style="font-size: 3rem; color: #ddd; margin-bottom: 20px;">
                                                <i class="fa fa-folder-open"></i>
                                            </div>
                                            <h4 class="text-muted">No Media Files Found</h4>
                                            <p class="text-muted">Upload some media files to get started.</p>
                                            <a href="<?= admin_url('ella_contractors/upload_media/' . $contract->id) ?>" class="btn btn-primary">
                                                <i class="fa fa-upload"></i> Upload First Media
                                            </a>
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
    </div>
</div>
<?php init_tail(); ?>
