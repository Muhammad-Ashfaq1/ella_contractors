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
$route['ella_contractors/contracts/(:num)'] = 'ella_contractors/contracts/$1';

// Projects management routes
$route['ella_contractors/projects'] = 'ella_contractors/projects';
$route['ella_contractors/projects/(:num)'] = 'ella_contractors/projects/$1';

// Payments management routes
$route['ella_contractors/payments'] = 'ella_contractors/payments';
$route['ella_contractors/payments/(:num)'] = 'ella_contractors/payments/$1';

// Settings routes
$route['ella_contractors/settings'] = 'ella_contractors/settings';