<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('handle_ella_media_upload')) {
    function handle_ella_media_upload($folder_id, $lead_id = null, $is_default = 0, $active = 1, $index_name = 'file') {
        $CI = &get_instance();
        $uploaded_files = [];
        
        $base_path = FCPATH . 'uploads/ella_presentations/';
        if ($is_default) {
            $path = $base_path . 'default/';
        } elseif ($lead_id) {
            $path = $base_path . 'lead_' . $lead_id . '/';
        } else {
            $path = $base_path . 'general/';
        }
        
        _maybe_create_upload_path($path);
        
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
                
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    if (_perfex_upload_error($_FILES[$index_name]['error'][$i]) || !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
                        continue;
                    }
                    
                    $filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                    $newFilePath = $path . $filename;
                    
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $data = [
                            'folder_id' => $folder_id,
                            'lead_id' => $lead_id,
                            'file_name' => $filename,
                            'original_name' => $_FILES[$index_name]['name'][$i],
                            'file_type' => $_FILES[$index_name]['type'][$i],
                            'file_size' => $_FILES[$index_name]['size'][$i],
                            'is_default' => $is_default,
                            'active' => $active,
                            'date_uploaded' => date('Y-m-d H:i:s')
                        ];
                        $CI->db->insert(db_prefix() . 'ella_contractor_media', $data);
                        $uploaded_files[] = $CI->db->insert_id();
                    }
                }
            }
        }
        
        return $uploaded_files;
    }
}
