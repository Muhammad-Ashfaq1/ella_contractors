<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
/**
 * Notes JavaScript functionality for appointment view
 * This file contains all JavaScript functions related to appointment notes
 */

// Notes Functions
function loadNotes() {
    var appointmentId = <?php echo isset($appointment) ? $appointment->id : 'appointmentId'; ?>;
    
    // Show loading indicator
    $('#appointment-notes-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading notes...</p></div>');
    
    $.ajax({
        url: admin_url + 'ella_contractors/appointments/get_notes/' + appointmentId,
        type: 'GET',
        data: {
            [csrf_token_name]: csrf_hash
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayNotes(response.data);
            } else {
                $('#appointment-notes-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No notes found for this appointment.</p></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading notes:', error, xhr.responseText);
            $('#appointment-notes-container').html('<div class="text-center text-danger"><i class="fa fa-exclamation-triangle fa-2x"></i><p>Error loading notes. Please try again.</p></div>');
        }
    });
}

function displayNotes(notes) {
    if (notes.length === 0) {
        $('#appointment-notes-container').html('<div class="text-center text-muted"><i class="fa fa-info-circle fa-2x"></i><p>No notes found for this appointment.</p></div>');
        return;
    }

    var html = '';
    var len = notes.length;
    var i = 0;
    
    notes.forEach(function(note) {
        var timeAgo = note.time_ago || moment(note.dateadded).fromNow();
        var staffName = note.firstname + ' ' + note.lastname;
        var staffProfileImage = note.profile_image && note.profile_image !== '' ? note.profile_image : admin_url + 'assets/images/user-placeholder.jpg';
        
        // Use same layout structure as timeline
        html += '<div class="timeline-record-wrapper">';
        html += '<div class="timeline-date-section">';
        html += '<div class="date">';
        html += '<span class="text-has-action" data-toggle="tooltip" data-title="' + note.dateadded + '" data-original-title="" title="">' + timeAgo + '</span>';
        html += '</div>';
        html += '</div>';
        html += '<div class="timeline-content-section">';
        html += '<div class="text">';
        
        // Use exact same icon system as timeline
        html += '<a href="' + admin_url + 'admin/profile/' + note.addedfrom + '">';
        
        // Use the profile_image from the server (same as timeline)
        var profileImageUrl = note.profile_image || admin_url + 'assets/images/user-placeholder.jpg';
        html += '<img class="staff-profile-xs-image pull-left mright5" src="' + profileImageUrl + '" alt="' + staffName + '">';
        
        html += '</a>';
        
        // Format name like timeline: First Name + Last Initial
        var nameParts = staffName.split(' ');
        var formattedName = '';
        if (nameParts.length >= 2) {
            var firstName = nameParts[0];
            var lastInitial = nameParts[nameParts.length - 1].charAt(0) + '.';
            formattedName = firstName + ' ' + lastInitial;
        } else {
            formattedName = staffName;
        }
        
        // Display in timeline format: ICON Name - Note content
        html += '<span class="timeline-formatted-entry">';
        html += '<strong>' + formattedName + '</strong> - ';
        html += '<span class="note-description">' + note.description + '</span>';
        html += '</span>';
        
        html += '</div>';
        
        // Action buttons
        html += '<div class="text-right mtop5">';
        html += '<button class="btn btn-default btn-xs" onclick="editNote(' + note.id + ')" title="Edit Note"><i class="fa fa-edit"></i></button> ';
        html += '<button class="btn btn-danger btn-xs" onclick="deleteNote(' + note.id + ')" title="Delete Note"><i class="fa fa-trash"></i></button>';
        html += '</div>';
        
        html += '</div>';
        html += '</div>';
        
        if (i >= 0 && i != len - 1) {
            html += '<hr />';
        }
        i++;
    });
    
    $('#appointment-notes-container').html(html);
    
    // Set up time refresh every minute
    if (typeof window.notesTimeRefresh !== 'undefined') {
        clearInterval(window.notesTimeRefresh);
    }
    
    window.notesTimeRefresh = setInterval(function() {
        refreshNotesTime();
    }, 60000); // Refresh every minute
}

