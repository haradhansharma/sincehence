<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
class ModelExtensionShippingStorePickupMap extends Model {
	private $compatibility = null;

	/*
	  Set compatibility for all versions of Opencart
	*/
	public function __construct($registry) {
		parent::__construct($registry);

		include_once DIR_SYSTEM . 'library/vendors/storepickup_map/compatibility.php';

		$this->compatibility = new OVCompatibility_13($registry);
		$this->compatibility->setApp('admin');
	}

	/*
	  Return compatibility instance
	*/
	public function compatibility() {
		return $this->compatibility;
	}

	/*
	  Pickup Locations
	*/
	/*edited sharma*/
	public function addStore($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "spm_store SET name = '" . $this->db->escape(str_replace('"', '', $data['name'])) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address']) . "', city = '" . $this->db->escape($data['city']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', latitude = '" . (float)$data['latitude'] . "', longitude = '" . (float)$data['longitude'] . "', cost = '" . (float)$data['cost'] . "', icon = '" . $this->db->escape($data['icon']) . "', store_id = '" . (int)$data['store_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', central = '" . (int)$data['central'] . "', date_added = NOW()");

		return $this->db->getLastId();;
	}
/*edited sharma*/
	public function editStore($storepickup_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "spm_store SET name = '" . $this->db->escape(str_replace('"', '', $data['name'])) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', address = '" . $this->db->escape($data['address']) . "', city = '" . $this->db->escape($data['city']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', latitude = '" . (float)$data['latitude'] . "', longitude = '" . (float)$data['longitude'] . "', cost = '" . (float)$data['cost'] . "', icon = '" . $this->db->escape($data['icon']) . "', store_id = '" . (int)$data['store_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', central = '" . (int)$data['central'] . "' WHERE storepickup_id = '" . (int)$storepickup_id . "'");
	}
/*edited sharma*/
	public function statusStore($storepickup_id, $status) {
		$this->db->query("UPDATE " . DB_PREFIX . "spm_store SET status = '" . (int)$status . "', central = '" . (int)$central . "' WHERE storepickup_id = '" . (int)$storepickup_id . "'");
	}

	public function deleteStore($storepickup_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "spm_store WHERE storepickup_id = '" . (int)$storepickup_id . "'");
	}

	public function getStore($storepickup_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "spm_store WHERE storepickup_id = '" . (int)$storepickup_id . "'");

		return $query->row;
	}

	public function getStores($data = array()) {
		$sql = "SELECT s.*, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = s.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = s.zone_id) AS zone FROM " . DB_PREFIX . "spm_store s WHERE 1=1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_address'])) {
			$sql .= " AND (s.address LIKE '%" . $this->db->escape($data['filter_address']) . "%' OR s.city LIKE '" . $this->db->escape($data['filter_address']) . "%')";
		}

		if (!empty($data['filter_country_id'])) {
			$sql .= " AND s.country_id = '" . (int)$data['filter_country_id'] . "'";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND s.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND s.status = '" . (int)$data['filter_status'] . "'";
		}
		/////sharma
		if (isset($data['filter_central']) && $data['filter_central'] !== '') {
			$sql .= " AND s.central = '" . (int)$data['filter_central'] . "'";
		}

        /////sharma
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(s.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$sort_data = array(
			's.storepickup_id',
			's.name',
			's.city',
			's.cost',
			's.sort_order',
			's.status',
			's.central',
			's.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY s.date_added";
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

	public function getTotalStores($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "spm_store s WHERE 1=1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_address'])) {
			$sql .= " AND (s.address LIKE '%" . $this->db->escape($data['filter_address']) . "%' OR s.city LIKE '" . $this->db->escape($data['filter_address']) . "%')";
		}

		if (!empty($data['filter_country_id'])) {
			$sql .= " AND s.country_id = '" . (int)$data['filter_country_id'] . "'";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND s.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND s.status = '" . (int)$data['filter_status'] . "'";
		}
		/////sharma
		if (isset($data['filter_central']) && $data['filter_central'] !== '') {
			$sql .= " AND s.central = '" . (int)$data['filter_central'] . "'";
		}

        /////sharma

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(s.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	/*
	  Reports
	*/
	public function getUsedStores($data = array()) {
		$sql = "SELECT s.storepickup_id, s.name, COUNT(*) AS orders, SUM((SELECT SUM(op.quantity) FROM " . DB_PREFIX . "order_product op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS products, SUM(o.total) AS total, MAX(o.date_added) AS last_used FROM " . DB_PREFIX . "spm_store s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE o.order_status_id > '0' AND o.shipping_code LIKE 'storepickup_map%'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		$sql .= " GROUP BY s.storepickup_id ORDER BY o.date_added DESC";

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

	public function getTotalUsedStores($data = array()) {
		$sql = "SELECT COUNT(DISTINCT s.storepickup_id) AS total FROM " . DB_PREFIX . "spm_store s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE o.order_status_id > '0' AND o.shipping_code LIKE 'storepickup_map%'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "spm_store s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE o.order_status_id > '0' AND o.shipping_code LIKE 'storepickup_map%'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(o.total) AS total FROM " . DB_PREFIX . "spm_store s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE o.order_status_id > '0' AND o.shipping_code LIKE 'storepickup_map%'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}

		if (isset($data['filter_store_id']) && $data['filter_store_id'] !== '') {
			$sql .= " AND o.store_id = '" . (int)$data['filter_store_id'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	/*
	  Installation & Update
	  Table structure for the module
	*/
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "spm_store (
			`storepickup_id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(128) NOT NULL,
			`email` varchar(96) NOT NULL,
			`telephone` varchar(25) NOT NULL DEFAULT '',
			`address` varchar(255) NOT NULL,
			`city` varchar(128) NOT NULL,
			`country_id` int(11) NOT NULL,
			`zone_id` int(11) NOT NULL,
			`latitude` decimal(12,10) NOT NULL DEFAULT '0.0000000000',
			`longitude` decimal(12,10) NOT NULL DEFAULT '0.0000000000',
			`cost` decimal(15,4) NOT NULL DEFAULT '0.0000',
			`icon` varchar(255) NOT NULL,
			`store_id` int(11) NOT NULL,
			`sort_order` int(11) NOT NULL DEFAULT '0',
			`status` tinyint(1) NOT NULL DEFAULT '0',
			`central` tinyint(1) NOT NULL DEFAULT '0',
			`date_added` datetime NOT NULL,
			PRIMARY KEY (`storepickup_id`),
			KEY `store_id` (`store_id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

		if (!$this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "spm_store LIKE 'icon'")->row) {
			$this->db->query("ALTER TABLE " . DB_PREFIX . "spm_store ADD `icon` varchar(255) NOT NULL");
		}

		if (!$this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'shipping_code'")->row) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `shipping_code` varchar(128) NOT NULL");
		}
	}

	public function uninstall() {
// 		$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "spm_store");
	}
}
?>