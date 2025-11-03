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
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#uploadPresentationModal">
                                    <i class="fa fa-upload"></i> Upload Presentation
                                </button>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading" />
                        
                        <!-- Presentations DataTable -->
                        <div class="table-responsive">
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
                                        <th class="text-center">Is Default</th>
                                        <th class="text-center">Active</th>
                                        <th class="text-center">Upload Date</th>
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
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <?php echo form_open_multipart(admin_url('ella_contractors/presentations/upload')); ?>
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
                                                <label for="description">Description</label>
                                                <textarea name="description" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="is_default" id="is_default" value="1">
                                                <label for="is_default">Is Default Presentation</label>
                                            </div>
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="active" id="active" value="1" checked>
                                                <label for="active">Active</label>
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
</style>

<script>
var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrf_hash = '<?php echo $this->security->get_csrf_hash(); ?>';

$(document).ready(function() {
    // Initialize DataTable for presentations
    // Sort by column 7 (Upload Date) descending by default
    // Columns: 0=checkbox, 1=ID, 2=File Name, 3=Type, 4=Size, 5=Is Default, 6=Active, 7=Upload Date, 8=File Path (hidden), 9=Options
    // Disable sorting on: column 0 (checkbox), column 9 (options)
    initDataTable('.table-ella_presentations', admin_url + 'ella_contractors/presentations/table', undefined, [0, 9], {}, [7, 'desc']);
    
    // Hide File Path column (column 8) from display but keep it for export
    // This column will be hidden in the listing but included in CSV/Excel/PDF exports
    function hideFilePathColumn() {
        var table = $('.table-ella_presentations').DataTable();
        if (table) {
            // Hide column 8 (File Path) from display
            table.column(8).visible(false);
        }
    }
    
    // Hide column after table initialization
    setTimeout(hideFilePathColumn, 500);
    
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
    // INTERACTIVE STATUS DROPDOWN FUNCTIONALITY
    // ========================================
    
    // Click handler for Is Default button
    $(document).on('click', '[id^="is-default-btn-"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var presentationId = $(this).attr('id').replace('is-default-btn-', '');
        var $menu = $('#is-default-menu-' + presentationId);
        
        // Hide all other menus first
        $('[id^="is-default-menu-"], [id^="active-menu-"]').not($menu).hide();
        
        // Toggle current menu
        if ($menu.is(':visible')) {
            $menu.hide();
        } else {
            $menu.show();
        }
    });
    
    // Click handler for Active button
    $(document).on('click', '[id^="active-btn-"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var presentationId = $(this).attr('id').replace('active-btn-', '');
        var $menu = $('#active-menu-' + presentationId);
        
        // Hide all other menus first
        $('[id^="is-default-menu-"], [id^="active-menu-"]').not($menu).hide();
        
        // Toggle current menu
        if ($menu.is(':visible')) {
            $menu.hide();
        } else {
            $menu.show();
        }
    });
    
    // Hide menus when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.status-wrapper').length) {
            $('[id^="is-default-menu-"], [id^="active-menu-"]').hide();
        }
    });
});

// Update Is Default status function
function updateIsDefault(value, presentationId) {
    var data = {};
    data.is_default = value;
    data.presentation_id = presentationId;
    
    // Show loading indicator
    var statusElement = $('#is-default-btn-' + presentationId);
    var originalContent = statusElement.html();
    statusElement.html('<i class="fa fa-spinner fa-spin"></i>');
    
    // Hide menu immediately
    $('#is-default-menu-' + presentationId).hide();
    
    $.post(admin_url + 'ella_contractors/presentations/update_is_default', data)
    .done(function (response) {
        var result = JSON.parse(response);
        
        if (result.success) {
            // Show success message
            alert_float('success', result.message);
            
            // Update the display in place
            updateIsDefaultInPlace(presentationId, value);
        } else {
            // Show error message
            alert_float('danger', result.message);
            
            // Restore original content
            statusElement.html(originalContent);
        }
    })
    .fail(function (xhr, status, error) {
        // Show error message
        alert_float('danger', 'Failed to update Is Default status. Please try again.');
        
        // Restore original content
        statusElement.html(originalContent);
        
        console.error('Is Default status update failed:', error);
    });
}

// Update Active status function
function updateActive(value, presentationId) {
    var data = {};
    data.active = value;
    data.presentation_id = presentationId;
    
    // Show loading indicator
    var statusElement = $('#active-btn-' + presentationId);
    var originalContent = statusElement.html();
    statusElement.html('<i class="fa fa-spinner fa-spin"></i>');
    
    // Hide menu immediately
    $('#active-menu-' + presentationId).hide();
    
    $.post(admin_url + 'ella_contractors/presentations/update_active', data)
    .done(function (response) {
        var result = JSON.parse(response);
        
        if (result.success) {
            // Show success message
            alert_float('success', result.message);
            
            // Update the display in place
            updateActiveInPlace(presentationId, value);
        } else {
            // Show error message
            alert_float('danger', result.message);
            
            // Restore original content
            statusElement.html(originalContent);
        }
    })
    .fail(function (xhr, status, error) {
        // Show error message
        alert_float('danger', 'Failed to update Active status. Please try again.');
        
        // Restore original content
        statusElement.html(originalContent);
        
        console.error('Active status update failed:', error);
    });
}

