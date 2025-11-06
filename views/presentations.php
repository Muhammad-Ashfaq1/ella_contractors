<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">Presentations Management</h4>
                        <hr class="hr-panel-heading" />
                        
                        <!-- Upload Presentation Form -->
                        <div class="_buttons">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#uploadPresentationModal">
                                        <i class="fa fa-upload" style="margin-right: 2% !important;"></i> Upload Presentation
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading" />
                        
                        <!-- Presentations DataTable -->
                        <div class="table-responsive" id="initial-presentations-table" style="display: none;">
                            <table class="table table-striped table-ella_presentations">
                                <thead>
                                    <tr>
                                        <th>
                                            <span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="ella_presentations"><label></label></div>
                                        </th>
                                        <th class="text-center"><?php echo _l('id'); ?></th>
                                        <th class="text-center">File Name</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Size</th>
                                        <th class="text-center">Upload Date</th>
                                        <th class="text-center">Published By</th>
                                        <th class="text-center">File Path</th>
                                        <th class="text-center" width="120px"><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        
                        <hr />
                        
                        <!-- Upload Modal -->
                        <div id="uploadPresentationModal" class="modal fade">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form id="uploadPresentationForm" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title">
                                                <i class="fa fa-upload"></i> Upload Presentations
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Presentation Name and Description in Same Row -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="presentation_name">Presentation Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="presentation_name" id="presentation_name" class="form-control" placeholder="e.g., Appointment Presentation" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="description">Description (Optional)</label>
                                                        <input type="text" name="description" id="presentation_description" class="form-control" placeholder="Brief description...">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <!-- Dropzone Section -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p class="text-muted">
                                                        Drop files here or click to select<br>
                                                        <small>Allowed file types: PDF, PPT, PPTX, HTML (Max size: 50MB per file)</small>
                                                    </p>
                                                    
                                                    <!-- Custom Dropzone (same as attachment modal) -->
                                                    <div class="drop-zone" id="presentationDropzone">
                                                        <span class="drop-zone__prompt">Drop Files Here or Click to Select</span>
                                                        <input type="file" name="presentation_files[]" class="drop-zone__input" 
                                                               id="presentation_files" multiple 
                                                               accept=".html,.pdf,.ppt,.pptx">
                                                        <div class="drop-zone__thumbnails" id="presentationThumbnails"></div>
                                                    </div>
                                                    
                                                    <!-- Hidden field to track selected files (for validation) -->
                                                    <input type="hidden" id="presentation_files_count" value="0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-default" id="uploadPresentationBtn" disabled>
                                                <i class="fa fa-upload"></i> Upload Files
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
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

<?php init_tail(); ?>

<style>
/* Fix checkbox alignment - center the checkmark icon */
.table-ella_presentations .checkbox label::after {
    padding-left: 3.5px !important;
    padding-top: 2px !important;
}

/* Ensure checkbox column width matches appointments table */
.table-ella_presentations thead th:first-child,
.table-ella_presentations tbody td:first-child {
    width: 30px;
    text-align: left;
}

/* Center align table headers */
.table-ella_presentations th {
    text-align: center;
    vertical-align: middle;
}

.table-ella_presentations td {
    vertical-align: middle;
}

/* Bulk delete button styling in DataTable toolbar */
#bulk-delete-presentations {
    display: inline-block;
    vertical-align: middle;
    margin-left: 5px !important;
}

#bulk-delete-presentations.hide {
    display: none !important;
}

/* Ensure button appears in same line as other DataTable controls */
.dataTables_length, .dt-buttons, #bulk-delete-presentations {
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
}

/* Match hover of Delete All button with listing delete button */
#bulk-delete-presentations.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

