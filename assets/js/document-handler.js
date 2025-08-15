/**
 * Document Handler - Client-side document processing and management
 * Supports PDF generation, image processing, and file management
 */

class DocumentHandler {
    constructor() {
        this.supportedFormats = {
            images: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            documents: ['pdf', 'doc', 'docx', 'txt'],
            presentations: ['ppt', 'pptx'],
            spreadsheets: ['xls', 'xlsx', 'csv']
        };
        
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        this.init();
    }

    init() {
        this.loadExternalLibraries();
        this.setupEventListeners();
    }

    /**
     * Load external libraries for document processing
     */
    loadExternalLibraries() {
        // Load PDF.js for PDF viewing
        if (!window.pdfjsLib) {
            this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js', () => {
                if (window.pdfjsLib) {
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                }
            });
        }

        // Load jsPDF for client-side PDF generation
        if (!window.jsPDF) {
            this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
        }

        // Load FileSaver.js for file downloads
        if (!window.saveAs) {
            this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js');
        }

        // Load JSZip for archive creation
        if (!window.JSZip) {
            this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
        }

        // Load Chart.js for report generation
        if (!window.Chart) {
            this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js');
        }

        // Load html2canvas for screenshot functionality
        if (!window.html2canvas) {
            this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
        }
    }

    /**
     * Load external script
     */
    loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.onload = callback || null;
        document.head.appendChild(script);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Global document ready
        $(document).ready(() => {
            this.initDocumentFeatures();
        });
    }

    /**
     * Initialize document features
     */
    initDocumentFeatures() {
        this.initImageProcessing();
        this.initPDFGeneration();
        this.initFileCompression();
        this.initDocumentViewer();
    }

    /**
     * Initialize image processing features
     */
    initImageProcessing() {
        // Image compression and resizing
        window.compressImage = (file, maxWidth = 1200, quality = 0.8) => {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = () => {
                    // Calculate new dimensions
                    let { width, height } = img;
                    if (width > maxWidth) {
                        height = (height * maxWidth) / width;
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Draw and compress
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob(resolve, 'image/jpeg', quality);
                };

                img.src = URL.createObjectURL(file);
            });
        };

        // Image thumbnail generation
        window.generateThumbnail = (file, size = 150) => {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = () => {
                    canvas.width = size;
                    canvas.height = size;

                    // Calculate crop area for square thumbnail
                    const minDim = Math.min(img.width, img.height);
                    const sx = (img.width - minDim) / 2;
                    const sy = (img.height - minDim) / 2;

                    ctx.drawImage(img, sx, sy, minDim, minDim, 0, 0, size, size);
                    canvas.toBlob(resolve, 'image/jpeg', 0.8);
                };

                img.src = URL.createObjectURL(file);
            });
        };
    }

    /**
     * Initialize PDF generation features
     */
    initPDFGeneration() {
        // Generate contract PDF
        window.generateContractPDF = (contractData) => {
            if (!window.jsPDF) {
                console.error('jsPDF library not loaded');
                return;
            }

            const { jsPDF } = window.jsPDF;
            const doc = new jsPDF();

            // Header
            doc.setFontSize(20);
            doc.setFont(undefined, 'bold');
            doc.text('CONTRACT AGREEMENT', 20, 30);

            // Contract details
            doc.setFontSize(12);
            doc.setFont(undefined, 'normal');
            
            let yPos = 50;
            const lineHeight = 10;

            doc.text(`Contract Number: ${contractData.contract_number}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`Contractor: ${contractData.contractor_name}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`Contract Value: $${contractData.contract_value}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`Start Date: ${contractData.start_date}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`End Date: ${contractData.end_date}`, 20, yPos);
            yPos += lineHeight * 2;

            // Description
            doc.setFont(undefined, 'bold');
            doc.text('Description:', 20, yPos);
            yPos += lineHeight;
            
            doc.setFont(undefined, 'normal');
            const splitDescription = doc.splitTextToSize(contractData.description, 170);
            doc.text(splitDescription, 20, yPos);

            // Footer
            yPos = doc.internal.pageSize.height - 30;
            doc.setFontSize(10);
            doc.text('Generated by EllaContractors CRM', 20, yPos);
            doc.text(`Date: ${new Date().toLocaleDateString()}`, 140, yPos);

            // Save the PDF
            doc.save(`contract_${contractData.contract_number}.pdf`);
        };

        // Generate invoice PDF
        window.generateInvoicePDF = (invoiceData) => {
            if (!window.jsPDF) {
                console.error('jsPDF library not loaded');
                return;
            }

            const { jsPDF } = window.jsPDF;
            const doc = new jsPDF();

            // Header
            doc.setFontSize(24);
            doc.setFont(undefined, 'bold');
            doc.text('INVOICE', 20, 30);

            // Invoice details
            doc.setFontSize(12);
            doc.setFont(undefined, 'normal');
            
            let yPos = 50;
            const lineHeight = 10;

            doc.text(`Invoice Number: ${invoiceData.invoice_number}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`Contractor: ${invoiceData.contractor_name}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`Amount Due: $${invoiceData.amount}`, 20, yPos);
            yPos += lineHeight;
            
            doc.text(`Due Date: ${invoiceData.due_date}`, 20, yPos);
            yPos += lineHeight * 3;

            // Amount box
            doc.setFont(undefined, 'bold');
            doc.setFontSize(16);
            doc.rect(120, yPos - 5, 60, 20);
            doc.text(`Total: $${invoiceData.amount}`, 125, yPos + 5);

            // Save the PDF
            doc.save(`invoice_${invoiceData.invoice_number}.pdf`);
        };

        // Generate report PDF with charts
        window.generateReportPDF = (reportData) => {
            if (!window.jsPDF || !window.html2canvas) {
                console.error('Required libraries not loaded');
                return;
            }

            // Create temporary canvas for chart
            const canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 300;
            const ctx = canvas.getContext('2d');

            // Generate chart
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: reportData.labels,
                    datasets: [{
                        label: reportData.title,
                        data: reportData.data,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                    }]
                },
                options: {
                    responsive: false,
                    animation: false
                }
            });

            // Convert chart to image and add to PDF
            setTimeout(() => {
                const { jsPDF } = window.jsPDF;
                const doc = new jsPDF();

                doc.setFontSize(20);
                doc.text(reportData.title, 20, 30);

                const imgData = canvas.toDataURL('image/png');
                doc.addImage(imgData, 'PNG', 20, 50, 160, 120);

                doc.save(`${reportData.filename}.pdf`);
            }, 1000);
        };
    }

    /**
     * Initialize file compression
     */
    initFileCompression() {
        // Create ZIP archive of multiple files
        window.createArchive = (files, archiveName = 'documents.zip') => {
            if (!window.JSZip) {
                console.error('JSZip library not loaded');
                return;
            }

            const zip = new JSZip();

            // Add files to archive
            files.forEach((file, index) => {
                zip.file(file.name, file);
            });

            // Generate and download archive
            zip.generateAsync({ type: 'blob' }).then((content) => {
                if (window.saveAs) {
                    window.saveAs(content, archiveName);
                } else {
                    // Fallback download
                    const url = URL.createObjectURL(content);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = archiveName;
                    a.click();
                    URL.revokeObjectURL(url);
                }
            });
        };
    }

    /**
     * Initialize document viewer
     */
    initDocumentViewer() {
        // PDF viewer modal
        window.showPDFViewer = (pdfUrl, title = 'PDF Viewer') => {
            const modal = $(`
                <div class="modal fade" id="pdfViewerModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title">${title}</h4>
                            </div>
                            <div class="modal-body">
                                <div id="pdfViewer" style="width: 100%; height: 500px; overflow: auto;">
                                    <canvas id="pdfCanvas"></canvas>
                                </div>
                                <div class="pdf-controls text-center" style="margin-top: 10px;">
                                    <button class="btn btn-sm btn-default" onclick="previousPage()">
                                        <i class="fa fa-chevron-left"></i> Previous
                                    </button>
                                    <span id="pageInfo" style="margin: 0 15px;">Page 1 of 1</span>
                                    <button class="btn btn-sm btn-default" onclick="nextPage()">
                                        Next <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            $('body').append(modal);
            $('#pdfViewerModal').modal('show');

            // Load PDF
            if (window.pdfjsLib) {
                this.loadPDF(pdfUrl);
            }

            // Clean up modal on close
            $('#pdfViewerModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        };
    }

    /**
     * Load PDF using PDF.js
     */
    loadPDF(url) {
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        const scale = 1.5;
        const canvas = document.getElementById('pdfCanvas');
        const ctx = canvas.getContext('2d');

        // Render page
        const renderPage = (num) => {
            pageRendering = true;
            pdfDoc.getPage(num).then((page) => {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                const renderTask = page.render(renderContext);

                renderTask.promise.then(() => {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            // Update page info
            document.getElementById('pageInfo').textContent = `Page ${num} of ${pdfDoc.numPages}`;
        };

        // Queue render page
        const queueRenderPage = (num) => {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        };

        // Previous page
        window.previousPage = () => {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        };

        // Next page
        window.nextPage = () => {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        };

        // Load PDF document
        window.pdfjsLib.getDocument(url).promise.then((pdfDoc_) => {
            pdfDoc = pdfDoc_;
            renderPage(pageNum);
        });
    }

    /**
     * File validation
     */
    validateFile(file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const allFormats = [
            ...this.supportedFormats.images,
            ...this.supportedFormats.documents,
            ...this.supportedFormats.presentations,
            ...this.supportedFormats.spreadsheets
        ];

        if (!allFormats.includes(fileExtension)) {
            throw new Error(`Unsupported file format: ${fileExtension}`);
        }

        if (file.size > this.maxFileSize) {
            throw new Error(`File too large. Maximum size is ${this.maxFileSize / 1024 / 1024}MB`);
        }

        return true;
    }

    /**
     * Get file type category
     */
    getFileCategory(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        
        for (const [category, extensions] of Object.entries(this.supportedFormats)) {
            if (extensions.includes(extension)) {
                return category;
            }
        }
        
        return 'unknown';
    }

    /**
     * Format file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Create document from template
     */
    createDocumentFromTemplate(templateType, data) {
        switch (templateType) {
            case 'contract':
                return this.generateContractPDF(data);
            case 'invoice':
                return this.generateInvoicePDF(data);
            case 'report':
                return this.generateReportPDF(data);
            default:
                throw new Error('Unknown template type');
        }
    }
}

// Initialize document handler when page loads
$(document).ready(function() {
    window.documentHandler = new DocumentHandler();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DocumentHandler;
}
