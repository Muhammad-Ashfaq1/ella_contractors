<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contractors - Ella CRM</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .search-box { margin-bottom: 20px; }
        .pagination { justify-content: center; margin-top: 20px; }
        .action-buttons { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fa fa-users"></i> Contractors Management</h1>
                    <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add New Contractor
                    </a>
                </div>
                
                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search contractors..." 
                                       value="<?= htmlspecialchars($search ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="active" <?= ($status_filter == 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= ($status_filter == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                    <option value="pending" <?= ($status_filter == 'pending') ? 'selected' : '' ?>>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Contractors Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Contractors List (<?= $total_count ?> total)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($contractors)): ?>
                            <div class="text-center py-4">
                                <i class="fa fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No contractors found</h5>
                                <p class="text-muted">Try adjusting your search criteria or add a new contractor.</p>
                                <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add First Contractor
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Company</th>
                                            <th>Contact Person</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Specialties</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contractors as $contractor): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($contractor->company_name) ?></strong>
                                                    <?php if ($contractor->license_number): ?>
                                                        <br><small class="text-muted">License: <?= htmlspecialchars($contractor->license_number) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($contractor->contact_person) ?></td>
                                                <td>
                                                    <a href="mailto:<?= htmlspecialchars($contractor->email) ?>">
                                                        <?= htmlspecialchars($contractor->email) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="tel:<?= htmlspecialchars($contractor->phone) ?>">
                                                        <?= htmlspecialchars($contractor->phone) ?>
                                                    </a>
                                                </td>
                                                <td><?= htmlspecialchars($contractor->specialties ?: 'General') ?></td>
                                                <td>
                                                    <span class="status-badge status-<?= $contractor->status ?>">
                                                        <?= ucfirst($contractor->status) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($contractor->date_created)) ?></td>
                                                <td class="action-buttons">
                                                    <a href="<?= admin_url('ella_contractors/contractors/edit/' . $contractor->id) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/contracts?contractor_id=' . $contractor->id) ?>" 
                                                       class="btn btn-sm btn-outline-info" title="View Contracts">
                                                        <i class="fa fa-file-contract"></i>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/projects?contractor_id=' . $contractor->id) ?>" 
                                                       class="btn btn-sm btn-outline-success" title="View Projects">
                                                        <i class="fa fa-tasks"></i>
                                                    </a>
                                                    <a href="<?= admin_url('ella_contractors/documents/gallery/' . $contractor->id) ?>" 
                                                       class="btn btn-sm btn-outline-warning" title="Documents">
                                                        <i class="fa fa-folder-open"></i>
                                                    </a>
                                                    <button onclick="deleteContractor(<?= $contractor->id ?>)" 
                                                            class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
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
                                                <a class="page-link" href="<?= admin_url('ella_contractors/contractors/' . ($current_page - 1)) ?>?search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status_filter ?? '') ?>">
                                                    <i class="fa fa-chevron-left"></i> Previous
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= admin_url('ella_contractors/contractors/' . $i) ?>?search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status_filter ?? '') ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= admin_url('ella_contractors/contractors/' . ($current_page + 1)) ?>?search=<?= urlencode($search ?? '') ?>&status=<?= urlencode($status_filter ?? '') ?>">
                                                    Next <i class="fa fa-chevron-right"></i>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        function deleteContractor(contractorId) {
            if (confirm('Are you sure you want to delete this contractor? This action cannot be undone.')) {
                window.location.href = '<?= admin_url('ella_contractors/contractors/delete/') ?>' + contractorId;
            }
        }
        
        // Auto-submit form on status change
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
