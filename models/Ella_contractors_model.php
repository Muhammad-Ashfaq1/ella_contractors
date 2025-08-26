<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contractors_model extends CI_Model
{
    protected $table = 'tblella_contractors';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all contractors with optional filters
     */
    public function get_contractors($filters = [], $limit = null, $offset = 0, $order_by = 'company_name', $order_direction = 'ASC')
    {
        $this->db->select('*');
        $this->db->from($this->table);
        
        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('company_name', $search);
            $this->db->or_like('contact_person', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('city', $search);
            $this->db->or_like('state', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['specialization'])) {
            $this->db->like('specialties', $filters['specialization']);
        }
        
        // Apply ordering
        $this->db->order_by($order_by, $order_direction);
        
        // Apply pagination
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }
    
    /**
     * Get contractor by ID
     */
    public function get_contractor($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }
    
    /**
     * Get contractor by email
     */
    public function get_contractor_by_email($email)
    {
        return $this->db->get_where($this->table, ['email' => $email])->row();
    }
    
    /**
     * Create new contractor
     */
    public function create_contractor($data)
    {
        // Map the data to match the actual database structure
        $db_data = [
            'company_name' => $data['company_name'],
            'contact_person' => $data['contact_person'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'country' => $data['country'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'business_license' => $data['business_license'] ?? null,
            'insurance_info' => $data['insurance_info'] ?? null,
            'specialties' => $data['specialization'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'date_created' => date('Y-m-d H:i:s'),
            'created_by' => get_staff_user_id()
        ];
        
        $this->db->insert($this->table, $db_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update contractor
     */
    public function update_contractor($id, $data)
    {
        // Map the data to match the actual database structure
        $db_data = [
            'company_name' => $data['company_name'],
            'contact_person' => $data['contact_person'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'country' => $data['country'] ?? null,
            'tax_id' => $data['tax_id'] ?? null,
            'business_license' => $data['business_license'] ?? null,
            'insurance_info' => $data['insurance_info'] ?? null,
            'specialties' => $data['specialization'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'status' => $data['status'] ?? 'active',
            'notes' => $data['notes'] ?? null,
            'date_updated' => date('Y-m-d H:i:s'),
            'updated_by' => get_staff_user_id()
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $db_data);
    }
    
    /**
     * Delete contractor
     */
    public function delete_contractor($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
    
    /**
     * Get contractors count with filters
     */
    public function get_contractors_count($filters = [])
    {
        $this->db->from($this->table);
        
        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('company_name', $search);
            $this->db->or_like('contact_person', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('city', $search);
            $this->db->or_like('state', $search);
            $this->db->group_end();
        }
        
        if (!empty($filters['specialization'])) {
            $this->db->like('specialties', $filters['specialization']);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Get contractors statistics
     */
    public function get_contractors_stats()
    {
        $stats = [];
        
        // Total contractors
        $stats['total'] = $this->db->count_all($this->table);
        
        // Active contractors
        $stats['active'] = $this->db->where('status', 'active')->count_all_results($this->table);
        
        // Inactive contractors
        $stats['inactive'] = $this->db->where('status', 'inactive')->count_all_results($this->table);
        
        // Pending contractors
        $stats['pending'] = $this->db->where('status', 'pending')->count_all_results($this->table);
        
        // Blacklisted contractors
        $stats['blacklisted'] = $this->db->where('status', 'blacklisted')->count_all_results($this->table);
        
        // Recent contractors (last 30 days)
        $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
        $stats['recent'] = $this->db->where('date_created >=', $thirty_days_ago)->count_all_results($this->table);
        
        return $stats;
    }
    
    /**
     * Get contractors for dropdown
     */
    public function get_contractors_dropdown($include_inactive = false)
    {
        $this->db->select('id, company_name, contact_person');
        $this->db->from($this->table);
        
        if (!$include_inactive) {
            $this->db->where('status', 'active');
        }
        
        $this->db->order_by('company_name', 'ASC');
        
        $contractors = $this->db->get()->result();
        
        $dropdown = [];
        foreach ($contractors as $contractor) {
            $dropdown[$contractor->id] = $contractor->company_name . ' (' . $contractor->contact_person . ')';
        }
        
        return $dropdown;
    }
    
    /**
     * Check if email exists (for validation)
     */
    public function email_exists($email, $exclude_id = null)
    {
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        
        return $this->db->where('email', $email)->count_all_results($this->table) > 0;
    }
    
    /**
     * Bulk update contractor status
     */
    public function bulk_update_status($ids, $status)
    {
        $this->db->where_in('id', $ids);
        return $this->db->update($this->table, ['status' => $status]);
    }
    
    /**
     * Get contractors by specialization
     */
    public function get_contractors_by_specialization($specialization)
    {
        $this->db->like('specialties', $specialization);
        $this->db->where('status', 'active');
        return $this->db->get($this->table)->result();
    }
}
