<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Handle different types of activity details based on description key
switch ($activity['description_key']) {
    case 'appointment_activity_created':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-calendar-plus"></i> Appointment Created</h6>';
        if (isset($additional_data['subject'])) {
            echo '<p><strong>Subject:</strong> ' . e($additional_data['subject']) . '</p>';
        }
        if (isset($additional_data['date'])) {
            echo '<p><strong>Date:</strong> ' . e($additional_data['date']) . '</p>';
        }
        if (isset($additional_data['time'])) {
            echo '<p><strong>Time:</strong> ' . e($additional_data['time']) . '</p>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_updated':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-edit"></i> Appointment Updated</h6>';
        if (isset($additional_data['subject'])) {
            echo '<p><strong>Subject:</strong> ' . e($additional_data['subject']) . '</p>';
        }
        if (isset($additional_data['changes']) && is_array($additional_data['changes'])) {
            echo '<h6>Changes Made:</h6><ul>';
            foreach ($additional_data['changes'] as $field => $change) {
                $field_label = ucwords(str_replace('_', ' ', $field));
                echo '<li><strong>' . e($field_label) . ':</strong> ';
                echo '<span class="change-item change-old">' . e($change['old']) . '</span> → ';
                echo '<span class="change-item change-new">' . e($change['new']) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_status_changed':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-exchange"></i> Status Changed</h6>';
        if (isset($additional_data['old_status']) && isset($additional_data['new_status'])) {
            echo '<p><strong>Status:</strong> ';
            echo '<span class="change-item change-old">' . e($additional_data['old_status']) . '</span> → ';
            echo '<span class="change-item change-new">' . e($additional_data['new_status']) . '</span>';
            echo '</p>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_note_added':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-sticky-note"></i> Note Added</h6>';
        if (isset($additional_data['note_preview'])) {
            echo '<p>' . e($additional_data['note_preview']) . '</p>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_measurement_added':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-plus-square"></i> Measurement Added</h6>';
        if (isset($additional_data['measurement'])) {
            echo '<p>' . e($additional_data['measurement']) . '</p>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_measurement_removed':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-minus-square"></i> Measurement Removed</h6>';
        if (isset($additional_data['measurement'])) {
            echo '<p>' . e($additional_data['measurement']) . '</p>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_process':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-cogs"></i> Scheduled Process</h6>';
        if (isset($additional_data['process'])) {
            echo '<p><strong>Process:</strong> ' . e($additional_data['process']) . '</p>';
        }
        if (isset($additional_data['status'])) {
            $status_class = ($additional_data['status'] === 'completed') ? 'change-new' : 'change-old';
            echo '<p><strong>Status:</strong> <span class="change-item ' . $status_class . '">' . e($additional_data['status']) . '</span></p>';
        }
        echo '</div>';
        break;
        
    case 'appointment_activity_deleted':
        echo '<div class="timeline-activity-details">';
        echo '<h6><i class="fa fa-trash"></i> Appointment Deleted</h6>';
        if (isset($additional_data['subject'])) {
            echo '<p><strong>Subject:</strong> ' . e($additional_data['subject']) . '</p>';
        }
        echo '</div>';
        break;
        
    default:
        // Fallback for unknown activity types
        if (!empty($additional_data)) {
            echo '<div class="timeline-activity-details">';
            echo '<h6><i class="fa fa-info-circle"></i> Additional Information</h6>';
            if (is_array($additional_data)) {
                echo '<ul>';
                foreach ($additional_data as $key => $value) {
                    if (is_string($value) || is_numeric($value)) {
                        echo '<li><strong>' . e(ucwords(str_replace('_', ' ', $key))) . ':</strong> ' . e($value) . '</li>';
                    }
                }
                echo '</ul>';
            } else {
                echo '<p>' . e($additional_data) . '</p>';
            }
            echo '</div>';
        }
        break;
}
?>
