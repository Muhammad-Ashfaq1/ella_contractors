<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-<?= isset($contractor) ? 'edit' : 'plus' ?>"></i> 
                            <?= isset($contractor) ? 'Edit Contractor' : 'Add New Contractor' ?>
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
                        
                        <form method="POST" class="needs-validation" id="contractor-form" novalidate>
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
                                        <label for="company_name" class="control-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" 
                                               value="<?= htmlspecialchars($contractor->company_name ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_person" class="control-label">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                               value="<?= htmlspecialchars($contractor->contact_person ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="control-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($contractor->email ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="control-label">Phone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?= htmlspecialchars($contractor->phone ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-map-marker-alt"></i> Address Information</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address" class="control-label">Street Address</label>
                                        <input type="text" class="form-control" id="address" name="address" 
                                               value="<?= htmlspecialchars($contractor->address ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city" class="control-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" 
                                               value="<?= htmlspecialchars($contractor->city ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state" class="control-label">State/Province</label>
                                        <select class="form-control" id="state" name="state">
                                            <option value="">Select State</option>
                                            <?php if (isset($states)): ?>
                                                <?php foreach ($states as $code => $name): ?>
                                                    <option value="<?= $code ?>" <?= (($contractor->state ?? '') == $code) ? 'selected' : '' ?>>
                                                        <?= $name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="zip_code" class="control-label">ZIP/Postal Code</label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                               value="<?= htmlspecialchars($contractor->zip_code ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country" class="control-label">Country</label>
                                        <select class="form-control" id="country" name="country">
                                            <option value="">Select Country</option>
                                            <?php if (isset($countries)): ?>
                                                <?php foreach ($countries as $code => $name): ?>
                                                    <option value="<?= $code ?>" <?= (($contractor->country ?? '') == $code) ? 'selected' : '' ?>>
                                                        <?= $name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website" class="control-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website" 
                                               value="<?= htmlspecialchars($contractor->website ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Business Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold"><i class="fa fa-briefcase"></i> Business Information</h4>
                                    <hr class="hr-panel-separator" />
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_id" class="control-label">Tax ID</label>
                                        <input type="text" class="form-control" id="tax_id" name="tax_id" 
                                               value="<?= htmlspecialchars($contractor->tax_id ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="business_license" class="control-label">Business License</label>
                                        <input type="text" class="form-control" id="business_license" name="business_license" 
                                               value="<?= htmlspecialchars($contractor->business_license ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="insurance_info" class="control-label">Insurance Information</label>
                                        <input type="text" class="form-control" id="insurance_info" name="insurance_info" 
                                               value="<?= htmlspecialchars($contractor->insurance_info ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialties" class="control-label">Specialties</label>
                                        <input type="text" class="form-control" id="specialties" name="specialties" 
                                               value="<?= htmlspecialchars($contractor->specialties ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hourly_rate" class="control-label">Hourly Rate ($)</label>
                                        <input type="number" step="0.01" min="0" class="form-control" id="hourly_rate" name="hourly_rate" 
                                               value="<?= htmlspecialchars($contractor->hourly_rate ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="control-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <?php if (isset($status_options)): ?>
                                                <?php foreach ($status_options as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= (($contractor->status ?? 'pending') == $value) ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="active" <?= (($contractor->status ?? 'active') == 'active') ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= (($contractor->status ?? '') == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                                <option value="pending" <?= (($contractor->status ?? '') == 'pending') ? 'selected' : '' ?>>Pending</option>
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
                                        <textarea class="form-control" id="notes" name="notes" rows="4"><?= htmlspecialchars($contractor->notes ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr class="hr-panel-separator" />
                                    <div class="btn-group pull-right">
                                        <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa fa-check"></i> <?= isset($contractor) ? 'Update Contractor' : 'Add Contractor' ?>
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
