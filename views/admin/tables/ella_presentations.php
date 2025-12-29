<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Prevent any output before JSON
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

$has_permission_delete = has_permission('ella_contractor', '', 'delete');
$has_permission_view   = has_permission('ella_contractor', '', 'view');
$has_permission_edit   = has_permission('ella_contractor', '', 'edit');

// Helper function for file size formatting
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $decimals = 2) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $dm = $decimals < 0 ? 0 : $decimals;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }
}

$aColumns = [
    db_prefix() . 'ella_contractor_media.id as checkbox_id', // 0: For checkbox selection
    db_prefix() . 'ella_contractor_media.id as id',           // 1: ID column
    db_prefix() . 'ella_contractor_media.original_name as file_name', // 2: File Name
    db_prefix() . 'ella_contractor_media.file_name as internal_file_name', // 3: Internal file name for extension
    db_prefix() . 'ella_contractor_media.file_size as file_size',    // 4: Size
    db_prefix() . 'ella_contractor_media.date_uploaded as date_uploaded', // 5: Upload Date
    'CONCAT(' . db_prefix() . 'staff.firstname, " ", ' . db_prefix() . 'staff.lastname) as staff_full_name', // 6: Published By (sortable by full name)
    db_prefix() . 'ella_contractor_media.uploaded_by as uploaded_by', // 7: Staff ID for avatar
    db_prefix() . 'staff.firstname as staff_firstname',               // 8: Staff first name
    db_prefix() . 'staff.lastname as staff_lastname',                 // 9: Staff last name
    db_prefix() . 'ella_contractor_media.id as file_path_id',         // 10: File Path (hidden column)
    db_prefix() . 'ella_contractor_media.id as options_id',           // 11: Options column
];

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'ella_contractor_media.uploaded_by'
];

$where = [
    'AND ' . db_prefix() . 'ella_contractor_media.rel_type = "presentation"'
];

try {
    $result = data_tables_init($aColumns, 'id', db_prefix() . 'ella_contractor_media', $join, $where, [], '', '', []);
    
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
        
        // Checkbox column
        $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
        
        // ID column
        $row[] = '<div class="text-center">' . htmlspecialchars($aRow['id']) . '</div>';
        
        // File Name column - with inline edit pencil icon
        $row[] = '<div class="text-center">
                    <span class="presentation-name" data-id="' . $aRow['id'] . '">' . htmlspecialchars($aRow['file_name']) . '</span>
                    <i class="fa fa-pencil edit-name-icon" onclick="editPresentationName(' . $aRow['id'] . ', \'' . addslashes($aRow['file_name']) . '\'); event.stopPropagation();" style="font-size: 11px; margin-left: 6px; opacity: 0.5; cursor: pointer; color: #3498db;" title="Edit name"></i>
                  </div>';
        
        // Type column
        $ext = strtoupper(pathinfo($aRow['internal_file_name'], PATHINFO_EXTENSION));
        $row[] = '<div class="text-center">' . $ext . '</div>';
        
        // Size column
        $row[] = '<div class="text-center">' . formatBytes($aRow['file_size']) . '</div>';
        
        // Upload Date column - formatted like appointments (date + time)
        $date_formatted = '';
        
        if (!empty($aRow['date_uploaded'])) {
            $date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $aRow['date_uploaded']);
            if ($date_obj) {
                $date_formatted = $date_obj->format('F jS, Y');
                
                // Add time underneath
                $time_formatted = strtolower($date_obj->format('g:ia'));
                $date_formatted .= '<br><small class="">' . $time_formatted . '</small>';
            } else {
                // Fallback if parsing fails
                $date_formatted = date('M d, Y', strtotime($aRow['date_uploaded']));
            }
        }
        
        $row[] = '<div class="text-center">' . $date_formatted . '</div>';
        
        // Published By column - Staff avatar with tooltip (like leads table)
        // Column 6 (staff_full_name) is used for sorting, we just display the avatar here
        $publishedByOutput = '';
        if (!empty($aRow['uploaded_by'])) {
            $full_name = $aRow['staff_firstname'] . ' ' . $aRow['staff_lastname'];
            $publishedByOutput = '<div class="text-center">
                <a data-toggle="tooltip" data-title="' . htmlspecialchars($full_name) . '" href="' . admin_url('profile/' . $aRow['uploaded_by']) . '">' . 
                    staff_profile_image($aRow['uploaded_by'], ['staff-profile-image-small']) . 
                '</a>
            </div>';
            // Add hidden full name for export
            $publishedByOutput .= '<span class="hide">' . htmlspecialchars($full_name) . '</span>';
        } else {
            $publishedByOutput = '<div class="text-center"><span class="text-muted">—</span></div>';
        }
        $row[] = $publishedByOutput;
        
        // Generate public URL and complete file path (for export only)
        // All presentations now stored in single folder
        // CRITICAL: Microsoft Office Online Viewer REQUIRES HTTPS URLs
        // Force HTTPS for external viewer compatibility
        $publicUrl = site_url('uploads/ella_presentations/' . $aRow['internal_file_name']);
        $publicUrl = str_replace('http://', 'https://', $publicUrl);
        
        $completeFilePath = $publicUrl;
        
        // File Path column (hidden from display via DataTables, but visible in export)
        // Contains full URL path for export purposes
        $row[] = '<div class="text-center">' . htmlspecialchars($completeFilePath) . '</div>';
        
        // Options column - buttons centered
        $options = '<div class="text-center" style="white-space: nowrap;">';
        
        $ext_lower = strtolower(pathinfo($aRow['internal_file_name'], PATHINFO_EXTENSION));
        
        if ($has_permission_view) {
            $options .= '<button class="btn btn-info btn-xs" style="display: inline-block; margin-right: 5px;" onclick="previewFile(' . $aRow['id'] . ', \'' . addslashes($aRow['file_name']) . '\', \'' . $ext_lower . '\', \'' . $publicUrl . '\'); return false;" title="Preview"><i class="fa fa-eye"></i></button>';
        }
        if ($has_permission_delete) {
            $options .= '<button class="btn btn-danger btn-xs" style="display: inline-block;" onclick="deletePresentation(' . $aRow['id'] . '); return false;" title="Delete"><i class="fa fa-trash"></i></button>';
        }
        
        $options .= '</div>';
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

