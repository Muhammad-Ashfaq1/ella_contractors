<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-<?= isset($project) ? 'edit' : 'plus' ?>"></i> 
                            <?= isset($project) ? 'Edit Project' : 'Add New Project' ?>
                        </h4>
                    </div>
                </div>
                
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (isset($errors) && $errors): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" id="project-form" novalidate>
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>" />
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-info-circle"></i> Basic Information</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contractor_id" class="control-label">Contractor <span class="text-danger">*</span></label>
                                        <select class="form-control" id="contractor_id" name="contractor_id" required>
                                            <option value="">Select Contractor</option>
                                            <?php if (isset($contractors)): ?>
                                                <?php foreach ($contractors as $contractor): ?>
                                                    <option value="<?= $contractor->id ?>" 
                                                            <?= (isset($project) && $project->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($contractor->company_name) ?> - <?= htmlspecialchars($contractor->contact_person) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Project Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                               value="<?= isset($project) ? htmlspecialchars($project->name) : '' ?>"
                                               placeholder="e.g., Office Building Renovation">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="control-label">Project Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" 
                                                  placeholder="Detailed description of the project scope, objectives, and deliverables..."><?= isset($project) ? htmlspecialchars($project->description) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Project Details -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-cogs"></i> Project Details</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location" class="control-label">Project Location</label>
                                        <input type="text" class="form-control" id="location" name="location"
                                               value="<?= isset($project) ? htmlspecialchars($project->location) : '' ?>"
                                               placeholder="e.g., 123 Main Street, New York, NY">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract_id" class="control-label">Related Contract</label>
                                        <select class="form-control" id="contract_id" name="contract_id">
                                            <option value="">Select Contract (Optional)</option>
                                            <?php if (isset($contracts)): ?>
                                                <?php foreach ($contracts as $contract): ?>
                                                    <option value="<?= $contract->id ?>" 
                                                            <?= (isset($project) && $project->contract_id == $contract->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($contract->title) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Timeline -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-calendar-alt"></i> Project Timeline</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date" class="control-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required
                                               value="<?= isset($project) ? $project->start_date : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date" class="control-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                               value="<?= isset($project) ? $project->end_date : '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Budget and Hours -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-dollar-sign"></i> Budget and Hours</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="budget" class="control-label">Budget ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="budget" name="budget"
                                               value="<?= isset($project) ? htmlspecialchars($project->budget) : '' ?>"
                                               placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estimated_hours" class="control-label">Estimated Hours</label>
                                        <input type="number" min="0" class="form-control" id="estimated_hours" name="estimated_hours"
                                               value="<?= isset($project) ? htmlspecialchars($project->estimated_hours) : '' ?>"
                                               placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="actual_hours" class="control-label">Actual Hours</label>
                                        <input type="number" min="0" class="form-control" id="actual_hours" name="actual_hours"
                                               value="<?= isset($project) ? htmlspecialchars($project->actual_hours) : '' ?>"
                                               placeholder="0">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status and Priority -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-tasks"></i> Status and Priority</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="control-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <?php if (isset($status_options)): ?>
                                                <?php foreach ($status_options as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= (($project->status ?? 'planning') == $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="planning" <?= (($project->status ?? 'planning') == 'planning') ? 'selected' : '' ?>>Planning</option>
                                                <option value="active" <?= (($project->status ?? '') == 'active') ? 'selected' : '' ?>>Active</option>
                                                <option value="on_hold" <?= (($project->status ?? '') == 'on_hold') ? 'selected' : '' ?>>On Hold</option>
                                                <option value="completed" <?= (($project->status ?? '') == 'completed') ? 'selected' : '' ?>>Completed</option>
                                                <option value="cancelled" <?= (($project->status ?? '') == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="priority" class="control-label">Priority</label>
                                        <select class="form-control" id="priority" name="priority">
                                            <?php if (isset($priority_options)): ?>
                                                <?php foreach ($priority_options as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= (($project->priority ?? 'medium') == $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="low" <?= (($project->priority ?? '') == 'low') ? 'selected' : '' ?>>Low</option>
                                                <option value="medium" <?= (($project->priority ?? 'medium') == 'medium') ? 'selected' : '' ?>>Medium</option>
                                                <option value="high" <?= (($project->priority ?? '') == 'high') ? 'selected' : '' ?>>High</option>
                                                <option value="urgent" <?= (($project->priority ?? '') == 'urgent') ? 'selected' : '' ?>>Urgent</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-sticky-note"></i> Additional Information</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes" class="control-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="4"
                                                  placeholder="Additional notes about the project..."><?= isset($project) ? htmlspecialchars($project->notes) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr class="hr-panel-separator" />
                                    <div class="btn-group pull-right">
                                        <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa fa-check"></i> <?= isset($project) ? 'Update Project' : 'Add Project' ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<!-- Load Ella Contractors JavaScript -->
<script src="<?= module_dir_url('ella_contractors', 'assets/js/ella_contractors.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Initialize the module
        if (typeof EllaContractors !== 'undefined') {
            EllaContractors.init();
        }
    });
</script>
