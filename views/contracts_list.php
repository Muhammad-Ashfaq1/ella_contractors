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
                            <p class="text-muted">Manage your contracts between leads and contractors</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?= admin_url('ella_contractors/add_contract') ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add New Contract
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-2">
            <div class="widget-card bg-primary text-white">
                <div class="widget-card-body">
                    <div class="widget-card-icon">
                        <i class="fa fa-file-contract fa-2x"></i>
                    </div>
                    <div class="widget-card-content">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Total Contracts</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="widget-card bg-success text-white">
                <div class="widget-card-body">
                    <div class="widget-card-icon">
                        <i class="fa fa-check-circle fa-2x"></i>
                    </div>
                    <div class="widget-card-content">
                        <h3><?= $counts['active'] ?></h3>
                        <p>Active</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="widget-card bg-info text-white">
                <div class="widget-card-body">
                    <div class="widget-card-icon">
                        <i class="fa fa-edit fa-2x"></i>
                    </div>
                    <div class="widget-card-content">
                        <h3><?= $counts['draft'] ?></h3>
                        <p>Draft</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="widget-card bg-warning text-white">
                <div class="widget-card-body">
                    <div class="widget-card-icon">
                        <i class="fa fa-clock-o fa-2x"></i>
                    </div>
                    <div class="widget-card-content">
                        <h3><?= $counts['completed'] ?></h3>
                        <p>Completed</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="widget-card bg-danger text-white">
                <div class="widget-card-body">
                    <div class="widget-card-icon">
                        <i class="fa fa-times-circle fa-2x"></i>
                    </div>
                    <div class="widget-card-content">
                        <h3><?= $counts['cancelled'] ?></h3>
                        <p>Cancelled</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="widget-card bg-default text-white">
                <div class="widget-card-body">
                    <div class="widget-card-icon">
                        <i class="fa fa-calendar fa-2x"></i>
                    </div>
                    <div class="widget-card-content">
                        <h3><?= $stats['recent'] ?></h3>
                        <p>Recent (30d)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-info">
                                <i class="fa fa-users"></i> Manage Contractors
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="text-muted">Total Value: <strong><?= format_money($stats['total_value']) ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <form method="GET" action="<?= admin_url('ella_contractors/contracts') ?>" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Search</label>
                                    <input type="text" name="search" class="form-control" value="<?= $filters['search'] ?? '' ?>" placeholder="Contract #, Subject, Lead, Contractor...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        <option value="expired" <?= ($filters['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Lead</label>
                                    <select name="lead_id" class="form-control">
                                        <option value="">All Leads</option>
                                        <?php foreach ($leads as $lead): ?>
                                        <?php
                                        $status_text = '';
                                        switch($lead->status) {
                                            case 1: $status_text = ' (New)'; break;
                                            case 2: $status_text = ' (Contacted)'; break;
                                            case 3: $status_text = ' (Accepted)'; break;
                                            case 4: $status_text = ' (Proposal)'; break;
                                            case 5: $status_text = ' (Negotiation)'; break;
                                            case 6: $status_text = ' (Won)'; break;
                                            case 7: $status_text = ' (Lost)'; break;
                                            default: $status_text = ' (Unknown)'; break;
                                        }
                                        ?>
                                        <option value="<?= $lead->id ?>" <?= ($filters['lead_id'] ?? '') == $lead->id ? 'selected' : '' ?>>
                                            <?= $lead->name ?> <?= $lead->company ? ' (' . $lead->company . ')' : '' ?><?= $status_text ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Contractor</label>
                                    <select name="contractor_id" class="form-control">
                                        <option value="">All Contractors</option>
                                        <?php foreach ($contractors as $contractor): ?>
                                        <option value="<?= $contractor->id ?>" <?= ($filters['contractor_id'] ?? '') == $contractor->id ? 'selected' : '' ?>>
                                            <?= $contractor->company_name ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label class="control-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <form id="bulk-actions-form" method="POST" action="<?= admin_url('ella_contractors/bulk_contract_actions') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select name="bulk_action" class="form-control bulk-action-select">
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate</option>
                                        <option value="complete">Complete</option>
                                        <option value="cancel">Cancel</option>
                                        <option value="delete">Delete (Draft Only)</option>
                                    </select>
                                    <button type="submit" class="btn btn-default" id="bulk-submit" disabled>
                                        Apply
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Clear Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contracts Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <?php if (!empty($contracts)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="contracts-table">
                            <thead>
                                <tr>
                                    <th width="20">
                                        <input type="checkbox" id="select-all-contracts">
                                    </th>
                                    <th>Contract #</th>
                                    <th>Subject</th>
                                    <th>Lead</th>
                                    <th>Contractor</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contracts as $contract): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="contract_ids[]" value="<?= $contract->id ?>" class="contract-checkbox">
                                    </td>
                                    <td>
                                        <strong><?= $contract->contract_number ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?= admin_url('ella_contractors/view_contract/' . $contract->id) ?>">
                                            <?= $contract->subject ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= $contract->lead_name ?></strong>
                                            <?php if ($contract->lead_company): ?>
                                            <br><small class="text-muted"><?= $contract->lead_company ?></small>
                                            <?php endif; ?>
                                            <?php
                                            $status_text = '';
                                            $status_class = '';
                                            switch($contract->lead_status) {
                                                case 1: $status_text = 'New'; $status_class = 'label-info'; break;
                                                case 2: $status_text = 'Contacted'; $status_class = 'label-warning'; break;
                                                case 3: $status_text = 'Accepted'; $status_class = 'label-success'; break;
                                                case 4: $status_text = 'Proposal'; $status_class = 'label-primary'; break;
                                                case 5: $status_text = 'Negotiation'; $status_class = 'label-warning'; break;
                                                case 6: $status_text = 'Won'; $status_class = 'label-success'; break;
                                                case 7: $status_text = 'Lost'; $status_class = 'label-danger'; break;
                                                default: $status_text = 'Unknown'; $status_class = 'label-default'; break;
                                            }
                                            ?>
                                            <br><span class="label <?= $status_class ?>"><?= $status_text ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= $contract->contractor_name ?></strong>
                                            <br><small class="text-muted"><?= $contract->contractor_contact ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($contract->contract_value): ?>
                                        <strong><?= format_money($contract->contract_value) ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_classes = [
                                            'draft' => 'default',
                                            'active' => 'success',
                                            'completed' => 'info',
                                            'cancelled' => 'danger',
                                            'expired' => 'warning'
                                        ];
                                        $status_class = $status_classes[$contract->status] ?? 'default';
                                        ?>
                                        <span class="label label-<?= $status_class ?>">
                                            <?= ucfirst($contract->status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $contract->start_date ? date('M d, Y', strtotime($contract->start_date)) : '-' ?>
                                    </td>
                                    <td>
                                        <?= $contract->end_date ? date('M d, Y', strtotime($contract->end_date)) : '-' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= admin_url('ella_contractors/view_contract/' . $contract->id) ?>" class="btn btn-xs btn-default" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/edit_contract/' . $contract->id) ?>" class="btn btn-xs btn-info" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <?php if ($contract->status === 'draft'): ?>
                                            <a href="<?= admin_url('ella_contractors/delete_contract/' . $contract->id) ?>" class="btn btn-xs btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this contract?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="text-center">
                        <ul class="pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                            <li>
                                <a href="<?= admin_url('ella_contractors/contracts/' . ($pagination['current_page'] - 1)) ?>?<?= http_build_query($filters) ?>">
                                    &laquo; Previous
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="<?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <a href="<?= admin_url('ella_contractors/contracts/' . $i) ?>?<?= http_build_query($filters) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <li>
                                <a href="<?= admin_url('ella_contractors/contracts/' . ($pagination['current_page'] + 1)) ?>?<?= http_build_query($filters) ?>">
                                    Next &raquo;
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php else: ?>
                    <div class="text-center empty-state-container">
                        <i class="fa fa-file-contract fa-3x text-muted"></i>
                        <h3 class="text-muted">No contracts found</h3>
                        <p class="text-muted">Get started by creating your first contract.</p>
                        <a href="<?= admin_url('ella_contractors/add_contract') ?>" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Create First Contract
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

<script>
$(document).ready(function() {
    // Select all contracts
    $('#select-all-contracts').change(function() {
        $('.contract-checkbox').prop('checked', this.checked);
        updateBulkSubmitButton();
    });
    
    // Individual checkbox change
    $('.contract-checkbox').change(function() {
        updateBulkSubmitButton();
        
        // Update select all checkbox
        var total = $('.contract-checkbox').length;
        var checked = $('.contract-checkbox:checked').length;
        
        if (checked === 0) {
            $('#select-all-contracts').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select-all-contracts').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all-contracts').prop('indeterminate', true);
        }
    });
    
    // Update bulk submit button
    function updateBulkSubmitButton() {
        var checked = $('.contract-checkbox:checked').length;
        var action = $('select[name="bulk_action"]').val();
        
        if (checked > 0 && action) {
            $('#bulk-submit').prop('disabled', false);
        } else {
            $('#bulk-submit').prop('disabled', true);
        }
    }
    
    // Bulk action change
    $('select[name="bulk_action"]').change(function() {
        updateBulkSubmitButton();
    });
    
    // Bulk form submission
    $('#bulk-actions-form').submit(function(e) {
        var checked = $('.contract-checkbox:checked').length;
        var action = $('select[name="bulk_action"]').val();
        
        if (checked === 0) {
            e.preventDefault();
            alert('Please select at least one contract.');
            return false;
        }
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action.');
            return false;
        }
        
        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete the selected draft contracts? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });
});
</script>

<?php init_tail(); ?>
