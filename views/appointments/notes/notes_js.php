<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
/**
 * IMPROVED Notes JavaScript - Prevents Scroll/Refresh Issues
 * Production-ready with proper async handling
 */

// Global variable to track notes loading state
window.notesLoading = false;

/**
 * Load notes with optional scroll position preservation
 * @param {boolean} preserveScroll - Whether to maintain scroll position
 * @param {function} callback - Optional callback after notes loaded
 */
function loadNotes(preserveScroll = false, callback = null) {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    if (!appointmentId) {
        $('#appointment-notes-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Invalid appointment ID.</p></div>');
        return;
    }
    
    // Prevent multiple simultaneous loads
    if (window.notesLoading) {
        return;
    }
    
    window.notesLoading = true;
    
    // Save scroll position BEFORE making AJAX call
    var savedScrollPosition = preserveScroll ? $(window).scrollTop() : null;
    
    // Show loading only if container is empty
    if ($('#appointment-notes-container').children().length === 0) {
        $('#appointment-notes-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading notes...</p></div>');
    }
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_notes/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayNotes(response.data, function() {
                    // RESTORE SCROLL AFTER DOM IS RENDERED
                    if (savedScrollPosition !== null) {
                        // Use requestAnimationFrame for smooth scroll restoration
                        requestAnimationFrame(function() {
                            $(window).scrollTop(savedScrollPosition);
                        });
                    }
                    
                    // Execute callback if provided
                    if (typeof callback === 'function') {
                        callback();
                    }
                });
            } else {
                $('#appointment-notes-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No notes found for this appointment.</p></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading notes:', error, xhr.responseText);
            $('#appointment-notes-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading notes. Please try again.</p></div>');
        },
        complete: function() {
            window.notesLoading = false;
        }
    });
}

/**
 * Display notes in the container with callback after rendering
 * @param {array} notes - Array of note objects
 * @param {function} callback - Called after DOM update
 */
function displayNotes(notes, callback = null) {
    if (notes.length === 0) {
        $('#appointment-notes-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No notes found for this appointment.</p></div>');
        if (typeof callback === 'function') callback();
        return;
    }

    var html = '';
    
    notes.forEach(function(note) {
        var timeAgo = note.time_ago || moment(note.dateadded).fromNow();
        var staffName = note.firstname + ' ' + note.lastname;
        
        html += '<div class="timeline-record-wrapper" data-note-id="' + note.id + '">';
        html += '<div class="timeline-date-section">';
        html += '<div class="date">';
        html += '<span class="text-has-action" data-toggle="tooltip" data-title="' + note.dateadded + '">' + timeAgo + '</span>';
        html += '</div>';
        html += '</div>';
        html += '<div class="timeline-content-section">';
        html += '<div class="text">';
        
        // Profile image and name
        html += '<a href="' + admin_url + 'profile/' + note.addedfrom + '">';
        var profileImageUrl = note.profile_image || admin_url + 'assets/images/user-placeholder.jpg';
        html += '<img class="staff-profile-xs-image pull-left mright5" src="' + profileImageUrl + '" alt="' + staffName + '">';
        html += '</a>';
        
        // Format name
        var nameParts = staffName.split(' ');
        var formattedName = nameParts.length >= 2 ? nameParts[0] + ' ' + nameParts[nameParts.length - 1].charAt(0) + '.' : staffName;
        
        html += '<span class="timeline-formatted-entry">';
        html += '<strong>' + formattedName + '</strong> - ';
        html += '<span class="note-description">' + note.description + '</span>';
        html += '</span>';
        
        html += '</div>';
        
        // Action buttons
        html += '<div class="text-right mtop5 note-actions">';
        html += '<button class="btn btn-default btn-xs" onclick="editNote(' + note.id + ')" title="Edit Note"><i class="fa fa-edit"></i></button> ';
        html += '<button class="btn btn-danger btn-xs" onclick="deleteNote(' + note.id + ')" title="Delete Note"><i class="fa fa-trash"></i></button>';
        html += '</div>';
        
        html += '</div>';
        html += '</div>';
    });
    
    $('#appointment-notes-container').html(html);
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Set up time refresh every minute
    if (typeof window.notesTimeRefresh !== 'undefined') {
        clearInterval(window.notesTimeRefresh);
    }
    
    window.notesTimeRefresh = setInterval(function() {
        refreshNotesTime();
    }, 60000);
    
    // Execute callback AFTER DOM is fully updated
    if (typeof callback === 'function') {
        // Use setTimeout to ensure DOM has rendered
        setTimeout(callback, 50);
    }
}

