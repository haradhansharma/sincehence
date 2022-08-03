<?php
class ModelExtensionDashboardCalculatorReport extends Model {
	public function getTotalCalculator($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "calculator_data`";

		$implode = array();

// 		if (!empty($data['filter_ip'])) {
// 			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
// 		}

		if (!empty($data['filter_calculator'])) {
			$implode[] = "cd_id > 0 AND your_name LIKE '" . $this->db->escape($data['filter_calculator']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}