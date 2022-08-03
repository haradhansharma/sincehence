<?php
class ModelToolUpload extends Model {
	public function addUpload($name, $filename) {
		$code = sha1(uniqid(mt_rand(), true));

		$this->db->query("INSERT INTO `" . DB_PREFIX . "upload` SET `name` = '" . $this->db->escape($name) . "', `filename` = '" . $this->db->escape($filename) . "', `code` = '" . $this->db->escape($code) . "', `date_added` = NOW()");

		return $code;
	}

	public function getUploadByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "upload` WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}

	//
	public function getoptionid(){
		    $query=$this->db->query("SELECT option_id FROM " . DB_PREFIX . " `option` where `type` = 'select' AND `sort_order` = '999'");
       
		return $query->rows;

	}
	public function getinputdays($datee){
		    $query=$this->db->query("SELECT itd_deliverydays FROM " . DB_PREFIX . " `input_time_difference` where `itd_collection_date_slot1` ='".$datee."' ");
       
		return $query->rows;

	}

	//
}