/**
 * Refresh time display for notes
 */
function refreshNotesTime() {
    $('#appointment-notes-container .timeline-record-wrapper .date .text-has-action').each(function() {
        var $this = $(this);
        var originalDate = $this.data('title');
        if (originalDate) {
            var timeAgo = moment(originalDate).fromNow();
            $this.text(timeAgo);
        }
    });
}

// Cleanup function to clear interval when page is unloaded
$(window).on('beforeunload', function() {
    if (typeof window.notesTimeRefresh !== 'undefined') {
        clearInterval(window.notesTimeRefresh);
    }
});

/**
 * Add note function - IMPROVED with scroll preservation
 */
function addNote() {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    if (!appointmentId) {
        alert_float('danger', 'Invalid appointment ID');
        return;
    }
    
    var description = $('#js-appointment_note_description').val().trim();
    
    if (!description) {
        alert_float('warning', 'Please enter a note description');
        $('#js-appointment_note_description').focus();
        return;
    }
    
    // Disable button and show loading state
    var $btn = $('#note-btn');
    var originalBtnHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/add_note/' + appointmentId,
        type: 'POST',
        data: {
            description: description,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Clear textarea
                $('#js-appointment_note_description').val('');
                
                // Clear emoji editor if exists
                var emojiEditor = $('#js-appointment_note_description')
                    .closest('.emoji-picker-container')
                    .find('.emoji-wysiwyg-editor');
                if (emojiEditor.length) {
                    emojiEditor.html('').text('');
                }
                
                // Reload notes WITH scroll preservation
                loadNotes(true, function() {
                    // Show success message AFTER notes are loaded
                    alert_float('success', response.message || 'Note added successfully');
                });
            } else {
                alert_float('danger', response.message || 'Failed to add note');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding note:', error);
            alert_float('danger', 'Error adding note. Please try again.');
        },
        complete: function() {
            // Restore button state
            $btn.prop('disabled', false).html(originalBtnHtml);
        }
    });
}

/**
 * Edit note - Open inline edit form
 */
function editNote(noteId) {
    // Close any existing edit form
    var existingEditForm = $('[data-note-edit]');
    if (existingEditForm.length > 0) {
        var existingNoteId = existingEditForm.attr('data-note-edit');
        if (existingNoteId == noteId) {
            return; // Already editing this note
        }
        cancelEditNote(existingNoteId);
    }
    
    openEditForm(noteId);
}

/**
 * Open edit form for a note
 */
function openEditForm(noteId) {
    var noteWrapper = $('[data-note-id="' + noteId + '"]');
    
    if (noteWrapper.length === 0) {
        alert_float('danger', 'Note not found');
        return;
    }
    
    var noteDescriptionElement = noteWrapper.find('.note-description');
    var noteText = noteDescriptionElement.html();
    var textContent = noteText ? noteText.replace(/<[^>]*>/g, '').trim() : '';
    
    // Hide action buttons
    noteWrapper.find('.note-actions').hide();
    
    // Create inline edit form
    var editForm = '<div class="edit-note-form-wrapper mtop10" data-note-edit="' + noteId + '" style="margin-left: 30px; padding-left: 15px; border-left: 3px solid #5bc0de;">';
    editForm += '<div class="form-group">';
    editForm += '<label><i class="fa fa-edit"></i> Edit Note:</label>';
    editForm += '<div class="lead emoji-picker-container leadnotes">';
    editForm += '<textarea class="form-control" rows="3" id="edit-note-' + noteId + '" data-emojiable="true">' + textContent + '</textarea>';
    editForm += '</div>';
    editForm += '</div>';
    editForm += '<div class="text-right">';
    editForm += '<button class="btn btn-default btn-xs" onclick="cancelEditNote(' + noteId + ')"><i class="fa fa-times"></i> Cancel</button> ';
    editForm += '<button class="btn btn-info btn-xs" onclick="updateNote(' + noteId + ')" style="background-color: #5bc0de !important;"><i class="fa fa-check"></i> Update</button>';
    editForm += '</div>';
    editForm += '</div>';
    
    noteWrapper.find('.timeline-content-section').append(editForm);
    
    // Focus textarea
    $('#edit-note-' + noteId).focus();
    
    // Re-initialize emoji picker if available
    if (typeof window.emojiPicker !== 'undefined') {
        window.emojiPicker.discover();
    }
}

