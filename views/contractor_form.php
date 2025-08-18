<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 5px 7px #4075A1;
    }

    .card-header-custom {
        background-color: #4075A1;
        color: white;
        padding: 15px 20px;
        border-bottom: none;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
    }

    .card-header-custom h4 {
        margin-bottom: 0;
        font-weight: 600;
    }

    .card-body-custom {
        padding: 2rem;
    }

    .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #4075A1;
        box-shadow: 0 0 0 0.2rem rgba(64, 117, 161, 0.25);
    }

    .btn-primary {
        background-color: #4075A1;
        border-color: #4075A1;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .btn-primary:hover {
        background-color: #36648b;
        border-color: #36648b;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .section-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #4075A1;
    }

    .section-header h5 {
        margin: 0;
        color: #4075A1;
        font-weight: 600;
    }

    .profile-image-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #4075A1;
        margin: 0 auto 15px;
        display: block;
    }

    .file-upload-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .file-upload-wrapper input[type=file] {
        position: absolute;
        left: -9999px;
    }

    .file-upload-btn {
        background-color: #4075A1;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .file-upload-btn:hover {
        background-color: #36648b;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.375rem;
        margin-right: 10px;
    }

    .status-active { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #f8d7da; color: #721c24; }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-suspended { background-color: #e2e3e5; color: #383d41; }
</style>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">
                        <i class="fa fa-user-plus"></i> <?= $title ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header-custom">
                    <h4 class="mb-0">
                        <i class="fa fa-info-circle"></i> Contractor Information
                    </h4>
                </div>
                <div class="card-body-custom">
                    <form method="POST" enctype="multipart/form-data" id="contractorForm">
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5><i class="fa fa-user"></i> Basic Information</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name" class="required-field">Company Name</label>
                                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                                       value="<?= htmlspecialchars($contractor->company_name ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_person" class="required-field">Contact Person</label>
                                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                                       value="<?= htmlspecialchars($contractor->contact_person ?? '') ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="required-field">Email Address</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?= htmlspecialchars($contractor->email ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone" class="required-field">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" 
                                                       value="<?= htmlspecialchars($contractor->phone ?? '') ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="website">Website</label>
                                                <input type="url" class="form-control" id="website" name="website" 
                                                       value="<?= htmlspecialchars($contractor->website ?? '') ?>" 
                                                       placeholder="https://example.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="pending" <?= ($contractor->status ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="active" <?= ($contractor->status ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="inactive" <?= ($contractor->status ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    <option value="suspended" <?= ($contractor->status ?? '') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <?php if (isset($contractor) && $contractor->profile_image): ?>
                                            <img src="<?= base_url($contractor->profile_image) ?>" alt="Profile" class="profile-image-preview" id="profilePreview">
                                        <?php else: ?>
                                            <div class="profile-image-preview bg-secondary d-flex align-items-center justify-content-center" id="profilePreview">
                                                <i class="fa fa-user fa-3x text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="file-upload-wrapper">
                                            <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this)">
                                            <label for="profile_image" class="file-upload-btn">
                                                <i class="fa fa-upload"></i> Upload Photo
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5><i class="fa fa-map-marker"></i> Address Information</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Street Address</label>
                                        <input type="text" class="form-control" id="address" name="address" 
                                               value="<?= htmlspecialchars($contractor->address ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city" 
                                               value="<?= htmlspecialchars($contractor->city ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state">State/Province</label>
                                        <input type="text" class="form-control" id="state" name="state" 
                                               value="<?= htmlspecialchars($contractor->state ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="zip_code">ZIP/Postal Code</label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                               value="<?= htmlspecialchars($contractor->zip_code ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <select class="form-control" id="country" name="country">
                                            <option value="">Select Country</option>
                                            <option value="US" <?= ($contractor->country ?? '') == 'US' ? 'selected' : '' ?>>United States</option>
                                            <option value="CA" <?= ($contractor->country ?? '') == 'CA' ? 'selected' : '' ?>>Canada</option>
                                            <option value="UK" <?= ($contractor->country ?? '') == 'UK' ? 'selected' : '' ?>>United Kingdom</option>
                                            <option value="AU" <?= ($contractor->country ?? '') == 'AU' ? 'selected' : '' ?>>Australia</option>
                                            <option value="DE" <?= ($contractor->country ?? '') == 'DE' ? 'selected' : '' ?>>Germany</option>
                                            <option value="FR" <?= ($contractor->country ?? '') == 'FR' ? 'selected' : '' ?>>France</option>
                                            <option value="JP" <?= ($contractor->country ?? '') == 'JP' ? 'selected' : '' ?>>Japan</option>
                                            <option value="Other" <?= ($contractor->country ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5><i class="fa fa-briefcase"></i> Business Information</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_id">Tax ID / EIN</label>
                                        <input type="text" class="form-control" id="tax_id" name="tax_id" 
                                               value="<?= htmlspecialchars($contractor->tax_id ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="business_license">Business License Number</label>
                                        <input type="text" class="form-control" id="business_license" name="business_license" 
                                               value="<?= htmlspecialchars($contractor->business_license ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialties">Specialties / Services</label>
                                        <textarea class="form-control" id="specialties" name="specialties" rows="3" 
                                                  placeholder="e.g., construction, electrical, plumbing, HVAC, etc."><?= htmlspecialchars($contractor->specialties ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hourly_rate">Hourly Rate ($)</label>
                                        <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" 
                                               value="<?= htmlspecialchars($contractor->hourly_rate ?? '') ?>" 
                                               step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="insurance_info">Insurance Information</label>
                                        <textarea class="form-control" id="insurance_info" name="insurance_info" rows="3" 
                                                  placeholder="Describe insurance coverage, policy numbers, etc."><?= htmlspecialchars($contractor->insurance_info ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h5><i class="fa fa-sticky-note"></i> Additional Information</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Notes & Comments</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="4" 
                                                  placeholder="Additional notes about the contractor, special requirements, etc."><?= htmlspecialchars($contractor->notes ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <div class="text-right">
                                    <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> <?= isset($contractor) ? 'Update Contractor' : 'Create Contractor' ?>
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

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('profilePreview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Profile" class="profile-image-preview">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    // Form validation
    $('#contractorForm').on('submit', function(e) {
        var isValid = true;
        
        // Check required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Email validation
        var email = $('#email').val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && !emailRegex.test(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert_float('danger', 'Please fill in all required fields correctly.');
        }
    });
    
    // Remove validation styling on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Phone number formatting
    $('#phone').on('input', function() {
        var value = $(this).val().replace(/\D/g, '');
        if (value.length >= 6) {
            value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
        }
        $(this).val(value);
    });
});
</script>

<?php init_tail(); ?>
