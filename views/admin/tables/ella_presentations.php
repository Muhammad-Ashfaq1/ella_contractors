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

$has_permission_delete = has_permission('ella_contractors', '', 'delete');
$has_permission_view   = has_permission('ella_contractors', '', 'view');
$has_permission_edit   = has_permission('ella_contractors', '', 'edit');

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
    db_prefix() . 'ella_contractor_media.is_default as is_default',  // 5: Is Default
    db_prefix() . 'ella_contractor_media.active as active',          // 6: Active
    db_prefix() . 'ella_contractor_media.date_uploaded as date_uploaded', // 7: Upload Date
    db_prefix() . 'ella_contractor_media.id as options_id',          // 8: Options column
];

$join = [];

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
        
        // File Name column
        $row[] = '<div class="text-center">' . htmlspecialchars($aRow['file_name']) . '</div>';
        
        // Type column
        $ext = strtoupper(pathinfo($aRow['internal_file_name'], PATHINFO_EXTENSION));
        $row[] = '<div class="text-center">' . $ext . '</div>';
        
        // Size column
        $row[] = '<div class="text-center">' . formatBytes($aRow['file_size']) . '</div>';
        
        // Is Default column with dropdown (similar to appointment status)
        $is_default = $aRow['is_default'] ? 'Yes' : 'No';
        $is_default_label = $aRow['is_default'] ? 'YES' : 'NO';
        $badge_class = $aRow['is_default'] ? 'label-success' : 'label-default';
        
        // Create is_default display with dropdown - export only the main label
        $outputIsDefault = '<div class="text-center" data-order="' . htmlspecialchars($is_default_label) . '">';
        $outputIsDefault .= '<div class="status-wrapper" style="position: relative; display: inline-block;">';
        
        // Main status text for display and export
        $outputIsDefault .= '<span class="status-button label ' . $badge_class . '" id="is-default-btn-' . $aRow['id'] . '" style="cursor: pointer !important;">';
        $outputIsDefault .= $is_default_label;
        $outputIsDefault .= '</span>';
        
        // Dropdown menu positioned on the left side (excluded from export via table-export-exclude class)
        if ($has_permission_edit) {
            $outputIsDefault .= '<div id="is-default-menu-' . $aRow['id'] . '" class="status-dropdown table-export-exclude" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 100px;">';
            
            $available_options = [
                ['value' => 1, 'label' => 'YES'],
                ['value' => 0, 'label' => 'NO']
            ];
            
            foreach ($available_options as $option) {
                if ($aRow['is_default'] != $option['value']) {
                    $outputIsDefault .= '<div class="status-option table-export-exclude" onclick="updateIsDefault(' . $option['value'] . ', ' . $aRow['id'] . '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
                    $outputIsDefault .= $option['label'];
                    $outputIsDefault .= '</div>';
                }
            }
            
            $outputIsDefault .= '</div>';
        }
        
        $outputIsDefault .= '</div>';
        $outputIsDefault .= '</div>';
        $row[] = $outputIsDefault;
        
        // Active column with dropdown (similar to appointment status)
        $active = $aRow['active'] ? 'Yes' : 'No';
        $active_label = $aRow['active'] ? 'YES' : 'NO';
        $badge_class_active = $aRow['active'] ? 'label-success' : 'label-danger';
        
        // Create active display with dropdown - export only the main label
        $outputActive = '<div class="text-center" data-order="' . htmlspecialchars($active_label) . '">';
        $outputActive .= '<div class="status-wrapper" style="position: relative; display: inline-block;">';
        
        // Main status text for display and export
        $outputActive .= '<span class="status-button label ' . $badge_class_active . '" id="active-btn-' . $aRow['id'] . '" style="cursor: pointer !important;">';
        $outputActive .= $active_label;
        $outputActive .= '</span>';
        
        // Dropdown menu positioned on the left side (excluded from export via table-export-exclude class)
        if ($has_permission_edit) {
            $outputActive .= '<div id="active-menu-' . $aRow['id'] . '" class="status-dropdown table-export-exclude" style="display: none; position: absolute; top: 0; right: 100%; z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 100px;">';
            
            $available_options = [
                ['value' => 1, 'label' => 'YES'],
                ['value' => 0, 'label' => 'NO']
            ];
            
            foreach ($available_options as $option) {
                if ($aRow['active'] != $option['value']) {
                    $outputActive .= '<div class="status-option table-export-exclude" onclick="updateActive(' . $option['value'] . ', ' . $aRow['id'] . '); return false;" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">';
                    $outputActive .= $option['label'];
                    $outputActive .= '</div>';
                }
            }
            
            $outputActive .= '</div>';
        }
        
        $outputActive .= '</div>';
        $outputActive .= '</div>';
        $row[] = $outputActive;
        
        // Upload Date column
        $date_formatted = date('M d, Y', strtotime($aRow['date_uploaded']));
        $row[] = '<div class="text-center">' . $date_formatted . '</div>';
        
        // Generate public URL and complete file path (for export only)
        $folder = $aRow['is_default'] ? 'default' : 'general';
        $publicUrl = site_url('uploads/ella_presentations/' . $folder . '/' . $aRow['internal_file_name']);
        $completeFilePath = site_url('uploads/ella_presentations/' . $folder . '/' . $aRow['internal_file_name']);
        
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

