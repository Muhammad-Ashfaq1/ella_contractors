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
						<form id="measurements-form" method="post" action="javascript:void(0);" onsubmit="return false;">
							<input type="hidden" name="category" id="selected-category" value="<?php echo html_escape($category ?? 'siding'); ?>">
							
							<!-- Hidden fields for relationship -->
							<input type="hidden" name="rel_type" value="lead">
							<input type="hidden" name="rel_id" id="rel_id" value="<?php echo html_escape($row['rel_id'] ?? ''); ?>">
							
							<!-- Leads/Jobs and Client Selection -->
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="lead_id">Lead/Job <span class="text-danger">*</span></label>
										<select name="lead_id" id="lead_id" class="form-control selectpicker" data-live-search="true" required>
											<option value="">Select Lead/Job</option>
											<?php if (isset($row['lead_id']) && $row['lead_id']): ?>
												<?php 
												$this->load->model('leads_model');
												$lead = $this->leads_model->get($row['lead_id']);
												if ($lead): ?>
													<option value="<?= $lead->id; ?>" selected><?= html_escape($lead->name); ?></option>
												<?php endif; ?>
											<?php endif; ?>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="client_name">Client (Optional)</label>
										<input type="text" name="client_name" id="client_name" class="form-control" 
											   value="<?= html_escape($row['client_name'] ?? ''); ?>" 
											   placeholder="Enter client name manually">
									</div>
								</div>
							</div>
							
							<hr class="hr-panel-heading" />

							<div class="tab-content">
								<!-- Siding Tab -->
								<div class="tab-pane <?php echo($category ?? 'siding') == 'siding' ? 'active' : ''; ?>" id="siding-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'siding', 'row' => ($row ?? null)]); ?>
								</div>

								<!-- Roofing Tab -->
								<div class="tab-pane <?php echo($category ?? 'siding') == 'roofing' ? 'active' : ''; ?>" id="roofing-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'roofing', 'row' => ($row ?? null)]); ?>
								</div>

								<!-- Windows Tab -->
								<div class="tab-pane <?php echo($category ?? 'siding') == 'windows' ? 'active' : ''; ?>" id="windows-tab">
									<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => 'windows', 'row' => ($row ?? null)]); ?>
								</div>

								<!-- Doors Tab -->
								<div class="tab-pane <?php echo($category ?? 'siding') == 'doors' ? 'active' : ''; ?>" id="doors-tab">
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

<!-- Window Modal -->
<div class="modal fade" id="windowModal" tabindex="-1" role="dialog" aria-labelledby="windowModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="windowModalLabel">Add New Window</h4>
			</div>
			<form id="window-form" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>TYPE</label>
								<button type="button" class="btn btn-info btn-block">Window</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>NAME <span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>LOCATION</label>
								<select name="location" class="form-control">
									<option value="">Select Location</option>
									<?php for($i = 1; $i <= 10; $i++): ?>
									<option value="Bedroom <?= $i; ?>">Bedroom <?= $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>LEVEL</label>
								<select name="level" class="form-control">
									<option value="">Select Level</option>
									<?php for($i = 1; $i <= 10; $i++): ?>
									<option value="<?= $i; ?>"><?= $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>QUANTITY</label>
								<input type="number" name="quantity" class="form-control" value="1" min="1">
							</div>
						</div>
					</div>
					<hr>
					<h5>Measurements</h5>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>WIDTH</label>
								<input type="number" name="width" class="form-control" value="0" step="0.01">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>HEIGHT</label>
								<input type="number" name="height" class="form-control" value="0" step="0.01">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>UI: <span id="ui-display">0 in</span></label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Area: <span id="area-display">0 sqft</span></label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-info">
						<i class="fa fa-save"></i> Save All Measurements
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Door Modal -->
<div class="modal fade" id="doorModal" tabindex="-1" role="dialog" aria-labelledby="doorModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="doorModalLabel">Add New Door</h4>
			</div>
			<form id="door-form" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>TYPE</label>
								<button type="button" class="btn btn-info btn-block">Door</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>NAME <span class="text-danger">*</span></label>
								<input type="text" name="name" class="form-control" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>LOCATION</label>
								<select name="location" class="form-control">
									<option value="">Select Location</option>
									<?php for($i = 1; $i <= 10; $i++): ?>
									<option value="Bedroom <?= $i; ?>">Bedroom <?= $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>LEVEL</label>
								<select name="level" class="form-control">
									<option value="">Select Level</option>
									<?php for($i = 1; $i <= 10; $i++): ?>
									<option value="<?= $i; ?>"><?= $i; ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>QUANTITY</label>
								<input type="number" name="quantity" class="form-control" value="1" min="1">
							</div>
						</div>
					</div>
					<hr>
					<h5>Measurements</h5>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>WIDTH</label>
								<input type="number" name="width" class="form-control" value="0" step="0.01">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>HEIGHT</label>
								<input type="number" name="height" class="form-control" value="0" step="0.01">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>UI: <span id="door-ui-display">0 in</span></label>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Area: <span id="door-area-display">0 sqft</span></label>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-info">
						<i class="fa fa-save"></i> Save All Measurements
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php init_tail(); ?>



