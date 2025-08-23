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

// Test route for debugging
$route['ella_contractors/test'] = 'ella_contractors/test_access';

// Public Media Gallery Routes (Perfex-compatible)
$route['media/(:num)/(:any)'] = 'public_access/media/$1/$2';
$route['default-media/(:any)'] = 'public_access/default_media/$1';
$route['media/(:num)/(:any)/download/(:any)'] = 'public_access/download/$1/$2/$3';
$route['media/(:num)/(:any)/view/(:any)'] = 'public_access/view/$1/$2/$3';
$route['default-media/(:any)/download/(:any)'] = 'public_access/download/0/$1/$2';
$route['default-media/(:any)/view/(:any)'] = 'public_access/view/0/$1/$2';

// Public Media Gallery Routes are now defined in application/config/routes.php
// for proper public access outside the admin panel