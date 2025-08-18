<?php

defined('BASEPATH') or exit('No direct script access allowed');

define('ELLA_CONTRACTORS_MODULE_NAME', 'ella_contractors');

$CI = &get_instance();

/*
Module Name: Ella Contractors
Description: Comprehensive contractor management system for CRM
Version: 1.0.0
Requires at least: 2.3.*
*/

// Hook into admin_init to add menu items
hooks()->add_action('admin_init', 'ella_contractors_init_menu_items');

/**
 * Initialize menu items
 */
function ella_contractors_init_menu_items() {
    $CI = &get_instance();
    
    // Check if user has permission to view contractors
    if (has_permission('ella_contractors', '', 'view')) {
        
        // Add main Ella Contractors menu item with dropdown
        $CI->app_menu->add_sidebar_menu_item('ella_contractors', [
            'name'     => 'Ella Contractors',
            'href'     => admin_url('ella_contractors'),
            'icon'     => 'fa fa-users',
            'position' => 30,
        ]);
        
        // Add dropdown submenu items
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_dashboard',
            'name'     => 'Dashboard',
            'href'     => admin_url('ella_contractors/dashboard'),
            'position' => 1,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_contractors',
            'name'     => 'Contractors',
            'href'     => admin_url('ella_contractors/contractors'),
            'position' => 2,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_contracts',
            'name'     => 'Contracts',
            'href'     => admin_url('ella_contractors/contracts'),
            'position' => 3,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_projects',
            'name'     => 'Projects',
            'href'     => admin_url('ella_contractors/projects'),
            'position' => 4,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_payments',
            'name'     => 'Payments',
            'href'     => admin_url('ella_contractors/payments'),
            'position' => 5,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_documents',
            'name'     => 'Documents',
            'href'     => admin_url('ella_contractors/documents'),
            'position' => 6,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('ella_contractors', [
            'slug'     => 'ella_contractors_settings',
            'name'     => 'Settings',
            'href'     => admin_url('ella_contractors/settings'),
            'position' => 7,
        ]);
    }
}

/**
 * Register activation module hook
 */
register_activation_hook('ella_contractors', 'ella_contractors_activate_module');

/**
 * Register deactivation module hook
 */
register_deactivation_hook('ella_contractors', 'ella_contractors_uninstall');

/**
 * Module activation function
 */
