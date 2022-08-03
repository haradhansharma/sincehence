<?php
class ModelExtensionSizechart extends Model {
	/* Size Chart Starts */
	public function getSizeChartProduct($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sizechart_product sp LEFT JOIN " . DB_PREFIX . "sizechart s ON (sp.sizechart_id = s.sizechart_id) LEFT JOIN " . DB_PREFIX . "sizechart_description sd ON(s.sizechart_id=sd.sizechart_id) WHERE sp.sizechart_id = s.sizechart_id AND sd.language_id = '". (int)$this->config->get('config_language_id') ."' AND sp.product_id = '". (int)$product_id ."' AND s.status = '1' GROUP BY s.sizechart_id LIMIT 0,1");
		
		return $query->row;
	}
	
	public function getSizeChartCategory($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sizechart_category sp LEFT JOIN " . DB_PREFIX . "sizechart s ON (sp.sizechart_id = s.sizechart_id) LEFT JOIN " . DB_PREFIX . "sizechart_description sd ON(s.sizechart_id=sd.sizechart_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON(sp.category_id=p2c.category_id) WHERE sp.sizechart_id = s.sizechart_id AND sd.language_id = '". (int)$this->config->get('config_language_id') ."' AND sp.category_id=p2c.category_id AND p2c.product_id = '". (int)$product_id ."' AND s.status = '1' GROUP BY s.sizechart_id LIMIT 0,1");
		
		return $query->row;
	}
	/* Size Chart Ends */
}
