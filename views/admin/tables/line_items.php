<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [];

if (has_permission('ella_contractors', '', 'delete')) {
    $aColumns[] = '1';
}

$aColumns = array_merge($aColumns, [
    'image',
    'name',
    'description',
    'cost',
    'quantity',
    'unit_type',
    'is_active',
    ]);

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'ella_contractor_line_items';

$join = [
    'LEFT JOIN ' . db_prefix() . 'ella_contractor_line_item_groups ON ' . db_prefix() . 'ella_contractor_line_item_groups.id = ' . db_prefix() . 'ella_contractor_line_items.group_id',
    ];

$additionalSelect = [
    db_prefix() . 'ella_contractor_line_items.id',
    db_prefix() . 'ella_contractor_line_items.group_id',
    db_prefix() . 'ella_contractor_line_item_groups.name as group_name',
    ];

$custom_fields = get_custom_fields('line_items');
$customFieldsColumns = [];

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'ella_contractor_line_items.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="line_items" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    if (has_permission('ella_contractors', '', 'delete')) {
        $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    } else {
        $row[] = '';
    }

    // Image column
    if ($aRow['image']) {
        $image_url = site_url('uploads/ella_line_items/' . $aRow['image']);
        $row[] = '<img src="' . $image_url . '" alt="' . htmlspecialchars($aRow['name']) . '" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">';
    } else {
        $row[] = '<div class="text-center" style="width: 40px; height: 40px; background: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;"><i class="fa fa-image text-muted"></i></div>';
    }

    // Name column with actions
    $nameOutput = '';
    $nameOutput = '<a href="#" data-toggle="modal" data-target="#line_item_modal" data-id="' . $aRow['id'] . '">' . $aRow['name'] . '</a>';
    $nameOutput .= '<div class="row-options">';

    if (has_permission('ella_contractors', '', 'edit')) {
        $nameOutput .= '<a href="#" data-toggle="modal" data-target="#line_item_modal" data-id="' . $aRow['id'] . '">Edit</a>';
    }

    if (has_permission('ella_contractors', '', 'delete')) {
        $nameOutput .= ' | <a href="' . admin_url('ella_contractors/delete_line_item/' . $aRow['id']) . '" class="text-danger _delete">Delete</a>';
    }

    $nameOutput .= '</div>';

    $row[] = $nameOutput;

    // Description column
    $description = $aRow['description'] ? htmlspecialchars(substr($aRow['description'], 0, 30)) . '...' : '-';
    $row[] = $description;

    // Cost column
    $cost = $aRow['cost'] ? '$' . number_format($aRow['cost'], 2) : 'N/A';
    $row[] = $cost;

    // Quantity column
    $row[] = number_format($aRow['quantity'], 2);

    // Unit Type column
    $row[] = $aRow['unit_type'];

    // Status column
    if ($aRow['is_active']) {
        $row[] = '<span class="label label-success">Active</span>';
    } else {
        $row[] = '<span class="label label-default">Inactive</span>';
    }

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}
