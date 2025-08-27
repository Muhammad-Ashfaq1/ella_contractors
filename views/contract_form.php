<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Ensure jQuery is loaded before any CSRF setup
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
}

// Override the problematic CSRF function to prevent errors
window.csrf_jquery_ajax_setup = function() {
    // Do nothing - prevent the error from general_helper.php
    return false;
};
</script>

<?php init_head(); ?>

<!-- Include module CSS -->
<link rel="stylesheet" href="<?php echo base_url('modules/ella_contractors/assets/css/ella_contractors.css'); ?>">

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <div class="clearfix"></div>
                    
                    <!-- Page Header -->
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="customer-profile-group-heading"><?= $title ?></h4>
                            <p class="text-muted">Create or edit contract details</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Contracts
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />

    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <?php echo form_open(admin_url('ella_contractors/' . (isset($contract) ? 'edit_contract/' . $contract->id : 'add_contract')), array('class' => 'form-horizontal', 'id' => 'contract-form')); ?>
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h5 class="form-section-title">
                            <i class="fa fa-info-circle"></i> Basic Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Lead <span class="text-danger">*</span></label>
                                    <select name="lead_id" class="form-control" required>
                                        <option value="">Select Lead</option>
                                        <?php foreach ($leads as $lead): ?>
                                        <?php
                                        $status_text = '';
                                        $status_class = '';
                                        switch($lead->status) {
                                            case 1: $status_text = ' (New)'; $status_class = 'text-info'; break;
                                            case 2: $status_text = ' (Contacted)'; $status_class = 'text-warning'; break;
                                            case 3: $status_text = ' (Accepted)'; $status_class = 'text-success'; break;
                                            case 4: $status_text = ' (Proposal)'; $status_class = 'text-primary'; break;
                                            case 5: $status_text = ' (Negotiation)'; $status_class = 'text-warning'; break;
                                            case 6: $status_text = ' (Won)'; $status_class = 'text-success'; break;
                                            case 7: $status_text = ' (Lost)'; $status_class = 'text-danger'; break;
                                            default: $status_text = ' (Unknown)'; $status_class = 'text-muted'; break;
                                        }
                                        ?>
                                        <option value="<?= $lead->id ?>" <?= (isset($contract) && $contract->lead_id == $lead->id) ? 'selected' : '' ?>>
                                            <?= $lead->name ?> <?= $lead->company ? ' (' . $lead->company . ')' : '' ?><span class="<?= $status_class ?>"><?= $status_text ?></span>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">All leads are shown. Accepted leads (status 3) are recommended for contracts.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Contractor <span class="text-danger">*</span></label>
                                    <select name="contractor_id" class="form-control" required>
                                        <option value="">Select Contractor</option>
                                        <?php foreach ($contractors as $contractor): ?>
                                        <option value="<?= $contractor->id ?>" <?= (isset($contract) && $contract->contractor_id == $contractor->id) ? 'selected' : '' ?>>
                                            <?= $contractor->company_name ?> - <?= $contractor->specialties ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" value="<?= isset($contract) ? $contract->subject : '' ?>" required maxlength="255">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4"><?= isset($contract) ? $contract->description : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Details -->
                    <div class="form-section">
                        <h5 class="form-section-title">
                            <i class="fa fa-calculator"></i> Contract Details
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Contract Value</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" name="contract_value" class="form-control" value="<?= isset($contract) ? $contract->contract_value : '' ?>" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= isset($contract) && $contract->start_date ? date('Y-m-d', strtotime($contract->start_date)) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= isset($contract) && $contract->end_date ? date('Y-m-d', strtotime($contract->end_date)) : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="draft" <?= (isset($contract) && $contract->status == 'draft') ? 'selected' : '' ?>>Draft</option>
                                        <option value="active" <?= (isset($contract) && $contract->status == 'active') ? 'selected' : '' ?>>Active</option>
                                        <option value="completed" <?= (isset($contract) && $contract->status == 'completed') ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= (isset($contract) && $contract->status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                        <option value="expired" <?= (isset($contract) && $contract->status == 'expired') ? 'selected' : '' ?>>Expired</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Payment Terms</label>
                                    <input type="text" name="payment_terms" class="form-control" value="<?= isset($contract) ? $contract->payment_terms : '' ?>" placeholder="e.g., Net 30, 50% upfront">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <h5 class="form-section-title">
                            <i class="fa fa-sticky-note"></i> Additional Information
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"><?= isset($contract) ? $contract->notes : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-section">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> <?= isset($contract) ? 'Update Contract' : 'Create Contract' ?>
                                        </button>
                                        <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

<script>
$(document).ready(function() {
    // Form validation
    $('#contract-form').submit(function(e) {
        var leadId = $('select[name="lead_id"]').val();
        var contractorId = $('select[name="contractor_id"]').val();
        var subject = $('input[name="subject"]').val();
        var status = $('select[name="status"]').val();
        
        if (!leadId) {
            alert('Please select a lead.');
            e.preventDefault();
            return false;
        }
        
        if (!contractorId) {
            alert('Please select a contractor.');
            e.preventDefault();
            return false;
        }
        
        if (!subject.trim()) {
            alert('Please enter a contract subject.');
            e.preventDefault();
            return false;
        }
        
        if (!status) {
            alert('Please select a contract status.');
            e.preventDefault();
            return false;
        }
        
        // Date validation
        var startDate = $('input[name="start_date"]').val();
        var endDate = $('input[name="end_date"]').val();
        
        if (startDate && endDate && startDate > endDate) {
            alert('Start date cannot be after end date.');
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
    // Auto-generate subject if lead and contractor are selected
    $('select[name="lead_id"], select[name="contractor_id"]').change(function() {
        var leadId = $('select[name="lead_id"]').val();
        var contractorId = $('select[name="contractor_id"]').val();
        var subject = $('input[name="subject"]').val();
        
        if (leadId && contractorId && !subject) {
            var leadName = $('select[name="lead_id"] option:selected').text();
            var contractorName = $('select[name="contractor_id"] option:selected').text();
            
            // Extract just the names (remove company info)
            leadName = leadName.split(' (')[0];
            contractorName = contractorName.split(' - ')[0];
            
            var autoSubject = 'Contract between ' + leadName + ' and ' + contractorName;
            $('input[name="subject"]').val(autoSubject);
        }
    });
    
    // Format currency input
    $('input[name="contract_value"]').on('input', function() {
        var value = $(this).val();
        if (value && !isNaN(value)) {
            $(this).val(parseFloat(value).toFixed(2));
        }
    });
});
</script>

<?php init_tail(); ?>
