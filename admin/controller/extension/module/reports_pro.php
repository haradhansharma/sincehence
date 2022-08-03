<?php
class ControllerExtensionModuleReportsPro extends Controller {
	private $error = array();

	public function install() {

		$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product` LIKE 'image_custom_alt'");
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."product` ADD 
        	(`image_custom_alt` varchar(255) NOT NULL DEFAULT '',
        	`image_custom_title` varchar(255) NOT NULL DEFAULT '',
        	`seo_score` float(6,2) NOT NULL,
        	`readability_score` float(6,2) NOT NULL)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_description` LIKE 'focus_keyphrase'");
		 
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."product_description` ADD 
        	(`focus_keyphrase` varchar(255) NOT NULL)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."product_image` LIKE 'image_custom_title'");
		 
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."product_image` ADD 
        	(`image_custom_title` text,
        	 `image_custom_alt` text)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."category` LIKE 'image_custom_title'");

        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."category` ADD 
        	(`image_custom_title` text,
        	 `image_custom_alt` text,
        	 `seo_score` float(6,2) NOT NULL,
        	 `readability_score` float(6,2) NOT NULL)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."category_description` LIKE 'focus_keyphrase'");
		 
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."category_description` ADD 
        	(`focus_keyphrase` varchar(255) NOT NULL,
        	 `tag` varchar(500) NOT NULL)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."information` LIKE 'seo_score'");
		 
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."information` ADD 
        	(`seo_score` float(6,2) NOT NULL,
        	 `readability_score` float(6,2) NOT NULL,
        	 `date_modified` datetime NOT NULL)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."information_description` LIKE 'focus_keyphrase'");
		 
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."information_description` ADD (`focus_keyphrase` varchar(255) NOT NULL)");

    	$query=$this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."manufacturer` LIKE 'focus_keyphrase'");
		 
        if(!$query->num_rows)
        $this->db->query("ALTER TABLE `".DB_PREFIX."manufacturer` ADD  
        	(`description` text NOT NULL,
        	 `meta_title` varchar(255) NOT NULL,
        	 `meta_description` varchar(255) NOT NULL,
        	 `meta_keyword` varchar(255) NOT NULL,
        	 `tag` varchar(255) NOT NULL,
        	 `focus_keyphrase` varchar(255) NOT NULL,
        	 `image_custom_title` text NOT NULL,
        	 `image_custom_alt` text NOT NULL,
        	 `seo_score` float(6,2) NOT NULL,
        	 `readability_score` float(6,2) NOT NULL,
        	 `date_modified` datetime NOT NULL)");

	}

	public function index() {

		$this->install();

		$this->load->language('extension/module/reports_pro');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_reports_pro', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/reports_pro', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/reports_pro', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_reports_pro_status'])) {
			$data['module_reports_pro_status'] = $this->request->post['module_reports_pro_status'];
		} else {
			$data['module_reports_pro_status'] = $this->config->get('module_reports_pro_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/report_setting', $data));
	}

	public function report()
	{
		$this->load->language('extension/module/reports_pro/report');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);


		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/reports_pro/report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_report'),
			'href' => $this->url->link('extension/module/reports_pro/report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$this->load->model('extension/reports_pro/report');
		
		// Store Report
		$data['store_meta_title'] 		= $this->config->get('config_meta_title');
		$data['store_meta_description'] = $this->config->get('config_meta_description');
		$data['store_meta_keyword'] 	= $this->config->get('config_meta_keyword');


		// Products Report
		$data['total_products'] = count($this->model_extension_reports_pro_report->getTotalProducts());

		$data['seo_url_products'] = count($this->model_extension_reports_pro_report->getSeoUrlProducts());

		$data['non_seo_url_products'] = number_format($data['total_products'] - $data['seo_url_products']);

		$data['active_products'] = count($this->model_extension_reports_pro_report->getActiveProducts());

		$data['inactive_products'] = number_format($data['total_products'] - $data['active_products']);

		$data['out_of_stock_products'] =count($this->model_extension_reports_pro_report->getOutOfStockProducts());

		$data['in_stock_products'] = number_format($data['total_products'] - $data['out_of_stock_products']);

		$data['products_with_tags'] =count($this->model_extension_reports_pro_report->getTagProducts());

		$data['products_without_tags'] = number_format($data['total_products'] - $data['products_with_tags']);

		$data['products_with_meta_title'] = count($this->model_extension_reports_pro_report->getMetaTitleProducts());

		$data['products_without_meta_title'] = number_format($data['total_products'] - $data['products_with_meta_title']);

		$data['products_with_meta_description'] = count($this->model_extension_reports_pro_report->getMetaDescriptionProducts());

		$data['products_without_meta_description'] = number_format($data['total_products'] - $data['products_with_meta_description']);

		$data['products_with_meta_keywords'] = count($this->model_extension_reports_pro_report->getMetaKeywordProducts());

		$data['products_without_meta_keywords'] = number_format($data['total_products'] - $data['products_with_meta_keywords']);

		$data['products_with_image_custom_title'] = count($this->model_extension_reports_pro_report->getImageCustomTitleProducts());

		$data['products_without_image_custom_title'] = number_format($data['total_products'] - $data['products_with_image_custom_title']);

		$data['products_with_image_custom_alt'] = count($this->model_extension_reports_pro_report->getImageCustomAltProducts());

		$data['products_without_image_custom_alt'] = number_format($data['total_products'] - $data['products_with_image_custom_alt']);

		$data['products_with_custom_h1_tag'] = count($this->model_extension_reports_pro_report->getH1TagProducts());

		$data['products_without_custom_h1_tag'] = number_format($data['total_products'] - $data['products_with_custom_h1_tag']);

		$data['products_with_custom_h2_tag'] = count($this->model_extension_reports_pro_report->getH2TagProducts());

		$data['products_without_custom_h2_tag'] = number_format($data['total_products'] - $data['products_with_custom_h2_tag']);

		$data['products_with_image'] = count($this->model_extension_reports_pro_report->getImageProducts());

		$data['products_without_image'] = number_format($data['total_products'] - $data['products_with_image']);


		// Categories Report
		$data['total_categories'] = count($this->model_extension_reports_pro_report->getTotalCategories());

		$data['seo_url_categories'] = count($this->model_extension_reports_pro_report->getSeoUrlCategories());

		$data['non_seo_url_categories'] = number_format($data['total_categories'] - $data['seo_url_categories']);

		$data['active_categories'] = count($this->model_extension_reports_pro_report->getActiveCategories());

		$data['inactive_categories'] = number_format($data['total_categories'] - $data['active_categories']);

		$data['categories_with_meta_title'] = count($this->model_extension_reports_pro_report->getMetaTitleCategories());
		$data['categories_without_meta_title'] = number_format($data['total_categories'] - $data['categories_with_meta_title']);

		$data['categories_with_meta_description'] = count($this->model_extension_reports_pro_report->getMetaDescriptionCategories());

		$data['categories_without_meta_description'] = number_format($data['total_categories'] - $data['categories_with_meta_description']);

		$data['categories_with_meta_keywords'] = count($this->model_extension_reports_pro_report->getMetaKeywordCategories());

		$data['categories_without_meta_keywords'] = number_format($data['total_categories'] - $data['categories_with_meta_keywords']);

		$data['categories_with_tags'] = count($this->model_extension_reports_pro_report->getTagCategories());

		$data['categories_without_tags'] = number_format($data['total_categories'] - $data['categories_with_tags']);

		$data['categories_with_image_custom_title'] = count($this->model_extension_reports_pro_report->getImageCustomTitleCategories());

		$data['categories_without_image_custom_title'] = number_format($data['total_categories'] - $data['categories_with_image_custom_title']);

		$data['categories_with_image_custom_alt'] = count($this->model_extension_reports_pro_report->getImageCustomAltCategories());

		$data['categories_without_image_custom_alt'] = number_format($data['total_categories'] - $data['categories_with_image_custom_alt']);

		$data['categories_with_custom_h1_tag'] = count($this->model_extension_reports_pro_report->getH1TagCategories());

		$data['categories_without_custom_h1_tag'] = number_format($data['total_categories'] - $data['categories_with_custom_h1_tag']);

		$data['categories_with_image'] = count($this->model_extension_reports_pro_report->getImageCategories());

		$data['categories_without_image'] = number_format($data['total_categories'] - $data['categories_with_image']);


		// Information Pages Report
		$data['total_informations'] = count($this->model_extension_reports_pro_report->getTotalInformations());

		$data['active_informations'] = count($this->model_extension_reports_pro_report->getActiveInformations());

		$data['inactive_informations'] = number_format($data['total_informations'] - $data['active_informations']);

		$data['seo_url_information'] = count($this->model_extension_reports_pro_report->getSeoUrlInformations());

		$data['non_seo_url_information'] = number_format($data['total_informations'] - $data['seo_url_information']);

		$data['informations_with_meta_title'] = count($this->model_extension_reports_pro_report->getMetaTitleInformations());

		$data['informations_without_meta_title'] = number_format($data['total_informations'] - $data['informations_with_meta_title']);

		$data['informations_with_meta_description'] = count($this->model_extension_reports_pro_report->getMetaDescriptionInformations());

		$data['informations_without_meta_description'] = number_format($data['total_informations'] - $data['informations_with_meta_description']);

		$data['informations_with_meta_keywords'] = count($this->model_extension_reports_pro_report->getMetaKeywordInformations());

		$data['informations_without_meta_keywords'] = number_format($data['total_informations'] - $data['informations_with_meta_keywords']);

		$data['informations_with_custom_h1_tag'] = count($this->model_extension_reports_pro_report->getH1TagInformations());

		$data['informations_without_custom_h1_tag'] = number_format($data['total_informations'] - $data['informations_with_custom_h1_tag']);


		// Manufacturers Report
		$data['total_manufacturers'] = count($this->model_extension_reports_pro_report->getTotalManufacturers());

		$data['seo_url_manufacturer'] = count($this->model_extension_reports_pro_report->getSeoUrlManufacturers());
		
		$data['manufacturers_with_image'] = count($this->model_extension_reports_pro_report->getImageManufacturers());

		$data['manufacturers_without_image'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_image']);

		$data['non_seo_url_manufacturer'] = number_format($data['total_manufacturers'] - $data['seo_url_manufacturer']);

		$data['manufacturers_with_meta_title'] = count($this->model_extension_reports_pro_report->getMetaTitleManufacturers());

		$data['manufacturers_without_meta_title'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_meta_title']);

		$data['manufacturers_with_meta_description'] = count($this->model_extension_reports_pro_report->getMetaDescriptionManufacturers());

		$data['manufacturers_without_meta_description'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_meta_description']);

		$data['manufacturers_with_meta_keywords'] = count($this->model_extension_reports_pro_report->getMetaKeywordManufacturers());

		$data['manufacturers_without_meta_keywords'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_meta_keywords']);

		$data['manufacturers_with_tags'] = count($this->model_extension_reports_pro_report->getTagManufacturers());

		$data['manufacturers_without_tags'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_tags']);

		$data['manufacturers_with_image_custom_title'] = count($this->model_extension_reports_pro_report->getImageCustomTitleManufacturers());

		$data['manufacturers_without_image_custom_title'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_image_custom_title']);

		$data['manufacturers_with_image_custom_alt'] = count($this->model_extension_reports_pro_report->getImageCustomAltManufacturers());

		$data['manufacturers_without_image_custom_alt'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_image_custom_alt']);

		$data['manufacturers_with_custom_h1_tag'] = count($this->model_extension_reports_pro_report->getH1TagManufacturers());

		$data['manufacturers_without_custom_h1_tag'] = number_format($data['total_manufacturers'] - $data['manufacturers_with_custom_h1_tag']);



		// Orders Report 
		$data['total_orders'] = count($this->model_extension_reports_pro_report->getTotalOrders());
		$data['pending_orders'] = count($this->model_extension_reports_pro_report->getTotalPendingOrders());
		$data['processing_orders'] = count($this->model_extension_reports_pro_report->getTotalProcessingOrders());
		$data['shipped_orders'] = count($this->model_extension_reports_pro_report->getTotalShippedOrders());
		$data['complete_orders'] = count($this->model_extension_reports_pro_report->getTotalCompleteOrders());
		$data['canceled_orders'] = count($this->model_extension_reports_pro_report->getTotalCanceledOrders());
		$data['denied_orders'] = count($this->model_extension_reports_pro_report->getTotalDeniedOrders());
		$data['canceled_reversal_orders'] = count($this->model_extension_reports_pro_report->getTotalCanceledReversalOrders());

		$data['failed_orders'] = count($this->model_extension_reports_pro_report->getTotalFailedOrders());
		$data['refunded_orders'] = count($this->model_extension_reports_pro_report->getTotalRefundedOrders());
		$data['reversed_orders'] = count($this->model_extension_reports_pro_report->getTotalReversedOrders());
		$data['charged_back_orders'] = count($this->model_extension_reports_pro_report->getTotalChargebackOrders());
		$data['expired_orders'] = count($this->model_extension_reports_pro_report->getTotalExpiredOrders());
		$data['processed_orders'] = count($this->model_extension_reports_pro_report->getTotalProcessedOrders());
		$data['voided_orders'] = count($this->model_extension_reports_pro_report->getTotalVoidedOrders());


		$data['store_action'] = $this->url->link('setting/setting', 'user_token=' . $this->session->data['user_token'], true);
		$data['product_action'] = $this->url->link('extension/module/reports_pro/product', 'user_token=' . $this->session->data['user_token'], true);
		$data['category_action'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'], true);
		$data['information_action'] = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'], true);
		$data['manufacturer_action'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'], true);
		$data['order_action'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/report', $data));

	}

	public function seo()
	{
		$this->load->language('extension/module/reports_pro/report');

		$this->document->setTitle($this->language->get('heading_seo'));

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);


		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/reports_pro/report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_seo'),
			'href' => $this->url->link('extension/module/reports_pro/seo', 'user_token=' . $this->session->data['user_token'], true)
		);

		$this->load->model('extension/reports_pro/seo');

		$custom_text = ' | '.$this->config->get('config_name');

		if(!empty($this->request->post)){

			// GENERATE ACTIONS
			if(isset($this->request->post['generate'])){

				if($this->request->post['generate'] == 'meta_title' ){

					$this->model_extension_reports_pro_seo->generateMetaTitle($custom_text);
					$data['success'] = $this->language->get('text_meta_title_success');

				}else if($this->request->post['generate'] == 'meta_description' ){

					$this->model_extension_reports_pro_seo->generateMetaDescription($custom_text);
					$data['success'] = $this->language->get('text_meta_description_success');

				}else if($this->request->post['generate'] == 'keywords' ){

					$this->model_extension_reports_pro_seo->generateMetaKeywords();
					$data['success'] = $this->language->get('text_meta_keyword_success');

				}else if($this->request->post['generate'] == 'meta_tags'){

					$this->model_extension_reports_pro_seo->generateTags();
					$data['success'] = $this->language->get('text_tag_success');

				}else if($this->request->post['generate'] == 'seo_url' ){

					$this->model_extension_reports_pro_seo->generateSeoUrl();
					$data['success'] = $this->language->get('text_seo_url_success');

				}else if($this->request->post['generate'] == 'custom_title' ){

					$this->model_extension_reports_pro_seo->generateCustomImageTitles($custom_text);
					$data['success'] = $this->language->get('text_custom_image_title_success');

				}else if($this->request->post['generate'] == 'custom_alt' ){

					$this->model_extension_reports_pro_seo->generateCustomImageAlts($custom_text);
					$data['success'] = $this->language->get('text_custom_image_alt_success');

				}else{
					// code...
				}

			}

			// RENAME IMAGES
			if(!empty($this->request->post['rename_images'])){

				$this->model_extension_reports_pro_seo->renameImages();
				$data['success'] = $this->language->get('text_image_name_rename_success');

			}


			// CLEAR ACTIONS
			if(isset($this->request->post['clear'])){

				if($this->request->post['clear'] == 'clear_meta_description'){

					$this->model_extension_reports_pro_seo->clearMetaDescription();	
					$data['success'] = $this->language->get('text_meta_description_success');

				}else if($this->request->post['clear'] == 'clear_meta_keywords'){

					$this->model_extension_reports_pro_seo->clearMetaKeywords();
					$data['success'] = $this->language->get('text_meta_keyword_success');

				}else if($this->request->post['clear'] == 'clear_meta_tags'){

					$this->model_extension_reports_pro_seo->clearTags();
					$data['success'] = $this->language->get('text_tag_success');

				}else if($this->request->post['clear'] == 'clear_seo_url'){

					$this->model_extension_reports_pro_seo->clearSeoUrl();
					$data['success'] = $this->language->get('text_seo_url_success');

				}else if($this->request->post['clear'] == 'clear_custom_titles'){

					$this->model_extension_reports_pro_seo->clearCustomImageTitles();
					$data['success'] = $this->language->get('text_custom_image_title_success');

				}else if($this->request->post['clear'] == 'clear_custom_alts'){

					$this->model_extension_reports_pro_seo->clearCustomImageAlts();	
					$data['success'] = $this->language->get('text_custom_image_alt_success');

				}else{
					// your code...
				}

			}
			
		}

		
		$data['action'] = $this->url->link('extension/module/reports_pro/seo', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/module/reports_pro/report', 'user_token=' . $this->session->data['user_token'], true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/seo', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/reports_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}