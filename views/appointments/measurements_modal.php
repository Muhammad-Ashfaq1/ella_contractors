<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Measurement Modal -->
<div class="modal fade" id="measurementModal" tabindex="-1" role="dialog" aria-labelledby="measurementModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="measurementModalLabel">Add Measurement Category</h4>
            </div>
            <form id="measurementForm" method="post" action="javascript:void(0);" onsubmit="return false;">
                <div class="modal-body">
                    <input type="hidden" id="measurement_id" name="id" value="">
                    <input type="hidden" name="rel_type" value="appointment">
                    <input type="hidden" name="rel_id" value="<?php echo $appointment->id; ?>">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment->id; ?>">
                    <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                    
                    <!-- Tab Navigation with Add Tab Button -->
                    <div class="tab-navigation-container">
                        <div class="tabs-scroll-container">
                            <ul class="nav nav-tabs mb-3" id="dynamic-tabs">
                                <!-- Tabs will be added dynamically -->
                            </ul>
                        </div>
                        <div class="add-category-btn-container">
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
.measurement-row select option.placeholder-option {
    color: #95a5a6 !important;
    opacity: 0.7 !important;
    font-style: italic !important;
}

/* Ensure other options maintain normal opacity and color */
.measurement-row select option:not(.placeholder-option) {
    color: #333 !important;
    opacity: 1 !important;
}

.measurement-row select {
    color: #333 !important;
}

.measurement-row select:invalid,
.measurement-row select.placeholder-active {
    color: #95a5a6 !important;
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

/* Measurement row styling */
.measurement-row .form-control {
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
}

.measurement-row .form-control:focus {
    border-color: #3498db !important;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.3) !important;
}

.measurement-row .form-control::placeholder {
    color: #95a5a6 !important;
    opacity: 0.7 !important;
}

.measurement-row label {
    color: #333 !important;
    font-weight: 500 !important;
    margin-bottom: 5px !important;
    font-size: 14px !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}

.measurement-row .btn {
    border-radius: 4px !important;
    transition: all 0.2s ease !important;
}

.measurement-row .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.measurement-row .btn-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.measurement-row .btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

/* Tab navigation container */
.tab-navigation-container {
    display: flex !important;
    align-items: flex-start !important;
    width: 100% !important;
    position: relative !important;
}

.tabs-scroll-container {
    flex: 1 !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    margin-right: 10px !important;
}

.add-category-btn-container {
    flex-shrink: 0 !important;
    position: relative !important;
    z-index: 10 !important;
}

/* Tab styling improvements */
.tab-title {
    display: inline-block;
    margin-right: 2px !important;
}

.tab-remove-btn {
    padding: 0 4px !important;
    margin-left: 2px !important;
    border: none !important;
    background: transparent !important;
}

.tab-remove-btn:hover {
    background-color: rgba(220, 53, 69, 0.1) !important;
    border-radius: 3px !important;
}

#dynamic-tabs {
    border-bottom: 1px solid #ddd;
    white-space: nowrap !important;
    display: inline-block !important;
    min-width: 100% !important;
}

#dynamic-tabs li {
    display: inline-block !important;
    float: none !important;
    margin-right: 2px;
}

#dynamic-tabs li a {
    display: inline-flex !important;
    align-items: center !important;
    padding: 8px 12px !important;
    text-decoration: none !important;
    border: 1px solid transparent !important;
    border-radius: 4px 4px 0 0 !important;
    background-color: #f8f9fa !important;
    color: #495057 !important;
    white-space: nowrap !important;
}

#dynamic-tabs li.active a {
    background-color: #fff !important;
    border-color: #ddd #ddd #fff !important;
    color: #007bff !important;
    border-bottom: 1px solid #fff !important;
}

/* Scroll styling */
.tabs-scroll-container::-webkit-scrollbar {
    height: 6px;
}

.tabs-scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.tabs-scroll-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.tabs-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

