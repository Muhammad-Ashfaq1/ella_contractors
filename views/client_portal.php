<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Ella Contractors</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
        }
        
        /* Header Styles */
        .portal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .portal-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .portal-header .subtitle {
            margin: 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        /* Sidebar Styles */
        .portal-sidebar {
            background: white;
            min-height: calc(100vh - 80px);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
        }
        
        .sidebar-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem 1rem;
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .sidebar-nav .nav-link {
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background-color: var(--light-bg);
            border-left-color: var(--secondary-color);
            color: var(--secondary-color);
        }
        
        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* Main Content Styles */
        .portal-main {
            padding: 2rem;
            min-height: calc(100vh - 80px);
        }
        
        .content-header {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .content-tabs {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .tab-content {
            padding: 2rem;
        }
        
        /* Tab Navigation */
        .nav-tabs .nav-link {
            border: none;
            color: var(--primary-color);
            font-weight: 500;
            padding: 1rem 1.5rem;
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--secondary-color);
            color: white;
            border: none;
        }
        
        /* Card Styles */
        .info-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--secondary-color);
        }
        
        .info-card h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        /* Media Gallery Styles */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .media-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .media-item:hover {
            transform: translateY(-5px);
        }
        
        .media-preview {
            height: 150px;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--secondary-color);
        }
        
        .media-info {
            padding: 1rem;
        }
        
        .media-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .media-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-in-progress { background-color: var(--warning-color); color: white; }
        .status-completed { background-color: var(--success-color); color: white; }
        .status-pending { background-color: var(--secondary-color); color: white; }
        
        /* Footer Styles */
        .portal-footer {
            background: var(--dark-bg);
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .portal-sidebar {
                position: static;
                min-height: auto;
            }
            
            .portal-main {
                padding: 1rem;
            }
            
            .media-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
        
        /* Custom Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Progress Bar */
        .progress-custom {
            height: 8px;
            border-radius: 4px;
            background-color: var(--light-bg);
        }
        
        .progress-custom .progress-bar {
            background: linear-gradient(90deg, var(--secondary-color), var(--success-color));
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="portal-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-building"></i> <?= $title ?></h1>
                    <p class="subtitle">Contract: <?= $contract['title'] ?> | Client: <?= $contract['client_name'] ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $contract['status'])) ?> me-3">
                            <?= $contract['status'] ?>
                        </span>
                        <button class="btn btn-outline-light btn-sm">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="portal-sidebar">
                    <div class="sidebar-header">
                        <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                        <h6 class="mb-0">Contract Portal</h6>
                    </div>
                    
                    <nav class="sidebar-nav">
                        <a class="nav-link active" href="#overview" data-bs-toggle="tab">
                            <i class="fas fa-tachometer-alt"></i> Overview
                        </a>
                        <a class="nav-link" href="#proposals" data-bs-toggle="tab">
                            <i class="fas fa-file-contract"></i> Proposals
                        </a>
                        <a class="nav-link" href="#gallery" data-bs-toggle="tab">
                            <i class="fas fa-images"></i> Image Gallery
                        </a>
                        <a class="nav-link" href="#presentations" data-bs-toggle="tab">
                            <i class="fas fa-presentation"></i> Presentations
                        </a>
                        <a class="nav-link" href="#documents" data-bs-toggle="tab">
                            <i class="fas fa-file-pdf"></i> PDFs & Documents
                        </a>
                        <a class="nav-link" href="#media" data-bs-toggle="tab">
                            <i class="fas fa-photo-video"></i> Media Library
                        </a>
                        <a class="nav-link" href="#appointments" data-bs-toggle="tab">
                            <i class="fas fa-calendar-alt"></i> Appointments
                        </a>
                        <a class="nav-link" href="#notes" data-bs-toggle="tab">
                            <i class="fas fa-sticky-note"></i> Notes
                        </a>
                        <a class="nav-link" href="#dimensions" data-bs-toggle="tab">
                            <i class="fas fa-ruler-combined"></i> Dimensions
                        </a>
                        <a class="nav-link" href="#estimates" data-bs-toggle="tab">
                            <i class="fas fa-calculator"></i> Estimates
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="portal-main">
                    <!-- Content Header -->
                    <div class="content-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2"><?= $contract['title'] ?></h3>
                                <p class="mb-0 text-muted"><?= $contract['description'] ?></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="progress-custom">
                                    <div class="progress-bar" style="width: 65%"></div>
                                </div>
                                <small class="text-muted">65% Complete</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="content-tabs">
                        <ul class="nav nav-tabs" id="portalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="proposals-tab" data-bs-toggle="tab" data-bs-target="#proposals" type="button" role="tab">Proposals</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" type="button" role="tab">Gallery</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="presentations-tab" data-bs-toggle="tab" data-bs-target="#presentations" type="button" role="tab">Presentations</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">Documents</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab">Media</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">Appointments</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Notes</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="dimensions-tab" data-bs-toggle="tab" data-bs-target="#dimensions" type="button" role="tab">Dimensions</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="estimates-tab" data-bs-toggle="tab" data-bs-target="#estimates" type="button" role="tab">Estimates</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="portalTabContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <h5><i class="fas fa-info-circle text-primary"></i> Contract Details</h5>
                                            <div class="row">
                                                <div class="col-6">
                                                    <p><strong>Status:</strong><br>
                                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $contract['status'])) ?>"><?= $contract['status'] ?></span></p>
                                                </div>
                                                <div class="col-6">
                                                    <p><strong>Total Value:</strong><br><?= $contract['total_value'] ?></p>
                                                </div>
                                            </div>
                                            <p><strong>Start Date:</strong> <?= date('M d, Y', strtotime($contract['start_date'])) ?></p>
                                            <p><strong>Estimated Completion:</strong> <?= date('M d, Y', strtotime($contract['estimated_completion'])) ?></p>
                                            <p><strong>Address:</strong> <?= $contract['address'] ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <h5><i class="fas fa-user text-primary"></i> Client Information</h5>
                                            <p><strong>Name:</strong> <?= $contract['client_name'] ?></p>
                                            <p><strong>Email:</strong> <?= $contract['client_email'] ?></p>
                                            <p><strong>Phone:</strong> <?= $contract['client_phone'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="info-card">
                                            <h5><i class="fas fa-chart-line text-primary"></i> Project Progress</h5>
                                            <div class="progress-custom mb-3">
                                                <div class="progress-bar" style="width: 65%"></div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-md-3">
                                                    <h4 class="text-success">65%</h4>
                                                    <small class="text-muted">Overall Progress</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4 class="text-primary">12</h4>
                                                    <small class="text-muted">Tasks Completed</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4 class="text-warning">5</h4>
                                                    <small class="text-muted">Tasks Pending</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <h4 class="text-info">3</h4>
                                                    <small class="text-muted">Days Remaining</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Proposals Tab -->
                            <div class="tab-pane fade" id="proposals" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-file-contract text-primary"></i> Contract Proposals</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Proposal #</th>
                                                    <th>Title</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Value</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>PROP-001</td>
                                                    <td>Initial Kitchen Design Proposal</td>
                                                    <td>Jan 10, 2024</td>
                                                    <td><span class="status-badge status-completed">Accepted</span></td>
                                                    <td>$45,000</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>PROP-002</td>
                                                    <td>Revised Cabinet Selection</td>
                                                    <td>Jan 25, 2024</td>
                                                    <td><span class="status-badge status-pending">Pending</span></td>
                                                    <td>$2,500</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Gallery Tab -->
                            <div class="tab-pane fade" id="gallery" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-images text-primary"></i> Project Image Gallery</h5>
                                    <div class="media-grid">
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Before - Kitchen</div>
                                                <div class="media-meta">Jan 15, 2024 • 2.3 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">After - Kitchen</div>
                                                <div class="media-meta">Jan 20, 2024 • 1.8 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Cabinet Installation</div>
                                                <div class="media-meta">Jan 22, 2024 • 3.1 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-image"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Countertop Installation</div>
                                                <div class="media-meta">Jan 25, 2024 • 2.7 MB</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Presentations Tab -->
                            <div class="tab-pane fade" id="presentations" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-presentation text-primary"></i> Project Presentations</h5>
                                    <div class="media-grid">
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-powerpoint"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Initial Design Presentation</div>
                                                <div class="media-meta">Jan 12, 2024 • 15.2 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-powerpoint"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Material Selection Guide</div>
                                                <div class="media-meta">Jan 18, 2024 • 8.7 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-powerpoint"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Progress Update</div>
                                                <div class="media-meta">Jan 24, 2024 • 12.1 MB</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Tab -->
                            <div class="tab-pane fade" id="documents" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-file-pdf text-primary"></i> PDFs & Documents</h5>
                                    <div class="media-grid">
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Contract Agreement</div>
                                                <div class="media-meta">Jan 10, 2024 • 2.1 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Project Specifications</div>
                                                <div class="media-meta">Jan 12, 2024 • 4.3 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Material List</div>
                                                <div class="media-meta">Jan 15, 2024 • 1.8 MB</div>
                                            </div>
                                        </div>
                                        <div class="media-item">
                                            <div class="media-preview">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div class="media-info">
                                                <div class="media-title">Warranty Information</div>
                                                <div class="media-meta">Jan 20, 2024 • 0.9 MB</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Media Library Tab -->
                            <div class="tab-pane fade" id="media" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-photo-video text-primary"></i> Media Library</h5>
                                    <?php if (!empty($media_files)): ?>
                                        <div class="media-grid">
                                            <?php foreach ($media_files as $media): ?>
                                            <div class="media-item">
                                                <div class="media-preview">
                                                    <i class="fas fa-<?= get_file_icon($media->file_type) ?>"></i>
                                                </div>
                                                <div class="media-info">
                                                    <div class="media-title"><?= character_limiter($media->original_name, 30) ?></div>
                                                    <div class="media-meta"><?= formatBytes($media->file_size) ?> • <?= $media->file_type ?></div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No media files available</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Appointments Tab -->
                            <div class="tab-pane fade" id="appointments" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-calendar-alt text-primary"></i> Scheduled Appointments</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Notes</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Jan 30, 2024<br><small class="text-muted">9:00 AM</small></td>
                                                    <td>Final Inspection</td>
                                                    <td><span class="status-badge status-pending">Scheduled</span></td>
                                                    <td>Final walkthrough with client</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Jan 28, 2024<br><small class="text-muted">2:00 PM</small></td>
                                                    <td>Material Selection</td>
                                                    <td><span class="status-badge status-completed">Completed</span></td>
                                                    <td>Client selected granite countertops</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Jan 25, 2024<br><small class="text-muted">10:00 AM</small></td>
                                                    <td>Progress Review</td>
                                                    <td><span class="status-badge status-completed">Completed</span></td>
                                                    <td>50% completion milestone reached</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes Tab -->
                            <div class="tab-pane fade" id="notes" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-sticky-note text-primary"></i> Project Notes</h5>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Add New Note</label>
                                                <textarea class="form-control" rows="3" placeholder="Enter your note here..."></textarea>
                                            </div>
                                            <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Note</button>
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <div class="notes-list">
                                        <div class="note-item border-start border-primary ps-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">Material Selection Complete</h6>
                                                <small class="text-muted">Jan 28, 2024</small>
                                            </div>
                                            <p class="mb-1">Client has selected all materials including granite countertops, maple cabinets, and porcelain tile flooring.</p>
                                            <small class="text-muted">Added by: Project Manager</small>
                                        </div>
                                        
                                        <div class="note-item border-start border-success ps-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">Electrical Work Completed</h6>
                                                <small class="text-muted">Jan 26, 2024</small>
                                            </div>
                                            <p class="mb-1">All electrical outlets and lighting fixtures have been installed and tested. Ready for inspection.</p>
                                            <small class="text-muted">Added by: Electrician</small>
                                        </div>
                                        
                                        <div class="note-item border-start border-warning ps-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1">Cabinet Delivery Delay</h6>
                                                <small class="text-muted">Jan 24, 2024</small>
                                            </div>
                                            <p class="mb-1">Cabinet delivery delayed by 2 days due to weather. New delivery date: Jan 26, 2024.</p>
                                            <small class="text-muted">Added by: Logistics Coordinator</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dimensions Tab -->
                            <div class="tab-pane fade" id="dimensions" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-ruler-combined text-primary"></i> Project Dimensions & Measurements</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Kitchen Layout</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Total Area:</strong></td>
                                                            <td>180 sq ft</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Length:</strong></td>
                                                            <td>18 ft</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Width:</strong></td>
                                                            <td>10 ft</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Ceiling Height:</strong></td>
                                                            <td>8 ft</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Cabinet Specifications</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Upper Cabinets:</strong></td>
                                                            <td>36" height</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Lower Cabinets:</strong></td>
                                                            <td>34.5" height</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Counter Depth:</strong></td>
                                                            <td>25"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Island Size:</strong></td>
                                                            <td>4' x 6'</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="text-primary">Detailed Measurements</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Component</th>
                                                            <th>Length</th>
                                                            <th>Width</th>
                                                            <th>Height</th>
                                                            <th>Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Upper Cabinet Section 1</td>
                                                            <td>36"</td>
                                                            <td>12"</td>
                                                            <td>36"</td>
                                                            <td>1</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Upper Cabinet Section 2</td>
                                                            <td>48"</td>
                                                            <td>12"</td>
                                                            <td>36"</td>
                                                            <td>1</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lower Cabinet Section 1</td>
                                                            <td>36"</td>
                                                            <td>24"</td>
                                                            <td>34.5"</td>
                                                            <td>1</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lower Cabinet Section 2</td>
                                                            <td>48"</td>
                                                            <td>24"</td>
                                                            <td>34.5"</td>
                                                            <td>1</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Kitchen Island</td>
                                                            <td>72"</td>
                                                            <td>48"</td>
                                                            <td>34.5"</td>
                                                            <td>1</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimates Tab -->
                            <div class="tab-pane fade" id="estimates" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-calculator text-primary"></i> Project Estimates & Costs</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Cost Summary</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Total Estimate:</strong></td>
                                                            <td class="text-end"><span class="h5 text-success">$45,000</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Materials:</strong></td>
                                                            <td class="text-end">$28,500</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Labor:</strong></td>
                                                            <td class="text-end">$12,000</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Permits & Fees:</strong></td>
                                                            <td class="text-end">$2,500</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Contingency:</strong></td>
                                                            <td class="text-end">$2,000</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Payment Schedule</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>Deposit (25%):</strong></td>
                                                            <td class="text-end">$11,250</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Progress Payment 1 (30%):</strong></td>
                                                            <td class="text-end">$13,500</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Progress Payment 2 (30%):</strong></td>
                                                            <td class="text-end">$13,500</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Final Payment (15%):</strong></td>
                                                            <td class="text-end">$6,750</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h6 class="text-primary">Detailed Cost Breakdown</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Item</th>
                                                            <th>Quantity</th>
                                                            <th>Unit Cost</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td rowspan="3">Cabinets</td>
                                                            <td>Upper Cabinets</td>
                                                            <td>84 linear ft</td>
                                                            <td>$85/ft</td>
                                                            <td>$7,140</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lower Cabinets</td>
                                                            <td>84 linear ft</td>
                                                            <td>$120/ft</td>
                                                            <td>$10,080</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Kitchen Island</td>
                                                            <td>1 unit</td>
                                                            <td>$2,500</td>
                                                            <td>$2,500</td>
                                                        </tr>
                                                        <tr>
                                                            <td rowspan="2">Countertops</td>
                                                            <td>Granite Slabs</td>
                                                            <td>45 sq ft</td>
                                                            <td>$65/sq ft</td>
                                                            <td>$2,925</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Installation</td>
                                                            <td>45 sq ft</td>
                                                            <td>$25/sq ft</td>
                                                            <td>$1,125</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Appliances</td>
                                                            <td>Refrigerator, Range, Dishwasher</td>
                                                            <td>3 units</td>
                                                            <td>$1,200/unit</td>
                                                            <td>$3,600</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Flooring</td>
                                                            <td>Porcelain Tile</td>
                                                            <td>180 sq ft</td>
                                                            <td>$8/sq ft</td>
                                                            <td>$1,440</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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

    <!-- Footer -->
    <footer class="portal-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">&copy; 2024 Ella Contractors. All rights reserved. | Contract Portal v1.0</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tabs
            var triggerTabList = [].slice.call(document.querySelectorAll('#portalTabs button'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
            
            // Sidebar navigation highlighting
            const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            const tabButtons = document.querySelectorAll('#portalTabs .nav-link');
            
            tabButtons.forEach((tab, index) => {
                tab.addEventListener('shown.bs.tab', function() {
                    // Remove active class from all sidebar links
                    sidebarLinks.forEach(link => link.classList.remove('active'));
                    // Add active class to corresponding sidebar link
                    if (sidebarLinks[index]) {
                        sidebarLinks[index].classList.add('active');
                    }
                });
            });
            
            // Add fade-in animation to content
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => {
                pane.classList.add('fade-in');
            });
        });
        
        // Helper function for file size formatting (if not available from backend)
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        
        // Helper function for file icon (if not available from backend)
        function get_file_icon(file_type) {
            const iconMap = {
                'pdf': 'fa-file-pdf',
                'doc': 'fa-file-word',
                'docx': 'fa-file-word',
                'xls': 'fa-file-excel',
                'xlsx': 'fa-file-excel',
                'ppt': 'fa-file-powerpoint',
                'pptx': 'fa-file-powerpoint',
                'jpg': 'fa-file-image',
                'jpeg': 'fa-file-image',
                'png': 'fa-file-image',
                'gif': 'fa-file-image',
                'mp4': 'fa-file-video',
                'avi': 'fa-file-video',
                'mov': 'fa-file-video'
            };
            return iconMap[file_type.toLowerCase()] || 'fa-file';
        }
        
        // Helper function for text limiting (if not available from backend)
        function character_limiter(str, length) {
            if (str.length <= length) return str;
            return str.substring(0, length) + '...';
        }
    </script>
</body>
</html>
