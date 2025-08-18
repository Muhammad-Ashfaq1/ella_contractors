<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-<?= isset($payment) ? 'edit' : 'plus' ?>"></i> 
                            <?= isset($payment) ? 'Edit Payment' : 'Add New Payment' ?>
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
                        
                        <form method="POST" class="needs-validation" id="payment-form" novalidate>
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
                                                            <?= (isset($payment) && $payment->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($contractor->company_name) ?> - <?= htmlspecialchars($contractor->contact_person) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount" class="control-label">Amount ($) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required
                                               value="<?= isset($payment) ? htmlspecialchars($payment->amount) : '' ?>"
                                               placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Related Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-link"></i> Related Information</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contract_id" class="control-label">Related Contract</label>
                                        <select class="form-control" id="contract_id" name="contract_id">
                                            <option value="">Select Contract (Optional)</option>
                                            <?php if (isset($contracts)): ?>
                                                <?php foreach ($contracts as $contract): ?>
                                                    <option value="<?= $contract->id ?>" 
                                                            <?= (isset($payment) && $payment->contract_id == $contract->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($contract->title) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project_id" class="control-label">Related Project</label>
                                        <select class="form-control" id="project_id" name="project_id">
                                            <option value="">Select Project (Optional)</option>
                                            <?php if (isset($projects)): ?>
                                                <?php foreach ($projects as $project): ?>
                                                    <option value="<?= $project->id ?>" 
                                                            <?= (isset($payment) && $payment->project_id == $project->id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($project->name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Details -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-credit-card"></i> Payment Details</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_date" class="control-label">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="payment_date" name="payment_date" required
                                               value="<?= isset($payment) ? $payment->payment_date : '' ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_date" class="control-label">Due Date</label>
                                        <input type="date" class="form-control" id="due_date" name="due_date"
                                               value="<?= isset($payment) ? $payment->due_date : '' ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method" class="control-label">Payment Method</label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="">Select Payment Method</option>
                                            <option value="check" <?= (($payment->payment_method ?? '') == 'check') ? 'selected' : '' ?>>Check</option>
                                            <option value="bank_transfer" <?= (($payment->payment_method ?? '') == 'bank_transfer') ? 'selected' : '' ?>>Bank Transfer</option>
                                            <option value="credit_card" <?= (($payment->payment_method ?? '') == 'credit_card') ? 'selected' : '' ?>>Credit Card</option>
                                            <option value="cash" <?= (($payment->payment_method ?? '') == 'cash') ? 'selected' : '' ?>>Cash</option>
                                            <option value="other" <?= (($payment->payment_method ?? '') == 'other') ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number" class="control-label">Reference Number</label>
                                        <input type="text" class="form-control" id="reference_number" name="reference_number"
                                               value="<?= isset($payment) ? htmlspecialchars($payment->reference_number) : '' ?>"
                                               placeholder="e.g., Check #1234, Transaction ID">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status and Notes -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-tasks"></i> Status and Notes</h4>
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
                                                    <option value="<?= $value ?>" <?= (($payment->status ?? 'pending') == $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="pending" <?= (($payment->status ?? 'pending') == 'pending') ? 'selected' : '' ?>>Pending</option>
                                                <option value="approved" <?= (($payment->status ?? '') == 'approved') ? 'selected' : '' ?>>Approved</option>
                                                <option value="paid" <?= (($payment->status ?? '') == 'paid') ? 'selected' : '' ?>>Paid</option>
                                                <option value="cancelled" <?= (($payment->status ?? '') == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="notes" class="control-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                                  placeholder="Additional notes about the payment..."><?= isset($payment) ? htmlspecialchars($payment->notes) : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr class="hr-panel-separator" />
                                    <div class="btn-group pull-right">
                                        <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa fa-check"></i> <?= isset($payment) ? 'Update Payment' : 'Add Payment' ?>
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
