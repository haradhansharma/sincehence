<?php
class ControllerExtensionModuleIextendSitemap extends Controller {

	private function getManufacturerSeoUrls($manufacturer_id) {
		$manufacturer_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "' limit 1");

		return $query->rows[0]['keyword'];
	}

	private function getCategorySeoUrls($category_id) {
		$category_seo_url = array();
		
		$query = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'category_id=" . (int)$category_id . "' limit 1");

		return $query->rows[0]['keyword'];
	}

	public function getBrandsTag() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

		
		$tags = array();

		foreach ($manufacturers as $manufacturer) {

			$manufacturer_seo_url = $this->getManufacturerSeoUrls($manufacturer['manufacturer_id']);

			$manufacturer_tags = explode(',', $manufacturer['tag']);

			foreach ($manufacturer_tags as $manufacturer_tag) {

				array_push($tags, trim($manufacturer_tag));

			}			

			foreach ($tags as $tag) {

				$tag = str_replace(' ', '-', $tag); 

				$output .= '<url>';
				$output .= '  <loc>'. str_replace('?tag=', '/', $this->url->link('product/manufacturer/info', 'tag=' . $tag . '&manufacturer_id=' . $manufacturer['manufacturer_id'])).'</loc>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($manufacturer['date_modified'])) . '</lastmod>';
				$output .= '  <image:image>';
				$output .= '  <image:loc>' . $this->model_tool_image->resize($manufacturer['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				$output .= '  </image:image>';
				$output .= '</url>';
			}

			$tags = array();

		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);

		}

	}

	public function getCategoryTags() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$results = $this->model_catalog_category->getCategories();

		$tags = array();

		foreach ($results as $result) {

			$category_seo_url = $this->getCategorySeoUrls($result['category_id']);

			$category_tags = explode(',', $result['tag']);

			foreach ($category_tags as $category_tag) {

				array_push($tags, trim($category_tag));

			}			

			foreach ($tags as $tag) {

				$tag = str_replace(' ', '-', $tag); 

				$output .= '<url>';
				$output .= '  <loc>'. str_replace('index.php?route=product/category&amp;tag=', $category_seo_url.'/', $this->url->link('product/category', 'tag=' . $tag)).'</loc>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($result['date_modified'])) . '</lastmod>';
				$output .= '</url>';
			}

			$tags = array();

		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);

		}

	}

	public function getImages() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$products = $this->model_catalog_product->getProducts();

			foreach ($products as $product) {
				if ($product['image']) {
					$output .= '<url>';
					$output .= '  <loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</loc>';
					$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
					$output .= '</url>';
				}
			}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}

	}

	public function getProductTags() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$products = $this->model_catalog_product->getProducts();

		$tags = array();

		foreach ($products as $product) {

			$product_tags = explode(',', $product['tag']);

			foreach ($product_tags as $product_tag) {

				array_push($tags, trim($product_tag));

			}			

			foreach ($tags as $tag) {
				$output .= '<url>';
				$output .= '  <loc>'.str_replace('?tag=', '/', $this->url->link('product/product', 'tag=' . $tag .'&product_id=' . $product['product_id'])).'</loc>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
				$output .= '  <image:image>';
				$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				$output .= '  </image:image>';
				$output .= '</url>';
			}

			$tags = array();

		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);

		}

	}

	public function getBrands() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/product');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$manufacturers = $this->model_catalog_manufacturer->getManufacturers();

		foreach ($manufacturers as $manufacturer) {
			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
			$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($manufacturer['date_modified'])) . '</lastmod>';
			$output .= '</url>';

			$products = $this->model_catalog_product->getProducts(array('filter_manufacturer_id' => $manufacturer['manufacturer_id']));

			foreach ($products as $product) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('product/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . '&product_id=' . $product['product_id']) . '</loc>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
				$output .= '  <image:image>';
				$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				$output .= '  </image:image>';
				$output .= '</url>';
			}
		}

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);

		}

	}
	public function getCategory() {
		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/product');

		$this->load->model('catalog/category');

		$this->load->model('tool/image');

		$output .= $this->getCategories(0);

		$output .= '</urlset>';

		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);

		}
	}
	public function getPages() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/information');

			$informations = $this->model_catalog_information->getInformations();

			// var_dump($informations);die;

			foreach ($informations as $information) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($information['date_modified'])) . '</lastmod>';
				$output .= '</url>';

				}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}

	}

	public function getProducts() {

		if ($this->config->get('module_iextend_sitemap_status')) {

		$output  = '<?xml version="1.0" encoding="UTF-8"?>';

		$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

			$products = $this->model_catalog_product->getProducts();

			// var_dump($products);die;

			foreach ($products as $product) {
				if ($product['image']) {
					$output .= '<url>';
					$output .= '  <loc>' . $this->url->link('product/product', 'product_id=' . $product['product_id']) . '</loc>';
					$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
					$output .= '  <image:image>';
					$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
					$output .= '  <image:caption>' . $product['name'] . '</image:caption>';
					$output .= '  <image:title>' . $product['name'] . '</image:title>';
					$output .= '  </image:image>';
					$output .= '</url>';
				}
			}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}

	}
	public function index() {
		if ($this->config->get('module_iextend_sitemap_status')) {

			$output  = '<?xml version="1.0" encoding="UTF-8"?>';

			$output .= '<?xml-stylesheet type="text/xsl" href="catalog/view/theme/default/stylesheet/sitemap.xsl"?>';

			$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('page-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('product-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('product-cat-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('category-tag-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('product-tag-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('brands-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('brands-tag-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '<sitemap>';
			$output .= '<loc>'.str_replace('index.php?route=', '', $this->url->link('images-sitemap.xml')).'</loc>';
			$output .= '</sitemap>';

			$output .= '</sitemapindex>';

			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	protected function getCategories($parent_id, $current_path = '') {
		$output = '';

		$results = $this->model_catalog_category->getCategories($parent_id);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}

			$output .= '<url>';
			$output .= '  <loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
			$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($result['date_modified'])) . '</lastmod>';
			$output .= '  <image:image>';
			$output .= '  <image:loc>' . $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
			$output .= '  </image:image>';
			$output .= '</url>';

			$products = $this->model_catalog_product->getProducts(array('filter_category_id' => $result['category_id']));

			foreach ($products as $product) {
				$output .= '<url>';
				$output .= '  <loc>' . $this->url->link('product/product', 'path=' . $new_path . '&product_id=' . $product['product_id']) . '</loc>';
				$output .= '  <lastmod>' . date('Y-m-d\TH:i:sP', strtotime($product['date_modified'])) . '</lastmod>';
				$output .= '  <image:image>';
				$output .= '  <image:loc>' . $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')) . '</image:loc>';
				$output .= '  </image:image>';
				$output .= '</url>';
			}

			$output .= $this->getCategories($result['category_id'], $new_path);
		}

		return $output;
	}
}
