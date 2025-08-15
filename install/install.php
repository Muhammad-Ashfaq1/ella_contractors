<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Installation script for Ella Contractors module
 */

if (!$CI->db->table_exists(db_prefix() . 'ella_contractors')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractors` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `company_name` varchar(255) NOT NULL,
        `contact_person` varchar(255) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `state` varchar(100) DEFAULT NULL,
        `zip_code` varchar(20) DEFAULT NULL,
        `country` varchar(100) DEFAULT NULL,
        `website` varchar(255) DEFAULT NULL,
        `tax_id` varchar(100) DEFAULT NULL,
        `business_license` varchar(255) DEFAULT NULL,
        `insurance_info` text DEFAULT NULL,
        `specialties` text DEFAULT NULL,
        `hourly_rate` decimal(10,2) DEFAULT NULL,
        `status` enum("active","inactive","pending","blacklisted") DEFAULT "pending",
        `notes` text DEFAULT NULL,
        `profile_image` varchar(255) DEFAULT NULL,
        `date_created` datetime NOT NULL,
        `date_updated` datetime DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `updated_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `status` (`status`),
        KEY `created_by` (`created_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

if (!$CI->db->table_exists(db_prefix() . 'ella_contracts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contracts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contractor_id` int(11) NOT NULL,
        `contract_number` varchar(100) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `hourly_rate` decimal(10,2) DEFAULT NULL,
        `estimated_hours` decimal(10,2) DEFAULT NULL,
        `fixed_amount` decimal(15,2) DEFAULT NULL,
        `payment_terms` text DEFAULT NULL,
        `status` enum("draft","active","completed","terminated","expired") DEFAULT "draft",
        `terms_conditions` text DEFAULT NULL,
        `attachments` text DEFAULT NULL,
        `date_created` datetime NOT NULL,
        `date_updated` datetime DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `updated_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `contract_number` (`contract_number`),
        KEY `contractor_id` (`contractor_id`),
        KEY `status` (`status`),
        FOREIGN KEY (`contractor_id`) REFERENCES `' . db_prefix() . 'ella_contractors`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

if (!$CI->db->table_exists(db_prefix() . 'ella_payments')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_payments` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contractor_id` int(11) NOT NULL,
        `contract_id` int(11) DEFAULT NULL,
        `amount` decimal(15,2) NOT NULL,
        `payment_date` date DEFAULT NULL,
        `due_date` date DEFAULT NULL,
        `payment_method` varchar(50) DEFAULT NULL,
        `payment_reference` varchar(255) DEFAULT NULL,
        `invoice_number` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `status` enum("pending","completed","failed","cancelled") DEFAULT "pending",
        `notes` text DEFAULT NULL,
        `date_created` datetime NOT NULL,
        `date_updated` datetime DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `updated_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `contractor_id` (`contractor_id`),
        KEY `contract_id` (`contract_id`),
        KEY `status` (`status`),
        KEY `payment_date` (`payment_date`),
        FOREIGN KEY (`contractor_id`) REFERENCES `' . db_prefix() . 'ella_contractors`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`contract_id`) REFERENCES `' . db_prefix() . 'ella_contracts`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

if (!$CI->db->table_exists(db_prefix() . 'ella_projects')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_projects` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contractor_id` int(11) DEFAULT NULL,
        `contract_id` int(11) DEFAULT NULL,
        `name` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `budget` decimal(15,2) DEFAULT NULL,
        `estimated_hours` decimal(10,2) DEFAULT NULL,
        `actual_hours` decimal(10,2) DEFAULT NULL,
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `status` enum("planning","in_progress","on_hold","completed","cancelled") DEFAULT "planning",
        `priority` enum("low","medium","high","urgent") DEFAULT "medium",
        `location` varchar(255) DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `date_created` datetime NOT NULL,
        `date_updated` datetime DEFAULT NULL,
        `created_by` int(11) NOT NULL,
        `updated_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `contractor_id` (`contractor_id`),
        KEY `contract_id` (`contract_id`),
        KEY `status` (`status`),
        FOREIGN KEY (`contractor_id`) REFERENCES `' . db_prefix() . 'ella_contractors`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`contract_id`) REFERENCES `' . db_prefix() . 'ella_contracts`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_documents')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_documents` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contractor_id` int(11) NOT NULL,
        `document_name` varchar(255) NOT NULL,
        `document_type` varchar(100) DEFAULT NULL,
        `file_name` varchar(255) NOT NULL,
        `file_path` varchar(500) NOT NULL,
        `file_size` int(11) DEFAULT NULL,
        `mime_type` varchar(100) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `date_uploaded` datetime NOT NULL,
        `uploaded_by` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `contractor_id` (`contractor_id`),
        FOREIGN KEY (`contractor_id`) REFERENCES `' . db_prefix() . 'ella_contractors`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

if (!$CI->db->table_exists(db_prefix() . 'ella_contractor_activity')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_contractor_activity` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contractor_id` int(11) NOT NULL,
        `activity` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `staff_id` int(11) NOT NULL,
        `date_created` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `contractor_id` (`contractor_id`),
        KEY `staff_id` (`staff_id`),
        FOREIGN KEY (`contractor_id`) REFERENCES `' . db_prefix() . 'ella_contractors`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

if (!$CI->db->table_exists(db_prefix() . 'ella_document_shares')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'ella_document_shares` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `document_id` int(11) NOT NULL,
        `share_token` varchar(64) NOT NULL,
        `expires_at` datetime NOT NULL,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `accessed_count` int(11) DEFAULT 0,
        `last_accessed` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `share_token` (`share_token`),
        KEY `document_id` (`document_id`),
        KEY `expires_at` (`expires_at`),
        FOREIGN KEY (`document_id`) REFERENCES `' . db_prefix() . 'ella_contractor_documents`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}

// Insert default permissions
if ($CI->db->table_exists(db_prefix() . 'permissions')) {
    $permissions = [
        ['name' => 'ella_contractors', 'shortname' => 'view'],
        ['name' => 'ella_contractors', 'shortname' => 'create'], 
        ['name' => 'ella_contractors', 'shortname' => 'edit'],
        ['name' => 'ella_contractors', 'shortname' => 'delete'],
        ['name' => 'ella_contractors_settings', 'shortname' => 'view']
    ];
    
    foreach ($permissions as $permission) {
        $exists = $CI->db->where($permission)->get('tblpermissions')->row();
        if (!$exists) {
            $CI->db->insert('tblpermissions', $permission);
        }
    }
}

// Insert default settings
$default_settings = [
    'ella_contractors_enable_notifications' => '1',
    'ella_contractors_default_payment_terms' => '30',
    'ella_contractors_currency' => get_base_currency()->name,
    'ella_contractors_require_approval' => '1'
];

foreach ($default_settings as $name => $value) {
    $exists = $CI->db->where('name', $name)->get('tbloptions')->row();
    if (!$exists) {
        $CI->db->insert('tbloptions', [
            'name' => $name,
            'value' => $value,
            'autoload' => 1
        ]);
    }
}
