<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Prevent any output before JSON - more robust approach
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Set error reporting to prevent warnings from corrupting JSON
$old_error_reporting = error_reporting();
error_reporting(E_ERROR | E_PARSE);

// Set proper JSON headers immediately
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

$has_permission_delete = has_permission('ella_contractors', '', 'delete');
$has_permission_view   = has_permission('ella_contractors', '', 'view');
$has_permission_edit   = has_permission('ella_contractors', '', 'edit');

// Ensure required columns exist in the database
$CI =& get_instance();
if (!$CI->db->field_exists('source', db_prefix() . 'appointly_appointments')) {
    try {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `source` VARCHAR(50) NULL DEFAULT NULL');
        $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `source` = "appointly" WHERE `source` IS NULL');
    } catch (Exception $e) {
        // Column might already exist or error occurred
    }
}

if (!$CI->db->field_exists('appointment_status', db_prefix() . 'appointly_appointments')) {
    try {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `appointment_status` ENUM(\'scheduled\',\'cancelled\',\'complete\') NULL DEFAULT \'scheduled\'');
        $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "cancelled" WHERE `cancelled` = 1');
        $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "complete" WHERE `finished` = 1 OR `approved` = 1');
        $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "scheduled" WHERE `appointment_status` IS NULL');
    } catch (Exception $e) {
        // Column might already exist or error occurred
    }
}

$aColumns = [
    db_prefix() . 'appointly_appointments.id as checkbox_id', // For checkbox selection
    db_prefix() . 'appointly_appointments.id as id',
    'COALESCE(' . db_prefix() . 'leads.name, "") as lead_name',
    'COALESCE(' . db_prefix() . 'leads.id, "") as lead_id',
    db_prefix() . 'appointly_appointments.subject as subject',
    db_prefix() . 'appointly_appointments.date as date',
    db_prefix() . 'appointly_appointments.start_hour as start_hour',
    // Use direct appointment_status column access
    'COALESCE(' . db_prefix() . 'appointly_appointments.appointment_status, "scheduled") as status',
    'COALESCE(measurement_counts.measurement_count, 0) as measurement_count',
    'COALESCE(estimate_counts.estimate_count, 0) as estimate_count',
    db_prefix() . 'appointly_appointments.id as options_id' // For options column
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
        FROM ' . db_prefix() . 'proposals 
        WHERE appointment_id IS NOT NULL 
        GROUP BY appointment_id
    ) estimate_counts ON estimate_counts.appointment_id = ' . db_prefix() . 'appointly_appointments.id'
];

$where = [];

// Filter to show only appointments created from EllaContractors module
// Handle cases where source column might not exist or have different values
if ($CI->db->field_exists('source', db_prefix() . 'appointly_appointments')) {
    $where[] = 'AND (' . db_prefix() . 'appointly_appointments.source = "ella_contractor" OR ' . db_prefix() . 'appointly_appointments.source IS NULL)';
} else {
    // If source column doesn't exist, show all appointments (fallback)
    // This ensures the table works even if the column hasn't been created yet
}

// Filter by status if requested (check both column search and custom parameter)
// DataTable columns: 0=checkbox, 1=ID, 2=Lead, 3=Subject, 4=Date, 5=Status, 6=Measurements, 7=Estimates, 8=Options
$status_filter = '';
$date_filter = '';

// Look for status filter in multiple places
if (isset($_POST['columns'][5]['search']['value']) && !empty($_POST['columns'][5]['search']['value'])) {
    $status_filter = $_POST['columns'][5]['search']['value'];
} elseif (isset($_POST['status_filter']) && !empty($_POST['status_filter'])) {
    $status_filter = $_POST['status_filter'];
} elseif (isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
    $status_filter = $_GET['status_filter'];
}

// Look for date filter
if (isset($_POST['date_filter']) && !empty($_POST['date_filter'])) {
    $date_filter = $_POST['date_filter'];
} elseif (isset($_GET['date_filter']) && !empty($_GET['date_filter'])) {
    $date_filter = $_GET['date_filter'];
}

if (!empty($status_filter)) {
    // Use direct appointment_status column filtering with case-insensitive comparison
    $where[] = 'AND LOWER(COALESCE(' . db_prefix() . 'appointly_appointments.appointment_status, "scheduled")) = "' . strtolower($status_filter) . '"';
}

