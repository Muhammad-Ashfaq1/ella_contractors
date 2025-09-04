<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_media_model');
        $this->load->helper('ella_media');
    }
    
    /**
     * Main index method - redirects to admin dashboard
     */
    public function index() {
        redirect(admin_url());
    }

    public function presentations() {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        $this->load->model('leads_model');
        $data['title'] = 'Presentations';
        $data['folders'] = $this->ella_media_model->get_folders();
        $data['media'] = $this->ella_media_model->get_media();
        $data['leads'] = $this->leads_model->get();
        $this->load->view('ella_contractors/presentations', $data);
    }

    public function create_folder() {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Folder Name', 'required');
        $this->form_validation->set_rules('lead_id', 'Lead', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
            } else {
                $data = [
                'name' => $this->input->post('name'),
                'lead_id' => $this->input->post('lead_id') ?: null,
                'is_default' => 0, // Folders are never default
                'active' => 1      // Folders are always active
            ];
            $folder_id = $this->ella_media_model->create_folder($data);
            if ($folder_id) {
                set_alert('success', 'Folder created successfully');
                } else {
                set_alert('warning', 'Failed to create folder');
            }
        }
        redirect(admin_url('ella_contractors/presentations'));
    }

    public function upload_presentation($folder_id) {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }
        
        $lead_id = $this->input->post('lead_id') ?: null;
        $is_default = $this->input->post('is_default') ? 1 : 0;
        $active = $this->input->post('active') ? 1 : 0;
        $description = $this->input->post('description');

        // Check if file was uploaded
        if (!isset($_FILES['file']) || $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
            set_alert('warning', 'No file selected for upload');
            redirect(admin_url('ella_contractors/presentations'));
            return;
        }

        // Check file size
        $max_size = 50 * 1024 * 1024; // 50MB
        if ($_FILES['file']['size'] > $max_size) {
            set_alert('warning', 'File size exceeds maximum allowed size of 50MB');
            redirect(admin_url('ella_contractors/presentations'));
            return;
        }

        // Check file type
        $extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'ppt', 'pptx', 'html'];
        
        if (!in_array($extension, $allowed_extensions)) {
            set_alert('warning', 'Invalid file type. Only PDF, PPT, PPTX, and HTML files are allowed.');
            redirect(admin_url('ella_contractors/presentations'));
            return;
        }

        $uploaded = handle_ella_media_upload($folder_id, $lead_id, $is_default, $active);

        if ($uploaded && !empty($uploaded)) {
            // Update description if needed
            foreach ($uploaded as $id) {
                $this->db->where('id', $id);
                $this->db->update(db_prefix() . 'ella_contractor_media', ['description' => $description]);
            }
            set_alert('success', 'File uploaded successfully');
        } else {
            set_alert('warning', 'Failed to upload file. Please check the file format and try again.');
        }
        redirect(admin_url('ella_contractors/presentations'));
    }

    public function preview_file($id) {
        $file = $this->ella_media_model->get_file($id);
        if (!$file) {
            show_404();
        }
        $data['file'] = $file;
        $data['title'] = 'Preview ' . $file->original_name;
        $this->load->view('ella_contractors/preview', $data);
    }

    public function get_preview_pdf($id) {
        $file = $this->ella_media_model->get_file($id);
        if (!$file) {
            show_404();
        }

        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        
        // If it's already a PDF, serve it directly
        if ($ext === 'pdf') {
            $file_path = FCPATH . 'uploads/ella_presentations/' . 
                        ($file->is_default ? 'default/' : ($file->lead_id ? 'lead_' . $file->lead_id . '/' : 'general/')) . 
                        $file->file_name;
            
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

    private function convert_ppt_to_pdf($file) {
        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        $original_path = FCPATH . 'uploads/ella_presentations/' . 
                        ($file->is_default ? 'default/' : ($file->lead_id ? 'lead_' . $file->lead_id . '/' : 'general/')) . 
                        $file->file_name;
        
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

    private function convert_with_alternative_method($input_path, $output_path, $ext) {
        // For now, we'll create a simple fallback that shows a message
        // In the future, you could integrate with online conversion services
        // or other PHP libraries like PhpOffice/PhpPresentation
        
        // Create a simple PDF with a message
        $this->create_fallback_pdf($input_path, $output_path, $ext);
        return true;
    }

    private function create_fallback_pdf($input_path, $output_path, $ext) {
        // Create a simple PDF using basic HTML to PDF conversion
        // This is a fallback when LibreOffice is not available
        
        $filename = pathinfo($input_path, PATHINFO_FILENAME);
        $file_size = file_exists($input_path) ? filesize($input_path) : 0;
        $file_size_formatted = $this->format_bytes($file_size);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Preview: ' . htmlspecialchars($filename) . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px; 
            line-height: 1.6;
            color: #333;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #3498db; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
        }
        .content { 
            max-width: 600px; 
            margin: 0 auto; 
        }
        .info-box { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 5px; 
            padding: 20px; 
            margin: 20px 0;
        }
        .download-btn { 
            background: #3498db; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block;
            margin: 10px 5px;
        }
        .download-btn:hover { 
            background: #2980b9; 
        }
        .note { 
            background: #fff3cd; 
            border: 1px solid #ffeaa7; 
            border-radius: 5px; 
            padding: 15px; 
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 ' . htmlspecialchars($filename) . '</h1>
        <p>PowerPoint Presentation Preview</p>
    </div>
    
    <div class="content">
        <div class="info-box">
            <h3>📋 File Information</h3>
            <p><strong>File Name:</strong> ' . htmlspecialchars($filename) . '</p>
            <p><strong>File Type:</strong> ' . strtoupper($ext) . '</p>
            <p><strong>File Size:</strong> ' . $file_size_formatted . '</p>
            <p><strong>Status:</strong> Ready for Download</p>
        </div>
        
        <div class="note">
            <h4>ℹ️ Preview Information</h4>
            <p>This is a preview of your PowerPoint presentation. To view the full presentation with all animations and formatting, please download the original file.</p>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="' . site_url('uploads/ella_presentations/' . 
                (strpos($input_path, 'default/') !== false ? 'default/' : 
                 (strpos($input_path, 'lead_') !== false ? 'lead_' . basename(dirname($input_path)) . '/' : 'general/')) . 
                basename($input_path)) . '" class="download-btn" download>
                📥 Download Original ' . strtoupper($ext) . ' File
            </a>
        </div>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666;">
            <p><small>This preview was generated automatically. The original file format is preserved for download.</small></p>
        </div>
    </div>
</body>
</html>';

        // For now, we'll just create an HTML file as a fallback
        // In a production environment, you might want to use a proper HTML to PDF converter
        file_put_contents($output_path . '.html', $html);
        
        // Create a simple text-based PDF (very basic)
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

    private function format_bytes($bytes, $decimals = 2) {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $dm = $decimals < 0 ? 0 : $decimals;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), $dm) . ' ' . $sizes[$i];
    }

    private function show_conversion_error($file) {
        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        $original_url = site_url('uploads/ella_presentations/' . 
                        ($file->is_default ? 'default/' : ($file->lead_id ? 'lead_' . $file->lead_id . '/' : 'general/')) . 
                        $file->file_name);
        
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

    public function debug_upload() {
        if (!is_admin()) {
            access_denied();
        }
        
        echo "<h3>Upload Debug Information</h3>";
        
        // Check upload directories
        $base_path = FCPATH . 'uploads/ella_presentations/';
        $directories = [
            $base_path,
            $base_path . 'default/',
            $base_path . 'general/',
        ];
        
        echo "<h4>Directory Status:</h4>";
        foreach ($directories as $dir) {
            $exists = is_dir($dir);
            $writable = $exists ? is_writable($dir) : false;
            echo "<p><strong>" . $dir . "</strong>: " . 
                 ($exists ? "Exists" : "Does not exist") . " | " . 
                 ($writable ? "Writable" : "Not writable") . "</p>";
        }
        
        // Check PHP upload settings
        echo "<h4>PHP Upload Settings:</h4>";
        echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
        echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
        echo "<p>max_execution_time: " . ini_get('max_execution_time') . "</p>";
        echo "<p>memory_limit: " . ini_get('memory_limit') . "</p>";
        
        // Check allowed files
        echo "<h4>Allowed Files Setting:</h4>";
        echo "<p>" . get_option('allowed_files') . "</p>";
        
        // Check if helper function exists
        echo "<h4>Helper Function Status:</h4>";
        echo "<p>handle_ella_media_upload function exists: " . (function_exists('handle_ella_media_upload') ? 'Yes' : 'No') . "</p>";
        
        echo "<br><a href='" . admin_url('ella_contractors/presentations') . "'>Back to Presentations</a>";
    }
}