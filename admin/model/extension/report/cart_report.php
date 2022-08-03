<?php
class ModelExtensionReportCartReport extends Model {
	public function getCarts($data = array()) {
		$sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, ca.cart_id, SUM(ca.quantity) as products FROM `" . DB_PREFIX . "cart` ca LEFT JOIN `" . DB_PREFIX . "customer` c ON (ca.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE ca.customer_id > 0 AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}

		$sql .= " GROUP BY ca.cart_id";

		$sql = "SELECT t.customer_id, t.customer, t.email, t.customer_group, t.status, COUNT(DISTINCT t.cart_id) AS carts, SUM(t.products) AS products FROM (" . $sql . ") AS t GROUP BY t.customer_id ORDER BY carts DESC";

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
		$sql = "SELECT COUNT(DISTINCT ca.customer_id) AS total FROM `" . DB_PREFIX . "cart` ca LEFT JOIN `" . DB_PREFIX . "customer` c ON (ca.customer_id = c.customer_id) WHERE ca.customer_id > '0'";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}


		$query = $this->db->query($sql);

		return $query->row['total'];
	}


}
