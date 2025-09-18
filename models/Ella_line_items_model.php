<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ella_line_items_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all line items
     */
    public function get_line_items($group_id = null, $active_only = false)
    {
        $items  = db_prefix() . 'ella_contractor_line_items';
        $groups = db_prefix() . 'ella_contractor_line_item_groups';

        $this->db->select("
            {$items}.*,
            {$groups}.name AS group_name,
            {$groups}.id   AS group_pk
        ");
        $this->db->from($items);
        $this->db->join($groups, "{$groups}.id = {$items}.group_id", 'left');

        if (!is_null($group_id)) {
            $this->db->where("{$items}.group_id", $group_id);
        }

        if ($active_only) {
            $this->db->where("{$items}.is_active", 1);
        }

        $this->db->order_by("{$groups}.name", 'ASC');
        $this->db->order_by("{$items}.name", 'ASC');

        return $this->db->get()->result_array();
    }


    /**
     * Get single line item by ID
     */
    public function get_line_item($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'ella_contractor_line_items')->row();
    }

    /**
     * Create new line item
     */
    public function create_line_item($data)
    {
        $this->db->insert(db_prefix() . 'ella_contractor_line_items', $data);
        return $this->db->insert_id();
    }

    /**
     * Update line item
     */
    public function update_line_item($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_contractor_line_items', $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete line item
     */
    public function delete_line_item($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ella_contractor_line_items');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Toggle line item active status
     */
    public function toggle_active($id)
    {
        $item = $this->get_line_item($id);
        if (!$item) {
            return false;
        }
        
        $new_status = $item->is_active ? 0 : 1;
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ella_contractor_line_items', ['is_active' => $new_status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get line items by group name
     */
    public function get_by_group($group_name, $active_only = false)
    {
        $this->db->where('group_name', $group_name);
        
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        
        $this->db->order_by(db_prefix() . 'ella_contractor_line_items.name', 'ASC');
        return $this->db->get(db_prefix() . 'ella_contractor_line_items')->result_array();
    }

    /**
     * Search line items
     */
    public function search($term, $group_name = null, $active_only = false)
    {
        $this->db->group_start();
        $this->db->like(db_prefix() . 'ella_contractor_line_items.name', $term);
        $this->db->or_like(db_prefix() . 'ella_contractor_line_items.description', $term);
        
        // Only search group_name if column exists
        if ($this->db->field_exists('group_name', db_prefix() . 'ella_contractor_line_items')) {
            $this->db->or_like('group_name', $term);
        }
        $this->db->group_end();
        
        if ($group_name && $this->db->field_exists('group_name', db_prefix() . 'ella_contractor_line_items')) {
            $this->db->where('group_name', $group_name);
        }
        
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        
        // Check if group_name column exists before ordering
        if ($this->db->field_exists('group_name', db_prefix() . 'ella_contractor_line_items')) {
            $this->db->order_by('group_name', 'ASC');
        }
        $this->db->order_by(db_prefix() . 'ella_contractor_line_items.name', 'ASC');
        
        return $this->db->get(db_prefix() . 'ella_contractor_line_items')->result_array();
    }

    /**
     * Get available unit types
     */
    public function get_unit_types()
    {
        return [
            'Square Foot' => 'Square Foot',
            'Square Yard' => 'Square Yard',
            'Square Meter' => 'Square Meter',
            'Linear Foot' => 'Linear Foot',
            'Linear Yard' => 'Linear Yard',
            'Linear Meter' => 'Linear Meter',
            'Inch' => 'Inch',
            'Foot' => 'Foot',
            'Yard' => 'Yard',
            'Meter' => 'Meter',
            'Piece' => 'Piece',
            'Each' => 'Each',
            'Set' => 'Set',
            'Pair' => 'Pair',
            'Dozen' => 'Dozen',
            'Hour' => 'Hour',
            'Day' => 'Day',
            'Week' => 'Week',
            'Month' => 'Month',
            'Gallon' => 'Gallon',
            'Liter' => 'Liter',
            'Pound' => 'Pound',
            'Kilogram' => 'Kilogram',
            'Ton' => 'Ton',
            'Cubic Foot' => 'Cubic Foot',
            'Cubic Yard' => 'Cubic Yard',
            'Cubic Meter' => 'Cubic Meter'
        ];
    }

    /**
     * Calculate total cost for line item
     */
    public function calculate_total($cost, $quantity)
    {
        if (is_null($cost) || is_null($quantity)) {
            return 0;
        }
        return $cost * $quantity;
    }

    /**
     * Add new line item
     */
    public function add($data)
    {
        unset($data['itemid']);
        
        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        $this->db->insert(db_prefix() . 'ella_contractor_line_items', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            hooks()->do_action('line_item_created', $insert_id);
            log_activity('New Line Item Added [ID:' . $insert_id . ', ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Edit line item
     */
    public function edit($data)
    {
        $itemid = $data['itemid'];
        unset($data['itemid']);

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        $affectedRows = 0;

        $data = hooks()->apply_filters('before_update_line_item', $data, $itemid);

        $this->db->where('id', $itemid);
        $this->db->update(db_prefix() . 'ella_contractor_line_items', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Line Item Updated [ID: ' . $itemid . ', ' . $data['name'] . ']');
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            hooks()->do_action('line_item_updated', $itemid);
        }

        return $affectedRows > 0 ? true : false;
    }

    /**
     * Delete line item
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ella_contractor_line_items');
        if ($this->db->affected_rows() > 0) {
            log_activity('Line Item Deleted [ID: ' . $id . ']');
            hooks()->do_action('line_item_deleted', $id);
            return true;
        }
        return false;
    }

    /**
     * Get single line item by ID (for AJAX)
     */
    public function get($id)
    {
        $this->db->select('*,' . db_prefix() . 'ella_contractor_line_item_groups.name as group_name');
        $this->db->from(db_prefix() . 'ella_contractor_line_items');
        $this->db->join(db_prefix() . 'ella_contractor_line_item_groups', db_prefix() . 'ella_contractor_line_item_groups.id = ' . db_prefix() . 'ella_contractor_line_items.group_id', 'left');
        $this->db->where(db_prefix() . 'ella_contractor_line_items.id', $id);
        return $this->db->get()->row();
    }
}
