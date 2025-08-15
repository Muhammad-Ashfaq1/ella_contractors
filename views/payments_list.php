<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Ella Contractors</title>
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
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
        .status-cancelled { background-color: #e9ecef; color: #495057; }
        .action-buttons .btn { margin: 0 0.125rem; }
        .search-filters { background-color: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .pagination-info { margin: 1rem 0; }
        .amount-cell { font-weight: 600; }
        .overdue { color: #dc3545; font-weight: bold; }
        .due-soon { color: #fd7e14; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-credit-card"></i> Payments Management</h1>
                    <a href="<?= admin_url('ella_contractors/payments/add') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Payment
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <form method="GET" action="<?= admin_url('ella_contractors/payments') ?>" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Search invoices, contractors, projects...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= ($status_filter ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="paid" <?= ($status_filter ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="overdue" <?= ($status_filter ?? '') === 'overdue' ? 'selected' : '' ?>>Overdue</option>
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

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Pending</h6>
                                <h4 class="mb-0">$<?= number_format($pending_total ?? 0, 2) ?></h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Paid</h6>
                                <h4 class="mb-0">$<?= number_format($paid_total ?? 0, 2) ?></h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Overdue</h6>
                                <h4 class="mb-0">$<?= number_format($overdue_total ?? 0, 2) ?></h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Count</h6>
                                <h4 class="mb-0"><?= $total_count ?? 0 ?></h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($payments)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No payments found</h4>
                        <p class="text-muted">Get started by creating your first payment record</p>
                        <a href="<?= admin_url('ella_contractors/payments/add') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Payment
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Contractor</th>
                                    <th>Project</th>
                                    <th>Amount</th>
                                    <th>Invoice Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <?php 
                                        $due_date = $payment->due_date ? strtotime($payment->due_date) : null;
                                        $is_overdue = $due_date && $due_date < time() && $payment->status === 'pending';
                                        $is_due_soon = $due_date && $due_date < strtotime('+7 days') && $due_date > time() && $payment->status === 'pending';
                                    ?>
                                    <tr class="<?= $is_overdue ? 'table-danger' : ($is_due_soon ? 'table-warning' : '') ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($payment->invoice_number ?: 'INV-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)) ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($payment->contractor_name) ?></strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?= htmlspecialchars($payment->project_name ?: 'N/A') ?>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <span class="text-success">
                                                $<?= number_format($payment->amount, 2) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $payment->invoice_date ? date('M j, Y', strtotime($payment->invoice_date)) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?php if ($due_date): ?>
                                                <span class="<?= $is_overdue ? 'overdue' : ($is_due_soon ? 'due-soon' : '') ?>">
                                                    <?= date('M j, Y', $due_date) ?>
                                                </span>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= $payment->status ?>">
                                                <?= ucfirst($payment->status) ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="<?= admin_url('ella_contractors/payments/view/' . $payment->id) ?>" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/payments/edit/' . $payment->id) ?>" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= admin_url('ella_contractors/pdf/invoice/' . $payment->id) ?>" 
                                               class="btn btn-sm btn-outline-danger" title="Generate PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <?php if ($payment->status === 'pending'): ?>
                                                <button onclick="markAsPaid(<?= $payment->id ?>)" 
                                                        class="btn btn-sm btn-outline-success" title="Mark as Paid">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="deletePayment(<?= $payment->id ?>)" 
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
                        <nav aria-label="Payments pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= admin_url('ella_contractors/payments/' . ($current_page - 1)) ?>?<?= http_build_query($_GET) ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= admin_url('ella_contractors/payments/' . $i) ?>?<?= http_build_query($_GET) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= admin_url('ella_contractors/payments/' . ($current_page + 1)) ?>?<?= http_build_query($_GET) ?>">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deletePayment(paymentId) {
            if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
                window.location.href = '<?= admin_url('ella_contractors/payments/delete/') ?>' + paymentId;
            }
        }

        function markAsPaid(paymentId) {
            if (confirm('Mark this payment as paid?')) {
                // You can implement this as a separate endpoint or form submission
                alert('Payment marked as paid! (Implementation needed)');
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
