<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 5px 7px #4075A1;
    }

    .card-header-custom {
        background-color: #4075A1;
        color: white;
        padding: 10px;
        border-bottom: none;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
    }

    .card-header-custom h4 {
        margin-bottom: 0;
        font-weight: 600;
    }

    .card-body-custom {
        padding: 1rem;
    }

    .status-badge { 
        padding: 4px 8px; 
        border-radius: 12px; 
        font-size: 12px; 
        font-weight: bold; 
    }
    
    .status-active { 
        background-color: #d4edda; 
        color: #155724; 
    }
    
    .status-inactive { 
        background-color: #f8d7da; 
        color: #721c24; 
    }
    
    .status-pending { 
        background-color: #fff3cd; 
        color: #856404; 
    }
    
    .status-suspended { 
        background-color: #e2e3e5; 
        color: #383d41; 
    }

    .search-box { 
        margin-bottom: 20px; 
    }
    
    .pagination { 
        justify-content: center; 
        margin-top: 20px; 
    }
    
    .action-buttons { 
        white-space: nowrap; 
    }

    .btn-primary {
        background-color: #4075A1;
        border-color: #4075A1;
    }

    .btn-primary:hover {
        background-color: #36648b;
        border-color: #36648b;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
    }

    .contractor-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

    .contractor-info {
        display: flex;
        align-items: center;
    }

    .contractor-details h6 {
        margin: 0;
        font-weight: 600;
        color: #495057;
    }

    .contractor-details small {
        color: #6c757d;
        font-size: 12px;
    }

    .filters-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .filters-card .card-body {
        padding: 20px;
    }

    .stats-row {
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #4075A1 0%, #36648b 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(64, 117, 161, 0.3);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">
                        <i class="fa fa-users"></i> Contractors Management
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row stats-row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?= $total_count ?></div>
                <div class="stat-label">Total Contractors</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?= $active_contracts ?? 0 ?></div>
                <div class="stat-label">Active Contracts</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?= $pending_payments ?? 0 ?></div>
                <div class="stat-label">Pending Payments</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?= $active_projects ?? 0 ?></div>
                <div class="stat-label">Active Projects</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row">
        <div class="col-md-12">
            <div class="card filters-card">
                <div class="card-body">
                    <form method="GET" class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search Contractors</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Search by company, contact person, email..." 
                                       value="<?= htmlspecialchars($search ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status Filter</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="active" <?= ($status_filter == 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= ($status_filter == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                    <option value="pending" <?= ($status_filter == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="suspended" <?= ($status_filter == 'suspended') ? 'selected' : '' ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="btn btn-primary btn-block">
                                        <i class="fa fa-plus"></i> Add New
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contractors Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header-custom">
                    <h4 class="mb-0">
                        <i class="fa fa-list"></i> Contractors List (<?= $total_count ?> total)
                    </h4>
                </div>
                <div class="card-body-custom">
                    <?php if (empty($contractors)): ?>
                        <div class="text-center py-5">
                            <i class="fa fa-users fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No contractors found</h5>
                            <p class="text-muted">Try adjusting your search criteria or add a new contractor.</p>
                            <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add First Contractor
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Contractor</th>
                                        <th>Contact Info</th>
                                        <th>Specialties</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contractors as $contractor): ?>
                                        <tr>
                                            <td>
                                                <div class="contractor-info">
                                                    <?php if ($contractor->profile_image): ?>
                                                        <img src="<?= base_url($contractor->profile_image) ?>" 
                                                             alt="Profile" class="contractor-avatar">
                                                    <?php else: ?>
                                                        <div class="contractor-avatar bg-secondary d-flex align-items-center justify-content-center">
                                                            <i class="fa fa-user text-white"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="contractor-details">
                                                        <h6><?= htmlspecialchars($contractor->company_name) ?></h6>
                                                        <?php if ($contractor->business_license): ?>
                                                            <small>License: <?= htmlspecialchars($contractor->business_license) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($contractor->contact_person) ?></strong><br>
                                                    <a href="mailto:<?= htmlspecialchars($contractor->email) ?>" class="text-info">
                                                        <i class="fa fa-envelope"></i> <?= htmlspecialchars($contractor->email) ?>
                                                    </a><br>
                                                    <?php if ($contractor->phone): ?>
                                                        <a href="tel:<?= htmlspecialchars($contractor->phone) ?>" class="text-info">
                                                            <i class="fa fa-phone"></i> <?= htmlspecialchars($contractor->phone) ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($contractor->specialties): ?>
                                                    <span class="badge badge-info"><?= htmlspecialchars($contractor->specialties) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($contractor->hourly_rate): ?>
                                                    <strong>$<?= number_format($contractor->hourly_rate, 2) ?>/hr</strong>
                                                <?php else: ?>
                                                    <span class="text-muted">Not set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = 'status-' . $contractor->status;
                                                $status_text = ucfirst($contractor->status);
                                                ?>
                                                <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                            </td>
                                            <td>
                                                <small><?= _dt($contractor->date_created) ?></small>
                                            </td>
                                            <td class="action-buttons">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= admin_url('ella_contractors/contractors/view/' . $contractor->id) ?>" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/contractors/edit/' . $contractor->id) ?>" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-contractor" 
                                                            data-id="<?= $contractor->id ?>" 
                                                            data-name="<?= htmlspecialchars($contractor->company_name) ?>"
                                                            title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Contractors pagination">
                                <ul class="pagination">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= admin_url('ella_contractors/contractors?page=' . ($current_page - 1) . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($status_filter ?? '')) ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                        <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= admin_url('ella_contractors/contractors?page=' . $i . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($status_filter ?? '')) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= admin_url('ella_contractors/contractors?page=' . ($current_page + 1) . '&search=' . urlencode($search ?? '') . '&status=' . urlencode($status_filter ?? '')) ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Delete contractor confirmation
    $('.delete-contractor').on('click', function() {
        var contractorId = $(this).data('id');
        var contractorName = $(this).data('name');
        
        if (confirm('Are you sure you want to delete "' + contractorName + '"? This action cannot be undone.')) {
            $.ajax({
                url: '<?= admin_url('ella_contractors/contractors/delete/') ?>' + contractorId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert_float('success', response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        alert_float('danger', response.message);
                    }
                },
                error: function() {
                    alert_float('danger', 'An error occurred while deleting the contractor.');
                }
            });
        }
    });

    // Auto-submit form on status change
    $('#status').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>

<?php init_tail(); ?>
