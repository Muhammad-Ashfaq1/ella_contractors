<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Main dashboard route
$route['ella_contractors'] = 'ella_contractors/dashboard';

// Test route for debugging
$route['ella_contractors/test'] = 'ella_contractors/test';

// Contractor management routes (commented for now)
// $route['ella_contractors/contractors'] = 'ella_contractors/Ella_contractors/contractors';
// $route['ella_contractors/contractor/(:num)'] = 'ella_contractors/Ella_contractors/contractor_profile/$1';
// $route['ella_contractors/contractor/add'] = 'ella_contractors/Ella_contractors/add_contractor';
// $route['ella_contractors/contractor/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_contractor/$1';
// $route['ella_contractors/contractor/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_contractor/$1';
// $route['ella_contractors/contractor/status/(:num)'] = 'ella_contractors/Ella_contractors/update_contractor_status/$1';

// Contract management routes (commented for now)
// $route['ella_contractors/contracts'] = 'ella_contractors/Ella_contractors/contracts';
// $route['ella_contractors/contract/(:num)'] = 'ella_contractors/Ella_contractors/contract_details/$1';
// $route['ella_contractors/contract/add'] = 'ella_contractors/Ella_contractors/add_contract';
// $route['ella_contractors/contract/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_contract/$1';
// $route['ella_contractors/contract/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_contract/$1';
// $route['ella_contractors/contract/terminate/(:num)'] = 'ella_contractors/Ella_contractors/terminate_contract/$1';

// Payment management routes (commented for now)
// $route['ella_contractors/payments'] = 'ella_contractors/Ella_contractors/payments';
// $route['ella_contractors/payment/(:num)'] = 'ella_contractors/Ella_contractors/payment_details/$1';
// $route['ella_contractors/payment/add'] = 'ella_contractors/Ella_contractors/add_payment';
// $route['ella_contractors/payment/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_payment/$1';
// $route['ella_contractors/payment/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_payment/$1';
// $route['ella_contractors/payment/approve/(:num)'] = 'ella_contractors/Ella_contractors/approve_payment/$1';

// Project management routes (commented for now)
// $route['ella_contractors/projects'] = 'ella_contractors/Ella_contractors/projects';
// $route['ella_contractors/project/(:num)'] = 'ella_contractors/Ella_contractors/project_details/$1';
// $route['ella_contractors/project/add'] = 'ella_contractors/Ella_contractors/add_project';
// $route['ella_contractors/project/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_project/$1';
// $route['ella_contractors/project/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_project/$1';
// $route['ella_contractors/project/assign/(:num)'] = 'ella_contractors/Ella_contractors/assign_contractor/$1';

// Reporting routes (commented for now)
// $route['ella_contractors/reports'] = 'ella_contractors/Ella_contractors/reports';
// $route['ella_contractors/reports/contractor/(:num)'] = 'ella_contractors/Ella_contractors/contractor_report/$1';
// $route['ella_contractors/reports/payments'] = 'ella_contractors/Ella_contractors/payment_reports';
// $route['ella_contractors/reports/projects'] = 'ella_contractors/Ella_contractors/project_reports';

// Ajax/API routes (commented for now)
// $route['ella_contractors/ajax/search_contractors'] = 'ella_contractors/Ella_contractors/search_contractors';
// $route['ella_contractors/ajax/get_contractor_data/(:num)'] = 'ella_contractors/Ella_contractors/get_contractor_data/$1';
// $route['ella_contractors/ajax/contractors_table'] = 'ella_contractors/Ella_contractors/contractors_table';
// $route['ella_contractors/ajax/contracts_table'] = 'ella_contractors/Ella_contractors/contracts_table';
// $route['ella_contractors/ajax/payments_table'] = 'ella_contractors/Ella_contractors/payments_table';
// $route['ella_contractors/ajax/projects_table'] = 'ella_contractors/Ella_contractors/projects_table';

