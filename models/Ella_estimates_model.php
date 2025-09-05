<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_estimates_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all estimates with line items count and totals
     */
    public function get_estimates($client_id = null, $lead_id = null, $status = null)
    {
        $this->db->select('e.*, 
                          CONCAT(s.firstname, " ", s.lastname) as created_by_name,
                          c.company as client_name,
                          l.name as lead_name');
        $this->db->from(db_prefix() . 'ella_contractor_estimates e');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = e.created_by', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = e.client_id', 'left');
        $this->db->join(db_prefix() . 'leads l', 'l.id = e.lead_id', 'left');
        
        if ($client_id) {
            $this->db->where('e.client_id', $client_id);
        }
        
        if ($lead_id) {
            $this->db->where('e.lead_id', $lead_id);
        }
        
        if ($status) {
            $this->db->where('e.status', $status);
        }
        
        $this->db->order_by('e.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Get single estimate by ID
     */
    public function get_estimate($id)
    {
        $this->db->select('e.*, 
                          CONCAT(s.firstname, " ", s.lastname) as created_by_name,
                          c.company as client_name,
                          l.name as lead_name');
        $this->db->from(db_prefix() . 'ella_contractor_estimates e');
        $this->db->join(db_prefix() . 'staff s', 's.staffid = e.created_by', 'left');
        $this->db->join(db_prefix() . 'clients c', 'c.userid = e.client_id', 'left');
        $this->db->join(db_prefix() . 'leads l', 'l.id = e.lead_id', 'left');
        $this->db->where('e.id', $id);
        
        return $this->db->get()->row();
    }

    /**
     * Create new estimate
     */
    public function create_estimate($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert(db_prefix() . 'ella_contractor_estimates', $data);
        $estimate_id = $this->db->insert_id();
        
        if ($estimate_id) {
            log_activity('New Estimate Created [ID: ' . $estimate_id . ', Name: ' . $data['estimate_name'] . ']');
            return $estimate_id;
        }
        
        return false;
    }

    /**
     * Update estimate
     */
    public function update_estimate($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_contractor_estimates', $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Estimate Updated [ID: ' . $id . ', Name: ' . $data['estimate_name'] . ']');
            return true;
        }
        
        return false;
    }

    /**
     * Delete estimate
     */
    public function delete_estimate($id)
    {
        $estimate = $this->get_estimate($id);
        
        if ($estimate) {
            // Delete estimate line items first (cascade will handle this)
            $this->db->where('estimate_id', $id);
            $this->db->delete(db_prefix() . 'ella_contractor_estimate_line_items');
            
            // Delete estimate
            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'ella_contractor_estimates');
            
            if ($this->db->affected_rows() > 0) {
                log_activity('Estimate Deleted [ID: ' . $id . ', Name: ' . $estimate->estimate_name . ']');
                return true;
            }
        }
        
        return false;
    }

    /**
     * Update estimate totals
     */
    public function update_estimate_totals($estimate_id)
    {
        // Get line items for this estimate
        $this->db->select('SUM(quantity) as total_quantity, SUM(total_price) as total_amount, COUNT(*) as line_items_count');
        $this->db->from(db_prefix() . 'ella_contractor_estimate_line_items');
        $this->db->where('estimate_id', $estimate_id);
        $result = $this->db->get()->row();
        
        $totals = [
            'total_quantity' => $result->total_quantity ?: 0,
            'total_amount' => $result->total_amount ?: 0,
            'line_items_count' => $result->line_items_count ?: 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $estimate_id);
        $this->db->update(db_prefix() . 'ella_contractor_estimates', $totals);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get estimate statuses
     */
    public function get_statuses()
    {
        return [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'expired' => 'Expired'
        ];
    }

    /**
     * Get estimate line items
     */
    public function get_estimate_line_items($estimate_id)
    {
        $this->db->select('eli.*, li.name as line_item_name, li.description as line_item_description, 
                          li.unit_type, li.image, lig.name as group_name');
        $this->db->from(db_prefix() . 'ella_contractor_estimate_line_items eli');
        $this->db->join(db_prefix() . 'ella_contractor_line_items li', 'li.id = eli.line_item_id', 'left');
        $this->db->join(db_prefix() . 'ella_contractor_line_item_groups lig', 'lig.id = li.group_id', 'left');
        $this->db->where('eli.estimate_id', $estimate_id);
        $this->db->order_by('li.group_name', 'ASC');
        $this->db->order_by('li.name', 'ASC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Add line item to estimate
     */
    public function add_line_item_to_estimate($estimate_id, $line_item_id, $quantity, $unit_price)
    {
        $data = [
            'estimate_id' => $estimate_id,
            'line_item_id' => $line_item_id,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $quantity * $unit_price,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert(db_prefix() . 'ella_contractor_estimate_line_items', $data);
        
        if ($this->db->affected_rows() > 0) {
            // Update estimate totals
            $this->update_estimate_totals($estimate_id);
            return $this->db->insert_id();
        }
        
        return false;
    }

    /**
     * Update line item in estimate
     */
    public function update_estimate_line_item($id, $quantity, $unit_price)
    {
        $data = [
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $quantity * $unit_price
        ];
        
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_contractor_estimate_line_items', $data);
        
        if ($this->db->affected_rows() > 0) {
            // Get estimate_id to update totals
            $this->db->select('estimate_id');
            $this->db->where('id', $id);
            $result = $this->db->get(db_prefix() . 'ella_contractor_estimate_line_items')->row();
            
            if ($result) {
                $this->update_estimate_totals($result->estimate_id);
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Remove line item from estimate
     */
    public function remove_line_item_from_estimate($id)
    {
        // Get estimate_id before deletion
        $this->db->select('estimate_id');
        $this->db->where('id', $id);
        $result = $this->db->get(db_prefix() . 'ella_contractor_estimate_line_items')->row();
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ella_contractor_estimate_line_items');
        
        if ($this->db->affected_rows() > 0 && $result) {
            // Update estimate totals
            $this->update_estimate_totals($result->estimate_id);
            return true;
        }
        
        return false;
    }
}

