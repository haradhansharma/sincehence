<?php
class ModelExtensionReportsProManufacturer extends Model {

	public function getManufacturersFocusKeyphrases($manufacturer_id)
	{
		return $this->db->query("SELECT DISTINCT(focus_keyphrase) FROM ".DB_PREFIX."manufacturer WHERE focus_keyphrase !='' AND manufacturer_id != ".$manufacturer_id."")->rows;
	}

	public function editManufacturer($manufacturer_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "' , description ='". $this->db->escape($data['description'])."' , meta_title ='". $this->db->escape($data['meta_title'])."'   , meta_description ='". $this->db->escape($data['meta_description'])."'  , meta_keyword ='". $this->db->escape($data['meta_keyword'])."' , focus_keyphrase = '".$this->db->escape($data['focus_keyphrase'])."', seo_score = '" . (int)$data['seo_score'] . "',  readability_score = '" . (int)$data['readability_score'] . "' ,  tag = '" . $this->db->escape($data['tag']) . "' , date_modified = NOW() WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		// update image custom title and alt attribute
		if(empty($data['image'])){
			$data['image_custom_title'] = '';
			$data['image_custom_alt'] = ''; 
		}

		$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_title = '".$this->db->escape($data['image_custom_title'])."', image_custom_alt = '".$this->db->escape($data['image_custom_alt'])."' WHERE manufacturer_id = '".(int)$manufacturer_id."' ");
		
		

		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_seo_url'])) {
			foreach ($data['manufacturer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$this->cache->delete('manufacturer');
	}


	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row;
	}

	public function getManufacturers($data = array()) {

		$manufacturer = '';

		// get manufacturer ids from seo url table
		$manufacturers = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'manufacturer_id=%'")->rows;

		if($manufacturers){
			$manufacturer = array();
		}

		foreach ($manufacturers as $manufacturer_id) {
			$manufacturer_seo = $manufacturer_id['query'];
			$manufacturer_id = str_replace('manufacturer_id=', '', $manufacturer_seo);
			array_push($manufacturer, $manufacturer_id);
		}

		if($manufacturers){
			$manufacturer = implode(',', $manufacturer);
		}

		$sql = "SELECT m.* FROM " . DB_PREFIX . "manufacturer m WHERE m.name !='' ";


		// if manufacturer have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($manufacturer)) {
				$sql .= " AND m.manufacturer_id IN (".$manufacturer.") ";
			}else{
					$sql .= " AND m.manufacturer_id IN (0) ";
				}

		}

