<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('handle_ella_media_upload')) {
    function handle_ella_media_upload($is_default = 0, $active = 1, $index_name = 'file') {
        $CI = &get_instance();
        $uploaded_files = [];
        
        $base_path = FCPATH . 'uploads/ella_presentations/';
        $path = $is_default ? $base_path . 'default/' : $base_path . 'general/';
        
        _maybe_create_upload_path($path);
        
        // Ensure the directory exists and is writable
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true)) {
                log_message('error', 'Ella Media Upload: Failed to create directory: ' . $path);
                return false;
            }
        }
        
        if (!is_writable($path)) {
            log_message('error', 'Ella Media Upload: Directory is not writable: ' . $path);
            return false;
        }
        
        // Debug: Log upload attempt
        log_message('debug', 'Ella Media Upload: Attempting to upload file. Path: ' . $path);
        
        if (isset($_FILES[$index_name]['name']) && !empty($_FILES[$index_name]['name'])) {
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
                
                log_message('debug', 'Ella Media Upload: Processing file: ' . $originalName);
                
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Check for upload errors
                    if (_perfex_upload_error($_FILES[$index_name]['error'][$i])) {
                        log_message('error', 'Ella Media Upload: Upload error for file: ' . $originalName . ' Error code: ' . $_FILES[$index_name]['error'][$i]);
                        continue;
                    }
                    
                    // Check file extension - allow specific file types for presentations
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    $allowed_extensions = ['pdf', 'ppt', 'pptx', 'html'];
                    
                    if (!in_array($extension, $allowed_extensions)) {
                        log_message('error', 'Ella Media Upload: Invalid file extension for file: ' . $originalName . ' Extension: ' . $extension);
                        continue;
                    }
                    
                    $filename = unique_filename($path, $originalName);
                    $newFilePath = $path . $filename;
                    
                    log_message('debug', 'Ella Media Upload: Moving file from ' . $tmpFilePath . ' to ' . $newFilePath);
                    
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $data = [
                            'file_name' => $filename,
                            'original_name' => $originalName,
                            'file_type' => $_FILES[$index_name]['type'][$i],
                            'file_size' => $_FILES[$index_name]['size'][$i],
                            'is_default' => $is_default,
                            'active' => $active,
                            'date_uploaded' => date('Y-m-d H:i:s')
                        ];
                        
                        $CI->db->insert(db_prefix() . 'ella_contractor_media', $data);
                        $insert_id = $CI->db->insert_id();
                        
                        if ($insert_id) {
                            $uploaded_files[] = $insert_id;
                            log_message('debug', 'Ella Media Upload: Successfully uploaded file: ' . $originalName . ' with ID: ' . $insert_id);
                        } else {
                            log_message('error', 'Ella Media Upload: Database insert failed for file: ' . $originalName);
                        }
                    } else {
                        log_message('error', 'Ella Media Upload: Failed to move uploaded file: ' . $originalName);
                    }
                } else {
                    log_message('error', 'Ella Media Upload: Empty file path for file: ' . $originalName);
                }
            }
        }
        
        return $uploaded_files;
    }
}
