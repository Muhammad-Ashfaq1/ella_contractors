<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Ella Contractors
Description: Simple contractor module for display purposes only
Version: 1.0.0
Author: Ella CRM Team
*/

define('ELLA_CONTRACTORS_MODULE_NAME', 'ella_contractors');

hooks()->add_action('admin_init', 'ella_contractors_init_menu');

/**
 * Register deactivation hook
 */
function ella_contractors_deactivation_hook($actions)
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
            'collapse' => true,
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
                'slug' => 'ella_contractors_default_media',
                'name' => 'Default Media',
                'href' => admin_url('ella_contractors/default_media'),
                'position' => 18,
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
                'position' => 30,
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
    $CI->load->dbforge();
    
    // Create contractors table
    $contractors_table = 'tblella_contractors';
    
    if (!$CI->db->table_exists($contractors_table)) {
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
            'mobile' => [
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
            'specialization' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'hourly_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'payment_terms' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive', 'suspended'],
                'default' => 'active'
            ],
            'rating' => [
                'type' => 'DECIMAL',
                'constraint' => '3,2',
                'null' => TRUE
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('email');
        $CI->dbforge->add_key('status');
        $CI->dbforge->create_table($contractors_table);
        
        log_message('info', 'Ella Contractors: Created table ' . $contractors_table);
    }
    
    // Create media table for contract attachments
    $table_name = 'ella_contractor_media';
    
    // Check if table exists
    if (!$CI->db->table_exists($table_name)) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'contract_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => FALSE
            ],
            'is_default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'date_uploaded' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ]
        ];
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contract_id');
        $CI->dbforge->add_key('is_default');
        $CI->dbforge->create_table($table_name);
        
        log_message('info', 'Ella Contractors: Created table ' . $table_name);
    } else {
        // Table already exists, log it
        log_message('info', 'Ella Contractors: Table ' . $table_name . ' already exists');
    }
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
    
    // Note: No tables to drop since none are created
}

/**
 * Include helper functions
 */
if (file_exists(__DIR__ . '/helpers/ella_contractors_helper.php')) {
    require_once(__DIR__ . '/helpers/ella_contractors_helper.php');
}
