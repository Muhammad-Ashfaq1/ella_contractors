<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
/* Dropzone styles - match appointment modal */
#attachmentViewDropzone.drop-zone {
    max-width: 100%;
    min-height: 150px;
    height: auto;
    padding: 25px;
    display: flex;
    flex-direction: column;
    width: 100%;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    color: #666;
    border: 2px dashed #009578;
    border-radius: 10px;
    margin-top: 10px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

#attachmentViewDropzone.drop-zone:hover {
    border-color: #007a5c;
    background-color: #f8f9fa;
}

#attachmentViewDropzone.drop-zone--over {
    border-style: solid;
    background-color: #e8f5e9;
}

#attachmentViewDropzone .drop-zone__input {
    display: none !important;
}

#attachmentViewDropzone .drop-zone__thumb {
    width: 150px;
    height: 150px;
    margin: 5px;
    background-color: #fff;
    background-size: cover;
    position: relative;
    border-radius: 5px;
    border: 1px solid #ccc;
}

#attachmentViewDropzone .drop-zone__thumbnails {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
    width: 100%;
    justify-content: center;
}

#attachmentViewDropzone .drop-zone__prompt {
    display: block;
    width: 100%;
    text-align: center;
    color: #666;
    font-size: 16px;
    line-height: 1.5;
}

#attachmentViewDropzone .removeimage {
    position: absolute;
    top: 8px;
    right: 8px;
    background: red;
    color: #fff;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    z-index: 99999;
    text-align: center;
    cursor: pointer;
    border: none;
    font-size: 18px;
    line-height: 22px;
}

#attachmentViewDropzone .removeimage:hover {
    background: darkred;
}

#attachmentViewDropzone .drop-zone__thumb .file-name-label {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 5px;
    background: rgba(0,0,0,0.75);
    color: white;
    font-size: 12px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<!-- Attachment Upload Modal -->
<div class="modal fade" id="attachmentUploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-upload"></i> Upload Attachments
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted">
                            Drop files here or click to select (Maximum 10 files)<br>
                            <small>Allowed file types: PDF, Word, Excel, PowerPoint, Images</small><br>
                        </p>
                        
                        <!-- Custom Dropzone (same as appointment modal) -->
                        <div class="drop-zone" id="attachmentViewDropzone">
                            <span class="drop-zone__prompt">Drop Files Here or Click to Select</span>
                            <input type="file" name="attachment_files[]" class="drop-zone__input" 
                                   id="attachment_files" multiple 
                                   accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <div class="drop-zone__thumbnails" id="attachmentViewThumbnails"></div>
                        </div>
                        
                        <!-- Hidden field to track selected files (for validation) -->
                        <input type="hidden" id="attachment_files_count" value="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-default" id="uploadAttachmentsBtn" disabled>
                    <i class="fa fa-upload"></i> Upload Files (<span id="fileCountBadge">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

