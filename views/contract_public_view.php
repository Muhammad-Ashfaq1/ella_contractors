<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract: <?= $contract->subject ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .contract-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .contract-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .contract-card .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .contract-card .card-body {
            padding: 20px;
        }
        .status-badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 600;
        }
        .status-draft { background: #6c757d; color: white; }
        .status-active { background: #28a745; color: white; }
        .status-completed { background: #17a2b8; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        .status-expired { background: #ffc107; color: #212529; }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
        .company-logo {
            width: 60px;
            height: 60px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        .company-logo i {
            font-size: 24px;
            color: #6c757d;
        }
        .footer {
            background: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }
        @media (max-width: 768px) {
            .contract-header {
                padding: 20px 0;
            }
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-value {
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="contract-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1><i class="fa fa-file-contract"></i> Contract Details</h1>
                    <p class="lead"><?= $contract->subject ?></p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="company-logo">
                        <i class="fa fa-building"></i>
                    </div>
                    <h4>Ella Contractors</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Contract Information -->
        <div class="row">
            <div class="col-md-8">
                <div class="contract-card">
                    <div class="card-header">
                        <h4><i class="fa fa-info-circle"></i> Contract Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Contract Number:</span>
                            <span class="info-value"><?= $contract->contract_number ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Subject:</span>
                            <span class="info-value"><?= $contract->subject ?></span>
                        </div>
                        <?php if ($contract->description): ?>
                        <div class="info-row">
                            <span class="info-label">Description:</span>
                            <span class="info-value"><?= nl2br($contract->description) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-<?= $contract->status ?>">
                                    <?= ucfirst($contract->status) ?>
                                </span>
                            </span>
                        </div>
                        <?php if ($contract->contract_value): ?>
                        <div class="info-row">
                            <span class="info-label">Contract Value:</span>
                            <span class="info-value">
                                <strong><?= '$' . number_format($contract->contract_value, 2) ?></strong>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if ($contract->start_date): ?>
                        <div class="info-row">
                            <span class="info-label">Start Date:</span>
                            <span class="info-value"><?= date('M d, Y', strtotime($contract->start_date)) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($contract->end_date): ?>
                        <div class="info-row">
                            <span class="info-label">End Date:</span>
                            <span class="info-value"><?= date('M d, Y', strtotime($contract->end_date)) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($contract->payment_terms): ?>
                        <div class="info-row">
                            <span class="info-label">Payment Terms:</span>
                            <span class="info-value"><?= $contract->payment_terms ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($contract->notes): ?>
                        <div class="info-row">
                            <span class="info-label">Notes:</span>
                            <span class="info-value"><?= nl2br($contract->notes) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Lead Information -->
                <div class="contract-card">
                    <div class="card-header">
                        <h5><i class="fa fa-user"></i> Lead Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="company-logo">
                            <i class="fa fa-user"></i>
                        </div>
                        <h5 class="text-center"><?= $contract->lead_name ?></h5>
                        <?php if ($contract->lead_company): ?>
                        <p class="text-center text-muted"><?= $contract->lead_company ?></p>
                        <?php endif; ?>
                        <?php if ($contract->lead_email): ?>
                        <p class="text-center">
                            <a href="mailto:<?= $contract->lead_email ?>">
                                <i class="fa fa-envelope"></i> <?= $contract->lead_email ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <?php if ($contract->lead_phone): ?>
                        <p class="text-center">
                            <a href="tel:<?= $contract->lead_phone ?>">
                                <i class="fa fa-phone"></i> <?= $contract->lead_phone ?>
                            </a>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Contractor Information -->
                <div class="contract-card">
                    <div class="card-header">
                        <h5><i class="fa fa-briefcase"></i> Contractor Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="company-logo">
                            <i class="fa fa-briefcase"></i>
                        </div>
                        <h5 class="text-center"><?= $contract->contractor_name ?></h5>
                        <p class="text-center text-muted"><?= $contract->contractor_contact ?></p>
                        <?php if ($contract->contractor_email): ?>
                        <p class="text-center">
                            <a href="mailto:<?= $contract->contractor_email ?>">
                                <i class="fa fa-envelope"></i> <?= $contract->contractor_email ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <?php if ($contract->contractor_phone): ?>
                        <p class="text-center">
                            <a href="tel:<?= $contract->contractor_phone ?>">
                                <i class="fa fa-phone"></i> <?= $contract->contractor_phone ?>
                            </a>
                        </p>
                        <?php endif; ?>
                        <?php if ($contract->contractor_specialties): ?>
                        <p class="text-center">
                            <small class="text-muted">
                                <i class="fa fa-tags"></i> <?= $contract->contractor_specialties ?>
                            </small>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contract Timeline -->
        <div class="row">
            <div class="col-md-12">
                <div class="contract-card">
                    <div class="card-header">
                        <h4><i class="fa fa-history"></i> Contract Timeline</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success">
                                    <i class="fa fa-plus"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Contract Created</h6>
                                    <p class="text-muted">
                                        Contract was created on <?= date('M d, Y \a\t H:i', strtotime($contract->date_created)) ?>
                                    </p>
                                </div>
                            </div>
                            
                            <?php if ($contract->date_updated): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info">
                                    <i class="fa fa-edit"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Contract Updated</h6>
                                    <p class="text-muted">
                                        Contract was last updated on <?= date('M d, Y \a\t H:i', strtotime($contract->date_updated)) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($contract->status === 'active' && $contract->start_date): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary">
                                    <i class="fa fa-play"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Contract Started</h6>
                                    <p class="text-muted">
                                        Contract became active on <?= date('M d, Y', strtotime($contract->start_date)) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($contract->status === 'completed' && $contract->end_date): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Contract Completed</h6>
                                    <p class="text-muted">
                                        Contract was completed on <?= date('M d, Y', strtotime($contract->end_date)) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Ella Contractors. All rights reserved.</p>
            <p><small>This is a shared contract view. Please contact us for any questions.</small></p>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <style>
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 30px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 80px;
        }
        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .timeline-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .timeline-content h6 {
            margin: 0 0 10px 0;
            color: #495057;
            font-weight: 600;
        }
        .timeline-content p {
            margin: 0;
            color: #6c757d;
        }
        .bg-success { background-color: #28a745; }
        .bg-info { background-color: #17a2b8; }
        .bg-primary { background-color: #007bff; }
    </style>
</body>
</html>
