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
        
        .portal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
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
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.show {
            display: block;
        }
        
        .tab-pane.active {
            display: block;
        }
        
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
        
        .info-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--secondary-color);
        }
        
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
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-accepted { background-color: var(--success-color); color: white; }
        .status-draft { background-color: var(--warning-color); color: white; }
        .status-sent { background-color: var(--secondary-color); color: white; }
        
        .portal-footer {
            background: var(--dark-bg);
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .portal-sidebar { position: static; min-height: auto; }
            .portal-main { padding: 1rem; }
            .media-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
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
                        <button class="btn btn-outline-light btn-sm me-2" onclick="copyPortalLink()" title="Copy Link">
                            <i class="fas fa-link"></i> Copy Link
                        </button>
                        <button class="btn btn-outline-light btn-sm" onclick="sharePortal()" title="Share Portal">
                            <i class="fas fa-share-alt"></i> Share
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
                        <h6 class="mb-0">Client Portal</h6>
                    </div>
                    
                    <nav class="sidebar-nav">
                        <a class="nav-link active" href="#overview">
                            <i class="fas fa-tachometer-alt"></i> Overview
                        </a>
                        <a class="nav-link" href="#gallery">
                            <i class="fas fa-images"></i> Image Gallery
                        </a>
                        <a class="nav-link" href="#presentations">
                            <i class="fas fa-presentation"></i> Presentations
                        </a>
                        <a class="nav-link" href="#documents">
                            <i class="fas fa-file-pdf"></i> Documents
                        </a>
                        <a class="nav-link" href="#media">
                            <i class="fas fa-photo-video"></i> Media Library
                        </a>
                        <a class="nav-link" href="#dimensions">
                            <i class="fas fa-ruler-combined"></i> Dimensions
                        </a>
                        <a class="nav-link" href="#estimates">
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
                                <div class="d-flex flex-column align-items-end">
                                    <div class="progress mb-2" style="height: 8px; width: 100%;">
                                        <div class="progress-bar" style="width: 65%"></div>
                                    </div>
                                    <small class="text-muted mb-2">65% Complete</small>
                                    <button class="btn btn-outline-primary btn-sm" onclick="sharePortal()">
                                        <i class="fas fa-share-alt"></i> Share Portal
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm ms-2" onclick="testTabChange()">
                                        <i class="fas fa-cog"></i> Test Tab
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="content-tabs">
                        <ul class="nav nav-tabs" id="portalTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" type="button" role="tab">Overview</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="gallery-tab" type="button" role="tab">Gallery</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="presentations-tab" type="button" role="tab">Presentations</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="documents-tab" type="button" role="tab">Documents</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="media-tab" type="button" role="tab">Media</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="dimensions-tab" type="button" role="tab">Dimensions</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="estimates-tab" type="button" role="tab">Estimates</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="portalTabContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <h5><i class="fas fa-info-circle text-primary"></i> Contract Details</h5>
                                            <p><strong>Status:</strong> <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $contract['status'])) ?>"><?= $contract['status'] ?></span></p>
                                            <p><strong>Total Value:</strong> <?= $contract['total_value'] ?></p>
                                            <p><strong>Start Date:</strong> <?= date('M d, Y', strtotime($contract['start_date'])) ?></p>
                                            <p><strong>Estimated Completion:</strong> <?= date('M d, Y', strtotime($contract['estimated_completion'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <h5><i class="fas fa-user text-primary"></i> Client Information</h5>
                                            <p><strong>Name:</strong> <?= $contract['client_name'] ?></p>
                                            <p><strong>Email:</strong> <?= $contract['client_email'] ?></p>
                                            <p><strong>Phone:</strong> <?= $contract['client_phone'] ?></p>
                                            <?php if (isset($contract['client_company'])): ?>
                                            <p><strong>Company:</strong> <?= $contract['client_company'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Tab -->
                            <div class="tab-pane fade" id="gallery" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-images text-primary"></i> Project Image Gallery</h5>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="media-grid">
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">Before - Project Site</div>
                                                        <div class="media-meta">Jan 15, 2024 • 2.3 MB</div>
                                                    </div>
                                                </div>
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">During - Construction Phase</div>
                                                        <div class="media-meta">Jan 18, 2024 • 3.1 MB</div>
                                                    </div>
                                                </div>
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">After - Completed Project</div>
                                                        <div class="media-meta">Jan 20, 2024 • 1.8 MB</div>
                                                    </div>
                                                </div>
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">Detail - Quality Check</div>
                                                        <div class="media-meta">Jan 22, 2024 • 2.7 MB</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-info-circle"></i> Gallery Info</h6>
                                                <p class="mb-2"><strong>Total Images:</strong> 24</p>
                                                <p class="mb-2"><strong>Last Updated:</strong> Jan 22, 2024</p>
                                                <p class="mb-0"><strong>Storage Used:</strong> 45.2 MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Presentations Tab -->
                            <div class="tab-pane fade" id="presentations" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-presentation text-primary"></i> Project Presentations</h5>
                                    <div class="row">
                                        <div class="col-md-8">
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
                                                        <div class="media-title">Progress Update Meeting</div>
                                                        <div class="media-meta">Jan 25, 2024 • 8.7 MB</div>
                                                    </div>
                                                </div>
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-file-powerpoint"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">Final Project Review</div>
                                                        <div class="media-meta">Jan 30, 2024 • 12.3 MB</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-warning">
                                                <h6><i class="fas fa-clock"></i> Next Presentation</h6>
                                                <p class="mb-2"><strong>Date:</strong> Feb 5, 2024</p>
                                                <p class="mb-2"><strong>Time:</strong> 2:00 PM</p>
                                                <p class="mb-0"><strong>Type:</strong> Final Walkthrough</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Tab -->
                            <div class="tab-pane fade" id="documents" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-file-pdf text-primary"></i> PDFs & Documents</h5>
                                    <div class="row">
                                        <div class="col-md-8">
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
                                                        <i class="fas fa-file-word"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">Project Specifications</div>
                                                        <div class="media-meta">Jan 12, 2024 • 1.8 MB</div>
                                                    </div>
                                                </div>
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-file-excel"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">Cost Breakdown</div>
                                                        <div class="media-meta">Jan 15, 2024 • 0.9 MB</div>
                                                    </div>
                                                </div>
                                                <div class="media-item">
                                                    <div class="media-preview">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div class="media-info">
                                                        <div class="media-title">Safety Guidelines</div>
                                                        <div class="media-meta">Jan 18, 2024 • 3.2 MB</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="alert alert-success">
                                                <h6><i class="fas fa-check-circle"></i> Document Status</h6>
                                                <p class="mb-2"><strong>Total Documents:</strong> 8</p>
                                                <p class="mb-2"><strong>Last Updated:</strong> Jan 25, 2024</p>
                                                <p class="mb-0"><strong>Storage Used:</strong> 12.7 MB</p>
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
                                                    <div class="media-title"><?= ella_character_limiter($media->original_filename, 30) ?></div>
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

                            <!-- Dimensions Tab -->
                            <div class="tab-pane fade" id="dimensions" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-ruler-combined text-primary"></i> Project Dimensions</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Project Layout</h6>
                                            <p><strong>Total Area:</strong> 180 sq ft</p>
                                            <p><strong>Length:</strong> 18 ft</p>
                                            <p><strong>Width:</strong> 10 ft</p>
                                            <p><strong>Height:</strong> 12 ft</p>
                                            <p><strong>Ceiling Height:</strong> 9 ft</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Room Specifications</h6>
                                            <p><strong>Living Room:</strong> 12' × 15'</p>
                                            <p><strong>Kitchen:</strong> 10' × 12'</p>
                                            <p><strong>Bedroom:</strong> 12' × 10'</p>
                                            <p><strong>Bathroom:</strong> 8' × 6'</p>
                                            <p><strong>Storage:</strong> 6' × 8'</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6 class="text-primary">Material Requirements</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <p><strong>Flooring:</strong> 180 sq ft</p>
                                                    <p><strong>Wall Paint:</strong> 540 sq ft</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p><strong>Ceiling Tiles:</strong> 180 sq ft</p>
                                                    <p><strong>Baseboards:</strong> 56 linear ft</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p><strong>Door Frames:</strong> 4 units</p>
                                                    <p><strong>Window Frames:</strong> 3 units</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimates Tab -->
                            <div class="tab-pane fade" id="estimates" role="tabpanel">
                                <div class="info-card">
                                    <h5><i class="fas fa-calculator text-primary"></i> Project Estimates</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Cost Summary</h6>
                                            <p><strong>Total Estimate:</strong> <?= $contract['total_value'] ?></p>
                                            <p><strong>Materials:</strong> $28,500</p>
                                            <p><strong>Labor:</strong> $12,000</p>
                                            <p><strong>Permits:</strong> $2,500</p>
                                            <p><strong>Contingency:</strong> $3,750</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Payment Schedule</h6>
                                            <p><strong>Deposit (30%):</strong> $4,725</p>
                                            <p><strong>Progress (40%):</strong> $6,300</p>
                                            <p><strong>Completion (30%):</strong> $4,725</p>
                                            <p><strong>Total:</strong> $15,750</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <h6 class="text-primary">Detailed Breakdown</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="text-info">Materials</h6>
                                                    <p><strong>Flooring:</strong> $8,500</p>
                                                    <p><strong>Paint:</strong> $2,800</p>
                                                    <p><strong>Fixtures:</strong> $4,200</p>
                                                    <p><strong>Hardware:</strong> $1,500</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="text-info">Labor</h6>
                                                    <p><strong>Demolition:</strong> $2,500</p>
                                                    <p><strong>Installation:</strong> $6,500</p>
                                                    <p><strong>Finishing:</strong> $2,000</p>
                                                    <p><strong>Cleanup:</strong> $1,000</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="text-info">Other Costs</h6>
                                                    <p><strong>Permits:</strong> $2,500</p>
                                                    <p><strong>Insurance:</strong> $800</p>
                                                    <p><strong>Equipment:</strong> $1,200</p>
                                                    <p><strong>Contingency:</strong> $3,750</p>
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

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">
                        <i class="fas fa-share-alt text-primary"></i> Share Client Portal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Portal Link</label>
                        <div class="input-group">
                                                                        <input type="text" class="form-control" id="portalLink" value="<?= base_url(uri_string()) ?>" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyPortalLink()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Share via</label>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="shareViaEmail()">
                                <i class="fas fa-envelope"></i> Email
                            </button>
                            <button class="btn btn-success" onclick="shareViaWhatsApp()">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </button>
                            <button class="btn btn-info" onclick="shareViaTelegram()">
                                <i class="fab fa-telegram"></i> Telegram
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> This portal is publicly accessible. Anyone with the link can view the contract information.
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <label class="form-label">QR Code for Mobile</label>
                            <div id="qrCode" class="border p-2 rounded bg-light"></div>
                            <small class="text-muted">Scan with your phone to open</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="portal-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">&copy; 2024 Ella Contractors. All rights reserved. | Client Portal v1.0</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing tabs...');
            
            // Get all tab elements
            const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
            const tabButtons = document.querySelectorAll('#portalTabs .nav-link');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            console.log('Found sidebar links:', sidebarLinks.length);
            console.log('Found tab buttons:', tabButtons.length);
            console.log('Found tab panes:', tabPanes.length);
            
            // Debug: Log each element found
            sidebarLinks.forEach((link, i) => console.log('Sidebar link', i, ':', link.textContent.trim()));
            tabButtons.forEach((btn, i) => console.log('Tab button', i, ':', btn.textContent.trim()));
            tabPanes.forEach((pane, i) => console.log('Tab pane', i, ':', pane.id));
            
            // Function to show a specific tab
            function showTab(tabIndex) {
                console.log('Showing tab:', tabIndex);
                
                // Hide all tab panes
                tabPanes.forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                // Remove active class from all tab buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Remove active class from all sidebar links
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                });
                
                // Show the selected tab pane
                if (tabPanes[tabIndex]) {
                    tabPanes[tabIndex].classList.add('show', 'active');
                }
                
                // Activate the corresponding tab button
                if (tabButtons[tabIndex]) {
                    tabButtons[tabIndex].classList.add('active');
                }
                
                // Activate the corresponding sidebar link
                if (sidebarLinks[tabIndex]) {
                    sidebarLinks[tabIndex].classList.add('active');
                }
            }
            
            // Handle sidebar clicks
            sidebarLinks.forEach((link, index) => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Sidebar link clicked:', index);
                    showTab(index);
                });
            });
            
            // Handle top tab clicks
            tabButtons.forEach((tab, index) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Top tab clicked:', index);
                    showTab(index);
                });
            });
            
            // Initialize with first tab active
            showTab(0);
            
            // Make showTab function globally available for testing
            window.showTab = showTab;
        });
        
        // Helper functions
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        
        function get_file_icon(file_type) {
            const iconMap = {
                'pdf': 'fa-file-pdf', 'doc': 'fa-file-word', 'docx': 'fa-file-word',
                'xls': 'fa-file-excel', 'xlsx': 'fa-file-excel', 'ppt': 'fa-file-powerpoint',
                'pptx': 'fa-file-powerpoint', 'jpg': 'fa-file-image', 'jpeg': 'fa-file-image',
                'png': 'fa-file-image', 'gif': 'fa-file-image', 'mp4': 'fa-file-video'
            };
            return iconMap[file_type.toLowerCase()] || 'fa-file';
        }
        
        function ella_character_limiter(str, length) {
            if (str.length <= length) return str;
            return str.substring(0, length) + '...';
        }
        
        // Sharing functionality
        function copyPortalLink() {
            const portalLink = document.getElementById('portalLink').value;
            navigator.clipboard.writeText(portalLink).then(function() {
                showToast('Link copied to clipboard!', 'success');
            }).catch(function() {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = portalLink;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showToast('Link copied to clipboard!', 'success');
            });
        }
        
        function sharePortal() {
            const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
            shareModal.show();
            
            // Generate QR code when modal opens
            setTimeout(() => {
                generateQRCode();
            }, 100);
        }
        
        function generateQRCode() {
            const portalLink = document.getElementById('portalLink').value;
            const qrContainer = document.getElementById('qrCode');
            
            // Clear previous QR code
            qrContainer.innerHTML = '';
            
            // Generate new QR code
            QRCode.toCanvas(qrContainer, portalLink, {
                width: 150,
                height: 150,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }, function (error) {
                if (error) {
                    qrContainer.innerHTML = '<div class="text-muted">QR Code generation failed</div>';
                }
            });
        }
        
        function shareViaEmail() {
            const portalLink = document.getElementById('portalLink').value;
            const subject = 'Client Portal Access - <?= $contract['title'] ?>';
            const body = `Hello,\n\nYou can access the client portal for your contract using this link:\n\n${portalLink}\n\nThis portal contains all the information about your project including:\n- Contract details\n- Project progress\n- Media files\n- Documents\n- Estimates\n\nBest regards,\nElla Contractors Team`;
            
            window.open(`mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`);
        }
        
        function shareViaWhatsApp() {
            const portalLink = document.getElementById('portalLink').value;
            const message = `Client Portal Access - <?= $contract['title'] ?>\n\nAccess your contract portal: ${portalLink}`;
            window.open(`https://wa.me/?text=${encodeURIComponent(message)}`);
        }
        
        function shareViaTelegram() {
            const portalLink = document.getElementById('portalLink').value;
            const message = `Client Portal Access - <?= $contract['title'] ?>\n\nAccess your contract portal: ${portalLink}`;
            window.open(`https://t.me/share/url?url=${encodeURIComponent(portalLink)}&text=${encodeURIComponent(message)}`);
        }
        
        function showToast(message, type = 'info') {
            // Create toast notification
            const toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            document.body.appendChild(toastContainer);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove toast container after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toastContainer);
            });
        }
    </script>
</body>
</html>