<script>
	$(document).ready(function() {
		// Initialize AJAX search for leads/jobs
		init_ajax_search('lead', '#lead_id');
		
		// Set rel_type to 'lead' for measurements
		$('input[name="rel_type"]').val('lead');
		
		// Handle lead selection change
		$('#lead_id').on('change', function() {
			var leadId = $(this).val();
			$('#rel_id').val(leadId);
			
			// Auto-fill client name if lead is selected and client field is empty
			if (leadId && !$('#client_name').val()) {
				var selectedText = $(this).find('option:selected').text();
				if (selectedText && selectedText !== 'Select Lead/Job') {
					// Extract company name if available (assuming format: "Lead Name - Company")
					var parts = selectedText.split(' - ');
					if (parts.length > 1) {
						$('#client_name').val(parts[1]);
					} else {
						$('#client_name').val(parts[0]);
					}
				}
			}
		});
		
		// Set initial rel_id if editing
		<?php if (isset($row['lead_id']) && $row['lead_id']): ?>
			$('#rel_id').val('<?= $row['lead_id']; ?>');
		<?php endif; ?>
		
		// Prevent any button clicks from submitting the form
		$(document).on('click', '#measurements-form button[type="submit"]', function(e) {
			e.preventDefault();
			e.stopPropagation();
			console.log('Submit button clicked');
			$('#measurements-form').trigger('submit');
			return false;
		});
		
		// Also prevent form submission on enter key
		$('#measurements-form').on('keypress', function(e) {
			if (e.which === 13) { // Enter key
				e.preventDefault();
				console.log('Enter key pressed, preventing form submission');
				$('#measurements-form').trigger('submit');
				return false;
			}
		});
		
		// Function to collect data from all tabs
		function collectAllTabsData() {
			var allData = {};
			var categories = ['roofing', 'siding', 'windows', 'doors'];
			
			categories.forEach(function(category) {
				var categoryData = {};
				$('input[name^="' + category + '["]').each(function() {
					var name = $(this).attr('name');
					var value = $(this).val();
					if (value && value.trim() !== '') {
						categoryData[name] = value;
					}
				});
				
				// Only add category data if it has values
				if (Object.keys(categoryData).length > 0) {
					allData[category] = categoryData;
				}
			});
			
			console.log('Collected data from all tabs:', allData);
			return allData;
		}
		
		// Function to show summary of what will be saved
		function showSaveSummary(allTabsData) {
			var categories = Object.keys(allTabsData);
			if (categories.length === 0) {
				return false;
			}
			
			var summary = 'The following measurement data will be saved:\n\n';
			categories.forEach(function(category) {
				var fieldCount = Object.keys(allTabsData[category]).length;
				summary += '• ' + category.charAt(0).toUpperCase() + category.slice(1) + ': ' + fieldCount + ' fields\n';
			});
			
			summary += '\nTotal categories: ' + categories.length;
			console.log('Save summary:', summary);
			
			return true; // For now, just return true. Could show modal with summary if needed
		}
		
		// Form validation and AJAX submission
		$(document).on('submit', '#measurements-form', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			console.log('Form submit event triggered');
			
			var leadId = $('#lead_id').val();
			if (!leadId) {
				alert('Please select a Lead/Job before saving the measurement.');
				$('#lead_id').focus();
				return false;
			}
			
			// Collect form data from all tabs
			var formData = $(this).serializeArray();
			var data = {};
			
			// Convert form data to object
			$.each(formData, function(i, field) {
				data[field.name] = field.value;
			});
			
			// Collect data from all tabs (roofing, siding, windows, doors)
			var allTabsData = collectAllTabsData();
			
			// Show summary of what will be saved
			var summary = showSaveSummary(allTabsData);
			if (!summary) {
				alert('Please enter at least one measurement in any category before saving.');
				return false;
			}
			
			// Merge all data
			$.extend(data, allTabsData);
			
			// Set category to 'combined' since we're saving all tabs
			data.category = 'combined';
			
			// Validation is already handled in showSaveSummary function
			
			// Show loading indicator
			var submitBtn = $(this).find('button[type="submit"]');
			var originalText = submitBtn.text();
			submitBtn.prop('disabled', true).text('Saving...');
			
			// Save via AJAX
			saveMeasurementAjax(data, function(success, response) {
				// Reset button
				submitBtn.prop('disabled', false).text(originalText);
				
				if (success) {
					alert_float('success', 'Measurement saved successfully!');
					// Redirect to measurements list
					setTimeout(function() {
						window.location.href = '<?php echo admin_url("ella_contractors/measurements"); ?>';
					}, 1500);
				} else {
					alert_float('danger', 'Error saving measurement: ' + (response.message || 'Unknown error'));
				}
			});
			
			return false; // Additional prevention
		});
		
		// AJAX save functionality for measurements
		function saveMeasurementAjax(formData, callback) {
			// Get CSRF token
			var csrfData = <?php echo json_encode(get_csrf_for_ajax()); ?>;
			
			// Add CSRF token to form data
			formData[csrfData.token_name] = csrfData.hash;
			
			// Debug logging
			console.log('Sending AJAX request with data:', formData);
			
			$.ajax({
				url: '<?php echo admin_url("ella_contractors/measurements/save"); ?>',
				type: 'POST',
				data: formData,
				dataType: 'json',
				success: function(response) {
					console.log('AJAX Response:', response);
					if (response.success) {
						if (typeof callback === 'function') {
							callback(true, response);
						}
					} else {
						if (typeof callback === 'function') {
							callback(false, response);
						}
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', error);
					console.error('Response Text:', xhr.responseText);
					if (typeof callback === 'function') {
						callback(false, {error: error, responseText: xhr.responseText});
					}
				}
			});
		}
		
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

		// Bind calculation to width/height inputs using jQuery
		$('input[name="width_val"], input[name="height_val"], input[name="length_unit"], input[name="area_unit"]').on('input change', calculateMeasurements);

		// Initial calculation
		calculateMeasurements();
	});
