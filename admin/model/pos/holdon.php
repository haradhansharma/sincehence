<?php
class ModelPosHoldON extends Model {
	public function addholdon($data){
		$holdcart = serialize($this->pos->getProducts());
		$holdon_no  = $data['holdon_no'].'-'.substr(md5(uniqid(rand(), true)), 0, 9);
		$this->db->query("INSERT INTO " . DB_PREFIX . "pos_holdon SET holdon_no = '" . $this->db->escape($holdon_no) . "',hold_option = '" . $holdcart . "', date_added = NOW()");
	}

	public function getholdon(){
		$query = $this->db->query("SELECT MAX(holdon_id) as lastid FROM " . DB_PREFIX . "pos_holdon");
		if(isset($query->row['lastid'])){
			return $query->row['lastid'];
		} else{
			return 0;
		}
	}

	public function getholdoreport($data){
		$sql = "SELECT * FROM " . DB_PREFIX . "pos_holdon WHERE holdon_id<>0";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getHoldNo($holdon_no){
		$sql = "SELECT * FROM " . DB_PREFIX . "pos_holdon WHERE holdon_no='".$holdon_no."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getholdProductId($holdon_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "pos_holdon WHERE holdon_id='".(int)$holdon_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function deleteHoldOn($holdon_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pos_holdon WHERE holdon_id = '" . (int)$holdon_id . "'");
	}
	
	public function getTotalHoldOn($data){
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pos_holdon WHERE holdon_id<>0";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getPosProduct($product_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "pos_product WHERE product_id = '" . (int)$product_id . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}

}