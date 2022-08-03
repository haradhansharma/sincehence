<?php
class ModelExtensionReportsProCategory extends Model {
	
	public function getCategoriesFocusKeyphrases($category_id)
	{
		return $this->db->query("SELECT DISTINCT(focus_keyphrase) FROM ".DB_PREFIX."category_description WHERE focus_keyphrase !='' AND category_id != ".$category_id."")->rows;
	}

	public function editCategory($category_id, $data) {
		
		$this->db->query("UPDATE " . DB_PREFIX . "category SET  status = '" . (int)$data['status'] . "', date_modified = NOW() , seo_score = '" . (int)$data['seo_score'] . "',  readability_score = '" . (int)$data['readability_score'] . "' WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}

		// update image custom title, status
		if(empty($data['image'])){
			$data['image_custom_title'] = '';
			$data['image_custom_alt'] = ''; 
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "category SET image_custom_title = '" . $this->db->escape($data['image_custom_title']) . "', image_custom_alt = '" . $this->db->escape($data['image_custom_alt']) . "' WHERE category_id = '" . (int)$category_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");

		foreach ($data['category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', tag = '".$this->db->escape($value['tag'])."', focus_keyphrase = '".$this->db->escape($value['focus_keyphrase'])."'");
		}


		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
		}


		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'category_id=" . (int)$category_id . "'");

		if (isset($data['category_seo_url'])) {
			foreach ($data['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" . (int)$category_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('category');
	}
	
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}

	public function getCategories($data = array()) {
		$category_ids = '';

		// get category ids from seo url table
		$categories = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'category_id=%'")->rows;

		if($categories){
			$category_ids = array();
		}

		foreach ($categories as $category) {
			$category_seo = $category['query'];
			$category_id = str_replace('category_id=', '', $category_seo);
			array_push($category_ids, $category_id);
		}

		if($categories){
			$category_ids = implode(',', $category_ids);
		}

		$sql = "SELECT c.category_id, c.image, c.seo_score, c.readability_score, cd.name, cd.meta_title, cd.meta_keyword, cd.meta_description, cd.tag, c.status, c.sort_order, c.image_custom_title, c.image_custom_alt FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		// if category have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($category_ids)) {
				$sql .= " AND c.category_id IN (".$category_ids.") ";
			}else{
				$sql .= " AND c.category_id IN (0) ";
			}

		}

		// if category don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($category_ids)) {
				$sql .= " AND c.category_id NOT IN (".$category_ids.") ";
			}
			
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND cd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND cd.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND cd.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND cd.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND cd.meta_description = '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND cd.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND cd.meta_keyword = '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 1 ) {
			$sql .= " AND c.image_custom_title != '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 0) {
			$sql .= " AND c.image_custom_title = '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 1 ) {
			$sql .= " AND c.image_custom_alt != '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 0 ) {
			$sql .= " AND c.image_custom_alt = '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 1 ) {
			$sql .= " AND c.image != '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 0 ) {
			$sql .= " AND c.image = '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 1 ) {
			$sql .= " AND cd.tag != '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 0 ) {
			$sql .= " AND cd.tag = '' ";
		}


		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND cd.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND cd.description NOT LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		$sort_data = array(
			'cd.name',
			'c.status',
			'cd.meta_title',
			'cd.meta_keyword',
			'c.image_custom_title',
			'c.image_custom_alt',
			'c.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cd.name";
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

	public function getCategoryDescriptions($category_id) {
		$category_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'focus_keyphrase'  => $result['focus_keyphrase'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],				
				'meta_keyword'     => $result['meta_keyword'],
				'tag'      		   => $result['tag'],
				'description'      => $result['description']
			);
		}

		return $category_description_data;
	}
	
	public function getCategoryPath($category_id) {
		$query = $this->db->query("SELECT category_id, path_id, level FROM " . DB_PREFIX . "category_path WHERE category_id = '" . (int)$category_id . "'");

		return $query->rows;
	}
	
	public function getCategoryFilters($category_id) {
		$category_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_filter_data[] = $result['filter_id'];
		}

