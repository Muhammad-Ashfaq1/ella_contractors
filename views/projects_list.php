<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Ella Contractors</title>
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
        .status-planning { background-color: #e9ecef; color: #495057; }
        .status-in_progress { background-color: #d1ecf1; color: #0c5460; }
        .status-on_hold { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .action-buttons .btn { margin: 0 0.125rem; }
        .search-filters { background-color: #f8f9fa; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .pagination-info { margin: 1rem 0; }
        .progress-bar { height: 8px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-project-diagram"></i> Projects Management</h1>
                    <a href="<?= admin_url('ella_contractors/projects/add') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Project
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <form method="GET" action="<?= admin_url('ella_contractors/projects') ?>" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Search projects, descriptions, contractors...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="planning" <?= ($status_filter ?? '') === 'planning' ? 'selected' : '' ?>>Planning</option>
                        <option value="in_progress" <?= ($status_filter ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="on_hold" <?= ($status_filter ?? '') === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
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

        <!-- Projects Grid -->
        <div class="row">
            <?php if (empty($projects)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No projects found</h4>
                        <p class="text-muted">Get started by creating your first project</p>
                        <a href="<?= admin_url('ella_contractors/projects/add') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Project
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?= htmlspecialchars($project->name) ?></h6>
                                <span class="status-badge status-<?= $project->status ?>">
                                    <?= ucfirst(str_replace('_', ' ', $project->status)) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-building"></i> 
                                        <?= htmlspecialchars($project->contractor_name) ?>
                                    </small>
                                </div>
                                
                                <?php if ($project->description): ?>
                                    <p class="card-text small">
                                        <?= htmlspecialchars(substr($project->description, 0, 100)) ?>
                                        <?= strlen($project->description) > 100 ? '...' : '' ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Budget</small>
                                        <div class="fw-bold text-success">
                                            $<?= number_format($project->budget, 2) ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Location</small>
                                        <div class="fw-bold">
                                            <?= htmlspecialchars($project->location ?: 'N/A') ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Start Date</small>
                                        <div>
                                            <?= $project->start_date ? date('M j, Y', strtotime($project->start_date)) : 'N/A' ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">End Date</small>
                                        <div>
                                            <?= $project->estimated_end_date ? date('M j, Y', strtotime($project->estimated_end_date)) : 'N/A' ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($project->project_manager): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Project Manager</small>
                                        <div class="fw-bold">
                                            <?= htmlspecialchars($project->project_manager) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Progress Bar for In Progress Projects -->
                                <?php if ($project->status === 'in_progress'): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Progress</small>
                                        <div class="progress progress-bar">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%"></div>
                                        </div>
                                        <small class="text-muted">65% Complete</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= admin_url('ella_contractors/projects/view/' . $project->id) ?>" 
                                           class="btn btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= admin_url('ella_contractors/projects/edit/' . $project->id) ?>" 
                                           class="btn btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= admin_url('ella_contractors/projects/generate_ppt/' . $project->id) ?>" 
                                           class="btn btn-outline-secondary" title="Generate PPT">
                                            <i class="fas fa-file-powerpoint"></i>
                                        </a>
                                    </div>
                                    <button onclick="deleteProject(<?= $project->id ?>)" 
                                            class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
            <nav aria-label="Projects pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= admin_url('ella_contractors/projects/' . ($current_page - 1)) ?>?<?= http_build_query($_GET) ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                        <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= admin_url('ella_contractors/projects/' . $i) ?>?<?= http_build_query($_GET) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= admin_url('ella_contractors/projects/' . ($current_page + 1)) ?>?<?= http_build_query($_GET) ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteProject(projectId) {
            if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                window.location.href = '<?= admin_url('ella_contractors/projects/delete/') ?>' + projectId;
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