</script>

<script>
	$(document).ready(function() {
		// Handle window modal button click using jQuery
		$(document).on('click', '#js-add-window', function(e) {
			e.preventDefault();
			console.log('jQuery: Window modal button clicked');
			$('#windowModal').modal({
				backdrop: 'static',
				keyboard: false
			}).modal('show');
		});

		// Handle door modal button click using jQuery
		$(document).on('click', '[data-target="#doorModal"]', function(e) {
			e.preventDefault();
			console.log('jQuery: Door modal button clicked');
			$('#doorModal').modal({
				backdrop: 'static',
				keyboard: false
			}).modal('show');
		});

		// Handle window modal form submission using jQuery
		$(document).on('submit', '#window-form', function(e) {
			e.preventDefault();
			console.log('jQuery: Window form submitted');
			
			// Get form data using jQuery
			var formData = $(this).serialize();
			console.log('Window form data:', formData);
			
			// Add your AJAX submission logic here
			$.ajax({
				url: '<?php echo admin_url("ella_contractors/measurements/save"); ?>',
				type: 'POST',
				data: formData,
				success: function(response) {
					console.log('jQuery: Window data saved successfully');
					$('#windowModal').modal('hide');
					// Reset form
					$('#window-form')[0].reset();
					$('#ui-display').text('0 in');
					$('#area-display').text('0 sqft');
				},
				error: function(xhr, status, error) {
					console.error('jQuery: Error saving window data:', error);
				}
			});
		});

		// Handle door modal form submission using jQuery
		$(document).on('submit', '#door-form', function(e) {
			e.preventDefault();
			console.log('jQuery: Door form submitted');
			
			// Get form data using jQuery
			var formData = $(this).serialize();
			console.log('Door form data:', formData);
			
			// Add your AJAX submission logic here
			$.ajax({
				url: '<?php echo admin_url("ella_contractors/measurements/save"); ?>',
				type: 'POST',
				data: formData,
				success: function(response) {
					console.log('jQuery: Door data saved successfully');
					$('#doorModal').modal('hide');
					// Reset form
					$('#door-form')[0].reset();
					$('#door-ui-display').text('0 in');
					$('#door-area-display').text('0 sqft');
				},
				error: function(xhr, status, error) {
					console.error('jQuery: Error saving door data:', error);
				}
			});
		});

		// Auto-calculate UI and Area for window modal using jQuery
		$(document).on('input change', '#windowModal input[name="width"], #windowModal input[name="height"]', function() {
			var $modal = $('#windowModal');
			var width = parseFloat($modal.find('input[name="width"]').val()) || 0;
			var height = parseFloat($modal.find('input[name="height"]').val()) || 0;
			
			if (width > 0 && height > 0) {
				var ui = width + height;
				var area = (width * height) / 144.0; // Convert to sqft
				$modal.find('#ui-display').text(ui.toFixed(2) + ' in');
				$modal.find('#area-display').text(area.toFixed(4) + ' sqft');
			} else {
				$modal.find('#ui-display').text('0 in');
				$modal.find('#area-display').text('0 sqft');
			}
		});

		// Auto-calculate UI and Area for door modal using jQuery
		$(document).on('input change', '#doorModal input[name="width"], #doorModal input[name="height"]', function() {
			var $modal = $('#doorModal');
			var width = parseFloat($modal.find('input[name="width"]').val()) || 0;
			var height = parseFloat($modal.find('input[name="height"]').val()) || 0;
			
			if (width > 0 && height > 0) {
				var ui = width + height;
				var area = (width * height) / 144.0; // Convert to sqft
				$modal.find('#door-ui-display').text(ui.toFixed(2) + ' in');
				$modal.find('#door-area-display').text(area.toFixed(4) + ' sqft');
			} else {
				$modal.find('#door-ui-display').text('0 in');
				$modal.find('#door-area-display').text('0 sqft');
			}
		});

		// Reset modals when they are hidden using jQuery
		$('#windowModal').on('hidden.bs.modal', function() {
			$(this).find('form')[0].reset();
			$(this).find('#ui-display').text('0 in');
			$(this).find('#area-display').text('0 sqft');
		});

		$('#doorModal').on('hidden.bs.modal', function() {
			$(this).find('form')[0].reset();
			$(this).find('#door-ui-display').text('0 in');
			$(this).find('#door-area-display').text('0 sqft');
		});
	});
</script>
