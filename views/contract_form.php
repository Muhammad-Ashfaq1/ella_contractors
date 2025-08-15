<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($contract) ? 'Edit Contract' : 'Add New Contract' ?> - Ella Contractors</title>
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
                        <i class="fas fa-file-contract"></i> 
                        <?= isset($contract) ? 'Edit Contract' : 'Add New Contract' ?>
                    </h1>
                    <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Contracts
                    </a>
                </div>
            </div>
        </div>

        <!-- Contract Form -->
        <div class="card">
            <div class="card-body">
                <form method="POST" action="" id="contractForm">
                    
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
                                                <?= (isset($contract) && $contract->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($contractor->company_name) ?> - <?= htmlspecialchars($contractor->contact_person) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-text">Choose the contractor for this project</div>
                            </div>
                            <div class="col-md-6">
                                <label for="contract_number" class="form-label">Contract Number</label>
                                <input type="text" class="form-control" id="contract_number" name="contract_number" 
                                       value="<?= isset($contract) ? htmlspecialchars($contract->contract_number) : '' ?>"
                                       placeholder="e.g., CON-2024-001">
                                <div class="help-text">Optional: Custom contract number (auto-generated if left blank)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Details -->
                    <div class="form-section">
                        <h5><i class="fas fa-project-diagram"></i> Project Details</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="title" class="form-label required-field">Project Title</label>
                                <input type="text" class="form-control" id="title" name="title" required
                                       value="<?= isset($contract) ? htmlspecialchars($contract->title) : '' ?>"
                                       placeholder="e.g., Office Building Renovation">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Project Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Detailed description of the project scope, requirements, and deliverables..."><?= isset($contract) ? htmlspecialchars($contract->description) : '' ?></textarea>
                                <div class="help-text">Provide a comprehensive description of the project</div>
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
                                       value="<?= isset($contract) ? $contract->start_date : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="<?= isset($contract) ? $contract->end_date : '' ?>">
                            </div>
                        </div>
                        <div class="help-text mt-2">Set project start and completion dates</div>
                    </div>

                    <!-- Financial Terms -->
                    <div class="form-section">
                        <h5><i class="fas fa-dollar-sign"></i> Financial Terms</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="fixed_amount" class="form-label required-field">Contract Value</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="fixed_amount" name="fixed_amount" 
                                           step="0.01" min="0" required
                                           value="<?= isset($contract) ? $contract->fixed_amount : '' ?>"
                                           placeholder="0.00">
                                </div>
                                <div class="help-text">Total contract value in USD</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" 
                                           step="0.01" min="0"
                                           value="<?= isset($contract) ? $contract->hourly_rate : '' ?>"
                                           placeholder="0.00">
                                </div>
                                <div class="help-text">Hourly rate for time-based contracts</div>
                            </div>
                            <div class="col-md-6">
                                <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimated_hours" name="estimated_hours" 
                                       step="0.01" min="0"
                                       value="<?= isset($contract) ? $contract->estimated_hours : '' ?>"
                                       placeholder="0.00">
                                <div class="help-text">Estimated total hours for the project</div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label required-field">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="draft" <?= (isset($contract) && $contract->status == 'draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="active" <?= (isset($contract) && $contract->status == 'active') ? 'selected' : '' ?>>Active</option>
                                    <option value="completed" <?= (isset($contract) && $contract->status == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= (isset($contract) && $contract->status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="payment_terms" class="form-label">Payment Terms</label>
                                <textarea class="form-control" id="payment_terms" name="payment_terms" rows="3"
                                          placeholder="e.g., 30% upfront, 40% at 50% completion, 30% upon final inspection..."><?= isset($contract) ? htmlspecialchars($contract->payment_terms) : '' ?></textarea>
                                <div class="help-text">Specify payment schedule and terms</div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-sticky-note"></i> Additional Information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="terms_conditions" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="4"
                                          placeholder="Standard terms and conditions for this contract..."><?= isset($contract) ? htmlspecialchars($contract->terms_conditions) : '' ?></textarea>
                                <div class="help-text">Legal terms and conditions</div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Notes & Comments</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"
                                          placeholder="Any additional notes, special requirements, or important information..."><?= isset($contract) ? htmlspecialchars($contract->notes) : '' ?></textarea>
                                <div class="help-text">Internal notes and comments about the contract</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <div>
                                    <?php if (isset($contract)): ?>
                                        <a href="<?= admin_url('ella_contractors/pdf/contract/' . $contract->id) ?>" 
                                           class="btn btn-outline-danger me-2">
                                            <i class="fas fa-file-pdf"></i> Generate PDF
                                        </a>
                                        <a href="<?= admin_url('ella_contractors/presentation/contract/' . $contract->id) ?>" 
                                           class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-file-powerpoint"></i> Generate PPT
                                        </a>
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> 
                                        <?= isset($contract) ? 'Update Contract' : 'Create Contract' ?>
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
        document.getElementById('contractForm').addEventListener('submit', function(e) {
            const contractorId = document.getElementById('contractor_id').value;
            const title = document.getElementById('title').value;
            const fixedAmount = document.getElementById('fixed_amount').value;
            const status = document.getElementById('status').value;

            if (!contractorId || !title || !fixedAmount || !status) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *');
                return false;
            }

            if (parseFloat(fixedAmount) <= 0) {
                e.preventDefault();
                alert('Contract value must be greater than 0');
                return false;
            }

            // Date validation
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                alert('Start date cannot be after end date');
                return false;
            }
        });

        // Auto-generate contract number if empty
        document.getElementById('title').addEventListener('blur', function() {
            const contractNumber = document.getElementById('contract_number');
            if (!contractNumber.value && this.value) {
                const projectTitle = this.value.replace(/[^a-zA-Z0-9]/g, '').substring(0, 8).toUpperCase();
                const year = new Date().getFullYear();
                contractNumber.value = `CON-${year}-${projectTitle}`;
            }
        });
    </script>
</body>
</html>
