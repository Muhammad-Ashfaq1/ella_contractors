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

// Register cron processing
hooks()->add_action('after_cron_run', 'ella_contractors_after_cron_run');

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
                'slug' => 'ella_contractors_estimates',
                'name' => 'Estimates',
                'href' => admin_url('proposals'),
                'icon' => 'fa fa-file-text-o',
                'position' => 23,
            ],
            [
                'slug' => 'ella_contractors_line_items',
                'name' => 'Service Items',
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
    
    // Load email templates helper (for appointment reminder emails)
    $email_templates_path = module_dir_path(ELLA_CONTRACTORS_MODULE_NAME, 'helpers/ella_email_templates_helper.php');
    if (file_exists($email_templates_path)) {
        require_once($email_templates_path);
    }
    
    // Load reminder helper manually (for ICS generation and email scheduling)
    $reminder_helper_path = module_dir_path(ELLA_CONTRACTORS_MODULE_NAME, 'helpers/ella_reminder_helper.php');
    if (file_exists($reminder_helper_path)) {
        require_once($reminder_helper_path);
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
    
    // Create ella_contractor_media table (for presentations and appointment attachments)
    if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_media')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_media` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `rel_type` varchar(50) DEFAULT NULL COMMENT "Type: attachment, presentation",
            `rel_id` bigint unsigned DEFAULT NULL COMMENT "Related entity ID",
            `org_id` bigint unsigned DEFAULT NULL,
            `file_name` varchar(255) NOT NULL,
            `original_name` varchar(255) NOT NULL,
            `file_type` varchar(100) NOT NULL,
            `file_size` int(11) NOT NULL,
            `description` text,
            `date_uploaded` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_rel_type_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`),
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
        
        // Remove is_default and active columns if they exist (no longer needed)
        if ($CI->db->field_exists('is_default', db_prefix() . 'ella_contractor_media')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` DROP COLUMN `is_default`');
            } catch (Exception $e) {
                // Column might not exist, ignore error
            }
        }
        
        if ($CI->db->field_exists('active', db_prefix() . 'ella_contractor_media')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` DROP COLUMN `active`');
            } catch (Exception $e) {
                // Column might not exist, ignore error
            }
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
    
    // Add uploaded_by column to track who published presentations
    if (!$CI->db->field_exists('uploaded_by', db_prefix() . 'ella_contractor_media')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD COLUMN `uploaded_by` INT(11) DEFAULT NULL AFTER `date_uploaded`');
        try {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_media` ADD KEY `idx_uploaded_by` (`uploaded_by`)');
        } catch (Exception $e) {
            // Key might already exist, ignore error
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
    
    // Add updated_by column to measurement_records table
    if (!$CI->db->field_exists('updated_by', db_prefix() . 'ella_contractor_measurement_records')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_measurement_records` ADD COLUMN `updated_by` int(11) DEFAULT NULL AFTER `created_by`');
        try {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ella_contractor_measurement_records` ADD KEY `idx_updated_by` (`updated_by`)');
        } catch (Exception $e) {
            // Key might already exist, ignore error
        }
    }
    
    // Create upload directory for presentations
    $base_path = FCPATH . 'uploads/ella_presentations/';
    $directories = [
        $base_path,
    ];
    
    // Also setup appointments attachments directory
    $appointments_base = FCPATH . 'uploads/ella_appointments/';
    if (!is_dir($appointments_base)) {
        mkdir($appointments_base, 0755, true);
    }
    
    // Create .htaccess for appointments base directory (allows access to subdirectories)
    $appointments_htaccess = $appointments_base . '.htaccess';
    $htaccess_content = '# Allow public access to attachment files for external viewers' . PHP_EOL .
                        'Order Allow,Deny' . PHP_EOL .
                        'Allow from all' . PHP_EOL .
                        '' . PHP_EOL .
                        '# Prevent directory listing' . PHP_EOL .
                        'Options -Indexes' . PHP_EOL .
                        '' . PHP_EOL .
                        '# Set correct MIME types for PowerPoint files' . PHP_EOL .
                        'AddType application/vnd.ms-powerpoint .ppt' . PHP_EOL .
                        'AddType application/vnd.openxmlformats-officedocument.presentationml.presentation .pptx' . PHP_EOL .
                        'AddType application/pdf .pdf' . PHP_EOL .
                        'AddType text/html .html';
    
    if (!file_exists($appointments_htaccess)) {
        file_put_contents($appointments_htaccess, $htaccess_content);
    } else {
        // Update if it has restrictive rules
        $existing = file_get_contents($appointments_htaccess);
        if (strpos($existing, 'Deny from all') !== false) {
            file_put_contents($appointments_htaccess, $htaccess_content);
        }
    }
    
    // Create index.html to prevent directory listing
    if (!file_exists($appointments_base . 'index.html')) {
        file_put_contents($appointments_base . 'index.html', '');
    }

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
        
        // Create .htaccess to allow public access for external viewers (Microsoft Office Online, Google Docs)
        // These viewers need direct HTTPS access to render PPT/PPTX files properly
        if (!file_exists($dir . '.htaccess')) {
            file_put_contents($dir . '.htaccess', 
                '# Allow public access to presentation files for external viewers' . PHP_EOL .
                'Order Allow,Deny' . PHP_EOL .
                'Allow from all' . PHP_EOL .
                '' . PHP_EOL .
                '# Prevent directory listing' . PHP_EOL .
                'Options -Indexes' . PHP_EOL .
                '' . PHP_EOL .
                '# Set correct MIME types for PowerPoint files' . PHP_EOL .
                'AddType application/vnd.ms-powerpoint .ppt' . PHP_EOL .
                'AddType application/vnd.openxmlformats-officedocument.presentationml.presentation .pptx' . PHP_EOL .
                'AddType application/pdf .pdf' . PHP_EOL .
                'AddType text/html .html'
            );
            } else {
                // Update existing .htaccess if it has restrictive rules
                $existing_htaccess = file_get_contents($dir . '.htaccess');
                if (strpos($existing_htaccess, 'Deny from all') !== false) {
                    file_put_contents($dir . '.htaccess', 
                        '# Allow public access to presentation files for external viewers' . PHP_EOL .
                        'Order Allow,Deny' . PHP_EOL .
                        'Allow from all' . PHP_EOL .
                        '' . PHP_EOL .
                        '# Prevent directory listing' . PHP_EOL .
                        'Options -Indexes' . PHP_EOL .
                        '' . PHP_EOL .
                        '# Set correct MIME types for PowerPoint files' . PHP_EOL .
                        'AddType application/vnd.ms-powerpoint .ppt' . PHP_EOL .
                        'AddType application/vnd.openxmlformats-officedocument.presentationml.presentation .pptx' . PHP_EOL .
                        'AddType application/pdf .pdf' . PHP_EOL .
                        'AddType text/html .html'
                    );
                }
            }
    }
    
    // Create line items image upload directory
    
        // Add appointment_status column to appointly_appointments table if it doesn't exist
        if (!$CI->db->field_exists('appointment_status', db_prefix() . 'appointly_appointments')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `appointment_status` ENUM(\'scheduled\',\'cancelled\',\'complete\') NULL DEFAULT \'scheduled\' AFTER `cancelled`');
            
            // Update existing records based on old boolean fields
            $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "cancelled" WHERE `cancelled` = 1');
            $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "complete" WHERE `finished` = 1 OR `approved` = 1');
            $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `appointment_status` = "scheduled" WHERE `appointment_status` IS NULL');
        }
        
        // Add end_date and end_time columns to appointly_appointments table if they don't exist
        if (!$CI->db->field_exists('end_date', db_prefix() . 'appointly_appointments')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `end_date` DATE NULL AFTER `date`');
            } catch (Exception $e) {
                // Column might already exist, ignore error
            }
        }
        
        if (!$CI->db->field_exists('end_time', db_prefix() . 'appointly_appointments')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `end_time` TIME NULL AFTER `start_hour`');
            } catch (Exception $e) {
                // Column might already exist, ignore error
            }
        }
        
        if (!$CI->db->field_exists('send_reminder', db_prefix() . 'appointly_appointments')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `send_reminder` TINYINT(1) DEFAULT 1 AFTER `appointment_status`');
            } catch (Exception $e) {
                // Column might already exist, ignore error
            }
        }
        
        if (!$CI->db->field_exists('reminder_48h', db_prefix() . 'appointly_appointments')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `reminder_48h` TINYINT(1) DEFAULT 1 AFTER `send_reminder`');
            } catch (Exception $e) {
                // Column might already exist, ignore error
            }
        }
        
        // Add staff_reminder_48h column for staff reminders (NEW - My Reminder feature)
        if (!$CI->db->field_exists('staff_reminder_48h', db_prefix() . 'appointly_appointments')) {
            try {
                // Add column with DEFAULT 1 (checked by default) to match UI behavior
                // TINYINT(1) NULL allows for compatibility with existing records
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `staff_reminder_48h` TINYINT(1) NULL DEFAULT 1 AFTER `reminder_48h`');
                
                // Add index for faster queries when filtering by this field
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD INDEX `idx_staff_reminder_48h` (`staff_reminder_48h`)');
                
                // Update existing ella_contractor appointments to have staff reminder enabled by default
                // This only affects appointments with source='ella_contractor', not appointly's own appointments
                $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `staff_reminder_48h` = 1 WHERE `source` = "ella_contractor" AND `staff_reminder_48h` IS NULL');
                
                log_message('info', 'EllaContractors: staff_reminder_48h column added successfully');
            } catch (Exception $e) {
                // Column might already exist or error occurred - log but don't break activation
                log_message('error', 'EllaContractors: Failed to add staff_reminder_48h column - ' . $e->getMessage());
            }
        }

        if (!$CI->db->field_exists('reminder_channel', db_prefix() . 'appointly_appointments')) {
            try {
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD COLUMN `reminder_channel` ENUM(\'sms\',\'email\',\'both\') NOT NULL DEFAULT \'both\' AFTER `staff_reminder_48h`');
                $CI->db->query('ALTER TABLE `' . db_prefix() . 'appointly_appointments` ADD INDEX `idx_reminder_channel` (`reminder_channel`)');
                // Ensure existing Ella appointments default to both
                $CI->db->query('UPDATE `' . db_prefix() . 'appointly_appointments` SET `reminder_channel` = \'both\' WHERE `source` = "ella_contractor" OR `reminder_channel` IS NULL');
                log_message('info', 'EllaContractors: reminder_channel column added successfully');
            } catch (Exception $e) {
                log_message('error', 'EllaContractors: Failed to add reminder_channel column - ' . $e->getMessage());
            }
        }
        
        // Create ICS upload directory for calendar file storage
        $ics_dir = FCPATH . 'uploads/ella_appointments/ics/';
        if (!is_dir($ics_dir)) {
            try {
                mkdir($ics_dir, 0755, true);
                
                // Create index.html to prevent directory listing
                if (!file_exists($ics_dir . 'index.html')) {
                    file_put_contents($ics_dir . 'index.html', '');
                }
                
                log_message('info', 'EllaContractors: ICS directory created successfully');
            } catch (Exception $e) {
                log_message('error', 'EllaContractors: Failed to create ICS directory - ' . $e->getMessage());
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
    }
    
    // Create ella_appointment_presentations pivot table for linking appointments to presentations
    if (!$CI->db->table_exists(db_prefix() . 'ella_appointment_presentations')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_appointment_presentations` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `appointment_id` int(11) NOT NULL,
            `presentation_id` int(11) NOT NULL,
            `attached_by` int(11) NOT NULL,
            `attached_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_appointment_id` (`appointment_id`),
            KEY `idx_presentation_id` (`presentation_id`),
            KEY `idx_attached_by` (`attached_by`),
            UNIQUE KEY `unique_appointment_presentation` (`appointment_id`, `presentation_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }

    // Create appointment_reminder table to track reminder statuses
    if (!$CI->db->table_exists(db_prefix() . 'appointment_reminder')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'appointment_reminder` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `appointment_id` int(11) NOT NULL,
            `client_instant_remind` TINYINT(1) NOT NULL DEFAULT 0,
            `client_48_hours` TINYINT(1) NOT NULL DEFAULT 0,
            `staff_48_hours` TINYINT(1) NOT NULL DEFAULT 0,
            `client_sms_reminder` TINYINT(1) NOT NULL DEFAULT 0,
            `sms_send` TINYINT(1) NOT NULL DEFAULT 0,
            `email_send` TINYINT(1) NOT NULL DEFAULT 0,
            `client_instant_sent` TINYINT(1) NOT NULL DEFAULT 0,
            `client_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0,
            `staff_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0,
            `client_sms_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0,
            `staff_sms_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0,
            `last_email_sent_at` datetime DEFAULT NULL,
            `last_sms_sent_at` datetime DEFAULT NULL,
            `rel_type` varchar(50) DEFAULT NULL,
            `rel_id` int(11) DEFAULT NULL,
            `org_id` int(11) DEFAULT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_appointment_id` (`appointment_id`),
            KEY `idx_rel_type_rel_id` (`rel_type`, `rel_id`),
            KEY `idx_org_id` (`org_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    } else {
        $table = db_prefix() . 'appointment_reminder';

        $fieldsToAdd = [
            'staff_48_hours'            => 'ALTER TABLE `' . $table . '` ADD COLUMN `staff_48_hours` TINYINT(1) NOT NULL DEFAULT 0 AFTER `client_48_hours`',
            'client_instant_sent'       => 'ALTER TABLE `' . $table . '` ADD COLUMN `client_instant_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email_send`',
            'client_48_hours_sent'      => 'ALTER TABLE `' . $table . '` ADD COLUMN `client_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `client_instant_sent`',
            'staff_48_hours_sent'       => 'ALTER TABLE `' . $table . '` ADD COLUMN `staff_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `client_48_hours_sent`',
            'client_sms_48_hours_sent'  => 'ALTER TABLE `' . $table . '` ADD COLUMN `client_sms_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `staff_48_hours_sent`',
            'staff_sms_48_hours_sent'   => 'ALTER TABLE `' . $table . '` ADD COLUMN `staff_sms_48_hours_sent` TINYINT(1) NOT NULL DEFAULT 0 AFTER `client_sms_48_hours_sent`',
            'last_email_sent_at'        => 'ALTER TABLE `' . $table . '` ADD COLUMN `last_email_sent_at` DATETIME DEFAULT NULL AFTER `staff_sms_48_hours_sent`',
            'last_sms_sent_at'          => 'ALTER TABLE `' . $table . '` ADD COLUMN `last_sms_sent_at` DATETIME DEFAULT NULL AFTER `last_email_sent_at`',
        ];

        foreach ($fieldsToAdd as $field => $statement) {
            if (!$CI->db->field_exists($field, $table)) {
                $CI->db->query($statement);
            }
        }
    }
    
    // ==================== DATA MIGRATION: Update rel_type for existing records ====================
    
    // Update existing media records that don't have rel_type set
    // Default: records without rel_type or with rel_type='appointment' become 'attachment'
    if ($CI->db->field_exists('rel_type', db_prefix() . 'ella_contractor_media')) {
        // Fix records where rel_type is NULL or 'appointment' - these are attachments
        $CI->db->where('rel_type IS NULL OR rel_type = "appointment"', NULL, FALSE);
        $CI->db->update(db_prefix() . 'ella_contractor_media', ['rel_type' => 'attachment']);
    }
    
    // ==================== END DATA MIGRATION ====================
    
    // Set module version
    update_option('ella_contractors_version', '1.0.0');
    
}

/**
 * Cron callback for Ella Contractors reminders.
 *
 * @param bool $manually
 * @return void
 */
function ella_contractors_after_cron_run($manually)
{
    $reminder_helper_path = module_dir_path(ELLA_CONTRACTORS_MODULE_NAME, 'helpers/ella_reminder_helper.php');
    if (file_exists($reminder_helper_path) && !function_exists('ella_process_48h_reminders_cron')) {
        require_once($reminder_helper_path);
    }

    if (function_exists('ella_process_48h_reminders_cron')) {
        ella_process_48h_reminders_cron();
    }
}

function ella_contractors_deactivate_module() {
    $CI = &get_instance();

    // Remove legacy tables
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ella_contractor_line_items`');
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ella_contractor_line_item_groups`');
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ella_contractor_estimates`');
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ella_contractor_estimate_line_items`');
    $CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'appointment_reminder`');

}


// Register module language files
register_language_files(ELLA_CONTRACTORS_MODULE_NAME, ['ella_contractors']);