function ella_contractors_activate_module() {
    $CI = &get_instance();
    
    // Create database tables
    $CI->load->dbforge();
    
    // Table: tblella_contractors
    $CI->dbforge->add_field([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE,
        ],
        'company_name' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'contact_person' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'email' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'phone' => [
            'type' => 'VARCHAR',
            'constraint' => 50,
            'null' => FALSE,
        ],
        'address' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'city' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => TRUE,
        ],
        'state' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => TRUE,
        ],
        'zip_code' => [
            'type' => 'VARCHAR',
            'constraint' => 20,
            'null' => TRUE,
        ],
        'country' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => TRUE,
        ],
        'website' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => TRUE,
        ],
        'tax_id' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => TRUE,
        ],
        'business_license' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => TRUE,
        ],
        'insurance_info' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'specialties' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'hourly_rate' => [
            'type' => 'DECIMAL',
            'constraint' => '10,2',
            'null' => TRUE,
        ],
        'status' => [
            'type' => 'ENUM',
            'constraint' => ['active', 'inactive', 'pending', 'suspended'],
            'default' => 'pending',
            'null' => FALSE,
        ],
        'notes' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'profile_image' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => TRUE,
        ],
        'date_created' => [
            'type' => 'DATETIME',
            'null' => FALSE,
        ],
        'date_updated' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
        'created_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => FALSE,
        ],
        'updated_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => TRUE,
        ],
    ]);
    
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('company_name');
    $CI->dbforge->add_key('email');
    $CI->dbforge->add_key('status');
    $CI->dbforge->create_table('tblella_contractors', TRUE);
    
    // Table: tblella_contracts
    $CI->dbforge->add_field([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE,
        ],
        'contractor_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'null' => FALSE,
        ],
        'title' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'description' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'start_date' => [
            'type' => 'DATE',
            'null' => FALSE,
        ],
        'end_date' => [
            'type' => 'DATE',
            'null' => FALSE,
        ],
        'amount' => [
            'type' => 'DECIMAL',
            'constraint' => '15,2',
            'null' => FALSE,
            'default' => 0.00,
        ],
        'status' => [
            'type' => 'ENUM',
            'constraint' => ['draft', 'active', 'completed', 'terminated'],
            'default' => 'draft',
            'null' => FALSE,
        ],
        'terms' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'notes' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'date_created' => [
            'type' => 'DATETIME',
            'null' => FALSE,
        ],
        'date_updated' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
        'created_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => FALSE,
        ],
        'updated_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => TRUE,
        ],
    ]);
    
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('contractor_id');
    $CI->dbforge->add_key('title');
    $CI->dbforge->add_key('status');
    $CI->dbforge->create_table('tblella_contracts', TRUE);
    
    // Table: tblella_projects
    $CI->dbforge->add_field([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE,
        ],
        'contractor_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'null' => FALSE,
        ],
        'name' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'description' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'start_date' => [
            'type' => 'DATE',
            'null' => FALSE,
        ],
        'end_date' => [
            'type' => 'DATE',
            'null' => TRUE,
        ],
        'budget' => [
            'type' => 'DECIMAL',
            'constraint' => '15,2',
            'null' => FALSE,
            'default' => 0.00,
        ],
        'status' => [
            'type' => 'ENUM',
            'constraint' => ['planning', 'active', 'on_hold', 'completed', 'cancelled'],
            'default' => 'planning',
            'null' => FALSE,
        ],
        'location' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => TRUE,
        ],
        'progress' => [
            'type' => 'INT',
            'constraint' => 3,
            'null' => FALSE,
            'default' => 0,
        ],
        'notes' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'date_created' => [
            'type' => 'DATETIME',
            'null' => FALSE,
        ],
        'date_updated' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
        'created_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => FALSE,
        ],
        'updated_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => TRUE,
        ],
    ]);
    
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('contractor_id');
    $CI->dbforge->add_key('name');
    $CI->dbforge->add_key('status');
    $CI->dbforge->create_table('tblella_projects', TRUE);
    
    // Table: tblella_payments
    $CI->dbforge->add_field([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE,
        ],
        'contractor_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'null' => FALSE,
        ],
        'contract_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'null' => TRUE,
        ],
        'amount' => [
            'type' => 'DECIMAL',
            'constraint' => '15,2',
            'null' => FALSE,
            'default' => 0.00,
        ],
        'payment_date' => [
            'type' => 'DATE',
            'null' => FALSE,
        ],
        'payment_method' => [
            'type' => 'ENUM',
            'constraint' => ['check', 'bank_transfer', 'credit_card', 'cash', 'other'],
            'default' => 'check',
            'null' => FALSE,
        ],
        'reference_number' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => TRUE,
        ],
        'status' => [
            'type' => 'ENUM',
            'constraint' => ['pending', 'approved', 'paid', 'cancelled'],
            'default' => 'pending',
            'null' => FALSE,
        ],
        'notes' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'date_created' => [
            'type' => 'DATETIME',
            'null' => FALSE,
        ],
        'date_updated' => [
            'type' => 'DATETIME',
            'null' => TRUE,
        ],
        'created_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => FALSE,
        ],
        'updated_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => TRUE,
        ],
    ]);
    
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('contractor_id');
    $CI->dbforge->add_key('contract_id');
    $CI->dbforge->add_key('status');
    $CI->dbforge->create_table('tblella_payments', TRUE);
    
    // Table: tblella_contractor_documents
    $CI->dbforge->add_field([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'auto_increment' => TRUE,
        ],
        'contractor_id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => TRUE,
            'null' => FALSE,
        ],
        'title' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'document_type' => [
            'type' => 'ENUM',
            'constraint' => ['contract', 'license', 'insurance', 'certificate', 'other'],
            'default' => 'other',
            'null' => FALSE,
        ],
        'file_name' => [
            'type' => 'VARCHAR',
            'constraint' => 255,
            'null' => FALSE,
        ],
        'file_path' => [
            'type' => 'VARCHAR',
            'constraint' => 500,
            'null' => FALSE,
        ],
        'file_size' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => FALSE,
            'default' => 0,
        ],
        'file_type' => [
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => FALSE,
        ],
        'description' => [
            'type' => 'TEXT',
            'null' => TRUE,
        ],
        'date_uploaded' => [
            'type' => 'DATETIME',
            'null' => FALSE,
        ],
        'uploaded_by' => [
            'type' => 'INT',
            'constraint' => 11,
            'null' => FALSE,
        ],
    ]);
    
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('contractor_id');
    $CI->dbforge->add_key('document_type');
    $CI->dbforge->create_table('tblella_contractor_documents', TRUE);
    
    // Create upload directories
    $upload_dirs = [
        'uploads/contractors',
        'uploads/contractors/documents',
        'uploads/contractors/temp'
    ];
    
    foreach ($upload_dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Add module to modules table
    $CI->db->insert('tblmodules', [
        'module_name' => 'ella_contractors',
        'installed_version' => '1.0.0',
        'active' => 1
    ]);
    
    // Add default module options
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_default_status',
        'value' => 'pending'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_auto_approve',
        'value' => '0'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_notification_email',
        'value' => ''
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_document_types',
        'value' => 'contract,license,insurance,certificate,other'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_max_file_size',
        'value' => '10485760'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_contract_number_format',
        'value' => 'CON-{YEAR}-{SEQUENCE}'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_contract_reminder_days',
        'value' => '30'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_default_payment_terms',
        'value' => 'Net 30'
    ]);
    
    $CI->db->insert('tbloptions', [
        'name' => 'ella_contractors_late_payment_fee',
        'value' => '0.05'
    ]);
}

/**
 * Module deactivation function
 */
function ella_contractors_uninstall() {
    $CI = &get_instance();
    
    // Remove module from modules table
    $CI->db->where('module_name', 'ella_contractors');
    $CI->db->delete('tblmodules');
    
    // Remove module options
    $CI->db->where('name LIKE', 'ella_contractors_%');
    $CI->db->delete('tbloptions');
    
    // Note: Tables are not dropped to preserve data
    // Uncomment the following lines if you want to completely remove all data
    /*
    $CI->load->dbforge();
    $CI->dbforge->drop_table('tblella_contractor_documents', TRUE);
    $CI->dbforge->drop_table('tblella_payments', TRUE);
    $CI->dbforge->drop_table('tblella_projects', TRUE);
    $CI->dbforge->drop_table('tblella_contracts', TRUE);
    $CI->dbforge->drop_table('tblella_contractors', TRUE);
    */
}

/**
 * Include helper functions
 */
if (file_exists(__DIR__ . '/helpers/ella_contractors_helper.php')) {
    require_once(__DIR__ . '/helpers/ella_contractors_helper.php');
}
