<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: EllaContractor
Description: Empty module - all functionality removed
Version: 1.0.0
Author: Custom
*/

$CI = &get_instance();

define('ELLA_CONTRACTORS_MODULE_NAME', 'ella_contractors');

// Register module menu
hooks()->add_action('admin_init', 'ella_contractors_init_menu');

// Register activation and deactivation hooks
register_activation_hook(ELLA_CONTRACTORS_MODULE_NAME, 'ella_contractors_activate_module');
register_deactivation_hook(ELLA_CONTRACTORS_MODULE_NAME, 'ella_contractors_deactivate_module');

/**
 * Initialize module menu
 */
function ella_contractors_init_menu() {
    $CI = &get_instance();
    if (is_staff_logged_in() && (is_super_admin() || is_admin() || has_permission('ella_contractors', '', 'view'))) {
        $CI->app_menu->add_sidebar_menu_item('ella_contractors', [
            'slug' => 'ella_contractors',
            'name' => 'EllaContractor',
            'icon' => 'fa fa-users',
            'position' => 30,
            'collapse' => true,
        ]);

        // Submenu items with icons
        $submenu = [
            [
                'slug' => 'ella_contractors_appointments',
                'name' => 'Appointments',
                'href' => admin_url('ella_contractors/appointments'),
                'icon' => 'fa fa-calendar-check-o',
                'position' => 10,
            ],
            [
                'slug' => 'ella_contractors_past_appointments',
                'name' => 'Past Appts',
                'href' => admin_url('ella_contractors/appointments/past'),
                'icon' => 'fa fa-history',
                'position' => 15,
            ],
            // [
            //     'slug' => 'ella_contractors_measurements',
            //     'name' => 'Measurements',
            //     'href' => admin_url('ella_contractors/measurements'),
            //     'icon' => 'fa fa-square-o',
            // ],
            [
                'slug' => 'ella_contractors_presentations',
                'name' => 'Presentations',
                'href' => admin_url('ella_contractors/presentations'),
                'icon' => 'fa fa-file-powerpoint-o',
                'position' => 20,
            ],
            [
                'slug' => 'ella_contractors_line_items',
                'name' => 'Service Items',
                'href' => admin_url('ella_contractors/line_items'),
                'icon' => 'fa fa-list-alt',
                'position' => 25,
            ],
            // [
            //     'slug' => 'ella_contractors_estimates',
            //     'name' => 'Estimates',
            //     'href' => admin_url('ella_contractors/estimates'),
            //     'icon' => 'fa fa-file-text-o',
            //     'position' => 30,
            // ],
            
        ];

        foreach ($submenu as $item) {
            $CI->app_menu->add_sidebar_children_item('ella_contractors', $item);
        }
    }
}

