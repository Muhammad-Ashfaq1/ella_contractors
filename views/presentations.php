<?php 
init_head(); 
// Helper function for file size formatting
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $decimals = 2) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $dm = $decimals < 0 ? 0 : $decimals;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }
}
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Presentations Management</h4>
                        <hr class="hr-panel-heading" />
                        
                        <!-- Upload Presentation Form -->
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#uploadPresentationModal">
                                    <i class="fa fa-upload"></i> Upload Presentation
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        
                        <hr />
                        
                        <!-- Upload Modal -->
                        <div id="uploadPresentationModal" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <?php echo form_open_multipart(admin_url('ella_contractors/upload_presentation')); ?>
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title">Upload Presentation</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="file">File (HTML/PDF/PPT/PPTX)</label>
                                                <input type="file" name="file" class="form-control" accept=".html,.pdf,.ppt,.pptx" required>
                                                <small class="text-muted">Supported formats: HTML, PDF, PPT, PPTX (Max size: 50MB)</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="lead_id">Attach to Lead (Optional)</label>
                                                <select name="lead_id" class="selectpicker" data-width="100%">
                                                    <option value="">None</option>
                                                    <?php foreach ($leads as $lead): ?>
                                                        <option value="<?= $lead['id']; ?>"><?= $lead['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="is_default" value="1">
                                                    Is Default Presentation
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="active" value="1" checked>
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Upload</button>
                                        </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Files List -->
                        <h5>Uploaded Files</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Lead</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Is Default</th>
                                    <th>Active</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($media as $file): ?>
                                    <tr>
                                        <td><?= $file['original_name']; ?></td>
                                        <td><?= $file['lead_id'] ? get_lead_name($file['lead_id']) : 'None'; ?></td>
                                        <td><?= strtoupper(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?></td>
                                        <td><?= formatBytes($file['file_size']); ?></td>
                                        <td><?= $file['is_default'] ? 'Yes' : 'No'; ?></td>
                                        <td><?= $file['active'] ? 'Yes' : 'No'; ?></td>
                                        <td><?= date('M d, Y', strtotime($file['date_uploaded'])); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-info btn-xs" onclick="previewFile(<?= $file['id']; ?>, '<?= $file['original_name']; ?>', '<?= strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?>', '<?= site_url('uploads/ella_presentations/' . ($file['is_default'] ? 'default/' : ($file['lead_id'] ? 'lead_' . $file['lead_id'] . '/' : 'general/')) . $file['file_name']); ?>'); return false;">Preview</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="closeFilePreview(); return false;" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="filePreviewModalLabel">File Preview</h4>
            </div>
            <div class="modal-body">
                <div id="filePreviewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="closeFilePreview(); return false;">Close</button>
                <a href="#" id="downloadFileBtn" class="btn btn-primary" target="_blank">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function previewFile(fileId, fileName, fileExt, fileUrl) {
    // Set modal title
    $('#filePreviewModalLabel').text('Preview: ' + fileName);
    
    // Set download link
    $('#downloadFileBtn').attr('href', fileUrl);
    
    // Clear previous content
    $('#filePreviewContent').html('');
    
    // Show loading
    $('#filePreviewContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Loading preview...</div>');
    
    // Show modal
    $('#filePreviewModal').modal({show: true, backdrop: 'static', keyboard: false});
    
    // Generate preview content based on file type
    var previewContent = '';
    
    if (fileExt === 'pdf') {
        previewContent = '<iframe src="' + fileUrl + '" width="100%" height="600px" frameborder="0"></iframe>';
    } else if (fileExt === 'ppt' || fileExt === 'pptx') {
        // Convert PPT/PPTX to PDF for preview
        var pdfPreviewUrl = '<?= admin_url('ella_contractors/get_preview_pdf/'); ?>' + fileId;
        previewContent = '<iframe src="' + pdfPreviewUrl + '" width="100%" height="600px" frameborder="0"></iframe>';
    } else if (fileExt === 'html') {
        previewContent = '<iframe src="' + fileUrl + '" width="100%" height="600px" frameborder="0"></iframe>';
    } else {
        previewContent = '<div class="alert alert-info text-center">' +
            '<h5><i class="fa fa-info-circle"></i> Preview Not Available</h5>' +
            '<p>Preview is not available for this file type (' + fileExt.toUpperCase() + ').</p>' +
            '<p><strong>File:</strong> ' + fileName + '</p>' +
            '<a href="' + fileUrl + '" class="btn btn-primary" target="_blank">' +
            '<i class="fa fa-external-link"></i> Open in New Tab</a>' +
            '</div>';
    }
    
    // Set preview content
    $('#filePreviewContent').html(previewContent);
}

function closeFilePreview() {
    $('#filePreviewModal').modal('hide');
    // Clear content when modal is hidden
    $('#filePreviewContent').html('');
}

// Handle modal close events
$('#filePreviewModal').on('hidden.bs.modal', function () {
    $('#filePreviewContent').html('');
});

// Handle iframe load errors
$(document).on('load', 'iframe', function() {
    var iframe = $(this);
    iframe.on('error', function() {
        iframe.parent().html('<div class="alert alert-warning text-center">' +
            '<h5><i class="fa fa-exclamation-triangle"></i> Preview Error</h5>' +
            '<p>Unable to load preview. This may be due to:</p>' +
            '<ul class="text-left">' +
            '<li>File not accessible from the internet (required for Office Online viewer)</li>' +
            '<li>File format not supported</li>' +
            '<li>Network connectivity issues</li>' +
            '</ul>' +
            '<a href="' + iframe.attr('src') + '" class="btn btn-primary" target="_blank">' +
            '<i class="fa fa-external-link"></i> Try Opening in New Tab</a>' +
            '</div>');
    });
});
</script>

<?php init_tail(); ?>