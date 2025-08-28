<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_contract_notes_model extends CI_Model
{
    protected $table = 'tblella_contract_notes';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all notes for a specific contract
     */
    public function get_contract_notes($contract_id, $public_only = false)
    {
        $this->db->select('n.*, s.firstname as created_by_name, s.lastname as created_by_lastname');
        $this->db->from($this->table . ' n');
        $this->db->join('tblstaff s', 's.staffid = n.created_by', 'left');
        $this->db->where('n.contract_id', $contract_id);
        
        if ($public_only) {
            $this->db->where('n.is_public', 1);
        }
        
        $this->db->order_by('n.created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get a single note by ID
     */
    public function get_note($note_id)
    {
        $this->db->select('n.*, s.firstname as created_by_name, s.lastname as created_by_lastname');
        $this->db->from($this->table . ' n');
        $this->db->join('tblstaff s', 's.staffid = n.created_by', 'left');
        $this->db->where('n.id', $note_id);
        
        return $this->db->get()->row();
    }
    
    /**
     * Create a new note
     */
    public function create_note($data)
    {
        $note_data = [
            'contract_id' => $data['contract_id'],
            'note_title' => $data['note_title'],
            'note_content' => $data['note_content'],
            'note_type' => $data['note_type'] ?? 'general',
            'is_public' => $data['is_public'] ?? 1,
            'created_by' => $data['created_by'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert($this->table, $note_data);
        return $this->db->insert_id();
    }
    
    /**
     * Update an existing note
     */
    public function update_note($note_id, $data)
    {
        $update_data = [
            'note_title' => $data['note_title'],
            'note_content' => $data['note_content'],
            'note_type' => $data['note_type'] ?? 'general',
            'is_public' => $data['is_public'] ?? 1,
            'updated_by' => $data['updated_by'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $note_id);
        return $this->db->update($this->table, $update_data);
    }
    
    /**
     * Delete a note
     */
    public function delete_note($note_id)
    {
        $this->db->where('id', $note_id);
        return $this->db->delete($this->table);
    }
    
    /**
     * Get notes summary for a contract
     */
    public function get_notes_summary($contract_id)
    {
        $this->db->select('COUNT(*) as total, note_type, is_public');
        $this->db->from($this->table);
        $this->db->where('contract_id', $contract_id);
        $this->db->group_by('note_type, is_public');
        
        $results = $this->db->get()->result();
        
        $summary = [
            'total' => 0,
            'public' => 0,
            'private' => 0,
            'by_type' => []
        ];
        
        foreach ($results as $result) {
            $summary['total'] += $result->total;
            
            if ($result->is_public) {
                $summary['public'] += $result->total;
            } else {
                $summary['private'] += $result->total;
            }
            
            if (!isset($summary['by_type'][$result->note_type])) {
                $summary['by_type'][$result->note_type] = 0;
            }
            $summary['by_type'][$result->note_type] += $result->total;
        }
        
        return $summary;
    }
    
    /**
     * Get notes by type for a contract
     */
    public function get_notes_by_type($contract_id, $note_type, $public_only = false)
    {
        $this->db->select('n.*, s.firstname as created_by_name, s.lastname as created_by_lastname');
        $this->db->from($this->table . ' n');
        $this->db->join('tblstaff s', 's.staffid = n.created_by', 'left');
        $this->db->where('n.contract_id', $contract_id);
        $this->db->where('n.note_type', $note_type);
        
        if ($public_only) {
            $this->db->where('n.is_public', 1);
        }
        
        $this->db->order_by('n.created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Search notes by content
     */
    public function search_notes($contract_id, $search_term, $public_only = false)
    {
        $this->db->select('n.*, s.firstname as created_by_name, s.lastname as created_by_lastname');
        $this->db->from($this->table . ' n');
        $this->db->join('tblstaff s', 's.staffid = n.created_by', 'left');
        $this->db->where('n.contract_id', $contract_id);
        
        if ($public_only) {
            $this->db->where('n.is_public', 1);
        }
        
        $this->db->group_start();
        $this->db->like('n.note_title', $search_term);
        $this->db->or_like('n.note_content', $search_term);
        $this->db->group_end();
        
        $this->db->order_by('n.created_at', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get recent notes across all contracts
     */
    public function get_recent_notes($limit = 10, $public_only = false)
    {
        $this->db->select('n.*, c.subject as contract_title, s.firstname as created_by_name, s.lastname as created_by_lastname');
        $this->db->from($this->table . ' n');
        $this->db->join('tblella_contracts c', 'c.id = n.contract_id', 'left');
        $this->db->join('tblstaff s', 's.staffid = n.created_by', 'left');
        
        if ($public_only) {
            $this->db->where('n.is_public', 1);
        }
        
        $this->db->order_by('n.created_at', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }
}