function ella_contractors_activate_module() {
    $CI = &get_instance();
    
    // Ensure PPT and PPTX files are allowed for upload
    $allowed_files = get_option('allowed_files');
    if ($allowed_files) {
        $allowed_extensions = explode(',', $allowed_files);
        $allowed_extensions = array_map('trim', $allowed_extensions);
        
        if (!in_array('.ppt', $allowed_extensions)) {
            $allowed_extensions[] = '.ppt';
        }
        if (!in_array('.pptx', $allowed_extensions)) {
            $allowed_extensions[] = '.pptx';
        }
        
        update_option('allowed_files', implode(',', $allowed_extensions));
    } else {
        // Set default allowed files if not set
        add_option('allowed_files', '.pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar');
    }
    
    // Create ella_media_folders table
    if (!$CI->db->table_exists(db_prefix() . 'ella_media_folders')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_media_folders` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `lead_id` int(11) DEFAULT NULL,
            `is_default` tinyint(1) DEFAULT 0,
            `active` tinyint(1) DEFAULT 1,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `lead_id` (`lead_id`),
            KEY `is_default` (`is_default`),
            KEY `active` (`active`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }
    
    // Create ella_contractor_media table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_media')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_media` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `folder_id` int(11) DEFAULT NULL,
            `lead_id` int(11) DEFAULT NULL,
            `file_name` varchar(255) NOT NULL,
            `original_name` varchar(255) NOT NULL,
            `file_type` varchar(100) NOT NULL,
            `file_size` int(11) NOT NULL,
            `description` text,
            `is_default` tinyint(1) DEFAULT 0,
            `active` tinyint(1) DEFAULT 1,
            `date_uploaded` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `folder_id` (`folder_id`),
            KEY `lead_id` (`lead_id`),
            KEY `is_default` (`is_default`),
            KEY `active` (`active`),
            KEY `file_type` (`file_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }
    
    // Create ella_contractor_line_item_groups table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_line_item_groups')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `description` text,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `is_active` (`is_active`),
            KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }
    
    // Create ella_contractor_line_items table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_line_items')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_line_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `description` text,
            `image` varchar(255) DEFAULT NULL,
            `cost` decimal(10,2) DEFAULT NULL,
            `quantity` decimal(10,2) DEFAULT 1.00,
            `unit_type` varchar(50) NOT NULL,
            `group_id` int(11) DEFAULT 0,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `is_active` (`is_active`),
            KEY `name` (`name`),
            KEY `unit_type` (`unit_type`),
            KEY `group_id` (`group_id`),
            FOREIGN KEY (`group_id`) REFERENCES `' . db_prefix() . 'ella_contractor_line_item_groups`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        // Check if group_id column exists, if not add it
        if (!$CI->db->field_exists('group_id', db_prefix() . 'ella_contractor_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD COLUMN `group_id` int(11) DEFAULT 0 AFTER `unit_type`');
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD KEY `group_id` (`group_id`)');
        }
        
        // Check if group_name column exists, if it does remove it
        if ($CI->db->field_exists('group_name', db_prefix() . 'ella_contractor_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` DROP COLUMN `group_name`');
        }
    }
    
    // Create ella_contractor_estimates table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_estimates')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_estimates` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `estimate_name` varchar(255) NOT NULL,
            `description` text,
            `client_id` int(11) DEFAULT NULL,
            `lead_id` int(11) DEFAULT NULL,
            `appointment_id` int(11) DEFAULT NULL,
            `status` enum(\'draft\',\'sent\',\'accepted\',\'rejected\',\'expired\') DEFAULT \'draft\',
            `total_amount` decimal(10,2) DEFAULT 0.00,
            `total_quantity` decimal(10,2) DEFAULT 0.00,
            `line_items_count` int(11) DEFAULT 0,
            `created_by` int(11) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `client_id` (`client_id`),
            KEY `lead_id` (`lead_id`),
            KEY `appointment_id` (`appointment_id`),
            KEY `status` (`status`),
            KEY `created_by` (`created_by`),
            KEY `created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        // Check if appointment_id column exists, if not add it
        if (!$CI->db->field_exists('appointment_id', db_prefix() . 'ella_contractor_estimates')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD COLUMN `appointment_id` int(11) DEFAULT NULL AFTER `lead_id`');
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD KEY `appointment_id` (`appointment_id`)');
        }
    }
    
    // Create ella_contractor_estimate_line_items table (pivot table)
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_estimate_line_items')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `estimate_id` int(11) NOT NULL,
            `line_item_id` int(11) NOT NULL,
            `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
            `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `estimate_id` (`estimate_id`),
            KEY `line_item_id` (`line_item_id`),
            UNIQUE KEY `unique_estimate_line_item` (`estimate_id`, `line_item_id`),
            FOREIGN KEY (`estimate_id`) REFERENCES `' . db_prefix() . 'ella_contractor_estimates`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`line_item_id`) REFERENCES `' . db_prefix() . 'ella_contractor_line_items`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }

    // Create ella_contractors_measurements table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractors_measurements')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractors_measurements` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `category` ENUM(\'windows\',\'doors\',\'roofing\',\'siding\',\'other\') NOT NULL DEFAULT \'other\',
            `rel_type` ENUM(\'customer\',\'lead\',\'project\',\'appointment\',\'other\') NOT NULL DEFAULT \'customer\',
            `rel_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `appointment_id` BIGINT UNSIGNED NULL DEFAULT NULL,
            `designator` VARCHAR(64) NULL,
            `name` VARCHAR(191) NOT NULL,
            `location_label` VARCHAR(128) NULL,
            `level_label` VARCHAR(64) NULL,
            `quantity` DECIMAL(12,4) NOT NULL DEFAULT 1.0000,
            `width_val` DECIMAL(12,4) NULL,
            `height_val` DECIMAL(12,4) NULL,
            `length_val` DECIMAL(12,4) NULL,
            `area_val` DECIMAL(14,4) NULL,
            `united_inches_val` DECIMAL(12,4) NULL,
            `length_unit` VARCHAR(16) NULL DEFAULT \'in\',
            `area_unit` VARCHAR(16) NULL DEFAULT \'sqft\',
            `ui_unit` VARCHAR(16) NULL DEFAULT \'in\',
            `attributes_json` JSON NULL,
            `notes` TEXT NULL,
            `status_code` TINYINT NOT NULL DEFAULT 1,
            `sort_order` INT NOT NULL DEFAULT 0,
            `dtmCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `dtmLastM` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `intCreatedByCode` INT NULL,
            `intAlteredByCode` INT NULL,
            `intRecordStatusCode` TINYINT NOT NULL DEFAULT 1,
            `intBranchCode` INT NULL,
            `intCompanyCode` INT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_cat_rel` (`category`,`rel_type`,`rel_id`),
            KEY `idx_appointment` (`appointment_id`),
            KEY `idx_location_level` (`location_label`,`level_label`),
            KEY `idx_created` (`dtmCreated`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        // Check if appointment_id column exists, if not add it
        if (!$CI->db->field_exists('appointment_id', db_prefix() . 'ella_contractors_measurements')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractors_measurements` ADD COLUMN `appointment_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_id`');
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractors_measurements` ADD KEY `idx_appointment` (`appointment_id`)');
        }
        
        // Check if 'appointment' is in rel_type enum, if not add it
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractors_measurements` MODIFY COLUMN `rel_type` ENUM(\'customer\',\'lead\',\'project\',\'appointment\',\'other\') NOT NULL DEFAULT \'customer\'');
    }
    
    // Create upload directories
    $base_path = FCPATH . 'uploads/ella_presentations/';
    $directories = [
        $base_path,
        $base_path . 'default/',
        $base_path . 'general/',
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                log_message('error', 'Failed to create directory: ' . $dir);
            }
        }
        
        // Create index.html to prevent directory listing
        if (!file_exists($dir . 'index.html')) {
            file_put_contents($dir . 'index.html', '');
        }
        
        // Create .htaccess to prevent direct access
        if (!file_exists($dir . '.htaccess')) {
            file_put_contents($dir . '.htaccess', 'Order Deny,Allow' . PHP_EOL . 'Deny from all');
        }
    }
    
    // Create line items image upload directory
    $line_items_path = FCPATH . 'uploads/ella_line_items/';
    if (!is_dir($line_items_path)) {
        if (!mkdir($line_items_path, 0755, true)) {
            log_message('error', 'Failed to create directory: ' . $line_items_path);
        }
    }
    
    // Create index.html to prevent directory listing
    if (!file_exists($line_items_path . 'index.html')) {
        file_put_contents($line_items_path . 'index.html', '');
    }
    
    // Create .htaccess to prevent direct access
    if (!file_exists($line_items_path . '.htaccess')) {
        file_put_contents($line_items_path . '.htaccess', 'Order Deny,Allow' . PHP_EOL . 'Deny from all');
    }
    
    // Insert default groups
    $default_groups = [
        ['name' => 'Roofing', 'description' => 'Roofing materials and services'],
        ['name' => 'Doors', 'description' => 'Door installation and materials'],
        ['name' => 'Windows', 'description' => 'Window installation and materials'],
        ['name' => 'Siding', 'description' => 'Siding materials and installation'],
        ['name' => 'Walls', 'description' => 'Wall construction and finishing'],
        ['name' => 'General', 'description' => 'General construction items']
    ];
    
    $existing_groups = $CI->db->count_all_results(db_prefix() . 'ella_contractor_line_item_groups');
    if ($existing_groups == 0) {
        foreach ($default_groups as $group) {
            $CI->db->insert(db_prefix() . 'ella_contractor_line_item_groups', $group);
        }
    }
    
    // Set module version
    update_option('ella_contractors_version', '1.0.0');
    
}

function ella_contractors_deactivate_module() {
    $CI = &get_instance();

    // Rollback code - commented out for now, uncomment when needed
    // Drop tables if they exist
    // if ($CI->db->table_exists(db_prefix() . 'ella_contractor_media')) {
    //     $CI->db->query('DROP TABLE `' . db_prefix() . 'ella_contractor_media`');
    // }
    
    // if ($CI->db->table_exists(db_prefix() . 'ella_media_folders')) {
    //     $CI->db->query('DROP TABLE `' . db_prefix() . 'ella_media_folders`');
    // }
    
    // Optionally remove upload directories (be careful with this)
    // $base_path = FCPATH . 'uploads/ella_presentations/';
    // if (is_dir($base_path)) {
    //     rmdir($base_path);
    // }
}


// Register module language files
register_language_files(ELLA_CONTRACTORS_MODULE_NAME, ['ella_contractors']);
