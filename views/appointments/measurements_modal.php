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
                    
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="category-tabs">
                        <li class="active">
                            <a href="#siding-tab" data-toggle="tab" data-category="siding">Siding</a>
                        </li>
                        <li>
                            <a href="#roofing-tab" data-toggle="tab" data-category="roofing">Roofing</a>
                        </li>
                        <li>
                            <a href="#windows-tab" data-toggle="tab" data-category="windows">Windows</a>
                        </li>
                        <li>
                            <a href="#doors-tab" data-toggle="tab" data-category="doors">Doors</a>
                        </li>
                    </ul>
                    <input type="hidden" name="category" id="selected-category" value="siding">
                    
                    <div class="tab-content">
                        <!-- Siding Tab - New Estimate Structure -->
                        <div class="tab-pane active" id="siding-tab">
                            <div id="estimate-rows-container">
                                <div class="row estimate-row" data-row="0">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="measurement_name_0">Name</label>
                                            <input type="text" class="form-control" name="measurements[0][name]" id="measurement_name_0" placeholder="Enter measurement name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="measurement_value_0">Value</label>
                                            <input type="number" step="0.0001" class="form-control" name="measurements[0][value]" id="measurement_value_0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="measurement_unit_0">Unit</label>
                                            <select class="form-control selectpicker-unit" name="measurements[0][unit]" id="measurement_unit_0">
                                                <option value="">Select Unit</option>
                                                <option value="cm">Centimeters (cm)</option>
                                                <option value="ft">Feet (ft)</option>
                                                <option value="in">Inches (in)</option>
                                                <option value="m">Meters (m)</option>
                                                <option value="mm">Millimeters (mm)</option>
                                                <option value="sqft">Square Feet (sqft)</option>
                                                <option value="yd">Yards (yd)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="button" class="btn btn-success btn-sm" onclick="addEstimateRow()" title="Add Estimate">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeEstimateRow(this)" title="Remove Row" style="display: none;">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Roofing Tab - New Estimate Structure -->
                        <div class="tab-pane" id="roofing-tab">
                            <div id="estimate-rows-container-roofing">
                                <div class="row estimate-row" data-row="0">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="measurement_name_roofing_0">Name</label>
                                            <input type="text" class="form-control" name="measurements_roofing[0][name]" id="measurement_name_roofing_0" placeholder="Enter measurement name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="measurement_value_roofing_0">Value</label>
                                            <input type="number" step="0.0001" class="form-control" name="measurements_roofing[0][value]" id="measurement_value_roofing_0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="measurement_unit_roofing_0">Unit</label>
                                            <select class="form-control selectpicker-unit" name="measurements_roofing[0][unit]" id="measurement_unit_roofing_0">
                                                <option value="">Select Unit</option>
                                                <option value="cm">Centimeters (cm)</option>
                                                <option value="ft">Feet (ft)</option>
                                                <option value="in">Inches (in)</option>
                                                <option value="m">Meters (m)</option>
                                                <option value="mm">Millimeters (mm)</option>
                                                <option value="sqft">Square Feet (sqft)</option>
                                                <option value="yd">Yards (yd)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="button" class="btn btn-success btn-sm" onclick="addEstimateRow('roofing')" title="Add Estimate">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeEstimateRow(this)" title="Remove Row" style="display: none;">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Windows Tab -->
                        <div class="tab-pane" id="windows-tab">
                            <?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'windows', 'row' => null]); ?>
                        </div>

                        <!-- Doors Tab -->
                        <div class="tab-pane" id="doors-tab">
                            <?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'doors', 'row' => null]); ?>
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

