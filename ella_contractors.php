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
    // Module activated - no database tables or sample data creation
    // This module is now purely for display purposes
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
