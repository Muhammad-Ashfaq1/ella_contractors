<?php

defined('BASEPATH') or exit('No direct script access allowed');

define('ELLA_CONTRACTORS_MODULE_NAME', 'ella_contractors');

$CI = &get_instance();

/*
Module Name: Ella Contractors
Description: Comprehensive contractor management system for Ella CRM with PDF generation and document management.
Version: 1.0.0
Author: Ella CRM Team
*/

hooks()->add_action('admin_init', 'ella_contractors_init_menu');
hooks()->add_filter('module_ella_contractors_action_links', 'ella_contractors_action_links');

function ella_contractors_action_links($actions)
{
    $actions[] = register_deactivation_hook(ELLA_CONTRACTORS_MODULE_NAME, 'ella_contractors_uninstall');
    return $actions;
}

function ella_contractors_init_menu() {
    $CI = &get_instance();
    if (is_staff_logged_in() && (is_super_admin() || is_admin() || has_permission('ella_contractors', '', 'view'))) {
        $CI->app_menu->add_sidebar_menu_item('ella_contractors', [
            'slug' => 'ella_contractors',
            'name' => 'Ella Contractors',
            'icon' => 'fa fa-users',
            'position' => 30,
        ]);

        $submenu = [
            [
                'slug' => 'ella_contractors_dashboard',
                'name' => 'Dashboard',
                'href' => admin_url('ella_contractors'),
                'position' => 5,
            ],
            [
                'slug' => 'ella_contractors_contractors',
                'name' => 'Contractors',
                'href' => admin_url('ella_contractors/contractors'),
                'position' => 10,
            ],
            [
                'slug' => 'ella_contractors_contracts',
                'name' => 'Contracts',
                'href' => admin_url('ella_contractors/contracts'),
                'position' => 15,
            ],
            [
                'slug' => 'ella_contractors_projects',
                'name' => 'Projects',
                'href' => admin_url('ella_contractors/projects'),
                'position' => 20,
            ],
            [
                'slug' => 'ella_contractors_payments',
                'name' => 'Payments',
                'href' => admin_url('ella_contractors/payments'),
                'position' => 25,
            ],
            [
                'slug' => 'ella_contractors_documents',
                'name' => 'Documents',
                'href' => admin_url('ella_contractors/documents'),
                'position' => 30,
            ]
        ];

        foreach ($submenu as $item) {
            $CI->app_menu->add_sidebar_children_item('ella_contractors', $item);
        }
        
        if(is_staff_logged_in() && (is_super_admin() || has_permission('ella_contractors_settings', '', 'view'))){
            // settings sub-menu
            $settings_sub_menu = [
                'slug' => 'ella_contractors_settings',
                'name' => 'Settings',
                'href' => admin_url('ella_contractors/settings'),
                'position' => 35,
            ];
            
            $CI->app_menu->add_sidebar_children_item('ella_contractors', $settings_sub_menu);
        }
    }
}

register_activation_hook(ELLA_CONTRACTORS_MODULE_NAME, 'ella_contractors_activate_module');

/**
 * Activate module function
 */
function ella_contractors_activate_module()
{
    $CI = &get_instance();
    
    // Create database tables if they don't exist
    $CI->load->dbforge();
    
    // Table: tblella_contractors
    if (!$CI->db->table_exists('tblella_contractors')) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'company_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'contact_person' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'zip_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'tax_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'business_license' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'insurance_info' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'specialties' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'hourly_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'ENUM("active","inactive","pending","suspended")',
                'default' => 'pending'
            ],
            'profile_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('tblella_contractors');
    }
    
    // Table: tblella_contracts
    if (!$CI->db->table_exists('tblella_contracts')) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'contractor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => FALSE
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => FALSE
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE
            ],
            'status' => [
                'type' => 'ENUM("draft","active","completed","terminated")',
                'default' => 'draft'
            ],
            'terms' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contractor_id');
        $CI->dbforge->create_table('tblella_contracts');
    }
    
    // Table: tblella_projects
    if (!$CI->db->table_exists('tblella_projects')) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'contractor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => FALSE
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'budget' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE
            ],
            'status' => [
                'type' => 'ENUM("planning","active","on_hold","completed","cancelled")',
                'default' => 'planning'
            ],
            'location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'progress' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contractor_id');
        $CI->dbforge->create_table('tblella_projects');
    }
    
    // Table: tblella_payments
    if (!$CI->db->table_exists('tblella_payments')) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'contractor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'contract_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => FALSE
            ],
            'payment_date' => [
                'type' => 'DATE',
                'null' => FALSE
            ],
            'payment_method' => [
                'type' => 'ENUM("check","bank_transfer","credit_card","cash","other")',
                'default' => 'check'
            ],
            'reference_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'status' => [
                'type' => 'ENUM("pending","approved","paid","cancelled")',
                'default' => 'pending'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contractor_id');
        $CI->dbforge->add_key('contract_id');
        $CI->dbforge->create_table('tblella_payments');
    }
    
    // Table: tblella_contractor_documents
    if (!$CI->db->table_exists('tblella_contractor_documents')) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'contractor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'document_type' => [
                'type' => 'ENUM("contract","license","insurance","certificate","other")',
                'default' => 'other'
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => FALSE
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'date_uploaded' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contractor_id');
        $CI->dbforge->create_table('tblella_contractor_documents');
    }
    
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
    
    // Add module to database
    $CI->db->insert('tblmodules', [
        'module_name' => ELLA_CONTRACTORS_MODULE_NAME,
        'installed_version' => '1.0.0',
        'active' => 1
    ]);
    
    // Set default options
    add_option('ella_contractors_default_status', 'pending');
    add_option('ella_contractors_auto_approve', 0);
    add_option('ella_contractors_notification_email', '');
    add_option('ella_contractors_document_types', 'contract,license,insurance,certificate,other');
    
    log_activity('Ella Contractors module activated');
}

/**
 * Uninstall module function
 */
function ella_contractors_uninstall()
{
    $CI = &get_instance();
    
    // Deactivate the module in tblmodules
    $CI->db->where('module_name', ELLA_CONTRACTORS_MODULE_NAME);
    $CI->db->update('tblmodules', ['active' => 0]);
    
    // Remove options
    delete_option('ella_contractors_default_status');
    delete_option('ella_contractors_auto_approve');
    delete_option('ella_contractors_notification_email');
    delete_option('ella_contractors_document_types');
    
    log_activity('Ella Contractors module deactivated');
}

/**
 * Include helper functions
 */
if (file_exists(__DIR__ . '/helpers/ella_contractors_helper.php')) {
    require_once(__DIR__ . '/helpers/ella_contractors_helper.php');
}