// Apply date filters
if (!empty($date_filter)) {
    $today = date('Y-m-d');
    switch ($date_filter) {
        case 'today':
            $where[] = 'AND ' . db_prefix() . 'appointly_appointments.date = "' . $today . '"';
            break;
        case 'this_week':
            $start_week = date('Y-m-d', strtotime('monday this week'));
            $end_week = date('Y-m-d', strtotime('sunday this week'));
            $where[] = 'AND ' . db_prefix() . 'appointly_appointments.date BETWEEN "' . $start_week . '" AND "' . $end_week . '"';
            break;
        case 'this_month':
            $start_month = date('Y-m-01');
            $end_month = date('Y-m-t');
            $where[] = 'AND ' . db_prefix() . 'appointly_appointments.date BETWEEN "' . $start_month . '" AND "' . $end_month . '"';
            break;
    }
}

try {
    $result = data_tables_init($aColumns, 'id', db_prefix() . 'appointly_appointments', $join, $where, [], '', '', []);
    
    if (!isset($result) || !is_array($result)) {
        throw new Exception('Failed to initialize data tables');
    }
    
    $output  = $result['output'];
    $rResult = $result['rResult'];
    
    if (!isset($output) || !is_array($output)) {
        throw new Exception('Invalid output from data_tables_init');
    }
    
} catch (Exception $e) {
    // Return error response
    $output = [
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Database error: ' . $e->getMessage()
    ];
    $rResult = [];
}


