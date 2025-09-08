<?php
$activeTab = 'roofing';

$roofingLeft = [
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

$roofingRight = [
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

<ul class="nav nav-tabs">
	<li class="<?= $activeTab === 'roofing' ? 'active' : ''; ?>"><a href="#tab_roofing" data-toggle="tab">Roofing</a></li>
	<li><a href="#tab_siding" data-toggle="tab">Siding</a></li>
	<li><a href="#tab_windows" data-toggle="tab">Windows</a></li>
	<li><a href="#tab_doors" data-toggle="tab">Doors</a></li>
</ul>

<div class="tab-content" style="margin-top:15px;">
	<div role="tabpanel" class="tab-pane <?= $activeTab === 'roofing' ? 'active' : ''; ?>" id="tab_roofing">
		<div class="row">
			<div class="col-md-6">
				<?php foreach ($roofingLeft as $f) : list($label,$unit,$name) = $f; ?>
				<div class="form-group">
					<label><?= $label; ?></label>
					<div class="input-group">
						<input type="number" step="0.0001" class="form-control" name="roofing[<?= $name; ?>]" value="">
						<span class="input-group-addon"><?= $unit; ?></span>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="col-md-6">
				<?php foreach ($roofingRight as $f) : list($label,$unit,$name) = $f; ?>
				<div class="form-group">
					<label><?= $label; ?></label>
					<div class="input-group">
						<input type="number" step="0.0001" class="form-control" name="roofing[<?= $name; ?>]" value="">
						<span class="input-group-addon"><?= $unit; ?></span>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div role="tabpanel" class="tab-pane" id="tab_siding">
		<div class="alert alert-info">Siding fields will be configured next.</div>
	</div>
	<div role="tabpanel" class="tab-pane" id="tab_windows">
		<div class="alert alert-info">Windows fields will be configured next.</div>
	</div>
	<div role="tabpanel" class="tab-pane" id="tab_doors">
		<div class="alert alert-info">Doors fields will be configured next.</div>
	</div>
</div>


