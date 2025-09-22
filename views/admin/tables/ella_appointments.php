<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = has_permission('ella_contractors', '', 'delete');
$has_permission_view   = has_permission('ella_contractors', '', 'view');
$has_permission_edit   = has_permission('ella_contractors', '', 'edit');

$aColumns = [
    '1',
    db_prefix() . 'appointly_appointments.id as id',
    'COALESCE(' . db_prefix() . 'leads.name, "") as lead_name',
    'COALESCE(' . db_prefix() . 'leads.id, "") as lead_id',
    db_prefix() . 'appointly_appointments.subject as subject',
    db_prefix() . 'appointly_appointments.date as date',
    db_prefix() . 'appointly_appointments.start_hour as start_hour',
    'CASE 
        WHEN ' . db_prefix() . 'appointly_appointments.cancelled = 1 THEN "Cancelled"
        WHEN ' . db_prefix() . 'appointly_appointments.finished = 1 THEN "Complete"
        WHEN ' . db_prefix() . 'appointly_appointments.approved = 1 THEN "Complete"
        ELSE "Scheduled"
    END as status',
    'COALESCE(measurement_counts.measurement_count, 0) as measurement_count',
    'COALESCE(estimate_counts.estimate_count, 0) as estimate_count',
    '1'
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'appointly_appointments.contact_id',
    'LEFT JOIN ' . db_prefix() . 'leads ON ' . db_prefix() . 'leads.id = ' . db_prefix() . 'appointly_appointments.contact_id',
    'LEFT JOIN (
        SELECT 
            appointment_id, 
            COUNT(*) as measurement_count
        FROM ' . db_prefix() . 'ella_contractors_measurements 
        WHERE appointment_id IS NOT NULL 
        GROUP BY appointment_id
    ) measurement_counts ON measurement_counts.appointment_id = ' . db_prefix() . 'appointly_appointments.id',
    'LEFT JOIN (
        SELECT 
            appointment_id, 
            COUNT(*) as estimate_count
        FROM ' . db_prefix() . 'ella_contractor_estimates 
        WHERE appointment_id IS NOT NULL 
        GROUP BY appointment_id
    ) estimate_counts ON estimate_counts.appointment_id = ' . db_prefix() . 'appointly_appointments.id'
];

$where = [];

// Filter to show only appointments created from EllaContractors module
$where[] = 'AND ' . db_prefix() . 'appointly_appointments.source = "ella_contractor"';

// Filter for past appointments if requested
if (isset($past) && $past == 1) {
    $where[] = 'AND ' . db_prefix() . 'appointly_appointments.date < CURDATE()';
}

$result = data_tables_init($aColumns, 'id', db_prefix() . 'appointly_appointments', $join, $where, [], '', '', []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    
    $row[] = $aRow['id'];
    
    // Lead column with hyperlink
    $lead_name = $aRow['lead_name'];
    $lead_id = $aRow['lead_id'];
    if (!empty($lead_name) && !empty($lead_id)) {
        $lead_link = '<a href="' . admin_url('leads/lead/' . $lead_id) . '" target="_blank">' . $lead_name . '</a>';
    } else {
        $lead_link = '<span class="text-muted">No Lead</span>';
    }
    $row[] = $lead_link;
    
    $subject = '<a href="' . admin_url('ella_contractors/appointments/view/' . $aRow['id']) . '">' . $aRow['subject'] . '</a>';
    $row[] = $subject;
    
    // Format date as "July 5th, 2025" with time underneath
    $date_formatted = '';
    if (!empty($aRow['date'])) {
        $date_obj = DateTime::createFromFormat('Y-m-d', $aRow['date']);
        if ($date_obj) {
            $date_formatted = $date_obj->format('F jS, Y');
        }
        
        if (!empty($aRow['start_hour'])) {
            $date_formatted .= '<br><small class="text-muted">' . $aRow['start_hour'] . '</small>';
        }
    }
    $row[] = $date_formatted;
    
    $status_class = '';
    switch ($aRow['status']) {
        case 'Cancelled':
            $status_class = 'label-danger';
            break;
        case 'Complete':
            $status_class = 'label-success';
            break;
        case 'Scheduled':
            $status_class = 'label-info';
            break;
        default:
            $status_class = 'label-warning';
    }
    $row[] = '<span class="label ' . $status_class . '">' . $aRow['status'] . '</span>';
    
    // Display measurement count with clickable badge
    $measurement_count = (int) $aRow['measurement_count'];
    $measurement_url = admin_url('ella_contractors/appointments/view/' . $aRow['id'] . '?tab=measurements');
    $measurement_badge = $measurement_count > 0 
        ? '<div class="text-center"><a href="' . $measurement_url . '" class="label label-info" title="Click to view measurements"><i class="fa fa-square-o"></i> ' . $measurement_count . '</a></div>'
        : '<div class="text-center"><a href="' . $measurement_url . '" class="text-muted" title="Click to add measurements"><i class="fa fa-square-o"></i> 0</a></div>';
    $row[] = $measurement_badge;
    
    // Display estimate count with clickable badge
    $estimate_count = (int) $aRow['estimate_count'];
    $estimate_url = admin_url('ella_contractors/appointments/view/' . $aRow['id'] . '?tab=estimates');
    $estimate_badge = $estimate_count > 0 
        ? '<div class="text-center"><a href="' . $estimate_url . '" class="label label-success" title="Click to view estimates"><i class="fa fa-file-text-o"></i> ' . $estimate_count . '</a></div>'
        : '<div class="text-center"><a href="' . $estimate_url . '" class="text-muted" title="Click to add estimates"><i class="fa fa-file-text-o"></i> 0</a></div>';
    $row[] = $estimate_badge;
    
    $options = '';
    if ($has_permission_view) {
        $options .= '<a href="' . admin_url('ella_contractors/appointments/view/' . $aRow['id']) . '" class="btn btn-default btn-xs" title="View Details"><i class="fa fa-eye"></i></a>';
    }
    
    // Only show edit/delete for current appointments, not past ones
    if (isset($past) && $past == 1) {
        // Past appointments - view only
        // No additional options needed
    } else {
        // Current appointments - full options
        if ($has_permission_edit) {
            $options .= ' <a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="editAppointment(' . $aRow['id'] . ')" title="Edit"><i class="fa fa-edit"></i></a>';
        }
        if ($has_permission_delete) {
            $options .= ' <a href="javascript:void(0)" class="btn btn-danger btn-xs" onclick="deleteAppointment(' . $aRow['id'] . ')" title="Delete"><i class="fa fa-trash"></i></a>';
        }
    }
    
    $row[] = $options;
    
    $output['aaData'][] = $row;
}

echo json_encode($output);
