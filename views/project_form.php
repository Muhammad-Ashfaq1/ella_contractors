<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($project) ? 'Edit Project' : 'Add New Project' ?> - Ella Contractors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-section { background-color: #f8f9fa; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .form-section h5 { color: #495057; margin-bottom: 1rem; }
        .required-field::after { content: " *"; color: #dc3545; }
        .help-text { font-size: 0.875rem; color: #6c757d; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>
                        <i class="fas fa-project-diagram"></i> 
                        <?= isset($project) ? 'Edit Project' : 'Add New Project' ?>
                    </h1>
                    <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Projects
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" action="" id="projectForm">
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="contractor_id" class="form-label required-field">Contractor</label>
                                <select class="form-select" id="contractor_id" name="contractor_id" required>
                                    <option value="">Select Contractor</option>
                                    <?php foreach ($contractors as $contractor): ?>
                                        <option value="<?= $contractor->id ?>" 
                                                <?= (isset($project) && $project->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($contractor->company_name) ?> - <?= htmlspecialchars($contractor->contact_person) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Choose the contractor responsible for this project</div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label required-field">Project Name</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       value="<?= isset($project) ? htmlspecialchars($project->name) : '' ?>"
                                       placeholder="e.g., Office Building Renovation">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Project Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Detailed description of the project scope, objectives, and deliverables..."><?= isset($project) ? htmlspecialchars($project->description) : '' ?></textarea>
                                <div class="help-text">Provide a comprehensive description of the project</div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Details -->
                    <div class="form-section">
                        <h5><i class="fas fa-cogs"></i> Project Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Project Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="<?= isset($project) ? htmlspecialchars($project->location) : '' ?>"
                                       placeholder="e.g., 123 Main Street, New York, NY">
                            </div>
                            <div class="col-md-6">
                                <label for="project_manager" class="form-label">Project Manager</label>
                                <input type="text" class="form-control" id="project_manager" name="project_manager"
                                       value="<?= isset($project) ? htmlspecialchars($project->project_manager) : '' ?>"
                                       placeholder="e.g., John Smith">
                                <div class="help-text">Person responsible for managing the project</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="form-section">
                        <h5><i class="fas fa-calendar-alt"></i> Project Timeline</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="<?= isset($project) ? $project->start_date : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="estimated_end_date" class="form-label">Estimated End Date</label>
                                <input type="date" class="form-control" id="estimated_end_date" name="estimated_end_date"
                                       value="<?= isset($project) ? $project->estimated_end_date : '' ?>">
                            </div>
                        </div>
                        <div class="help-text mt-2">Set project start and estimated completion dates</div>
                    </div>

                    <!-- Financial Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-dollar-sign"></i> Financial Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="budget" class="form-label required-field">Project Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="budget" name="budget" 
                                           step="0.01" min="0" required
                                           value="<?= isset($project) ? $project->budget : '' ?>"
                                           placeholder="0.00">
                                </div>
                                <div class="help-text">Total project budget in USD</div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label required-field">Project Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="planning" <?= (isset($project) && $project->status == 'planning') ? 'selected' : '' ?>>Planning</option>
                                    <option value="in_progress" <?= (isset($project) && $project->status == 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="on_hold" <?= (isset($project) && $project->status == 'on_hold') ? 'selected' : '' ?>>On Hold</option>
                                    <option value="completed" <?= (isset($project) && $project->status == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= (isset($project) && $project->status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-sticky-note"></i> Additional Information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Notes & Comments</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"
                                          placeholder="Any additional notes, special requirements, or important information about the project..."><?= isset($project) ? htmlspecialchars($project->notes) : '' ?></textarea>
                                <div class="help-text">Internal notes and comments about the project</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <div>
                                    <?php if (isset($project)): ?>
                                        <a href="<?= admin_url('ella_contractors/presentation/project/' . $project->id) ?>" 
                                           class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-file-powerpoint"></i> Generate PPT
                                        </a>
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> 
                                        <?= isset($project) ? 'Update Project' : 'Create Project' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            const contractorId = document.getElementById('contractor_id').value;
            const projectName = document.getElementById('name').value;
            const budget = document.getElementById('budget').value;
            const status = document.getElementById('status').value;

            if (!contractorId || !projectName || !budget || !status) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *');
                return false;
            }

            if (parseFloat(budget) <= 0) {
                e.preventDefault();
                alert('Project budget must be greater than 0');
                return false;
            }

            // Date validation
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('estimated_end_date').value;
            
            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                alert('Start date cannot be after estimated end date');
                return false;
            }
        });

        // Status change handler
        document.getElementById('status').addEventListener('change', function() {
            const status = this.value;
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('estimated_end_date');
            
            if (status === 'completed') {
                // If marked as completed, ensure end date is set
                if (!endDate.value) {
                    endDate.value = new Date().toISOString().split('T')[0];
                }
            }
        });
    </script>
</body>
</html>
