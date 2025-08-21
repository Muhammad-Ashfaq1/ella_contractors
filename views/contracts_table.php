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

                        <!-- Contracts Table -->
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
                                                                <a href="<?= admin_url('ella_contractors/contracts/view/' . $proposal->id) ?>">
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
</div>

<script>
$(document).ready(function() {
    <?php if (!empty($accepted_proposals)): ?>
    // Initialize DataTable for better functionality
    $('#contracts-table').DataTable({
        "pageLength": 25,
        "order": [[ 6, "desc" ]], // Sort by date created descending
        "columnDefs": [
            { "orderable": false, "targets": [9] } // Disable sorting on Actions column
        ],
        "language": {
            "search": "Search contracts:",
            "lengthMenu": "Show _MENU_ contracts per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ contracts",
            "emptyTable": "No accepted proposals found"
        }
    });
    <?php endif; ?>
});
</script>

<?php init_tail(); ?>