// Update Is Default display in place without reloading the table
function updateIsDefaultInPlace(presentationId, newValue) {
    var table = $('.table-ella_presentations').DataTable();
    var currentOrder = table.order();
    
    // Find the row
    var rowIndex = -1;
    table.rows().every(function(rowIdx, data, node) {
        if (data[1].includes('>' + presentationId + '<')) {
            rowIndex = rowIdx;
            return false;
        }
    });
    
    if (rowIndex !== -1) {
        // Generate new HTML
        var newLabel = newValue == 1 ? 'YES' : 'NO';
        var badgeClass = newValue == 1 ? 'label-success' : 'label-default';
        var statusHtml = generateIsDefaultHtml(newValue, presentationId, newLabel, badgeClass);
        
        // Update cell (column 5 is Is Default)
        var cell = table.cell(rowIndex, 5).node();
        $(cell).html('<div class="text-center" data-order="' + newLabel + '">' + statusHtml + '</div>');
        
        table.order(currentOrder).draw(false);
    } else {
        // Fallback: reload table
        table.ajax.reload(function() {
            table.order(currentOrder).draw(false);
        }, false);
    }
}

// Update Active display in place without reloading the table
function updateActiveInPlace(presentationId, newValue) {
    var table = $('.table-ella_presentations').DataTable();
    var currentOrder = table.order();
    
    // Find the row
    var rowIndex = -1;
    table.rows().every(function(rowIdx, data, node) {
        if (data[1].includes('>' + presentationId + '<')) {
            rowIndex = rowIdx;
            return false;
        }
    });
    
    if (rowIndex !== -1) {
        // Generate new HTML
        var newLabel = newValue == 1 ? 'YES' : 'NO';
        var badgeClass = newValue == 1 ? 'label-success' : 'label-danger';
        var statusHtml = generateActiveHtml(newValue, presentationId, newLabel, badgeClass);
        
        // Update cell (column 6 is Active)
        var cell = table.cell(rowIndex, 6).node();
        $(cell).html('<div class="text-center" data-order="' + newLabel + '">' + statusHtml + '</div>');
        
        table.order(currentOrder).draw(false);
    } else {
        // Fallback: reload table
        table.ajax.reload(function() {
            table.order(currentOrder).draw(false);
        }, false);
    }
}

// Generate Is Default HTML
function generateIsDefaultHtml(value, presentationId, label, badgeClass) {
    var hasPermission = <?php echo has_permission('ella_contractors', '', 'edit') ? 'true' : 'false'; ?>;
    
    var html = '<div class="status-wrapper" style="position: relative; display: inline-block;">';
    html += '<span class="status-button label ' + badgeClass + '" id="is-default-btn-' + presentationId + '" style="cursor: pointer !important;">';
    html += label;
    html += '</span>';
    
    if (hasPermission) {
        html += '<div id="is-default-menu-' + presentationId + '" class="status-dropdown table-export-exclude" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 100px;">';
        
        var options = [
            {value: 1, label: 'YES'},
            {value: 0, label: 'NO'}
        ];
        
        for (var i = 0; i < options.length; i++) {
            if (value != options[i].value) {
                html += '<div class="status-option table-export-exclude" onclick="updateIsDefault(' + options[i].value + ', ' + presentationId + '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
                html += options[i].label;
                html += '</div>';
            }
        }
        
        html += '</div>';
    }
    
    html += '</div>';
    return html;
}

// Generate Active HTML
function generateActiveHtml(value, presentationId, label, badgeClass) {
    var hasPermission = <?php echo has_permission('ella_contractors', '', 'edit') ? 'true' : 'false'; ?>;
    
    var html = '<div class="status-wrapper" style="position: relative; display: inline-block;">';
    html += '<span class="status-button label ' + badgeClass + '" id="active-btn-' + presentationId + '" style="cursor: pointer !important;">';
    html += label;
    html += '</span>';
    
    if (hasPermission) {
        html += '<div id="active-menu-' + presentationId + '" class="status-dropdown table-export-exclude" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 100px;">';
        
        var options = [
            {value: 1, label: 'YES'},
            {value: 0, label: 'NO'}
        ];
        
        for (var i = 0; i < options.length; i++) {
            if (value != options[i].value) {
                html += '<div class="status-option table-export-exclude" onclick="updateActive(' + options[i].value + ', ' + presentationId + '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
                html += options[i].label;
                html += '</div>';
            }
        }
        
        html += '</div>';
    }
    
    html += '</div>';
    return html;
}

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
        previewContent = '<iframe src="' + fileUrl + '" width="100%" height="600px" frameborder="0"></iframe>';
    } else if (fileExt === 'ppt' || fileExt === 'pptx') {
        // Convert PPT/PPTX to PDF for preview
        var pdfPreviewUrl = '<?= admin_url('ella_contractors/presentations/get_preview_pdf/'); ?>' + fileId;
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