<?php
class ControllerExtensionModuleReportsProManufacturer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/reports_pro/manufacturer');

		$this->document->setTitle($this->language->get('manufacturer_heading_title'));

		$this->load->model('extension/reports_pro/manufacturer');

		$this->getList();
	}


	public function edit() {
		$this->load->language('extension/module/reports_pro/manufacturer');
		
		$this->document->setTitle($this->language->get('manufacturer_heading_title'));

		$this->load->model('extension/reports_pro/manufacturer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_extension_reports_pro_manufacturer->editManufacturer($this->request->get['manufacturer_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

			if (isset($this->request->get['filter_image_custom_title_status'])) {
				$url .= '&filter_image_custom_title_status=' . $this->request->get['filter_image_custom_title_status'];
			}

			if (isset($this->request->get['filter_image_custom_alt_status'])) {
				$url .= '&filter_image_custom_alt_status=' . $this->request->get['filter_image_custom_alt_status'];
			}

			if (isset($this->request->get['filter_tags_status'])) {
				$url .= '&filter_tags_status=' . $this->request->get['filter_tags_status'];
			}

			if (isset($this->request->get['filter_custom_h1_tag_status'])) {
				$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
			}

			if (isset($this->request->get['filter_image_status'])) {
				$url .= '&filter_image_status=' . $this->request->get['filter_image_status'];
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

			$this->response->redirect($this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}



	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
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

		if (isset($this->request->get['filter_image_custom_title_status'])) {
			$filter_image_custom_title_status = $this->request->get['filter_image_custom_title_status'];
		} else {
			$filter_image_custom_title_status = '';
		}

		if (isset($this->request->get['filter_image_custom_alt_status'])) {
			$filter_image_custom_alt_status = $this->request->get['filter_image_custom_alt_status'];
		} else {
			$filter_image_custom_alt_status = '';
		}

		if (isset($this->request->get['filter_tags_status'])) {
			$filter_tags_status = $this->request->get['filter_tags_status'];
		} else {
			$filter_tags_status = '';
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$filter_custom_h1_tag_status = $this->request->get['filter_custom_h1_tag_status'];
		} else {
			$filter_custom_h1_tag_status = '';
		}

		if (isset($this->request->get['filter_image_status'])) {
			$filter_image_status = $this->request->get['filter_image_status'];
		} else {
			$filter_image_status = '';
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
			$sort = 'm.name';
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

		$custom_text = ' | '.$this->config->get('config_name');


		// check if no product is selected
		if($this->request->server['REQUEST_METHOD'] == 'POST' && empty($data['selected'])){
			$data['warning']  = true;
		}
		
		// actions against each selected product
		if(!empty($data['selected'])){

			$manufacturer_ids = $data['selected'];
			
			$action = $this->request->post['action'];

			if($action == 'seo_url'){

				$this->model_extension_reports_pro_manufacturer->generateSeoUrls($manufacturer_ids);

			}else if($action == 'meta_title'){

				$this->model_extension_reports_pro_manufacturer->generateMetaTitle($manufacturer_ids, $custom_text);

			}else if($action == 'meta_description'){

				$this->model_extension_reports_pro_manufacturer->generateMetaDescription($manufacturer_ids);
				
			}else if($action == 'meta_keywords'){

				$this->model_extension_reports_pro_manufacturer->generateMetaKeywords($manufacturer_ids);
				
			}else if($action == 'tags'){

				$this->model_extension_reports_pro_manufacturer->generateTags($manufacturer_ids);
				
			}else if($action == 'image_custom_title'){

				$this->model_extension_reports_pro_manufacturer->generateImageCustomTitle($manufacturer_ids, $custom_text);
				
			}else if($action == 'image_custom_alt'){

				$this->model_extension_reports_pro_manufacturer->generateImageCustomAlt($manufacturer_ids, $custom_text);
				
			}else {

				// your code
				
			}
			 
			
			$this->session->data['success'] = $this->language->get('text_success');

			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image_custom_title_status'])) {
			$url .= '&filter_image_custom_title_status=' . $this->request->get['filter_image_custom_title_status'];
		}

		if (isset($this->request->get['filter_image_custom_alt_status'])) {
			$url .= '&filter_image_custom_alt_status=' . $this->request->get['filter_image_custom_alt_status'];
		}


		if (isset($this->request->get['filter_tags_status'])) {
			$url .= '&filter_tags_status=' . $this->request->get['filter_tags_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_image_status'])) {
			$url .= '&filter_image_status=' . $this->request->get['filter_image_status'];
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
			'text' => $this->language->get('manufacturer_title'),
			'href' => $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['manufacturers'] = array();

		$filter_data = array(
			'filter_name'	  						=> $filter_name,
			'filter_meta_title_status'   			=> $filter_meta_title_status,
			'filter_meta_description_status'		=> $filter_meta_description_status,
			'filter_meta_keywords_status'   		=> $filter_meta_keywords_status,
			'filter_image_custom_title_status'   	=> $filter_image_custom_title_status,
			'filter_image_custom_alt_status'   		=> $filter_image_custom_alt_status,
			'filter_custom_h1_tag_status'			=> $filter_custom_h1_tag_status,
			'filter_tags_status'   					=> $filter_tags_status,
			'filter_image_status'   				=> $filter_image_status,
			'filter_seo_status'      				=> $filter_seo_status,
			'filter_status'   						=> $filter_status,
			'sort'  								=> $sort,
			'order' 								=> $order,
			'start' 								=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 								=> $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$manufacturer_total = $this->model_extension_reports_pro_manufacturer->getTotalManufacturers($filter_data);

		$results = $this->model_extension_reports_pro_manufacturer->getManufacturers($filter_data);

		foreach ($results as $result) {

			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['manufacturers'][] = array(
				'focus_keyphrase' 	=> $result['focus_keyphrase'],
				'seo_score' 	 	=> $result['seo_score'],
				'readability_score' => $result['readability_score'],
				'manufacturer_id' 	=> $result['manufacturer_id'],
				'name'            	=> $result['name'],
				'image'       		=> $image,
				'meta_title'      	=> $result['meta_title'],
				'meta_keyword'    	=> $result['meta_keyword'],
				'tag'    	        => $result['tag'],
				'image_custom_title'=> $result['image_custom_title'],
				'image_custom_alt'  => $result['image_custom_alt'],
				'edit'            	=> $this->url->link('extension/module/reports_pro/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $result['manufacturer_id'] . $url, true)
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image_custom_title_status'])) {
			$url .= '&filter_image_custom_title_status=' . $this->request->get['filter_image_custom_title_status'];
		}

		if (isset($this->request->get['filter_image_custom_alt_status'])) {
			$url .= '&filter_image_custom_alt_status=' . $this->request->get['filter_image_custom_alt_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_tags_status'])) {
			$url .= '&filter_tags_status=' . $this->request->get['filter_tags_status'];
		}
		
		if (isset($this->request->get['filter_image_status'])) {
			$url .= '&filter_image_status=' . $this->request->get['filter_image_status'];
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

		$data['sort_name'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.name' . $url, true);
		$data['sort_meta_title'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.meta_title' . $url, true);
		$data['sort_meta_keyword'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.meta_keyword' . $url, true);
		$data['sort_image_custom_title'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.image_custom_title' . $url, true);
		$data['sort_image_custom_alt'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=m.image_custom_alt' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image_custom_title_status'])) {
			$url .= '&filter_image_custom_title_status=' . $this->request->get['filter_image_custom_title_status'];
		}

		if (isset($this->request->get['filter_image_custom_alt_status'])) {
			$url .= '&filter_image_custom_alt_status=' . $this->request->get['filter_image_custom_alt_status'];
		}

		if (isset($this->request->get['filter_tags_status'])) {
			$url .= '&filter_tags_status=' . $this->request->get['filter_tags_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_seo_status'])) {
			$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
		}

		if (isset($this->request->get['filter_image_status'])) {
			$url .= '&filter_image_status=' . $this->request->get['filter_image_status'];
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
		$pagination->total = $manufacturer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($manufacturer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($manufacturer_total - $this->config->get('config_limit_admin'))) ? $manufacturer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $manufacturer_total, ceil($manufacturer_total / $this->config->get('config_limit_admin')));

		// store filters into data 
		$data['filter_name'] = $filter_name;
		$data['filter_meta_title_status'] = $filter_meta_title_status;
		$data['filter_meta_description_status'] = $filter_meta_description_status;
		$data['filter_meta_keywords_status'] = $filter_meta_keywords_status;
		$data['filter_image_custom_title_status'] = $filter_image_custom_title_status;
		$data['filter_image_custom_alt_status'] = $filter_image_custom_alt_status;
		$data['filter_custom_h1_tag_status'] = $filter_custom_h1_tag_status;
		$data['filter_tags_status'] = $filter_tags_status;
		$data['filter_image_status'] = $filter_image_status;
		$data['filter_seo_status'] = $filter_seo_status;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/manufacturer_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['manufacturer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image_custom_title_status'])) {
			$url .= '&filter_image_custom_title_status=' . $this->request->get['filter_image_custom_title_status'];
		}

		if (isset($this->request->get['filter_image_custom_alt_status'])) {
			$url .= '&filter_image_custom_alt_status=' . $this->request->get['filter_image_custom_alt_status'];
		}

		if (isset($this->request->get['filter_tags_status'])) {
			$url .= '&filter_tags_status=' . $this->request->get['filter_tags_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		if (isset($this->request->get['filter_image_status'])) {
			$url .= '&filter_image_status=' . $this->request->get['filter_image_status'];
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
			'text' => $this->language->get('manufacturer_title'),
			'href' => $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (isset($this->request->get['manufacturer_id'])) {
			$data['action'] = $this->url->link('extension/module/reports_pro/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, true);
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
		

		$data['focus_keyphrases'] = $this->model_extension_reports_pro_manufacturer->getManufacturersFocusKeyphrases($this->request->get['manufacturer_id']);


		$data['cancel'] = $this->url->link('extension/module/reports_pro/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['manufacturer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$manufacturer_info = $this->model_extension_reports_pro_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($manufacturer_info)) {
			$data['name'] = $manufacturer_info['name'];
		} else {
			$data['name'] = '';
		}


		// custom description
		if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($manufacturer_info)) {
			$data['description'] = $manufacturer_info['description'];
		} else {
			$data['description'] = '';
		}

		// custom meta title
		if (isset($this->request->post['meta_title'])) {
			$data['meta_title'] = $this->request->post['meta_title'];
		} elseif (!empty($manufacturer_info)) {
			$data['meta_title'] = $manufacturer_info['meta_title'];
		} else {
			$data['meta_title'] = '';
		}


		// custom meta description
		if (isset($this->request->post['meta_description'])) {
			$data['meta_description'] = $this->request->post['meta_description'];
		} elseif (!empty($manufacturer_info)) {
			$data['meta_description'] = $manufacturer_info['meta_description'];
		} else {
			$data['meta_description'] = '';
		}

		// custom meta keyword
		if (isset($this->request->post['meta_keyword'])) {
			$data['meta_keyword'] = $this->request->post['meta_keyword'];
		} elseif (!empty($manufacturer_info)) {
			$data['meta_keyword'] = $manufacturer_info['meta_keyword'];
		} else {
			$data['meta_keyword'] = '';
		}

		// custom meta keyword
		if (isset($this->request->post['tag'])) {
			$data['tag'] = $this->request->post['tag'];
		} elseif (!empty($manufacturer_info)) {
			$data['tag'] = $manufacturer_info['tag'];
		} else {
			$data['tag'] = '';
		}

		// image custom title
		if (isset($this->request->post['image_custom_title'])) {
			$data['image_custom_title'] = $this->request->post['image_custom_title'];
		} elseif (!empty($manufacturer_info)) {
			$data['image_custom_title'] = $manufacturer_info['image_custom_title'];
		} else {
			$data['image_custom_title'] = '';
		}

		// image custom alt
		if (isset($this->request->post['image_custom_alt'])) {
			$data['image_custom_alt'] = $this->request->post['image_custom_alt'];
		} elseif (!empty($manufacturer_info)) {
			$data['image_custom_alt'] = $manufacturer_info['image_custom_alt'];
		} else {
			$data['image_custom_alt'] = '';
		}

		// focus keyphrase
		if (isset($this->request->post['focus_keyphrase'])) {
			$data['focus_keyphrase'] = $this->request->post['focus_keyphrase'];
		} elseif (!empty($manufacturer_info)) {
			$data['focus_keyphrase'] = $manufacturer_info['focus_keyphrase'];
		} else {
			$data['focus_keyphrase'] = '';
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

		if (isset($this->request->post['manufacturer_store'])) {
			$data['manufacturer_store'] = $this->request->post['manufacturer_store'];
		} elseif (isset($this->request->get['manufacturer_id'])) {
			$data['manufacturer_store'] = $this->model_extension_reports_pro_manufacturer->getManufacturerStores($this->request->get['manufacturer_id']);
		} else {
			$data['manufacturer_store'] = array(0);
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($manufacturer_info)) {
			$data['image'] = $manufacturer_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($manufacturer_info) && is_file(DIR_IMAGE . $manufacturer_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($manufacturer_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($manufacturer_info)) {
			$data['sort_order'] = $manufacturer_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['manufacturer_seo_url'])) {
			$data['manufacturer_seo_url'] = $this->request->post['manufacturer_seo_url'];
		} elseif (isset($this->request->get['manufacturer_id'])) {
			$data['manufacturer_seo_url'] = $this->model_extension_reports_pro_manufacturer->getManufacturerSeoUrls($this->request->get['manufacturer_id']);
		} else {
			$data['manufacturer_seo_url'] = array();
		}
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/manufacturer_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/reports_pro/manufacturer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ($this->request->post['manufacturer_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['manufacturer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}							
						
						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
						
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['manufacturer_id']) || (($seo_url['query'] != 'manufacturer_id=' . $this->request->get['manufacturer_id'])))) {
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
							}
						}
					}
				}
			}
		}

		return !$this->error;
	}


	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/reports_pro/manufacturer');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_reports_pro_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}