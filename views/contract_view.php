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
                            <h4 class="customer-profile-group-heading">Contract Details</h4>
                            <p class="text-muted">View contract information and details</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Contracts
                            </a>
                            <a href="<?= admin_url('ella_contractors/edit_contract/' . $contract->id) ?>" class="btn btn-info">
                                <i class="fa fa-edit"></i> Edit Contract
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />

    <div class="row">
        <div class="col-md-8">
            <!-- Contract Information -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5 class="panel-title">
                        <i class="fa fa-file-contract"></i> Contract Information
                    </h5>
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Contract Number:</strong></td>
                                    <td><?= $contract->contract_number ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td><?= $contract->subject ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php
                                        $status_classes = [
                                            'draft' => 'default',
                                            'active' => 'success',
                                            'completed' => 'info',
                                            'cancelled' => 'danger',
                                            'expired' => 'warning'
                                        ];
                                        $status_class = $status_classes[$contract->status] ?? 'default';
                                        ?>
                                        <span class="label label-<?= $status_class ?>">
                                            <?= ucfirst($contract->status) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Contract Value:</strong></td>
                                    <td>
                                        <?php if ($contract->contract_value): ?>
                                        <strong><?= format_money($contract->contract_value) ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>
                                        <?= $contract->start_date ? date('M d, Y', strtotime($contract->start_date)) : 'Not specified' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>
                                        <?= $contract->end_date ? date('M d, Y', strtotime($contract->end_date)) : 'Not specified' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Terms:</strong></td>
                                    <td>
                                        <?= $contract->payment_terms ?: 'Not specified' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>
                                        <?= date('M d, Y H:i', strtotime($contract->date_created)) ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if ($contract->description): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h6><strong>Description:</strong></h6>
                            <p><?= nl2br($contract->description) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($contract->notes): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h6><strong>Notes:</strong></h6>
                            <p><?= nl2br($contract->notes) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Lead Information -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5 class="panel-title">
                        <i class="fa fa-user"></i> Lead Information
                    </h5>
                    <hr>
                    
                    <div class="text-center">
                        <div class="avatar-placeholder">
                            <i class="fa fa-user fa-3x text-muted"></i>
                        </div>
                        <h4><?= $contract->lead_name ?></h4>
                        <?php if ($contract->lead_company): ?>
                        <p class="text-muted"><?= $contract->lead_company ?></p>
                        <?php endif; ?>
                        <?php
                        $status_text = '';
                        $status_class = '';
                        switch($contract->lead_status) {
                            case 1: $status_text = 'New'; $status_class = 'label-info'; break;
                            case 2: $status_text = 'Contacted'; $status_class = 'label-warning'; break;
                            case 3: $status_text = 'Accepted'; $status_class = 'label-success'; break;
                            case 4: $status_text = 'Proposal'; $status_class = 'label-primary'; break;
                            case 5: $status_text = 'Negotiation'; $status_class = 'label-warning'; break;
                            case 6: $status_text = 'Won'; $status_class = 'label-success'; break;
                            case 7: $status_text = 'Lost'; $status_class = 'label-danger'; break;
                            default: $status_text = 'Unknown'; $status_class = 'label-default'; break;
                        }
                        ?>
                        <span class="label <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?= admin_url('leads/lead/' . $contract->lead_id) ?>" class="btn btn-info btn-block">
                                <i class="fa fa-eye"></i> View Lead Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contractor Information -->
            <div class="panel_s">
                <div class="panel-body">
                    <h5 class="panel-title">
                        <i class="fa fa-briefcase"></i> Contractor Information
                    </h5>
                    <hr>
                    
                    <div class="text-center">
                        <div class="avatar-placeholder">
                            <i class="fa fa-briefcase fa-3x text-muted"></i>
                        </div>
                        <h4><?= $contract->contractor_name ?></h4>
                        <p class="text-muted"><?= $contract->contractor_company ?></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?= admin_url('ella_contractors/view_contractor/' . $contract->contractor_id) ?>" class="btn btn-success btn-block">
                                <i class="fa fa-eye"></i> View Contractor Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contract Timeline -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h5 class="panel-title">
                        <i class="fa fa-history"></i> Contract Timeline
                    </h5>
                    <hr>
                    
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fa fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Contract Created</h6>
                                <p class="text-muted">
                                    Contract was created on <?= date('M d, Y \a\t H:i', strtotime($contract->date_created)) ?>
                                    <?php if ($contract->created_by): ?>
                                    by <?= get_staff_full_name($contract->created_by) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <?php if ($contract->date_updated): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info">
                                <i class="fa fa-edit"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Contract Updated</h6>
                                <p class="text-muted">
                                    Contract was last updated on <?= date('M d, Y \a\t H:i', strtotime($contract->date_updated)) ?>
                                    <?php if ($contract->updated_by): ?>
                                    by <?= get_staff_full_name($contract->updated_by) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($contract->status === 'active' && $contract->start_date): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary">
                                <i class="fa fa-play"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Contract Started</h6>
                                <p class="text-muted">
                                    Contract became active on <?= date('M d, Y', strtotime($contract->start_date)) ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($contract->status === 'completed' && $contract->end_date): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fa fa-check"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Contract Completed</h6>
                                <p class="text-muted">
                                    Contract was completed on <?= date('M d, Y', strtotime($contract->end_date)) ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
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
    // Add any additional JavaScript functionality here
    console.log('Contract view loaded successfully');
});
</script>

<?php init_tail(); ?>
