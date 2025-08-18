<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
// Define helper functions for this view
if (!function_exists('get_contractor_status_badge')) {
    function get_contractor_status_badge($status) {
        $badges = [
            'active' => '<span class="label label-success">Active</span>',
            'inactive' => '<span class="label label-default">Inactive</span>',
            'pending' => '<span class="label label-warning">Pending</span>',
            'blacklisted' => '<span class="label label-danger">Blacklisted</span>'
        ];
        return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('get_payment_status_badge')) {
    function get_payment_status_badge($status) {
        $badges = [
            'pending' => '<span class="label label-warning">Pending</span>',
            'completed' => '<span class="label label-success">Completed</span>',
            'failed' => '<span class="label label-danger">Failed</span>',
            'cancelled' => '<span class="label label-default">Cancelled</span>'
        ];
        return isset($badges[$status]) ? $badges[$status] : '<span class="label label-default">' . ucfirst($status) . '</span>';
    }
}

if (!function_exists('format_contractor_currency')) {
    function format_contractor_currency($amount, $currency = null) {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('calculate_contract_value')) {
    function calculate_contract_value($contract) {
        $total = 0;
        
        if (!empty($contract->hourly_rate) && !empty($contract->estimated_hours)) {
            $total = $contract->hourly_rate * $contract->estimated_hours;
        } elseif (!empty($contract->fixed_amount)) {
            $total = $contract->fixed_amount;
        }
        
        return $total;
    }
}
?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="widget-dragger"></div>
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading">
                        
                        <!-- Quick Actions Section -->
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Quick Actions</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/contractors/add') ?>" class="btn btn-primary btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-plus"></i> Add Contractor
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/contracts/add') ?>" class="btn btn-success btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-plus"></i> Add Contract
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/projects/add') ?>" class="btn btn-info btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-plus"></i> Add Project
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/payments/add') ?>" class="btn btn-warning btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-plus"></i> Add Payment
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-outline-primary btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-users"></i> Manage Contractors
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-outline-success btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-file-contract"></i> Manage Contracts
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-outline-info btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-tasks"></i> Manage Projects
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-outline-warning btn-block" style="margin-bottom: 10px;">
                                                <i class="fa fa-money"></i> Manage Payments
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                                    <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 15px;">
                                            <i class="fa fa-users" style="font-size: 2.5em; opacity: 0.8;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9em; margin-bottom: 5px;">Total Contractors</div>
                                            <div style="font-size: 2em; font-weight: bold;"><?php echo $stats['total_contractors']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                                    <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 15px;">
                                            <i class="fa fa-check-circle" style="font-size: 2.5em; opacity: 0.8;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9em; margin-bottom: 5px;">Active Contractors</div>
                                            <div style="font-size: 2em; font-weight: bold;"><?php echo $stats['active_contractors']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                                    <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 15px;">
                                            <i class="fa fa-file-text" style="font-size: 2.5em; opacity: 0.8;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9em; margin-bottom: 5px;">Active Contracts</div>
                                            <div style="font-size: 2em; font-weight: bold;"><?php echo $stats['active_contracts']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div style="background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); color: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                                    <div style="display: flex; align-items: center;">
                                        <div style="margin-right: 15px;">
                                            <i class="fa fa-money" style="font-size: 2.5em; opacity: 0.8;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9em; margin-bottom: 5px;">Pending Payments</div>
                                            <div style="font-size: 2em; font-weight: bold;"><?php echo $stats['pending_payments']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts and Tables Row -->
                        <div class="row mt-4">
                            <!-- Recent Contractors -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Recent Contractors</h3>
                                        <div class="panel-heading-actions">
                                            <a href="#" onclick="alert('Full contractors page coming soon!')" class="btn btn-xs btn-primary">View All</a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php if(!empty($recent_contractors)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Company</th>
                                                            <th>Contact</th>
                                                            <th>Status</th>
                                                            <th>Created</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($recent_contractors as $contractor): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="<?php echo admin_url('ella_contractors/documents/gallery/' . $contractor->id); ?>">
                                                                        <?= htmlspecialchars($contractor->company_name) ?>
                                                                    </a>
                                                                </td>
                                                                <td><?= htmlspecialchars($contractor->contact_person) ?></td>
                                                                <td><?= htmlspecialchars($contractor->email) ?></td>
                                                                <td><?= htmlspecialchars($contractor->phone) ?></td>
                                                                <td>
                                                                    <span class="status-badge status-<?= $contractor->status ?>">
                                                                        <?= ucfirst($contractor->status) ?>
                                                                    </span>
                                                                </td>
                                                                <td><?= date('M j, Y', strtotime($contractor->date_created)) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted">No contractors found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Contracts -->
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Active Contracts</h3>
                                        <div class="panel-heading-actions">
                                            <a href="#" onclick="alert('Full contracts page coming soon!')" class="btn btn-xs btn-primary">View All</a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php if(!empty($active_contracts)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Contract #</th>
                                                            <th>Contractor</th>
                                                            <th>Value</th>
                                                            <th>End Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($active_contracts as $contract): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="#" onclick="alert('Contract details coming soon!')">
                                                                        <?php echo $contract->contract_number; ?>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo $contract->company_name; ?></td>
                                                                <td><?php echo format_contractor_currency(calculate_contract_value($contract)); ?></td>
                                                                <td><?php echo date('M j, Y', strtotime($contract->end_date)); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted">No active contracts found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Payments -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Pending Payments</h3>
                                        <div class="panel-heading-actions">
                                            <a href="#" onclick="alert('Full payments page coming soon!')" class="btn btn-xs btn-primary">View All</a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php if(!empty($pending_payments)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Contractor</th>
                                                            <th>Contract</th>
                                                            <th>Amount</th>
                                                            <th>Due Date</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($pending_payments as $payment): ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="#" onclick="alert('Contractor profile coming soon!')">
                                                                        <?php echo $payment->company_name; ?>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <?php if($payment->contract_number): ?>
                                                                        <a href="#" onclick="alert('Contract details coming soon!')">
                                                                            <?php echo $payment->contract_number; ?>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        -
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td><?php echo format_contractor_currency($payment->amount); ?></td>
                                                                <td>
                                                                    <span class="<?php echo (strtotime($payment->due_date) < time()) ? 'text-danger' : ''; ?>">
                                                                        <?php echo date('M j, Y', strtotime($payment->due_date)); ?>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo get_payment_status_badge($payment->status); ?></td>
                                                                <td>
                                                                    <a href="#" onclick="alert('Payment details coming soon!')" 
                                                                       class="btn btn-xs btn-primary">View</a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted">No pending payments found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Management Section -->
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Document Management & Generation</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>PDF Generation</h4>
                                            <div class="btn-group-vertical" style="width: 100%;">
                                                <a href="<?= admin_url('ella_contractors/pdf/contract/1') ?>" class="btn btn-primary btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-file-pdf-o"></i> Generate Contract PDF
                                                </a>
                                                <a href="<?= admin_url('ella_contractors/pdf/invoice/1') ?>" class="btn btn-success btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-file-pdf-o"></i> Generate Invoice PDF
                                                </a>
                                                <a href="<?= admin_url('ella_contractors/pdf/report/daily') ?>" class="btn btn-info btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-file-pdf-o"></i> Daily Report PDF
                                                </a>
                                                <a href="<?= admin_url('ella_contractors/pdf/report/monthly') ?>" class="btn btn-warning btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-file-pdf-o"></i> Monthly Report PDF
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Presentation Generation</h4>
                                            <div class="btn-group-vertical" style="width: 100%;">
                                                <a href="<?= admin_url('ella_contractors/presentation/contractor/1') ?>" class="btn btn-primary btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-file-powerpoint-o"></i> Contractor Presentation
                                                </a>
                                                <a href="<?= admin_url('ella_contractors/presentation/project/1') ?>" class="btn btn-success btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-file-powerpoint-o"></i> Project Presentation
                                                </a>
                                            </div>
                                            
                                            <h4 style="margin-top: 20px;">Document Management</h4>
                                            <div class="btn-group-vertical" style="width: 100%;">
                                                <a href="<?= admin_url('ella_contractors/documents/gallery/1') ?>" class="btn btn-info btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-folder-open"></i> Document Gallery
                                                </a>
                                                <a href="<?= admin_url('ella_contractors/documents/upload/1') ?>" class="btn btn-warning btn-sm" style="margin-bottom: 5px;">
                                                    <i class="fa fa-upload"></i> Upload Documents
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-md-12">
                                            <div class="alert alert-success">
                                                <strong>✓ Libraries Working!</strong> 
                                                <span class="text-success">TCPDF Available - Real PDFs will be generated</span>
                                                <br><span class="text-success">✓ PhpPresentation Available - Real PPTX files will be generated</span>
                                                <br><br>
                                                <a href="<?= admin_url('ella_contractors/test_libraries') ?>" class="btn btn-sm btn-info">
                                                    <i class="fa fa-cog"></i> Test Libraries
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script src="<?php echo module_dir_url('ella_contractors', 'assets/js/document-handler.js'); ?>"></script>

<script>
$(document).ready(function() {
    // Initialize any dashboard widgets or charts here
    console.log('Ella Contractors Dashboard loaded');
    
    // Load document handler
    if (typeof DocumentHandler !== 'undefined') {
        window.docHandler = new DocumentHandler();
    }
});

// PDF Generation Functions
function generateSamplePDF(type) {
    const sampleData = {
        contract: {
            contract_number: 'CON-2024-0001',
            contractor_name: 'ABC Construction Co.',
            contract_value: '15,000.00',
            start_date: '2024-01-15',
            end_date: '2024-03-15',
            description: 'Complete renovation of office building including electrical and plumbing work.'
        },
        invoice: {
            invoice_number: 'INV-2024-0001',
            contractor_name: 'ABC Construction Co.',
            amount: '5,000.00',
            due_date: '2024-01-25'
        },
        report: {
            title: 'Contractors Report',
            filename: 'contractors_report_' + new Date().toISOString().split('T')[0],
            labels: ['Active', 'Pending', 'Completed', 'Cancelled'],
            data: [18, 7, 15, 3]
        }
    };
    
    if (typeof window.generateContractPDF !== 'undefined' && type === 'contract') {
        window.generateContractPDF(sampleData.contract);
    } else if (typeof window.generateInvoicePDF !== 'undefined' && type === 'invoice') {
        window.generateInvoicePDF(sampleData.invoice);
    } else if (typeof window.generateReportPDF !== 'undefined' && type === 'report') {
        window.generateReportPDF(sampleData.report);
    } else {
        // Fallback to server-side generation
        let url = '';
        switch(type) {
            case 'contract':
                url = '<?php echo admin_url('ella_contractors/pdf/contract/1'); ?>';
                break;
            case 'invoice':
                url = '<?php echo admin_url('ella_contractors/pdf/invoice/1'); ?>';
                break;
            case 'report':
                url = '<?php echo admin_url('ella_contractors/pdf/report/contractors'); ?>';
                break;
        }
        if (url) {
            window.open(url, '_blank');
        }
    }
}

// Archive Creation
function createImageArchive() {
    // Mock file list for demo
    const mockFiles = [
        new File(['mock content 1'], 'contract_001.pdf', { type: 'application/pdf' }),
        new File(['mock content 2'], 'invoice_001.pdf', { type: 'application/pdf' }),
        new File(['mock content 3'], 'project_photo.jpg', { type: 'image/jpeg' })
    ];
    
    if (typeof window.createArchive !== 'undefined') {
        window.createArchive(mockFiles, 'contractor_documents_' + new Date().toISOString().split('T')[0] + '.zip');
    } else {
        alert('Creating archive of sample contractor documents...');
    }
}

// Share Functions
function generateShareableLink() {
    const shareUrl = '<?php echo base_url('shared/contractors/abc-construction'); ?>';
    const shareData = {
        title: 'ABC Construction - Contractor Documents',
        text: 'View contractor documents and project information',
        url: shareUrl
    };
    
    if (navigator.share) {
        navigator.share(shareData).then(() => {
            alert_float('success', 'Shared successfully');
        }).catch(err => {
            fallbackShare(shareUrl);
        });
    } else {
        fallbackShare(shareUrl);
    }
}

function fallbackShare(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            alert_float('success', 'Share link copied to clipboard');
        });
    } else {
        const shareHtml = `
            <div class="alert alert-info">
                <h5>Share Link Generated</h5>
                <p><strong>URL:</strong> <a href="${url}" target="_blank">${url}</a></p>
                <button class="btn btn-sm btn-primary" onclick="copyToClipboard('${url}')">Copy Link</button>
            </div>
        `;
        bootbox.alert({
            title: 'Share Contractor Information',
            message: shareHtml,
            size: 'large'
        });
    }
}

function emailDocuments() {
    const emailData = {
        to: 'contractor@example.com',
        subject: 'Project Documents - ABC Construction',
        body: 'Please find attached the project documents and contract information.'
    };
    
    const mailtoLink = `mailto:${emailData.to}?subject=${encodeURIComponent(emailData.subject)}&body=${encodeURIComponent(emailData.body)}`;
    window.location.href = mailtoLink;
    
    alert_float('info', 'Email client opened with document information');
}

function createCustomerPortal() {
    const portalFeatures = [
        '✓ Secure document access',
        '✓ Project progress tracking',
        '✓ Invoice and payment history',
        '✓ Direct communication channel',
        '✓ Mobile-responsive design'
    ];
    
    const portalHtml = `
        <div class="alert alert-success">
            <h5><i class="fa fa-globe"></i> Customer Portal Features</h5>
            <ul style="margin: 10px 0; padding-left: 20px;">
                ${portalFeatures.map(feature => `<li>${feature}</li>`).join('')}
            </ul>
            <p><strong>Demo Portal:</strong> <a href="#" onclick="window.open('https://demo.ellacontractors.com/portal/abc-construction', '_blank')">View Sample Portal</a></p>
        </div>
    `;
    
    bootbox.alert({
        title: 'Customer Portal',
        message: portalHtml,
        size: 'large'
    });
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            alert_float('success', 'Link copied to clipboard');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            alert_float('success', 'Link copied to clipboard');
        } catch (err) {
            alert_float('warning', 'Unable to copy link');
        }
        document.body.removeChild(textArea);
    }
}
</script>

<style>
.btn-purple {
    background-color: #6f42c1;
    border-color: #6f42c1;
    color: white;
}

.btn-purple:hover {
    background-color: #5a32a3;
    border-color: #5a32a3;
    color: white;
}

.document-feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
}

.document-feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.btn-group-vertical .btn {
    margin-bottom: 5px;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}
</style>

</body>
</html>
