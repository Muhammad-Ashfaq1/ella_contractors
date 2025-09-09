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
									<?php
									$rows = $this->measurements_model->list($category);
									foreach ($rows['data'] as $r) : ?>
									<tr>
										<td>
											<?php if ($r['lead_name']): ?>
												<strong><?= html_escape($r['lead_name']); ?></strong>
												<?php if ($r['lead_company']): ?>
													<br><small class="text-muted"><?= html_escape($r['lead_company']); ?></small>
												<?php endif; ?>
											<?php else: ?>
												<span class="text-muted">No Lead</span>
											<?php endif; ?>
										</td>
										<td><?= html_escape($r['designator']); ?></td>
										<td><?= html_escape($r['name']); ?></td>
										<td><?= html_escape($r['location_label']); ?></td>
										<td><?= html_escape($r['level_label']); ?></td>
										<td><?= html_escape($r['width_val']); ?> <?= html_escape($r['length_unit']); ?></td>
										<td><?= html_escape($r['height_val']); ?> <?= html_escape($r['length_unit']); ?></td>
										<td><?= html_escape($r['united_inches_val']); ?> <?= html_escape($r['ui_unit']); ?></td>
										<td><?= html_escape($r['area_val']); ?> <?= html_escape($r['area_unit']); ?></td>
										<td>
											<a href="<?= admin_url('ella_contractors/measurements/edit/' . $r['id']); ?>" class="btn btn-default btn-xs">
												<i class="fa fa-edit"></i> Edit
											</a>
											<a href="<?= admin_url('ella_contractors/measurements/delete/' . $r['id']); ?>" 
											   onclick="return confirm('Delete this measurement?');" 
											   class="btn btn-danger btn-xs">
												<i class="fa fa-trash"></i> Delete
											</a>
										</td>
									</tr>
									<?php endforeach; ?>
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


