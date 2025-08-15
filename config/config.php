<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['csrf_protection'] = TRUE;

/*
Module Name: Ella Contractors
Description: Comprehensive contractor management module for Ella CRM with contractor profiles, contracts, payments, and project tracking.
Version: 1.0.0
Author: Ella CRM Team
*/

// Module configuration settings
$config['ella_contractors'] = [
    'version' => '1.0.0',
    'enable_email_notifications' => true,
    'default_contract_terms' => 30,
    'payment_methods' => ['bank_transfer', 'check', 'paypal', 'stripe'],
    'contractor_statuses' => ['active', 'inactive', 'pending', 'blacklisted'],
    'contract_statuses' => ['draft', 'active', 'completed', 'terminated', 'expired'],
    'project_statuses' => ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'],
];
