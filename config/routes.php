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
$route[$module_name . '/preview_file/(.*)'] = $module_name . '/preview_file/$1';
