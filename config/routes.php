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

// AJAX endpoints
$route[$module_name . '/get_line_items_ajax'] = $module_name . '/get_line_items_ajax';
$route[$module_name . '/get_estimates_ajax'] = $module_name . '/get_estimates_ajax';
