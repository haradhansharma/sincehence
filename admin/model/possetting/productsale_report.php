<?php
class ModelPossettingProductsalereport extends Model {
	public function getProductSales($data=array()){
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}
		$sql="select *,(select sum(quantity) AS total from " . DB_PREFIX . "order_product op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ") and op.product_id=p.product_id) as totalsale  from " . DB_PREFIX . "product p LEFT JOIN " .DB_PREFIX. "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id<>0";
		
		$sort_data = array(
			'p.product_id',
			'totalsale'
		);

		if (!empty($data['filter_productid'])) {
			$sql .= " AND p.product_id LIKE '" . $this->db->escape($data['filter_productid']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND p.date_added LIKE '" . $this->db->escape($data['filter_date_added']) . "%'";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY p.product_id";
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

	
	public function getTotalProductSales($data=array()) {
		$sql="select COUNT(*) AS total from " . DB_PREFIX . "product p LEFT JOIN " .DB_PREFIX. "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id<>0";
		
		if (!empty($data['filter_productid'])) {
			$sql .= " AND p.product_id LIKE '" . $this->db->escape($data['filter_productid']) . "%'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND p.date_added LIKE '" . $this->db->escape($data['filter_date_added']) . "%'";
		}

		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getTotalSales($product_id) {
		$implode = array();

		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			$implode[] = "'" . (int)$order_status_id . "'";
		}

		$sql="select sum(quantity) AS total from " . DB_PREFIX . "order_product op LEFT JOIN " .DB_PREFIX. "order o ON (op.order_id = o.order_id) WHERE o.order_status_id IN(" . implode(",", $implode) . ") and op.product_id='".$product_id."'";
				
		$query = $this->db->query($sql);
		if(isset($query->row['total'])){
			return $query->row['total'];
		} else {
			return 0;
		}
	}
	
}