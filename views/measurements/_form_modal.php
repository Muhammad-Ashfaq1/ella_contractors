<?php

$existing_attributes = [];
if (isset($row) && !empty($row['attributes_json'])) {
    $existing_attributes = json_decode($row['attributes_json'], true) ?: [];
}

$editing = isset($row) && !empty($row);
?>
<!-- <div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Designator</label>
            <input type="text" name="designator" class="form-control" value="<?= html_escape($row['designator'] ?? ''); ?>">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label>Name <span class="text-danger">*</span></label>
            <input required type="text" name="name" class="form-control" value="<?= html_escape($row['name'] ?? ''); ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Quantity</label>
            <input type="number" step="0.01" name="quantity" class="form-control" value="<?= html_escape($row['quantity'] ?? '1'); ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Sort</label>
            <input type="number" name="sort_order" class="form-control" value="<?= html_escape($row['sort_order'] ?? '0'); ?>">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Location</label>
            <select name="location_label" class="form-control">
                <option value="">Select Location</option>
                <?php for($i = 1; $i <= 10; $i++): ?>
                <option value="Bedroom <?= $i; ?>" <?= ($row['location_label'] ?? '') == "Bedroom $i" ? 'selected' : ''; ?>>Bedroom <?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Level</label>
            <select name="level_label" class="form-control">
                <option value="">Select Level</option>
                <?php for($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i; ?>" <?= ($row['level_label'] ?? '') == "$i" ? 'selected' : ''; ?>><?= $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Width</label>
            <input type="number" step="0.01" name="width_val" class="form-control" value="<?= html_escape($row['width_val'] ?? ''); ?>">
        </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            <label>Unit</label>
            <input type="text" name="length_unit" class="form-control" value="<?= html_escape($row['length_unit'] ?? 'in'); ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Height</label>
            <input type="number" step="0.01" name="height_val" class="form-control" value="<?= html_escape($row['height_val'] ?? ''); ?>">
        </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            <label>UI Unit</label>
            <input type="text" name="ui_unit" class="form-control" value="<?= html_escape($row['ui_unit'] ?? 'in'); ?>">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>United Inches</label>
            <input type="number" step="0.01" name="united_inches_val" class="form-control" value="<?= html_escape($row['united_inches_val'] ?? ''); ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Area</label>
            <input type="number" step="0.0001" name="area_val" class="form-control" value="<?= html_escape($row['area_val'] ?? ''); ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label>Area Unit</label>
            <input type="text" name="area_unit" class="form-control" value="<?= html_escape($row['area_unit'] ?? 'sqft'); ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>Notes</label>
            <input type="text" name="notes" class="form-control" value="<?= html_escape($row['notes'] ?? ''); ?>">
        </div>
    </div>
</div> -->

<?php if ($editing): ?>
<input type="hidden" name="id" value="<?= html_escape($row['id']); ?>">
<?php endif; ?>

<hr class="hr-panel-heading" />

<!-- Category-specific fields -->

<?php if ($category === 'siding') : ?>
	<?php
	$sidingLeft = [
		['Siding Total Area','sqft','siding_total_area'],
		['Siding Area + 5% Waste','sqft','siding_area_5_waste'],
		['Siding Area + 10% Waste','sqft','siding_area_10_waste'],
		['Siding Area + 15% Waste','sqft','siding_area_15_waste'],
		['Siding Area + 18% Waste','sqft','siding_area_18_waste'],
		['Siding Inside Corner Quantity','ea','siding_inside_corner_qty'],
		['Siding Inside Corner Length','lf','siding_inside_corner_len'],
		['Siding Outside Corner Quantity','ea','siding_outside_corner_qty'],
		['Siding Outside Corner Length','lf','siding_outside_corner_len'],
		['Siding Soffit Level Frieze','sqft','siding_soffit_level_frieze'],
		['Siding Soffit Eaves','sqft','siding_soffit_eaves'],
		['Siding Soffit Rakes','sqft','siding_soffit_rakes'],
		['Siding Soffit Sloped Frieze','sqft','siding_soffit_sloped_frieze'],
		['Siding Soffit Total','sqft','siding_soffit_total'],
	];
	$sidingRight = [
		['Siding Opening Quantity','ea','siding_opening_qty'],
		['Siding Opening Tops Length','lf','siding_opening_tops_len'],
		['Siding Opening Sills Length','lf','siding_opening_sills_len'],
		['Siding Opening Sides Length','lf','siding_opening_sides_len'],
		['Siding Level Starter','lf','siding_level_starter'],
		['Siding Sloped Trim','lf','siding_sloped_trim'],
		['Siding Vertical Trim','lf','siding_vertical_trim'],
		['Siding Level Frieze Board','lf','siding_level_frieze_board'],
		['Siding Sloped Frieze Board','lf','siding_sloped_frieze_board'],
		['Siding Frieze Board','lf','siding_frieze_board'],
		['Siding Windows Quantity','ea','siding_windows_qty'],
		['Siding Doors Quantity','ea','siding_doors_qty'],
		['Siding Garage Doors Quantity','ea','siding_garage_doors_qty'],
		['Siding Shutter Quantity','ea','siding_shutter_qty'],
		['Siding Shutter Area','sqft','siding_shutter_area'],
		['Siding Vents Quantity','ea','siding_vents_qty'],
		['Siding Vents Area','sqft','siding_vents_area'],
	];
	?>
	<?php 
	$allSidingFields = array_merge($sidingLeft, $sidingRight);
	$sidingRows = array_chunk($allSidingFields, 4);
	?>
	<?php foreach ($sidingRows as $row): ?>
	<div class="row">
		<?php foreach ($row as $f) : list($label,$unit,$name) = $f; ?>
		<div class="col-md-3">
			<div class="form-group">
				<label><?= $label; ?></label>
				<div class="input-group">
					<input type="number" step="0.0001" class="form-control" name="siding[<?= $name; ?>]" value="<?= html_escape($existing_attributes['siding'][$name] ?? ''); ?>">
					<span class="input-group-addon"><?= $unit; ?></span>
				</div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
<?php elseif ($category === 'roofing') : ?>
	<?php
	$roofingTotalsLeft = [
		['Roof Total Area','sqft','roof_total_area'],
		['Roof Area + 5% Waste','sqft','roof_area_5_waste'],
		['Roof Area + 10% Waste','sqft','roof_area_10_waste'],
		['Roof Area + 12% Waste','sqft','roof_area_12_waste'],
		['Roof Area + 15% Waste','sqft','roof_area_15_waste'],
		['Roof Area + 20% Waste','sqft','roof_area_20_waste'],
		['Roof Flat Area','sqft','roof_flat_area'],
		['Roof High Roof Area','sqft','roof_high_roof_area'],
		['Roof Flat Shallow Area','sqft','roof_flat_shallow_area'],
		['Roof Low Slope Area','sqft','roof_low_slope_area'],
		['Roof Average Slope Area','sqft','roof_avg_slope_area'],
		['Roof Steep Slope Area','sqft','roof_steep_slope_area'],
		['Roof Ultra Steep Slope Area','sqft','roof_ultra_steep_slope_area'],
		['Roof Sloped Area','sqft','roof_sloped_area'],
		['Roof Sloped Area + 10% Waste','sqft','roof_sloped_area_10_waste'],
		['Roof 7-9/12 Pitch Area','sqft','roof_pitch_7_9_12_area'],
		['Roof 10-12/12 Pitch Area','sqft','roof_pitch_10_12_12_area'],
		['Roof 13/12+ Pitch Area','sqft','roof_pitch_13_plus_area'],
	];
	$roofingTotalsRight = [
		['Roof Ridge','lf','roof_ridge'],
		['Roof Hip','lf','roof_hip'],
		['Roof Ridge Hip','lf','roof_ridge_hip'],
		['Roof Valley','lf','roof_valley'],
		['Roof Downspout Elbows','ea','roof_downspout_elbows'],
		['Roof Downspouts','lf','roof_downspouts'],
		['Roof Gutter Miters','ea','roof_gutter_miters'],
		['Roof Eave','lf','roof_eave'],
		['Roof Rake','lf','roof_rake'],
		['Roof Perimeter','lf','roof_perimeter'],
		['Roof Step Flashing','lf','roof_step_flashing'],
		['Roof Headwall Flashing','lf','roof_headwall_flashing'],
		['Roof Total Flashing','lf','roof_total_flashing'],
		['Roof Valley Eave','lf','roof_valley_eave'],
		['Roof Valley Eave Rake','lf','roof_valley_eave_rake'],
	];
	$pitchAreasLeft = [];
	for ($i = 0; $i <= 12; $i++) {
		$pitchAreasLeft[] = [$i . '/12 Pitch Area', 'sqft', 'roof_pitch_' . $i . '_12_area'];
	}
	$pitchAreasRight = [];
	for ($i = 13; $i <= 25; $i++) {
		$pitchAreasRight[] = [$i . '/12 Pitch Area', 'sqft', 'roof_pitch_' . $i . '_12_area'];
	}
	?>
	<div class="panel_s">
		<div class="panel-heading">Roofing Total</div>
		<div class="panel-body">
			<?php 
			$allRoofingTotals = array_merge($roofingTotalsLeft, $roofingTotalsRight);
			$roofingTotalRows = array_chunk($allRoofingTotals, 4);
			?>
			<?php foreach ($roofingTotalRows as $row): ?>
			<div class="row">
				<?php foreach ($row as $f) : list($label,$unit,$name) = $f; ?>
				<div class="col-md-3">
					<div class="form-group">
						<label><?= $label; ?></label>
						<div class="input-group">
							<input type="number" step="0.0001" class="form-control" name="roofing[<?= $name; ?>]" value="<?= html_escape($existing_attributes['roofing'][$name] ?? ''); ?>">
							<span class="input-group-addon"><?= $unit; ?></span>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="panel_s">
		<div class="panel-heading">Roof Areas by Pitch</div>
		<div class="panel-body">
			<?php 
			$allPitchAreas = array_merge($pitchAreasLeft, $pitchAreasRight);
			$pitchAreaRows = array_chunk($allPitchAreas, 4);
			?>
			<?php foreach ($pitchAreaRows as $row): ?>
			<div class="row">
				<?php foreach ($row as $f) : list($label,$unit,$name) = $f; ?>
				<div class="col-md-3">
					<div class="form-group">
						<label><?= $label; ?></label>
						<div class="input-group">
							<input type="number" step="0.0001" class="form-control" name="roofing[<?= $name; ?>]" value="<?= html_escape($existing_attributes['roofing'][$name] ?? ''); ?>">
							<span class="input-group-addon"><?= $unit; ?></span>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php elseif ($category === 'windows') : ?>
	<div class="row">
		<div class="col-md-12">
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-info" data-toggle="modal" id="js-add-window">
					<i class="fa fa-plus"></i> Add Window
				</button>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="table-responsive">
		<table class="table table-striped dataTable no-footer">
			<thead>
				<tr>
					<th>Designator</th>
					<th>Name</th>
					<th>Location</th>
					<th>Level</th>
					<th>Width</th>
					<th>Height</th>
					<th>UI</th>
					<th>Area</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Designator 1</td>
					<td>Name 1</td>
					<td>Location 1</td>
					<td>Level 1</td>
					<td>10</td>
					<td>10</td>
					<td>10</td>
					<td>100</td>
					<td>
						<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#windowModal">
							<i class="fa fa-edit"></i>
						</button>
					</td>
				</tr>
				<tr>
					<td>Designator 2</td>
					<td>Name 2</td>
					<td>Location 2</td>
					<td>Level 2</td>
					<td>10</td>
					<td>10</td>
					<td>100</td>
					<td>100</td>
					<td>
						<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#windowModal">
							<i class="fa fa-edit"></i>
						</button>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
	</div>
<?php elseif ($category === 'doors') : ?>
	<div class="row">
		<div class="col-md-12">
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-info" data-toggle="modal" data-target="#doorModal">
					<i class="fa fa-plus"></i> Add Door
				</button>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="table-responsive">
			<table class="table table-striped dataTable no-footer">
				<thead>
					<tr>
						<th>Type</th>
						<th>Name</th>
						<th>Location</th>
						<th>Level</th>
						<th>Width</th>
						<th>Height</th>
						<th>UI</th>
						<th>Area</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Type 1</td>
						<td>Name 1</td>
						<td>Location 1</td>
						<td>Level 1</td>
						<td>10</td>
						<td>10</td>
						<td>100</td>
						<td>100</td>
						<td>
							<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#doorModal">
								<i class="fa fa-edit"></i>
							</button>
						</td>
					</tr>
					<tr>
						<td>Type 2</td>
						<td>Name 2</td>
						<td>Location 2</td>
						<td>Level 2</td>
						<td>10</td>
						<td>10</td>
						<td>100</td>
						<td>100</td>
						<td>
							<button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#doorModal">
								<i class="fa fa-edit"></i>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php else : ?>
	<div class="alert alert-info">No fields configured for this category yet.</div>
<?php endif; ?>




