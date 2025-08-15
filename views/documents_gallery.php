<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-folder-open"></i>
                            Documents Gallery - <?php echo $contractor_name; ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Upload New Document</h4>
                                <form action="<?php echo admin_url('ella_contractors/documents/upload/' . $contractor_id); ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="document_name">Document Name</label>
                                        <input type="text" class="form-control" id="document_name" name="document_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="document_type">Document Type</label>
                                        <select class="form-control" id="document_type" name="document_type">
                                            <option value="contract">Contract</option>
                                            <option value="invoice">Invoice</option>
                                            <option value="profile">Company Profile</option>
                                            <option value="images">Images/Photos</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="document_file">File</label>
                                        <input type="file" class="form-control" id="document_file" name="document_file" required>
                                        <small class="text-muted">Max size: 10MB. Allowed types: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> Upload Document
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h4>Quick Actions</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-success btn-block" onclick="generateSamplePDF()">
                                            <i class="fa fa-file-pdf-o"></i> Generate PDF
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-info btn-block" onclick="generateSamplePPT()">
                                            <i class="fa fa-file-powerpoint-o"></i> Generate PPT
                                        </button>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-warning btn-block" onclick="createImageArchive()">
                                            <i class="fa fa-images"></i> Image Archive
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary btn-block" onclick="generateShareableLink()">
                                            <i class="fa fa-share"></i> Share Documents
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Documents</h4>
                        <div class="row">
                            <?php foreach ($documents as $doc): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="document-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 20px; background: #fff;">
                                    <div class="document-icon text-center" style="margin-bottom: 15px;">
                                        <?php
                                        $icon_class = 'fa fa-file-o';
                                        $icon_color = '#666';
                                        
                                        switch (strtolower($doc['document_type'])) {
                                            case 'pdf':
                                            case 'contract':
                                            case 'invoice':
                                                $icon_class = 'fa fa-file-pdf-o';
                                                $icon_color = '#d9534f';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                            case 'profile':
                                                $icon_class = 'fa fa-file-word-o';
                                                $icon_color = '#337ab7';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $icon_class = 'fa fa-file-excel-o';
                                                $icon_color = '#5cb85c';
                                                break;
                                            case 'ppt':
                                            case 'pptx':
                                                $icon_class = 'fa fa-file-powerpoint-o';
                                                $icon_color = '#f0ad4e';
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif':
                                            case 'images':
                                                $icon_class = 'fa fa-file-image-o';
                                                $icon_color = '#5bc0de';
                                                break;
                                        }
                                        ?>
                                        <i class="<?php echo $icon_class; ?>" style="font-size: 48px; color: <?php echo $icon_color; ?>;"></i>
                                    </div>
                                    
                                    <div class="document-info text-center">
                                        <h5 style="margin: 0 0 10px 0; font-size: 14px; height: 40px; overflow: hidden;">
                                            <?php echo $doc['document_name']; ?>
                                        </h5>
                                        <p style="margin: 0; font-size: 12px; color: #666;">
                                            <strong>Type:</strong> <?php echo ucfirst($doc['document_type']); ?><br>
                                            <strong>Size:</strong> <?php echo $doc['file_size']; ?><br>
                                            <strong>Date:</strong> <?php echo date('M j, Y', strtotime($doc['date_uploaded'])); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="document-actions text-center" style="margin-top: 15px;">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-default" onclick="downloadDocument(<?php echo $doc['id']; ?>)" title="Download">
                                                <i class="fa fa-download"></i>
                                            </button>
                                            <button class="btn btn-info" onclick="shareDocument(<?php echo $doc['id']; ?>)" title="Share">
                                                <i class="fa fa-share"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteDocument(<?php echo $doc['id']; ?>)" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadDocument(docId) {
    window.location.href = '<?php echo admin_url('ella_contractors/documents/download/'); ?>' + docId;
}

function shareDocument(docId) {
    // Show share dialog
    alert('Share functionality for document ID: ' + docId + '\n\nIn a real implementation, this would:\n- Generate a shareable link\n- Set expiration date\n- Send email notifications\n- Track access statistics');
}

function deleteDocument(docId) {
    if (confirm('Are you sure you want to delete this document?')) {
        $.ajax({
            url: '<?php echo admin_url('ella_contractors/documents/delete/'); ?>' + docId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Document deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error occurred while deleting document');
            }
        });
    }
}

function generateSamplePDF() {
    alert('PDF Generation Demo\n\nThis would generate a professional PDF document using:\n- TCPDF library (server-side)\n- jsPDF (client-side)\n- Custom templates\n- Company branding');
}

function generateSamplePPT() {
    alert('Presentation Generation Demo\n\nThis would create a PowerPoint presentation using:\n- PhpPresentation library (server-side)\n- Custom slide templates\n- Company branding\n- Data visualization');
}

function createImageArchive() {
    alert('Image Archive Demo\n\nThis would create a ZIP archive containing:\n- Optimized images\n- Thumbnails\n- Metadata files\n- Sharing links');
}

function generateShareableLink() {
    alert('Shareable Link Demo\n\nThis would generate:\n- Secure sharing URLs\n- Expiration dates\n- Access controls\n- Download tracking');
}
</script>

<?php init_tail(); ?>
