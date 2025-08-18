<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contracts - Ella Contractors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-active { background-color: #d1ecf1; color: #0c5460; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .action-buttons .btn { margin: 0 0.125rem; }
        .search-filters { background-color: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .pagination-info { margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-file-contract"></i> Contracts Management</h1>
                    <a href="<?= admin_url('ella_contractors/contracts/add') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Contract
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <form method="GET" action="<?= admin_url('ella_contractors/contracts') ?>" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                                                <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= htmlspecialchars($search ?? '') ?>" 
                                   placeholder="Search contracts, titles, contractors...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="draft" <?= ($status_filter ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="active" <?= ($status_filter ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="completed" <?= ($status_filter ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($status_filter ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="contractor_id" class="form-label">Contractor</label>
                    <select class="form-select" id="contractor_id" name="contractor_id">
                        <option value="">All Contractors</option>
                        <?php foreach ($contractors as $contractor): ?>
                            <option value="<?= $contractor->id ?>" 
                                    <?= ($contractor_filter ?? '') == $contractor->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($contractor->company_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pagination Info -->
        <?php if (isset($total_count) && $total_count > 0): ?>
            <div class="pagination-info">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">
                            Showing <?= (($current_page - 1) * 20) + 1 ?> to 
                            <?= min($current_page * 20, $total_count) ?> of <?= $total_count ?> contracts
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= admin_url('ella_contractors/contracts/add') ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Quick Add
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contracts Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($contracts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No contracts found</h4>
                        <p class="text-muted">Get started by creating your first contract</p>
                        <a href="<?= admin_url('ella_contractors/contracts/add') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Contract
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Contract #</th>
                                    <th>Project</th>
                                    <th>Contractor</th>
                                    <th>Value</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contracts as $contract): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($contract->contract_number ?: 'CON-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT)) ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($contract->title) ?></strong>
                                                <?php if ($contract->description): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(substr($contract->description, 0, 50)) ?>...</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($contract->contractor_name) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                $<?= number_format($contract->fixed_amount, 2) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $contract->start_date ? date('M j, Y', strtotime($contract->start_date)) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?= $contract->end_date ? date('M j, Y', strtotime($contract->end_date)) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= $contract->status ?>">
                                                <?= ucfirst($contract->status) ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract->id) ?>" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/contracts/edit/' . $contract->id) ?>" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/contracts/generate_pdf/' . $contract->id) ?>" 
                                               class="btn btn-sm btn-outline-danger" title="Generate PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/contracts/generate_ppt/' . $contract->id) ?>" 
                                               class="btn btn-sm btn-outline-secondary" title="Generate PPT">
                                                <i class="fas fa-file-powerpoint"></i>
                                            </a>
                                            <button onclick="deleteContract(<?= $contract->id ?>)" 
                                                    class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                        <nav aria-label="Contracts pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= admin_url('ella_contractors/contracts/' . ($current_page - 1)) ?>?<?= http_build_query($_GET) ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= admin_url('ella_contractors/contracts/' . $i) ?>?<?= http_build_query($_GET) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= admin_url('ella_contractors/contracts/' . ($current_page + 1)) ?>?<?= http_build_query($_GET) ?>">
                                            Next <i class="fas fa-chevron-right"></i>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this contract? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete Contract</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteContract(contractId) {
            if (confirm('Are you sure you want to delete this contract? This action cannot be undone.')) {
                window.location.href = '<?= admin_url('ella_contractors/contracts/delete/') ?>' + contractId;
            }
        }

        // Auto-submit form when filters change
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });

        document.getElementById('contractor_id').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
</body>
</html>
