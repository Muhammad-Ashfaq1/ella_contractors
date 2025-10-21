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
        $data['media'] = $this->ella_media_model->get_media();
        $this->load->view('ella_contractors/presentations', $data);
    }

    /**
     * Upload presentation file
     */
    public function upload() {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }
        
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

        $uploaded = handle_ella_media_upload($is_default, $active);

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
            $file_path = FCPATH . 'uploads/ella_presentations/' . 
                        ($file->is_default ? 'default/' : 'general/') . 
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

    // ==================== PRIVATE METHODS ====================

    /**
     * Convert PPT/PPTX to PDF for preview
     */
    private function convert_ppt_to_pdf($file) {
        $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
        $original_path = FCPATH . 'uploads/ella_presentations/' . 
                        ($file->is_default ? 'default/' : 'general/') . 
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
        $original_url = site_url('uploads/ella_presentations/' . 
                        ($file->is_default ? 'default/' : 'general/') . 
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
}

