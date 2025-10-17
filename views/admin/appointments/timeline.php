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
                                <?php
                                // Format: ICON Fname Linitial - ACTIONITEM
                                $staff_icon = '';
                                $formatted_name = '';
                                $action_item = '';
                                
                                // Get staff icon
                                if ($activity['staff_id'] > 0) {
                                    $staff_icon = staff_profile_image($activity['staff_id'], ['staff-profile-xs-image', 'pull-left', 'mright5']);
                                } else {
                                    $staff_icon = '<img class="staff-profile-xs-image pull-left mright5" src="' . admin_url('assets/images/user-placeholder.jpg') . '" alt="' . htmlspecialchars($activity['full_name']) . '">';
                                }
                                
                                // Format name: First Name + Last Initial
                                $name_parts = explode(' ', trim($activity['full_name']));
                                if (count($name_parts) >= 2) {
                                    $first_name = $name_parts[0];
                                    $last_initial = substr($name_parts[count($name_parts) - 1], 0, 1) . '.';
                                    $formatted_name = $first_name . ' ' . $last_initial;
                                } else {
                                    $formatted_name = $activity['full_name'];
                                }
                                
                                // Get action item based on description_key (with fallback for existing records)
                                $action_item = get_timeline_action_label($activity['description_key']);
                                
                                // Fallback for existing records that might not have proper description_key
                                if (empty($action_item) || $action_item === $activity['description_key']) {
                                    // Try to extract action from description if description_key is not available
                                    $description = $activity['description'] ?? '';
                                    if (strpos($description, 'created') !== false) {
                                        $action_item = _l('timeline_action_created');
                                    } elseif (strpos($description, 'updated') !== false) {
                                        $action_item = _l('timeline_action_updated');
                                    } elseif (strpos($description, 'added') !== false) {
                                        $action_item = _l('timeline_action_measurement_added');
                                    } elseif (strpos($description, 'removed') !== false || strpos($description, 'deleted') !== false) {
                                        $action_item = _l('timeline_action_measurement_removed');
                                    } else {
                                        // Last resort: use first word from description as action
                                        $words = explode(' ', trim($description));
                                        $action_item = !empty($words[0]) ? strtoupper($words[0]) : 'ACTION';
                                    }
                                }
                                
                                // Display in new format: ICON Fname Linitial - ACTIONITEM
                                echo $staff_icon;
                                echo '<span class="timeline-formatted-entry">';
                                echo '<strong>' . htmlspecialchars($formatted_name) . '</strong> - ';
                                echo '<span class="timeline-activity-type ' . $activity['description_key'] . '">' . $action_item . '</span>';
                                echo '</span>';
                                
                                // Show detailed changes if available (keep existing functionality)
                                if (!empty($activity['additional_data'])) {
                                    $additional_data = @unserialize($activity['additional_data']);
                                    if ($additional_data !== false && is_array($additional_data) && isset($additional_data['changes']) && !empty($additional_data['changes'])) {
                                        echo '<div class="timeline-activity-details">';
                                        echo '<h6>Changes:</h6>';
                                        echo '<ul>';
                                        foreach ($additional_data['changes'] as $field => $change) {
                                            echo '<li><strong>' . ucfirst(str_replace('_', ' ', $field)) . ':</strong> ';
                                            if (is_array($change)) {
                                                if (isset($change['old']) && isset($change['new'])) {
                                                    // Handle old/new format
                                                    $old_value = is_array($change['old']) ? json_encode($change['old']) : $change['old'];
                                                    $new_value = is_array($change['new']) ? json_encode($change['new']) : $change['new'];
                                                    echo '<span class="change-item change-old">' . htmlspecialchars($old_value) . '</span> → ';
                                                    echo '<span class="change-item change-new">' . htmlspecialchars($new_value) . '</span>';
                                                } else {
                                                    // Handle array without old/new structure
                                                    echo htmlspecialchars(json_encode($change));
                                                }
                                            } else {
                                                // Handle simple string value
                                                echo htmlspecialchars($change);
                                            }
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                        echo '</div>';
                                    }
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