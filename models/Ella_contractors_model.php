<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ella_contractors_model extends App_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    // ========================================
    // CONTRACTORS MANAGEMENT
    // ========================================
    
    /**
     * Get all contractors with pagination and search
     */
    public function getContractors($limit = 10, $offset = 0, $search = '', $status = '') {
        // Ensure offset is never negative
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        $this->db->select('*');
        $this->db->from('tblella_contractors');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('company_name', $search);
            $this->db->or_like('contact_person', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('status', $status);
        }
        
        $this->db->order_by('date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get contractor by ID
     */
    public function getContractorById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('tblella_contractors');
        return $query->row();
    }
    
    /**
     * Create new contractor
     */
    public function createContractor($data) {
        // Map the form fields to the actual database columns
        $db_data = [
            'company_name' => $data['company_name'] ?? '',
            'contact_person' => $data['contact_person'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'zip_code' => $data['zip_code'] ?? '',
            'country' => $data['country'] ?? '',
            'website' => $data['website'] ?? '',
            'tax_id' => $data['tax_id'] ?? '',
            'business_license' => $data['business_license'] ?? $data['license_number'] ?? '',
            'insurance_info' => $data['insurance_info'] ?? '',
            'specialties' => $data['specialties'] ?? $data['specialization'] ?? '',
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? '',
            'profile_image' => $data['profile_image'] ?? '',
            'date_created' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_contractors', $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update contractor
     */
    public function updateContractor($id, $data) {
        // Map the form fields to the actual database columns
        $db_data = [
            'company_name' => $data['company_name'] ?? '',
            'contact_person' => $data['contact_person'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'zip_code' => $data['zip_code'] ?? '',
            'country' => $data['country'] ?? '',
            'website' => $data['website'] ?? '',
            'tax_id' => $data['tax_id'] ?? '',
            'business_license' => $data['business_license'] ?? $data['license_number'] ?? '',
            'insurance_info' => $data['insurance_info'] ?? '',
            'specialties' => $data['specialties'] ?? $data['specialization'] ?? '',
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? '',
            'profile_image' => $data['profile_image'] ?? '',
            'date_updated' => date('Y-m-d H:i:s'),
            'updated_by' => get_staff_user_id()
        ];
        
        $this->db->where('id', $id);
        return $this->db->update('tblella_contractors', $db_data);
    }
    
    /**
     * Delete contractor
     */
    public function deleteContractor($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_contractors');
    }
    
    /**
     * Get contractors count for pagination
     */
    public function getContractorsCount($search = '', $status = '') {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('company_name', $search);
            $this->db->or_like('contact_person', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('status', $status);
        }
        
        return $this->db->count_all_results('tblella_contractors');
    }
    
    // ========================================
    // CONTRACTS MANAGEMENT
    // ========================================
    
    /**
     * Get all contracts with pagination and filters
     */
    public function getContracts($limit = 10, $offset = 0, $search = '', $status = '', $contractor_id = '') {
        // Ensure offset is never negative
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_contracts')) {
            return [];
        }
        
        $this->db->select('c.*');
        $this->db->from('tblella_contracts c');
        
        // Only join with contractors table if it exists
        if ($this->db->table_exists('tblella_contractors')) {
            $this->db->select('co.company_name as contractor_name');
            $this->db->join('tblella_contractors co', 'c.contractor_id = co.id', 'left');
        }
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('c.title', $search);
            $this->db->or_like('c.contract_number', $search);
            if ($this->db->table_exists('tblella_contractors')) {
                $this->db->or_like('co.company_name', $search);
            }
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('c.status', $status);
        }
        
        if (!empty($contractor_id)) {
            $this->db->where('c.contractor_id', $contractor_id);
        }
        
        $this->db->order_by('c.date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get contract by ID with contractor details
     */
    public function getContractById($id) {
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_contracts')) {
            return null;
        }
        
        $this->db->select('c.*');
        $this->db->from('tblella_contracts c');
        
        // Only join with contractors table if it exists
        if ($this->db->table_exists('tblella_contractors')) {
            $this->db->select('co.company_name, co.contact_person, co.email, co.phone');
            $this->db->join('tblella_contractors co', 'c.contractor_id = co.id', 'left');
        }
        
        $this->db->where('c.id', $id);
        
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Create new contract
     */
    public function createContract($data) {
        // Map the form fields to the actual database columns
        $db_data = [
            'contractor_id' => $data['contractor_id'] ?? '',
            'contract_number' => $data['contract_number'] ?? '',
            'title' => $data['title'] ?? $data['project_name'] ?? '',
            'description' => $data['description'] ?? '',
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'fixed_amount' => $data['fixed_amount'] ?? $data['contract_value'] ?? 0.00,
            'payment_terms' => $data['payment_terms'] ?? '',
            'status' => $data['status'] ?? 'draft',
            'terms_conditions' => $data['terms_conditions'] ?? '',
            'attachments' => $data['attachments'] ?? '',
            'date_created' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_contracts', $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update contract
     */
    public function updateContract($id, $data) {
        // Map the form fields to the actual database columns
        $db_data = [
            'contractor_id' => $data['contractor_id'] ?? '',
            'contract_number' => $data['contract_number'] ?? '',
            'title' => $data['title'] ?? $data['project_name'] ?? '',
            'description' => $data['description'] ?? '',
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'fixed_amount' => $data['fixed_amount'] ?? $data['contract_value'] ?? 0.00,
            'payment_terms' => $data['payment_terms'] ?? '',
            'status' => $data['status'] ?? 'draft',
            'terms_conditions' => $data['terms_conditions'] ?? '',
            'attachments' => $data['attachments'] ?? '',
            'date_updated' => date('Y-m-d H:i:s'),
            'updated_by' => get_staff_user_id()
        ];
        
        $this->db->where('id', $id);
        return $this->db->update('tblella_contracts', $db_data);
    }
    
    /**
     * Delete contract
     */
    public function deleteContract($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_contracts');
    }
    
    /**
     * Get contracts count for pagination
     */
    public function getContractsCount($search = '', $status = '', $contractor_id = '') {
        $this->db->from('tblella_contracts c');
        $this->db->join('tblella_contractors co', 'c.contractor_id = co.id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('c.project_name', $search);
            $this->db->or_like('c.contract_number', $search);
            $this->db->or_like('co.company_name', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('c.status', $status);
        }
        
        if (!empty($contractor_id)) {
            $this->db->where('c.contractor_id', $contractor_id);
        }
        
        return $this->db->count_all_results();
    }
    
    // ========================================
    // PROJECTS MANAGEMENT
    // ========================================
    
    /**
     * Get all projects with pagination and filters
     */
    public function getProjects($limit = 10, $offset = 0, $search = '', $status = '', $contractor_id = '') {
        // Ensure offset is never negative
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_projects')) {
            return [];
        }
        
        $this->db->select('p.*');
        $this->db->from('tblella_projects p');
        
        // Only join with contractors table if it exists
        if ($this->db->table_exists('tblella_contractors')) {
            $this->db->select('co.company_name as contractor_name');
            $this->db->join('tblella_contractors co', 'p.contractor_id = co.id', 'left');
        }
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.name', $search);
            if ($this->db->table_exists('tblella_contractors')) {
                $this->db->or_like('co.company_name', $search);
            }
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }
        
        if (!empty($contractor_id)) {
            $this->db->where('p.contractor_id', $contractor_id);
        }
        
        $this->db->order_by('p.date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get project by ID with contractor details
     */
    public function getProjectById($id) {
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_projects')) {
            return null;
        }
        
        $this->db->select('p.*');
        $this->db->from('tblella_projects p');
        
        // Only join with contractors table if it exists
        if ($this->db->table_exists('tblella_contractors')) {
            $this->db->select('co.company_name, co.contact_person, co.email, co.phone');
            $this->db->join('tblella_contractors co', 'p.contractor_id = co.id', 'left');
        }
        
        $this->db->where('p.id', $id);
        
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Create new project
     */
    public function createProject($data) {
        // Map the form fields to the actual database columns
        $db_data = [
            'contractor_id' => $data['contractor_id'] ?? '',
            'contract_id' => $data['contract_id'] ?? null,
            'name' => $data['name'] ?? $data['project_name'] ?? '',
            'description' => $data['description'] ?? '',
            'budget' => $data['budget'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'actual_hours' => $data['actual_hours'] ?? null,
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'status' => $data['status'] ?? 'planning',
            'priority' => $data['priority'] ?? 'medium',
            'location' => $data['location'] ?? '',
            'notes' => $data['notes'] ?? '',
            'date_created' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_projects', $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update project
     */
    public function updateProject($id, $data) {
        // Map the form fields to the actual database columns
        $db_data = [
            'contractor_id' => $data['contractor_id'] ?? '',
            'contract_id' => $data['contract_id'] ?? null,
            'name' => $data['name'] ?? $data['project_name'] ?? '',
            'description' => $data['description'] ?? '',
            'budget' => $data['budget'] ?? null,
            'estimated_hours' => $data['estimated_hours'] ?? null,
            'actual_hours' => $data['actual_hours'] ?? null,
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'status' => $data['status'] ?? 'planning',
            'priority' => $data['priority'] ?? 'medium',
            'location' => $data['location'] ?? '',
            'notes' => $data['notes'] ?? '',
            'date_updated' => date('Y-m-d H:i:s'),
            'updated_by' => get_staff_user_id()
        ];
        
        $this->db->where('id', $id);
        return $this->db->update('tblella_projects', $db_data);
    }
    
    /**
     * Delete project
     */
    public function deleteProject($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_projects');
    }
    
    // ========================================
    // PAYMENTS MANAGEMENT
    // ========================================
    
    /**
     * Get all payments with pagination and filters
     */
    public function getPayments($limit = 10, $offset = 0, $search = '', $status = '', $contractor_id = '') {
        // Ensure offset is never negative
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_payments')) {
            return [];
        }
        
        $this->db->select('p.*, co.company_name');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'p.contractor_id = co.id', 'left');
        
        // Only join with contracts table if it exists
        if ($this->db->table_exists('tblella_contracts')) {
            $this->db->select('c.title as project_name');
            $this->db->join('tblella_contracts c', 'p.contract_id = c.id', 'left');
        }
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.invoice_number', $search);
            $this->db->or_like('co.company_name', $search);
            if ($this->db->table_exists('tblella_contracts')) {
                $this->db->or_like('c.title', $search);
            }
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }
        
        if (!empty($contractor_id)) {
            $this->db->where('p.contractor_id', $contractor_id);
        }
        
        $this->db->order_by('p.date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get payment by ID with full details
     */
    public function getPaymentById($id) {
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_payments')) {
            return null;
        }
        
        $this->db->select('p.*, co.company_name, co.contact_person, co.email');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'p.contractor_id = co.id', 'left');
        
        // Only join with contracts table if it exists
        if ($this->db->table_exists('tblella_contracts')) {
            $this->db->select('c.title as project_name, c.contract_number');
            $this->db->join('tblella_contracts c', 'p.contract_id = c.id', 'left');
        }
        
        $this->db->where('p.id', $id);
        
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Create new payment
     */
    public function createPayment($data) {
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['date_updated'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();
        
        $this->db->insert('tblella_payments', $data);
        return $this->db->insert_id();
    }
    
    /**
     * Update payment
     */
    public function updatePayment($id, $data) {
        $data['date_updated'] = date('Y-m-d H:i:s');
        $data['updated_by'] = get_staff_user_id();
        
        $this->db->where('id', $id);
        return $this->db->update('tblella_payments', $data);
    }
    
    /**
     * Delete payment
     */
    public function deletePayment($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_payments');
    }
    
    // ========================================
    // DOCUMENTS MANAGEMENT
    // ========================================
    
    /**
     * Get documents for a contractor
     */
    public function getContractorDocuments($contractor_id, $limit = 20, $offset = 0) {
        $this->db->where('contractor_id', $contractor_id);
        $this->db->order_by('date_uploaded', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get('tblella_contractor_documents');
        return $query->result();
    }
    
    /**
     * Get document by ID
     */
    public function getDocumentById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('tblella_contractor_documents');
        return $query->row();
    }
    
    /**
     * Save document record
     */
    public function saveDocument($data) {
        $data['date_uploaded'] = date('Y-m-d H:i:s');
        
        if (isset($data['id'])) {
            // Update existing
            $id = $data['id'];
            unset($data['id']);
            $this->db->where('id', $id);
            return $this->db->update('tblella_contractor_documents', $data);
        } else {
            // Create new
            return $this->db->insert('tblella_contractor_documents', $data);
        }
    }
    
    /**
     * Delete document
     */
    public function deleteDocument($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_contractor_documents');
    }
    
    // ========================================
    // DASHBOARD STATISTICS
    // ========================================
    
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total contractors
        $stats['total_contractors'] = $this->db->count_all('tblella_contractors');
        
        // Active contractors
        $this->db->where('status', 'active');
        $stats['active_contractors'] = $this->db->count_all_results('tblella_contractors');
        
        // Total contracts
        $stats['total_contracts'] = $this->db->count_all('tblella_contracts');
        
        // Active contracts
        $this->db->where('status', 'active');
        $stats['active_contracts'] = $this->db->count_all_results('tblella_contracts');
        
        // Total projects
        $stats['total_projects'] = $this->db->count_all('tblella_projects');
        
        // Active projects
        $this->db->where('status', 'in_progress');
        $stats['active_projects'] = $this->db->count_all_results('tblella_projects');
        
        // Total revenue
        $this->db->select('SUM(amount) as total_revenue');
        $this->db->where('status', 'paid');
        $query = $this->db->get('tblella_payments');
        $result = $query->row();
        $stats['total_revenue'] = $result->total_revenue ?? 0;
        
        // Pending payments
        $this->db->select('SUM(amount) as pending_amount');
        $this->db->where('status', 'pending');
        $query = $this->db->get('tblella_payments');
        $result = $query->row();
        $stats['pending_payments'] = $result->pending_amount ?? 0;
        
        return $stats;
    }
    
    /**
     * Get recent contractors
     */
    public function getRecentContractors($limit = 5) {
        $this->db->select('*');
        $this->db->from('tblella_contractors');
        $this->db->order_by('date_created', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get active contracts
     */
    public function getActiveContracts($limit = 5) {
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_contracts')) {
            return [];
        }
        
        $this->db->select('c.*');
        $this->db->from('tblella_contracts c');
        
        // Only join with contractors table if it exists
        if ($this->db->table_exists('tblella_contractors')) {
            $this->db->select('co.company_name');
            $this->db->join('tblella_contractors co', 'c.contractor_id = co.id', 'left');
        }
        
        $this->db->where('c.status', 'active');
        $this->db->order_by('c.start_date', 'ASC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get pending payments
     */
    public function getPendingPayments($limit = 5) {
        // Check if required tables exist
        if (!$this->db->table_exists('tblella_payments')) {
            return [];
        }
        
        $this->db->select('p.*, co.company_name');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'p.contractor_id = co.id', 'left');
        
        // Only join with contracts table if it exists
        if ($this->db->table_exists('tblella_contracts')) {
            $this->db->select('c.title as project_name');
            $this->db->join('tblella_contracts c', 'p.contract_id = c.id', 'left');
        }
        
        $this->db->where('p.status', 'pending');
        $this->db->order_by('p.due_date', 'ASC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    // ========================================
    // SEARCH AND FILTERS
    // ========================================
    
    /**
     * Search across all entities
     */
    public function globalSearch($search_term, $limit = 20) {
        $results = [];
        
        // Search contractors
        if ($this->db->table_exists('tblella_contractors')) {
            $this->db->select('id, company_name, contact_person, "contractor" as type');
            $this->db->from('tblella_contractors');
            $this->db->like('company_name', $search_term);
            $this->db->or_like('contact_person', $search_term);
            $this->db->limit($limit);
            $contractors = $this->db->get()->result();
            $results = array_merge($results, $contractors);
        }
        
        // Search contracts
        if ($this->db->table_exists('tblella_contracts')) {
            $this->db->select('id, title, contract_number, "contract" as type');
            $this->db->from('tblella_contracts');
            $this->db->like('title', $search_term);
            $this->db->or_like('contract_number', $search_term);
            $this->db->limit($limit);
            $contracts = $this->db->get()->result();
            $results = array_merge($results, $contracts);
        }
        
        // Search projects
        if ($this->db->table_exists('tblella_projects')) {
            $this->db->select('id, name, description, "project" as type');
            $this->db->from('tblella_projects');
            $this->db->like('name', $search_term);
            $this->db->or_like('description', $search_term);
            $this->db->limit($limit);
            $projects = $this->db->get()->result();
            $results = array_merge($results, $projects);
        }
        
        return $results;
    }
}
