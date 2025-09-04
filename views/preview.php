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

$ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
$preview_url = site_url('uploads/ella_presentations/' . ($file->is_default ? 'default/' : ($file->lead_id ? 'lead_' . $file->lead_id . '/' : 'general/')) . $file->file_name);
?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?= $title; ?></h4>
                        <hr />
                        
                        <!-- File Preview Modal -->
                        <div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" onclick="closeFilePreview(); return false;" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="filePreviewModalLabel">Preview: <?= $file->original_name; ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="filePreviewContent">
                                            <!-- Preview content will be loaded here -->
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" onclick="closeFilePreview(); return false;">Close</button>
                                        <a href="<?= $preview_url; ?>" class="btn btn-primary" target="_blank">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-info btn-lg" onclick="previewFile(<?= $file->id; ?>, '<?= $file->original_name; ?>', '<?= $ext; ?>', '<?= $preview_url; ?>'); return false;">
                                <i class="fa fa-eye"></i> Preview File
                            </button>
                            <a href="<?= $preview_url; ?>" class="btn btn-primary btn-lg" target="_blank">
                                <i class="fa fa-download"></i> Download File
                            </a>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>File Information</h5>
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>File Name:</strong></td>
                                        <td><?= $file->original_name; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>File Size:</strong></td>
                                        <td><?= formatBytes($file->file_size); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>File Type:</strong></td>
                                        <td><?= strtoupper($ext); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Upload Date:</strong></td>
                                        <td><?= date('M d, Y H:i', strtotime($file->date_uploaded)); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Preview Options</h5>
                                <div class="alert alert-info">
                                    <h6><i class="fa fa-info-circle"></i> Preview Information</h6>
                                    <p>Click "Preview File" to view the file in a modal popup.</p>
                                    <?php if (in_array($ext, ['ppt', 'pptx'])): ?>
                                        <p><strong>Note:</strong> PowerPoint files use Office Online viewer and require the file to be accessible from the internet.</p>
                                    <?php elseif ($ext == 'pdf'): ?>
                                        <p><strong>Note:</strong> PDF files are displayed directly in the browser.</p>
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
function previewFile(fileId, fileName, fileExt, fileUrl) {
    // Set modal title
    $('#filePreviewModalLabel').text('Preview: ' + fileName);
    
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