#bulk-delete-presentations.btn-danger:hover,
.table-ella_presentations .btn-danger:hover {
    background-color: #bb2d3b;
    border-color: #b02a37;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

/* Status Dropdown Styling (same as appointments) */
.status-wrapper {
    position: relative;
    display: inline-block;
}

.status-button {
    cursor: pointer !important;
    transition: opacity 0.2s ease;
    font-size: 13px;
    padding: 10px 18px !important;
    font-weight: 700;
    min-width: 80px;
    text-align: center;
    display: inline-block;
    border-radius: 4px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.status-button:hover {
    cursor: pointer !important;
    opacity: 0.8;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}

.status-dropdown {
    position: absolute;
    top: 0;
    right: 100%;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    min-width: 100px;
}

.status-option {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s ease;
}

.status-option:hover {
    background-color: #f5f5f5;
}

.status-option:last-child {
    border-bottom: none;
}

/* Hide dropdown menu from print/export */
@media print {
    .status-dropdown,
    .status-option,
    .table-export-exclude {
        display: none !important;
    }
}

/* Pencil icon hover effect */
.edit-name-icon {
    transition: opacity 0.2s ease;
}

.presentation-name:hover + .edit-name-icon,
.edit-name-icon:hover {
    opacity: 1 !important;
}

/* Dropzone styles - match attachment modal */
#presentationDropzone.drop-zone {
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

#presentationDropzone.drop-zone:hover {
    border-color: #007a5c;
    background-color: #f8f9fa;
}

#presentationDropzone.drop-zone--over {
    border-style: solid;
    background-color: #e8f5e9;
}

#presentationDropzone .drop-zone__input {
    display: none !important;
}

#presentationDropzone .drop-zone__thumb {
    width: 150px;
    height: 150px;
    margin: 5px;
    background-color: #fff;
    background-size: cover;
    position: relative;
    border-radius: 5px;
    border: 1px solid #ccc;
}

#presentationDropzone .drop-zone__thumbnails {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
    width: 100%;
    justify-content: center;
}

#presentationDropzone .drop-zone__prompt {
    display: block;
    width: 100%;
    text-align: center;
    color: #666;
    font-size: 16px;
    line-height: 1.5;
}

#presentationDropzone .removeimage {
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

#presentationDropzone .removeimage:hover {
    background: darkred;
}

#presentationDropzone .drop-zone__thumb .file-name-label {
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

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

