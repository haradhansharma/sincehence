<?php
class ControllerExtensionModuleReportsProInformation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/reports_pro/information');

		$this->document->setTitle($this->language->get('information_heading_title'));

		$this->load->model('extension/reports_pro/information');

		$this->getList();
	}


	public function edit() {
		$this->load->language('extension/module/reports_pro/information');

		$this->document->setTitle($this->language->get('information_heading_title'));

		$this->load->model('extension/reports_pro/information');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_reports_pro_information->editInformation($this->request->get['information_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_title'])) {
				$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_meta_title_status'])) {
				$url .= '&filter_meta_title_status=' . $this->request->get['filter_meta_title_status'];
			}

			if (isset($this->request->get['filter_meta_description_status'])) {
				$url .= '&filter_meta_description_status=' . $this->request->get['filter_meta_description_status'];
			}

			if (isset($this->request->get['filter_meta_keywords_status'])) {
				$url .= '&filter_meta_keywords_status=' . $this->request->get['filter_meta_keywords_status'];
			}

			if (isset($this->request->get['filter_custom_h1_tag_status'])) {
				$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
			}

			if (isset($this->request->get['filter_seo_status'])) {
				$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}


	protected function getList() {
		if (isset($this->request->get['filter_title'])) {
			$filter_title = $this->request->get['filter_title'];
		} else {
			$filter_title = '';
		}

		if (isset($this->request->get['filter_meta_title_status'])) {
			$filter_meta_title_status = $this->request->get['filter_meta_title_status'];
		} else {
			$filter_meta_title_status = '';
		}

		if (isset($this->request->get['filter_meta_description_status'])) {
			$filter_meta_description_status = $this->request->get['filter_meta_description_status'];
		} else {
			$filter_meta_description_status = '';
		}

		if (isset($this->request->get['filter_meta_keywords_status'])) {
			$filter_meta_keywords_status = $this->request->get['filter_meta_keywords_status'];
		} else {
			$filter_meta_keywords_status = '';
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$filter_custom_h1_tag_status = $this->request->get['filter_custom_h1_tag_status'];
		} else {
			$filter_custom_h1_tag_status = '';
		}

		if (isset($this->request->get['filter_seo_status'])) {
			$filter_seo_status = $this->request->get['filter_seo_status'];
		} else {
			$filter_seo_status = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}


		// check if no information is selected
		if($this->request->server['REQUEST_METHOD'] == 'POST' && empty($data['selected'])){
			$data['warning']  = true;
		}
		
		$custom_text = ' | '.$this->config->get('config_name');

		// actions against each selected information
		if(!empty($data['selected'])){

			$information_ids = $data['selected'];
			
			$action = $this->request->post['action'];
			
			
			if($action == 'seo_url'){

				$this->model_extension_reports_pro_information->generateSeoUrls($information_ids);

			}else if($action == 'meta_title'){

				$this->model_extension_reports_pro_information->generateMetaTitle($information_ids, $custom_text);

			}else if($action == 'meta_description'){

				$this->model_extension_reports_pro_information->generateMetaDescription($information_ids);
				
			}else if($action == 'meta_keywords'){

				$this->model_extension_reports_pro_information->generateMetaKeywords($information_ids, $custom_text);
				
			}else{
				// your code...
			}
			
			
			$this->session->data['success'] = $this->language->get('text_success');

			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_meta_title_status'])) {
			$url .= '&filter_meta_title_status=' . $this->request->get['filter_meta_title_status'];
		}

		if (isset($this->request->get['filter_meta_description_status'])) {
			$url .= '&filter_meta_description_status=' . $this->request->get['filter_meta_description_status'];
		}

		if (isset($this->request->get['filter_meta_keywords_status'])) {
			$url .= '&filter_meta_keywords_status=' . $this->request->get['filter_meta_keywords_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_seo_status'])) {
			$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/reports_pro/report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('information_title'),
			'href' => $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$data['informations'] = array();

		$filter_data = array(
			'filter_title'	  				=> $filter_title,
			'filter_meta_title_status'   	=> $filter_meta_title_status,
			'filter_meta_description_status'=> $filter_meta_description_status,
			'filter_meta_keywords_status'   => $filter_meta_keywords_status,
			'filter_custom_h1_tag_status'		=> $filter_custom_h1_tag_status,
			'filter_seo_status'      		=> $filter_seo_status,
			'filter_status'   				=> $filter_status,
			'sort' 							=> $sort,
			'order'							=> $order,
			'start'							=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'							=> $this->config->get('config_limit_admin')
		);

		$information_total = $this->model_extension_reports_pro_information->getTotalInformations($filter_data);

		$results = $this->model_extension_reports_pro_information->getInformations($filter_data);

		foreach ($results as $result) {
			$data['informations'][] = array(
				'seo_score' 	 	=> $result['seo_score'],
				'readability_score' => $result['readability_score'],
				'information_id' 	=> $result['information_id'],
				'title'          	=> $result['title'],
				'status'         	=> $result['status'],
				'meta_title' 	 	=> $result['meta_title'],
				'meta_keyword'   	=> $result['meta_keyword'],
				'edit'           	=> $this->url->link('extension/module/reports_pro/information/edit', 'user_token=' . $this->session->data['user_token'] . '&information_id=' . $result['information_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_meta_title_status'])) {
			$url .= '&filter_meta_title_status=' . $this->request->get['filter_meta_title_status'];
		}

		if (isset($this->request->get['filter_meta_description_status'])) {
			$url .= '&filter_meta_description_status=' . $this->request->get['filter_meta_description_status'];
		}

		if (isset($this->request->get['filter_meta_keywords_status'])) {
			$url .= '&filter_meta_keywords_status=' . $this->request->get['filter_meta_keywords_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_seo_status'])) {
			$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . '&sort=i.status' . $url, true);
		$data['sort_meta_title'] = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.meta_title' . $url, true);
		$data['sort_meta_keyword'] = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.meta_keyword' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_meta_title_status'])) {
			$url .= '&filter_meta_title_status=' . $this->request->get['filter_meta_title_status'];
		}

		if (isset($this->request->get['filter_meta_description_status'])) {
			$url .= '&filter_meta_description_status=' . $this->request->get['filter_meta_description_status'];
		}

		if (isset($this->request->get['filter_meta_keywords_status'])) {
			$url .= '&filter_meta_keywords_status=' . $this->request->get['filter_meta_keywords_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_seo_status'])) {
			$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $information_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($information_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($information_total - $this->config->get('config_limit_admin'))) ? $information_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $information_total, ceil($information_total / $this->config->get('config_limit_admin')));

		// store filters into data 
		$data['filter_title'] = $filter_title;
		$data['filter_meta_title_status'] = $filter_meta_title_status;
		$data['filter_meta_description_status'] = $filter_meta_description_status;
		$data['filter_meta_keywords_status'] = $filter_meta_keywords_status;
		$data['filter_custom_h1_tag_status'] = $filter_custom_h1_tag_status;
		$data['filter_seo_status'] = $filter_seo_status;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/information_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['information_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_title'])) {
			$url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_meta_title_status'])) {
			$url .= '&filter_meta_title_status=' . $this->request->get['filter_meta_title_status'];
		}

		if (isset($this->request->get['filter_meta_description_status'])) {
			$url .= '&filter_meta_description_status=' . $this->request->get['filter_meta_description_status'];
		}

		if (isset($this->request->get['filter_meta_keywords_status'])) {
			$url .= '&filter_meta_keywords_status=' . $this->request->get['filter_meta_keywords_status'];
		}	

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_seo_status'])) {
			$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}


		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/reports_pro/report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('information_title'),
			'href' => $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['information_id'])) {
			$data['action'] = $this->url->link('extension/module/reports_pro/information/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/reports_pro/information/edit', 'user_token=' . $this->session->data['user_token'] . '&information_id=' . $this->request->get['information_id'] . $url, true);
		}

		// STORE URLS
		// http://www.iExtendlabs.com
		$data['www_store_non_secure_url'] = rtrim(HTTP_CATALOG, '/'); 
		// https://www.iExtendlabs.com
		$data['www_store_secure_url'] = rtrim(HTTPS_CATALOG, '/');
		// http://iExtendlabs.com
		$data['store_non_secure_url'] =rtrim((str_replace('www.', '', HTTP_CATALOG)), '/');
		// https://iExtendlabs.com
		$data['store_secure_url'] =rtrim((str_replace('www.', '', HTTPS_CATALOG)), '/'); 
		

		$data['focus_keyphrases'] = $this->model_extension_reports_pro_information->getInformationsFocusKeyphrases($this->request->get['information_id']);


		$data['cancel'] = $this->url->link('extension/module/reports_pro/information', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['information_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$information_info = $this->model_extension_reports_pro_information->getInformation($this->request->get['information_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['information_description'])) {
			$data['information_description'] = $this->request->post['information_description'];
		} elseif (isset($this->request->get['information_id'])) {
			$data['information_description'] = $this->model_extension_reports_pro_information->getInformationDescriptions($this->request->get['information_id']);
		} else {
			$data['information_description'] = array();
		}

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		if (isset($this->request->post['information_store'])) {
			$data['information_store'] = $this->request->post['information_store'];
		} elseif (isset($this->request->get['information_id'])) {
			$data['information_store'] = $this->model_extension_reports_pro_information->getInformationStores($this->request->get['information_id']);
		} else {
			$data['information_store'] = array(0);
		}

		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($information_info)) {
			$data['bottom'] = $information_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($information_info)) {
			$data['status'] = $information_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($information_info)) {
			$data['sort_order'] = $information_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}
		
		if (isset($this->request->post['information_seo_url'])) {
			$data['information_seo_url'] = $this->request->post['information_seo_url'];
		} elseif (isset($this->request->get['information_id'])) {
			$data['information_seo_url'] = $this->model_extension_reports_pro_information->getInformationSeoUrls($this->request->get['information_id']);
		} else {
			$data['information_seo_url'] = array();
		}
		
		if (isset($this->request->post['information_layout'])) {
			$data['information_layout'] = $this->request->post['information_layout'];
		} elseif (isset($this->request->get['information_id'])) {
			$data['information_layout'] = $this->model_extension_reports_pro_information->getInformationLayouts($this->request->get['information_id']);
		} else {
			$data['information_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/information_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/reports_pro/information')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['information_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 1) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if ($this->request->post['information_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['information_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}						
						
						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
						
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['information_id']) || ($seo_url['query'] != 'information_id=' . $this->request->get['information_id']))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}



	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_title'])) {
			$this->load->model('extension/reports_pro/information');

			$filter_data = array(
				'filter_title' => $this->request->get['filter_title'],
				'sort'        => 'title',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_reports_pro_information->getInformations($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'information_id' => $result['information_id'],
					'title'        => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['title'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


}