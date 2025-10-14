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
                    
                    <!-- Tab Navigation with Add Custom Tab Button -->
                    <div style="position: relative;">
                        <ul class="nav nav-tabs mb-3" id="category-tabs">
                            <li class="active">
                                <a href="#siding-tab" data-toggle="tab" data-category="siding">Siding</a>
                            </li>
                            <li>
                                <a href="#roofing-tab" data-toggle="tab" data-category="roofing">Roofing</a>
                            </li>
                        </ul>
                        <div style="position: absolute; top: 5px; right: 0;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addNewMeasurementTab()" title="Add Custom Category">
                                <i class="fa fa-plus"></i> Add Category
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="category" id="selected-category" value="siding">
                    
                    <div class="tab-content">
                        <!-- Siding Tab -->
                        <div class="tab-pane active" id="siding-tab" data-category="siding">
                            <div id="estimate-rows-container-siding"></div>
                        </div>

                        <!-- Roofing Tab -->
                        <div class="tab-pane" id="roofing-tab" data-category="roofing">
                            <div id="estimate-rows-container-roofing"></div>
                        </div>
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
</style>