		// if manufacturer don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($manufacturer)) {
				$sql .= " AND m.manufacturer_id NOT IN (".$manufacturer.") ";
			}
			
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND m.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND m.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND m.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND m.meta_description = '' ";
		}


		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND m.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND m.meta_keyword = '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 1 ) {
			$sql .= " AND m.image_custom_title != '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 0) {
			$sql .= " AND m.image_custom_title = '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 1 ) {
			$sql .= " AND m.image_custom_alt != '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 0 ) {
			$sql .= " AND m.image_custom_alt = '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 1 ) {
			$sql .= " AND m.image != '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 0 ) {
			$sql .= " AND m.image = '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 1 ) {
			$sql .= " AND m.tag != '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 0 ) {
			$sql .= " AND m.tag = '' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND m.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND m.description NOT LIKE '%&lt;/h1&gt;%' ";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND m.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'm.name',
			'm.meta_title',
			'm.meta_keyword',
			'm.image_custom_title',
			'm.image_custom_alt',
			'm.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY m.name";
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

	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}
	
	public function getManufacturerSeoUrls($manufacturer_id) {
		$manufacturer_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $manufacturer_seo_url_data;
	}
	
	public function getTotalManufacturers($data = array()) {
		$manufacturer = '';

		// get manufacturer ids from seo url table
		$manufacturers = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'manufacturer_id=%'")->rows;

		if($manufacturers){
			$manufacturer = array();
		}


		foreach ($manufacturers as $manufacturer_id) {
			$manufacturer_seo = $manufacturer_id['query'];
			$manufacturer_id = str_replace('manufacturer_id=', '', $manufacturer_seo);
			array_push($manufacturer, $manufacturer_id);
		}


		if($manufacturers){
			$manufacturer = implode(',', $manufacturer);
		}

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer m WHERE m.name !='' ";

		
		// if manufacturer have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($manufacturer)) {
				$sql .= " AND m.manufacturer_id IN (".$manufacturer.") ";
			}else{
					$sql .= " AND m.manufacturer_id IN (0) ";
				}

		}

		// if manufacturer don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($manufacturer)) {
				$sql .= " AND m.manufacturer_id NOT IN (".$manufacturer.") ";
			}
			
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND m.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND m.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND m.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND m.meta_description = '' ";
		}


		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND m.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND m.meta_keyword = '' ";
		}
		
		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 1 ) {
			$sql .= " AND m.image_custom_title != '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 0) {
			$sql .= " AND m.image_custom_title = '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 1 ) {
			$sql .= " AND m.image_custom_alt != '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 0 ) {
			$sql .= " AND m.image_custom_alt = '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 1 ) {
			$sql .= " AND m.image != '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 0 ) {
			$sql .= " AND m.image = '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 1 ) {
			$sql .= " AND m.tag != '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 0 ) {
			$sql .= " AND m.tag = '' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND m.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND m.description NOT LIKE '%&lt;/h1&gt;%' ";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND m.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'm.name',
			'm.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY m.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		// if (isset($data['start']) || isset($data['limit'])) {
		// 	if ($data['start'] < 0) {
		// 		$data['start'] = 0;
		// 	}

		// 	if ($data['limit'] < 1) {
		// 		$data['limit'] = 20;
		// 	}

		// 	$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		// }

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	// ------- reports pro additional functions ------- //

	public function generateSeoUrls($manufacturer_ids = array(), $store_id = 0) 
	{

		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");

				$language_id = '';

				$manufacturer_name = utf8_substr(trim(strip_tags(html_entity_decode($manufacturer['name'], ENT_QUOTES, 'UTF-8'))), 0);

				$manufacturer_seo_url = preg_replace('/\s+/', '-', strtolower($manufacturer_name));
				$manufacturer_seo_url = str_replace('&', '-', $manufacturer_seo_url);
				$manufacturer_seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $manufacturer_seo_url);
				$manufacturer_seo_url = preg_replace('/\-+/', '-', $manufacturer_seo_url);

				$manufacturer_seo_url = rtrim($manufacturer_seo_url, "-");


				if (!empty($manufacturer_seo_url)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($manufacturer_seo_url) . "'");
				}
			}
				
		}
	}

	public function generateMetaTitle($manufacturer_ids = array(), $custom_text, $store_id = 0)
	{
		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$manufacturer_name = $manufacturer['name'];

				$manufacturer_meta_title = $this->db->escape($manufacturer_name);

				$manufacturer_meta_title .= $custom_text; 

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_title = '".$manufacturer_meta_title."' WHERE manufacturer_id = '".$manufacturer_id."' ");

			}

		}
	}

	public function generateMetaDescription($manufacturer_ids = array())
	{
		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name, description FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$manufacturer_name = $manufacturer['name'];

				$manufacturer_meta_description = $this->db->escape($manufacturer_name);
				
				if(!empty($manufacturer['description'])){
					$manufacturer_meta_description .= ' - '. utf8_substr(trim(strip_tags(html_entity_decode($manufacturer['description'], ENT_QUOTES, 'UTF-8'))), 0); 
				}

				$manufacturer_meta_description = $this->db->escape($manufacturer_meta_description);
				
				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_description = '".$manufacturer_meta_description."' WHERE manufacturer_id = '".$manufacturer_id."' ");

			}
		}
		
	}

	public function generateMetaKeywords($manufacturer_ids = array())
	{
		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name, description FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$manufacturer_keywords = strtolower($manufacturer['name']);

				$manufacturer_keywords = utf8_substr(trim(strip_tags(html_entity_decode($manufacturer_keywords, ENT_QUOTES, 'UTF-8'))), 0);

				$manufacturer_keywords = str_replace(' ', ', ', $manufacturer_keywords);
				$manufacturer_keywords = str_replace('&, ', '', $manufacturer_keywords);
				$manufacturer_keywords = str_replace('-, ', '', $manufacturer_keywords);
				$manufacturer_keywords = preg_replace('/\,+/', ',', $manufacturer_keywords);

				$manufacturer_keywords = explode(',', $manufacturer_keywords);

				$manufacturer_keywords_array = array();

				foreach ($manufacturer_keywords as $manufacturer_keyword) {
					if(!is_numeric($manufacturer_keyword) && $manufacturer_keyword != ' '){
						array_push($manufacturer_keywords_array, $manufacturer_keyword);
					}
				}

				$manufacturer_keywords = implode(',', $manufacturer_keywords_array);

				$manufacturer_keywords = rtrim($manufacturer_keywords, ", ");

				$manufacturer_keywords = $this->db->escape($manufacturer_keywords);
				
				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_keyword = '".$manufacturer_keywords."' WHERE manufacturer_id = '".$manufacturer_id."' ");

			}
		}
		
	}

	public function generateTags($manufacturer_ids = array())
	{
		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name, description FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$manufacturer_tags = strtolower($manufacturer['name']);

				$manufacturer_tags = utf8_substr(trim(strip_tags(html_entity_decode($manufacturer_tags, ENT_QUOTES, 'UTF-8'))), 0);

				$manufacturer_tags = str_replace(' ', ', ', $manufacturer_tags);
				$manufacturer_tags = str_replace('&, ', '', $manufacturer_tags);
				$manufacturer_tags = str_replace('-, ', '', $manufacturer_tags);
				$manufacturer_tags = preg_replace('/\,+/', ',', $manufacturer_tags);

				$manufacturer_tags = explode(',', $manufacturer_tags);

				$manufacturer_tags_array = array();

				foreach ($manufacturer_tags as $tag) {
					if(!is_numeric($tag) && $tag != ' '){
						array_push($manufacturer_tags_array, $tag);
					}
				}

				$manufacturer_tags = implode(',', $manufacturer_tags_array);

				$manufacturer_tags = rtrim($manufacturer_tags, ", ");

				$manufacturer_tags = $this->db->escape($manufacturer_tags);
				
				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET tag = '".$manufacturer_tags."' WHERE manufacturer_id = '".$manufacturer_id."' ");

			}
		}
		
	}


	public function generateImageCustomTitle($manufacturer_ids = array(), $custom_text)
	{
		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$manufacturer_name = $manufacturer['name'];

				$manufacturer_image_title = $this->db->escape($manufacturer_name);

				if(!empty($custom_text)){
					$manufacturer_image_title .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_title = '".$manufacturer_image_title."' WHERE manufacturer_id = '".$manufacturer_id."' AND image !='' ");

			}
		}

	}


	public function generateImageCustomAlt($manufacturer_ids = array(), $custom_text)
	{
		foreach ($manufacturer_ids as $manufacturer_id) {

			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."manufacturer WHERE manufacturer_id = '".$manufacturer_id."' ");

			$manufacturer = $query->row;

			if(!empty($manufacturer)){

				$manufacturer_name = $manufacturer['name'];

				$manufacturer_image_alt = $this->db->escape($manufacturer_name);

				if(!empty($custom_text)){
					$manufacturer_image_alt .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_alt = '".$manufacturer_image_alt."' WHERE manufacturer_id = '".$manufacturer_id."' AND image !='' ");

			}
		}
	}

}
