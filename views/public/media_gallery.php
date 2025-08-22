<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .public-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .company-logo {
            max-height: 60px;
            max-width: 200px;
        }
        
        .gallery-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .gallery-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .gallery-subtitle {
            color: #7f8c8d;
            text-align: center;
            margin-bottom: 30px;
            font-size: 18px;
        }
        
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .media-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .media-icon-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
        }
        
        .media-icon {
            font-size: 48px;
            color: white;
        }
        
        .media-info-section {
            padding: 20px;
        }
        
        .media-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.4;
        }
        
        .media-meta {
            margin-bottom: 15px;
        }
        
        .media-meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .media-meta-item i {
            margin-right: 8px;
            width: 16px;
            color: #667eea;
        }
        
        .media-description {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #495057;
            font-size: 14px;
            border-left: 4px solid #667eea;
        }
        
        .media-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .media-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 80px;
        }
        
        .media-btn:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .media-btn-view {
            background: #3498db;
        }
        
        .media-btn-view:hover {
            background: #2980b9;
        }
        
        .media-btn-download {
            background: #27ae60;
        }
        
        .media-btn-download:hover {
            background: #229954;
        }
        
        .media-btn i {
            margin-right: 5px;
        }
        
        .summary-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
        }
        
        .summary-text {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .empty-icon {
            font-size: 64px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .empty-description {
            font-size: 16px;
            color: #7f8c8d;
        }
        
        @media (max-width: 768px) {
            .media-grid {
                grid-template-columns: 1fr;
            }
            
            .gallery-container {
                padding: 20px;
                margin: 15px;
            }
            
            .media-actions {
                flex-direction: column;
            }
            
            .media-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Public Header -->
    <div class="public-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3 mb-0 text-primary">
                        <i class="fas fa-images me-2"></i>
                        Media Gallery
                    </h1>
                    <p class="text-muted mb-0">Contract: <?= $proposal->subject ?></p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="me-3">
                            <small class="text-muted d-block">Contract ID</small>
                            <strong class="text-primary">#<?= $proposal->id ?></strong>
                        </div>
                        <div class="me-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-<?= $proposal->status == 1 ? 'success' : 'warning' ?>">
                                <?= $proposal->status == 1 ? 'Active' : 'Pending' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="gallery-container">
            <h2 class="gallery-title">
                <i class="fas fa-images me-2"></i>
                <?= $proposal->subject ?>
            </h2>
            <p class="gallery-subtitle">
                Media files and documents for this contract
            </p>
            
            <?php if (!empty($media_files)): ?>
                <div class="media-grid">
                    <?php foreach ($media_files as $media): ?>
                        <div class="media-card">
                            <!-- File Icon/Preview -->
                            <div class="media-icon-section">
                                                                                <div class="media-icon">
                                                    <i class="fas fa-file"></i>
                                                </div>
                            </div>
                            
                            <!-- File Info -->
                            <div class="media-info-section">
                                <h6 class="media-title">
                                    <?= character_limiter($media->original_name, 40) ?>
                                </h6>
                                
                                <div class="media-meta">
                                    <div class="media-meta-item">
                                        <i class="fas fa-hdd"></i>
                                        <?= $media->file_size >= 1073741824 ? number_format($media->file_size / 1073741824, 2) . ' GB' : ($media->file_size >= 1048576 ? number_format($media->file_size / 1048576, 2) . ' MB' : ($media->file_size >= 1024 ? number_format($media->file_size / 1024, 2) . ' KB' : $media->file_size . ' bytes')) ?>
                                    </div>
                                    <div class="media-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <?= date('m-d-Y g:i A', strtotime($media->date_uploaded)) ?>
                                    </div>
                                </div>
                                
                                <?php if ($media->description): ?>
                                    <div class="media-description">
                                        <?= character_limiter($media->description, 80) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Action Buttons -->
                                <div class="media-actions">
                                    <a href="<?= site_url("media-gallery/{$contract_id}/{$hash}/view/{$media->file_name}") ?>" 
                                       target="_blank" class="media-btn media-btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?= site_url("media-gallery/{$contract_id}/{$hash}/download/{$media->file_name}") ?>" 
                                       class="media-btn media-btn-download">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Summary -->
                <div class="summary-section">
                    <p class="summary-text mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Summary:</strong> 
                        <?= count($media_files) ?> media file(s) found. 
                        Total size: <strong><?php 
                            $total_size = 0;
                            foreach ($media_files as $media) {
                                $total_size += $media->file_size;
                            }
                            echo $total_size >= 1073741824 ? number_format($total_size / 1073741824, 2) . ' GB' : ($total_size >= 1048576 ? number_format($total_size / 1048576, 2) . ' MB' : ($total_size >= 1024 ? number_format($total_size / 1024, 2) . ' KB' : $total_size . ' bytes'));
                        ?></strong>
                    </p>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="empty-title">No Media Files Found</h3>
                    <p class="empty-description">
                        This contract doesn't have any media files uploaded yet.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center text-white mb-4">
        <small>
            <i class="fas fa-shield-alt me-1"></i>
            Secure access provided by Ella Contractors CRM
        </small>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
