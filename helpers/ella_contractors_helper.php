<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ella Contractors Helper Functions
 */

/**
 * Get contract media files
 * @param int $contract_id
 * @param bool $include_default
 * @return array
 */
function get_contract_media($contract_id, $include_default = true) {
    $CI = &get_instance();
    
    // Get contract-specific media
        $CI->db->select('*');
        $CI->db->from('ella_contractor_media');
            $CI->db->where('contract_id', $contract_id);
    $CI->db->where('is_default', 0);
    $contract_media = $CI->db->get()->result();
    
    // Get default media if requested
    $default_media = [];
    if ($include_default) {
        $CI->db->select('*');
        $CI->db->from('ella_contractor_media');
        $CI->db->where('is_default', 1);
        $default_media = $CI->db->get()->result();
    }
    
    // Merge and return
    return array_merge($contract_media, $default_media);
}

/**
 * Get default contract media files
 * @return array
 */
function get_default_contract_media() {
    $CI = &get_instance();
    
    // Check if table exists
    if (!$CI->db->table_exists('ella_contractor_media')) {
        return [];
    }
    
    $CI->db->select('*');
    $CI->db->from('ella_contractor_media');
    $CI->db->where('is_default', 1);
    $CI->db->order_by('id', 'DESC');
    
    return $CI->db->get()->result();
}

/**
 * Get file icon based on file type
 * @param string $file_type
 * @return string
 */
function get_file_icon($file_type) {
    $icon_map = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
        'jpg' => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png' => 'fa-file-image',
        'gif' => 'fa-file-image',
        'bmp' => 'fa-file-image',
            'mp4' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'mov' => 'fa-file-video',
            'wmv' => 'fa-file-video',
        'mp3' => 'fa-file-audio',
        'wav' => 'fa-file-audio',
        'zip' => 'fa-file-archive',
        'rar' => 'fa-file-archive',
        '7z' => 'fa-file-archive'
    ];
    
    $file_extension = strtolower(pathinfo($file_type, PATHINFO_EXTENSION));
    return isset($icon_map[$file_extension]) ? $icon_map[$file_extension] : 'fa-file';
}

/**
 * Format file size in human readable format
 * @param int $bytes
 * @param int $decimals
 * @return string
 */
function formatBytes($bytes, $decimals = 2) {
    if ($bytes === 0) return '0 Bytes';
    
    $k = 1024;
    $dm = $decimals < 0 ? 0 : $decimals;
    $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
}

/**
 * Limit text length with ellipsis (custom version)
 * @param string $str
 * @param int $length
 * @return string
 */
function ella_character_limiter($str, $length) {
    if (strlen($str) <= $length) {
        return $str;
    }
    
    return substr($str, 0, $length) . '...';
}

/**
 * Get contract status badge class
 * @param string $status
 * @return string
 */
function get_status_badge_class($status) {
    $status_map = [
        'pending' => 'status-pending',
        'in_progress' => 'status-in-progress',
        'completed' => 'status-completed',
        'cancelled' => 'status-cancelled',
        'on_hold' => 'status-on-hold'
    ];
    
    $status_key = strtolower(str_replace(' ', '_', $status));
    return isset($status_map[$status_key]) ? $status_map[$status_key] : 'status-pending';
}

/**
 * Format date in readable format
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date($date, $format = 'M d, Y') {
    if (empty($date)) return 'N/A';
    
    try {
        $date_obj = new DateTime($date);
        return $date_obj->format($format);
    } catch (Exception $e) {
        return 'Invalid Date';
    }
}

/**
 * Get contract progress percentage
 * @param int $contract_id
 * @return int
 */
function get_contract_progress($contract_id) {
    // This is a placeholder function
    // In a real implementation, you would calculate based on completed tasks
    return rand(20, 90); // Random progress for demo
}

/**
 * Check if user has permission to view contract
 * @param int $contract_id
 * @param int $user_id
 * @return bool
 */
