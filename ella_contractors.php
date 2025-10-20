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

// Register module assets
hooks()->add_action('admin_init', 'ella_contractors_init_assets');
hooks()->add_action('app_admin_head', 'ella_contractors_load_global_css');

// Load timeline helper
hooks()->add_action('init', 'ella_contractors_load_helpers');

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
            'icon' => 'modules/ella_contractors/assets/images/ella-con1.png',
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
                'href' => admin_url('invoice_items?service_items=true'),
                'icon' => 'fa fa-list-alt',
                'position' => 25,
            ],
            
        ];

        foreach ($submenu as $item) {
            $CI->app_menu->add_sidebar_children_item('ella_contractors', $item);
        }
    }
}

/**
 * Initialize module assets
 */
function ella_contractors_init_assets() {
    // CSS assets are loaded via hook to ensure proper timing
    // The CSS file is loaded in individual views where needed
}

/**
 * Load global CSS for sidebar styling consistency
 */
function ella_contractors_load_global_css() {
    echo '<link href="' . module_dir_url(ELLA_CONTRACTORS_MODULE_NAME, 'assets/css/ella-contractors.css') . '" rel="stylesheet" type="text/css">';
    
    // Add JavaScript fix for EllaContractor icon red color issue
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fix EllaContractor icon red color issue
        function fixEllaContractorIcon() {
            var ellaIcon = document.querySelector("#side-menu li.menu-item-ella_contractors a img[src*=\"ella-con1.png\"]");
            if (ellaIcon) {
                // Remove any problematic classes or inline styles
                ellaIcon.style.filter = "none";
                ellaIcon.style.webkitFilter = "none";
                ellaIcon.style.mozFilter = "none";
                ellaIcon.style.msFilter = "none";
                ellaIcon.style.background = "none";
                ellaIcon.style.backgroundColor = "transparent";
                ellaIcon.style.color = "inherit";
                ellaIcon.style.opacity = "1";
                ellaIcon.style.mixBlendMode = "normal";
                ellaIcon.style.webkitBackgroundClip = "unset";
                ellaIcon.style.backgroundClip = "unset";
                
                // Ensure proper dimensions and spacing
                ellaIcon.style.width = "18px";
                ellaIcon.style.height = "18px";
                ellaIcon.style.objectFit = "contain";
                ellaIcon.style.marginRight = "16px";
                ellaIcon.style.display = "block";
                ellaIcon.style.float = "left";
            }
        }
        
        // Run immediately
        fixEllaContractorIcon();
        
        // Run again after a short delay to catch dynamically loaded content
        setTimeout(fixEllaContractorIcon, 100);
        
        // Run when menu items are updated
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === "childList") {
                    fixEllaContractorIcon();
                }
            });
        });
        
        var menuContainer = document.querySelector("#side-menu");
        if (menuContainer) {
            observer.observe(menuContainer, { childList: true, subtree: true });
        }
    });
    </script>';
}

/**
 * Load module helpers
 */
