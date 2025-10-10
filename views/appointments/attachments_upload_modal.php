<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Attachment Upload Modal -->
<div class="modal fade" id="attachmentUploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-upload"></i> <?php echo _l('upload_attachments'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted">
                            <?php echo _l('attachment_upload_instructions'); ?><br>
                            <small><?php echo _l('allowed_file_types'); ?>: PDF, Word, Excel, PowerPoint, Images</small><br>
                            <small><?php echo _l('max_file_size'); ?>: 50MB per file</small>
                        </p>
                        
                        <!-- Dropzone Form -->
                        <form action="<?php echo admin_url('ella_contractors/appointments/upload_attachment/' . $appointment->id); ?>" 
                              id="appointment-attachment-upload" 
                              class="dropzone" 
                              method="post" 
                              enctype="multipart/form-data">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <div class="dz-message">
                                <i class="fa fa-cloud-upload fa-3x"></i>
                                <h4><?php echo _l('drop_files_here_to_upload'); ?></h4>
                                <span><?php echo _l('or_click_to_browse'); ?></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

