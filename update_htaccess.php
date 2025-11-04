<?php
/**
 * Update .htaccess files in ella_presentations directory to allow public access
 * This is needed for external viewers (Microsoft Office Online, Google Docs) to access PPT/PPTX files
 * 
 * Run this file once after updating the module:
 * URL: https://your-domain.com/modules/ella_contractors/update_htaccess.php?confirm=yes
 * OR via command line: php update_htaccess.php
 */

// Security check - only allow access from localhost or authenticated admin
$allowed = false;

// Check if running from command line
if (php_sapi_name() === 'cli') {
    $allowed = true;
}

// Check if running from localhost
if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    $allowed = true;
}

// Check if accessed via web and user is admin (basic check)
if (!$allowed && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $allowed = true;
}

if (!$allowed) {
    die('Access denied. To run this script, add ?confirm=yes to the URL or run from command line.');
}

// Define paths
$base_path = dirname(dirname(dirname(__FILE__))) . '/uploads/ella_presentations/';
$directories = [
    $base_path,
    $base_path . 'default/',
    $base_path . 'general/',
    $base_path . 'cache/',
];

$new_htaccess_content = '# Allow public access to presentation files for external viewers
Order Allow,Deny
Allow from all

# Prevent directory listing
Options -Indexes

# Set correct MIME types for PowerPoint files
AddType application/vnd.ms-powerpoint .ppt
AddType application/vnd.openxmlformats-officedocument.presentationml.presentation .pptx
AddType application/pdf .pdf
AddType text/html .html';

$results = [];

foreach ($directories as $dir) {
    $htaccess_path = $dir . '.htaccess';
    
    // Create directory if it doesn't exist
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            $results[] = "✓ Created directory: $dir";
        } else {
            $results[] = "✗ Failed to create directory: $dir";
            continue;
        }
    }
    
    // Check if .htaccess exists
    if (file_exists($htaccess_path)) {
        $existing_content = file_get_contents($htaccess_path);
        
        // Check if it has restrictive rules
        if (strpos($existing_content, 'Deny from all') !== false) {
            if (file_put_contents($htaccess_path, $new_htaccess_content)) {
                $results[] = "✓ Updated restrictive .htaccess: $htaccess_path";
            } else {
                $results[] = "✗ Failed to update .htaccess: $htaccess_path";
            }
        } else {
            $results[] = "→ .htaccess already allows access: $htaccess_path";
        }
    } else {
        // Create new .htaccess
        if (file_put_contents($htaccess_path, $new_htaccess_content)) {
            $results[] = "✓ Created new .htaccess: $htaccess_path";
        } else {
            $results[] = "✗ Failed to create .htaccess: $htaccess_path";
        }
    }
}

// Output results
if (php_sapi_name() === 'cli') {
    // Command line output
    echo "\n=== Ella Contractors .htaccess Update ===\n\n";
    foreach ($results as $result) {
        echo $result . "\n";
    }
    echo "\n=== Update Complete ===\n\n";
} else {
    // Web output
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Ella Contractors .htaccess Update</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
            .result { padding: 10px; margin: 10px 0; border-radius: 4px; border-left: 4px solid #ccc; }
            .result.success { background: #d4edda; border-color: #28a745; color: #155724; }
            .result.error { background: #f8d7da; border-color: #dc3545; color: #721c24; }
            .result.info { background: #d1ecf1; border-color: #17a2b8; color: #0c5460; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #7f8c8d; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 Ella Contractors .htaccess Update</h1>
            
            <h2>Update Results:</h2>
            <?php foreach ($results as $result): ?>
                <?php
                $class = 'info';
                if (strpos($result, '✓') === 0) {
                    $class = 'success';
                } elseif (strpos($result, '✗') === 0) {
                    $class = 'error';
                }
                ?>
                <div class="result <?php echo $class; ?>">
                    <?php echo htmlspecialchars($result); ?>
                </div>
            <?php endforeach; ?>
            
            <div class="footer">
                <p><strong>✅ Update Complete!</strong></p>
                <p>Your presentation files should now be accessible to external viewers.</p>
                <p><em>You can delete this file after running it.</em></p>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>

