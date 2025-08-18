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
     * Get contractors count for pagination
     */
    public function getContractorsCount($search = '', $status = '') {
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
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get all contractors without pagination (for dropdowns)
     */
    public function getAllContractors() {
        $this->db->select('id, company_name, contact_person, status');
        $this->db->from('tblella_contractors');
        $this->db->where('status !=', 'deleted');
        $this->db->order_by('company_name', 'ASC');
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
     * Search contractors for AJAX
     */
    public function searchContractors($search) {
        $this->db->select('id, company_name, contact_person, email, phone');
        $this->db->from('tblella_contractors');
        $this->db->like('company_name', $search);
        $this->db->or_like('contact_person', $search);
        $this->db->or_like('email', $search);
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result();
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
     * Get contractors by status count
     */
    public function getContractorsByStatusCount($status) {
        $this->db->where('status', $status);
        return $this->db->count_all_results('tblella_contractors');
    }
    
    // ========================================
    // CONTRACTS MANAGEMENT
    // ========================================
    
    /**
     * Get all contracts with pagination and search
     */
    public function getContracts($limit = 10, $offset = 0, $search = '', $status = '') {
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        $this->db->select('c.*, co.company_name');
        $this->db->from('tblella_contracts c');
        $this->db->join('tblella_contractors co', 'co.id = c.contractor_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('c.title', $search);
            $this->db->or_like('co.company_name', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('c.status', $status);
        }
        
        $this->db->order_by('c.date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get contracts count for pagination
     */
    public function getContractsCount($search = '', $status = '') {
        $this->db->from('tblella_contracts c');
        $this->db->join('tblella_contractors co', 'co.id = c.contractor_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('c.title', $search);
            $this->db->or_like('co.company_name', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('c.status', $status);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get contract by ID
     */
    public function getContractById($id) {
        $this->db->select('c.*, co.company_name');
        $this->db->from('tblella_contracts c');
        $this->db->join('tblella_contractors co', 'co.id = c.contractor_id', 'left');
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Create new contract
     */
    public function createContract($data) {
        $db_data = [
            'title' => $data['title'] ?? '',
            'contractor_id' => $data['contractor_id'] ?? '',
            'description' => $data['description'] ?? '',
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'amount' => $data['amount'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'terms' => $data['terms'] ?? '',
            'notes' => $data['notes'] ?? '',
            'date_created' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_contracts', $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update contract
     */
    public function updateContract($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tblella_contracts', $data);
    }
    
    /**
     * Delete contract
     */
    public function deleteContract($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_contracts');
    }
    
    /**
     * Get contracts by contractor
     */
    public function getContractsByContractor($contractor_id) {
        $this->db->select('*');
        $this->db->from('tblella_contracts');
        $this->db->where('contractor_id', $contractor_id);
        $this->db->order_by('date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get active contracts by contractor
     */
    public function getActiveContractsByContractor($contractor_id) {
        $this->db->where('contractor_id', $contractor_id);
        $this->db->where('status', 'active');
        return $this->db->get('tblella_contracts')->result();
    }
    
    /**
     * Get recent contracts
     */
    public function getRecentContracts($limit = 5) {
        $this->db->select('c.*, co.company_name');
        $this->db->from('tblella_contracts c');
        $this->db->join('tblella_contractors co', 'co.id = c.contractor_id', 'left');
        $this->db->order_by('c.date_created', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get all contracts for export
     */
    public function getAllContractsForExport() {
        $this->db->select('c.*, co.company_name');
        $this->db->from('tblella_contracts c');
        $this->db->join('tblella_contractors co', 'co.id = c.contractor_id', 'left');
        $this->db->order_by('c.date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    // ========================================
    // PROJECTS MANAGEMENT
    // ========================================
    
    /**
     * Get all projects with pagination and search
     */
    public function getProjects($limit = 10, $offset = 0, $search = '', $status = '') {
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        $this->db->select('p.*, co.company_name');
        $this->db->from('tblella_projects p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.name', $search);
            $this->db->or_like('co.company_name', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }
        
        $this->db->order_by('p.date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get projects count for pagination
     */
    public function getProjectsCount($search = '', $status = '') {
        $this->db->from('tblella_projects p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.name', $search);
            $this->db->or_like('co.company_name', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get project by ID
     */
    public function getProjectById($id) {
        $this->db->select('p.*, co.company_name');
        $this->db->from('tblella_projects p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Create new project
     */
    public function createProject($data) {
        $db_data = [
            'name' => $data['name'] ?? '',
            'contractor_id' => $data['contractor_id'] ?? '',
            'description' => $data['description'] ?? '',
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'budget' => $data['budget'] ?? 0,
            'status' => $data['status'] ?? 'planning',
            'location' => $data['location'] ?? '',
            'progress' => $data['progress'] ?? 0,
            'notes' => $data['notes'] ?? '',
            'date_created' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_projects', $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update project
     */
    public function updateProject($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tblella_projects', $data);
    }
    
    /**
     * Delete project
     */
    public function deleteProject($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tblella_projects');
    }
    
    /**
     * Get projects by contractor
     */
    public function getProjectsByContractor($contractor_id) {
        $this->db->select('*');
        $this->db->from('tblella_projects');
        $this->db->where('contractor_id', $contractor_id);
        $this->db->order_by('date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get active projects by contractor
     */
    public function getActiveProjectsByContractor($contractor_id) {
        $this->db->where('contractor_id', $contractor_id);
        $this->db->where('status', 'active');
        return $this->db->get('tblella_projects')->result();
    }
    
    /**
     * Get all projects for export
     */
    public function getAllProjectsForExport() {
        $this->db->select('p.*, co.company_name');
        $this->db->from('tblella_projects p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->order_by('p.date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    // ========================================
    // PAYMENTS MANAGEMENT
    // ========================================
    
    /**
     * Get all payments with pagination and search
     */
    public function getPayments($limit = 10, $offset = 0, $search = '', $status = '') {
        $offset = max(0, (int)$offset);
        $limit = max(1, (int)$limit);
        
        $this->db->select('p.*, co.company_name, c.title as contract_title');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->join('tblella_contracts c', 'c.id = p.contract_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('co.company_name', $search);
            $this->db->or_like('c.title', $search);
            $this->db->or_like('p.reference_number', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }
        
        $this->db->order_by('p.date_created', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get payments count for pagination
     */
    public function getPaymentsCount($search = '', $status = '') {
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->join('tblella_contracts c', 'c.id = p.contract_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('co.company_name', $search);
            $this->db->or_like('c.title', $search);
            $this->db->or_like('p.reference_number', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('p.status', $status);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get payment by ID
     */
    public function getPaymentById($id) {
        $this->db->select('p.*, co.company_name, c.title as contract_title');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->join('tblella_contracts c', 'c.id = p.contract_id', 'left');
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Create new payment
     */
    public function createPayment($data) {
        $db_data = [
            'contractor_id' => $data['contractor_id'] ?? '',
            'contract_id' => $data['contract_id'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'payment_date' => $data['payment_date'] ?? '',
            'payment_method' => $data['payment_method'] ?? 'check',
            'reference_number' => $data['reference_number'] ?? '',
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? '',
            'date_created' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_payments', $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update payment
     */
    public function updatePayment($id, $data) {
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
    
    /**
     * Get payments by contractor
     */
    public function getPaymentsByContractor($contractor_id) {
        $this->db->select('p.*, c.title as contract_title');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contracts c', 'c.id = p.contract_id', 'left');
        $this->db->where('p.contractor_id', $contractor_id);
        $this->db->order_by('p.date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get recent payments
     */
    public function getRecentPayments($limit = 5) {
        $this->db->select('p.*, co.company_name');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->order_by('p.date_created', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get all payments for export
     */
    public function getAllPaymentsForExport() {
        $this->db->select('p.*, co.company_name, c.title as contract_title');
        $this->db->from('tblella_payments p');
        $this->db->join('tblella_contractors co', 'co.id = p.contractor_id', 'left');
        $this->db->join('tblella_contracts c', 'c.id = p.contract_id', 'left');
        $this->db->order_by('p.date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    // ========================================
    // DOCUMENTS MANAGEMENT
    // ========================================
    
    /**
     * Get documents by contractor
     */
    public function getDocumentsByContractor($contractor_id) {
        $this->db->select('*');
        $this->db->from('tblella_contractor_documents');
        $this->db->where('contractor_id', $contractor_id);
        $this->db->order_by('date_uploaded', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get all documents
     */
    public function getAllDocuments() {
        $this->db->select('d.*, co.company_name');
        $this->db->from('tblella_contractor_documents d');
        $this->db->join('tblella_contractors co', 'co.id = d.contractor_id', 'left');
        $this->db->order_by('d.date_uploaded', 'DESC');
        $query = $this->db->get();
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
     * Create new document
     */
    public function createDocument($data) {
        $db_data = [
            'contractor_id' => $data['contractor_id'] ?? '',
            'title' => $data['title'] ?? '',
            'document_type' => $data['document_type'] ?? 'other',
            'file_name' => $data['file_name'] ?? '',
            'file_path' => $data['file_path'] ?? '',
            'file_size' => $data['file_size'] ?? 0,
            'file_type' => $data['file_type'] ?? '',
            'description' => $data['description'] ?? '',
            'date_uploaded' => date('Y-m-d H:i:s'),
            'uploaded_by' => get_staff_user_id()
        ];
        
        $this->db->insert('tblella_contractor_documents', $db_data);
        return $this->db->insert_id();
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
     * Get active contracts count
     */
    public function getActiveContractsCount() {
        $this->db->where('status', 'active');
        return $this->db->count_all_results('tblella_contracts');
    }
    
    /**
     * Get pending payments count
     */
    public function getPendingPaymentsCount() {
        $this->db->where('status', 'pending');
        return $this->db->count_all_results('tblella_payments');
    }
    
    /**
     * Get active projects count
     */
    public function getActiveProjectsCount() {
        $this->db->where('status', 'active');
        return $this->db->count_all_results('tblella_projects');
    }
    
    /**
     * Get contractors by status counts
     */
    public function getContractorsByStatusCounts() {
        $this->db->select('status, COUNT(*) as count');
        $this->db->from('tblella_contractors');
        $this->db->group_by('status');
        $query = $this->db->get();
        
        $result = [];
        foreach ($query->result() as $row) {
            $result[$row->status] = $row->count;
        }
        
        return $result;
    }
    
    /**
     * Get contracts by status counts
     */
    public function getContractsByStatusCounts() {
        $this->db->select('status, COUNT(*) as count');
        $this->db->from('tblella_contracts');
        $this->db->group_by('status');
        $query = $this->db->get();
        
        $result = [];
        foreach ($query->result() as $row) {
            $result[$row->status] = $row->count;
        }
        
        return $result;
    }
    
    // ========================================
    // EXPORT FUNCTIONS
    // ========================================
    
    /**
     * Get all contractors for export
     */
    public function getAllContractorsForExport() {
        $this->db->select('*');
        $this->db->from('tblella_contractors');
        $this->db->order_by('date_created', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
}
