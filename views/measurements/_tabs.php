<?php
$tabs = [
	'windows' => 'Windows',
	'doors'   => 'Doors',
	'roofing' => 'Roofing',
	'siding'  => 'Siding',
	'other'   => 'Other',
];
?>
<ul class="nav nav-tabs">
	<?php foreach ($tabs as $slug => $label) : ?>
	<li class="<?= $category == $slug ? 'active' : ''; ?>">
		<a href="<?= admin_url('ella_contractors/measurements/' . $slug); ?>" aria-controls="<?= $slug; ?>" role="tab"><?= $label; ?></a>
	</li>
	<?php endforeach; ?>
</ul>