		return $category_filter_data;
	}

	public function getCategoryStores($category_id) {
		$category_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}
	
	public function getCategorySeoUrls($category_id) {
		$category_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $category_seo_url_data;
	}
	
	public function getCategoryLayouts($category_id) {
		$category_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $category_layout_data;
	}

	public function getTotalCategories($data = array()) {
		
		$category_ids = '';

		// get category ids from seo url table
		$categories = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'category_id=%'")->rows;

		if($categories){
			$category_ids = array();
		}

		foreach ($categories as $category) {
			$category_seo = $category['query'];
			$category_id = str_replace('category_id=', '', $category_seo);
			array_push($category_ids, $category_id);
		}

		if($categories){
			$category_ids = implode(',', $category_ids);
		}

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


		// if category have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($category_ids)) {
				$sql .= " AND c.category_id IN (".$category_ids.") ";
			}else{
				$sql .= " AND c.category_id IN (0) ";
			}

		}

		// if category don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($category_ids)) {
				$sql .= " AND c.category_id NOT IN (".$category_ids.") ";
			}
			
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND cd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND cd.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND cd.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND cd.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND cd.meta_description = '' ";
		}


		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND cd.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND cd.meta_keyword = '' ";
		}


		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 1 ) {
			$sql .= " AND c.image_custom_title != '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 0) {
			$sql .= " AND c.image_custom_title = '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 1 ) {
			$sql .= " AND c.image_custom_alt != '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 0 ) {
			$sql .= " AND c.image_custom_alt = '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 1 ) {
			$sql .= " AND c.image != '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 0 ) {
			$sql .= " AND c.image = '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 1 ) {
			$sql .= " AND cd.tag != '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 0 ) {
			$sql .= " AND cd.tag = '' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND cd.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND cd.description NOT LIKE '%&lt;/h1&gt;%' ";
		}
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function getTotalCategoriesByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	// ------- reports pro additional functions ------- //


	public function generateSeoUrls($category_ids = array(), $store_id = 0) 
	{
		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name, language_id FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$language_id = $category['language_id'];

				$category_name = utf8_substr(trim(strip_tags(html_entity_decode($category['name'], ENT_QUOTES, 'UTF-8'))), 0);

				$category_seo_url = preg_replace('/\s+/', '-', strtolower($category_name));
				$category_seo_url = str_replace('&', '-', $category_seo_url);
				$category_seo_url = str_replace("'", 'foot', $category_seo_url);
				$category_seo_url = str_replace('"', 'inch', $category_seo_url);
				$category_seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $category_seo_url);
				$category_seo_url = preg_replace('/\-+/', '-', $category_seo_url);

				$category_seo_url = rtrim($category_seo_url, "-");

				$delete = $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'category_id=".$category_id . "'");

				if (!empty($category_seo_url)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" .$category_id . "', keyword = '" . $this->db->escape($category_seo_url) . "'");
				}
			}

		}
	}

	public function generateMetaTitle($category_ids = array(), $custom_text, $store_id = 0)
	{
		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name, language_id FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$category_name = $category['name'];

				$category_meta_title = $this->db->escape($category_name);

				$category_meta_title .= $custom_text; 

				$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_title = '".$category_meta_title."' WHERE category_id = '".$category_id."'");
			}

		}

	}

	public function generateMetaDescription($category_ids = array())
	{
		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name, description FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$category_name = $category['name'];

				$category_meta_description = $this->db->escape($category_name);

				if(!empty($category['description'])){
					$category_meta_description .= ' - '. utf8_substr(trim(strip_tags(html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_category_description_length')) . ''; 
				}


				$category_meta_description = $this->db->escape($category_meta_description);
				
				if(!empty($category_meta_description)){
					$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_description = '".$category_meta_description."' WHERE category_id = '".$category_id."'");
				}

			}
		}
		
	}

	public function generateMetaKeywords($category_ids = array())
	{
		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$category_keywords = strtolower($category['name']);

				$category_keywords = utf8_substr(trim(strip_tags(html_entity_decode($category_keywords, ENT_QUOTES, 'UTF-8'))), 0);

				$category_keywords = str_replace(' ', ', ', $category_keywords);
				$category_keywords = str_replace('&, ', '', $category_keywords);
				$category_keywords = str_replace('-, ', '', $category_keywords);
				$category_keywords = preg_replace('/\,+/', ',', $category_keywords);

				$category_keywords = explode(',', $category_keywords);

				$category_keywords_array = array();

				foreach ($category_keywords as $category_keyword) {
					if(!is_numeric($category_keyword) && $category_keyword != ' '){
						array_push($category_keywords_array, $category_keyword);
					}
				}

				$category_keywords = implode(',', $category_keywords_array);

				$category_keywords = rtrim($category_keywords, ", ");

				$category_keywords = $this->db->escape($category_keywords);
				
				$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_keyword = '".$category_keywords."' WHERE category_id = '".$category_id."'");
			}
		}
		
	}

	public function generateTags($category_ids = array())
	{
		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$category_tags = strtolower($category['name']);

				$category_tags = utf8_substr(trim(strip_tags(html_entity_decode($category_tags, ENT_QUOTES, 'UTF-8'))), 0);

				$category_tags = str_replace(' ', ', ', $category_tags);
				$category_tags = str_replace('&, ', '', $category_tags);
				$category_tags = str_replace('-, ', '', $category_tags);
				$category_tags = preg_replace('/\,+/', ',', $category_tags);

				$category_tags = explode(',', $category_tags);

				$category_tags_array = array();

				foreach ($category_tags as $tag) {
					if(!is_numeric($tag) && $tag != ' '){
						array_push($category_tags_array, $tag);
					}
				}

				$category_tags = implode(',', $category_tags_array);

				$category_tags = rtrim($category_tags, ", ");

				$category_tags = $this->db->escape($category_tags);
				
				$this->db->query("UPDATE ".DB_PREFIX."category_description SET tag = '".$category_tags."' WHERE category_id = '".$category_id."'");
			}
		}
		
	}


	public function generateImageCustomTitle($category_ids = array(), $custom_text)
	{

		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$category_name = $category['name'];

				$category_image_title = $this->db->escape($category_name);

				if(!empty($custom_text)){
					$category_image_title .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."category SET image_custom_title = '".$category_image_title."' WHERE category_id = '".$category_id."' AND image !='' ");

			}
		}

	}


	public function generateImageCustomAlt($category_ids = array(), $custom_text)
	{
		foreach ($category_ids as $category_id) {
			
			$query = $this->db->query("SELECT name FROM ".DB_PREFIX."category_description WHERE category_id = '".$category_id."' ");
			
			$category = $query->row;

			if(!empty($category)){

				$category_name = $category['name'];

				$category_image_alt = $this->db->escape($category_name);

				if(!empty($custom_text)){
					$category_image_alt .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."category SET image_custom_alt = '".$category_image_alt."' WHERE category_id = '".$category_id."' AND image !='' ");

			}
		}
	}

}