function ella_contractors_load_helpers() {
    $CI = &get_instance();
    
    // Load timeline helper
    $CI->load->helper('ella_contractors/ella_timeline_helper');
    
    // Load appointments helper
    $CI->load->helper('ella_contractors/ella_appointments_helper');
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
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` bigint unsigned DEFAULT NULL,
            `org_id` bigint unsigned DEFAULT NULL,
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
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
            KEY `folder_id` (`folder_id`),
            KEY `lead_id` (`lead_id`),
            KEY `is_default` (`is_default`),
            KEY `active` (`active`),
            KEY `file_type` (`file_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        // Add rel_type, rel_id, org_id columns if they don't exist
        if (!$CI->db->field_exists('rel_type', db_prefix() . 'ella_contractor_media')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD COLUMN `rel_type` VARCHAR(50) NULL DEFAULT NULL AFTER `id`');
        }
        if (!$CI->db->field_exists('rel_id', db_prefix() . 'ella_contractor_media')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD COLUMN `rel_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_type`');
        }
        if (!$CI->db->field_exists('org_id', db_prefix() . 'ella_contractor_media')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD COLUMN `org_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_id`');
        }
        
        // Add indexes if they don't exist (only for existing tables)
        if ($CI->db->table_exists(db_prefix() . 'ella_contractor_media')) {
            // Check if indexes exist before adding them
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_media` WHERE Key_name = 'idx_rel_type_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD INDEX `idx_rel_type_id` (`rel_type`, `rel_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
            
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_media` WHERE Key_name = 'idx_org_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD INDEX `idx_org_id` (`org_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
        }
    }
    
    // Create ella_contractor_line_item_groups table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_line_item_groups')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` bigint unsigned DEFAULT NULL,
            `org_id` bigint unsigned DEFAULT NULL,
            `name` varchar(255) NOT NULL,
            `description` text,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
            KEY `is_active` (`is_active`),
            KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        // Add rel_type, rel_id, org_id columns if they don't exist
        if (!$CI->db->field_exists('rel_type', db_prefix() . 'ella_contractor_line_item_groups')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` ADD COLUMN `rel_type` VARCHAR(50) NULL DEFAULT NULL AFTER `id`');
        }
        if (!$CI->db->field_exists('rel_id', db_prefix() . 'ella_contractor_line_item_groups')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` ADD COLUMN `rel_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_type`');
        }
        if (!$CI->db->field_exists('org_id', db_prefix() . 'ella_contractor_line_item_groups')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` ADD COLUMN `org_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_id`');
        }
        
        // Add indexes if they don't exist (only for existing tables)
        if ($CI->db->table_exists(db_prefix() . 'ella_contractor_line_item_groups')) {
            // Check if indexes exist before adding them
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_line_item_groups` WHERE Key_name = 'idx_rel_type_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` ADD INDEX `idx_rel_type_id` (`rel_type`, `rel_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
            
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_line_item_groups` WHERE Key_name = 'idx_org_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_item_groups` ADD INDEX `idx_org_id` (`org_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
        }
    }
    
    // Create ella_contractor_line_items table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_line_items')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_line_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` bigint unsigned DEFAULT NULL,
            `org_id` bigint unsigned DEFAULT NULL,
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
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
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
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD KEY `group_id` (`group_id`)');
            } catch (Exception $e) {
                // Key might already exist, ignore error
            }
        }
        
        // Check if group_name column exists, if it does remove it
        if ($CI->db->field_exists('group_name', db_prefix() . 'ella_contractor_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` DROP COLUMN `group_name`');
        }
        
        // Add rel_type, rel_id, org_id columns if they don't exist
        if (!$CI->db->field_exists('rel_type', db_prefix() . 'ella_contractor_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD COLUMN `rel_type` VARCHAR(50) NULL DEFAULT NULL AFTER `id`');
        }
        if (!$CI->db->field_exists('rel_id', db_prefix() . 'ella_contractor_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD COLUMN `rel_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_type`');
        }
        if (!$CI->db->field_exists('org_id', db_prefix() . 'ella_contractor_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD COLUMN `org_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_id`');
        }
        
        // Add indexes if they don't exist (only for existing tables)
        if ($CI->db->table_exists(db_prefix() . 'ella_contractor_line_items')) {
            // Check if indexes exist before adding them
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_line_items` WHERE Key_name = 'idx_rel_type_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD INDEX `idx_rel_type_id` (`rel_type`, `rel_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
            
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_line_items` WHERE Key_name = 'idx_org_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_line_items` ADD INDEX `idx_org_id` (`org_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
        }
    }
    
    // Create ella_contractor_estimates table
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_estimates')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_estimates` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` bigint unsigned DEFAULT NULL,
            `org_id` bigint unsigned DEFAULT NULL,
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
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
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
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD KEY `appointment_id` (`appointment_id`)');
            } catch (Exception $e) {
                // Key might already exist, ignore error
            }
        }
        
        // Add rel_type, rel_id, org_id columns if they don't exist
        if (!$CI->db->field_exists('rel_type', db_prefix() . 'ella_contractor_estimates')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD COLUMN `rel_type` VARCHAR(50) NULL DEFAULT NULL AFTER `id`');
        }
        if (!$CI->db->field_exists('rel_id', db_prefix() . 'ella_contractor_estimates')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD COLUMN `rel_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_type`');
        }
        if (!$CI->db->field_exists('org_id', db_prefix() . 'ella_contractor_estimates')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD COLUMN `org_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_id`');
        }
        
        // Add indexes if they don't exist (only for existing tables)
        if ($CI->db->table_exists(db_prefix() . 'ella_contractor_estimates')) {
            // Check if indexes exist before adding them
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_estimates` WHERE Key_name = 'idx_rel_type_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD INDEX `idx_rel_type_id` (`rel_type`, `rel_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
            
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_estimates` WHERE Key_name = 'idx_org_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimates` ADD INDEX `idx_org_id` (`org_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
        }
    }
    
    // Create ella_contractor_estimate_line_items table (pivot table)
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_estimate_line_items')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` bigint unsigned DEFAULT NULL,
            `org_id` bigint unsigned DEFAULT NULL,
            `estimate_id` int(11) NOT NULL,
            `line_item_id` int(11) NOT NULL,
            `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
            `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
            KEY `estimate_id` (`estimate_id`),
            KEY `line_item_id` (`line_item_id`),
            UNIQUE KEY `unique_estimate_line_item` (`estimate_id`, `line_item_id`),
            FOREIGN KEY (`estimate_id`) REFERENCES `' . db_prefix() . 'ella_contractor_estimates`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`line_item_id`) REFERENCES `' . db_prefix() . 'ella_contractor_line_items`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        // Add rel_type, rel_id, org_id columns if they don't exist
        if (!$CI->db->field_exists('rel_type', db_prefix() . 'ella_contractor_estimate_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` ADD COLUMN `rel_type` VARCHAR(50) NULL DEFAULT NULL AFTER `id`');
        }
        if (!$CI->db->field_exists('rel_id', db_prefix() . 'ella_contractor_estimate_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` ADD COLUMN `rel_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_type`');
        }
        if (!$CI->db->field_exists('org_id', db_prefix() . 'ella_contractor_estimate_line_items')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` ADD COLUMN `org_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `rel_id`');
        }
        
        // Add indexes if they don't exist (only for existing tables)
        if ($CI->db->table_exists(db_prefix() . 'ella_contractor_estimate_line_items')) {
            // Check if indexes exist before adding them
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_estimate_line_items` WHERE Key_name = 'idx_rel_type_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` ADD INDEX `idx_rel_type_id` (`rel_type`, `rel_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
            
            $indexes = $CI->db->query("SHOW INDEX FROM `" . db_prefix() . "ella_contractor_estimate_line_items` WHERE Key_name = 'idx_org_id'")->result();
            if (empty($indexes)) {
                try {
                    $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_estimate_line_items` ADD INDEX `idx_org_id` (`org_id`)');
                } catch (Exception $e) {
                    // Index might already exist, ignore error
                }
            }
        }
    }

    // Create ella_contractor_measurement_records table - New dynamic tab structure
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_measurement_records')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_measurement_records` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` bigint unsigned DEFAULT NULL,
            `org_id` bigint unsigned DEFAULT NULL,
            `appointment_id` int(11) DEFAULT NULL,
            `tab_name` varchar(255) NOT NULL,
            `created_by` int(11) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
            KEY `idx_appointment_id` (`appointment_id`),
            KEY `idx_tab_name` (`tab_name`),
            KEY `idx_created_by` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
        
        log_message('info', 'Ella Contractors - Created ella_contractor_measurement_records table');
    }
    
    // Create ella_contractor_measurement_items table - Individual measurements within each record
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_measurement_items')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_measurement_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `measurement_record_id` int(11) NOT NULL,
            `name` varchar(255) NOT NULL,
            `value` decimal(12,4) NOT NULL DEFAULT 0.0000,
            `unit` varchar(50) NOT NULL,
            `sort_order` int(11) DEFAULT 0,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_measurement_record_id` (`measurement_record_id`),
            KEY `idx_sort_order` (`sort_order`),
            FOREIGN KEY (`measurement_record_id`) REFERENCES `' . db_prefix() . 'ella_contractor_measurement_records`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
        
        log_message('info', 'Ella Contractors - Created ella_contractor_measurement_items table');
    }


    // Add Appointment ID Column in proposals Table starts here 

    if (!$CI->db->field_exists('appointment_id', db_prefix() . 'proposals')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'proposals` ADD COLUMN `appointment_id` int(11) DEFAULT NULL AFTER `org_id`');
        try {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'proposals` ADD KEY `appointment_id` (`appointment_id`)');
        } catch (Exception $e) {
            // Key might already exist, ignore error
        }
    }

    // Add Appointment ID Column in proposals Table ends here 
    
    
    
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
    
        // Add appointment_status column to appointly_appointments table if it doesn't exist
        if ($CI->db->field_exists('appointment_status', db_prefix() . 'appointly_appointments')) {
            log_message('info', 'appointment_status column already exists in appointly_appointments table');
        } else {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `appointment_status` ENUM(\'scheduled\',\'cancelled\',\'complete\') NULL DEFAULT \'scheduled\' AFTER `cancelled`');
            log_message('info', 'Added appointment_status column to appointly_appointments table');
            
            // Update existing records based on old boolean fields
            $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "cancelled" WHERE `cancelled` = 1');
            $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "complete" WHERE `finished` = 1 OR `approved` = 1');
            $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "scheduled" WHERE `appointment_status` IS NULL');
            log_message('info', 'Updated existing appointment records with new status values');
        }
        
        // Add end_date and end_time columns to appointly_appointments table if they don't exist
        if (!$CI->db->field_exists('end_date', db_prefix() . 'appointly_appointments')) {
            try {
                // Add end_date column
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `end_date` DATE NULL AFTER `date`');
                log_message('info', 'Ella Appointments - Created end_date column');
            } catch (Exception $e) {
                log_message('error', 'Ella Appointments - Error creating end_date column: ' . $e->getMessage());
            }
        }
        
        if (!$CI->db->field_exists('end_time', db_prefix() . 'appointly_appointments')) {
            try {
                // Add end_time column
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `end_time` TIME NULL AFTER `start_hour`');
                log_message('info', 'Ella Appointments - Created end_time column');
            } catch (Exception $e) {
                log_message('error', 'Ella Appointments - Error creating end_time column: ' . $e->getMessage());
            }
        }
        
        // Add send_reminder column to appointly_appointments table if it doesn't exist
        if (!$CI->db->field_exists('send_reminder', db_prefix() . 'appointly_appointments')) {
            try {
                // Add send_reminder column
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `send_reminder` TINYINT(1) DEFAULT 0 AFTER `appointment_status`');
                log_message('info', 'Ella Appointments - Created send_reminder column');
            } catch (Exception $e) {
                log_message('error', 'Ella Appointments - Error creating send_reminder column: ' . $e->getMessage());
            }
        }
    
    // Create ella_appointment_activity_log table for timeline tracking
    if (!$CI->db->table_exists(db_prefix() . 'ella_appointment_activity_log')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_appointment_activity_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) NOT NULL DEFAULT "appointment",
            `rel_id` int(11) NOT NULL,
            `org_id` int(11) DEFAULT NULL,
            `staff_id` int(11) NOT NULL,
            `description` varchar(500) NOT NULL,
            `description_key` varchar(100) NOT NULL,
            `additional_data` text,
            `date` datetime NOT NULL,
            `full_name` varchar(255) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
            KEY `idx_staff_id` (`staff_id`),
            KEY `idx_date` (`date`),
            KEY `idx_rel_id` (`rel_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
        
        log_message('info', 'Ella Appointments - Created ella_appointment_activity_log table');
    }
    
    // Set module version
    update_option('ella_contractors_version', '1.0.0');
    
}

function ella_contractors_deactivate_module() {
    $CI = &get_instance();

    // ========================================
    // COLUMN DROPPING FUNCTIONALITY
    // ========================================
    // Uncomment the code below when you need to remove rel_type, rel_id, org_id columns
    // during module deactivation. This will clean up the database structure.
    
    /*
    // List of EllaContractor tables that have the new columns
    $tables = [
        'ella_contractor_media',
        'ella_contractor_line_item_groups',
        'ella_contractor_line_items', 
        'ella_contractor_estimates',
        'ella_contractor_estimate_line_items',
        'ella_contractors_measurements'
    ];
    
    foreach ($tables as $table) {
        $full_table_name = db_prefix() . $table;
        
        // Check if table exists before attempting to drop columns
        if (!$CI->db->table_exists($full_table_name)) {
            log_message('info', "Table {$full_table_name} does not exist, skipping column drops");
            continue;
        }
        
        log_message('info', "Processing table for column drops: {$full_table_name}");
        
        // Drop indexes first (to avoid constraint issues)
        try {
            $CI->db->query("ALTER TABLE `{$full_table_name}` DROP INDEX IF EXISTS `idx_rel_type_id`");
            log_message('info', "Dropped idx_rel_type_id index from {$full_table_name}");
        } catch (Exception $e) {
            log_message('debug', "Index idx_rel_type_id may not exist on {$full_table_name}: " . $e->getMessage());
        }
        
        try {
            $CI->db->query("ALTER TABLE `{$full_table_name}` DROP INDEX IF EXISTS `idx_org_id`");
            log_message('info', "Dropped idx_org_id index from {$full_table_name}");
        } catch (Exception $e) {
            log_message('debug', "Index idx_org_id may not exist on {$full_table_name}: " . $e->getMessage());
        }
        
        // Drop columns in reverse order (org_id, rel_id, rel_type)
        if ($CI->db->field_exists('org_id', $full_table_name)) {
            $CI->db->query("ALTER TABLE `{$full_table_name}` DROP COLUMN `org_id`");
            log_message('info', "Dropped org_id column from {$full_table_name}");
        }
        
        if ($CI->db->field_exists('rel_id', $full_table_name)) {
            $CI->db->query("ALTER TABLE `{$full_table_name}` DROP COLUMN `rel_id`");
            log_message('info', "Dropped rel_id column from {$full_table_name}");
        }
        
        if ($CI->db->field_exists('rel_type', $full_table_name)) {
            $CI->db->query("ALTER TABLE `{$full_table_name}` DROP COLUMN `rel_type`");
            log_message('info', "Dropped rel_type column from {$full_table_name}");
        }
        
        log_message('info', "Completed column drops for table: {$full_table_name}");
    }
    
    log_message('info', 'EllaContractor column deactivation completed successfully');
    */
    
    // ========================================
    // TABLE DROPPING FUNCTIONALITY  
    // ========================================
    // Uncomment the code below when you need to completely remove all EllaContractor tables
    // during module deactivation. WARNING: This will delete all data!
    
    /*
    // Drop tables if they exist (in reverse dependency order)
    $tables_to_drop = [
        'ella_contractor_estimate_line_items',  // Pivot table first
        'ella_contractor_estimates',
        'ella_contractor_line_items',
        'ella_contractor_line_item_groups',
        'ella_contractors_measurements',
        'ella_contractor_media',
        'ella_media_folders'
    ];
    
    foreach ($tables_to_drop as $table) {
        $full_table_name = db_prefix() . $table;
        if ($CI->db->table_exists($full_table_name)) {
            $CI->db->query("DROP TABLE `{$full_table_name}`");
            log_message('info', "Dropped table: {$full_table_name}");
        }
    }
    */
    
    // ========================================
    // DIRECTORY CLEANUP FUNCTIONALITY
    // ========================================
    // Uncomment the code below when you need to remove upload directories
    // during module deactivation. WARNING: This will delete all uploaded files!
    
    /*
    // Remove upload directories (be careful with this)
    $directories_to_remove = [
        FCPATH . 'uploads/ella_presentations/',
        FCPATH . 'uploads/ella_line_items/'
    ];
    
    foreach ($directories_to_remove as $dir) {
        if (is_dir($dir)) {
            // Remove all files in directory first
            $files = glob($dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            // Remove directory
            rmdir($dir);
            log_message('info', "Removed directory: {$dir}");
        }
    }
    */
    
    // ========================================
    // OPTION CLEANUP FUNCTIONALITY
    // ========================================
    // Uncomment the code below when you need to remove module options
    // during module deactivation
    
    /*
    // Remove module-specific options
    $options_to_remove = [
        'ella_contractors_version'
    ];
    
    foreach ($options_to_remove as $option) {
        delete_option($option);
        log_message('info', "Removed option: {$option}");
    }
    */
    
    log_message('info', 'EllaContractor module deactivated (no cleanup performed - uncomment desired cleanup code above)');
}


// Register module language files
register_language_files(ELLA_CONTRACTORS_MODULE_NAME, ['ella_contractors']);
