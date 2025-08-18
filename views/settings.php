<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 5px 7px #4075A1;
        margin-bottom: 20px;
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

    .setting-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .setting-item:hover {
        border-color: #4075A1;
        box-shadow: 0 2px 8px rgba(64, 117, 161, 0.1);
    }

    .setting-item h6 {
        color: #495057;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .setting-item p {
        color: #6c757d;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #4075A1;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .document-type-tag {
        display: inline-block;
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 4px;
        margin: 2px;
        font-size: 0.8rem;
    }

    .document-type-tag .remove {
        margin-left: 5px;
        cursor: pointer;
        color: #dc3545;
    }

    .backup-section {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .backup-section h5 {
        color: #856404;
        margin-bottom: 15px;
    }

    .backup-section p {
        color: #856404;
        margin-bottom: 15px;
    }

    .danger-zone {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 1px solid #dc3545;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .danger-zone h5 {
        color: #721c24;
        margin-bottom: 15px;
    }

    .danger-zone p {
        color: #721c24;
        margin-bottom: 15px;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
</style>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="no-margin">
                        <i class="fa fa-cog"></i> Ella Contractors Settings
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
                        <i class="fa fa-sliders-h"></i> Module Configuration
                    </h4>
                </div>
                <div class="card-body-custom">
                    <form method="POST" id="settingsForm">
                        <!-- General Settings -->
                        <div class="section-header">
                            <h5><i class="fa fa-cog"></i> General Settings</h5>
                        </div>

                        <div class="setting-item">
                            <h6>Default Contractor Status</h6>
                            <p>Set the default status for newly created contractors</p>
                            <select class="form-control" name="default_status" id="default_status">
                                <option value="pending" <?= (get_option('ella_contractors_default_status') == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="active" <?= (get_option('ella_contractors_default_status') == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (get_option('ella_contractors_default_status') == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="setting-item">
                            <h6>Auto-approve Contractors</h6>
                            <p>Automatically approve new contractors without manual review</p>
                            <label class="toggle-switch">
                                <input type="checkbox" name="auto_approve" id="auto_approve" 
                                       value="1" <?= (get_option('ella_contractors_auto_approve') == '1') ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="setting-item">
                            <h6>Notification Email</h6>
                            <p>Email address for receiving contractor-related notifications</p>
                            <input type="email" class="form-control" name="notification_email" id="notification_email" 
                                   value="<?= get_option('ella_contractors_notification_email') ?>" 
                                   placeholder="admin@example.com">
                        </div>

                        <!-- Document Management -->
                        <div class="section-header">
                            <h5><i class="fa fa-file-alt"></i> Document Management</h5>
                        </div>

                        <div class="setting-item">
                            <h6>Allowed Document Types</h6>
                            <p>Configure which document types contractors can upload</p>
                            <div id="documentTypesContainer">
                                <?php 
                                $current_types = explode(',', get_option('ella_contractors_document_types', 'contract,license,insurance,certificate,other'));
                                foreach ($current_types as $type): 
                                    if (!empty(trim($type))):
                                ?>
                                    <span class="document-type-tag">
                                        <?= htmlspecialchars(trim($type)) ?>
                                        <span class="remove" onclick="removeDocumentType(this)">&times;</span>
                                    </span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control" id="newDocumentType" placeholder="Add new document type">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" onclick="addDocumentType()">Add</button>
                                </div>
                            </div>
                            <input type="hidden" name="document_types" id="documentTypesInput" 
                                   value="<?= get_option('ella_contractors_document_types', 'contract,license,insurance,certificate,other') ?>">
                        </div>

                        <div class="setting-item">
                            <h6>Maximum File Size</h6>
                            <p>Set the maximum file size for document uploads (in MB)</p>
                            <input type="number" class="form-control" name="max_file_size" id="max_file_size" 
                                   value="<?= get_option('ella_contractors_max_file_size', '10') ?>" 
                                   min="1" max="100" step="1">
                        </div>

                        <!-- Contract Settings -->
                        <div class="section-header">
                            <h5><i class="fa fa-file-contract"></i> Contract Settings</h5>
                        </div>

                        <div class="setting-item">
                            <h6>Contract Number Format</h6>
                            <p>Format for automatically generated contract numbers</p>
                            <input type="text" class="form-control" name="contract_number_format" id="contract_number_format" 
                                   value="<?= get_option('ella_contractors_contract_number_format', 'CON-{YEAR}-{SEQUENCE}') ?>" 
                                   placeholder="CON-{YEAR}-{SEQUENCE}">
                            <small class="form-text text-muted">
                                Available placeholders: {YEAR}, {MONTH}, {SEQUENCE}, {CONTRACTOR_ID}
                            </small>
                        </div>

                        <div class="setting-item">
                            <h6>Auto-renewal Reminder</h6>
                            <p>Send reminder emails before contracts expire (days in advance)</p>
                            <input type="number" class="form-control" name="contract_reminder_days" id="contract_reminder_days" 
                                   value="<?= get_option('ella_contractors_contract_reminder_days', '30') ?>" 
                                   min="1" max="365" step="1">
                        </div>

                        <!-- Payment Settings -->
                        <div class="section-header">
                            <h5><i class="fa fa-dollar-sign"></i> Payment Settings</h5>
                        </div>

                        <div class="setting-item">
                            <h6>Default Payment Terms</h6>
                            <p>Default payment terms for new contracts (in days)</p>
                            <input type="number" class="form-control" name="default_payment_terms" id="default_payment_terms" 
                                   value="<?= get_option('ella_contractors_default_payment_terms', '30') ?>" 
                                   min="0" max="365" step="1">
                        </div>

                        <div class="setting-item">
                            <h6>Late Payment Fee</h6>
                            <p>Percentage fee for late payments</p>
                            <div class="input-group">
                                <input type="number" class="form-control" name="late_payment_fee" id="late_payment_fee" 
                                       value="<?= get_option('ella_contractors_late_payment_fee', '5') ?>" 
                                       min="0" max="50" step="0.1">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Backup & Maintenance -->
                        <div class="backup-section">
                            <h5><i class="fa fa-database"></i> Backup & Maintenance</h5>
                            <p>Manage module data and perform maintenance tasks</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-warning" onclick="exportData()">
                                        <i class="fa fa-download"></i> Export All Data
                                    </button>
                                    <small class="form-text text-muted d-block mt-1">
                                        Export all contractors, contracts, and payments to CSV
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-info" onclick="generateReport()">
                                        <i class="fa fa-chart-bar"></i> Generate Report
                                    </button>
                                    <small class="form-text text-muted d-block mt-1">
                                        Create comprehensive module report
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="danger-zone">
                            <h5><i class="fa fa-exclamation-triangle"></i> Danger Zone</h5>
                            <p>These actions cannot be undone. Please proceed with caution.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger" onclick="clearAllData()">
                                        <i class="fa fa-trash"></i> Clear All Data
                                    </button>
                                    <small class="form-text text-muted d-block mt-1">
                                        Remove all contractors, contracts, and related data
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger" onclick="resetSettings()">
                                        <i class="fa fa-undo"></i> Reset to Defaults
                                    </button>
                                    <small class="form-text text-muted d-block mt-1">
                                        Reset all settings to their default values
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fa fa-undo"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        // Update hidden input with current document types
        updateDocumentTypesInput();
        
        // Submit form via AJAX
        $.ajax({
            url: '<?= admin_url('ella_contractors/settings') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Settings saved successfully');
                } else {
                    alert_float('danger', response.message || 'Failed to save settings');
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while saving settings');
            }
        });
    });

    // Auto-save document types when changed
    $('#newDocumentType').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            addDocumentType();
        }
    });
});

