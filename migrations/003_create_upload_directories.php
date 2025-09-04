<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_upload_directories extends App_module_migration
{
    public function up($db)
    {
        // Create upload directories for presentations
        $base_path = FCPATH . 'uploads/ella_presentations/';
        $directories = [
            $base_path,
            $base_path . 'default/',
            $base_path . 'general/',
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Create index.html to prevent directory listing
            if (!file_exists($dir . 'index.html')) {
                file_put_contents($dir . 'index.html', '');
            }
        }
    }

    public function down($db)
    {
        // Optionally remove directories (be careful with this)
        // $base_path = FCPATH . 'uploads/ella_presentations/';
        // if (is_dir($base_path)) {
        //     rmdir($base_path);
        // }
    }
}