// Settings routes (commented for now)
// $route['ella_contractors/settings'] = 'ella_contractors/Ella_contractors/settings';
// $route['ella_contractors/settings/save'] = 'ella_contractors/Ella_contractors/save_settings';

// Document management routes (ACTIVE)
$route['ella_contractors/documents/upload/(:num)'] = 'ella_contractors/Ella_contractors/upload_document/$1';
$route['ella_contractors/documents/download/(:num)'] = 'ella_contractors/Ella_contractors/download_document/$1';
$route['ella_contractors/documents/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_document/$1';
$route['ella_contractors/documents/gallery/(:num)'] = 'ella_contractors/Ella_contractors/documents_gallery/$1';
$route['ella_contractors/documents/share/(:num)'] = 'ella_contractors/Ella_contractors/share_document/$1';

// PDF Generation routes
$route['ella_contractors/pdf/contract/(:num)'] = 'ella_contractors/Ella_contractors/generate_contract_pdf/$1';
$route['ella_contractors/pdf/invoice/(:num)'] = 'ella_contractors/Ella_contractors/generate_invoice_pdf/$1';
$route['ella_contractors/pdf/report/(:any)'] = 'ella_contractors/Ella_contractors/generate_report_pdf/$1';

// Presentation routes
$route['ella_contractors/presentation/contractor/(:num)'] = 'ella_contractors/Ella_contractors/generate_contractor_presentation/$1';
$route['ella_contractors/presentation/project/(:num)'] = 'ella_contractors/Ella_contractors/generate_project_presentation/$1';

// Test route (remove after testing)
$route['ella_contractors/test_routes'] = 'ella_contractors/Ella_contractors/test_routes';

// Library test route
$route['ella_contractors/test_libraries'] = 'ella_contractors/Ella_contractors/test_libraries';

// ========================================
// FULL CRUD ROUTES (ACTIVE)
// ========================================

// Contractors management
$route['ella_contractors/contractors'] = 'ella_contractors/Ella_contractors/contractors';
$route['ella_contractors/contractors/(:num)'] = 'ella_contractors/Ella_contractors/contractors/$1';
$route['ella_contractors/contractors/add'] = 'ella_contractors/Ella_contractors/add_contractor';
$route['ella_contractors/contractors/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_contractor/$1';
$route['ella_contractors/contractors/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_contractor/$1';

// Contracts management
$route['ella_contractors/contracts'] = 'ella_contractors/Ella_contractors/contracts';
$route['ella_contractors/contracts/(:num)'] = 'ella_contractors/Ella_contractors/contracts/$1';
$route['ella_contractors/contracts/add'] = 'ella_contractors/Ella_contractors/add_contract';
$route['ella_contractors/contracts/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_contract/$1';
$route['ella_contractors/contracts/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_contract/$1';

// Projects management
$route['ella_contractors/projects'] = 'ella_contractors/Ella_contractors/projects';
$route['ella_contractors/projects/(:num)'] = 'ella_contractors/Ella_contractors/projects/$1';
$route['ella_contractors/projects/add'] = 'ella_contractors/Ella_contractors/add_project';
$route['ella_contractors/projects/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_project/$1';
$route['ella_contractors/projects/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_project/$1';

// Payments management
$route['ella_contractors/payments'] = 'ella_contractors/Ella_contractors/payments';
$route['ella_contractors/payments/(:num)'] = 'ella_contractors/Ella_contractors/payments/$1';
$route['ella_contractors/payments/add'] = 'ella_contractors/Ella_contractors/add_payment';
$route['ella_contractors/payments/edit/(:num)'] = 'ella_contractors/Ella_contractors/edit_payment/$1';
$route['ella_contractors/payments/delete/(:num)'] = 'ella_contractors/Ella_contractors/delete_payment/$1';

// Notification routes (commented for now)
// $route['ella_contractors/notifications/send'] = 'ella_contractors/Ella_contractors/send_notification';
// $route['ella_contractors/notifications/reminder/(:num)'] = 'ella_contractors/Ella_contractors/send_reminder/$1';
