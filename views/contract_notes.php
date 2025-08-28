<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Ensure jQuery is loaded before any CSRF setup
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
}

// Override the problematic CSRF function to prevent errors
window.csrf_jquery_ajax_setup = function() {
    // Do nothing - prevent the error from general_helper.php
    return false;
};
</script>

<?php init_head(); ?>

<!-- Include module CSS -->
<link rel="stylesheet" href="<?php echo base_url('modules/ella_contractors/assets/css/ella_contractors.css'); ?>">

<div class="content">
    <div class="row">
    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                    <!-- Page Header -->
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="customer-profile-group-heading">
                                <i class="fa fa-sticky-note"></i> Contract Notes
                            </h4>
                            <p class="text-muted">
                                Managing notes for contract: <strong><?php echo htmlspecialchars($contract->subject ?? 'Unknown Contract'); ?></strong>
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-primary" onclick="openAddNoteModal()">
                                <i class="fa fa-plus"></i> Add Note
                            </button>
                            <a href="<?php echo admin_url('ella_contractors/view_contract/' . $contract_id); ?>" class="btn btn-info">
                                <i class="fa fa-arrow-left"></i> Back to Contract
                            </a>
                        </div>
                    </div>
                    <hr class="hr-panel-heading" />
                
                <?php if (isset($table_missing) && $table_missing): ?>
                    <div class="alert alert-warning">
                        <h5><i class="fa fa-exclamation-triangle"></i> Notes Table Not Found</h5>
                        <p>The contract notes table has not been created yet. Please run the database migration to create the required table.</p>
                        <p><strong>Migration File:</strong> <code>application/migrations/201_version_201.php</code></p>
                        <p>After running the migration, refresh this page to start managing notes.</p>
                    </div>
                <?php else: ?>
                
                <!-- Notes Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="widget-card bg-primary text-white">
                            <div class="widget-card-body">
                                <div class="widget-card-icon">
                                    <i class="fa fa-sticky-note fa-2x"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h3 class="widget-card-title"><?php echo $notes_summary['total']; ?></h3>
                                    <p class="widget-card-text">Total Notes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-card bg-success text-white">
                            <div class="widget-card-body">
                                <div class="widget-card-icon">
                                    <i class="fa fa-eye fa-2x"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h3 class="widget-card-title"><?php echo $notes_summary['public']; ?></h3>
                                    <p class="widget-card-text">Public Notes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-card bg-warning text-white">
                            <div class="widget-card-body">
                                <div class="widget-card-icon">
                                    <i class="fa fa-eye-slash fa-2x"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h3 class="widget-card-title"><?php echo $notes_summary['private']; ?></h3>
                                    <p class="widget-card-text">Private Notes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-card bg-info text-white">
                            <div class="widget-card-body">
                                <div class="widget-card-icon">
                                    <i class="fa fa-tags fa-2x"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h3 class="widget-card-title"><?php echo count($notes_summary['by_type']); ?></h3>
                                    <p class="widget-card-text">Note Types</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes List -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="panel-title">Contract Notes</h5>
                                <hr class="hr-panel-separator">
                                
                                <div id="notes-container">
                                    <?php if (!empty($notes)): ?>
                                        <?php foreach ($notes as $note): ?>
                                            <div class="note-item mb-3 p-3 border rounded" data-note-id="<?php echo $note->id; ?>">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-0 text-primary"><?php echo htmlspecialchars($note->note_title); ?></h6>
                                                        <small class="text-muted">
                                                            <i class="fa fa-tag"></i> <?php echo ucfirst($note->note_type); ?>
                                                            <?php if ($note->is_public): ?>
                                                                <span class="badge badge-success ml-2">Public</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-warning ml-2">Private</span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                                            <i class="fa fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a class="dropdown-item" href="#" onclick="editNote(<?php echo $note->id; ?>)">
                                                                <i class="fa fa-edit"></i> Edit
                                                            </a></li>
                                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteNote(<?php echo $note->id; ?>)">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="note-content mb-2">
                                                    <?php echo nl2br(htmlspecialchars($note->note_content)); ?>
                                                </div>
                                                <div class="note-meta">
                                                    <small class="text-muted">
                                                        <i class="fa fa-user"></i> By: <?php echo htmlspecialchars($note->created_by_name . ' ' . $note->created_by_lastname); ?>
                                                        <span class="ml-3">
                                                                                                                         <i class="fa fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($note->created_at)); ?>
                                                        </span>
                                                        <?php if ($note->updated_at): ?>
                                                            <span class="ml-3">
                                                                <i class="fa fa-edit"></i> Updated: <?php echo date('M d, Y H:i', strtotime($note->updated_at)); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fa fa-sticky-note fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No notes available for this contract yet.</p>
                                            <button type="button" class="btn btn-primary" onclick="openAddNoteModal()">
                                                <i class="fa fa-plus"></i> Add First Note
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalTitle">Add New Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="noteForm">
                <div class="modal-body">
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                    <input type="hidden" id="note_id" name="note_id">
                    <input type="hidden" id="contract_id" name="contract_id" value="<?php echo $contract_id; ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="note_title">Note Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="note_title" name="note_title" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="note_type">Note Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="note_type" name="note_type" required>
                                    <option value="general">General</option>
                                    <option value="progress">Progress</option>
                                    <option value="issue">Issue</option>
                                    <option value="milestone">Milestone</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="note_content">Note Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="note_content" name="note_content" rows="6" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" checked>
                            <label class="custom-control-label" for="is_public">
                                Make this note visible to clients (public)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveNoteBtn">
                                                        <i class="fa fa-save"></i> Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.widget-card {
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.widget-card-body {
    display: flex;
    align-items: center;
}

.widget-card-icon {
    margin-right: 15px;
}

.widget-card-content h3 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
}