// Function to refresh time display for notes
function refreshNotesTime() {
    $('.feed-item .date .text-has-action').each(function() {
        var $this = $(this);
        var originalDate = $this.data('title');
        if (originalDate) {
            // Use moment.js to calculate fresh time
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

// Add note function
function addNote() {
    var appointmentId = <?php echo isset($appointment) ? $appointment->id : 'appointmentId'; ?>;
    var description = $('#appointment_note_description').val().trim();
    
    if (!description) {
        alert_float('warning', 'Please enter a note description');
        $('#appointment_note_description').focus();
        return;
    }
    
    // Show loading state
    var originalBtn = $('#note-btn').html();
    $('#note-btn').html('<i class="fa fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
    
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
                // Clear the textarea
                $('#appointment_note_description').val('');
                // Reload notes
                loadNotes();
                alert_float('success', response.message || 'Note added successfully');
            } else {
                alert_float('danger', response.message || 'Failed to add note');
            }
        },
        error: function(xhr, status, error) {
            alert_float('danger', 'Error adding note');
        },
        complete: function() {
            // Restore button
            $('#note-btn').html(originalBtn).prop('disabled', false);
        }
    });
}

function editNote(noteId) {
    // Get the note content - using timeline structure
    var noteWrapper = $('button[onclick="editNote(' + noteId + ')"]').closest('.timeline-record-wrapper');
    
    if (noteWrapper.length === 0) {
        alert_float('danger', 'Note element not found');
        return;
    }
    
    var noteDescriptionElement = noteWrapper.find('.note-description');
    
    if (noteDescriptionElement.length === 0) {
        alert_float('danger', 'Note content not found');
        return;
    }
    
    var noteText = noteDescriptionElement.html();
    
    // Extract just the text content (remove HTML tags)
    var textContent = noteText ? noteText.replace(/<[^>]*>/g, '').trim() : '';
    
    // Create edit form
    var editForm = '<div class="timeline-record-wrapper" data-note-edit="' + noteId + '">';
    editForm += '<div class="timeline-date-section">';
    editForm += '<div class="date"><i class="fa fa-edit text-info"></i></div>';
    editForm += '</div>';
    editForm += '<div class="timeline-content-section">';
    editForm += '<div class="form-group">';
    editForm += '<div class="lead emoji-picker-container leadnotes">';
    editForm += '<textarea class="form-control" rows="3" id="edit-note-' + noteId + '" data-emojiable="true">' + textContent + '</textarea>';
    editForm += '</div>';
    editForm += '</div>';
    
    editForm += '<div class="text-right">';
    editForm += '<button class="btn btn-default btn-xs" onclick="cancelEditNote(' + noteId + ')" title="Cancel"><i class="fa fa-times"></i> Cancel</button> ';
    editForm += '<button class="btn btn-info btn-xs" onclick="updateNote(' + noteId + ')" title="Update"><i class="fa fa-check"></i> Update</button>';
    editForm += '</div>';
    editForm += '</div>';
    editForm += '</div>';
    
    // Replace the note with edit form
    noteWrapper.replaceWith(editForm);
    
    // Re-initialize emoji picker for the new textarea
    if (typeof window.emojiPicker !== 'undefined') {
        window.emojiPicker.discover();
    }
}

function cancelEditNote(noteId) {
    // Reload notes to restore original display
    loadNotes();
}

function updateNote(noteId) {
    var appointmentId = <?php echo isset($appointment) ? $appointment->id : 'appointmentId'; ?>;
    var newDescription = $('#edit-note-' + noteId).val();
    
    if (!newDescription.trim()) {
        alert_float('danger', 'Note description cannot be empty');
        return;
    }
    
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
                alert_float('success', 'Note updated successfully!');
                loadNotes(); // Reload notes
            } else {
                alert_float('danger', response.message || 'Failed to update note');
            }
        },
        error: function() {
            alert_float('danger', 'Error updating note');
        }
    });
}

function deleteNote(noteId) {
    if (confirm('Are you sure you want to delete this note?')) {
        $.ajax({
            url: admin_url + 'ella_contractors/appointments/delete_note/' + noteId,
            type: 'POST',
            data: {
                [csrf_token_name]: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', 'Note deleted successfully!');
                    loadNotes(); // Reload notes
                } else {
                    alert_float('danger', response.message || 'Failed to delete note');
                }
            },
            error: function() {
                alert_float('danger', 'Error deleting note');
            }
        });
    }
}

// Submit notes on appointment modal do ajax not the regular request
$(document).ready(function() {
    $("body").on('submit', '#appointment-notes', function () {
        var form = $(this);
        var data = $(form).serialize();
        $.post(form.attr('action'), data).done(function (response) {
            response = JSON.parse(response);
            if (response.success) {
                // Clear the textarea and hide form
                $("#appointment_note_description").val('');
                $(".emoji-wysiwyg-editor").text('');
                toggleNoteForm(); // Hide the form
                // Reload notes
                loadNotes();
                alert_float('success', 'Note added successfully!');
            } else {
                alert_float('danger', response.message || 'Failed to add note');
            }
        }).fail(function (data) {
            alert_float('danger', data.responseText);
        });
        return false;
    });
});
</script>

