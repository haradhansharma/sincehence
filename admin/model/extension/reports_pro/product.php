<?php
class ModelExtensionReportsProProduct extends Model {
		
	public function getProductsFocusKeyphrases($product_id)
	{
		return $this->db->query("SELECT DISTINCT(focus_keyphrase) FROM ".DB_PREFIX."product_description WHERE focus_keyphrase !='' AND product_id != ".$product_id."")->rows;
	}

	public function editProduct($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', seo_score = '" . (int)$data['seo_score'] . "',  readability_score = '" . (int)$data['readability_score'] . "',  sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		// update image custom title and alt attribute
		if(empty($data['image'])){

			$data['image_custom_title'] = '';
			$data['image_custom_alt'] = ''; 
		}

		$this->db->query("UPDATE ".DB_PREFIX."product SET image_custom_title = '".$this->db->escape($data['image_custom_title'])."', image_custom_alt = '".$this->db->escape($data['image_custom_alt'])."' WHERE product_id = '".(int)$product_id."' ");
		

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', focus_keyphrase = '".$this->db->escape($value['focus_keyphrase'])."' ");
		}

		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', image_custom_title = '" . $this->db->escape($product_image['image_custom_title']) . "', image_custom_alt = '" . $this->db->escape($product_image['image_custom_alt']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}
		
