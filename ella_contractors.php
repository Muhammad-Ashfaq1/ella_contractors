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
                'href' => admin_url('ella_contractors/documents/gallery/1'),
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
    
    // Create tables if they don't exist
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
                'null' => TRUE
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
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
                'constraint' => 255,
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
                'type' => 'ENUM("active","inactive","pending","blacklisted")',
                'default' => 'pending',
                'null' => TRUE
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'profile_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $CI->dbforge->add_key('status');
        $CI->dbforge->add_key('company_name');
        $CI->dbforge->add_key('email');
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
            'contractor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'contract_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'hourly_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'estimated_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'fixed_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => TRUE
            ],
            'payment_terms' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'ENUM("draft","active","completed","terminated")',
                'default' => 'draft',
                'null' => FALSE
            ],
            'terms_conditions' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'attachments' => [
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
        $CI->dbforge->add_key('contract_number');
        $CI->dbforge->add_key('status');
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'budget' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => TRUE
            ],
            'estimated_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'actual_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'priority' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE
            ],
            'location' => [
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
        $CI->dbforge->add_key('contractor_id');
        $CI->dbforge->add_key('contract_id');
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
                'null' => TRUE
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
            'document_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'document_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
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
            'mime_type' => [
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
        $CI->dbforge->add_key('document_type');
        $CI->dbforge->create_table('tblella_contractor_documents');
    }
    
    // Table: tblella_contractor_activity
    if (!$CI->db->table_exists('tblella_contractor_activity')) {
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
            'activity' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'staff_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('contractor_id');
        $CI->dbforge->add_key('staff_id');
        $CI->dbforge->create_table('tblella_contractor_activity');
    }
    
    // Table: tblella_document_shares
    if (!$CI->db->table_exists('tblella_document_shares')) {
        $fields = [
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'document_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'share_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'expires_at' => [
                'type' => 'DATETIME',
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
            'accessed_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => FALSE
            ],
            'last_accessed' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ];
        
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->add_key('document_id');
        $CI->dbforge->add_key('share_token');
        $CI->dbforge->create_table('tblella_document_shares');
    }
    
    // Insert sample data if tables are empty
    if ($CI->db->count_all('tblella_contractors') == 0) {
        $sample_contractors = [
            [
                'company_name' => 'ABC Construction Co.',
                'contact_person' => 'John Smith',
                'email' => 'john@abcconstruction.com',
                'phone' => '(555) 123-4567',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'zip_code' => '10001',
                'country' => 'US',
                'website' => 'https://abcconstruction.com',
                'tax_id' => '12-3456789',
                'business_license' => 'LIC-2024-001',
                'specialties' => 'construction, renovation, remodeling',
                'hourly_rate' => 75.00,
                'status' => 'active',
                'notes' => 'Reliable construction company with 15+ years experience',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id()
            ],
            [
                'company_name' => 'Elite Electrical Services',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@eliteelectrical.com',
                'phone' => '(555) 234-5678',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip_code' => '90210',
                'country' => 'US',
                'website' => 'https://eliteelectrical.com',
                'tax_id' => '98-7654321',
                'business_license' => 'LIC-2024-002',
                'specialties' => 'electrical, wiring, lighting, maintenance',
                'hourly_rate' => 65.00,
                'status' => 'active',
                'notes' => 'Licensed electrical contractor specializing in commercial and residential',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id()
            ]
        ];
        
        foreach ($sample_contractors as $contractor) {
            $CI->db->insert('tblella_contractors', $contractor);
        }
    }
    
    // Insert sample contracts if table is empty
    if ($CI->db->count_all('tblella_contracts') == 0) {
        $sample_contracts = [
            [
                'contractor_id' => 1,
                'contract_number' => 'CON-2024-001',
                'title' => 'Office Building Renovation',
                'description' => 'Complete renovation of 3-story office building including electrical, plumbing, and HVAC systems.',
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-30',
                'hourly_rate' => 75.00,
                'estimated_hours' => 1667.00,
                'fixed_amount' => 125000.00,
                'payment_terms' => 'Payment schedule: 30% upfront, 40% at 50% completion, 30% upon final inspection.',
                'status' => 'active',
                'terms_conditions' => 'Standard construction terms and conditions apply.',
                'attachments' => '',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id()
            ]
        ];
        
        foreach ($sample_contracts as $contract) {
            $CI->db->insert('tblella_contracts', $contract);
        }
    }
    
    // Insert sample payments if table is empty
    if ($CI->db->count_all('tblella_payments') == 0) {
        $sample_payments = [
            [
                'contractor_id' => 1,
                'contract_id' => 1,
                'amount' => 37500.00,
                'payment_date' => null,
                'due_date' => '2024-02-15',
                'payment_method' => 'check',
                'payment_reference' => '',
                'invoice_number' => 'INV-2024-001',
                'description' => 'Upfront payment for office renovation project',
                'status' => 'pending',
                'notes' => '30% upfront payment as per contract terms',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id()
            ]
        ];
        
        foreach ($sample_payments as $payment) {
            $CI->db->insert('tblella_payments', $payment);
        }
    }

    // Insert sample projects if table is empty
    if ($CI->db->count_all('tblella_projects') == 0) {
        $sample_projects = [
            [
                'contractor_id' => 1,
                'contract_id' => 1,
                'name' => 'Office Building Renovation',
                'description' => 'Complete renovation of 3-story office building including electrical, plumbing, and HVAC systems.',
                'budget' => 125000.00,
                'estimated_hours' => 1667.00,
                'actual_hours' => null,
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-30',
                'status' => 'in_progress',
                'priority' => 'high',
                'location' => 'Downtown Business District',
                'notes' => 'High priority project for downtown office complex',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
                'created_by' => get_staff_user_id()
            ]
        ];
        
        foreach ($sample_projects as $project) {
            $CI->db->insert('tblella_projects', $project);
        }
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
    
    // Note: We don't drop tables to preserve data
    // Tables can be manually dropped if needed
}

/**
 * Include helper functions
 */
if (file_exists(__DIR__ . '/helpers/ella_contractors_helper.php')) {
    require_once(__DIR__ . '/helpers/ella_contractors_helper.php');
}
