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

// Presentations routes
$route[$module_name . '/presentations'] = 'Presentations/index';
$route[$module_name . '/presentations/upload'] = 'Presentations/upload';
$route[$module_name . '/presentations/get_preview_pdf/(:num)'] = 'Presentations/get_preview_pdf/$1';
$route[$module_name . '/presentations/debug_upload'] = 'Presentations/debug_upload';

// Measurement routes (for appointment view only)
$route[$module_name . '/measurements/save'] = 'Measurements/save';
$route[$module_name . '/measurements/delete/(:num)'] = 'Measurements/delete/$1';
$route[$module_name . '/measurements/get_measurement/(:num)'] = 'Measurements/get_measurement/$1';
$route[$module_name . '/measurements/get_appointment_measurements/(:num)'] = 'Measurements/get_appointment_measurements/$1';

// Estimates routes - REMOVED - Now using Proposals module


// Appointments routes
$route[$module_name . '/appointments'] = 'Appointments/index';
$route[$module_name . '/appointments/edit/(:num)'] = 'Appointments/edit/$1';
$route[$module_name . '/appointments/view/(:num)'] = 'Appointments/view/$1';
$route[$module_name . '/appointments/save'] = 'Appointments/save';
$route[$module_name . '/appointments/delete/(:num)'] = 'Appointments/delete/$1';
$route[$module_name . '/appointments/table'] = 'Appointments/table';

// AJAX routes for modal operations
$route[$module_name . '/appointments/get_appointment_data'] = 'Appointments/get_appointment_data';
$route[$module_name . '/appointments/save_ajax'] = 'Appointments/save_ajax';
$route[$module_name . '/appointments/delete_ajax'] = 'Appointments/delete_ajax';
$route[$module_name . '/appointments/download_attachment/(:num)'] = 'Appointments/download_attachment/$1';
$route[$module_name . '/appointments/get_appointment_attachments/(:num)'] = 'Appointments/get_appointment_attachments/$1';
$route[$module_name . '/appointments/delete_appointment_attachment/(:num)'] = 'Appointments/delete_appointment_attachment/$1';

// Appointment Measurements routes (using measurements controller) - moved to measurements controller

// Appointment Notes routes
$route[$module_name . '/appointments/get_notes/(:num)'] = 'Appointments/get_notes/$1';
$route[$module_name . '/appointments/add_note/(:num)'] = 'Appointments/add_note/$1';
$route[$module_name . '/appointments/add_note/(:num)/(:num)'] = 'Appointments/add_note/$1/$2';
$route[$module_name . '/appointments/delete_note/(:num)'] = 'Appointments/delete_note/$1';

// AJAX endpoints
$route[$module_name . '/get_line_items_ajax'] = $module_name . '/get_line_items_ajax';

// Global appointment AJAX endpoints
$route[$module_name . '/appointments/get_types'] = 'Appointments/get_types';
$route[$module_name . '/appointments/save_ajax'] = 'Appointments/save_ajax';
$route[$module_name . '/appointments/send_sms'] = 'Appointments/send_sms';
$route[$module_name . '/appointments/get_sms_logs'] = 'Appointments/get_sms_logs';
$route[$module_name . '/appointments/upload_sms_media'] = 'Appointments/upload_sms_media';