try {
    foreach ($rResult as $aRow) {
        $row = [];
        
        // Checkbox column - centered with proper structure for export functionality
        $row[] = '<div class="text-center"><div class="checkbox"><input type="checkbox" value="' . htmlspecialchars($aRow['id']) . '"><label></label></div></div>';
        
        $row[] = '<div class="text-center">' . htmlspecialchars($aRow['id']) . '</div>';
        
        // Lead column with hyperlink
        $lead_name = isset($aRow['lead_name']) ? $aRow['lead_name'] : '';
        $lead_id = isset($aRow['lead_id']) ? $aRow['lead_id'] : '';
        if (!empty($lead_name) && !empty($lead_id)) {
            $lead_link = '<a href="' . admin_url('leads/index/' . $lead_id) . '">' . htmlspecialchars($lead_name) . '</a>';
        } else {
            $lead_link = '<span class="text-muted">No Lead</span>';
        }
        $row[] = '<div class="text-center">' . $lead_link . '</div>';
        
        $subject = '<a href="' . admin_url('ella_contractors/appointments/view/' . $aRow['id']) . '" class="appointment-subject-link" title="' . htmlspecialchars($aRow['subject']) . '">' . htmlspecialchars($aRow['subject']) . '</a>';
        $row[] = '<div class="text-center">' . $subject . '</div>';
        
        // Format date as "July 5th, 2025" with time underneath
        $date_formatted = '';
        if (!empty($aRow['date'])) {
            $date_obj = DateTime::createFromFormat('Y-m-d', $aRow['date']);
            if ($date_obj) {
                $date_formatted = $date_obj->format('F jS, Y');
            } else {
                $date_formatted = htmlspecialchars($aRow['date']);
            }
            
            if (!empty($aRow['start_hour'])) {
                // Try to parse time (assumes format like "09:45:00" or "09:45")
                $time_obj = DateTime::createFromFormat('H:i:s', $aRow['start_hour']);
                if (!$time_obj) {
                    $time_obj = DateTime::createFromFormat('H:i', $aRow['start_hour']);
                }
        
                if ($time_obj) {
                    // U.S. format => 9:45am / 9:45pm (no leading zeros)
                    $time_formatted = strtolower($time_obj->format('g:ia'));
                } else {
                    // Fallback if parsing fails
                    $time_formatted = htmlspecialchars($aRow['start_hour']);
                }
        
                $date_formatted .= '<br><small class="">' . $time_formatted . '</small>';
            }
        }        
        $row[] = '<div class="text-center">' . $date_formatted . '</div>';
        
        // Status column with dropdown (similar to leads implementation)
        $status = isset($aRow['status']) ? $aRow['status'] : 'scheduled';
        $status_class = '';
        $status_label = '';
        
        switch ($status) {
            case 'cancelled':
                $status_class = 'label-danger';
                $status_label = strtoupper(_l('cancelled'));
                break;
            case 'complete':
                $status_class = 'label-success';
                $status_label = strtoupper(_l('complete'));
                break;
            case 'scheduled':
                $status_class = 'label-info';
                $status_label = strtoupper(_l('scheduled'));
                break;
            default:
                $status_class = 'label-warning';
                $status_label = strtoupper($status);
        }
        
        // Create status display with dropdown - export only the main status label
        // Use data-order attribute for DataTables to properly sort and export
        $outputStatus = '<div class="text-center" data-order="' . htmlspecialchars($status_label) . '">';
        $outputStatus .= '<div class="status-wrapper" style="position: relative; display: inline-block;">';
        $outputStatus .= '<span class="status-button label ' . $status_class . '" id="status-btn-' . $aRow['id'] . '" style="cursor: pointer !important;">';
        $outputStatus .= $status_label;
        $outputStatus .= '</span>';
        
        // Hidden span for export only (will be extracted by DataTables export)
        $outputStatus .= '<span class="hide export-value">' . htmlspecialchars($status_label) . '</span>';
        
        // Dropdown menu positioned on the left side
        if ($has_permission_edit) {
            $outputStatus .= '<div id="status-menu-' . $aRow['id'] . '" class="status-dropdown not-export" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 120px;">';
            
            $available_statuses = [
                ['value' => 'scheduled', 'label' => strtoupper(_l('scheduled'))],
                ['value' => 'complete', 'label' => strtoupper(_l('complete'))],
                ['value' => 'cancelled', 'label' => strtoupper(_l('cancelled'))]
            ];
            
            foreach ($available_statuses as $status_option) {
                if ($status !== $status_option['value']) {
                    $outputStatus .= '<div class="status-option not-export" onclick="appointment_mark_as(\'' . $status_option['value'] . '\', ' . $aRow['id'] . '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
                    $outputStatus .= $status_option['label'];
                    $outputStatus .= '</div>';
                }
            }
            
            $outputStatus .= '</div>';
        }
        
        $outputStatus .= '</div>';
        $outputStatus .= '</div>';
        $row[] = $outputStatus;
        
        // Display measurement count with clickable badge
        $measurement_count = isset($aRow['measurement_count']) ? (int) $aRow['measurement_count'] : 0;
        $measurement_url = admin_url('ella_contractors/appointments/view/' . $aRow['id'] . '?tab=measurements');
        $measurement_badge = $measurement_count > 0 
            ? '<div class="text-center"><a href="' . $measurement_url . '" class="label label-info" title="Click to view measurements"><i class="fa fa-square-o"></i> ' . $measurement_count . '</a></div>'
            : '<div class="text-center"><a href="' . $measurement_url . '" class="text-muted" title="Click to add measurements"><i class="fa fa-square-o"></i> 0</a></div>';
        $row[] = $measurement_badge;
        
        // Display estimate count with clickable badge
        $estimate_count = isset($aRow['estimate_count']) ? (int) $aRow['estimate_count'] : 0;
        $estimate_url = admin_url('ella_contractors/appointments/view/' . $aRow['id'] . '?tab=estimates');
        $estimate_badge = $estimate_count > 0 
            ? '<div class="text-center"><a href="' . $estimate_url . '" class="label label-success" title="Click to view estimates"><i class="fa fa-file-text-o"></i> ' . $estimate_count . '</a></div>'
            : '<div class="text-center"><a href="' . $estimate_url . '" class="text-muted" title="Click to add estimates"><i class="fa fa-file-text-o"></i> 0</a></div>';
        $row[] = $estimate_badge;
        
        $options = '';

        
        // Show full options for all appointments
        if ($has_permission_edit) {
            $options .= ' <a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="editAppointment(' . $aRow['id'] . ')" title="Edit"><i class="fa fa-edit"></i></a>';
        }
        if ($has_permission_delete) {
            $options .= ' <a href="javascript:void(0)" class="btn btn-danger btn-xs" onclick="deleteAppointment(' . $aRow['id'] . ')" title="Delete"><i class="fa fa-trash"></i></a>';
        }
        
        $row[] = $options;
        
        $output['aaData'][] = $row;
    }
} catch (Exception $e) {
    // If data processing fails, ensure we have valid output
    if (!isset($output['aaData'])) {
        $output['aaData'] = [];
    }
    $output['error'] = 'Data processing error: ' . $e->getMessage();
}

// Clean any output buffer and restore error reporting
ob_end_clean();
error_reporting($old_error_reporting);

// Ensure we have valid output
if (!isset($output) || !is_array($output)) {
    $output = [
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
    ];
}

// Output JSON and exit to prevent any additional output
echo json_encode($output, JSON_UNESCAPED_UNICODE);
exit;