$(document).ready(function() {
    // Initialize DataTable for presentations
    // Sort by column 5 (Upload Date) descending by default
    // Columns: 0=checkbox, 1=ID, 2=File Name, 3=Type, 4=Size, 5=Upload Date, 6=Published By (SORTABLE), 7=File Path (hidden), 8=Options
    // Disable sorting on: column 0 (checkbox), column 8 (options)
    // Published By (column 6) is NOW SORTABLE by staff full name
    // Show table only AFTER data is loaded to prevent flash/glitch
    initDataTable('.table-ella_presentations', admin_url + 'ella_contractors/presentations/table', undefined, [0, 8], {
        initComplete: function(settings, json) {
            // Show table only after first AJAX load completes
            $('#initial-presentations-table').show();
            
            // Hide File Path column (column 7) from display but keep it for export
            var table = $('.table-ella_presentations').DataTable();
            if (table) {
                table.column(7).visible(false);
            }
        }
    }, [5, 'desc']);
    
    // Function to hide File Path column after table redraws
    function hideFilePathColumn() {
        var table = $('.table-ella_presentations').DataTable();
        if (table) {
            // Hide column 7 (File Path) from display
            table.column(7).visible(false);
        }
    }
    
    // Re-hide column after table redraws (after delete, bulk delete, etc.)
    if ($('.table-ella_presentations').length) {
        $('.table-ella_presentations').on('draw.dt', function() {
            setTimeout(hideFilePathColumn, 100);
        });
    }
    
    // Function to add bulk delete button to DataTable toolbar
    function addBulkDeleteButton() {
        if ($('.table-ella_presentations').length && $('#bulk-delete-presentations').length === 0) {
            // Find the DataTable wrapper
            var $wrapper = $('.table-ella_presentations').closest('.dataTables_wrapper');
            
            if ($wrapper.length) {
                // Try to find the buttons container first (if Export button exists)
                var $buttonsContainer = $wrapper.find('.dt-buttons');
                
                if ($buttonsContainer.length) {
                    // Add bulk delete button after export button
                    $buttonsContainer.append('<button type="button" class="btn btn-danger btn-xs hide" id="bulk-delete-presentations">' +
                                            '<i class="fa fa-trash"></i> Delete All (<span id="selected-count">0</span>)' +
                                         '</button>');
                } else {
                    // Fallback: add to the left side with length dropdown
                    var $lengthContainer = $wrapper.find('.dataTables_length');
                    if ($lengthContainer.length) {
                        $lengthContainer.after('<button type="button" class="btn btn-danger btn-xs hide" id="bulk-delete-presentations" style="margin-left: 10px;">' +
                            '<i class="fa fa-trash"></i> Delete (<span id="selected-count">0</span>)' +
                        '</button>');
                    }
                }
            }
        }
    }
    
    // Add bulk delete button to DataTable toolbar after initialization
    setTimeout(addBulkDeleteButton, 800);
    
    // Re-add button after table draws (if needed)
    if ($('.table-ella_presentations').length) {
        $('.table-ella_presentations').on('draw.dt', function() {
            setTimeout(addBulkDeleteButton, 100);
        });
    }
    
    // ========================================
    // BULK DELETE FUNCTIONALITY
    // ========================================
    
    // Handle individual checkbox changes
    $(document).on('change', '.table-ella_presentations tbody input[type="checkbox"]', function() {
        updateBulkDeleteButton();
    });
    
    // Handle select all checkbox
    $(document).on('change', '#mass_select_all', function() {
        var isChecked = $(this).prop('checked');
        $('.table-ella_presentations tbody input[type="checkbox"]').prop('checked', isChecked);
        updateBulkDeleteButton();
    });
    
    // Update bulk delete button visibility and count
    function updateBulkDeleteButton() {
        var selectedCount = $('.table-ella_presentations tbody input[type="checkbox"]:checked').length;
        $('#selected-count').text(selectedCount);
        
        if (selectedCount > 0) {
            $('#bulk-delete-presentations').removeClass('hide');
        } else {
            $('#bulk-delete-presentations').addClass('hide');
        }
    }
    
    // Handle bulk delete button click
    $(document).on('click', '#bulk-delete-presentations', function() {
        var selectedIds = [];
        $('.table-ella_presentations tbody input[type="checkbox"]:checked').each(function() {
            var presentationId = $(this).val();
            if (presentationId) {
                selectedIds.push(presentationId);
            }
        });
        
        if (selectedIds.length === 0) {
            alert_float('warning', 'No presentations selected');
            return;
        }
        
        // Confirm deletion
        var confirmMessage = 'Are you sure you want to delete ' + selectedIds.length + ' presentation(s)? This action cannot be undone.';
        if (!confirm(confirmMessage)) {
            return;
        }
        
        // Save current sort order
        var table = $('.table-ella_presentations').DataTable();
        var currentOrder = table.order();
        
        // Show loading state
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');
        
        // Send AJAX request
        $.ajax({
            url: admin_url + 'ella_contractors/presentations/bulk_delete',
            type: 'POST',
            data: {
                ids: selectedIds,
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    
                    // Uncheck mass select all
                    $('#mass_select_all').prop('checked', false);
                    
                    // Hide bulk delete button
                    $('#bulk-delete-presentations').addClass('hide');
                    
                    // Reload table maintaining sort order
                    table.ajax.reload(function() {
                        table.order(currentOrder).draw(false);
                    });
                } else {
                    alert_float('danger', response.message || 'Failed to delete presentations');
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error deleting presentations: ' + error);
                console.error('Bulk delete error:', error);
            },
            complete: function() {
                // Restore button state
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // ========================================
    // CUSTOM DROPZONE FOR PRESENTATION UPLOADS (BATCH UPLOAD)
    // ========================================
    
    var presentationFiles = []; // Store files in memory
    var MAX_PRESENTATION_FILES = 10;
    
    // Initialize dropzone when modal is opened
    $('#uploadPresentationModal').on('shown.bs.modal', function() {
        initializePresentationDropzone();
    });
    
    function initializePresentationDropzone() {
        const dropZoneElement = document.querySelector("#presentationDropzone");
        const inputElement = document.querySelector("#presentation_files");
        const thumbnailsContainer = document.querySelector("#presentationThumbnails");
        const promptElement = document.querySelector("#presentationDropzone .drop-zone__prompt");
        const uploadBtn = document.querySelector("#uploadPresentationBtn");
        
        if (!dropZoneElement || !inputElement) {
            console.warn('Presentation dropzone elements not found');
            return;
        }
        
        // Reset files array on initialization
        presentationFiles = [];
        
        // Click to browse
        dropZoneElement.addEventListener("click", function(e) {
            if (e.target === inputElement || e.target.closest('.removeimage')) {
                return;
            }
            inputElement.click();
        });
        
        // File selection via input
        inputElement.addEventListener("change", function(e) {
            if (inputElement.files.length > 0) {
                handlePresentationFiles(inputElement.files);
            }
        });
        
        // Drag & Drop events
        dropZoneElement.addEventListener("dragover", function(e) {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });
        
        ["dragleave", "dragend"].forEach(function(type) {
            dropZoneElement.addEventListener(type, function(e) {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });
        
        dropZoneElement.addEventListener("drop", function(e) {
            e.preventDefault();
            dropZoneElement.classList.remove("drop-zone--over");
            
            if (e.dataTransfer.files.length > 0) {
                handlePresentationFiles(e.dataTransfer.files);
            }
        });
        
        // Handle files (add to array and show preview)
        function handlePresentationFiles(files) {
            const remainingSlots = MAX_PRESENTATION_FILES - presentationFiles.length;
            
            if (files.length > remainingSlots) {
                alert_float('warning', 'You can only upload ' + MAX_PRESENTATION_FILES + ' files at once. ' + remainingSlots + ' slots remaining.');
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Validate file type
                const fileExt = file.name.split('.').pop().toLowerCase();
                const allowedExts = ['pdf', 'ppt', 'pptx', 'html'];
                if (allowedExts.indexOf(fileExt) === -1) {
                    alert_float('danger', 'File "' + file.name + '" is invalid. Only PDF, PPT, PPTX, and HTML files are allowed.');
                    continue;
                }
                
                // Validate file size (50MB)
                if (file.size > 50 * 1024 * 1024) {
                    alert_float('danger', 'File "' + file.name + '" is too large. Maximum size is 50MB.');
                    continue;
                }
                
                // Add to array
                presentationFiles.push(file);
                
                // Create thumbnail
                createPresentationThumbnail(file, presentationFiles.length - 1);
            }
            
            updatePresentationDropzoneUI();
        }
        
        // Create thumbnail preview
        function createPresentationThumbnail(file, index) {
            const thumbnailElement = document.createElement("div");
            thumbnailElement.classList.add("drop-zone__thumb");
            thumbnailElement.dataset.index = index;
            
            // Show file icon (presentations are never images)
            const iconData = getFileIconWithColor(file.name);
            thumbnailElement.innerHTML = '<div style="text-align: center; padding: 20px;">' +
                '<i class="' + iconData.icon + '" style="font-size: 48px; color: ' + iconData.color + ';"></i>' +
                '</div>';
            
            // File name label
            const fileNameDiv = document.createElement("div");
            fileNameDiv.textContent = file.name;
            fileNameDiv.className = "file-name-label";
            thumbnailElement.appendChild(fileNameDiv);
            
            // Remove button
            const removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.classList.add("removeimage");
            removeBtn.innerHTML = "×";
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                removePresentationFile(index);
            };
            thumbnailElement.appendChild(removeBtn);
            
            thumbnailsContainer.appendChild(thumbnailElement);
        }
        
        // Remove file from array
        function removePresentationFile(index) {
            presentationFiles.splice(index, 1);
            
            // Clear and rebuild thumbnails
            thumbnailsContainer.innerHTML = '';
            presentationFiles.forEach(function(file, idx) {
                createPresentationThumbnail(file, idx);
            });
            
            updatePresentationDropzoneUI();
        }
        
        // Update UI based on file count
        function updatePresentationDropzoneUI() {
            const fileCount = presentationFiles.length;
            
            // Enable/disable upload button
            if (fileCount > 0) {
                uploadBtn.disabled = false;
                uploadBtn.classList.remove('btn-default');
                uploadBtn.classList.add('btn-info');
                promptElement.style.display = 'none';
            } else {
                uploadBtn.disabled = true;
                uploadBtn.classList.add('btn-default');
                uploadBtn.classList.remove('btn-info');
                promptElement.style.display = 'block';
            }
            
            // Update hidden count field
            document.querySelector("#presentation_files_count").value = fileCount;
        }
        
        // Get icon and color based on file extension
        function getFileIconWithColor(fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            
            if (ext === 'pdf') {
                return { icon: 'fa fa-file-pdf-o', color: '#dc3545' };
            }
            if (ext === 'ppt' || ext === 'pptx') {
                return { icon: 'fa fa-file-powerpoint-o', color: '#d24726' };
            }
            if (ext === 'html') {
                return { icon: 'fa fa-file-code-o', color: '#e67e22' };
            }
            return { icon: 'fa fa-file-o', color: '#666' };
        }
    }
    
    // Upload button click handler
    $(document).on('click', '#uploadPresentationBtn', function() {
        // Validate presentation name
        var presentationName = $('#presentation_name').val().trim();
        if (!presentationName) {
            alert_float('warning', 'Please enter a presentation name');
            $('#presentation_name').focus();
            return;
        }
        
        if (presentationFiles.length === 0) {
            alert_float('warning', 'Please select files to upload');
            return;
        }
        
        // Show progress
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
        
        // Create FormData and append all files
        const formData = new FormData();
        
        // Debug: Log files being uploaded
        console.log('Uploading ' + presentationFiles.length + ' file(s):', presentationFiles.map(f => f.name));
        
        // Append each file
        presentationFiles.forEach(function(file, index) {
            formData.append('presentation_files[]', file);
            console.log('Appended presentation_files[' + index + ']:', file.name, '(' + file.size + ' bytes)');
        });
        
        // Add form fields
        formData.append('presentation_name', $('#presentation_name').val().trim());
        formData.append('description', $('#presentation_description').val());
        
        // Add CSRF token
        formData.append(csrf_token_name, csrf_hash);
        
        // AJAX upload
        $.ajax({
            url: admin_url + 'ella_contractors/presentations/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    
                    // Clear files array and thumbnails
                    presentationFiles = [];
                    const thumbnailsContainer = document.querySelector("#presentationThumbnails");
                    if (thumbnailsContainer) {
                        thumbnailsContainer.innerHTML = '';
                    }
                    const promptElement = document.querySelector("#presentationDropzone .drop-zone__prompt");
                    if (promptElement) {
                        promptElement.style.display = 'block';
                    }
                    
                    // Reset button
                    $btn.removeClass('btn-info').addClass('btn-default');
                    $btn.prop('disabled', true).html(originalText);
                    
                    // Reset form fields
                    $('#presentation_name').val('');
                    $('#presentation_description').val('');
                    
                    // Close modal
                    $('#uploadPresentationModal').modal('hide');
                    
                    // Reload table maintaining sort order
                    var table = $('.table-ella_presentations').DataTable();
                    var currentOrder = table.order();
                    table.ajax.reload(function() {
                        table.order(currentOrder).draw(false);
                        setTimeout(function() {
                            // Hide File Path column (column 7)
                            table.column(7).visible(false);
                        }, 100);
                    });
                } else {
                    alert_float('danger', response.message || 'Upload failed');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Error uploading files: ' + error);
                console.error('Upload error:', xhr.responseText);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Reset dropzone when modal is closed
    $('#uploadPresentationModal').on('hidden.bs.modal', function() {
        presentationFiles = [];
        const thumbnailsContainer = document.querySelector("#presentationThumbnails");
        if (thumbnailsContainer) {
            thumbnailsContainer.innerHTML = '';
        }
        const promptElement = document.querySelector("#presentationDropzone .drop-zone__prompt");
        if (promptElement) {
            promptElement.style.display = 'block';
        }
        const uploadBtn = document.querySelector("#uploadPresentationBtn");
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.classList.remove('btn-info');
            uploadBtn.classList.add('btn-default');
            uploadBtn.innerHTML = '<i class="fa fa-upload"></i> Upload Files';
        }
        
        // Reset form fields
        $('#uploadPresentationForm')[0].reset();
        $('#presentation_name').val('');
        $('#presentation_description').val('');
    });
});


// Preview file function
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
        // PDF: Direct embed
        previewContent = '<iframe src="' + fileUrl + '" width="100%" height="600px" frameborder="0" style="border: 1px solid #ddd; border-radius: 4px;"></iframe>';
    } else if (fileExt === 'ppt' || fileExt === 'pptx') {
        // PowerPoint: Use Microsoft Office Online Viewer (Free, No Dependencies Required)
        // This renders PPT/PPTX natively with full animations and formatting
        
        // Force HTTPS if not already (Microsoft requires HTTPS)
        if (fileUrl.indexOf('http://') === 0) {
            fileUrl = fileUrl.replace('http://', 'https://');
        }
        
        var encodedUrl = encodeURIComponent(fileUrl);
        
        // Primary: Microsoft Office Online Viewer (Best Quality)
        var officeViewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodedUrl;
        
        // Fallback: Google Docs Viewer
        var googleViewerUrl = 'https://docs.google.com/gview?url=' + encodedUrl + '&embedded=true';
        
        previewContent = '<div class="pptx-preview-container">' +
            '<iframe id="pptx-preview-iframe" src="' + officeViewerUrl + '" width="100%" height="600px" frameborder="0" style="border: 1px solid #ddd; border-radius: 4px;"></iframe>' +
            '<div class="text-center" style="margin-top: 15px;">' +
                '<div class="btn-group btn-group-sm" role="group">' +
                    '<button type="button" class="btn btn-default" onclick="switchPPTViewer(\'microsoft\', \'' + encodedUrl + '\')"><i class="fa fa-windows"></i> Microsoft Viewer</button>' +
                    '<button type="button" class="btn btn-default" onclick="switchPPTViewer(\'google\', \'' + encodedUrl + '\')"><i class="fa fa-google"></i> Google Viewer</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    } else if (fileExt === 'html') {
        // HTML: Direct embed
        previewContent = '<iframe src="' + fileUrl + '" width="100%" height="600px" frameborder="0" style="border: 1px solid #ddd; border-radius: 4px;"></iframe>';
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

// Switch between Microsoft and Google viewers for PPT/PPTX
function switchPPTViewer(viewerType, encodedUrl) {
    var iframe = document.getElementById('pptx-preview-iframe');
    if (!iframe) return;
    
    // Show loading state
    iframe.style.opacity = '0.5';
    
    if (viewerType === 'microsoft') {
        iframe.src = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodedUrl;
    } else if (viewerType === 'google') {
        iframe.src = 'https://docs.google.com/gview?url=' + encodedUrl + '&embedded=true';
    }
    
    // Restore opacity after loading
    setTimeout(function() {
        iframe.style.opacity = '1';
    }, 1000);
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

// Edit presentation name inline
function editPresentationName(presentationId, currentName) {
    var nameSpan = $('.presentation-name[data-id="' + presentationId + '"]');
    var parentDiv = nameSpan.parent();
    
    // Replace with input and small icon buttons
    parentDiv.html(
        '<input type="text" class="form-control" id="edit-name-' + presentationId + '" value="' + currentName + '" style="display: inline-block; width: 200px; padding: 4px 8px; font-size: 13px;" onkeypress="if(event.key===\'Enter\'){savePresentationName(' + presentationId + ', \'' + currentName + '\');}">' +
        '<i class="fa fa-check" onclick="savePresentationName(' + presentationId + ', \'' + currentName + '\')" style="font-size: 11px; margin-left: 6px; cursor: pointer; color: #28a745;" title="Save"></i>' +
        '<i class="fa fa-times" onclick="cancelEditPresentationName(' + presentationId + ', \'' + currentName + '\')" style="font-size: 11px; margin-left: 6px; cursor: pointer; color: #6c757d;" title="Cancel"></i>'
    );
    
    // Focus and select
    setTimeout(function() {
        $('#edit-name-' + presentationId).focus().select();
    }, 100);
}

// Save presentation name
function savePresentationName(presentationId, oldName) {
    var newName = $('#edit-name-' + presentationId).val().trim();
    
    if (!newName) {
        alert_float('warning', 'Please enter a name');
        $('#edit-name-' + presentationId).focus();
        return;
    }
    
    if (newName === oldName) {
        cancelEditPresentationName(presentationId, oldName);
        return;
    }
    
    var table = $('.table-ella_presentations').DataTable();
    var currentOrder = table.order();
    
    $.ajax({
        url: admin_url + 'ella_contractors/presentations/update_name',
        type: 'POST',
        data: {
            id: presentationId,
            name: newName,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Name updated successfully');
                // Reload table maintaining sort order
                table.ajax.reload(function() {
                    table.order(currentOrder).draw(false);
                });
            } else {
                alert_float('danger', response.message || 'Failed to update name');
                cancelEditPresentationName(presentationId, oldName);
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error updating name: ' + error);
            cancelEditPresentationName(presentationId, oldName);
        }
    });
}

// Cancel edit presentation name
function cancelEditPresentationName(presentationId, originalName) {
    var parentDiv = $('.presentation-name[data-id="' + presentationId + '"]').parent();
    if (!parentDiv.length) {
        parentDiv = $('#edit-name-' + presentationId).parent();
    }
    
    // Restore original display with small pencil icon
    parentDiv.html(
        '<span class="presentation-name" data-id="' + presentationId + '">' + originalName + '</span>' +
        '<i class="fa fa-pencil edit-name-icon" onclick="editPresentationName(' + presentationId + ', \'' + originalName.replace(/'/g, "\\'") + '\'); event.stopPropagation();" style="font-size: 11px; margin-left: 6px; opacity: 0.5; cursor: pointer; color: #3498db;" title="Edit name"></i>'
    );
}

// Delete presentation
function deletePresentation(presentationId) {
    if (!confirm('Are you sure you want to delete this presentation? This action cannot be undone.')) {
        return;
    }
    
    var table = $('.table-ella_presentations').DataTable();
    var currentOrder = table.order();
    
    $.ajax({
        url: admin_url + 'ella_contractors/presentations/delete',
        type: 'POST',
        data: {
            id: presentationId,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', 'Presentation deleted successfully');
                // Reload table maintaining sort order
                table.ajax.reload(function() {
                    table.order(currentOrder).draw(false);
                });
            } else {
                alert_float('danger', response.message || 'Failed to delete presentation');
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error deleting presentation: ' + error);
        }
    });
}
</script>