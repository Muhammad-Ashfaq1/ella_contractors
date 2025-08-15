<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= isset($contractor) ? 'Edit Contractor' : 'Add New Contractor' ?> - Ella CRM</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .form-section { margin-bottom: 30px; }
        .form-section h4 { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; }
        .required { color: #dc3545; }
        .help-text { font-size: 12px; color: #6c757d; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>
                        <i class="fa fa-<?= isset($contractor) ? 'edit' : 'plus' ?>"></i> 
                        <?= isset($contractor) ? 'Edit Contractor' : 'Add New Contractor' ?>
                    </h1>
                    <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Contractors
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($errors) && $errors): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                                <?= $errors ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <!-- Basic Information -->
                            <div class="form-section">
                                <h4><i class="fa fa-info-circle"></i> Basic Information</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_name">Company Name <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                                   value="<?= htmlspecialchars($contractor->company_name ?? '') ?>" required>
                                            <div class="invalid-feedback">Please provide a company name.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact_person">Contact Person <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                                   value="<?= htmlspecialchars($contractor->contact_person ?? '') ?>" required>
                                            <div class="invalid-feedback">Please provide a contact person.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="required">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($contractor->email ?? '') ?>" required>
                                            <div class="invalid-feedback">Please provide a valid email address.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone <span class="required">*</span></label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?= htmlspecialchars($contractor->phone ?? '') ?>" required>
                                            <div class="invalid-feedback">Please provide a phone number.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address Information -->
                            <div class="form-section">
                                <h4><i class="fa fa-map-marker-alt"></i> Address Information</h4>
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
                                            <label for="website">Website</label>
                                            <input type="url" class="form-control" id="website" name="website" 
                                                   value="<?= htmlspecialchars($contractor->website ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Business Information -->
                            <div class="form-section">
                                <h4><i class="fa fa-briefcase"></i> Business Information</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tax_id">Tax ID / EIN</label>
                                            <input type="text" class="form-control" id="tax_id" name="tax_id" 
                                                   value="<?= htmlspecialchars($contractor->tax_id ?? '') ?>">
                                            <div class="help-text">Federal Tax ID or Employer Identification Number</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="business_license">Business License</label>
                                            <input type="text" class="form-control" id="business_license" name="business_license" 
                                                   value="<?= htmlspecialchars($contractor->business_license ?? '') ?>">
                                            <div class="help-text">Professional or business license number</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialties">Specialties</label>
                                            <input type="text" class="form-control" id="specialties" name="specialties" 
                                                   value="<?= htmlspecialchars($contractor->specialties ?? '') ?>"
                                                   placeholder="e.g., construction, electrical, plumbing">
                                            <div class="help-text">Comma-separated list of specialties</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
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
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="insurance_info">Insurance Information</label>
                                            <textarea class="form-control" id="insurance_info" name="insurance_info" rows="3" 
                                                      placeholder="General Liability, Workers Comp, etc."><?= htmlspecialchars($contractor->insurance_info ?? '') ?></textarea>
                                            <div class="help-text">List insurance coverage and policy numbers</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hourly_rate">Hourly Rate ($)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" 
                                                       step="0.01" min="0"
                                                       value="<?= isset($contractor) ? $contractor->hourly_rate : '' ?>"
                                                       placeholder="0.00">
                                            </div>
                                            <div class="help-text">Standard hourly rate for services</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="website">Website</label>
                                            <input type="url" class="form-control" id="website" name="website"
                                                   value="<?= htmlspecialchars($contractor->website ?? '') ?>"
                                                   placeholder="https://example.com">
                                            <div class="help-text">Company website URL</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="profile_image">Profile Image</label>
                                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                            <div class="help-text">Upload company logo or profile image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="form-section">
                                <h4><i class="fa fa-sticky-note"></i> Additional Information</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">Notes</label>
                                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                                      placeholder="Additional notes about the contractor..."><?= htmlspecialchars($contractor->notes ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="form-section">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-secondary mr-2">
                                            <i class="fa fa-times"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-<?= isset($contractor) ? 'save' : 'plus' ?>"></i> 
                                            <?= isset($contractor) ? 'Update Contractor' : 'Add Contractor' ?>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>
</html>
