<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_line_item_groups_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all groups
     */
    public function get_groups()
    {
        $this->db->order_by('name', 'asc');
        return $this->db->get(db_prefix() . 'ella_contractor_line_item_groups')->result_array();
    }

    /**
     * Get single group by ID
     */
    public function get_group($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ella_contractor_line_item_groups')->row();
    }

    /**
     * Add new group
     */
    public function add_group($data)
    {
        $this->db->insert(db_prefix() . 'ella_contractor_line_item_groups', $data);
        log_activity('Line Item Group Created [Name: ' . $data['name'] . ']');
        return $this->db->insert_id();
    }

    /**
     * Edit group
     */
    public function edit_group($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_contractor_line_item_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Line Item Group Updated [Name: ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete group
     */
    public function delete_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get(db_prefix() . 'ella_contractor_line_item_groups')->row();

        if ($group) {
            // Update line items to remove group reference
            $this->db->where('group_id', $id);
            $this->db->update(db_prefix() . 'ella_contractor_line_items', [
                'group_id' => 0,
            ]);

            $this->db->where('id', $id);
            $this->db->delete(db_prefix() . 'ella_contractor_line_item_groups');

            log_activity('Line Item Group Deleted [Name: ' . $group->name . ']');
            return true;
        }

        return false;
    }
}
