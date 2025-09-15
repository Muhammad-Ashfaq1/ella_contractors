<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$module_name = 'ella_contractors';

// Main route - redirect to admin dashboard
$route[$module_name] = $module_name . '/index';



// Module management routes
$route[$module_name . '/activate'] = $module_name . '/activate_module';
// Projects, Payments, and Settings routes removed



// Public routes removed

// Public Contract View Routes removed

$route[$module_name . '/presentations'] = $module_name . '/presentations';
$route[$module_name . '/create_folder'] = $module_name . '/create_folder';
$route[$module_name . '/upload_presentation'] = $module_name . '/upload_presentation';

// Measurements routes
$measurements_route = 'measurements';
$route[$module_name . '/measurements'] = $measurements_route . '/index';
$route[$module_name . '/measurements/(:any)'] = $measurements_route . '/index/$1';
$route[$module_name . '/measurements/save'] = $measurements_route . '/save';
$route[$module_name . '/measurements/delete/(:num)'] = $measurements_route . '/delete/$1';
$route[$module_name . '/measurements/create/(:any)'] = $measurements_route . '/create/$1';
$route[$module_name . '/measurements/edit/(:num)'] = $measurements_route . '/edit/$1';

// Estimates routes
$route[$module_name . '/estimates'] = 'Estimates/index';
$route[$module_name . '/estimates/create'] = 'Estimates/create_estimate';
$route[$module_name . '/estimates/update/(:num)'] = 'Estimates/update_estimate/$1';
$route[$module_name . '/estimates/delete/(:num)'] = 'Estimates/delete_estimate/$1';
$route[$module_name . '/estimates/view/(:num)'] = 'Estimates/view_estimate/$1';
$route[$module_name . '/estimates/table'] = 'Estimates/table';
$route[$module_name . '/estimates/get_estimate_data/(:num)'] = 'Estimates/get_estimate_data/$1';
$route[$module_name . '/estimates/estimates_bulk_action'] = 'Estimates/estimates_bulk_action';


$route[$module_name . '/estimates/manage_estimate'] = 'Estimates/manage_estimate';
$route[$module_name . '/estimates/(:any)'] = 'Estimates/$1';



// Appointments routes
$route[$module_name . '/appointments'] = 'Appointments/index';
$route[$module_name . '/appointments/create'] = 'Appointments/create';
$route[$module_name . '/appointments/edit/(:num)'] = 'Appointments/edit/$1';
$route[$module_name . '/appointments/view/(:num)'] = 'Appointments/view/$1';
$route[$module_name . '/appointments/save'] = 'Appointments/save';
$route[$module_name . '/appointments/delete/(:num)'] = 'Appointments/delete/$1';
$route[$module_name . '/appointments/table'] = 'Appointments/table';
$route[$module_name . '/appointments/upcoming'] = 'Appointments/upcoming';
$route[$module_name . '/appointments/past'] = 'Appointments/past';

// AJAX routes for modal operations
$route[$module_name . '/appointments/get_appointment_data'] = 'Appointments/get_appointment_data';
$route[$module_name . '/appointments/save_ajax'] = 'Appointments/save_ajax';
$route[$module_name . '/appointments/delete_ajax'] = 'Appointments/delete_ajax';

// Appointment Measurements routes
$route[$module_name . '/appointments/get_measurements/(:num)'] = 'Appointments/get_measurements/$1';
$route[$module_name . '/appointments/get_measurement/(:num)/(:num)'] = 'Appointments/get_measurement/$1/$2';
$route[$module_name . '/appointments/save_measurement/(:num)'] = 'Appointments/save_measurement/$1';
$route[$module_name . '/appointments/delete_measurement/(:num)/(:num)'] = 'Appointments/delete_measurement/$1/$2';

// AJAX endpoints
$route[$module_name . '/get_line_items_ajax'] = $module_name . '/get_line_items_ajax';
$route[$module_name . '/get_estimates_ajax'] = $module_name . '/get_estimates_ajax';