// Document type management
function addDocumentType() {
    var input = $('#newDocumentType');
    var type = input.val().trim();
    
    if (type) {
        var tag = '<span class="document-type-tag">' + 
                  htmlEscape(type) + 
                  '<span class="remove" onclick="removeDocumentType(this)">&times;</span>' + 
                  '</span>';
        
        $('#documentTypesContainer').append(tag);
        input.val('');
        updateDocumentTypesInput();
    }
}

function removeDocumentType(element) {
    $(element).parent().remove();
    updateDocumentTypesInput();
}

function updateDocumentTypesInput() {
    var types = [];
    $('.document-type-tag').each(function() {
        types.push($(this).text().replace('×', '').trim());
    });
    $('#documentTypesInput').val(types.join(','));
}

function htmlEscape(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

// Utility functions
function exportData() {
    if (confirm('Export all module data? This may take a few moments.')) {
        window.open('<?= admin_url('ella_contractors/export/all') ?>', '_blank');
    }
}

function generateReport() {
    if (confirm('Generate comprehensive module report?')) {
        window.open('<?= admin_url('ella_contractors/report/comprehensive') ?>', '_blank');
    }
}

function clearAllData() {
    if (confirm('WARNING: This will permanently delete ALL contractors, contracts, and related data. This action cannot be undone. Are you absolutely sure?')) {
        if (confirm('Final confirmation: Type "DELETE" to confirm this action.')) {
            var confirmation = prompt('Type "DELETE" to confirm:');
            if (confirmation === 'DELETE') {
                $.ajax({
                    url: '<?= admin_url('ella_contractors/settings/clear_data') ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert_float('success', 'All data cleared successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            alert_float('danger', response.message || 'Failed to clear data');
                        }
                    },
                    error: function() {
                        alert_float('danger', 'An error occurred while clearing data');
                    }
                });
            }
        }
    }
}

function resetSettings() {
    if (confirm('Reset all settings to default values? This will remove all custom configurations.')) {
        $.ajax({
            url: '<?= admin_url('ella_contractors/settings/reset') ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Settings reset to defaults');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    alert_float('danger', response.message || 'Failed to reset settings');
                }
            },
            error: function() {
                alert_float('danger', 'An error occurred while resetting settings');
            }
        });
    }
}

function resetForm() {
    if (confirm('Reset form to current saved values? Any unsaved changes will be lost.')) {
        location.reload();
    }
}
</script>

<?php init_tail(); ?>
