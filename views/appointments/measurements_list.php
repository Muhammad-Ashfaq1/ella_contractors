<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Measurements Tab Content -->
<div class="row">
    <div class="col-md-12">
        <div class="pull-right mbot15">
            <button type="button" class="btn btn-info btn-sm" onclick="openMeasurementModal()">
                <i class="fa fa-plus"></i> Add Measurement
            </button>
        </div>
        <div class="clearfix"></div>
        <hr class="hr-panel-heading" />
        
        <div id="measurements-container">
            <!-- Measurements will be loaded here via AJAX -->
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <p>Loading measurements...</p>
            </div>
        </div>
    </div>
</div>

