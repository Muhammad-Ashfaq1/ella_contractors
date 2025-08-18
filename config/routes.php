<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Main routes
$route['ella_contractors'] = 'ella_contractors/index';
$route['ella_contractors/dashboard'] = 'ella_contractors/dashboard';
$route['ella_contractors/settings'] = 'ella_contractors/settings';

// Contractors routes
$route['ella_contractors/contractors'] = 'ella_contractors/contractors';
$route['ella_contractors/contractors/add'] = 'ella_contractors/add_contractor';
$route['ella_contractors/contractors/edit/(:num)'] = 'ella_contractors/edit_contractor/$1';
$route['ella_contractors/contractors/view/(:num)'] = 'ella_contractors/view_contractor/$1';
$route['ella_contractors/contractors/delete/(:num)'] = 'ella_contractors/delete_contractor/$1';
$route['ella_contractors/contractors/generate_pdf/(:num)'] = 'ella_contractors/generateContractorPDF/$1';
$route['ella_contractors/contractors/generate_ppt/(:num)'] = 'ella_contractors/generateContractorPPT/$1';

// Contracts routes
$route['ella_contractors/contracts'] = 'ella_contractors/contracts';
$route['ella_contractors/contracts/add'] = 'ella_contractors/add_contract';
$route['ella_contractors/contracts/edit/(:num)'] = 'ella_contractors/edit_contract/$1';
$route['ella_contractors/contracts/view/(:num)'] = 'ella_contractors/view_contract/$1';
$route['ella_contractors/contracts/delete/(:num)'] = 'ella_contractors/delete_contract/$1';
$route['ella_contractors/contracts/generate_pdf/(:num)'] = 'ella_contractors/generateContractPDF/$1';

// Projects routes
$route['ella_contractors/projects'] = 'ella_contractors/projects';
$route['ella_contractors/projects/add'] = 'ella_contractors/add_project';
$route['ella_contractors/projects/edit/(:num)'] = 'ella_contractors/edit_project/$1';
$route['ella_contractors/projects/view/(:num)'] = 'ella_contractors/view_project/$1';
$route['ella_contractors/projects/delete/(:num)'] = 'ella_contractors/delete_project/$1';
$route['ella_contractors/projects/generate_pdf/(:num)'] = 'ella_contractors/generateProjectPDF/$1';

// Payments routes
$route['ella_contractors/payments'] = 'ella_contractors/payments';
$route['ella_contractors/payments/add'] = 'ella_contractors/add_payment';
$route['ella_contractors/payments/edit/(:num)'] = 'ella_contractors/edit_payment/$1';
$route['ella_contractors/payments/delete/(:num)'] = 'ella_contractors/delete_payment/$1';

// Documents routes
$route['ella_contractors/documents'] = 'ella_contractors/documents';
$route['ella_contractors/documents/upload'] = 'ella_contractors/upload_document';
$route['ella_contractors/documents/download'] = 'ella_contractors/download_document';
$route['ella_contractors/documents/delete/(:num)'] = 'ella_contractors/delete_document/$1';

// AJAX routes
$route['ella_contractors/ajax/search_contractors'] = 'ella_contractors/search_contractors';
$route['ella_contractors/ajax/save_settings'] = 'ella_contractors/save_settings';
$route['ella_contractors/ajax/export_data'] = 'ella_contractors/export_data';
$route['ella_contractors/ajax/clear_data'] = 'ella_contractors/clear_data';
$route['ella_contractors/ajax/reset_settings'] = 'ella_contractors/reset_settings';
