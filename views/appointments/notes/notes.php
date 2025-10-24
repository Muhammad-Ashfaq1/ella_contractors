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
                    <button type="button" class="btn btn-primary btn-sm" onclick="addNote()" id="note-btn">
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
/* Ensure button maintains color when clicked - matching CRM primary button */
#note-btn.btn-primary,
#note-btn.btn-primary:visited {
    background-color: #337ab7 !important;
    border-color: #2e6da4 !important;
    color: #fff !important;
}

#note-btn.btn-primary:hover {
    background-color: #286090 !important;
    border-color: #204d74 !important;
    color: #fff !important;
}

#note-btn.btn-primary:active,
#note-btn.btn-primary:focus,
#note-btn.btn-primary.active,
#note-btn.btn-primary:active:focus {
    background-color: #337ab7 !important;
    border-color: #2e6da4 !important;
    color: #fff !important;
    box-shadow: none !important;
    outline: none !important;
}

/* Maintain color when disabled/loading */
#note-btn.btn-primary:disabled,
#note-btn.btn-primary[disabled] {
    background-color: #337ab7 !important;
    border-color: #2e6da4 !important;
    color: #fff !important;
    opacity: 0.8 !important;
    cursor: not-allowed !important;
}

/* Prevent Bootstrap default styles from overriding */
.btn-primary:not(:disabled):not(.disabled):active,
.btn-primary:not(:disabled):not(.disabled).active,
.show > .btn-primary.dropdown-toggle {
    background-color: #337ab7 !important;
    border-color: #2e6da4 !important;
}
</style>
