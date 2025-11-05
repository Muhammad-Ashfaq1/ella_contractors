<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Handle Ella Media Upload - Supports both Presentations and Attachments
 * 
 * @param string $custom_name   Custom name for presentation (used as file_name in DB)
 * @param string $description   Description for the file
 * @param string $index_name    $_FILES array key
 * @param string $rel_type      'presentation' or 'attachment' (default: 'presentation')
 * @param int    $rel_id        Related entity ID (e.g., appointment_id)
 * @return array|false          Array of uploaded file IDs or false on failure
 */
if (!function_exists('handle_ella_media_upload')) {
    function handle_ella_media_upload($custom_name = '', $description = '', $index_name = 'file', $rel_type = 'presentation', $rel_id = null) {
        $CI = &get_instance();
        $uploaded_files = [];
        
        // Determine upload path based on rel_type
        if ($rel_type === 'presentation') {
            // Presentations: /uploads/ella_presentations/
            $path = FCPATH . 'uploads/ella_presentations/';
            $allowed_extensions = ['pdf', 'ppt', 'pptx', 'html'];
        } elseif ($rel_type === 'attachment') {
            // Attachments: /uploads/ella_appointments/{appointment_id}/
            if (!$rel_id) {
                log_message('error', 'Ella Media Upload: rel_id required for attachments');
                return false;
            }
            $path = FCPATH . 'uploads/ella_appointments/' . $rel_id . '/';
            $allowed_extensions = ['pdf', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        } else {
            log_message('error', 'Ella Media Upload: Invalid rel_type: ' . $rel_type);
            return false;
        }
        
        _maybe_create_upload_path($path);
        
        // Ensure directory exists and is writable
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                log_message('error', 'Ella Media Upload: Failed to create directory: ' . $path);
                return false;
            }
        }
        
        if (!is_writable($path)) {
            log_message('error', 'Ella Media Upload: Directory not writable: ' . $path);
            return false;
        }
        
        log_message('debug', 'Ella Media Upload: Uploading ' . $rel_type . ' to: ' . $path);
        
        // Process uploaded files
        if (isset($_FILES[$index_name]['name']) && !empty($_FILES[$index_name]['name'])) {
            // Normalize to array format
            if (!is_array($_FILES[$index_name]['name'])) {
                $_FILES[$index_name] = [
                    'name' => [$_FILES[$index_name]['name']],
                    'type' => [$_FILES[$index_name]['type']],
                    'tmp_name' => [$_FILES[$index_name]['tmp_name']],
                    'error' => [$_FILES[$index_name]['error']],
                    'size' => [$_FILES[$index_name]['size']]
                ];
            }
            
            for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
                $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
                $originalName = $_FILES[$index_name]['name'][$i];
                
                if (empty($tmpFilePath) || $_FILES[$index_name]['error'][$i] !== UPLOAD_ERR_OK) {
                    log_message('error', 'Ella Media Upload: Upload error for ' . $originalName);
                    continue;
                }
                
                // Validate file extension
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowed_extensions)) {
                    log_message('error', 'Ella Media Upload: Invalid extension (' . $extension . ') for ' . $rel_type);
                    continue;
                }
                
                // Generate unique filename
                $filename = unique_filename($path, $originalName);
                $newFilePath = $path . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    // Prepare database record
                    // For presentations: use custom_name as file_name (what user entered)
                    // For attachments: use original filename as file_name
                    $display_name = ($rel_type === 'presentation' && !empty($custom_name)) ? $custom_name : $originalName;
                    
                    $data = [
                        'rel_type' => $rel_type,
                        'rel_id' => $rel_id,
                        'file_name' => $filename,  // Unique physical filename on disk
                        'original_name' => $display_name,  // User's custom name or original filename
                        'file_type' => $_FILES[$index_name]['type'][$i],
                        'file_size' => $_FILES[$index_name]['size'][$i],
                        'description' => $description,
                        'date_uploaded' => date('Y-m-d H:i:s')
                    ];
                    
                    // Insert to database
                    $CI->db->insert(db_prefix() . 'ella_contractor_media', $data);
                    $insert_id = $CI->db->insert_id();
                    
                    if ($insert_id) {
                        $uploaded_files[] = $insert_id;
                        log_message('debug', 'Ella Media: Uploaded ' . $rel_type . ' - ' . $originalName . ' (ID: ' . $insert_id . ')');
                    } else {
                        log_message('error', 'Ella Media Upload: DB insert failed for ' . $originalName);
                        @unlink($newFilePath); // Clean up file if DB insert fails
                    }
                } else {
                    log_message('error', 'Ella Media Upload: Failed to move file: ' . $originalName);
                }
            }
        }
        
        return $uploaded_files;
    }
}

/**
 * Get public URL for presentation file
 * Direct URL access: /uploads/ella_presentations/{filename}
 * 
 * @param object $media Media record from database
 * @return string Public URL
 */
if (!function_exists('get_ella_presentation_public_url')) {
    function get_ella_presentation_public_url($media) {
        if (!$media || $media->rel_type !== 'presentation') {
            return '';
        }
        
        return site_url('uploads/ella_presentations/' . $media->file_name);
    }
}

/**
 * Get public URL for appointment attachment
 * Direct URL access: /uploads/ella_appointments/{appointment_id}/{filename}
 * 
 * @param object $media Media record from database
 * @return string Public URL
 */
if (!function_exists('get_ella_attachment_public_url')) {
    function get_ella_attachment_public_url($media) {
        if (!$media || $media->rel_type !== 'attachment') {
            return '';
        }
        
        if (!$media->rel_id) {
            return '';
        }
        
        return site_url('uploads/ella_appointments/' . $media->rel_id . '/' . $media->file_name);
    }
}