/**
 * Cancel edit note
 */
function cancelEditNote(noteId) {
    $('[data-note-edit="' + noteId + '"]').remove();
    $('[data-note-id="' + noteId + '"]').find('.note-actions').show();
}

/**
 * Update note - IMPROVED with scroll preservation
 */
function updateNote(noteId) {
    var appointmentId = <?php echo isset($appointment->id) ? (int)$appointment->id : 0; ?>;
    
    if (!appointmentId) {
        alert_float('danger', 'Invalid appointment ID');
        return;
    }
    
    var newDescription = $('#edit-note-' + noteId).val().trim();
    
    if (!newDescription) {
        alert_float('danger', 'Note description cannot be empty');
        return;
    }
    
    // Show loading state
    var $updateBtn = $('[data-note-edit="' + noteId + '"]').find('.btn-info');
    var originalBtnText = $updateBtn.html();
    $updateBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/add_note/' + appointmentId + '/' + noteId,
        type: 'POST',
        data: {
            description: newDescription,
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Reload notes WITH scroll preservation
                loadNotes(true, function() {
                    alert_float('success', 'Note updated successfully');
                });
            } else {
                alert_float('danger', response.message || 'Failed to update note');
                $updateBtn.prop('disabled', false).html(originalBtnText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating note:', error);
            alert_float('danger', 'Error updating note. Please try again.');
            $updateBtn.prop('disabled', false).html(originalBtnText);
        }
    });
}

/**
 * Delete note - IMPROVED with scroll preservation
 */
function deleteNote(noteId) {
    if (!confirm('Are you sure you want to delete this note?')) {
        return;
    }
    
    // Show loading state on the note wrapper
    var noteWrapper = $('[data-note-id="' + noteId + '"]');
    noteWrapper.css('opacity', '0.5');
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/delete_note/' + noteId,
        type: 'POST',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Reload notes WITH scroll preservation
                loadNotes(true, function() {
                    alert_float('success', 'Note deleted successfully');
                });
            } else {
                alert_float('danger', response.message || 'Failed to delete note');
                noteWrapper.css('opacity', '1');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting note:', error);
            alert_float('danger', 'Error deleting note. Please try again.');
            noteWrapper.css('opacity', '1');
        }
    });
}

// Cleanup interval on page unload
$(window).on('beforeunload', function() {
    if (typeof window.notesTimeRefresh !== 'undefined') {
        clearInterval(window.notesTimeRefresh);
    }
});

// Handle legacy form submission (if any)
$(document).ready(function() {
    $("body").on('submit', '#appointment-notes', function(e) {
        e.preventDefault(); // PREVENT default form submission
        
        var form = $(this);
        var data = form.serialize();
        
        $.post(form.attr('action'), data)
            .done(function(response) {
                try {
                    response = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (response.success) {
                        // Clear textarea
                        $("#appointment_note_description").val('');
                        $(".emoji-wysiwyg-editor").html('').text('');
                        
                        // Hide form if toggle function exists
                        if (typeof toggleNoteForm === 'function') {
                            toggleNoteForm();
                        }
                        
                        // Reload notes WITH scroll preservation
                        loadNotes(true, function() {
                            alert_float('success', 'Note added successfully');
                        });
                    } else {
                        alert_float('danger', response.message || 'Failed to add note');
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    alert_float('danger', 'Unexpected error occurred');
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Error submitting note:', error);
                alert_float('danger', 'Error adding note. Please try again.');
            });
        
        return false;
    });
});
</script>
