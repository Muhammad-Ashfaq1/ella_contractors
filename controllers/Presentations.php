<?php defined('BASEPATH') or exit('No direct script access allowed');

class Presentations extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_contractors/ella_media_model');
        $this->load->helper('ella_contractors/ella_media');
    }

    /**
     * Main presentations management page
     */
    public function index() {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        $data['title'] = 'Presentations';
        $this->load->view('ella_contractors/presentations', $data);
    }
    
    /**
     * DataTable server-side processing
     */
    public function table() {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('ella_contractors', 'admin/tables/ella_presentations'));
    }

    /**
     * Upload presentation file (AJAX)
     */
    public function upload() {
        if (!has_permission('ella_contractors', '', 'create')) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }
        
        $presentation_name = trim($this->input->post('presentation_name'));
        $description = $this->input->post('description');
        
        // Validate presentation name is provided
        if (empty($presentation_name)) {
            echo json_encode([
                'success' => false,
                'message' => 'Presentation name is required'
            ]);
            return;
        }

        // Handle multiple file uploads - check for presentation_files[] array or fallback to single file
        $files_key = null;
        if (isset($_FILES['presentation_files']) && !empty($_FILES['presentation_files']['name'])) {
            $files_key = 'presentation_files';
        } elseif (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
            // Fallback to old single file upload for backward compatibility
            $files_key = 'file';
        }
        
        if (!$files_key) {
            echo json_encode([
                'success' => false,
                'message' => 'No file selected for upload'
            ]);
            return;
        }

        // Convert single file to array format for uniform processing
        if (!is_array($_FILES[$files_key]['name'])) {
            $_FILES[$files_key]['name'] = [$_FILES[$files_key]['name']];
            $_FILES[$files_key]['type'] = [$_FILES[$files_key]['type']];
            $_FILES[$files_key]['tmp_name'] = [$_FILES[$files_key]['tmp_name']];
            $_FILES[$files_key]['error'] = [$_FILES[$files_key]['error']];
            $_FILES[$files_key]['size'] = [$_FILES[$files_key]['size']];
        }

        $uploaded_files = [];
        $errors = [];
        $max_size = 50 * 1024 * 1024; // 50MB
        $allowed_extensions = ['pdf', 'ppt', 'pptx', 'html'];

        // Process each file
        for ($i = 0; $i < count($_FILES[$files_key]['name']); $i++) {
            // Skip if no file or error uploading
            if (empty($_FILES[$files_key]['tmp_name'][$i]) || $_FILES[$files_key]['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload error for: ' . ($_FILES[$files_key]['name'][$i] ?? 'unknown');
                continue;
            }

            $file_name = $_FILES[$files_key]['name'][$i];
            $file_size = $_FILES[$files_key]['size'][$i];
            
            // Check file size
            if ($file_size > $max_size) {
                $errors[] = 'File "' . $file_name . '" exceeds maximum size of 50MB';
                continue;
            }

            // Check file type
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowed_extensions)) {
                $errors[] = 'Invalid file type for "' . $file_name . '". Only PDF, PPT, PPTX, and HTML allowed.';
                continue;
            }

            // Create temporary $_FILES array for single file processing
            $_FILES['temp_file'] = [
                'name' => $_FILES[$files_key]['name'][$i],
                'type' => $_FILES[$files_key]['type'][$i],
                'tmp_name' => $_FILES[$files_key]['tmp_name'][$i],
                'error' => $_FILES[$files_key]['error'][$i],
                'size' => $_FILES[$files_key]['size'][$i]
            ];

            // Upload as presentation with rel_type = 'presentation'
            // Pass the user's custom name to be stored in database
            $uploaded = handle_ella_media_upload($presentation_name, $description, 'temp_file', 'presentation', null);

            if ($uploaded && !empty($uploaded)) {
                foreach ($uploaded as $id) {
                    $uploaded_files[] = [
                        'id' => $id,
                        'name' => $presentation_name
                    ];
                    
                    // Log activity
                    log_activity('Presentation Uploaded [ID: ' . $id . ', Name: ' . $presentation_name . ', File: ' . $file_name . ']');
                }
            } else {
                $errors[] = 'Failed to upload "' . $file_name . '"';
            }
            
            // Clean up temp file entry
            unset($_FILES['temp_file']);
        }

        // Return response
        if (count($uploaded_files) > 0) {
            $message = 'Presentations uploaded successfully';
            if (count($errors) > 0) {
                $message .= ' (some files failed)';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'uploaded' => $uploaded_files,
                'errors' => $errors
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload presentations. ' . (count($errors) > 0 ? implode(', ', $errors) : 'Unknown error'),
                'errors' => $errors
            ]);
        }
    }

    /**
     * Get PDF preview for presentation file
     * Converts PPT/PPTX to PDF if needed
     */
    public function get_preview_pdf($id) {
        $file = $this->ella_media_model->get_file($id);
        if (!$file) {
            show_404();
        }

        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        
        // If it's already a PDF, serve it directly
        if ($ext === 'pdf') {
            $file_path = FCPATH . 'uploads/ella_presentations/' . $file->file_name;
            
            if (file_exists($file_path)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $file->original_name . '"');
                readfile($file_path);
                exit;
            }
        }
        
        // For PPT/PPTX files, convert to PDF for preview
        if (in_array($ext, ['ppt', 'pptx'])) {
            $this->convert_ppt_to_pdf($file);
        } else {
            show_404();
        }
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Convert PPT/PPTX to PDF for preview
     */
    private function convert_ppt_to_pdf($file) {
        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        $original_path = FCPATH . 'uploads/ella_presentations/' . $file->file_name;
        
        // Create a cache directory for converted PDFs
        $cache_dir = FCPATH . 'uploads/ella_presentations/cache/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        // Generate cache filename
        $cache_filename = 'preview_' . $file->id . '_' . md5($file->file_name . $file->date_uploaded) . '.pdf';
        $cache_path = $cache_dir . $cache_filename;
        
        // Check if cached PDF exists and is newer than original file
        if (file_exists($cache_path) && filemtime($cache_path) > filemtime($original_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . pathinfo($file->original_name, PATHINFO_FILENAME) . '.pdf"');
            readfile($cache_path);
            exit;
        }
        
        // Try to convert using LibreOffice (if available)
        if ($this->convert_with_libreoffice($original_path, $cache_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . pathinfo($file->original_name, PATHINFO_FILENAME) . '.pdf"');
            readfile($cache_path);
            exit;
        }
        
        // Try alternative conversion methods
        if ($this->convert_with_alternative_method($original_path, $cache_path, $ext)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . pathinfo($file->original_name, PATHINFO_FILENAME) . '.pdf"');
            readfile($cache_path);
            exit;
        }
        
        // Fallback: Show error message
        $this->show_conversion_error($file);
    }

    /**
     * Convert file using LibreOffice
     */
    private function convert_with_libreoffice($input_path, $output_path) {
        // Check if LibreOffice is available
        $libreoffice_path = $this->find_libreoffice();
        if (!$libreoffice_path) {
            return false;
        }
        
        // Create output directory
        $output_dir = dirname($output_path);
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0755, true);
        }
        
        // Convert using LibreOffice
        $command = escapeshellarg($libreoffice_path) . 
                  ' --headless --convert-to pdf --outdir ' . escapeshellarg($output_dir) . 
                  ' ' . escapeshellarg($input_path) . ' 2>&1';
        
        $output = [];
        $return_code = 0;
        exec($command, $output, $return_code);
        
        // Check if conversion was successful
        if ($return_code === 0 && file_exists($output_path)) {
            return true;
        }
        
        log_message('error', 'LibreOffice conversion failed: ' . implode("\n", $output));
        return false;
    }

    /**
     * Find LibreOffice executable
     */
    private function find_libreoffice() {
        $possible_paths = [
            '/usr/bin/libreoffice',
            '/usr/local/bin/libreoffice',
            '/opt/libreoffice/program/soffice',
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
            'libreoffice', // Try PATH
        ];
        
        foreach ($possible_paths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }
        
        return false;
    }

    /**
     * Alternative conversion method - creates fallback PDF
     */
    private function convert_with_alternative_method($input_path, $output_path, $ext) {
        // Create a simple fallback PDF with file information
        $this->create_fallback_pdf($input_path, $output_path, $ext);
        return true;
    }

    /**
     * Create fallback PDF when LibreOffice is not available
     */
    private function create_fallback_pdf($input_path, $output_path, $ext) {
        $filename = pathinfo($input_path, PATHINFO_FILENAME);
        $file_size = file_exists($input_path) ? filesize($input_path) : 0;
        $file_size_formatted = $this->format_bytes($file_size);
        
        // Create a simple text-based PDF
        $pdf_content = "%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
/Resources <<
/Font <<
/F1 5 0 R
>>
>>
>>
endobj

4 0 obj
<<
/Length 200
>>
stream
BT
/F1 12 Tf
72 720 Td
(PowerPoint Preview: " . $filename . ") Tj
0 -20 Td
(File Type: " . strtoupper($ext) . ") Tj
0 -20 Td
(File Size: " . $file_size_formatted . ") Tj
0 -40 Td
(This is a preview. Download the original file) Tj
0 -20 Td
(for full presentation with animations.) Tj
ET
endstream
endobj

5 0 obj
<<
/Type /Font
/Subtype /Type1
/BaseFont /Helvetica
>>
endobj

xref
0 6
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000274 00000 n 
0000000525 00000 n 
trailer
<<
/Size 6
/Root 1 0 R
>>
startxref
625
%%EOF";

        file_put_contents($output_path, $pdf_content);
    }

    /**
     * Format bytes to human readable format
     */
    private function format_bytes($bytes, $decimals = 2) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $dm = $decimals < 0 ? 0 : $decimals;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }

    /**
     * Show conversion error page
     */
    private function show_conversion_error($file) {
        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        $original_url = site_url('uploads/ella_presentations/' . $file->file_name);
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Preview Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .error-container { text-align: center; max-width: 500px; margin: 0 auto; }
        .error-icon { font-size: 48px; color: #e74c3c; margin-bottom: 20px; }
        .error-title { font-size: 24px; color: #2c3e50; margin-bottom: 15px; }
        .error-message { color: #7f8c8d; margin-bottom: 20px; }
        .download-btn { 
            background: #3498db; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <div class="error-title">Preview Not Available</div>
        <div class="error-message">
            Unable to convert ' . strtoupper($ext) . ' file to PDF for preview.<br>
            This may be due to server configuration or missing conversion tools.
        </div>
        <a href="' . $original_url . '" class="download-btn" download>
            📥 Download Original File
        </a>
    </div>
</body>
</html>';
        exit;
    }
    
    /**
     * Get all presentations (AJAX)
     * Used by appointment view to populate presentation selection modal
     */
    public function get_all() {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }
        
        // Get all presentations
        $this->db->select('id, file_name, original_name, file_type, file_size, date_uploaded');
        $this->db->from(db_prefix() . 'ella_contractor_media');
        $this->db->where('rel_type', 'presentation');
        $this->db->order_by('date_uploaded', 'DESC');
        
        $presentations = $this->db->get()->result_array();

        if (!empty($presentations)) {
            foreach ($presentations as &$presentation) {
                $public_url = site_url('uploads/ella_presentations/' . $presentation['file_name']);
                $public_url = str_replace('http://', 'https://', $public_url);
                $presentation['public_url'] = $public_url;
            }
            unset($presentation);
        }
        
        echo json_encode([
            'success' => true,
            'data' => $presentations
        ]);
    }

    /**
     * Delete presentation (AJAX)
     */
    public function delete() {
        if (!has_permission('ella_contractors', '', 'delete')) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }
        
        $presentation_id = $this->input->post('id');
        
        if (!$presentation_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Presentation ID is required'
            ]);
            return;
        }
        
        // Get presentation details before deleting
        $presentation = $this->ella_media_model->get_file($presentation_id);
        
        if (!$presentation || $presentation->rel_type !== 'presentation') {
            echo json_encode([
                'success' => false,
                'message' => 'Presentation not found'
            ]);
            return;
        }
        
        // Delete physical file
        $file_path = FCPATH . 'uploads/ella_presentations/' . $presentation->file_name;
        
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
        
        // Delete from database
        $this->db->where('id', $presentation_id);
        $this->db->where('rel_type', 'presentation');
        $deleted = $this->db->delete(db_prefix() . 'ella_contractor_media');
        
        if ($deleted) {
            // Also remove from appointment links
            $this->db->where('presentation_id', $presentation_id);
            $this->db->delete(db_prefix() . 'ella_appointment_presentations');
            
            echo json_encode([
                'success' => true,
                'message' => 'Presentation deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete presentation'
            ]);
        }
    }
    
    /**
     * Update presentation name (AJAX inline edit)
     */
    public function update_name() {
        if (!has_permission('ella_contractors', '', 'edit')) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }
        
        $presentation_id = $this->input->post('id');
        $new_name = trim($this->input->post('name'));
        
        if (!$presentation_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Presentation ID is required'
            ]);
            return;
        }
        
        if (empty($new_name)) {
            echo json_encode([
                'success' => false,
                'message' => 'Name cannot be empty'
            ]);
            return;
        }
        
        // Verify presentation exists
        $presentation = $this->ella_media_model->get_file($presentation_id);
        
        if (!$presentation || $presentation->rel_type !== 'presentation') {
            echo json_encode([
                'success' => false,
                'message' => 'Presentation not found'
            ]);
            return;
        }
        
        // Update name in database
        $this->db->where('id', $presentation_id);
        $this->db->where('rel_type', 'presentation');
        $updated = $this->db->update(db_prefix() . 'ella_contractor_media', [
            'original_name' => $new_name
        ]);
        
        if ($updated) {
            echo json_encode([
                'success' => true,
                'message' => 'Name updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update name'
            ]);
        }
    }
    
    /**
     * Bulk delete presentations via AJAX
     */
    public function bulk_delete() {
        if (!has_permission('ella_contractors', '', 'delete')) {
            echo json_encode([
                'success' => false,
                'message' => 'Access denied'
            ]);
            return;
        }

        $ids = $this->input->post('ids');
        
        if (empty($ids) || !is_array($ids)) {
            echo json_encode([
                'success' => false,
                'message' => 'No presentations selected'
            ]);
            return;
        }

        $deleted_count = 0;
        $failed_count = 0;
        
        foreach ($ids as $id) {
            // Get presentation details before deleting
            $presentation = $this->ella_media_model->get_file($id);
            
            if ($presentation && $presentation->rel_type === 'presentation') {
                // Delete physical file
                $file_path = FCPATH . 'uploads/ella_presentations/' . $presentation->file_name;
                
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
                
                // Delete from database
                $this->db->where('id', $id);
                $this->db->where('rel_type', 'presentation');
                $deleted = $this->db->delete(db_prefix() . 'ella_contractor_media');
                
                if ($deleted) {
                    // Also remove from appointment links
                    $this->db->where('presentation_id', $id);
                    $this->db->delete(db_prefix() . 'ella_appointment_presentations');
                    $deleted_count++;
                } else {
                    $failed_count++;
                }
            } else {
                $failed_count++;
            }
        }

        $total = count($ids);
        
        if ($deleted_count > 0) {
            $message = $deleted_count . ' presentation(s) deleted successfully';
            if ($failed_count > 0) {
                $message .= ' (' . $failed_count . ' failed)';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deleted_count,
                'failed_count' => $failed_count,
                'total' => $total
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete presentations'
            ]);
        }
    }

    /**
     * Check tutorial status for current user
     * Returns whether tutorial should be shown
     * 
     * @return json
     */
    public function check_tutorial_status()
    {
        if (!is_staff_logged_in()) {
            echo json_encode(['show_tutorial' => false]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Check user meta for tutorial dismissal
        if (!function_exists('get_meta')) {
            $this->load->helper('user_meta');
        }
        
        $tutorial_dismissed = get_meta('staff', $staff_id, 'ella_contractors_presentation_tutorial_dismissed');
        
        $show_tutorial = empty($tutorial_dismissed) || $tutorial_dismissed != '1';
        
        echo json_encode([
            'show_tutorial' => $show_tutorial,
            'dismissed' => $tutorial_dismissed == '1'
        ]);
    }

    /**
     * Save tutorial preference (dismissed state)
     * 
     * @return json
     */
    public function save_tutorial_preference()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        $dismissed = $this->input->post('dismissed') ? 1 : 0;
        
        // Load user meta helper if not loaded
        if (!function_exists('update_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Save preference
        $result = update_meta('staff', $staff_id, 'ella_contractors_presentation_tutorial_dismissed', $dismissed);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Tutorial preference saved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save tutorial preference'
            ]);
        }
    }

    /**
     * Reset tutorial for current user (admin function)
     * Allows users to restart the tutorial
     * 
     * @return json
     */
    public function reset_tutorial()
    {
        if (!is_staff_logged_in()) {
            echo json_encode([
                'success' => false,
                'message' => 'Not authenticated'
            ]);
            return;
        }

        $staff_id = get_staff_user_id();
        
        // Load user meta helper if not loaded
        if (!function_exists('delete_meta')) {
            $this->load->helper('user_meta');
        }
        
        // Remove tutorial dismissal preference
        $result = delete_meta('staff', $staff_id, 'ella_contractors_presentation_tutorial_dismissed');
        
        echo json_encode([
            'success' => true,
            'message' => 'Tutorial reset successfully. Refresh the page to see it again.'
        ]);
    }
}

