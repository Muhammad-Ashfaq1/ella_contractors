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
                            <p class="text-muted">Contractor profile and information</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Contractors
                            </a>
                            <a href="<?= admin_url('ella_contractors/edit_contractor/' . $contractor->id) ?>" class="btn btn-primary">
                                <i class="fa fa-pencil"></i> Edit Contractor
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />

                    <!-- Contractor Header -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="contractor-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h2><?= $contractor->company_name ?></h2>
                                        <p class="text-muted"><?= $contractor->contact_person ?></p>
                                        <div class="contractor-meta">
                                            <span class="label label-<?= $contractor->status == 'active' ? 'success' : ($contractor->status == 'inactive' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($contractor->status) ?>
                                            </span>
                                            <?php if ($contractor->rating): ?>
                                            <span class="rating-display">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fa fa-star<?= $i <= $contractor->rating ? '' : '-o' ?> text-warning"></i>
                                                <?php endfor; ?>
                                                <span class="rating-text"><?= $contractor->rating ?>/5</span>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <div class="contractor-actions">
                                            <a href="mailto:<?= $contractor->email ?>" class="btn btn-info">
                                                <i class="fa fa-envelope"></i> Send Email
                                            </a>
                                            <?php if ($contractor->phone): ?>
                                            <a href="tel:<?= $contractor->phone ?>" class="btn btn-success">
                                                <i class="fa fa-phone"></i> Call
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <!-- Contractor Information -->
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8">
                            <!-- Contact Information -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-phone"></i> Contact Information
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Email:</label>
                                                <div><a href="mailto:<?= $contractor->email ?>"><?= $contractor->email ?></a></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Phone:</label>
                                                <div>
                                                    <?php if ($contractor->phone): ?>
                                                        <a href="tel:<?= $contractor->phone ?>"><?= $contractor->phone ?></a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not provided</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Mobile:</label>
                                                <div>
                                                    <?php if ($contractor->mobile): ?>
                                                        <a href="tel:<?= $contractor->mobile ?>"><?= $contractor->mobile ?></a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not provided</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-map-marker"></i> Address Information
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <?php if ($contractor->address || $contractor->city || $contractor->state || $contractor->zip_code || $contractor->country): ?>
                                    <div class="address-block">
                                        <?php if ($contractor->address): ?>
                                        <div><?= $contractor->address ?></div>
                                        <?php endif; ?>
                                        
                                        <div class="address-details">
                                            <?php
                                            $address_parts = [];
                                            if ($contractor->city) $address_parts[] = $contractor->city;
                                            if ($contractor->state) $address_parts[] = $contractor->state;
                                            if ($contractor->zip_code) $address_parts[] = $contractor->zip_code;
                                            if ($contractor->country) $address_parts[] = $contractor->country;
                                            
                                            if (!empty($address_parts)) {
                                                echo implode(', ', $address_parts);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-muted">No address information provided</div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Business Information -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-briefcase"></i> Business Information
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Specialization:</label>
                                                <div>
                                                    <?php if ($contractor->specialization): ?>
                                                        <span class="label label-info"><?= $contractor->specialization ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not specified</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Hourly Rate:</label>
                                                <div>
                                                    <?php if ($contractor->hourly_rate): ?>
                                                        <strong>$<?= number_format($contractor->hourly_rate, 2) ?>/hr</strong>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not set</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Payment Terms:</label>
                                                <div>
                                                    <?php if ($contractor->payment_terms): ?>
                                                        <?= $contractor->payment_terms ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not specified</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Tax ID:</label>
                                                <div>
                                                    <?php if ($contractor->tax_id): ?>
                                                        <?= $contractor->tax_id ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not provided</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item">
                                                <label>Business License:</label>
                                                <div>
                                                    <?php if ($contractor->business_license): ?>
                                                        <?= $contractor->business_license ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not provided</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance Information -->
                            <?php if ($contractor->insurance_info): ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-shield"></i> Insurance Information
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="insurance-info">
                                        <?= nl2br($contractor->insurance_info) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Notes -->
                            <?php if ($contractor->notes): ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-sticky-note-o"></i> Notes
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="notes-content">
                                        <?= nl2br($contractor->notes) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-4">
                            <!-- Quick Stats -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-info-circle"></i> Quick Information
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="quick-stats">
                                        <div class="stat-item">
                                            <label>Status:</label>
                                            <span class="label label-<?= $contractor->status == 'active' ? 'success' : ($contractor->status == 'inactive' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($contractor->status) ?>
                                            </span>
                                        </div>
                                        <div class="stat-item">
                                            <label>Rating:</label>
                                            <span>
                                                <?php if ($contractor->rating): ?>
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa fa-star<?= $i <= $contractor->rating ? '' : '-o' ?> text-warning"></i>
                                                    <?php endfor; ?>
                                                    <span class="rating-text"><?= $contractor->rating ?>/5</span>
                                                <?php else: ?>
                                                    <span class="text-muted">No rating</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="stat-item">
                                            <label>Created:</label>
                                            <span><?= date('M j, Y', strtotime($contractor->created_at)) ?></span>
                                        </div>
                                        <?php if ($contractor->updated_at): ?>
                                        <div class="stat-item">
                                            <label>Last Updated:</label>
                                            <span><?= date('M j, Y', strtotime($contractor->updated_at)) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-cogs"></i> Actions
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="action-buttons">
                                        <a href="<?= admin_url('ella_contractors/edit_contractor/' . $contractor->id) ?>" class="btn btn-primary btn-block">
                                            <i class="fa fa-pencil"></i> Edit Contractor
                                        </a>
                                        <a href="mailto:<?= $contractor->email ?>" class="btn btn-info btn-block">
                                            <i class="fa fa-envelope"></i> Send Email
                                        </a>
                                        <?php if ($contractor->phone): ?>
                                        <a href="tel:<?= $contractor->phone ?>" class="btn btn-success btn-block">
                                            <i class="fa fa-phone"></i> Call Phone
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($contractor->mobile): ?>
                                        <a href="tel:<?= $contractor->mobile ?>" class="btn btn-success btn-block">
                                            <i class="fa fa-mobile"></i> Call Mobile
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-default btn-block">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Card -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-address-card"></i> Contact Card
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="contact-card">
                                        <div class="contact-name"><?= $contractor->contact_person ?></div>
                                        <div class="contact-company"><?= $contractor->company_name ?></div>
                                        <div class="contact-email">
                                            <i class="fa fa-envelope"></i> <?= $contractor->email ?>
                                        </div>
                                        <?php if ($contractor->phone): ?>
                                        <div class="contact-phone">
                                            <i class="fa fa-phone"></i> <?= $contractor->phone ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($contractor->mobile): ?>
                                        <div class="contact-mobile">
                                            <i class="fa fa-mobile"></i> <?= $contractor->mobile ?>
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
</div>

<style>
.contractor-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.contractor-header h2 {
    margin: 0 0 10px 0;
    color: white;
}

.contractor-meta {
    margin-top: 15px;
}

.contractor-meta .label {
    margin-right: 10px;
}

.rating-display {
    margin-left: 10px;
}

.rating-text {
    margin-left: 5px;
    font-weight: bold;
}

.contractor-actions {
    margin-top: 20px;
}

.contractor-actions .btn {
    margin-left: 10px;
}

.info-item {
    margin-bottom: 15px;
}

.info-item label {
    font-weight: bold;
    color: #666;
    display: block;
    margin-bottom: 5px;
}

.address-block {
    line-height: 1.6;
}

.address-details {
    color: #666;
    margin-top: 5px;
}

.quick-stats .stat-item {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.quick-stats .stat-item:last-child {
    border-bottom: none;
}

.quick-stats .stat-item label {
    font-weight: bold;
    color: #666;
    display: block;
    margin-bottom: 5px;
}

.action-buttons .btn {
    margin-bottom: 10px;
}

.contact-card {
    text-align: center;
    padding: 20px 0;
}

.contact-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 5px;
}

.contact-company {
    color: #666;
    margin-bottom: 15px;
}

.contact-email,
.contact-phone,
.contact-mobile {
    margin-bottom: 8px;
    color: #333;
}

.contact-email i,
.contact-phone i,
.contact-mobile i {
    margin-right: 8px;
    color: #666;
}

.insurance-info,
.notes-content {
    line-height: 1.6;
    white-space: pre-line;
}
</style>

<?php init_tail(); ?>
