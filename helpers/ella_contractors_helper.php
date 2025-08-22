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
 * Get appropriate file icon based on file type
 * @param string $file_type MIME type of the file
 * @return string FontAwesome icon class
 */
function get_file_icon($file_type)
{
    $icon_map = [
        // Images
        'image/jpeg' => 'fa-file-image',
        'image/png' => 'fa-file-image',
        'image/gif' => 'fa-file-image',
        'image/webp' => 'fa-file-image',
        'image/svg+xml' => 'fa-file-image',
        'image/tiff' => 'fa-file-image',
        'image/bmp' => 'fa-file-image',
        
        // Documents
        'application/pdf' => 'fa-file-pdf',
        'application/msword' => 'fa-file-word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fa-file-word',
        'application/vnd.ms-excel' => 'fa-file-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fa-file-excel',
        'application/vnd.ms-powerpoint' => 'fa-file-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fa-file-powerpoint',
        
        // Archives
        'application/zip' => 'fa-file-archive',
        'application/x-rar-compressed' => 'fa-file-archive',
        'application/x-7z-compressed' => 'fa-file-archive',
        'application/gzip' => 'fa-file-archive',
        'application/tar' => 'fa-file-archive',
        
        // Text files
        'text/plain' => 'fa-file-text',
        'text/html' => 'fa-file-code',
        'text/css' => 'fa-file-code',
        'text/javascript' => 'fa-file-code',
        'application/json' => 'fa-file-code',
        'application/xml' => 'fa-file-code',
        
        // Audio
        'audio/mpeg' => 'fa-file-audio',
        'audio/wav' => 'fa-file-audio',
        'audio/ogg' => 'fa-file-audio',
        'audio/mp4' => 'fa-file-audio',
        
        // Video
        'video/mp4' => 'fa-file-video',
        'video/avi' => 'fa-file-video',
        'video/mov' => 'fa-file-video',
        'video/wmv' => 'fa-file-video',
        'video/webm' => 'fa-file-video',
        
        // CAD files
        'application/dxf' => 'fa-drafting-compass',
        'application/dwg' => 'fa-drafting-compass',
        
        // 3D files
        'model/stl' => 'fa-cube',
        'model/obj' => 'fa-cube',
        'model/fbx' => 'fa-cube',
        
        // Default
        'default' => 'fa-file'
    ];
    
    // Check if we have a specific icon for this file type
    if (isset($icon_map[$file_type])) {
        return $icon_map[$file_type];
    }
    
    // Check by file extension if MIME type not found
    $extension = pathinfo($file_type, PATHINFO_EXTENSION);
    if ($extension) {
        $extension_icons = [
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'webp' => 'fa-file-image',
            'svg' => 'fa-file-image',
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            '7z' => 'fa-file-archive',
            'txt' => 'fa-file-text',
            'html' => 'fa-file-code',
            'css' => 'fa-file-code',
            'js' => 'fa-file-code',
            'json' => 'fa-file-code',
            'xml' => 'fa-file-code',
            'mp3' => 'fa-file-audio',
            'wav' => 'fa-file-audio',
            'ogg' => 'fa-file-audio',
            'mp4' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'mov' => 'fa-file-video',
            'wmv' => 'fa-file-video',
            'webm' => 'fa-file-video',
            'stl' => 'fa-cube',
            'obj' => 'fa-cube',
            'fbx' => 'fa-cube',
            'dxf' => 'fa-drafting-compass',
            'dwg' => 'fa-drafting-compass'
        ];
        
        if (isset($extension_icons[$extension])) {
            return $extension_icons[$extension];
        }
    }
    
    // Return default icon
    return $icon_map['default'];
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

/**
 * Generate a shareable URL for media gallery
 * @param int $contract_id Contract ID
 * @param string $hash Access hash
 * @return string Shareable URL
 */
function get_media_gallery_shareable_url($contract_id, $hash)
{
    return site_url("media-gallery/{$contract_id}/{$hash}");
}

/**
 * Generate a shareable URL for default media gallery
 * @param string $hash Access hash
 * @return string Shareable URL
 */
function get_default_media_gallery_shareable_url($hash)
{
    return site_url("default-media-gallery/{$hash}");
}

/**
 * Check if media gallery hash is valid for access
 * @param int $contract_id Contract ID
 * @param string $hash Access hash
 * @return bool True if valid, false otherwise
 */
function check_media_gallery_access($contract_id, $hash)
{
    $CI = &get_instance();
    
    if (!$hash || !$contract_id) {
        return false;
    }
    
    // Check if this is a contract-specific gallery
    if ($contract_id > 0) {
        $CI->db->where('id', $contract_id);
        $CI->db->where('hash', $hash);
        $proposal = $CI->db->get(db_prefix() . 'proposals')->row();
        
        if ($proposal && $proposal->hash == $hash) {
            return true;
        }
    }
    
    // Check if this is a default media gallery access
    if ($contract_id == 0) {
        // For default media, we'll use a special hash system
        // You can implement your own hash validation logic here
        $CI->db->where('hash', $hash);
        $CI->db->where('is_default', 1);
        $media = $CI->db->get(db_prefix() . 'ella_contractor_media')->row();
        
        if ($media && $media->hash == $hash) {
            return true;
        }
    }
    
    return false;
}

/**
 * Generate a unique hash for media gallery access
 * @param int $contract_id Contract ID (0 for default media)
 * @return string Unique hash
 */
function generate_media_gallery_hash($contract_id = 0)
{
    $CI = &get_instance();
    
    if ($contract_id > 0) {
        // For contract-specific galleries, use the proposal hash
        $CI->db->where('id', $contract_id);
        $proposal = $CI->db->get(db_prefix() . 'proposals')->row();
        
        if ($proposal) {
            return $proposal->hash;
        }
    }
    
    // For default media gallery, generate a new hash
    $hash = app_generate_hash();
    
    // Store this hash in the database for validation
    $CI->db->insert(db_prefix() . 'ella_contractor_media', [
        'contract_id' => 0,
        'hash' => $hash,
        'is_default' => 1,
        'date_uploaded' => date('Y-m-d H:i:s')
    ]);
    
    return $hash;
}

/**
 * Get media files for public gallery (filtered for public access)
 * @param int $contract_id Contract ID (0 for default media)
 * @param string $hash Access hash
 * @return array Media files
 */
function get_public_media_gallery($contract_id, $hash)
{
    if (!check_media_gallery_access($contract_id, $hash)) {
        return [];
    }
    
    $CI = &get_instance();
    
    if ($contract_id > 0) {
        // Get contract-specific media
        return get_contract_media($contract_id);
    } else {
        // Get default media
        return get_default_contract_media();
    }
}
