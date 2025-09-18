<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('ella_contractors/appointments'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('back_to_appointments'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-ella_appointments_past">
                                <thead>
                                    <tr>
                                        <th width="50px"></th>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('appointment_subject'); ?></th>
                                        <th><?php echo _l('appointment_meeting_date'); ?></th>
                                        <th><?php echo _l('client'); ?></th>
                                        <th><?php echo _l('appointment_status'); ?></th>
                                        <th width="100px"><i class="fa fa-square-o"></i> Measurements</th>
                                        <th width="100px"><i class="fa fa-file-text-o"></i> Estimates</th>
                                        <th width="80px"><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($appointments)): ?>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <?php
                                            // Get measurement count for this appointment
                                            $this->db->where('appointment_id', $appointment['id']);
                                            $measurement_count = $this->db->count_all_results(db_prefix() . 'ella_contractors_measurements');
                                            
                                            // Get estimate count for this appointment
                                            $this->db->where('appointment_id', $appointment['id']);
                                            $estimate_count = $this->db->count_all_results(db_prefix() . 'ella_contractor_estimates');
                                            
                                            // Determine status
                                            $status = 'Pending';
                                            $status_class = 'label-warning';
                                            if ($appointment['cancelled']) {
                                                $status = 'Cancelled';
                                                $status_class = 'label-danger';
                                            } elseif ($appointment['finished']) {
                                                $status = 'Finished';
                                                $status_class = 'label-success';
                                            } elseif ($appointment['approved']) {
                                                $status = 'Approved';
                                                $status_class = 'label-info';
                                            }
                                            
                                            // Get client name
                                            $client_name = '';
                                            if (!empty($appointment['client_name'])) {
                                                $client_name = $appointment['client_name'];
                                            } elseif (!empty($appointment['lead_name'])) {
                                                $client_name = $appointment['lead_name'];
                                            } else {
                                                $client_name = $appointment['name'];
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" value="<?php echo $appointment['id']; ?>">
                                                        <label></label>
                                                    </div>
                                                </td>
                                                <td><?php echo $appointment['id']; ?></td>
                                                <td>
                                                    <a href="<?php echo admin_url('ella_contractors/appointments/view/' . $appointment['id']); ?>">
                                                        <?php echo $appointment['subject']; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo _dt($appointment['date'] . ' ' . $appointment['start_hour']); ?></td>
                                                <td><?php echo $client_name; ?></td>
                                                <td>
                                                    <span class="label <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <?php
                                                        $measurement_url = admin_url('ella_contractors/appointments/view/' . $appointment['id'] . '?tab=measurements');
                                                        if ($measurement_count > 0): ?>
                                                            <a href="<?php echo $measurement_url; ?>" class="label label-info" title="Click to view measurements">
                                                                <i class="fa fa-square-o"></i> <?php echo $measurement_count; ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo $measurement_url; ?>" class="text-muted" title="Click to add measurements">
                                                                <i class="fa fa-square-o"></i> 0
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <?php
                                                        $estimate_url = admin_url('ella_contractors/appointments/view/' . $appointment['id'] . '?tab=estimates');
                                                        if ($estimate_count > 0): ?>
                                                            <a href="<?php echo $estimate_url; ?>" class="label label-success" title="Click to view estimates">
                                                                <i class="fa fa-file-text-o"></i> <?php echo $estimate_count; ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo $estimate_url; ?>" class="text-muted" title="Click to add estimates">
                                                                <i class="fa fa-file-text-o"></i> 0
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="<?php echo admin_url('ella_contractors/appointments/view/' . $appointment['id']); ?>" 
                                                       class="btn btn-default btn-xs" title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="fa fa-info-circle fa-2x"></i>
                                                <p>No past appointments found.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<style>
/* Custom styling for appointment count badges */
.table-ella_appointments_past .label {
    font-size: 11px;
    padding: 4px 8px;
    margin: 2px;
    display: inline-block;
}
.table-ella_appointments_past .text-muted {
    font-size: 11px;
    padding: 4px 8px;
    margin: 2px;
    display: inline-block;
    opacity: 0.6;
}
.table-ella_appointments_past th {
    text-align: center;
    vertical-align: middle;
}
.table-ella_appointments_past td {
    vertical-align: middle;
}
/* Clickable badge styling */
.table-ella_appointments_past .label a,
.table-ella_appointments_past .text-muted a {
    color: inherit;
    text-decoration: none;
    cursor: pointer;
}
.table-ella_appointments_past .label a:hover,
.table-ella_appointments_past .text-muted a:hover {
    color: inherit;
    text-decoration: none;
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
}
</style>

<script>
$(document).ready(function() {
    console.log('hello from js past');
});
</script>