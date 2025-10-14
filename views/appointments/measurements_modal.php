<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Measurement Modal -->
<div class="modal fade" id="measurementModal" tabindex="-1" role="dialog" aria-labelledby="measurementModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="measurementModalLabel">Add Measurement</h4>
            </div>
            <form id="measurementForm" method="post" action="javascript:void(0);" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" id="measurement_id" name="id" value="">
                    <input type="hidden" name="rel_type" value="appointment">
                    <input type="hidden" name="rel_id" value="<?php echo $appointment->id; ?>">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment->id; ?>">
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    
                    <!-- Tab Navigation with Add Tab Button -->
                    <div style="position: relative;">
                        <ul class="nav nav-tabs mb-3" id="dynamic-tabs">
                            <!-- Tabs will be added dynamically -->
                        </ul>
                        <div style="position: absolute; top: 5px; right: 0;">
                            <button type="button" class="btn btn-primary btn-sm" id="addTabBtn" onclick="addNewTab()" title="Add Category">
                                <i class="fa fa-plus"></i> Add Category
                            </button>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="dynamic-tab-content">
                        <!-- Tab content will be added dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelMeasurement">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveMeasurement">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Fix Select Unit placeholder opacity to match other placeholders */
.selectpicker-unit option[value=""] {
    opacity: 0.5;
    color: #999;
}

/* Custom tab name input styling */
.custom-tab-name-input {
    border: none !important;
    border-bottom: 2px dashed #3498db !important;
    background: transparent !important;
    padding: 2px 5px !important;
    width: 120px !important;
    color: #333 !important;
    font-size: 14px !important;
    outline: none !important;
}

.custom-tab-name-input:focus {
    border-bottom-color: #2980b9 !important;
    background-color: rgba(52, 152, 219, 0.05) !important;
}

.custom-tab-name-input::placeholder {
    color: #95a5a6;
    font-style: italic;
    opacity: 0.7;
}

/* Pulse animation for Save button */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
</style>

