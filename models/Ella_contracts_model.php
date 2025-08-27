<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contracts_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'tblella_contracts';
    }

    /**
     * Get all contracts with filters and pagination
     */
    public function get_contracts($filters = [], $page = 1, $limit = 20)
    {
        $this->db->select('c.*, l.name as lead_name, l.email as lead_email, l.phonenumber as lead_phone, l.status as lead_status,
                           cont.company_name as contractor_name, cont.contact_person as contractor_contact, 
                           cont.email as contractor_email, cont.phone as contractor_phone');
        $this->db->from($this->table . ' c');
        $this->db->join('tblleads l', 'l.id = c.lead_id', 'left');
        $this->db->join('tblella_contractors cont', 'cont.id = c.contractor_id', 'left');
        
        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('c.contract_number', $filters['search']);
            $this->db->or_like('c.subject', $filters['search']);
            $this->db->or_like('l.name', $filters['search']);
            $this->db->or_like('cont.company_name', $filters['search']);
            $this->db->group_end();
        }
        
        if (!empty($filters['lead_id'])) {
            $this->db->where('c.lead_id', $filters['lead_id']);
        }
        
        if (!empty($filters['contractor_id'])) {
            $this->db->where('c.contractor_id', $filters['contractor_id']);
        }
        
        // Count total records for pagination
        $total_records = $this->db->count_all_results('', false);
        
        // Apply pagination
        $offset = ($page - 1) * $limit;
        $this->db->limit($limit, $offset);
        
        // Order by
        $this->db->order_by('c.date_created', 'DESC');
        
        $contracts = $this->db->get()->result();
        
        return [
            'contracts' => $contracts,
            'total_records' => $total_records,
            'total_pages' => ceil($total_records / $limit),
            'current_page' => $page
        ];
    }

    /**
     * Get single contract by ID
     */
    public function get_contract($id)
    {
        $this->db->select('c.*, l.name as lead_name, l.email as lead_email, l.phonenumber as lead_phone, 
                           l.company as lead_company, l.address as lead_address, l.city as lead_city, 
                           l.state as lead_state, l.zip as lead_zip, l.country as lead_country, l.status as lead_status,
                           cont.company_name as contractor_name, cont.contact_person as contractor_contact, 
                           cont.email as contractor_email, cont.phone as contractor_phone, cont.specialties,
                           cont.hourly_rate as contractor_hourly_rate');
        $this->db->from($this->table . ' c');
        $this->db->join('tblleads l', 'l.id = c.lead_id', 'left');
        $this->db->join('tblella_contractors cont', 'cont.id = c.contractor_id', 'left');
        $this->db->where('c.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Create new contract
     */
    public function create_contract($data)
    {
        // Generate contract number
        $data['contract_number'] = $this->generate_contract_number();
        
        // Set timestamps
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['created_by'] = get_staff_user_id();
        
        // Format dates
        if (!empty($data['start_date'])) {
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        }
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update existing contract
     */
    public function update_contract($id, $data)
    {
        // Set timestamps
        $data['date_updated'] = date('Y-m-d H:i:s');
        $data['updated_by'] = get_staff_user_id();
        
        // Format dates
        if (!empty($data['start_date'])) {
            $data['start_date'] = date('Y-m-d', strtotime($data['start_date']));
        }
        if (!empty($data['end_date'])) {
            $data['end_date'] = date('Y-m-d', strtotime($data['end_date']));
        }
        
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete contract
     */
    public function delete_contract($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get contracts count by status
     */
    public function get_contracts_count($filters = [])
    {
        $this->db->select('status, COUNT(*) as count');
        $this->db->from($this->table);
        
        if (!empty($filters['lead_id'])) {
            $this->db->where('lead_id', $filters['lead_id']);
        }
        
        if (!empty($filters['contractor_id'])) {
            $this->db->where('contractor_id', $filters['contractor_id']);
        }
        
        $this->db->group_by('status');
        $result = $this->db->get()->result_array();
        
        $counts = [
            'draft' => 0,
            'active' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'expired' => 0
        ];
        
        foreach ($result as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }
        
        return $counts;
    }

    /**
     * Get contracts statistics
     */
    public function get_contracts_stats()
    {
        $stats = [
            'total' => $this->db->count_all($this->table),
            'total_value' => 0,
            'recent' => 0
        ];
        
        // Get total value
        $this->db->select('SUM(contract_value) as total_value');
        $this->db->from($this->table);
        $this->db->where('status', 'active');
        $result = $this->db->get()->row();
        $stats['total_value'] = $result ? $result->total_value : 0;
        
        // Get recent contracts (last 30 days)
        $this->db->where('date_created >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $stats['recent'] = $this->db->count_all_results($this->table);
        
        return $stats;
    }

    /**
     * Generate unique contract number
     */
    private function generate_contract_number()
    {
        $prefix = 'CON';
        $year = date('Y');
        $month = date('m');
        
        // Get last contract number for this month
        $this->db->select('contract_number');
        $this->db->from($this->table);
        $this->db->like('contract_number', $prefix . $year . $month);
        $this->db->order_by('contract_number', 'DESC');
        $this->db->limit(1);
        
        $result = $this->db->get()->row();
        
        if ($result) {
            // Extract sequence number and increment
            $last_number = $result->contract_number;
            $sequence = (int)substr($last_number, -4) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get leads for dropdown (all leads)
     */
    public function get_leads_for_contracts()
    {
        $this->db->select('id, name, email, phonenumber, company, status');
        $this->db->from('tblleads');
        $this->db->where('junk', 0); // Not junk
        $this->db->order_by('name', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get contractors for dropdown
     */
    public function get_contractors_for_contracts()
    {
        $this->db->select('id, company_name, contact_person, email, phone, specialties');
        $this->db->from('tblella_contractors');
        $this->db->where('status', 'active');
        $this->db->order_by('company_name', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Check if lead already has active contract
     */
    public function lead_has_active_contract($lead_id)
    {
        $this->db->where('lead_id', $lead_id);
        $this->db->where_in('status', ['draft', 'active']);
        
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Bulk update contract status
     */
    public function bulk_update_status($contract_ids, $status)
    {
        $this->db->where_in('id', $contract_ids);
        $this->db->update($this->table, [
            'status' => $status,
            'date_updated' => date('Y-m-d H:i:s'),
            'updated_by' => get_staff_user_id()
        ]);
        
        return $this->db->affected_rows();
    }

    /**
     * Get contracts with portal status
     */
    public function get_contracts_with_portal_status()
    {
        $this->db->select('c.*, l.name as lead_name, l.email as lead_email, l.phonenumber as lead_phone, 
                           cont.company_name as contractor_name, cont.contact_person as contractor_contact');
        $this->db->from($this->table . ' c');
        $this->db->join('tblleads l', 'l.id = c.lead_id', 'left');
        $this->db->join('tblella_contractors cont', 'cont.id = c.contractor_id', 'left');
        $this->db->order_by('c.date_created', 'DESC');
        
        return $this->db->get()->result();
    }
}
