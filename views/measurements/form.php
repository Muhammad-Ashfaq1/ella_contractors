<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<a href="<?php echo admin_url('ella_contractors/measurements'); ?>" class="btn btn-default">Back</a>
						</div>
						<h4 class="no-margin mtop20"><?php echo html_escape($title); ?></h4>
						<hr class="hr-panel-heading" />

						<!-- Tab Navigation -->
						<ul class="nav nav-tabs mb-3" id="category-tabs">
							<li class="<?php echo($category ?? 'siding') == 'siding' ? 'active' : ''; ?>">
								<a href="#siding-tab" data-toggle="tab" data-category="siding">Siding</a>
							</li>
							<li class="<?php echo($category ?? 'siding') == 'roofing' ? 'active' : ''; ?>">
								<a href="#roofing-tab" data-toggle="tab" data-category="roofing">Roofing</a>
							</li>
							<li class="<?php echo($category ?? 'siding') == 'windows' ? 'active' : ''; ?>">
								<a href="#windows-tab" data-toggle="tab" data-category="windows">Windows</a>
							</li>
							<li class="<?php echo($category ?? 'siding') == 'doors' ? 'active' : ''; ?>">
								<a href="#doors-tab" data-toggle="tab" data-category="doors">Doors</a>
							</li>
						</ul>

						<form id="measurements-form" method="post" action="<?php echo admin_url('ella_contractors/measurements/save'); ?>">
							<input type="hidden" name="category" id="selected-category" value="<?php echo html_escape($category ?? 'siding'); ?>">

							<div class="tab-content">
								<!-- Siding Tab -->
								<div class="tab-pane								                     <?php echo($category ?? 'siding') == 'siding' ? 'active' : ''; ?>" id="siding-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'siding', 'row' => ($row ?? null)]); ?>
								</div>

								<!-- Roofing Tab -->
								<div class="tab-pane								                     <?php echo($category ?? 'siding') == 'roofing' ? 'active' : ''; ?>" id="roofing-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'roofing', 'row' => ($row ?? null)]); ?>
								</div>

								<!-- Windows Tab -->
								<div class="tab-pane								                     <?php echo($category ?? 'siding') == 'windows' ? 'active' : ''; ?>" id="windows-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'windows', 'row' => ($row ?? null)]); ?>
								</div>

								<!-- Doors Tab -->
								<div class="tab-pane								                     <?php echo($category ?? 'siding') == 'doors' ? 'active' : ''; ?>" id="doors-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'doors', 'row' => ($row ?? null)]); ?>
								</div>
							</div>

							<div class="text-right mtop15">
								<a href="<?php echo admin_url('ella_contractors/measurements'); ?>" class="btn btn-default">Cancel</a>
								<button type="submit" class="btn btn-primary">Save Changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>



<script>
	$(document).ready(function() {
		// Handle tab clicks
		$('#category-tabs a[data-toggle="tab"]').on('click', function(e) {
			e.preventDefault();
			var category = $(this).data('category');
			$('#selected-category').val(category);

			// Show the corresponding tab content
			$('.tab-pane').removeClass('active');
			$('#' + category + '-tab').addClass('active');

			// Update active tab
			$('#category-tabs li').removeClass('active');
			$(this).parent().addClass('active');
		});

		// Auto-calculate UI and Area when width/height change
		function calculateMeasurements() {
			var width = parseFloat($('input[name="width_val"]').val()) || 0;
			var height = parseFloat($('input[name="height_val"]').val()) || 0;
			var lengthUnit = $('input[name="length_unit"]').val() || 'in';
			var areaUnit = $('input[name="area_unit"]').val() || 'sqft';

			if (width > 0 && height > 0) {
				// Calculate United Inches (width + height)
				$('input[name="united_inches_val"]').val((width + height).toFixed(2));

				// Calculate Area (convert to sqft if inches)
				if (lengthUnit === 'in' && areaUnit === 'sqft') {
					var area = (width * height) / 144.0;
					$('input[name="area_val"]').val(area.toFixed(4));
				}
			}
		}

		// Bind calculation to width/height inputs
		$('input[name="width_val"], input[name="height_val"], input[name="length_unit"], input[name="area_unit"]').on('input change', calculateMeasurements);

		// Initial calculation
		calculateMeasurements();
	});
</script>

<script>
	$(document).ready(function() {
		$('#js-add-window').on('click', function() {
			console.log('js-add-window clicked');
			$('#windowModal').modal('show');
		});
	});
</script>