		// SEO URL
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");
		
		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url']as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		$this->cache->delete('product');
	}

	
	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProducts($data = array()) {
		$product_ids = '';

		// get product ids from seo url table
		$products = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'product_id=%'")->rows;

		if($products){
			$product_ids = array();
		}

		foreach ($products as $product) {
			$product_seo = $product['query'];
			$product_id = str_replace('product_id=', '', $product_seo);
			array_push($product_ids, $product_id);
		}

		if($products){
			$product_ids = implode(',', $product_ids);
		}


		$sql = "SELECT p.product_id, p.image, p.status,p.price, p.quantity,  p.model, p.seo_score, p.readability_score, pd.name,p.image_custom_title, p.image_custom_alt, pd.meta_title, pd.meta_keyword, pd.meta_description, pd.tag, p.image_custom_title, p.image_custom_alt FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		// if product have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($product_ids)) {
				$sql .= " AND p.product_id IN (".$product_ids.") ";
			}else{
				$sql .= "AND p.product_id IN (0) ";
			}

		}

		// if product don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($product_ids)) {
				$sql .= " AND p.product_id NOT IN (".$product_ids.") ";
			}
			
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}


		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '' && $data['filter_quantity'] == 1 ) {
			$sql .= " AND p.quantity >= 1";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '' && $data['filter_quantity'] == 0 ) {
			$sql .= " AND p.quantity <= 0";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND pd.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND pd.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND pd.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND pd.meta_description = '' ";
		}


		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND pd.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND pd.meta_keyword = '' ";
		}


		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 1 ) {
			$sql .= " AND pd.tag != '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 0 ) {
			$sql .= " AND pd.tag = '' ";
		}


		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 1 ) {
			$sql .= " AND p.image_custom_title != '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 0) {
			$sql .= " AND p.image_custom_title = '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 1 ) {
			$sql .= " AND p.image_custom_alt != '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 0 ) {
			$sql .= " AND p.image_custom_alt = '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 1 ) {
			$sql .= " AND p.image != '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 0 ) {
			$sql .= " AND p.image = '' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND pd.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND pd.description NOT LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h2_tag_status']) && $data['filter_custom_h2_tag_status'] !== '' && $data['filter_custom_h2_tag_status'] == 1 ) {
			$sql .= " AND pd.description LIKE '%&lt;/h2&gt;%' ";
		}

		if (isset($data['filter_custom_h2_tag_status']) && $data['filter_custom_h2_tag_status'] !== '' && $data['filter_custom_h2_tag_status'] == 0 ) {
			$sql .= " AND pd.description NOT LIKE '%&lt;/h2&gt;%' ";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'pd.meta_title',
			'pd.meta_keyword',
			'pd.tag',
			'p.image_custom_title',
			'p.image_custom_alt',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'focus_keyphrase'  => $result['focus_keyphrase'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}
	
	public function getProductSeoUrls($product_id) {
		$product_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $product_seo_url_data;
	}
	
	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$product_ids = '';

		// get product ids from seo url table
		$products = $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'product_id=%'")->rows;

		if($products){
			$product_ids = array();
		}

		foreach ($products as $product) {
			$product_seo = $product['query'];
			$product_id = str_replace('product_id=', '', $product_seo);
			array_push($product_ids, $product_id);
		}

		if($products){
			$product_ids = implode(',', $product_ids);
		}


		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		

		// if product have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 1 ) {

			if(!empty($product_ids)) {
				$sql .= " AND p.product_id IN (".$product_ids.") ";
			}else{
				$sql .= " AND p.product_id IN (0) ";
			}

		}

		// if product don't have seo
		if (isset($data['filter_seo_status']) && $data['filter_seo_status'] !== '' && $data['filter_seo_status'] == 0 ) {

			if(!empty($product_ids)) {
				$sql .= " AND p.product_id NOT IN (".$product_ids.") ";
			}
			
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}
		
		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '' && $data['filter_quantity'] == 1 ) {
			$sql .= " AND p.quantity >= 1";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '' && $data['filter_quantity'] == 0 ) {
			$sql .= " AND p.quantity <= 0";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 1 ) {
			$sql .= " AND pd.meta_title != '' ";
		}

		if (isset($data['filter_meta_title_status']) && $data['filter_meta_title_status'] !== '' && $data['filter_meta_title_status'] == 0 ) {
			$sql .= " AND pd.meta_title = '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 1 ) {
			$sql .= " AND pd.meta_description != '' ";
		}

		if (isset($data['filter_meta_description_status']) && $data['filter_meta_description_status'] !== '' && $data['filter_meta_description_status'] == 0 ) {
			$sql .= " AND pd.meta_description = '' ";
		}


		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 1 ) {
			$sql .= " AND pd.meta_keyword != '' ";
		}

		if (isset($data['filter_meta_keywords_status']) && $data['filter_meta_keywords_status'] !== '' && $data['filter_meta_keywords_status'] == 0 ) {
			$sql .= " AND pd.meta_keyword = '' ";
		}


		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 1 ) {
			$sql .= " AND pd.tag != '' ";
		}

		if (isset($data['filter_tags_status']) && $data['filter_tags_status'] !== '' && $data['filter_tags_status'] == 0 ) {
			$sql .= " AND pd.tag = '' ";
		}


		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 1 ) {
			$sql .= " AND p.image_custom_title != '' ";
		}

		if (isset($data['filter_image_custom_title_status']) && $data['filter_image_custom_title_status'] !== '' && $data['filter_image_custom_title_status'] == 0) {
			$sql .= " AND p.image_custom_title = '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 1 ) {
			$sql .= " AND p.image_custom_alt != '' ";
		}

		if (isset($data['filter_image_custom_alt_status']) && $data['filter_image_custom_alt_status'] !== '' && $data['filter_image_custom_alt_status'] == 0 ) {
			$sql .= " AND p.image_custom_alt = '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 1 ) {
			$sql .= " AND p.image != '' ";
		}

		if (isset($data['filter_image_status']) && $data['filter_image_status'] !== '' && $data['filter_image_status'] == 0 ) {
			$sql .= " AND p.image = '' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 1 ) {
			$sql .= " AND pd.description LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h1_tag_status']) && $data['filter_custom_h1_tag_status'] !== '' && $data['filter_custom_h1_tag_status'] == 0 ) {
			$sql .= " AND pd.description NOT LIKE '%&lt;/h1&gt;%' ";
		}

		if (isset($data['filter_custom_h2_tag_status']) && $data['filter_custom_h2_tag_status'] !== '' && $data['filter_custom_h2_tag_status'] == 1 ) {
			$sql .= " AND pd.description LIKE '%&lt;/h2&gt;%' ";
		}

		if (isset($data['filter_custom_h2_tag_status']) && $data['filter_custom_h2_tag_status'] !== '' && $data['filter_custom_h2_tag_status'] == 0 ) {
			$sql .= " AND pd.description NOT LIKE '%&lt;/h2&gt;%' ";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

	// ------- reports pro additional functions ------- //


	public function generateSeoUrls($product_ids = array(), $store_id = 0) {

		foreach ($product_ids as $product_id) {

			$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'product_id=" . (int)$product_id . "'");

			$query = $this->db->query("SELECT name, language_id FROM ".DB_PREFIX."product_description WHERE product_id = '".$product_id."'");
			
			$product = $query->row;

			if(!empty($product)){

				$language_id = $product['language_id'];

				$product_name = utf8_substr(trim(strip_tags(html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'))), 0);

				$keyword = str_replace(' ', '-', strtolower($product_name));
				$keyword = str_replace('.', '-', $keyword);
				$keyword = str_replace('&', '-', $keyword);
				$keyword = str_replace("'", 'foot', $keyword);
				$keyword = str_replace('"', 'inch', $keyword);
				$keyword = preg_replace('/[^A-Za-z0-9\-]/', '', $keyword);
				$keyword = preg_replace('/\-+/', '-', $keyword);

				$keyword = rtrim($keyword, "-");

				$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");

			}
		}
	}

	public function generateMetaTitle($product_ids = array(),$custom_text, $store_id = 0)
	{

		foreach ($product_ids as $product_id) {

			$query = $this->db->query("SELECT pd.name, p.product_id, p.model, p.price, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '".$product_id."' ");

			$product = $query->row;
			
			if(!empty($product)){
				
				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];
				$price 	= $this->currency->format($product['price'], $this->config->get('config_currency'));

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc LEFT JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product['product_id']."'")->rows;

				$product_meta_title = ''; 
			
				if(!empty($name)){
					$product_meta_title = $name.'#';
				}

				if(!empty($model)){
					$product_meta_title .= $model.'#';
				}

				if(!empty($brand)){
					$product_meta_title .= $brand.'#';
				}

				if(!empty($price)){
					$product_meta_title .= $price.'#';
				}

				if(!empty($sku)){
					$product_meta_title .= $sku.'#';
				}

				if(!empty($upc)){
					$product_meta_title .= $upc.'#';
				}

				if(!empty($product_categories)){
					foreach ($product_categories as $product_category) {
						$product_meta_title .= $product_category['name'].'#';
					}
				}

				$product_meta_title = utf8_substr(trim(strip_tags(html_entity_decode($product_meta_title, ENT_QUOTES, 'UTF-8'))), 0);

				$product_meta_title = str_replace('#', '-', $product_meta_title);

				$product_meta_title = preg_replace('/[^A-Za-z0-9\,\-\(\)\&\'\"\.\$ ]/', '', $product_meta_title);

				$product_meta_title = rtrim($product_meta_title, "- ");

				$product_meta_title .= $custom_text; 

				$product_meta_title = $this->db->escape($product_meta_title);


				$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_title = '".$product_meta_title."' WHERE product_id = '".$product_id."' ");

			}

		}

	}

	public function generateMetaDescription($product_ids = array())
	{

		foreach ($product_ids as $product_id) {

			$query = $this->db->query("SELECT pd.name, pd.description, p.price, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = ".$product_id." ");

			$product = $query->row;

			
			if(!empty($product)){

				$name 			= $product['name'];
				$model 			= $product['model'];
				$description 	= $product['description'];

				$product_meta_description = ''; 
			
				if(!empty($name)){
					$product_meta_description = $name;
				}

				if(!empty($model)){
					$product_meta_description .= ' ('.$model.') ';
				}

				$product_meta_description = utf8_substr(trim(strip_tags(html_entity_decode($product_meta_description, ENT_QUOTES, 'UTF-8'))), 0);

				if(!empty($description)){
					$product_meta_description .= ' - '.utf8_substr(trim(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'))), 0, 320); 	
				}

				$product_meta_description = rtrim($product_meta_description, ", ");

				$product_meta_description = $this->db->escape($product_meta_description);

				if(!empty($product_meta_description)){
					$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_description = '".$product_meta_description."' WHERE product_id = '".$product_id."' ");	
				}
				
			}



				
		}
		
	}

	public function generateMetaKeywords($product_ids = array())
	{
		foreach ($product_ids as $product_id) {

			$query = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '".$product_id."'");

			$product = $query->row;

			// update product meta keywords
			if(!empty($product)){

				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product_id."'")->rows;


				$keyword = ''; 
			
				if(!empty($name)){
					$keyword = $name.'#';
				}

				if(!empty($brand)){
					$keyword .= $brand.'#';
				}

				if(!empty($model)){
					$keyword .= $model.'#';
				}

				if(!empty($sku)){
					$keyword .= $sku.'#';
				}

				if(!empty($upc)){
					$keyword .= $upc.'#';
				}

				if(!empty($product_categories)){
					foreach ($product_categories as $product_category) {
						$keyword .= $product_category['name'].'#';
					}
				}

				$keyword = utf8_substr(trim(strip_tags(html_entity_decode($keyword, ENT_QUOTES, 'UTF-8'))), 0);

				$keywords = str_replace('#', ', ', $keyword);
				$keywords = str_replace(' ', ', ', $keywords);
				$keywords = str_replace('&, ', '', $keywords);
				$keywords = str_replace('-, ', '', $keywords);
				$keywords = preg_replace('/\,+/', ',', strtolower($keywords));

				$keywords = explode(',', $keywords);

				$keywords_array = array();

				foreach ($keywords as $keyword) {
					if(!is_numeric($keyword) && $keyword != ' '){
						array_push($keywords_array, $keyword);
					}
				}

				$keywords = implode(',', $keywords_array);

				$product_keyword = rtrim($keywords, ", ");

				$product_keyword = $this->db->escape($product_keyword);


				$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_keyword = '".$product_keyword."' WHERE product_id = '".$product_id."' ");

			}

		}
		
	}

	public function generateProductTags($product_ids = array())
	{

		foreach ($product_ids as $product_id) {
			
			$query = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = ".$product_id." ");

			$product = $query->row;

			// update products meta tags
			if(!empty($product)){

				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product_id."'")->rows;


				$tags = ''; 
			
				if(!empty($name)){
					$tags = $name.'#';
				}

				if(!empty($brand)){
					$tags .= $brand.'#';
				}

				if(!empty($model)){
					$tags .= $model.'#';
				}

				if(!empty($sku)){
					$tags .= $sku.'#';
				}

				if(!empty($upc)){
					$tags .= $upc.'#';
				}

				if(!empty($product_categories)){
					foreach ($product_categories as $product_category) {
						$tags .= $product_category['name'].'#';
					}
				}

				$tags = utf8_substr(trim(strip_tags(html_entity_decode($tags, ENT_QUOTES, 'UTF-8'))), 0);

				$tags = str_replace('#', ', ', $tags);
				$tags = str_replace(' ', ', ', $tags);
				$tags = str_replace('&, ', '', $tags);
				$tags = str_replace('-, ', '', $tags);
				$tags = preg_replace('/\,+/', ',', strtolower($tags));

				$tags = explode(',', $tags);

				$tags_array = array();

				foreach ($tags as $tag) {
					if(!is_numeric($tag) && $tag != ' '){
						array_push($tags_array, $tag);
					}
				}

				$tags = implode(',', $tags_array);

				$product_tags = rtrim($tags, ", ");

				$product_tags = $this->db->escape($product_tags);

				if(!empty($product_tags)){
					$this->db->query("UPDATE ".DB_PREFIX."product_description SET tag = '".$product_tags."' WHERE product_id = '".$product_id."' ");
				}
			}
		}
	}

	public function generateImageCustomTitle($product_ids = array(), $custom_text)
	{
		foreach ($product_ids as $product_id) {
		
			$query = $this->db->query("SELECT pd.name, p.product_id, p.model, p.price, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '".$product_id."' ");

			$product = $query->row;

			// product images meta title
			if(!empty($product)){
				

					$product_images = $this->db->query("SELECT pi.product_image_id, pi.image AS product_image FROM ".DB_PREFIX."product p RIGHT JOIN ".DB_PREFIX."product_image pi ON(p.product_id = pi.product_id) WHERE pi.product_id = '".$product_id."' ");


					$name 	= $product['name'];
					$model 	= $product['model'];
					$sku 	= $product['sku'];
					$upc 	= $product['upc'];
					$brand 	= $product['brand'];
					$price 	= $this->currency->format($product['price'], $this->config->get('config_currency'));


					$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product['product_id']."'")->rows;


					$product_image_title = '';

					$product_featured_image_title = '';

					$count_image = 1;

					// product additional images title
					if($product_images->num_rows){
						foreach ($product_images->rows as $product_image) {
						
							$product_image_id = $product_image['product_image_id'];
							
							if(!empty($name)){
								$product_image_title = $name.'#';
							}

							if(!empty($brand) && $count_image == 1){
								$product_image_title .= $brand.'#';
							}

							if(!empty($model) && $count_image == 2){
								$product_image_title .= $model.'#';

							}

							if(!empty($sku) && $count_image == 3){
								$product_image_title .= $sku.'#';
							}

							if(!empty($upc) && $count_image == 6){
								$product_image_title .= $upc.'#';
							}


							if(!empty($product_categories && $count_image == 7)){
								foreach ($product_categories as $product_category) {
									$product_image_title .= $product_category['name'].'#';
								}
							}

							if($count_image > 7){
								$product_image_title .= $product_image_title ;
							}


							// product additional images title
							$product_image_title = utf8_substr(trim(strip_tags(html_entity_decode($product_image_title, ENT_QUOTES, 'UTF-8'))), 0);

							$product_image_title = str_replace('#', '-', $product_image_title);
							
							$product_image_title = preg_replace('/\-+/', '-', $product_image_title);
							
							$product_image_title = rtrim($product_image_title, "- ");

							if(!empty($custom_text)){
								$product_image_title .= $custom_text;
							}

							$product_image_title = $this->db->escape($product_image_title);

							if(!empty($product_image_title)){
								$this->db->query("UPDATE ".DB_PREFIX."product_image SET image_custom_title = '".$product_image_title."' WHERE product_image_id = '".$product_image_id."' AND product_id = '".$product_id."' AND image !=''  ");
							}

							$count_image++;
						}

					} 
				
					if(!empty($name)){
						$product_featured_image_title = $name.'#';
					}

					if(!empty($model)){
						$product_featured_image_title .= $model.'#';
					}


					if(!empty($brand)){
						$product_featured_image_title .= $brand.'#';

					}

					if(!empty($price)){
						$product_featured_image_title .= $price.'#';
					}

					if(!empty($sku)){
						$product_featured_image_title .= $sku.'#';
					}

					if(!empty($upc)){
						$product_featured_image_title .= $upc.'#';
					}

					if(!empty($product_categories)){
						foreach ($product_categories as $product_category) {
							$product_featured_image_title .= $product_category['name'].'#';
						}
					}


					// featured image title
					$product_featured_image_title = utf8_substr(trim(strip_tags(html_entity_decode($product_featured_image_title, ENT_QUOTES, 'UTF-8'))), 0);

					$product_featured_image_title = str_replace('#', '-', $product_featured_image_title);

					$product_featured_image_title = preg_replace('/\-+/', '-', $product_featured_image_title);

					$product_featured_image_title = rtrim($product_featured_image_title, "- ");

					if(!empty($custom_text)){
						$product_featured_image_title .= $custom_text;
					}

					$product_featured_image_title = $this->db->escape($product_featured_image_title);

					$this->db->query("UPDATE ".DB_PREFIX."product SET image_custom_title = '".$product_featured_image_title."' WHERE product_id = '".$product_id."' AND image !=''  ");
				
			}
		}
	}


	public function generateImageCustomAlt($product_ids = array(), $custom_text)
	{
		foreach ($product_ids as $product_id) {

			$query = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '".$product_id."' ");

			$product = $query->row;

			// product images meta title
			if(!empty($product)){

				$product_id = $product['product_id'];

				$product_images = $this->db->query("SELECT pi.product_image_id, pi.image AS product_image FROM ".DB_PREFIX."product p RIGHT JOIN ".DB_PREFIX."product_image pi ON(p.product_id = pi.product_id) WHERE pi.product_id = '".$product_id."' ");


				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product['product_id']."'")->rows;


				$product_image_alt = '';

				$product_featured_image_alt = '';

				$count_image = 1;

				// product additional images alt
				if($product_images->num_rows){
					foreach ($product_images->rows as $product_image) {
					
						$product_image_id = $product_image['product_image_id'];
						
						if(!empty($name)){
							$product_image_alt = $name.'#';
						}

						if(!empty($brand) && $count_image == 1){
							$product_image_alt .= $brand.'#';
						}

						if(!empty($model) && $count_image == 2){
							$product_image_alt .= $model.'#';

						}

						if(!empty($sku) && $count_image == 3){
							$product_image_alt .= $sku.'#';
						}

						if(!empty($upc) && $count_image == 6){
							$product_image_alt .= $upc.'#';
						}


						if(!empty($product_categories && $count_image == 7)){
							foreach ($product_categories as $product_category) {
								$product_image_alt .= $product_category['name'].'#';
							}
						}

						if($count_image > 7){
							$product_image_alt .= $product_image_alt ;
						}


						// product additional images alt
						$product_image_alt = utf8_substr(trim(strip_tags(html_entity_decode($product_image_alt, ENT_QUOTES, 'UTF-8'))), 0);

						$product_image_alt = str_replace('#', '-', $product_image_alt);
						
						$product_image_alt = preg_replace('/\-+/', '-', $product_image_alt);
						
						$product_image_alt = rtrim($product_image_alt, "- ");

						if(!empty($custom_text)){
							$product_image_alt .= $custom_text;
						}

						$product_image_alt = $this->db->escape($product_image_alt);

						$this->db->query("UPDATE ".DB_PREFIX."product_image SET image_custom_alt = '".$product_image_alt."' WHERE product_image_id = '".$product_image_id."' AND product_id = '".$product_id."' ");

						$count_image++;
					}

				} 
			
				if(!empty($name)){
					$product_featured_image_alt = $name.'#';
				}

				if(!empty($brand)){
					$product_featured_image_alt .= $brand.'#';

				}

				if(!empty($model)){
					$product_featured_image_alt .= $model.'#';
				}

				if(!empty($sku)){
					$product_featured_image_alt .= $sku.'#';
				}

				if(!empty($upc)){
					$product_featured_image_alt .= $upc.'#';
				}

				if(!empty($product_categories)){
					foreach ($product_categories as $product_category) {
						$product_featured_image_alt .= $product_category['name'].'#';
					}
				}


				// featured image alt
				$product_featured_image_alt = utf8_substr(trim(strip_tags(html_entity_decode($product_featured_image_alt, ENT_QUOTES, 'UTF-8'))), 0);

				$product_featured_image_alt = str_replace('#', '-', $product_featured_image_alt);
				
				$product_featured_image_alt = preg_replace('/\-+/', '-', $product_featured_image_alt);

				$product_featured_image_alt = rtrim($product_featured_image_alt, "- ");

				if(!empty($custom_text)){
					$product_featured_image_alt .= $custom_text;
				}

				$product_featured_image_alt = $this->db->escape($product_featured_image_alt);

				if(!empty($product_featured_image_alt)){
					$this->db->query("UPDATE ".DB_PREFIX."product SET image_custom_alt = '".$product_featured_image_alt."' WHERE product_id = '".$product_id."' AND image !='' ");
				}

			}
		}
	}
}
