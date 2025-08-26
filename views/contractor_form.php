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
                            <p class="text-muted">
                                <?= isset($contractor) ? 'Update contractor information' : 'Add a new contractor to your system' ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Contractors
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />

                    <!-- Error Messages -->
                    <?php if (isset($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        <?= $errors ?>
                    </div>
                    <?php endif; ?>

                    <!-- Contractor Form -->
                    <?php echo form_open(admin_url('ella_contractors/' . (isset($contractor) ? 'edit_contractor/' . $contractor->id : 'add_contractor')), array('class' => 'form-horizontal')); ?>
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="form-section-heading">
                                    <i class="fa fa-building"></i> Basic Information
                                </h4>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Company Name <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" name="company_name" class="form-control" value="<?= isset($contractor) ? $contractor->company_name : set_value('company_name') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Contact Person <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" name="contact_person" class="form-control" value="<?= isset($contractor) ? $contractor->contact_person : set_value('contact_person') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Email <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="email" name="email" class="form-control" value="<?= isset($contractor) ? $contractor->email : set_value('email') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Status <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <select name="status" class="form-control" required>
                                            <option value="active" <?= (isset($contractor) && $contractor->status == 'active') || set_value('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= (isset($contractor) && $contractor->status == 'inactive') || set_value('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            <option value="pending" <?= (isset($contractor) && $contractor->status == 'pending') || set_value('status') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="blacklisted" <?= (isset($contractor) && $contractor->status == 'blacklisted') || set_value('status') == 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="form-section-heading">
                                    <i class="fa fa-phone"></i> Contact Information
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Phone</label>
                                    <div class="col-md-8">
                                        <input type="text" name="phone" class="form-control" value="<?= isset($contractor) ? $contractor->phone : set_value('phone') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Mobile</label>
                                    <div class="col-md-8">
                                        <input type="text" name="mobile" class="form-control" value="<?= isset($contractor) ? $contractor->mobile : set_value('mobile') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="form-section-heading">
                                    <i class="fa fa-map-marker"></i> Address Information
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Address</label>
                                    <div class="col-md-10">
                                        <textarea name="address" class="form-control" rows="3"><?= isset($contractor) ? $contractor->address : set_value('address') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-4">City</label>
                                    <div class="col-md-8">
                                        <input type="text" name="city" class="form-control" value="<?= isset($contractor) ? $contractor->city : set_value('city') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-4">State</label>
                                    <div class="col-md-8">
                                        <input type="text" name="state" class="form-control" value="<?= isset($contractor) ? $contractor->state : set_value('state') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-4">ZIP Code</label>
                                    <div class="col-md-8">
                                        <input type="text" name="zip_code" class="form-control" value="<?= isset($contractor) ? $contractor->zip_code : set_value('zip_code') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Country</label>
                                    <div class="col-md-8">
                                        <input type="text" name="country" class="form-control" value="<?= isset($contractor) ? $contractor->country : set_value('country') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="form-section-heading">
                                    <i class="fa fa-briefcase"></i> Business Information
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Tax ID</label>
                                    <div class="col-md-8">
                                        <input type="text" name="tax_id" class="form-control" value="<?= isset($contractor) ? $contractor->tax_id : set_value('tax_id') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Business License</label>
                                    <div class="col-md-8">
                                        <input type="text" name="business_license" class="form-control" value="<?= isset($contractor) ? $contractor->business_license : set_value('business_license') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Specialization</label>
                                    <div class="col-md-8">
                                        <input type="text" name="specialization" class="form-control" value="<?= isset($contractor) ? $contractor->specialization : set_value('specialization') ?>" placeholder="e.g., Plumbing, Electrical, HVAC">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Hourly Rate</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" name="hourly_rate" class="form-control" value="<?= isset($contractor) ? $contractor->hourly_rate : set_value('hourly_rate') ?>" step="0.01" min="0">
                                            <span class="input-group-addon">/hr</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Payment Terms</label>
                                    <div class="col-md-8">
                                        <input type="text" name="payment_terms" class="form-control" value="<?= isset($contractor) ? $contractor->payment_terms : set_value('payment_terms') ?>" placeholder="e.g., Net 30, Net 60">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Rating</label>
                                    <div class="col-md-8">
                                        <select name="rating" class="form-control">
                                            <option value="">No Rating</option>
                                            <option value="1" <?= (isset($contractor) && $contractor->rating == 1) || set_value('rating') == 1 ? 'selected' : '' ?>>1 Star</option>
                                            <option value="2" <?= (isset($contractor) && $contractor->rating == 2) || set_value('rating') == 2 ? 'selected' : '' ?>>2 Stars</option>
                                            <option value="3" <?= (isset($contractor) && $contractor->rating == 3) || set_value('rating') == 3 ? 'selected' : '' ?>>3 Stars</option>
                                            <option value="4" <?= (isset($contractor) && $contractor->rating == 4) || set_value('rating') == 4 ? 'selected' : '' ?>>4 Stars</option>
                                            <option value="5" <?= (isset($contractor) && $contractor->rating == 5) || set_value('rating') == 5 ? 'selected' : '' ?>>5 Stars</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Insurance Info</label>
                                    <div class="col-md-10">
                                        <textarea name="insurance_info" class="form-control" rows="3" placeholder="Insurance details, policy numbers, coverage information..."><?= isset($contractor) ? $contractor->insurance_info : set_value('insurance_info') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Notes</label>
                                    <div class="col-md-10">
                                        <textarea name="notes" class="form-control" rows="4" placeholder="Additional notes, special requirements, or important information..."><?= isset($contractor) ? $contractor->notes : set_value('notes') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <div class="text-right">
                                    <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> 
                                        <?= isset($contractor) ? 'Update Contractor' : 'Add Contractor' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form validation
    $('form').submit(function() {
        var requiredFields = $('input[required], select[required]');
        var isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            alert('Please fill in all required fields.');
            return false;
        }
        
        return true;
    });
    
    // Remove validation styling on input
    $('input, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>

<?php init_tail(); ?>
