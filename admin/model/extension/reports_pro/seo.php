<?php 

class ModelExtensionReportsProSeo extends Model
{
	public function generateMetaTitle($custom_text = '')
	{
		$products = $this->db->query("SELECT pd.name, p.product_id, p.model, p.price, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id)");
		
		$categories = $this->db->query("SELECT name, category_id FROM ".DB_PREFIX."category_description");

		$informations = $this->db->query("SELECT title,information_id FROM ".DB_PREFIX."information_description");

		$manufacturers = $this->db->query("SELECT name, manufacturer_id FROM ".DB_PREFIX."manufacturer ");


		// update products meta title
		if($products->num_rows){
			foreach ($products->rows as $product) {

				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];
				$price 	= $this->currency->format($product['price'], $this->config->get('config_currency'));

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product['product_id']."'")->rows;


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

				$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_title = '".$product_meta_title."' WHERE product_id = '".$product['product_id']."' ");


			}


		}


		// update categories meta title
		if($categories->num_rows){
			foreach ($categories->rows as $category) {

				$category_name = $category['name'];

				$category_meta_title = $this->db->escape($category_name);

				$category_meta_title .= $custom_text; 

				$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_title = '".$category_meta_title."' WHERE category_id = '".$category['category_id']."'");

			}
		}


		// update informations meta title
		if($informations->num_rows){
			foreach ($informations->rows as $information) {

				$information_title = $information['title'];

				$information_meta_title = $this->db->escape($information_title);

				$information_meta_title .= $custom_text; 

				$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_title = '".$information_meta_title."' WHERE information_id = '".$information['information_id']."' ");

			}

		}

		// update manufacturers meta title
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {

				$manufacturer_name = $manufacturer['name'];

				$manufacturer_meta_title = $this->db->escape($manufacturer_name);

				$manufacturer_meta_title .= $custom_text; 

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_title = '".$manufacturer_meta_title."' WHERE manufacturer_id = '".$manufacturer['manufacturer_id']."' ");

			}
		}

	}
	
	public function generateMetaDescription($custom_text)
	{

		$products = $this->db->query("SELECT pd.name, pd.description, p.price, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id)");

		$categories = $this->db->query("SELECT name, description, category_id FROM ".DB_PREFIX."category_description");

		$informations = $this->db->query("SELECT title , description, information_id FROM ".DB_PREFIX."information_description");

		$manufacturers = $this->db->query("SELECT name, manufacturer_id, description FROM ".DB_PREFIX."manufacturer ");


		// update products meta description
		if($products->num_rows){
			foreach ($products->rows as $product) {

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
					$product_meta_description .= ' - '.utf8_substr(trim(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_category_description_length')) . '..'; 	
				}
				
				$product_meta_description = rtrim($product_meta_description, ", ");

				$product_meta_description .= $custom_text; 

				$product_meta_description = $this->db->escape($product_meta_description);
				

				if(!empty($product_meta_description)){
					$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_description = '".$product_meta_description."' WHERE product_id = '".$product['product_id']."' ");	
				}
				

			}

		}


		// update categories meta description
		if($categories->num_rows){
			foreach ($categories->rows as $category) {
				$category_name = $category['name'];

				$category_meta_description = $this->db->escape($category_name);

				if(!empty($category['description'])){
					$category_meta_description .= ' - '. utf8_substr(trim(strip_tags(html_entity_decode($category['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_category_description_length')) . ''; 
				}

				$category_meta_description .= $custom_text; 

				$category_meta_description = $this->db->escape($category_meta_description);

				if(!empty($category_meta_description)){
					$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_description = '".$category_meta_description."' WHERE category_id = '".$category['category_id']."'");
				}
				
			}
		}


		// update informatioins meta description
		if($informations->num_rows){
			foreach ($informations->rows as $information) {
				$information_title = $information['title'];

				$information_meta_description = $this->db->escape($information_title);
				
				if(!empty($information['description'])){
					$information_meta_description .= ' - '. utf8_substr(trim(strip_tags(html_entity_decode($information['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_information_description_length')) . ''; 
				}

				$information_meta_description .= $custom_text; 

				$information_meta_description = $this->db->escape($information_meta_description);


				if(!empty($information_meta_description)){
					$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_description = '".$information_meta_description."' WHERE information_id = '".$information['information_id']."' ");
				}
				
			}
		}

		// update manufacturers meta description
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {
				$manufacturer_name = $manufacturer['name'];

				$manufacturer_meta_description = $this->db->escape($manufacturer_name);
				
				if(!empty($manufacturer['description'])){
					$manufacturer_meta_description .= ' - '. utf8_substr(trim(strip_tags(html_entity_decode($manufacturer['description'], ENT_QUOTES, 'UTF-8'))), 0); 
				}

				$manufacturer_meta_description .= $custom_text; 

				$manufacturer_meta_description = $this->db->escape($manufacturer_meta_description);
				

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_description = '".$manufacturer_meta_description."' WHERE manufacturer_id = '".$manufacturer['manufacturer_id']."' ");

			}
		}

	}

	public function generateMetaKeywords()
	{

		$products = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id)");

		$categories = $this->db->query("SELECT name, category_id FROM ".DB_PREFIX."category_description");

		$informations = $this->db->query("SELECT title,information_id FROM ".DB_PREFIX."information_description");

		$manufacturers = $this->db->query("SELECT name, manufacturer_id FROM ".DB_PREFIX."manufacturer ");


		// update products meta keywords
		if($products->num_rows){
			foreach ($products->rows as $product) {

				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product['product_id']."'")->rows;


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

				$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_keyword = '".$product_keyword."' WHERE product_id = '".$product['product_id']."' ");

			}

		}



		// update categories meta keywords
		if($categories->num_rows){
			foreach ($categories->rows as $category) {

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

				$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_keyword = '".$category_keywords."' WHERE category_id = '".$category['category_id']."'");

			}
		}


		// update informatioins meta keywords
		if($informations->num_rows){
			foreach ($informations->rows as $information) {

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
				
				$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_keyword = '".$information_keywords."' WHERE information_id = '".$information['information_id']."' ");

			}
		}


		// update manufacturers meta keywords
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {
				
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

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_keyword = '".$manufacturer_keywords."' WHERE manufacturer_id = '".$manufacturer['manufacturer_id']."' ");

			}
		}



	}

	public function generateTags()
	{

		$products = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id)");

		$categories = $this->db->query("SELECT name, category_id FROM ".DB_PREFIX."category_description");

		$manufacturers = $this->db->query("SELECT name, manufacturer_id FROM ".DB_PREFIX."manufacturer ");


		// update products meta tags
		if($products->num_rows){
			foreach ($products->rows as $product) {

				$name 	= $product['name'];
				$model 	= $product['model'];
				$sku 	= $product['sku'];
				$upc 	= $product['upc'];
				$brand 	= $product['brand'];

				$product_categories = $this->db->query("SELECT cd.name FROM ".DB_PREFIX."product_to_category ptc JOIN ".DB_PREFIX."category_description cd ON(ptc.category_id = cd.category_id) WHERE product_id ='".$product['product_id']."'")->rows;


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
					$this->db->query("UPDATE ".DB_PREFIX."product_description SET tag = '".$product_tags."' WHERE product_id = '".$product['product_id']."' ");
				}

			}

		}



		// update categories tags
		if($categories->num_rows){
			foreach ($categories->rows as $category) {

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
				
				$this->db->query("UPDATE ".DB_PREFIX."category_description SET tag = '".$category_tags."' WHERE category_id = '".$category['category_id']."'");

			}
		}


		// update manufacturers tags
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {
				
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

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET tag = '".$manufacturer_tags."' WHERE manufacturer_id = '".$manufacturer['manufacturer_id']."' ");

			}
		}



	}

	

	public function generateSeoUrl()
	{

		$products = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, pd.language_id, m.name AS brand, store_id FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id) LEFT JOIN ".DB_PREFIX."product_to_store pts ON (p.product_id = pts.product_id)");

