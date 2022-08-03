<?php
class ModelPossettingSaleReport extends Model {

	public function getSellTaxs($data) {
		$sql="select * from " . DB_PREFIX . "tax_rate where tax_rate_id<>0";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalTaxs($order_id,$name) {
		$query = $this->db->query("SELECT value,order_id FROM " . DB_PREFIX . "order_total WHERE order_id='".$order_id."' and code = 'tax' and title='".$name."'");
		return $query->row;
	}

	public function getTaxReports($data) {
		$sql="SELECT * FROM " . DB_PREFIX . "order o LEFT JOIN " . DB_PREFIX . "order_total ot ON (o.order_id = ot.order_id) ";

		if (!empty($data['filter_order_id'])) {
		 $sql .=" and o.order_id like '".(int)$data['filter_order_id']."%'";
		}

		if (!empty($data['filter_payment_method'])) {
		 $sql .=" and o.payment_method like '".$this->db->escape($data['filter_payment_method'])."%'";
		}
		
		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}

		$sql .= " WHERE o.order_id<>0 AND ot.code = 'total' group by ot.order_id";

		$sort_data = array(
			'o.order_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		 $sql .= " ORDER BY " . $data['sort'];
		} else {
		 $sql .= " ORDER BY o.order_id";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

	public function getSellProducts($order_id) {
		$sql="select * from " . DB_PREFIX . "order_product where order_id='".$order_id."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalTaxReport($data) {
		$sql="SELECT COUNT(DISTINCT ot.order_id) AS total FROM " . DB_PREFIX . "order o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) ";

		$sql .= " WHERE ot.code = 'total'";

		if (!empty($data['filter_order_id'])) {
		 	$sql .=" and o.order_id like '".(int)$data['filter_order_id']."%'";
		}

		if (!empty($data['filter_payment_method'])) {
		 	$sql .=" and o.payment_method like '".$this->db->escape($data['filter_payment_method'])."%'";
		}

		if (!empty($data['filter_date_form'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_form']) . "'";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
		}


		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getSumTaxs($order_id) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "order_total WHERE order_id='".$order_id."' and code='tax' group by order_id");
		return $query->row['value'];
	}

}