<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
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
                                <?php if ($contract_id): ?>
                                <p class="text-muted">Upload media for: <strong><?= isset($contract_subject) ? $contract_subject : 'Contract #' . $contract_id ?></strong></p>
                                <?php else: ?>
                                <p class="text-muted">Upload default media files that will be available for all contracts</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if ($contract_id): ?>
                                <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract_id) ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Contract
                                </a>
                                <?php else: ?>
                                <a href="<?= admin_url('ella_contractors/media_gallery') ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back to Gallery
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <!-- Upload Form -->
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel_s">
                                    <div class="panel-heading">
                                        <h5>Upload Media File</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?= form_open_multipart('', ['id' => 'upload-form']) ?>
                                        
                                        <div class="form-group">
                                            <label for="media_file">Select File <span class="text-danger">*</span></label>
                                            <input type="file" name="media_file" id="media_file" class="form-control" required 
                                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                                            <small class="help-block">
                                                Allowed formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, ZIP, RAR<br>
                                                Maximum file size: 50MB
                                            </small>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" class="form-control" rows="3" 
                                                      placeholder="Optional description for this file"></textarea>
                                        </div>

                                        <?php if (!$contract_id): ?>
                                        <!-- Only show default checkbox when uploading general media -->
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="is_default" value="1" checked>
                                                    <strong>Use as default media for all contracts</strong>
                                                    <br><small class="text-muted">This file will be available in all contract media galleries</small>
                                                </label>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <!-- Ask if this should be default when uploading for specific contract -->
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="is_default" value="1">
                                                    <strong>Also use as default media for all contracts</strong>
                                                    <br><small class="text-muted">This file will be available in all contract media galleries, not just this contract</small>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fa fa-upload"></i> Upload File
                                            </button>
                                            <?php if ($contract_id): ?>
                                            <a href="<?= admin_url('ella_contractors/contracts/view/' . $contract_id) ?>" class="btn btn-default btn-lg">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                            <?php else: ?>
                                            <a href="<?= admin_url('ella_contractors/media_gallery') ?>" class="btn btn-default btn-lg">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                            <?php endif; ?>
                                        </div>

                                        <?= form_close() ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Type Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="fa fa-info-circle"></i> Supported File Types</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Documents:</strong><br>
                                            <i class="fa fa-file-pdf-o text-danger"></i> PDF<br>
                                            <i class="fa fa-file-word-o text-primary"></i> DOC, DOCX
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Spreadsheets:</strong><br>
                                            <i class="fa fa-file-excel-o text-success"></i> XLS, XLSX
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Presentations:</strong><br>
                                            <i class="fa fa-file-powerpoint-o text-warning"></i> PPT, PPTX
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Images & Archives:</strong><br>
                                            <i class="fa fa-file-image-o text-info"></i> JPG, PNG, GIF<br>
                                            <i class="fa fa-file-archive-o text-muted"></i> ZIP, RAR
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
</div>

<script>
$(document).ready(function() {
    // File upload preview
    $('#media_file').on('change', function() {
        var file = this.files[0];
        if (file) {
            var fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            var fileName = file.name;
            
            if (fileSize > 50) {
                alert('File size exceeds 50MB limit. Please choose a smaller file.');
                $(this).val('');
                return;
            }
            
            // Show file info
            var fileInfo = '<div class="alert alert-success"><strong>Selected File:</strong> ' + fileName + ' (' + fileSize + ' MB)</div>';
            $('.help-block').after(fileInfo);
        }
    });
    
    // Form validation
    $('#upload-form').on('submit', function(e) {
        var file = $('#media_file')[0].files[0];
        if (!file) {
            alert('Please select a file to upload.');
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php init_tail(); ?>
