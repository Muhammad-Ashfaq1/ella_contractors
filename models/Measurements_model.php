<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Measurements_model extends App_Model
{
	protected $table;

	public function __construct()
	{
		parent::__construct();
		$this->table = db_prefix() . 'ella_contractors_measurements';
	}

	public function list($category, $params = [])
	{
		$this->db->from($this->table);
		$this->db->where('category', $category);
		if (!empty($params['rel_type']) && !empty($params['rel_id'])) {
			$this->db->where('rel_type', $params['rel_type']);
			$this->db->where('rel_id', (int) $params['rel_id']);
		}
		$this->db->order_by('sort_order ASC, id DESC');
		$rows = $this->db->get()->result_array();
		return ['data' => $rows];
	}

	public function create($data)
	{
		$fillable = [
			'category','rel_type','rel_id','designator','name','location_label','level_label',
			'quantity','width_val','height_val','length_val','area_val','united_inches_val',
			'length_unit','area_unit','ui_unit','attributes_json','notes','status_code','sort_order',
			'intCreatedByCode','intAlteredByCode','intRecordStatusCode','intBranchCode','intCompanyCode'
		];
		$insert = [];
		foreach ($fillable as $f) {
			if (isset($data[$f])) {
				$insert[$f] = $data[$f];
			}
		}
		$insert['dtmCreated'] = date('Y-m-d H:i:s');
		$ok = $this->db->insert($this->table, $insert);
		return $ok ? $this->db->insert_id() : false;
	}

	public function update($id, $data)
	{
		$fillable = [
			'category','rel_type','rel_id','designator','name','location_label','level_label',
			'quantity','width_val','height_val','length_val','area_val','united_inches_val',
			'length_unit','area_unit','ui_unit','attributes_json','notes','status_code','sort_order',
			'intAlteredByCode','intRecordStatusCode','intBranchCode','intCompanyCode'
		];
		$update = [];
		foreach ($fillable as $f) {
			if (isset($data[$f])) {
				$update[$f] = $data[$f];
			}
		}
		$this->db->where('id', (int) $id);
		return $this->db->update($this->table, $update);
	}

	public function delete($id)
	{
		$this->db->where('id', (int) $id);
		return $this->db->delete($this->table);
	}

	public function find($id)
	{
		return $this->db->get_where($this->table, ['id' => (int) $id])->row_array();
	}
}