.widget-card-content p {
    margin: 5px 0 0 0;
    opacity: 0.9;
}

.note-item {
    transition: all 0.3s ease;
}

.note-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.note-content {
    line-height: 1.6;
}

.note-meta {
    border-top: 1px solid #eee;
    padding-top: 10px;
}
</style>

<script>
let isEditMode = false;

function openAddNoteModal() {
    isEditMode = false;
    $('#noteModalTitle').text('Add New Note');
    $('#noteForm')[0].reset();
    $('#note_id').val('');
    $('#is_public').prop('checked', true);
    $('#noteModal').modal('show');
}

function editNote(noteId) {
    isEditMode = true;
    $('#noteModalTitle').text('Edit Note');
    
    // Get note data via AJAX
    $.ajax({
        url: '<?php echo admin_url("ella_contractors/get_contract_notes_ajax/" . $contract_id); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const note = response.notes.find(n => n.id == noteId);
                if (note) {
                    $('#note_id').val(note.id);
                    $('#note_title').val(note.note_title);
                    $('#note_content').val(note.note_content);
                    $('#note_type').val(note.note_type);
                    $('#is_public').prop('checked', note.is_public == 1);
                    $('#noteModal').modal('show');
                }
            }
        },
        error: function(xhr) {
            if (xhr.status === 419) {
                alert_float('danger', 'CSRF token expired. Please refresh the page and try again.');
                location.reload();
            } else {
                alert_float('danger', 'Failed to load note data.');
            }
        }
    });
}

function deleteNote(noteId) {
    if (confirm('Are you sure you want to delete this note? This action cannot be undone.')) {
        $.ajax({
            url: '<?php echo admin_url("ella_contractors/delete_note_ajax"); ?>',
            type: 'POST',
            data: { 
                note_id: noteId,
                '<?php echo $this->security->get_csrf_token_name(); ?>': $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update CSRF token if provided
                    if (response.csrf_token) {
                        $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    }
                    
                    // Remove note from DOM
                    $('.note-item[data-note-id="' + noteId + '"]').fadeOut(300, function() {
                        $(this).remove();
                        // Refresh notes count
                        location.reload();
                    });
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message);
                    
                    // Update CSRF token if provided (even on error)
                    if (response.csrf_token) {
                        $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    alert_float('danger', 'CSRF token expired. Please refresh the page and try again.');
                    // Refresh the page to get a new CSRF token
                    location.reload();
                } else {
                    alert_float('danger', 'An error occurred while deleting the note.');
                }
            }
        });
    }
}

$(document).ready(function() {
    // Function to refresh CSRF token
    function refreshCSRFToken() {
        $.ajax({
            url: '<?php echo admin_url("ella_contractors/get_contract_notes_ajax/" . $contract_id); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                }
            }
        });
    }
    
    // Refresh CSRF token every 5 minutes to prevent expiration
    setInterval(refreshCSRFToken, 300000);
    
    $('#noteForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = isEditMode ? 
            '<?php echo admin_url("ella_contractors/update_note_ajax"); ?>' : 
            '<?php echo admin_url("ella_contractors/add_note_ajax"); ?>';
        
        $('#saveNoteBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#noteModal').modal('hide');
                    alert_float('success', response.message);
                    
                    // Update CSRF token if provided
                    if (response.csrf_token) {
                        $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    }
                    
                    // Refresh the page to show new/updated note
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                    
                    // Update CSRF token if provided (even on error)
                    if (response.csrf_token) {
                        $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    alert_float('danger', 'CSRF token expired. Please refresh the page and try again.');
                    // Refresh the page to get a new CSRF token
                    location.reload();
                } else {
                    alert_float('danger', 'An error occurred while saving the note.');
                }
            },
            complete: function() {
                $('#saveNoteBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Note');
            }
        });
            });
    });
</script>

                </div><!-- /.panel-body -->
            </div><!-- /.panel_s -->
        </div><!-- /.col-md-12 -->
    </div><!-- /.row -->
</div><!-- /.content -->

<?php init_tail(); ?>
