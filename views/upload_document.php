<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-upload"></i>
                            Upload Document
                        </h4>
                        <p class="text-muted">Add a new document for contractor ID: <?php echo $contractor_id; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <form action="<?php echo admin_url('ella_contractors/documents/upload/' . $contractor_id); ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                            <div class="form-group">
                                <label for="document_name">Document Name *</label>
                                <input type="text" class="form-control" id="document_name" name="document_name" required placeholder="Enter document name">
                            </div>

                            <div class="form-group">
                                <label for="document_type">Document Type *</label>
                                <select class="form-control" id="document_type" name="document_type" required>
                                    <option value="">Select document type</option>
                                    <option value="contract">Contract Agreement</option>
                                    <option value="invoice">Invoice</option>
                                    <option value="profile">Company Profile</option>
                                    <option value="license">Business License</option>
                                    <option value="insurance">Insurance Certificate</option>
                                    <option value="images">Project Images/Photos</option>
                                    <option value="financial">Financial Documents</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="document_file">File *</label>
                                <input type="file" class="form-control" id="document_file" name="document_file" required>
                                <small class="text-muted">
                                    <strong>Supported formats:</strong> PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, TXT<br>
                                    <strong>Maximum size:</strong> 10MB
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="description">Description (Optional)</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add any additional notes about this document"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="tags">Tags (Optional)</label>
                                <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags separated by commas">
                                <small class="text-muted">Example: important, contract, 2024, renovation</small>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_public" value="1"> Make this document publicly shareable
                                </label>
                                <small class="text-muted">Public documents can be shared with external parties via secure links</small>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="notify_contractor" value="1"> Notify contractor via email
                                </label>
                                <small class="text-muted">Send an email notification to the contractor about this new document</small>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <a href="<?php echo admin_url('ella_contractors/documents/gallery/' . $contractor_id); ?>" class="btn btn-default btn-block">
                                        <i class="fa fa-arrow-left"></i> Back to Gallery
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-block" id="uploadBtn">
                                        <i class="fa fa-upload"></i> Upload Document
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Progress -->
        <div class="row" id="uploadProgress" style="display: none;">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h5>Upload Progress</h5>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                                <span id="progressText">0%</span>
                            </div>
                        </div>
                        <div id="uploadStatus" class="text-muted"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // File size validation
    $('#document_file').on('change', function() {
        var file = this.files[0];
        var maxSize = 10 * 1024 * 1024; // 10MB
        
        if (file && file.size > maxSize) {
            alert('File size must be less than 10MB. Selected file size: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB');
            this.value = '';
            return false;
        }
        
        // Update file info
        if (file) {
            $('#uploadStatus').html('<strong>Selected file:</strong> ' + file.name + ' (' + (file.size / 1024).toFixed(2) + 'KB)');
        }
    });

    // Form submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var $form = $(this);
        var $uploadBtn = $('#uploadBtn');
        var $progress = $('#uploadProgress');
        
        // Show progress
        $progress.show();
        $uploadBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        
        // Simulate upload progress (in real implementation, use XMLHttpRequest with progress events)
        var progress = 0;
        var progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            
            $('.progress-bar').css('width', progress + '%');
            $('#progressText').text(Math.round(progress) + '%');
            
            if (progress >= 90) {
                clearInterval(progressInterval);
            }
        }, 200);
        
        // Submit form via AJAX
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                clearInterval(progressInterval);
                $('.progress-bar').css('width', '100%');
                $('#progressText').text('100%');
                $('#uploadStatus').html('<span class="text-success"><i class="fa fa-check"></i> Upload completed successfully!</span>');
                
                setTimeout(function() {
                    // Redirect to gallery
                    window.location.href = '<?php echo admin_url('ella_contractors/documents/gallery/' . $contractor_id); ?>';
                }, 1500);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $('.progress-bar').removeClass('progress-bar-striped active').addClass('progress-bar-danger');
                $('#uploadStatus').html('<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> Upload failed: ' + error + '</span>');
                $uploadBtn.prop('disabled', false).html('<i class="fa fa-upload"></i> Try Again');
            }
        });
    });

    // Auto-fill document name from file
    $('#document_file').on('change', function() {
        var fileName = this.files[0]?.name;
        if (fileName && !$('#document_name').val()) {
            // Remove file extension and replace underscores/dashes with spaces
            var docName = fileName.replace(/\.[^/.]+$/, "").replace(/[_-]/g, " ");
            $('#document_name').val(docName);
        }
    });

    // Auto-suggest tags based on document type
    $('#document_type').on('change', function() {
        var type = $(this).val();
        var suggestedTags = '';
        
        switch(type) {
            case 'contract':
                suggestedTags = 'contract, agreement, legal, important';
                break;
            case 'invoice':
                suggestedTags = 'invoice, payment, financial, billing';
                break;
            case 'profile':
                suggestedTags = 'profile, company, information, contact';
                break;
            case 'license':
                suggestedTags = 'license, permit, legal, compliance';
                break;
            case 'insurance':
                suggestedTags = 'insurance, coverage, compliance, legal';
                break;
            case 'images':
                suggestedTags = 'images, photos, project, visual';
                break;
            case 'financial':
                suggestedTags = 'financial, accounting, budget, cost';
                break;
        }
        
        if (suggestedTags && !$('#tags').val()) {
            $('#tags').val(suggestedTags);
        }
    });
});
</script>

<style>
.panel_s {
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.panel-body {
    padding: 25px;
}

.form-group label {
    font-weight: 600;
    color: #333;
}

.progress {
    height: 25px;
    border-radius: 4px;
}

.progress-bar {
    line-height: 25px;
    font-weight: 600;
}

#uploadProgress {
    margin-top: 20px;
}

.btn-block {
    padding: 12px;
    font-size: 16px;
}

.text-muted {
    color: #6c757d;
}

hr {
    border-color: #ddd;
    margin: 30px 0;
}
</style>

<?php init_tail(); ?>
