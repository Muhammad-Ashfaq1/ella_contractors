<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <!-- Add Note Section -->
        <div class="row mbot15">
            <div class="col-md-12">
                <div class="form-group" id="appointmentnote">
                    <div class="lead emoji-picker-container leadnotes">
                        <textarea id="appointment_note_description" name="appointment_note_description" class="form-control" rows="3" data-emojiable="true" placeholder="Add a note about this appointment..."></textarea>
                    </div>
                </div>
                <div class="text-right">
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
/* Ensure button maintains color when clicked */
#note-btn.btn-info {
    background-color: #5bc0de !important;
    border-color: #46b8da !important;
    color: #fff !important;
}

#note-btn.btn-info:hover {
    background-color: #31b0d5 !important;
    border-color: #269abc !important;
    color: #fff !important;
}

#note-btn.btn-info:active,
#note-btn.btn-info:focus,
#note-btn.btn-info.active {
    background-color: #31b0d5 !important;
    border-color: #269abc !important;
    color: #fff !important;
    box-shadow: none !important;
}

#note-btn.btn-info:disabled {
    background-color: #5bc0de !important;
    border-color: #46b8da !important;
    opacity: 0.65 !important;
    color: #fff !important;
}
</style>
