<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Helper functions for Ella Contractors module
 */



/**
 * Get module version
 */
function get_ella_contractors_version()
{
    return '1.0.0';
}

/**
 * Check if contractor has permission
 */
function has_contractor_permission($permission, $contractor_id = null)
{
    if (is_admin()) {
        return true;
    }
    
    return has_permission('ella_contractors', '', $permission);
}

/**
 * Get upload path for contract media
 */
function get_contract_media_upload_path($contract_id = null)
{
    $base_path = FCPATH . 'uploads/contracts/';
    
    if ($contract_id) {
        return $base_path . 'media/contract_' . $contract_id . '/';
    } else {
        return $base_path . 'default/';
    }
}

/**
 * Get URL for contract media
 */
function get_contract_media_url($contract_id = null)
{
    $base_url = base_url('uploads/contracts/');
    
    if ($contract_id) {
        return $base_url . 'media/contract_' . $contract_id . '/';
    } else {
        return $base_url . 'default/';
    }
}

/**
 * Upload contract media file
 */
function upload_contract_media($contract_id, $file_input_name, $description = '', $is_default = false)
{
    $CI = &get_instance();
    
    // Ensure the table exists before proceeding
    ensure_contract_media_table_exists();
    
    // Allowed file types
    $allowed_types = 'pdf|doc|docx|xls|xlsx|ppt|pptx|jpg|jpeg|png|gif|zip|rar';
    
    // Set upload path
    $upload_path = get_contract_media_upload_path($is_default ? null : $contract_id);
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Configure upload
    $config['upload_path'] = $upload_path;
    $config['allowed_types'] = $allowed_types;
    $config['max_size'] = 51200; // 50MB
    $config['encrypt_name'] = true;
    
    $CI->load->library('upload', $config);
    
    if (!$CI->upload->do_upload($file_input_name)) {
        return ['success' => false, 'error' => $CI->upload->display_errors()];
    }
    
    $upload_data = $CI->upload->data();
    
    // Save to database
    $media_data = [
        'contract_id' => $is_default ? null : $contract_id,
        'file_name' => $upload_data['file_name'],
        'original_name' => $upload_data['orig_name'],
        'file_type' => $upload_data['file_type'],
        'file_size' => $upload_data['file_size'],
        'file_path' => 'uploads/contracts/' . ($is_default ? 'default/' : 'media/contract_' . $contract_id . '/') . $upload_data['file_name'],
        'is_default' => $is_default ? 1 : 0,
        'description' => $description,
        'uploaded_by' => get_staff_user_id(),
        'date_uploaded' => date('Y-m-d H:i:s')
    ];
    
    $CI->db->insert('tblella_contractor_media', $media_data);
    $media_id = $CI->db->insert_id();
    
    return [
        'success' => true, 
        'media_id' => $media_id,
        'file_data' => $upload_data,
        'media_data' => $media_data
    ];
}

/**
 * Get contract media files
 */
function get_contract_media($contract_id, $include_defaults = true)
{
    $CI = &get_instance();
    
    // Ensure the table exists before querying
    ensure_contract_media_table_exists();
    
    try {
        $CI->db->select('*');
        $CI->db->from('ella_contractor_media');
        
        if ($include_defaults) {
            $CI->db->group_start();
            $CI->db->where('contract_id', $contract_id);
            $CI->db->or_where('is_default', 1);
            $CI->db->group_end();
        } else {
            $CI->db->where('contract_id', $contract_id);
        }
        
        $CI->db->order_by('date_uploaded', 'DESC');
        
        $result = $CI->db->get()->result();
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in get_contract_media(): " . $e->getMessage());
        return [];
    }
}

/**
 * Get default media files
 */
function get_default_contract_media()
{
    $CI = &get_instance();
    
    // Ensure the table exists before querying
    ensure_contract_media_table_exists();
    
    try {
        $CI->db->select('*');
        $CI->db->from('ella_contractor_media');
        $CI->db->where('is_default', 1);
        $CI->db->order_by('date_uploaded', 'DESC');
        
        $result = $CI->db->get()->result();
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in get_default_contract_media(): " . $e->getMessage());
        return [];
    }
}

/**
 * Delete contract media
 */
function delete_contract_media($media_id)
{
    $CI = &get_instance();
    
    // Ensure the table exists before querying
    ensure_contract_media_table_exists();
    
    // Get media info
    $media = $CI->db->where('id', $media_id)->get('ella_contractor_media')->row();
    
    if (!$media) {
        return false;
    }
    
    // Delete file
    $file_path = FCPATH . $media->file_path;
    if (file_exists($file_path) && is_file($file_path)) {
        unlink($file_path);
    }
    
    // Delete from database
    $CI->db->where('id', $media_id)->delete('ella_contractor_media');
    
    return true;
}

/**
 * Get file icon based on file type
 */
function get_file_icon($file_type)
{
    $icons = [
        'application/pdf' => 'fa-file-pdf-o text-danger',
        'application/msword' => 'fa-file-word-o text-primary',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word-o text-primary',
        'application/vnd.ms-excel' => 'fa-file-excel-o text-success',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel-o text-success',
        'application/vnd.ms-powerpoint' => 'fa-file-powerpoint-o text-warning',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint-o text-warning',
        'image/jpeg' => 'fa-file-image-o text-info',
        'image/jpg' => 'fa-file-image-o text-info',
        'image/png' => 'fa-file-image-o text-info',
        'image/gif' => 'fa-file-image-o text-info',
        'application/zip' => 'fa-file-archive-o text-muted',
        'application/x-rar-compressed' => 'fa-file-archive-o text-muted'
    ];
    
    return isset($icons[$file_type]) ? $icons[$file_type] : 'fa-file-o text-muted';
}

/**
 * Ensure the contract media table exists
 */
function ensure_contract_media_table_exists()
{
    $CI = &get_instance();
    $table_name = 'ella_contractor_media';
    
    // Check if table exists (with and without tbl prefix)
    if (!$CI->db->table_exists($table_name) && !$CI->db->table_exists('tbl' . $table_name)) {
        $CI->load->dbforge();
        
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'contract_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => FALSE
            ],
            'is_default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'date_uploaded' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contract_id');
        $CI->dbforge->add_key('is_default');
        $CI->dbforge->create_table($table_name);
    }
}

/**
 * Format file size
 */
function format_file_size($size_kb)
{
    if ($size_kb < 1024) {
        return $size_kb . ' KB';
    } elseif ($size_kb < 1048576) {
        return round($size_kb / 1024, 2) . ' MB';
    } else {
        return round($size_kb / 1048576, 2) . ' GB';
    }
}
