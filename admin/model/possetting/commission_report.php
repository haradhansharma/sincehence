<?php
class ModelPossettingCommissionReport extends Model {
	public function deleteBarcodes($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_order` WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getReports($data) {
		$sql="select * from " . DB_PREFIX . "pos_order  po left join " . DB_PREFIX . "pos_user pu on pu.user_id=po.user_id where pu.commission_value<>0";
		
		if (isset($data['filter_username'])) {
		 $sql .=" and po.user_id like '".$this->db->escape($data['filter_username'])."%'";
		}

		if (isset($data['filter_order_id'])) {
		 $sql .=" and opo.rder_id like '".$this->db->escape($data['filter_order_id'])."%'";
		}

		$sort_data = array(
			'po.user_id',
			'po.order_id'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY po.user_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {

		 if ($data['start'] < 0) {
		 $data['start'] = 0;
		 }

		 if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalReport($data) {
		$sql="select count(*) as total from " . DB_PREFIX . "pos_order  po left join " . DB_PREFIX . "pos_user pu on pu.user_id=po.user_id where pu.commission_value<>0";
		
		if (isset($data['filter_username'])) {
		 $sql .=" and user_id like '".$this->db->escape($data['filter_username'])."%'";
		}

		if (isset($data['filter_order_id'])) {
		 $sql .=" and order_id like '".$this->db->escape($data['filter_order_id'])."%'";
		}

	
		

		$query = $this->db->query($sql);
		return $query->row['total'];
	}


	public function getUserName($user_id){		
		$sql="select * from " . DB_PREFIX . "pos_user where user_id='".$user_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	}

	public function getAmount($order_id){		
		$sql="select * from " . DB_PREFIX . "order where order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	} 

	public function getCommission($order_id){		
		$sql="select * from " . DB_PREFIX . "order where order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	} 


	
}