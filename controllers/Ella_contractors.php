<?php defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors extends AdminController
{
    public function __construct() {
        parent::__construct();
        $this->load->model('ella_media_model');
        $this->load->model('ella_line_items_model');
        $this->load->model('ella_line_item_groups_model');
        $this->load->model('ella_estimates_model');
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

    // ==================== LINE ITEMS MANAGEMENT ====================

    /**
     * Service Items Management
     */
    public function line_items()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $data['title'] = 'Service Items Management';
        $data['groups'] = $this->ella_line_item_groups_model->get_groups();
        $data['unit_types'] = $this->ella_line_items_model->get_unit_types();
        $data['line_items'] = $this->ella_line_items_model->get_line_items();
        
        $this->load->view('ella_contractors/line_items', $data);
    }

    /**
     * Create Service Item
     */
    public function create_line_item()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('group_id', 'Group', 'required|numeric');
        $this->form_validation->set_rules('unit_type', 'Unit Type', 'required');
        $this->form_validation->set_rules('cost', 'Cost', 'numeric');
        $this->form_validation->set_rules('quantity', 'Quantity', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'cost' => $this->input->post('cost') ?: null,
                'quantity' => $this->input->post('quantity') ?: 1.00,
                'unit_type' => $this->input->post('unit_type'),
                'group_id' => $this->input->post('group_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_result = $this->handle_line_item_image_upload();
                if ($upload_result) {
                    $data['image'] = $upload_result;
                }
            }

            $line_item_id = $this->ella_line_items_model->create_line_item($data);
            if ($line_item_id) {
                set_alert('success', 'Line item created successfully');
            } else {
                set_alert('warning', 'Failed to create line item');
            }
        }
        redirect(admin_url('ella_contractors/line_items'));
    }

    /**
     * Update Line Item
     */
    public function update_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('group_id', 'Group', 'required|numeric');
        $this->form_validation->set_rules('unit_type', 'Unit Type', 'required');
        $this->form_validation->set_rules('cost', 'Cost', 'numeric');
        $this->form_validation->set_rules('quantity', 'Quantity', 'numeric');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'cost' => $this->input->post('cost') ?: null,
                'quantity' => $this->input->post('quantity') ?: 1.00,
                'unit_type' => $this->input->post('unit_type'),
                'group_id' => $this->input->post('group_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_result = $this->handle_line_item_image_upload();
                if ($upload_result) {
                    $data['image'] = $upload_result;
                }
            }

            if ($this->ella_line_items_model->update_line_item($id, $data)) {
                set_alert('success', 'Line item updated successfully');
            } else {
                set_alert('warning', 'Failed to update line item');
            }
        }
        redirect(admin_url('ella_contractors/line_items'));
    }

    /**
     * Delete Line Item
     */
    public function delete_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        if ($this->ella_line_items_model->delete_line_item($id)) {
            set_alert('success', 'Line item deleted successfully');
        } else {
            set_alert('warning', 'Failed to delete line item');
        }
        redirect(admin_url('ella_contractors/line_items'));
    }

    /**
     * Toggle Line Item Active Status
     */
    public function toggle_line_item_active($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        if ($this->ella_line_items_model->toggle_active($id)) {
            set_alert('success', 'Line item status updated successfully');
        } else {
            set_alert('warning', 'Failed to update line item status');
        }
        redirect(admin_url('ella_contractors/line_items'));
    }


    /**
     * Handle Line Item Image Upload
     */
    private function handle_line_item_image_upload()
    {
        $upload_path = FCPATH . 'uploads/ella_line_items/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        } else {
            log_message('error', 'Line item image upload failed: ' . $this->upload->display_errors());
            return false;
        }
    }

    /**
     * Get Line Item Data for AJAX
     */
    public function get_line_item_data($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $line_item = $this->ella_line_items_model->get_line_item($id);
        if ($line_item) {
            echo json_encode($line_item);
        } else {
            echo json_encode(['error' => 'Line item not found']);
        }
    }

    // Table method removed - using direct view rendering

    /**
     * Manage Line Item (Add/Edit) - AJAX
     */
    public function manage_line_item()
    {
        if (has_permission('ella_contractors', '', 'view')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                if ($data['itemid'] == '') {
                    if (!has_permission('ella_contractors', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id = $this->ella_line_items_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('line_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item' => $this->ella_line_items_model->get($id),
                    ]);
                } else {
                    if (!has_permission('ella_contractors', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->ella_line_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('line_item'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    /**
     * Add Group
     */
    public function add_group()
    {
        if ($this->input->post() && has_permission('ella_contractors', '', 'create')) {
            $this->ella_line_item_groups_model->add_group($this->input->post());
            set_alert('success', _l('added_successfully', _l('item_group')));
        }
    }

    /**
     * Update Group
     */
    public function update_group($id)
    {
        if ($this->input->post() && has_permission('ella_contractors', '', 'edit')) {
            $this->ella_line_item_groups_model->edit_group($this->input->post(), $id);
            set_alert('success', _l('updated_successfully', _l('item_group')));
        }
    }

    /**
     * Delete Group
     */
    public function delete_group($id)
    {
        if (has_permission('ella_contractors', '', 'delete')) {
            if ($this->ella_line_item_groups_model->delete_group($id)) {
                set_alert('success', _l('deleted', _l('item_group')));
            }
        }
        redirect(admin_url('ella_contractors/line_items?groups_modal=true'));
    }

    /**
     * Bulk Actions
     */
    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_line_items');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $has_permission_delete = has_permission('ella_contractors', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->ella_line_items_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    // ==================== ESTIMATES MANAGEMENT ====================

    /**
     * Estimates Management
     */
    public function estimates()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $this->load->model('clients_model');
        $data['clients'] = $this->clients_model->get();
        
        $this->load->model('leads_model');
        $data['leads'] = $this->leads_model->get();
        $data['line_items'] = $this->ella_line_items_model->get_line_items(null, true);

        // die(json_encode($data['line_items']));
        $data['title'] = 'Estimates Management';
        $data['estimates'] = $this->ella_estimates_model->get_estimates();
        $data['statuses'] = $this->ella_estimates_model->get_statuses();
        
        $this->load->view('ella_contractors/estimates', $data);
    }

    /**
     * Create Estimate
     */
    public function create_estimate()
    {
        if (!has_permission('ella_contractors', '', 'create')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('estimate_name', 'Estimate Name', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'estimate_name' => $this->input->post('estimate_name'),
                'description' => $this->input->post('description'),
                'client_id' => $this->input->post('client_id') ?: null,
                'lead_id' => $this->input->post('lead_id') ?: null,
                'status' => $this->input->post('status')
            ];

            $estimate_id = $this->ella_estimates_model->create_estimate($data);
            if ($estimate_id) {
                // Add line items if provided
                $line_items = $this->input->post('line_items');
                if ($line_items && is_array($line_items)) {
                    foreach ($line_items as $item) {
                        if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                            $this->ella_estimates_model->add_line_item_to_estimate(
                                $estimate_id,
                                $item['line_item_id'],
                                $item['quantity'],
                                $item['unit_price']
                            );
                        }
                    }
                }
                
                set_alert('success', 'Estimate created successfully');
                redirect(admin_url('ella_contractors/estimates'));
            } else {
                set_alert('warning', 'Failed to create estimate');
            }
        }
        redirect(admin_url('ella_contractors/estimates'));
    }

    /**
     * Update Estimate
     */
    public function update_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('estimate_name', 'Estimate Name', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {
            set_alert('warning', validation_errors());
        } else {
            $data = [
                'estimate_name' => $this->input->post('estimate_name'),
                'description' => $this->input->post('description'),
                'client_id' => $this->input->post('client_id') ?: null,
                'lead_id' => $this->input->post('lead_id') ?: null,
                'status' => $this->input->post('status')
            ];

            if ($this->ella_estimates_model->update_estimate($id, $data)) {
                set_alert('success', 'Estimate updated successfully');
            } else {
                set_alert('warning', 'Failed to update estimate');
            }
        }
        redirect(admin_url('ella_contractors/estimates'));
    }

    /**
     * Delete Estimate
     */
    public function delete_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'delete')) {
            access_denied('ella_contractors');
        }

        if ($this->ella_estimates_model->delete_estimate($id)) {
            set_alert('success', 'Estimate deleted successfully');
        } else {
            set_alert('warning', 'Failed to delete estimate');
        }
        redirect(admin_url('ella_contractors/estimates'));
    }

    /**
     * View Estimate Details
     */
    public function view_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $data['title'] = 'Estimate Details';
        $data['estimate'] = $this->ella_estimates_model->get_estimate($id);
        $data['estimate_line_items'] = $this->ella_estimates_model->get_estimate_line_items($id);
        $data['line_items'] = $this->ella_line_items_model->get_line_items(null, true);
        $data['statuses'] = $this->ella_estimates_model->get_statuses();
        
        if (!$data['estimate']) {
            show_404();
        }

        $this->load->view('ella_contractors/view_estimate', $data);
    }

    /**
     * Add Line Item to Estimate
     */
    public function add_line_item_to_estimate()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $estimate_id = $this->input->post('estimate_id');
        $line_items = $this->input->post('line_items');

        $added_count = 0;
        if ($line_items && is_array($line_items)) {
            foreach ($line_items as $item) {
                if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                    if ($this->ella_estimates_model->add_line_item_to_estimate(
                        $estimate_id,
                        $item['line_item_id'],
                        $item['quantity'],
                        $item['unit_price']
                    )) {
                        $added_count++;
                    }
                }
            }
        }

        if ($added_count > 0) {
            // Update totals
            $this->ella_estimates_model->update_estimate_totals($estimate_id);
            set_alert('success', $added_count . ' line item(s) added to estimate successfully');
        } else {
            set_alert('warning', 'Failed to add line items to estimate');
        }

        redirect(admin_url('ella_contractors/view_estimate/' . $estimate_id));
    }

    /**
     * Update Line Item in Estimate
     */
    public function update_estimate_line_item()
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        $id = $this->input->post('id');
        $quantity = $this->input->post('quantity');
        $unit_price = $this->input->post('unit_price');

        if ($this->ella_estimates_model->update_estimate_line_item($id, $quantity, $unit_price)) {
            set_alert('success', 'Line item updated successfully');
        } else {
            set_alert('warning', 'Failed to update line item');
        }

        $estimate_id = $this->input->post('estimate_id');
        redirect(admin_url('ella_contractors/view_estimate/' . $estimate_id));
    }

    /**
     * Remove Line Item from Estimate
     */
    public function remove_estimate_line_item($id)
    {
        if (!has_permission('ella_contractors', '', 'edit')) {
            access_denied('ella_contractors');
        }

        // Get estimate_id before deletion
        $this->db->select('estimate_id');
        $this->db->where('id', $id);
        $result = $this->db->get(db_prefix() . 'ella_contractor_estimate_line_items')->row();

        if ($this->ella_estimates_model->remove_line_item_from_estimate($id)) {
            set_alert('success', 'Line item removed from estimate successfully');
        } else {
            set_alert('warning', 'Failed to remove line item from estimate');
        }

        if ($result) {
            redirect(admin_url('ella_contractors/view_estimate/' . $result->estimate_id));
        } else {
            redirect(admin_url('ella_contractors/estimates'));
        }
    }

    /**
     * Get Line Item Data for AJAX
     */
    public function get_line_item_for_estimate($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $line_item = $this->ella_line_items_model->get_line_item($id);
        if ($line_item) {
            echo json_encode($line_item);
        } else {
            echo json_encode(['error' => 'Line item not found']);
        }
    }

    /**
     * Manage Estimate (Add/Edit) - AJAX
     */
    public function manage_estimate()
    {
        if (has_permission('ella_contractors', '', 'view')) {
            if ($this->input->post()) {
                try {
                    $data = $this->input->post();
                    
                    // Debug logging
                    log_message('debug', 'Manage estimate data: ' . json_encode($data));
                if ($data['estimate_id'] == '') {
                    if (!has_permission('ella_contractors', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    
                    // Remove line_items from main data
                    $line_items = isset($data['line_items']) ? $data['line_items'] : [];
                    unset($data['line_items']);
                    
                    $id = $this->ella_estimates_model->create_estimate($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        // Add line items if provided
                        if ($line_items && is_array($line_items)) {
                            foreach ($line_items as $item) {
                                if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                                    $this->ella_estimates_model->add_line_item_to_estimate(
                                        $id,
                                        $item['line_item_id'],
                                        $item['quantity'],
                                        $item['unit_price']
                                    );
                                }
                            }
                        }
                        
                        $success = true;
                        $message = _l('added_successfully', _l('estimate'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'estimate' => $this->ella_estimates_model->get_estimate($id),
                    ]);
                } else {
                    if (!has_permission('ella_contractors', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    
                    // Extract line_items before removing from main data
                    $line_items = isset($data['line_items']) ? $data['line_items'] : [];
                    unset($data['line_items']);
                    
                    $success = $this->ella_estimates_model->update_estimate($data['estimate_id'], $data);
                    $message = '';
                    if ($success) {
                        // Delete existing line items
                        $this->db->where('estimate_id', $data['estimate_id']);
                        $this->db->delete(db_prefix() . 'ella_contractor_estimate_line_items');
                        
                        // Add posted line items
                        if (is_array($line_items)) {
                            foreach ($line_items as $item) {
                                if (!empty($item['line_item_id']) && !empty($item['quantity']) && !empty($item['unit_price'])) {
                                    $this->ella_estimates_model->add_line_item_to_estimate(
                                        $data['estimate_id'],
                                        $item['line_item_id'],
                                        $item['quantity'],
                                        $item['unit_price']
                                    );
                                }
                            }
                        }
                        
                        // Update totals
                        $this->ella_estimates_model->update_estimate_totals($data['estimate_id']);
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'debug' => [
                            'estimate_id' => $data['estimate_id'],
                            'line_items_count' => count($line_items)
                        ]
                    ]);
                }
                } catch (Exception $e) {
                    log_message('error', 'Estimate management error: ' . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Get Estimate Data for AJAX
     */
    public function get_estimate_data($id)
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }

        $estimate = (array) $this->ella_estimates_model->get_estimate($id);
        $estimate['line_items'] = $this->ella_estimates_model->get_estimate_line_items($id);
        echo json_encode($estimate);
    }
    
    /**
     * DataTable server-side processing for estimates
     */
    public function estimates_table()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('ella_contractor_estimates');
    }

    /**
     * Get Estimates Data for AJAX
     */
    public function get_estimates_ajax()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            access_denied('ella_contractors');
        }
        
        $estimates = $this->ella_estimates_model->get_estimates();
        echo json_encode($estimates);
    }

    /**
     * Estimates Bulk Actions
     */
    public function estimates_bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_estimates');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids = $this->input->post('ids');
            $has_permission_delete = has_permission('ella_contractors', '', 'delete');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete) {
                            if ($this->ella_estimates_model->delete_estimate($id)) {
                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }

    public function get_line_items_ajax()
    {
        if (!has_permission('ella_contractors', '', 'view')) {
            ajax_access_denied();
        }
        
        $line_items = $this->ella_line_items_model->get_line_items(null, true);        
        // Debug logging
        log_message('debug', 'Line items count: ' . count($line_items));
        if (!empty($line_items)) {
            log_message('debug', 'First line item: ' . json_encode($line_items[0]));
        }
        
        $data = [];
        foreach($line_items as $item) {
            $data[] = [
                'id' => $item['id'],
                'name' => htmlspecialchars($item['name']) . ' - $' . number_format($item['cost'], 2),
                'cost' => $item['cost'],
                'unit_price' => $item['cost'],
                'description' => $item['description'] ?? '',
                'unit_type' => $item['unit_type'] ?? ''
            ];
        }
        
        // Debug logging
        log_message('debug', 'Data count: ' . count($data));
        if (!empty($data)) {
            log_message('debug', 'First data item: ' . json_encode($data[0]));
        }
        
        // Return in the format expected by the sample function
        $response = [
            'success' => true,
            'data' => $data,
            'message' => 'Line items loaded successfully'
        ];
        
        // Set proper content type
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    // Test endpoint to debug line items
    public function test_line_items()
    {
        if (!is_admin()) {
            access_denied();
        }
        
        $line_items = $this->ella_line_items_model->get_line_items(null, true);
        
        echo "<h3>Line Items Debug</h3>";
        echo "<p>Count: " . count($line_items) . "</p>";
        echo "<pre>";
        print_r($line_items);
        echo "</pre>";
        
        $options = [];
        foreach($line_items as $item) {
            $options[] = [
                'value' => $item['id'],
                'text' => htmlspecialchars($item['name']) . ' - $' . number_format($item['cost'], 2),
                'cost' => $item['cost']
            ];
        }
        
        echo "<h3>Options Array</h3>";
        echo "<pre>";
        print_r($options);
        echo "</pre>";
        
        echo "<h3>JSON Output</h3>";
        echo "<pre>";
        echo json_encode($options, JSON_PRETTY_PRINT);
        echo "</pre>";
    }

}