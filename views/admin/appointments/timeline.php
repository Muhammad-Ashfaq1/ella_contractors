<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Timeline Activities Container -->
<div class="panel_s no-shadow">
    <div class="panel-body">
        <div id="timeline-activities-container">
            <?php if (!empty($timeline_activities)): ?>
                <?php foreach ($timeline_activities as $activity): ?>
                    <div class="timeline-record-wrapper">
                        <div class="timeline-date-section">
                            <div class="date">
                                <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo $activity['date']; ?>" data-original-title="" title="">
                                    <?php echo time_ago($activity['date']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="timeline-content-section">
                            <div class="text">
                            <?php if ($activity['staff_id'] > 0): ?>
                                <a href="<?php echo admin_url('admin/profile/' . $activity['staff_id']); ?>">
                                    <?php echo staff_profile_image($activity['staff_id'], ['staff-profile-xs-image', 'pull-left', 'mright5']); ?>
                                </a>
                            <?php else: ?>
                                <img class="staff-profile-xs-image pull-left mright5" src="<?php echo admin_url('assets/images/user-placeholder.jpg'); ?>" alt="<?php echo $activity['full_name']; ?>">
                            <?php endif; ?>
                            
                            <span class="timeline-activity-type <?php echo $activity['description_key']; ?>">
                                <?php 
                                // Convert description_key to proper display format
                                $activity_parts = explode('_', $activity['description_key']);
                                if (count($activity_parts) >= 2) {
                                    $entity = ucfirst($activity_parts[0]);
                                    $action = ucfirst($activity_parts[1]);
                                    echo "{$entity} {$action}";
                                } else {
                                    echo ucfirst(str_replace('_', ' ', $activity['description_key']));
                                }
                                ?>
                            </span>
                            
                            <b><?php echo $activity['full_name']; ?></b> - 
                            
                            <?php 
                            // Handle additional data display
                            if (!empty($activity['additional_data'])) {
                                $additional_data = @unserialize($activity['additional_data']);
                                if ($additional_data !== false && is_array($additional_data)) {
                                    echo $activity['description'];
                                    
                                    // Show detailed changes if available
                                    if (isset($additional_data['changes']) && !empty($additional_data['changes'])) {
                                        echo '<div class="timeline-activity-details">';
                                        echo '<h6>Changes:</h6>';
                                        echo '<ul>';
                                        foreach ($additional_data['changes'] as $field => $change) {
                                            echo '<li><strong>' . ucfirst(str_replace('_', ' ', $field)) . ':</strong> ';
                                            if (isset($change['old']) && isset($change['new'])) {
                                                echo '<span class="change-item change-old">' . htmlspecialchars($change['old']) . '</span> → ';
                                                echo '<span class="change-item change-new">' . htmlspecialchars($change['new']) . '</span>';
                                            } else {
                                                echo htmlspecialchars($change);
                                            }
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo $activity['description'];
                                }
                            } else {
                                echo $activity['description'];
                            }
                            ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="timeline-empty">
                    <i class="fa fa-history"></i>
                    <p><?php echo _l('timeline_no_activities'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
});
</script>