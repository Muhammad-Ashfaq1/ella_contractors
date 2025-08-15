<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-<?= isset($contract) ? 'edit' : 'plus' ?>"></i> 
                            <?= isset($contract) ? 'Edit Contract' : 'Add New Contract' ?>
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
                        
                        <form method="POST" class="needs-validation" novalidate>
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
                                                            <?= (isset($contract) && $contract->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($contractor->company_name) ?> - <?= htmlspecialchars($contractor->contact_person) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title" class="control-label">Contract Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" required
                                               value="<?= isset($contract) ? htmlspecialchars($contract->title) : '' ?>"
                                               placeholder="e.g., Office Renovation Contract">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract_number" class="control-label">Contract Number</label>
                                        <input type="text" class="form-control" id="contract_number" name="contract_number"
                                               value="<?= isset($contract) ? htmlspecialchars($contract->contract_number) : '' ?>"
                                               placeholder="e.g., CON-2024-001">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="control-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <?php if (isset($status_options)): ?>
                                                <?php foreach ($status_options as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= (($contract->status ?? 'draft') == $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="draft" <?= (($contract->status ?? 'draft') == 'draft') ? 'selected' : '' ?>>Draft</option>
                                                <option value="active" <?= (($contract->status ?? '') == 'active') ? 'selected' : '' ?>>Active</option>
                                                <option value="completed" <?= (($contract->status ?? '') == 'completed') ? 'selected' : '' ?>>Completed</option>
                                                <option value="terminated" <?= (($contract->status ?? '') == 'terminated') ? 'selected' : '' ?>>Terminated</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="control-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"
                                                  placeholder="Detailed description of the contract scope and terms..."><?= isset($contract) ? htmlspecialchars($contract->description) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Timeline -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-calendar-alt"></i> Contract Timeline</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date" class="control-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required
                                               value="<?= isset($contract) ? $contract->start_date : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date" class="control-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                               value="<?= isset($contract) ? $contract->end_date : '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Financial Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-dollar-sign"></i> Financial Information</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hourly_rate" class="control-label">Hourly Rate ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="hourly_rate" name="hourly_rate"
                                               value="<?= isset($contract) ? htmlspecialchars($contract->hourly_rate) : '' ?>"
                                               placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estimated_hours" class="control-label">Estimated Hours</label>
                                        <input type="number" min="0" class="form-control" id="estimated_hours" name="estimated_hours"
                                               value="<?= isset($contract) ? htmlspecialchars($contract->estimated_hours) : '' ?>"
                                               placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fixed_amount" class="control-label">Fixed Amount ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="fixed_amount" name="fixed_amount"
                                               value="<?= isset($contract) ? htmlspecialchars($contract->fixed_amount) : '' ?>"
                                               placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_terms" class="control-label">Payment Terms</label>
                                        <input type="text" class="form-control" id="payment_terms" name="payment_terms"
                                               value="<?= isset($contract) ? htmlspecialchars($contract->payment_terms) : '' ?>"
                                               placeholder="e.g., Net 30, 50% upfront">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="terms_conditions" class="control-label">Terms & Conditions</label>
                                        <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3"
                                                  placeholder="Contract terms and conditions..."><?= isset($contract) ? htmlspecialchars($contract->terms_conditions) : '' ?></textarea>
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
                                                  placeholder="Additional notes about the contract..."><?= isset($contract) ? htmlspecialchars($contract->notes) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr class="hr-panel-separator" />
                                    <div class="btn-group pull-right">
                                        <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa fa-check"></i> <?= isset($contract) ? 'Update Contract' : 'Add Contract' ?>
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
