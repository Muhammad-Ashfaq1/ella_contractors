<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$module_name = 'ella_contractors';

// Main dashboard route
$route[$module_name] = $module_name . '/dashboard';
$route[$module_name . '/dashboard'] = $module_name . '/dashboard';

// Contractors management routes
$route[$module_name . '/contractors'] = $module_name . '/contractors';
$route[$module_name . '/contractors/(:num)'] = $module_name . '/contractors/$1';

// Contracts management routes
$route[$module_name . '/contracts'] = $module_name . '/contracts';
$route[$module_name . '/contracts/view/(:num)'] = $module_name . '/view_contract/$1';
$route[$module_name . '/contracts/(:num)'] = $module_name . '/contracts/$1';
$route[$module_name . '/view_contract/(:num)'] = $module_name . '/view_contract/$1';

// Media management routes
$route[$module_name . '/upload_media/(:num)'] = $module_name . '/upload_media/$1';
$route[$module_name . '/upload_media'] = $module_name . '/upload_media';
$route[$module_name . '/delete_media/(:num)'] = $module_name . '/delete_media/$1';
$route[$module_name . '/media_gallery/(:num)'] = $module_name . '/media_gallery/$1';
$route[$module_name . '/media_gallery'] = $module_name . '/media_gallery';
$route[$module_name . '/default_media'] = $module_name . '/default_media';

// Module management routes
$route[$module_name . '/activate'] = $module_name . '/activate_module';
// Projects management routes
$route[$module_name . '/projects'] = $module_name . '/projects';
$route[$module_name . '/projects/(:num)'] = $module_name . '/projects/$1';

// Payments management routes
$route[$module_name . '/payments'] = $module_name . '/payments';
$route[$module_name . '/payments/(:num)'] = $module_name . '/payments/$1';

// Settings routes
$route[$module_name . '/settings'] = $module_name . '/settings';



// Public Media Gallery Routes (Simple Perfex-compatible)
$route['public_media/(:num)/(:any)'] = 'ella_contractors/public_media/$1/$2';
$route['public_default_media/(:any)'] = 'ella_contractors/public_default_media/$1';
