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


	public function create($data)
	{
		$fillable = [
			'category','rel_type','rel_id','appointment_id','designator','name','location_label','level_label',
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
		
		// Debug: Log what we're inserting
		log_message('debug', 'Model create - Insert data: ' . json_encode($insert));
		log_message('debug', 'Model create - Table: ' . $this->table);
		
		$ok = $this->db->insert($this->table, $insert);
		
		// Debug: Log the result
		log_message('debug', 'Model create - Insert result: ' . ($ok ? 'SUCCESS' : 'FAILED'));
		if (!$ok) {
			log_message('error', 'Model create - Database error: ' . $this->db->last_query());
			log_message('error', 'Model create - Database error message: ' . $this->db->error()['message']);
		}
		
		return $ok ? $this->db->insert_id() : false;
	}

	public function update($id, $data)
	{
		$fillable = [
			'category','rel_type','rel_id','appointment_id','designator','name','location_label','level_label',
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


