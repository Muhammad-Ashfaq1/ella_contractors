<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentManager {
    
    private $upload_path;
    private $temp_path;
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->upload_path = FCPATH . 'modules/ella_contractors/uploads/contractors/documents/';
        $this->temp_path = FCPATH . 'modules/ella_contractors/uploads/contractors/temp/';
        
        // Ensure directories exist
        if (!is_dir($this->upload_path)) {
            mkdir($this->upload_path, 0755, true);
        }
        if (!is_dir($this->temp_path)) {
            mkdir($this->temp_path, 0755, true);
        }
    }
    
    /**
     * Upload a file and return document data
     */
    public function uploadFile($file_data, $contractor_id) {
        $this->ci->load->library('upload');
        
        $config['upload_path'] = $this->upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|ppt|pptx|txt';
        $config['max_size'] = 10240; // 10MB
        $config['file_name'] = time() . '_' . $file_data['name'];
        
        $this->ci->upload->initialize($config);
        
        if ($this->ci->upload->do_upload('document_file')) {
            $upload_data = $this->ci->upload->data();
            
            return [
                'id' => uniqid(),
                'contractor_id' => $contractor_id,
                'filename' => $upload_data['file_name'],
                'original_name' => $file_data['name'],
                'file_type' => $upload_data['file_type'],
                'file_size' => $upload_data['file_size'],
                'file_path' => $upload_data['full_path'],
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ];
        }
        
        return false;
    }
    
    /**
     * Get document gallery for a contractor
     */
    public function getDocumentGallery($contractor_id) {
        // In real implementation, this would query the database
        // For now, return dummy data
        return [
            [
                'id' => 'doc_001',
                'contractor_id' => $contractor_id,
                'filename' => 'contract_agreement.pdf',
                'original_name' => 'Contract Agreement',
                'file_type' => 'application/pdf',
                'file_size' => '245760',
                'upload_date' => '2024-01-15 10:30:00',
                'status' => 'active'
            ],
            [
                'id' => 'doc_002',
                'contractor_id' => $contractor_id,
                'filename' => 'project_photos.jpg',
                'original_name' => 'Project Photos',
                'file_type' => 'image/jpeg',
                'file_size' => '1024000',
                'upload_date' => '2024-01-14 15:45:00',
                'status' => 'active'
            ],
            [
                'id' => 'doc_003',
                'contractor_id' => $contractor_id,
                'filename' => 'invoice_template.xlsx',
                'original_name' => 'Invoice Template',
                'file_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'file_size' => '51200',
                'upload_date' => '2024-01-13 09:15:00',
                'status' => 'active'
            ]
        ];
    }
    
    /**
     * Generate share link for a document
     */
    public function generateShareLink($document_id) {
        $token = bin2hex(random_bytes(16));
        $share_url = base_url("ella_contractors/documents/shared/{$token}");
        
        return [
            'token' => $token,
            'url' => $share_url,
            'expires' => date('Y-m-d H:i:s', strtotime('+7 days'))
        ];
    }
    
    /**
     * Generate contract PDF using TCPDF
     */
    public function generateContractPDF($contract_data) {
        if (class_exists('TCPDF')) {
            return $this->generateTCPDF($contract_data, 'contract');
        } else {
            return $this->generateSimplePDF($contract_data, 'contract');
        }
    }
    
    /**
     * Generate invoice PDF using TCPDF
     */
    public function generateInvoicePDF($invoice_data) {
        if (class_exists('TCPDF')) {
            return $this->generateTCPDF($invoice_data, 'invoice');
        } else {
            return $this->generateSimplePDF($invoice_data, 'invoice');
        }
    }
    
    /**
     * Generate report PDF using TCPDF
     */
    public function generateReportPDF($report_data, $report_type) {
        if (class_exists('TCPDF')) {
            return $this->generateTCPDF($report_data, 'report', $report_type);
        } else {
            return $this->generateSimplePDF($report_data, 'report', $report_type);
        }
    }
    
    /**
     * Generate contractor presentation using PhpPresentation
     */
    public function generateContractorPresentation($contractor_data) {
        if (class_exists('PhpOffice\PhpPresentation\PhpPresentation')) {
            return $this->generatePhpPresentation($contractor_data, 'contractor');
        } else {
            return $this->generateSimplePresentation($contractor_data, 'contractor');
        }
    }
    
    /**
     * Generate project presentation using PhpPresentation
     */
    public function generateProjectPresentation($project_data) {
        if (class_exists('PhpOffice\PhpPresentation\PhpPresentation')) {
            return $this->generatePhpPresentation($project_data, 'project');
        } else {
            return $this->generateSimplePresentation($project_data, 'project');
        }
    }
    
    /**
     * Generate PDF using TCPDF library
     */
    private function generateTCPDF($data, $type, $subtype = '') {
        try {
            // Create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator('Ella CRM');
            $pdf->SetAuthor('Ella Contractors Module');
            $pdf->SetTitle(ucfirst($type) . ' Document');
            
            // Set default header data
            $pdf->SetHeaderData('', 0, 'Ella CRM - ' . ucfirst($type), date('Y-m-d H:i:s'));
            
            // Set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            
            // Set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            
            // Set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            
            // Set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            
            // Set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            // Add a page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('helvetica', '', 12);
            
            // Generate content based on type
            $html = $this->getPDFHTML($data, $type, $subtype);
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Generate filename
            $filename = $type . '_' . time() . '.pdf';
            $filepath = $this->temp_path . $filename;
            
            // Output PDF to file
            $pdf->Output($filepath, 'F');
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'type' => 'pdf',
                'size' => filesize($filepath)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'TCPDF Error: ' . $e->getMessage(),
                'fallback' => $this->generateSimplePDF($data, $type, $subtype)
            ];
        }
    }
    
    /**
     * Generate PDF using simple HTML fallback
     */
    private function generateSimplePDF($data, $type, $subtype = '') {
        $html = $this->getPDFHTML($data, $type, $subtype);
        $filename = $type . '_' . time() . '.html';
        $filepath = $this->temp_path . $filename;
        
        file_put_contents($filepath, $html);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'type' => 'html',
            'size' => filesize($filepath),
            'note' => 'Generated as HTML (TCPDF not available)'
        ];
    }
    
    /**
     * Generate presentation using PhpPresentation library
     */
    private function generatePhpPresentation($data, $type) {
        try {
            // Create new presentation
            $presentation = new \PhpOffice\PhpPresentation\PhpPresentation();
            
            // Set document properties
            $presentation->getDocumentProperties()
                ->setCreator('Ella CRM')
                ->setLastModifiedBy('Ella Contractors Module')
                ->setTitle(ucfirst($type) . ' Presentation')
                ->setSubject(ucfirst($type) . ' Information')
                ->setDescription('Generated by Ella CRM Contractors Module');
            
            // Create slide
            $slide = $presentation->getActiveSlide();
            
            // Add title
            $shape = $slide->createRichTextShape()
                ->setHeight(50)
                ->setWidth(600)
                ->setOffsetX(10)
                ->setOffsetY(10);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(\PhpOffice\PhpPresentation\Style\Alignment::HORIZONTAL_CENTER);
            $textRun = $shape->createTextRun('Ella CRM - ' . ucfirst($type) . ' Presentation');
            $textRun->getFont()->setBold(true)->setSize(18);
            
            // Add content based on type
            $this->addPresentationContent($slide, $data, $type);
            
            // Generate filename
            $filename = $type . '_presentation_' . time() . '.pptx';
            $filepath = $this->temp_path . $filename;
            
            // Save presentation
            $writer = \PhpOffice\PhpPresentation\IOFactory::createWriter($presentation, 'PowerPoint2007');
            $writer->save($filepath);
            
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'type' => 'pptx',
                'size' => filesize($filepath)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'PhpPresentation Error: ' . $e->getMessage(),
                'fallback' => $this->generateSimplePresentation($data, $type)
            ];
        }
    }
    
    /**
     * Generate simple presentation as text file
     */
    private function generateSimplePresentation($data, $type) {
        $content = $this->getPresentationContent($data, $type);
        $filename = $type . '_presentation_' . time() . '.txt';
        $filepath = $this->temp_path . $filename;
        
        file_put_contents($filepath, $content);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'type' => 'txt',
            'size' => filesize($filepath),
            'note' => 'Generated as text (PhpPresentation not available)'
        ];
    }
    
    /**
     * Add content to presentation slide
     */
    private function addPresentationContent($slide, $data, $type) {
        $yOffset = 80;
        
        if ($type === 'contractor') {
            // Add contractor information
            $this->addTextToSlide($slide, 'Contractor Details', 10, $yOffset, 200, 20, true);
            $yOffset += 30;
            
            $this->addTextToSlide($slide, 'Name: ' . ($data['name'] ?? 'N/A'), 10, $yOffset, 400, 20);
            $yOffset += 25;
            
            $this->addTextToSlide($slide, 'Email: ' . ($data['email'] ?? 'N/A'), 10, $yOffset, 400, 20);
            $yOffset += 25;
            
            $this->addTextToSlide($slide, 'Phone: ' . ($data['phone'] ?? 'N/A'), 10, $yOffset, 400, 20);
            $yOffset += 25;
            
            $this->addTextToSlide($slide, 'Status: ' . ($data['status'] ?? 'N/A'), 10, $yOffset, 400, 20);
            
        } elseif ($type === 'project') {
            // Add project information
            $this->addTextToSlide($slide, 'Project Details', 10, $yOffset, 200, 20, true);
            $yOffset += 30;
            
            $this->addTextToSlide($slide, 'Project: ' . ($data['name'] ?? 'N/A'), 10, $yOffset, 400, 20);
            $yOffset += 25;
            
            $this->addTextToSlide($slide, 'Contractor: ' . ($data['contractor_name'] ?? 'N/A'), 10, $yOffset, 400, 20);
            $yOffset += 25;
            
            $this->addTextToSlide($slide, 'Start Date: ' . ($data['start_date'] ?? 'N/A'), 10, $yOffset, 400, 20);
            $yOffset += 25;
            
            $this->addTextToSlide($slide, 'Status: ' . ($data['status'] ?? 'N/A'), 10, $yOffset, 400, 20);
        }
    }
    
    /**
     * Helper method to add text to slide
     */
    private function addTextToSlide($slide, $text, $x, $y, $width, $height, $bold = false) {
        $shape = $slide->createRichTextShape()
            ->setHeight($height)
            ->setWidth($width)
            ->setOffsetX($x)
            ->setOffsetY($y);
        
        $textRun = $shape->createTextRun($text);
        if ($bold) {
            $textRun->getFont()->setBold(true)->setSize(14);
        } else {
            $textRun->getFont()->setSize(12);
        }
    }
    
    /**
     * Get HTML content for PDF generation
     */
    private function getPDFHTML($data, $type, $subtype = '') {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . ucfirst($type) . ' Document</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                .section { margin: 20px 0; }
                .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                .info-row { margin: 10px 0; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .value { display: inline-block; }
                .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>';
        
        if ($type === 'contract') {
            $html .= $this->getContractHTML($data);
        } elseif ($type === 'invoice') {
            $html .= $this->getInvoiceHTML($data);
        } elseif ($type === 'report') {
            $html .= $this->getReportHTML($data, $subtype);
        }
        
        $html .= '
            <div class="footer">
                <p>Generated by Ella CRM Contractors Module on ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Get contract HTML content
     */
    private function getContractHTML($data) {
        // Generate PDF content
        $html = '
        <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
            <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px;">
                <h1 style="color: #2c3e50; margin: 0;">CONTRACT AGREEMENT</h1>
                <p style="color: #7f8c8d; margin: 5px 0;">Contract #' . ($data['contract_number'] ?? 'N/A') . '</p>
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #34495e; border-bottom: 1px solid #bdc3c7; padding-bottom: 10px;">Contract Details</h2>
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa; font-weight: bold; width: 30%;">Contractor:</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . ($data['contractor_name'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa; font-weight: bold;">Project Title:</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . ($data['title'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa; font-weight: bold;">Start Date:</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . ($data['start_date'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa; font-weight: bold;">End Date:</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . ($data['end_date'] ?? 'N/A') . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa; font-weight: bold;">Contract Value:</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">$' . number_format($data['fixed_amount'] ?? 0, 2) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; background-color: #f8f9fa; font-weight: bold;">Status:</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . ($data['status'] ?? 'N/A') . '</td>
                    </tr>
                </table>
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #34495e; border-bottom: 1px solid #bdc3c7; padding-bottom: 10px;">Project Description</h2>
                <p style="line-height: 1.6; color: #2c3e50;">' . ($data['description'] ?? 'No description provided.') . '</p>
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #34495e; border-bottom: 1px solid #bdc3c7; padding-bottom: 10px;">Payment Terms</h2>
                <p style="line-height: 1.6; color: #2c3e50;">' . ($data['payment_terms'] ?? 'No payment terms specified.') . '</p>
            </div>
            
            <div style="margin-bottom: 30px;">
                <h2 style="color: #34495e; border-bottom: 1px solid #bdc3c7; padding-bottom: 10px;">Terms & Conditions</h2>
                <p style="line-height: 1.6; color: #2c3e50;">' . ($data['terms_conditions'] ?? 'Standard terms and conditions apply.') . '</p>
            </div>
            
            <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #333; text-align: center;">
                <p style="color: #7f8c8d; font-size: 12px;">Generated on ' . date('F j, Y \a\t g:i A') . '</p>
                <p style="color: #7f8c8d; font-size: 12px;">Ella Contractors CRM System</p>
            </div>
        </div>';
        return $html;
    }
    
    /**
     * Get invoice HTML content
     */
    private function getInvoiceHTML($data) {
        return '
            <div class="header">
                <h1>Invoice</h1>
                <p>Invoice #: ' . ($data['invoice_number'] ?? 'N/A') . '</p>
            </div>
            
            <div class="section">
                <h3>Invoice Details</h3>
                <div class="info-row">
                    <span class="label">Contractor:</span>
                    <span class="value">' . ($data['contractor_name'] ?? 'N/A') . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Project:</span>
                    <span class="value">' . ($data['title'] ?? 'N/A') . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Invoice Date:</span>
                    <span class="value">' . ($data['invoice_date'] ?? 'N/A') . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Due Date:</span>
                    <span class="value">' . ($data['due_date'] ?? 'N/A') . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Amount:</span>
                    <span class="value">$' . number_format(($data['amount'] ?? 0), 2) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="value">' . ($data['status'] ?? 'N/A') . '</span>
                </div>
            </div>
            
            <div class="section">
                <h3>Payment Instructions</h3>
                <p>Please make payment within the specified due date. Late payments may incur additional charges.</p>
            </div>';
    }
    
    /**
     * Get report HTML content
     */
    private function getReportHTML($data, $report_type) {
        $title = ucfirst($report_type) . ' Report';
        
        $html = '
            <div class="header">
                <h1>' . $title . '</h1>
                <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
            </div>';
        
        if ($report_type === 'daily') {
            $html .= $this->getDailyReportHTML($data);
        } elseif ($report_type === 'monthly') {
            $html .= $this->getMonthlyReportHTML($data);
        } else {
            $html .= $this->getGeneralReportHTML($data);
        }
        
        return $html;
    }
    
    /**
     * Get daily report HTML content
     */
    private function getDailyReportHTML($data) {
        return '
            <div class="section">
                <h3>Daily Summary</h3>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value">' . date('Y-m-d') . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Active Contracts:</span>
                    <span class="value">' . ($data['active_contracts'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">New Projects:</span>
                    <span class="value">' . ($data['new_projects'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Completed Tasks:</span>
                    <span class="value">' . ($data['completed_tasks'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Revenue:</span>
                    <span class="value">$' . number_format(($data['total_revenue'] ?? 0), 2) . '</span>
                </div>
            </div>';
    }
    
    /**
     * Get monthly report HTML content
     */
    private function getMonthlyReportHTML($data) {
        return '
            <div class="section">
                <h3>Monthly Summary</h3>
                <div class="info-row">
                    <span class="label">Month:</span>
                    <span class="value">' . date('F Y') . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Contracts:</span>
                    <span class="value">' . ($data['total_contracts'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Active Projects:</span>
                    <span class="value">' . ($data['active_projects'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Completed Projects:</span>
                    <span class="value">' . ($data['completed_projects'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Monthly Revenue:</span>
                    <span class="value">$' . number_format(($data['monthly_revenue'] ?? 0), 2) . '</span>
                </div>
            </div>';
    }
    
    /**
     * Get general report HTML content
     */
    private function getGeneralReportHTML($data) {
        return '
            <div class="section">
                <h3>General Statistics</h3>
                <div class="info-row">
                    <span class="label">Total Contractors:</span>
                    <span class="value">' . ($data['total_contractors'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Projects:</span>
                    <span class="value">' . ($data['total_projects'] ?? 0) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Revenue:</span>
                    <span class="value">$' . number_format(($data['total_revenue'] ?? 0), 2) . '</span>
                </div>
                <div class="info-row">
                    <span class="label">Average Project Value:</span>
                    <span class="value">$' . number_format(($data['avg_project_value'] ?? 0), 2) . '</span>
                </div>
            </div>';
    }
    
    /**
     * Get presentation content for text fallback
     */
    private function getPresentationContent($data, $type) {
        $content = "ELLA CRM - " . strtoupper($type) . " PRESENTATION\n";
        $content .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        
        if ($type === 'contractor') {
            $content .= "CONTRACTOR DETAILS:\n";
            $content .= "Name: " . ($data['name'] ?? 'N/A') . "\n";
            $content .= "Email: " . ($data['email'] ?? 'N/A') . "\n";
            $content .= "Phone: " . ($data['phone'] ?? 'N/A') . "\n";
            $content .= "Status: " . ($data['status'] ?? 'N/A') . "\n";
        } elseif ($type === 'project') {
            $content .= "PROJECT DETAILS:\n";
            $content .= "Project: " . ($data['name'] ?? 'N/A') . "\n";
            $content .= "Contractor: " . ($data['contractor_name'] ?? 'N/A') . "\n";
            $content .= "Start Date: " . ($data['start_date'] ?? 'N/A') . "\n";
            $content .= "Status: " . ($data['status'] ?? 'N/A') . "\n";
        }
        
        $content .= "\n\nNote: This is a text version. Install PhpPresentation for PowerPoint format.";
        
        return $content;
    }
}