// 		$categories = $this->db->query("SELECT name, cd.category_id, store_id FROM ".DB_PREFIX."category_description cd LEFT JOIN ".DB_PREFIX."category_to_store cts ON (cd.category_id = cts.category_id)");

		$informations = $this->db->query("SELECT title, id.information_id, language_id, store_id FROM ".DB_PREFIX."information_description id LEFT JOIN ".DB_PREFIX."information_to_store its ON (id.information_id = its.information_id)");
		
		

		$manufacturers = $this->db->query("SELECT name, m.manufacturer_id, store_id FROM ".DB_PREFIX."manufacturer m LEFT JOIN ".DB_PREFIX."manufacturer_to_store mts ON (m.manufacturer_id = mts.manufacturer_id)");

		$store_id = 0;

		// products seo urls 
		if($products->num_rows){
		    
		    foreach ($products->rows as $product2) {
		    
		    $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'product_id=" . (int)$product2['product_id'] . "'");
		    }
			foreach ($products->rows as $product) {

				$language_id = $product['language_id'];

				$name 	= strtolower($product['name']);
				
				if(!empty($name)){
					$seo_url = $name;
				}

				$seo_url = utf8_substr(trim(strip_tags(html_entity_decode($seo_url, ENT_QUOTES, 'UTF-8'))), 0);

				$seo_url = str_replace(' ', '-', $seo_url);
				$seo_url = str_replace('.', '-', $seo_url);
				$seo_url = str_replace('&', '-', $seo_url);
				$seo_url = str_replace("'", 'foot', $seo_url);
				$seo_url = str_replace('"', 'inch', $seo_url);
				$seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $seo_url);
				$seo_url = preg_replace('/\-+/', '-', $seo_url);

				$product_seo_url = rtrim($seo_url, "-");

				
				if (!empty($product_seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$product['store_id'] . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product['product_id'] . "', keyword = '" . $this->db->escape($product_seo_url) . "'");
				}

			}

		}

		// categories seo url
		////sharma edited to add parent id
		$categories = $this->db->query("SELECT `name`, `language_id`, cd.category_id, `parent_id`, `store_id` FROM `".DB_PREFIX."category_description` cd LEFT JOIN `".DB_PREFIX."category` c ON (c.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."category_to_store cts ON (cd.category_id = cts.category_id)");
		
		

		if($categories->num_rows){
		    foreach ($categories->rows as $category2) {
		    
		    $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'category_id=". (int)$category2['category_id'] ."'");
		    }
		    
			foreach ($categories->rows as $category) {
			    
			    if($category['parent_id'] == 0){
			        
			        $parent_id = '';
			    }else{
			       $parent_id =  $category['parent_id'];
			    }

				$language_id = $category['language_id'];

				$category_name = utf8_substr(trim(strip_tags(html_entity_decode($category['name'] . $parent_id, ENT_QUOTES, 'UTF-8'))), 0);

				$category_seo_url = preg_replace('/\s+/', '-', strtolower($category_name));
				$category_seo_url = str_replace('&', '-', $category_seo_url);
				$category_seo_url = str_replace("'", 'foot', $category_seo_url);
				$category_seo_url = str_replace('"', 'inch', $category_seo_url);
				$category_seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $category_seo_url);
				$category_seo_url = preg_replace('/\-+/', '-', $category_seo_url);

				$category_seo_url = rtrim($category_seo_url, "-");

				

				if (!empty($category_seo_url)) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$category['store_id'] . "', language_id = '" . (int)$language_id . "', query = 'category_id=" .$category['category_id'] . "', keyword = '" . $this->db->escape($category_seo_url) . "'");
				}

			}

		}

		// information pages seo url
		if($informations->num_rows){
		    foreach ($informations->rows as $information2) {
		    $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$information2['information_id'] . "'");
		    }
			foreach ($informations->rows as $information) {
				

					$language_id = $information['language_id'];

					$information_title = utf8_substr(trim(strip_tags(html_entity_decode($information['title'], ENT_QUOTES, 'UTF-8'))), 0);

					$information_seo_url = preg_replace('/\s+/', '-', strtolower($information_title));
					$information_seo_url = str_replace('&', '-', $information_seo_url);
					$information_seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $information_seo_url);
					$information_seo_url = preg_replace('/\-+/', '-', $information_seo_url);

					$information_seo_url = rtrim($information_seo_url, "-");

					if (!empty($information_seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$information['store_id'] . "', language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information['information_id'] . "', keyword = '" . $this->db->escape($information_seo_url) . "'");
					}

			}
		}

		// manufacturer seo url
		if($manufacturers->num_rows){
		    
		    foreach ($manufacturers->rows as $manufacturer2) {
		    $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'manufacturer_id=" . (int)$manufacturer2['manufacturer_id'] . "'");
		    }
			foreach ($manufacturers->rows as $manufacturer) {
				
				

					$language_id = '1';

					$manufacturer_name = utf8_substr(trim(strip_tags(html_entity_decode($manufacturer['name'], ENT_QUOTES, 'UTF-8'))), 0);

					$manufacturer_seo_url = preg_replace('/\s+/', '-', strtolower($manufacturer_name));
					$manufacturer_seo_url = str_replace('&', '-', $manufacturer_seo_url);
					$manufacturer_seo_url = preg_replace('/[^A-Za-z0-9\-]/', '', $manufacturer_seo_url);
					$manufacturer_seo_url = preg_replace('/\-+/', '-', $manufacturer_seo_url);

					$manufacturer_seo_url = rtrim($manufacturer_seo_url, "-");


					if (!empty($manufacturer_seo_url)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$manufacturer['store_id'] . "', language_id = '" . (int)$language_id . "', query = 'manufacturer_id=" . (int)$manufacturer['manufacturer_id'] . "', keyword = '" . $this->db->escape($manufacturer_seo_url) . "'");
					}

			}
		}
		
	}

	public function generateCustomImageTitles($custom_text)
	{
		$products = $this->db->query("SELECT pd.name, p.product_id, p.model,p.price, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id)");

		$categories = $this->db->query("SELECT name, category_id FROM ".DB_PREFIX."category_description");

		$manufacturers = $this->db->query("SELECT name, manufacturer_id FROM ".DB_PREFIX."manufacturer ");

		// product images meta title
		if($products->num_rows){
			foreach ($products->rows as $product) {

				$product_id = $product['product_id'];

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

						$this->db->query("UPDATE ".DB_PREFIX."product_image SET image_custom_title = '".$product_image_title."' WHERE product_image_id = '".$product_image_id."' AND product_id = '".$product_id."' AND image !=''  ");

						$count_image++;
					}

				} 
			
				if(!empty($name)){
					$product_featured_image_title = $name.'#';
				}

				if(!empty($brand)){
					$product_featured_image_title .= $brand.'#';

				}

				if(!empty($model)){
					$product_featured_image_title .= $model.'#';
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


		// category image custom title
		if($categories->num_rows){
			foreach ($categories->rows as $category) {
				$category_name = $category['name'];

				$category_image_title = $this->db->escape($category_name);

				if(!empty($custom_text)){
					$category_image_title .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."category SET image_custom_title = '".$category_image_title."' WHERE category_id = '".$category['category_id']."' AND image !='' ");

			}
		}

		// manufacturer image custom title
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {
				$manufacturer_name = $manufacturer['name'];

				$manufacturer_image_title = $this->db->escape($manufacturer_name);

				if(!empty($custom_text)){
					$manufacturer_image_title .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_title = '".$manufacturer_image_title."' WHERE manufacturer_id = '".$manufacturer['manufacturer_id']."' AND image !='' ");

			}
		}

	}

	public function generateCustomImageAlts($custom_text)
	{
		$products = $this->db->query("SELECT pd.name, p.product_id, p.model, p.sku, p.upc, m.name AS brand FROM ".DB_PREFIX."product p JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."manufacturer m ON(p.manufacturer_id = m.manufacturer_id)");

		$categories = $this->db->query("SELECT name, category_id FROM ".DB_PREFIX."category_description");

		$manufacturers = $this->db->query("SELECT name, manufacturer_id FROM ".DB_PREFIX."manufacturer ");


		// product images meta alt
		if($products->num_rows){
			foreach ($products->rows as $product) {

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

				$this->db->query("UPDATE ".DB_PREFIX."product SET image_custom_alt = '".$product_featured_image_alt."' WHERE product_id = '".$product_id."' AND image !='' ");

			}

		}


		// category image custom alt
		if($categories->num_rows){
			foreach ($categories->rows as $category) {
				$category_name = $category['name'];

				$category_image_alt = $this->db->escape($category_name);

				if(!empty($custom_text)){
					$category_image_alt .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."category SET image_custom_alt = '".$category_image_alt."' WHERE category_id = '".$category['category_id']."' AND image !='' ");

			}
		}


		// manufacturer image custom alt
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {
				$manufacturer_name = $manufacturer['name'];

				$manufacturer_image_alt = $this->db->escape($manufacturer_name);

				if(!empty($custom_text)){
					$manufacturer_image_alt .= $custom_text;
				}

				$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_alt = '".$manufacturer_image_alt."' WHERE manufacturer_id = '".$manufacturer['manufacturer_id']."' AND image !='' ");

			}
		}
	}

	public function renameImages()
	{	

		// products featured images rename 
		$products = $this->db->query("SELECT image, product_id FROM ".DB_PREFIX."product WHERE image != '' ORDER BY product_id ASC ");
		
		if($products->num_rows){
			foreach ($products->rows as $product) {
				
				// product featured image
				$featured_image_path = $product['image'];

				$image_dir  = substr($featured_image_path, 0,strrpos($featured_image_path, '/'));

				$image = explode('/', $featured_image_path);

				$image_name  = end($image);

				$image_extension = explode('.', $image_name);

				$product_seo_urls = $this->db->query("SELECT keyword FROM ".DB_PREFIX."seo_url WHERE query = 'product_id=".$product['product_id']."'");

				if($product_seo_urls->row){
					
					$product_seo_url = $product_seo_urls->row['keyword'];

					$image_new_name = $image_dir.'/'.$product_seo_url.'.'.$image_extension[1];

					if(file_exists(DIR_IMAGE.$featured_image_path)){

						rename(DIR_IMAGE.$featured_image_path, DIR_IMAGE.$image_new_name);

						$this->db->query("UPDATE ".DB_PREFIX."product SET image = '".(string)$image_new_name."' WHERE image = '".(string)$featured_image_path."' ");

						$this->db->query("UPDATE ".DB_PREFIX."product_image SET image = '".(string)$image_new_name."' WHERE image = '".(string)$featured_image_path."'  ");

					}

					// product additional images rename
					$product_images = $this->db->query("SELECT product_image_id , image, product_id FROM ".DB_PREFIX."product_image WHERE product_id = '".$product['product_id']."' AND image != '' ORDER BY product_image_id ASC ");
			
					if($product_images->num_rows){

						$count  = 1 ;

						foreach ($product_images->rows as $product_image) {
							
							// product additional image
							$additional_image_path = $product_image['image'];

							$image_dir  = substr($additional_image_path, 0,strrpos($additional_image_path, '/'));

							$image = explode('/', $additional_image_path);

							$image_name  = end($image);

							$image_extension = explode('.', $image_name);

								
							$image_new_name = $image_dir.'/'.$product_seo_url.'-'.$count.'.'.$image_extension[1];

							if(file_exists(DIR_IMAGE.$additional_image_path)){

								rename(DIR_IMAGE.$additional_image_path, DIR_IMAGE.$image_new_name);

								$this->db->query("UPDATE ".DB_PREFIX."product SET image = '".(string)$image_new_name."' WHERE image = '".(string)$additional_image_path."' ");

								$this->db->query("UPDATE ".DB_PREFIX."product_image SET image = '".(string)$image_new_name."' WHERE image = '".(string)$additional_image_path."'");

								$count++;

							}

						}
					}
				}
			}
		}


		// categories images rename 
		$categories = $this->db->query("SELECT image, category_id FROM ".DB_PREFIX."category WHERE image != '' ORDER BY category_id ASC ");
		
		if($categories->num_rows){
			foreach ($categories->rows as $category) {

				$image_path = $category['image'];

				$image_dir  = substr($image_path, 0,strrpos($image_path, '/'));

				$image = explode('/', $image_path);

				$image_name  = end($image);

				$image_extension = explode('.', $image_name);

				$category_seo_urls = $this->db->query("SELECT keyword FROM ".DB_PREFIX."seo_url WHERE query = 'category_id=".$category['category_id']."'");

				if($category_seo_urls->row){

					$category_seo_url = $category_seo_urls->row['keyword'];

					$image_new_name = $image_dir.'/'.$category_seo_url.'.'.$image_extension[1];

					if(file_exists(DIR_IMAGE.$image_path)){

						rename(DIR_IMAGE.$image_path, DIR_IMAGE.$image_new_name);

						$this->db->query("UPDATE ".DB_PREFIX."category SET image = '".(string)$image_new_name."' WHERE image = '".(string)$image_path."' ");

					}
				}
			}
		}


		// manufacturers images rename 
		$manufacturers = $this->db->query("SELECT image, manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE image != '' ORDER BY manufacturer_id ASC ");
		
		if($manufacturers->num_rows){
			foreach ($manufacturers->rows as $manufacturer) {

				$image_path = $manufacturer['image'];

				$image_dir  = substr($image_path, 0,strrpos($image_path, '/'));

				$image = explode('/', $image_path);

				$image_name  = end($image);

				$image_extension = explode('.', $image_name);

				$manufacturer_seo_urls = $this->db->query("SELECT keyword FROM ".DB_PREFIX."seo_url WHERE query = 'manufacturer_id=".$manufacturer['manufacturer_id']."'");

				if($manufacturer_seo_urls->row){
					
					$manufacturer_seo_url = $manufacturer_seo_urls->row['keyword'];

					$image_new_name = $image_dir.'/'.$manufacturer_seo_url.'.'.$image_extension[1];

					if(file_exists(DIR_IMAGE.$image_path)){

						rename(DIR_IMAGE.$image_path, DIR_IMAGE.$image_new_name);

						$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image = '".(string)$image_new_name."' WHERE image = '".(string)$image_path."'");

					}
				}

			}
		}
	}


	//// CLEAR DATA FUNCTIONS

	public function clearMetaDescription()
	{
		$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_description = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_description = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_description = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_description = '' ");
	}

	public function clearMetaKeywords()
	{
		$this->db->query("UPDATE ".DB_PREFIX."product_description SET meta_keyword = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."category_description SET meta_keyword = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."information_description SET meta_keyword = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET meta_keyword = '' ");
	}


	public function clearTags()
	{
		$this->db->query("UPDATE ".DB_PREFIX."product_description SET tag = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."category_description SET tag = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET tag = '' ");
	}

	public function clearSeoUrl()
	{
		$this->db->query("DELETE FROM ".DB_PREFIX."seo_url WHERE query LIKE 'product_id=%' OR query LIKE 'category_id=%' OR query LIKE 'information_id=%' OR query LIKE 'manufacturer_id=%'");
		
	}

	public function clearCustomImageTitles()
	{
		$this->db->query("UPDATE ".DB_PREFIX."product SET image_custom_title = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."product_image SET image_custom_title = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."category SET image_custom_title = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_title = '' ");
	}

	public function clearCustomImageAlts()
	{
		$this->db->query("UPDATE ".DB_PREFIX."product SET image_custom_alt = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."product_image SET image_custom_alt = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."category SET image_custom_alt = '' ");
		$this->db->query("UPDATE ".DB_PREFIX."manufacturer SET image_custom_alt = '' ");
	}
	
}

?>