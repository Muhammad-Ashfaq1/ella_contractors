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
                        
                        <!-- Create Folder Form -->
                        <h5>Create New Folder</h5>
                        <?php echo form_open(admin_url('ella_contractors/create_folder')); ?>
                            <div class="form-group">
                                <label for="name">Folder Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="lead_id">Attach to Lead</label>
                                <select name="lead_id" class="selectpicker" data-width="100%">
                                    <option value="">None</option>
                                    <?php foreach ($leads as $lead): ?>
                                        <option value="<?= $lead['id']; ?>"><?= $lead['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Folder</button>
                        <?php echo form_close(); ?>
                        
                        <hr />
                        
                        <!-- Folders Table -->
                        <h5>Folders</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Lead</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($folders as $folder): ?>
                                    <tr>
                                        <td><?= $folder['name']; ?></td>
                                        <td><?= $folder['lead_id'] ? get_lead_name($folder['lead_id']) : 'None'; ?></td>
                                        <td><?= date('M d, Y', strtotime($folder['created_at'])); ?></td>
                                        <td>
                                            <a href="#uploadModal_<?= $folder['id']; ?>" class="btn btn-default btn-xs" data-toggle="modal">Upload File</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Upload Modals for each folder -->
                        <?php foreach ($folders as $folder): ?>
                            <div id="uploadModal_<?= $folder['id']; ?>" class="modal fade">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <?php echo form_open_multipart(admin_url('ella_contractors/upload_presentation/' . $folder['id'])); ?>
                                            <div class="modal-header">
                                                <h4 class="modal-title">Upload to <?= $folder['name']; ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="file">File (HTML/PDF/PPT)</label>
                                                    <input type="file" name="file" class="form-control" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="lead_id">Attach to Lead</label>
                                                    <select name="lead_id" class="selectpicker" data-width="100%">
                                                        <option value="">None</option>
                                                        <?php foreach ($leads as $lead): ?>
                                                            <option value="<?= $lead['id']; ?>"><?= $lead['name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea name="description" class="form-control"></textarea>
                                                </div>
                                                <div class="checkbox">
                                                    <input type="checkbox" name="is_default" value="1">
                                                    <label>Is Default</label>
                                                </div>
                                                <div class="checkbox">
                                                    <input type="checkbox" name="active" value="1" checked>
                                                    <label>Active</label>
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
                        <?php endforeach; ?>
                        
                        <!-- Files List -->
                        <h5>Uploaded Files</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Folder</th>
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
                                        <td><?= $file['folder_id'] ? $this->ella_media_model->get_folder_name($file['folder_id']) : 'No Folder'; ?></td>
                                        <td><?= $file['lead_id'] ? get_lead_name($file['lead_id']) : 'None'; ?></td>
                                        <td><?= strtoupper(pathinfo($file['file_name'], PATHINFO_EXTENSION)); ?></td>
                                        <td><?= formatBytes($file['file_size']); ?></td>
                                        <td><?= $file['is_default'] ? 'Yes' : 'No'; ?></td>
                                        <td><?= $file['active'] ? 'Yes' : 'No'; ?></td>
                                        <td><?= date('M d, Y', strtotime($file['date_uploaded'])); ?></td>
                                        <td>
                                            <a href="<?= admin_url('ella_contractors/preview_file/' . $file['id']); ?>" class="btn btn-info btn-xs" target="_blank">Preview</a>
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
<?php init_tail(); ?>