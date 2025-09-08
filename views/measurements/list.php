<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<?php $this->load->view('ella_contractors/measurements/_tabs'); ?>
				<div class="panel_s">
					<div class="panel-body">
						<div class="row mbot15">
							<div class="col-md-6"><h4><?= html_escape($title); ?></h4></div>
							<div class="col-md-6 text-right">
								<a href="#" class="btn btn-info" onclick="$('#addModal').modal('show');return false;"><i class="fa fa-plus"></i> Add</a>
							</div>
						</div>

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
									<?php
									$rows = $this->db
										->where('category', $category)
										->order_by('sort_order ASC, id DESC')
										->get(db_prefix() . 'ella_contractors_measurements')
										->result_array();
									foreach ($rows as $r) : ?>
									<tr>
										<td><?= html_escape($r['designator']); ?></td>
										<td><?= html_escape($r['name']); ?></td>
										<td><?= html_escape($r['location_label']); ?></td>
										<td><?= html_escape($r['level_label']); ?></td>
										<td><?= html_escape($r['width_val']); ?> <?= html_escape($r['length_unit']); ?></td>
										<td><?= html_escape($r['height_val']); ?> <?= html_escape($r['length_unit']); ?></td>
										<td><?= html_escape($r['united_inches_val']); ?> <?= html_escape($r['ui_unit']); ?></td>
										<td><?= html_escape($r['area_val']); ?> <?= html_escape($r['area_unit']); ?></td>
										<td>
											<a href="#" onclick="editRow(<?= (int) $r['id']; ?>);return false;">Edit</a> |
											<a href="<?= admin_url('ella_contractors/measurements/delete/' . $r['id']); ?>" onclick="return confirm('Delete this row?');" class="text-danger">Delete</a>
											<div id="edit-form-<?= (int) $r['id']; ?>" class="hidden">
												<?php $row = $r; $this->load->view('ella_contractors/measurements/_form_modal', compact('row') + ['category' => $category]); ?>
											</div>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add New <?= ucfirst($category); ?></h4>
			</div>
			<div class="modal-body">
				<?php $this->load->view('ella_contractors/measurements/_form_modal', ['category' => $category]); ?>
			</div>
		</div>
	</div>
</div>

<script>
function editRow(id){
	var html = document.getElementById('edit-form-'+id).innerHTML;
	var modal = document.getElementById('addModal');
	$(modal).find('.modal-title').text('Edit');
	$(modal).find('.modal-body').html(html);
	$('#addModal').modal('show');
}
</script>
<?php init_tail(); ?>


