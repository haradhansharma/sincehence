<?php
class ModelExtensionReportIncartReport extends Model {
	public function getCarts($data = array()) {
		$sql = "SELECT ca.store_name, ca.phone, ca.customer_id, ca.name AS customer, ca.email, ca.cart_id, SUM(ca.quantity) as products FROM `" . DB_PREFIX . "cart_incomplete` ca  WHERE ca.cartincom_id > 0 ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND ca.name LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		$sql .= " GROUP BY ca.cart_id";

		$sql = "SELECT t.store_name, t.phone, t.customer_id, t.customer, t.email, COUNT(DISTINCT t.cart_id) AS carts, SUM(t.products) AS products FROM (" . $sql . ") AS t GROUP BY t.customer_id ORDER BY carts DESC";

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

	public function getTotalCarts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT ca.email) AS total FROM `" . DB_PREFIX . "cart_incomplete` ca  WHERE ca.cartincom_id > '0'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND ca.name LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}


		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function getProducts($email) {

	    $query = $this->db->query("SELECT pd.product_id, pd.name, pd.description, p.image FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON pd.product_id = p.product_id  WHERE pd.product_id IN (SELECT `product_id` FROM `" . DB_PREFIX . "cart_incomplete` WHERE `email`= '".$email."')");
	    return $query->rows;
	    
		
	}



}
