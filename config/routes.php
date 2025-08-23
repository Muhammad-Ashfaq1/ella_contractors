<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Main dashboard route
$route['ella_contractors'] = 'ella_contractors/dashboard';
$route['ella_contractors/dashboard'] = 'ella_contractors/dashboard';

// Contractors management routes
$route['ella_contractors/contractors'] = 'ella_contractors/contractors';
$route['ella_contractors/contractors/(:num)'] = 'ella_contractors/contractors/$1';

// Contracts management routes
$route['ella_contractors/contracts'] = 'ella_contractors/contracts';
$route['ella_contractors/contracts/view/(:num)'] = 'ella_contractors/view_contract/$1';
$route['ella_contractors/contracts/(:num)'] = 'ella_contractors/contracts/$1';
$route['ella_contractors/view_contract/(:num)'] = 'ella_contractors/view_contract/$1';

// Media management routes
$route['ella_contractors/upload_media/(:num)'] = 'ella_contractors/upload_media/$1';
$route['ella_contractors/upload_media'] = 'ella_contractors/upload_media';
$route['ella_contractors/delete_media/(:num)'] = 'ella_contractors/delete_media/$1';
$route['ella_contractors/media_gallery/(:num)'] = 'ella_contractors/media_gallery/$1';
$route['ella_contractors/media_gallery'] = 'ella_contractors/media_gallery';
$route['ella_contractors/default_media'] = 'ella_contractors/default_media';

// Module management routes
$route['ella_contractors/activate'] = 'ella_contractors/activate_module';
// Projects management routes
$route['ella_contractors/projects'] = 'ella_contractors/projects';
$route['ella_contractors/projects/(:num)'] = 'ella_contractors/projects/$1';

// Payments management routes
$route['ella_contractors/payments'] = 'ella_contractors/payments';
$route['ella_contractors/payments/(:num)'] = 'ella_contractors/payments/$1';

// Settings routes
$route['ella_contractors/settings'] = 'ella_contractors/settings';

// Public Media Gallery Routes (Simple Perfex-compatible)
$route['public_media/(:num)/(:any)'] = 'ella_contractors/public_media/$1/$2';
$route['public_default_media/(:any)'] = 'ella_contractors/public_default_media/$1';
