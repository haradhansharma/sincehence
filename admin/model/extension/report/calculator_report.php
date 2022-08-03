<?php
class ModelExtensionReportCalculatorReport extends Model {
	public function getCalulators($data = array()) {
	    $sql = "SELECT * FROM `" . DB_PREFIX . "calculator_data` WHERE cd_id >0  ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_calculator'])) {
			$sql .= " AND your_name LIKE '" . $this->db->escape($data['filter_calculator']) . "'";
		}

		$sql .= " GROUP BY date_added ORDER BY date_added DESC";

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

	public function getTotalCalculators($data = array()) {
		$sql = "SELECT COUNT(DISTINCT cd_id) AS total FROM `" . DB_PREFIX . "calculator_data`";

		$implode = array();

		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		if (!empty($data['filter_calculator'])) {
			$implode[] = "your_name LIKE '" . $this->db->escape($data['filter_calculator']) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}