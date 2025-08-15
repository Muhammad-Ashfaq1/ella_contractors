<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4><i class="fas fa-exclamation-triangle"></i> Error</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $error_message; ?></h5>
                        <p class="card-text">The Ella Contractors module encountered an error while loading.</p>
                        
                        <?php if (isset($error_details)): ?>
                        <div class="alert alert-info">
                            <strong>Technical Details:</strong><br>
                            <code><?php echo htmlspecialchars($error_details); ?></code>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="<?php echo admin_url('ella_contractors/dashboard'); ?>" class="btn btn-primary">Try Again</a>
                            <a href="<?php echo admin_url(); ?>" class="btn btn-secondary">Go to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
