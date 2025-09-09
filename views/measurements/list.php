<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<!-- <?php //$this->load->view('ella_contractors/measurements/_tabs'); ?> -->
				<div class="panel_s">
					<div class="panel-body">
						<div class="row mbot15">
							<div class="col-md-6"><h4><?= html_escape($title); ?></h4></div>
							<div class="col-md-6 text-right">
								<a href="<?= admin_url('ella_contractors/measurements/create/'); ?>" class="btn btn-info"><i class="fa fa-plus"></i> Add</a>
							</div>
						</div>

						<div class="table-responsive">
							<table class="table table-striped dataTable no-footer">
								<thead>
									<tr>
										<th>Lead/Job</th>
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
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function editRow(id){
	window.location = '<?= admin_url('ella_contractors/measurements/edit/'); ?>' + id;
}
</script>
<?php init_tail(); ?>


