<?php
class ModelExtensionDashboardIncartReport extends Model {
	public function getTotalIncart($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cart_incomplete`";

		$implode = array();

// 		if (!empty($data['filter_ip'])) {
// 			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
// 		}

		if (!empty($data['filter_incart'])) {
			$implode[] = "cart_id > 0 AND name LIKE '" . $this->db->escape($data['filter_incart']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function getTotalinpro($data = array()) {
		$sql = "SELECT SUM(quantity) AS quantity FROM `" . DB_PREFIX . "cart_incomplete`";

		$implode = array();

// 		if (!empty($data['filter_ip'])) {
// 			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
// 		}

		if (!empty($data['filter_cart'])) {
			$implode[] = "cart_id > 0 AND name LIKE '" . $this->db->escape($data['filter_cart']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['quantity'];
	}
}