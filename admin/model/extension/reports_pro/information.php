<?php
class ModelExtensionReportsProInformation extends Model {

	public function getInformationsFocusKeyphrases($information_id)
	{
		return $this->db->query("SELECT DISTINCT(focus_keyphrase) FROM ".DB_PREFIX."information_description WHERE focus_keyphrase !='' AND information_id != ".$information_id."")->rows;
	}

	public function editInformation($information_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "information SET sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "' , seo_score = '" . (int)$data['seo_score'] . "',  readability_score = '" . (int)$data['readability_score'] . "', date_modified = NOW() WHERE information_id = '" . (int)$information_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");

		foreach ($data['information_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "' , focus_keyphrase = '".$this->db->escape($value['focus_keyphrase'])."'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");

		if (isset($data['information_store'])) {
			foreach ($data['information_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_store SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");

		if (isset($data['information_seo_url'])) {
			foreach ($data['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "information_to_layout` WHERE information_id = '" . (int)$information_id . "'");

		if (isset($data['information_layout'])) {
			foreach ($data['information_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "information_to_layout` SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('information');
	}


	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");

		return $query->row;
	}

	public function getInformations($data = array()) {

		$information_ids = '';

		// get information ids from seo url table
		$informations = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'information_id=%'")->rows;

		if($informations){
			$information_ids = array();
		}

		foreach ($informations as $information) {
			$information_seo = $information['query'];
			$information_id = str_replace('information_id=', '', $information_seo);
			array_push($information_ids, $information_id);
		}

		if($informations){
			$information_ids = implode(',', $information_ids);
		}


			$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";


			// if information have seo
			if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

				if(!empty($information_ids)) {
					$sql .= " AND i.information_id IN (".$information_ids.") ";
				}else{
					$sql .= " AND i.information_id IN (0) ";
				}

			}

			// if information don't have seo
			if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

				if(!empty($information_ids)) {
					$sql .= " AND i.information_id NOT IN (".$information_ids.") ";
				}
				
			}
			
			if (!empty($data['filter_title'])) {
				$sql .= " AND id.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
			}

			if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
				$sql .= " AND id.meta_title != '' ";
			}

			if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
				$sql .= " AND id.meta_title = '' ";
			}

			if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
				$sql .= " AND id.meta_description != '' ";
			}

			if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
				$sql .= " AND id.meta_description = '' ";
			}


			if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
				$sql .= " AND id.meta_keyword != '' ";
			}

			if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
				$sql .= " AND id.meta_keyword = '' ";
			}

			if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND id.description LIKE '%&lt;/h1&gt;%' ";
			}

			if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
				$sql .= " AND id.description NOT LIKE '%&lt;/h1&gt;%' ";
			}


			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND i.status = '" . (int)$data['filter_status'] . "'";
			}

			$sort_data = array(
				'id.title',
				'i.status',
				'id.meta_title',
				'id.meta_keyword'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY id.title";
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

	public function getInformationDescriptions($information_id) {
		$information_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'focus_keyphrase'  => $result['focus_keyphrase'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $information_description_data;
	}

	public function getInformationStores($information_id) {
		$information_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_store_data[] = $result['store_id'];
		}

		return $information_store_data;
	}

	public function getInformationSeoUrls($information_id) {
		$information_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $information_seo_url_data;
	}

	public function getInformationLayouts($information_id) {
		$information_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $information_layout_data;
	}

	public function getTotalInformations($data = array()) {
		
		$information_ids = '';

		// get information ids from seo url table
		$informations = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'information_id=%'")->rows;

		if($informations){
			$information_ids = array();
		}

		foreach ($informations as $information) {
			$information_seo = $information['query'];
			$information_id = str_replace('information_id=', '', $information_seo);
			array_push($information_ids, $information_id);
		}

		if($informations){
			$information_ids = implode(',', $information_ids);
		}

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		// if information have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($information_ids)) {
				$sql .= " AND i.information_id IN (".$information_ids.") ";
			}else{
				$sql .= " AND i.information_id IN (0) ";
			}

		}

		// if information don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($information_ids)) {
				$sql .= " AND i.information_id NOT IN (".$information_ids.") ";
			}
			
		}
		
		if (!empty($data['filter_title'])) {
			$sql .= " AND id.title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND id.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND id.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND id.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND id.meta_description = '' ";
		}


		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND id.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND id.meta_keyword = '' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND id.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND id.description NOT LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND i.status = '" . (int)$data['filter_status'] . "'";
		}


		$sort_data = array(
			'id.title',
			'i.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY i.sort_order";
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

	public function getTotalInformationsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}


	// ------- reports pro additional functions ------- //

	
	public function generateSeoUrls($information_ids = array(), $store_id = 0) 
	{
		foreach ($information_ids as $information_id) {
			
			$query = $this->db->query("SELECT title, information_id, language_id FROM ".DB_PREFIX."information_description WHERE information_id = '".$information_id."' ");

			$information = $query->row;

			if(!empty($information)){

				$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$information['information_id'] . "'");

					$language_id = $information['language_id'];

					$information_title = utf8_substr(trim(strip_tags(html_entity_decode($information['title'], ENT_QUOTES, 'UTF-8'))), 0);

					$information_seo_url = preg_replace('/\s+/', '-', strtolower($information_title));
					$information_seo_url = str_replace('&', '-', $information_seo_url);
					$information_seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $information_seo_url);
					$information_seo_url = preg_replace('/\-+/', '-', $information_seo_url);

					$information_seo_url = rtrim($information_seo_url, "-");

					if (!empty($information_seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information['information_id'] . "', keyword = '" . $this->db->escape($information_seo_url) . "'");
					}
			}
		}
	}

	public function generateMetaTitle($information_ids = array(), $custom_text, $store_id = 0)
	{
		foreach ($information_ids as $information_id) {
			
			$query = $this->db->query("SELECT title, information_id, language_id FROM ".DB_PREFIX."information_description WHERE information_id = '".$information_id."' ");

			$information = $query->row;

			if(!empty($information)){

				$information_title = $information['title'];

				$information_meta_title = $this->db->escape($information_title);

				$information_meta_title .= $custom_text; 

				$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_title = '".$information_meta_title."' WHERE information_id = '".$information['information_id']."' ");

			}
		}
	}

	public function generateMetaDescription($information_ids = array())
	{
		
		foreach ($information_ids as $information_id) {
			
			$query = $this->db->query("SELECT title, information_id, description ,language_id FROM ".DB_PREFIX."information_description WHERE information_id = '".$information_id."' ");

			$information = $query->row;

			if(!empty($information)){

				$information_title = $information['title'];

				$information_meta_description = $this->db->escape($information_title);
				
				if(!empty($information['description'])){
					$information_meta_description .= ' - '. utf8_substr(trim(strip_tags(html_entity_decode($information['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_information_description_length')) . ''; 
				}


				$information_meta_description = $this->db->escape($information_meta_description);
				

				if(!empty($information_meta_description)){
					$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_description = '".$information_meta_description."' WHERE information_id = '".$information_id."' ");
				}
			}

		}
		
	}

	public function generateMetaKeywords($information_ids = array())
	{
		foreach ($information_ids as $information_id) {
			
			$query = $this->db->query("SELECT title, information_id, language_id FROM ".DB_PREFIX."information_description WHERE information_id = '".$information_id."' ");

			$information = $query->row;

			if(!empty($information)){

				$information_keywords = strtolower($information['title']);

				$information_keywords = utf8_substr(trim(strip_tags(html_entity_decode($information_keywords, ENT_QUOTES, 'UTF-8'))), 0);

				$information_keywords = str_replace(' ', ', ', $information_keywords);
				$information_keywords = str_replace('&, ', '', $information_keywords);
				$information_keywords = str_replace('-, ', '', $information_keywords);
				$information_keywords = preg_replace('/\,+/', ',', $information_keywords);

				$information_keywords = explode(',', $information_keywords);

				$information_keywords_array = array();

				foreach ($information_keywords as $information_keyword) {
					if(!is_numeric($information_keyword) && $information_keyword != ' '){
						array_push($information_keywords_array, $information_keyword);
					}
				}

				$information_keywords = implode(',', $information_keywords_array);

				$information_keywords = rtrim($information_keywords, ", ");

				$information_keywords = $this->db->escape($information_keywords);
				
				
				$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_keyword = '".$information_keywords."' WHERE information_id = '".$information_id."' ");

			}
		}
		
	}

}