function can_view_contract($contract_id, $user_id = null) {
    // This is a placeholder function
    // In a real implementation, you would check user permissions
    return true;
}

/**
 * Get contract summary data
 * @param int $contract_id
 * @return array
 */
function get_contract_summary($contract_id) {
    // This is a placeholder function
    // In a real implementation, you would fetch from database
    return [
        'total_tasks' => rand(10, 25),
        'completed_tasks' => rand(5, 20),
        'pending_tasks' => rand(1, 10),
        'total_hours' => rand(40, 200),
        'budget_used' => rand(60, 90),
        'days_remaining' => rand(1, 30)
    ];
}

/**
 * Upload contract media file
 * @param int $contract_id
 * @param string $file_field
 * @param string $description
 * @param bool $is_default
 * @param string $media_category
 * @param string $tags
 * @return array
 */
function upload_contract_media($contract_id, $file_field, $description = '', $is_default = false, $media_category = '', $tags = '') {
    $CI = &get_instance();
    
    // Load upload library
    $CI->load->library('upload');
    
    // Set upload path
    if ($is_default) {
        $upload_path = FCPATH . 'uploads/contracts/default/';
    } else {
        $upload_path = FCPATH . 'uploads/contracts/media/contract_' . $contract_id . '/';
    }
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Configure upload
    $config['upload_path'] = $upload_path;
    $config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|ppt|pptx|jpg|jpeg|png|gif|bmp|mp4|avi|mov|wmv|mp3|wav|zip|rar|7z';
    $config['max_size'] = 50 * 1024; // 50MB
    $config['encrypt_name'] = true;
    $config['remove_spaces'] = true;
    
    $CI->upload->initialize($config);
    
    if (!$CI->upload->do_upload($file_field)) {
        return [
            'success' => false,
            'error' => $CI->upload->display_errors('', '')
        ];
    }
    
    $upload_data = $CI->upload->data();
    
    // Prepare data for database
    $media_data = [
        'contract_id' => $is_default ? null : $contract_id,
        'file_name' => $upload_data['file_name'],
        'original_name' => $upload_data['orig_name'],
        'file_type' => $upload_data['file_ext'],
        'file_size' => $upload_data['file_size'],
        'file_path' => $upload_path . $upload_data['file_name'],
        'is_default' => $is_default ? 1 : 0,
        'description' => $description,
        'media_category' => $media_category,
        'tags' => $tags,
        'uploaded_by' => get_staff_user_id(),
        'date_uploaded' => date('Y-m-d H:i:s')
    ];
    
    // Insert into database
    $CI->db->insert('ella_contractor_media', $media_data);
    
    if ($CI->db->affected_rows() > 0) {
        return [
            'success' => true,
            'media_id' => $CI->db->insert_id(),
            'file_name' => $upload_data['file_name']
        ];
    } else {
        // Delete uploaded file if database insert failed
        unlink($upload_path . $upload_data['file_name']);
        return [
            'success' => false,
            'error' => 'Failed to save media information to database'
        ];
    }
}

/**
 * Delete contract media file
 * @param int $media_id
 * @return bool
 */
function delete_contract_media($media_id) {
    $CI = &get_instance();
    
    // Get media info
    $CI->db->select('*');
    $CI->db->from('ella_contractor_media');
    $CI->db->where('id', $media_id);
    $media = $CI->db->get()->row();
    
    if (!$media) {
        return false;
    }
    
    // Delete file from filesystem
    if (file_exists($media->file_path)) {
        unlink($media->file_path);
    }
    
    // Delete from database
    $CI->db->where('id', $media_id);
    $CI->db->delete('ella_contractor_media');
    
    return $CI->db->affected_rows() > 0;
}

/**
 * Get contract media URL for viewing/downloading
 * @param int $contract_id
 * @return string
 */
function get_contract_media_url($contract_id) {
    if ($contract_id) {
        return base_url('uploads/contracts/media/contract_' . $contract_id . '/');
    } else {
        return base_url('uploads/contracts/default/');
    }
}
