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
                        <h4><?= $title; ?></h4>
                        <hr />
                        <?php
                        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                        $preview_url = site_url('uploads/ella_presentations/' . ($file->is_default ? 'default/' : ($file->lead_id ? 'lead_' . $file->lead_id . '/' : 'general/')) . $file->file_name);
                        ?>
                        <?php if (in_array($ext, ['pdf', 'ppt', 'pptx'])): ?>
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle"></i> Preview Information</h5>
                                <p>For local development, PDF/PPT preview may not work with Office Online viewer.</p>
                                <p><strong>File:</strong> <?= $file->original_name; ?></p>
                                <p><strong>Size:</strong> <?= formatBytes($file->file_size); ?></p>
                                <p><strong>Type:</strong> <?= strtoupper($ext); ?></p>
                            </div>
                            <div class="text-center">
                                <a href="<?= $preview_url; ?>" class="btn btn-primary btn-lg" target="_blank">
                                    <i class="fa fa-download"></i> Download File
                                </a>
                                <a href="<?= $preview_url; ?>" class="btn btn-success btn-lg" target="_blank">
                                    <i class="fa fa-external-link"></i> Open in New Tab
                                </a>
                            </div>
                            <hr>
                            <div class="text-center">
                                <small class="text-muted">
                                    Note: Online preview will work when deployed to a public server with HTTPS.
                                </small>
                            </div>
                        <?php elseif ($ext == 'html'): ?>
                            <iframe src="<?= $preview_url; ?>" width="100%" height="600px" frameborder="0"></iframe>
                        <?php else: ?>
                            <p>Preview not available for this file type. <a href="<?= $preview_url; ?>" download>Download</a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>