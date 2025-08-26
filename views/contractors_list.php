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
                            <p class="text-muted">Manage your contractors and their information</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?= admin_url('ella_contractors/add_contractor') ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add New Contractor
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
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div class="widget-card-content">
                                        <h3><?= $stats['total'] ?></h3>
                                        <p>Total Contractors</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget-card bg-success text-white">
                                <div class="widget-card-body">
                                    <div class="widget-card-icon">
                                        <i class="fa fa-check-circle"></i>
                                    </div>
                                    <div class="widget-card-content">
                                        <h3><?= $stats['active'] ?></h3>
                                        <p>Active</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget-card bg-warning text-white">
                                <div class="widget-card-body">
                                    <div class="widget-card-icon">
                                        <i class="fa fa-pause-circle"></i>
                                    </div>
                                    <div class="widget-card-content">
                                        <h3><?= $stats['inactive'] ?></h3>
                                        <p>Inactive</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget-card bg-info text-white">
                                <div class="widget-card-body">
                                    <div class="widget-card-icon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <div class="widget-card-content">
                                        <h3><?= $stats['pending'] ?></h3>
                                        <p>Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget-card bg-danger text-white">
                                <div class="widget-card-body">
                                    <div class="widget-card-icon">
                                        <i class="fa fa-ban"></i>
                                    </div>
                                    <div class="widget-card-content">
                                        <h3><?= $stats['blacklisted'] ?></h3>
                                        <p>Blacklisted</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="widget-card bg-info text-white">
                                <div class="widget-card-body">
                                    <div class="widget-card-icon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <div class="widget-card-content">
                                        <h3><?= $stats['recent'] ?></h3>
                                        <p>Recent (30d)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Search and Filters -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <form method="GET" action="<?= admin_url('ella_contractors/contractors') ?>" class="form-horizontal">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Search</label>
                                                    <input type="text" name="search" class="form-control" value="<?= isset($filters['search']) ? $filters['search'] : '' ?>" placeholder="Company, Contact, Email, City...">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="">All Statuses</option>
                                                        <option value="active" <?= (isset($filters['status']) && $filters['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= (isset($filters['status']) && $filters['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                                        <option value="pending" <?= (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                                        <option value="blacklisted" <?= (isset($filters['status']) && $filters['status'] == 'blacklisted') ? 'selected' : '' ?>>Blacklisted</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Specialization</label>
                                                    <input type="text" name="specialization" class="form-control" value="<?= isset($filters['specialization']) ? $filters['specialization'] : '' ?>" placeholder="e.g., Plumbing, Electrical...">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fa fa-search"></i> Search
                                                        </button>
                                                        <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-default">
                                                            <i class="fa fa-refresh"></i> Reset
                                                        </a>
                                                    </div>
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
                            <form id="bulk-actions-form" method="POST" action="<?= admin_url('ella_contractors/bulk_actions') ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <select name="bulk_action" class="form-control" style="width: 200px;">
                                                <option value="">Bulk Actions</option>
                                                <option value="activate">Activate</option>
                                                <option value="deactivate">Deactivate</option>
                                                <option value="pending">Set Pending</option>
                                                <option value="blacklist">Blacklist</option>
                                                <option value="delete">Delete</option>
                                            </select>
                                            <button type="submit" class="btn btn-default" id="bulk-submit" disabled>
                                                Apply
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <span class="text-muted"><?= $total_contractors ?> contractors found</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Contractors Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!empty($contractors)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="contractors-table">
                                    <thead>
                                        <tr>
                                            <th width="20">
                                                <input type="checkbox" id="select-all-contractors">
                                            </th>
                                            <th>Company</th>
                                            <th>Contact Person</th>
                                            <th>Contact Info</th>
                                            <th>Location</th>
                                            <th>Specialization</th>
                                            <th>Hourly Rate</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contractors as $contractor): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="contractor_ids[]" value="<?= $contractor->id ?>" class="contractor-checkbox">
                                            </td>
                                            <td>
                                                <strong><?= $contractor->company_name ?></strong>
                                                <?php if ($contractor->business_license): ?>
                                                <br><small class="text-muted">License: <?= $contractor->business_license ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= $contractor->contact_person ?></strong>
                                            </td>
                                            <td>
                                                <div><i class="fa fa-envelope"></i> <?= $contractor->email ?></div>
                                                <?php if ($contractor->phone): ?>
                                                <div><i class="fa fa-phone"></i> <?= $contractor->phone ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($contractor->city): ?>
                                                <div><?= $contractor->city ?></div>
                                                <?php endif; ?>
                                                <?php if ($contractor->state): ?>
                                                <div><?= $contractor->state ?></div>
                                                <?php endif; ?>
                                                <?php if ($contractor->country): ?>
                                                <div><?= $contractor->country ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($contractor->specialties): ?>
                                                <span class="label label-info"><?= $contractor->specialties ?></span>
                                                <?php else: ?>
                                                <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($contractor->hourly_rate): ?>
                                                $<?= number_format($contractor->hourly_rate, 2) ?>/hr
                                                <?php else: ?>
                                                <span class="text-muted">Not set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                $status_text = '';
                                                switch ($contractor->status) {
                                                    case 'active':
                                                        $status_class = 'label-success';
                                                        $status_text = 'Active';
                                                        break;
                                                    case 'inactive':
                                                        $status_class = 'label-warning';
                                                        $status_text = 'Inactive';
                                                        break;
                                                    case 'pending':
                                                        $status_class = 'label-info';
                                                        $status_text = 'Pending';
                                                        break;
                                                    case 'blacklisted':
                                                        $status_class = 'label-danger';
                                                        $status_text = 'Blacklisted';
                                                        break;
                                                }
                                                ?>
                                                <span class="label <?= $status_class ?>"><?= $status_text ?></span>
                                            </td>
                                            <td>
                                                <small><?= date('M j, Y', strtotime($contractor->date_created)) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= admin_url('ella_contractors/view_contractor/' . $contractor->id) ?>" class="btn btn-default btn-xs" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/edit_contractor/' . $contractor->id) ?>" class="btn btn-default btn-xs" title="Edit">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <?php if (has_permission('ella_contractors', '', 'delete')): ?>
                                                    <a href="<?= admin_url('ella_contractors/delete_contractor/' . $contractor->id) ?>" class="btn btn-danger btn-xs" title="Delete" onclick="return confirm('Are you sure you want to delete this contractor?')">
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
                            <?php if ($pagination): ?>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <?= $pagination ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php else: ?>
                            <div class="text-center" style="padding: 40px;">
                                <i class="fa fa-users fa-3x text-muted"></i>
                                <h3 class="text-muted">No contractors found</h3>
                                <p class="text-muted">Get started by adding your first contractor.</p>
                                <a href="<?= admin_url('ella_contractors/add_contractor') ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add First Contractor
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

<script>
$(document).ready(function() {
    // Select all contractors
    $('#select-all-contractors').change(function() {
        $('.contractor-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkSubmitButton();
    });
    
    // Individual contractor checkbox change
    $('.contractor-checkbox').change(function() {
        updateBulkSubmitButton();
        
        // Update select all checkbox
        var total = $('.contractor-checkbox').length;
        var checked = $('.contractor-checkbox:checked').length;
        
        if (checked === 0) {
            $('#select-all-contractors').prop('indeterminate', false).prop('checked', false);
        } else if (checked === total) {
            $('#select-all-contractors').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#select-all-contractors').prop('indeterminate', true);
        }
    });
    
    // Update bulk submit button
    function updateBulkSubmitButton() {
        var checked = $('.contractor-checkbox:checked').length;
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
    
    // Bulk actions form submission
    $('#bulk-actions-form').submit(function(e) {
        var checked = $('.contractor-checkbox:checked').length;
        var action = $('select[name="bulk_action"]').val();
        
        if (checked === 0) {
            e.preventDefault();
            alert('Please select at least one contractor.');
            return false;
        }
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action.');
            return false;
        }
        
        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete ' + checked + ' contractor(s)? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });
});
</script>

<?php init_tail(); ?>
