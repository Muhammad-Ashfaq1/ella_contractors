<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <!-- Add Note Section -->
        <div class="row mbot15">
            <div class="col-md-12">
                <div class="form-group" id="appointmentnote">
                    <div class="lead emoji-picker-container leadnotes">
                        <textarea id="js-appointment_note_description" name="appointment_note_description" class="form-control" rows="3" data-emojiable="true" placeholder="Add a note about this appointment..."></textarea>
                    </div>
                </div>
                <div class="text-right" id="note-btn-container">
                    <button type="button" class="btn btn-info btn-sm" onclick="addNote()" id="note-btn">
                        <i class="fa fa-plus"></i> Add Note
                    </button>
                </div>
            </div>
        </div>
        
        <hr class="hr-panel-heading" />
        
        <!-- Notes Display -->
        <div class="panel_s no-shadow">
            <div class="panel-body">
                <div id="appointment-notes-container">
                    <!-- Notes will be loaded here via AJAX -->
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Loading notes...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #note-btn{
        background-color: #5bc0de !important;
        border-color: #46b8da !important;
        color: #fff !important;
    }
    
    /* Smooth transitions for note operations */
    .timeline-record-wrapper {
        transition: opacity 0.3s ease;
    }

    .edit-note-form-wrapper {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Disabled button state */
    #note-btn:disabled,
    .btn-info:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Loading note wrapper */
    .timeline-record-wrapper[style*="opacity: 0.5"] {
        pointer-events: none;
    }
    
    /* Better focus state for edit textarea */
    .edit-note-form-wrapper textarea:focus {
        border-color: #5bc0de;
        box-shadow: 0 0 8px rgba(91, 192, 222, 0.3);
    }
</style>