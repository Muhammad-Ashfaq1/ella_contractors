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

// Public Media Gallery Routes (self-contained within module)
$route['public-media-gallery/(:num)/(:any)'] = 'public_media_gallery/index/$1/$2';
$route['public-default-media/(:any)'] = 'public_media_gallery/default_gallery/$1';
$route['public-media-gallery/(:num)/(:any)/download/(:any)'] = 'public_media_gallery/download/$1/$2/$3';
$route['public-media-gallery/(:num)/(:any)/view/(:any)'] = 'public_media_gallery/view/$1/$2/$3';
$route['public-default-media/(:any)/download/(:any)'] = 'public_media_gallery/download/0/$1/$2';
$route['public-default-media/(:any)/view/(:any)'] = 'public_media_gallery/view/0/$1/$2';

// Public Media Gallery Routes are now defined in application/config/routes.php
// for proper public access outside the admin panel