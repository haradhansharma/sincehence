<?php
class ControllerExtensionModuleReportsProCategory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/reports_pro/category');

		$this->document->setTitle($this->language->get('category_heading_title'));

		$this->load->model('extension/reports_pro/category');

		$this->getList();
	}


	public function edit() {
		$this->load->language('catalog/category');
		$this->load->language('extension/module/reports_pro/category');

		$this->document->setTitle($this->language->get('category_heading_title'));

		$this->load->model('extension/reports_pro/category');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_reports_pro_category->editCategory($this->request->get['category_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			$sort = 'cd.name';
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

		// check if no category is selected 
		if($this->request->server['REQUEST_METHOD'] == 'POST' && empty($data['selected'])){
			$data['warning']  = true;
		}

		$custom_text = ' | '.$this->config->get('config_name');

		// actions against each selected category
		if(!empty($data['selected'])){

			$category_ids = $data['selected'];
			
			$action = $this->request->post['action'];


			if($action == 'seo_url'){

				$this->model_extension_reports_pro_category->generateSeoUrls($category_ids);

			}else if($action == 'meta_title'){

				$this->model_extension_reports_pro_category->generateMetaTitle($category_ids, $custom_text);

			}else if($action == 'meta_description'){

				$this->model_extension_reports_pro_category->generateMetaDescription($category_ids);
				
			}else if($action == 'meta_keywords'){

				$this->model_extension_reports_pro_category->generateMetaKeywords($category_ids);
				
			}else if($action == 'tags'){

				$this->model_extension_reports_pro_category->generateTags($category_ids);
				
			}else if($action == 'image_custom_title'){

				$this->model_extension_reports_pro_category->generateImageCustomTitle($category_ids, $custom_text);
				
			}else if($action == 'image_custom_alt'){

				$this->model_extension_reports_pro_category->generateImageCustomAlt($category_ids, $custom_text);
				
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

		if (isset($this->request->get['filter_seo_status'])) {
			$url .= '&filter_seo_status=' . $this->request->get['filter_seo_status'];
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		// breadcrumbs
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
			'text' => $this->language->get('heading_category'),
			'href' => $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$data['categories'] = array();

		// filter data into array
		$filter_data = array(
			'filter_name'	  					=> $filter_name,
			'filter_meta_title_status'   		=> $filter_meta_title_status,
			'filter_meta_description_status'	=> $filter_meta_description_status,
			'filter_meta_keywords_status'   	=> $filter_meta_keywords_status,
			'filter_image_custom_title_status'	=> $filter_image_custom_title_status,
			'filter_image_custom_alt_status' 	=> $filter_image_custom_alt_status,
			'filter_custom_h1_tag_status'		=> $filter_custom_h1_tag_status,
			'filter_tags_status'      			=> $filter_tags_status,
			'filter_seo_status'      			=> $filter_seo_status,
			'filter_image_status'   			=> $filter_image_status,
			'filter_status'   					=> $filter_status,
			'sort'            					=> $sort,
			'order'           					=> $order,
			'start'           					=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           					=> $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');
		
		$category_total = $this->model_extension_reports_pro_category->getTotalCategories($filter_data);

		$results = $this->model_extension_reports_pro_category->getCategories($filter_data);

		foreach ($results as $result) {

			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['categories'][] = array(
				'seo_score' 	 	 => $result['seo_score'],
				'readability_score'  => $result['readability_score'],
				'category_id'		 => $result['category_id'],
				'name'       		 => $result['name'],
				'image'       		 => $image,
				'sort_order' 		 => $result['sort_order'],
				'status'     		 => $result['status'],
				'meta_title' 		 => $result['meta_title'],
				'meta_keyword' 		 => $result['meta_keyword'],
				'tag' 		 	     => $result['tag'],
				'image_custom_title' => $result['image_custom_title'],
				'image_custom_alt'   => $result['image_custom_alt'],
				'edit'               => $this->url->link('extension/module/reports_pro/category/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url, true)
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.name' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
		$data['sort_meta_title'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.meta_title' . $url, true);
		$data['sort_meta_keyword'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.meta_keyword' . $url, true);
		$data['sort_tag'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=cd.tag' . $url, true);
		$data['sort_image_custom_title'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=c.image_custom_title' . $url, true);
		$data['sort_image_custom_alt'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . '&sort=c.image_custom_alt' . $url, true);

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

		// pagination setting
		$pagination = new Pagination();
		$pagination->total = $category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));

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

		$this->response->setOutput($this->load->view('extension/module/reports_pro/category_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['category_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
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

		if (isset($this->error['parent'])) {
			$data['error_parent'] = $this->error['parent'];
		} else {
			$data['error_parent'] = '';
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


		if (isset($this->request->get['filter_tags_status'])) {
			$url .= '&filter_tags_status=' . $this->request->get['filter_tags_status'];
		}

		if (isset($this->request->get['filter_custom_h1_tag_status'])) {
			$url .= '&filter_custom_h1_tag_status=' . $this->request->get['filter_custom_h1_tag_status'];
		}

		// image custom title
		if (isset($this->request->get['filter_image_custom_title_status'])) {
			$url .= '&filter_image_custom_title_status=' . $this->request->get['filter_image_custom_title_status'];
		}

		// image custom alt
		if (isset($this->request->get['filter_image_custom_alt_status'])) {
			$url .= '&filter_image_custom_alt_status=' . $this->request->get['filter_image_custom_alt_status'];
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
			'text' => $this->language->get('heading_category'),
			'href' => $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (isset($this->request->get['category_id'])) {
			$data['action'] = $this->url->link('extension/module/reports_pro/category/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $this->request->get['category_id'] . $url, true);
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


		$data['focus_keyphrases'] = $this->model_extension_reports_pro_category->getCategoriesFocusKeyphrases($this->request->get['category_id']);
		
		

		$data['cancel'] = $this->url->link('extension/module/reports_pro/category', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$category_info = $this->model_extension_reports_pro_category->getCategory($this->request->get['category_id']);
		}


		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['category_description'])) {
			$data['category_description'] = $this->request->post['category_description'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_description'] = $this->model_extension_reports_pro_category->getCategoryDescriptions($this->request->get['category_id']);
		} else {
			$data['category_description'] = array();
		}

		
		if (isset($this->request->post['path'])) {
			$data['path'] = $this->request->post['path'];
		} elseif (!empty($category_info)) {
			$data['path'] = $category_info['path'];
		} else {
			$data['path'] = '';
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($category_info)) {
			$data['parent_id'] = $category_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
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

		if (isset($this->request->post['category_store'])) {
			$data['category_store'] = $this->request->post['category_store'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_store'] = $this->model_extension_reports_pro_category->getCategoryStores($this->request->get['category_id']);
		} else {
			$data['category_store'] = array(0);
		}

		

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($category_info)) {
			$data['image'] = $category_info['image'];
		} else {
			$data['image'] = '';
		}

		// image_custom_title
		if (isset($this->request->post['image_custom_title'])) {
			$data['image_custom_title'] = $this->request->post['image_custom_title'];
		} elseif (!empty($category_info)) {
			$data['image_custom_title'] = $category_info['image_custom_title'];
		} else {
			$data['image_custom_title'] = '';
		}

		// image_custom_alt
		if (isset($this->request->post['image_custom_alt'])) {
			$data['image_custom_alt'] = $this->request->post['image_custom_alt'];
		} elseif (!empty($category_info)) {
			$data['image_custom_alt'] = $category_info['image_custom_alt'];
		} else {
			$data['image_custom_alt'] = '';
		}


		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['top'])) {
			$data['top'] = $this->request->post['top'];
		} elseif (!empty($category_info)) {
			$data['top'] = $category_info['top'];
		} else {
			$data['top'] = 0;
		}

		if (isset($this->request->post['column'])) {
			$data['column'] = $this->request->post['column'];
		} elseif (!empty($category_info)) {
			$data['column'] = $category_info['column'];
		} else {
			$data['column'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($category_info)) {
			$data['sort_order'] = $category_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($category_info)) {
			$data['status'] = $category_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['category_seo_url'])) {
			$data['category_seo_url'] = $this->request->post['category_seo_url'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_seo_url'] = $this->model_extension_reports_pro_category->getCategorySeoUrls($this->request->get['category_id']);
		} else {
			$data['category_seo_url'] = array();
		}
				
		if (isset($this->request->post['category_layout'])) {
			$data['category_layout'] = $this->request->post['category_layout'];
		} elseif (isset($this->request->get['category_id'])) {
			$data['category_layout'] = $this->model_extension_reports_pro_category->getCategoryLayouts($this->request->get['category_id']);
		} else {
			$data['category_layout'] = array();
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/reports_pro/category_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/reports_pro/category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['category_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 1) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		if (isset($this->request->get['category_id']) && $this->request->post['parent_id']) {
			$results = $this->model_extension_reports_pro_category->getCategoryPath($this->request->post['parent_id']);
			
			foreach ($results as $result) {
				if ($result['path_id'] == $this->request->get['category_id']) {
					$this->error['parent'] = $this->language->get('error_parent');
					
					break;
				}
			}
		}

		if ($this->request->post['category_seo_url']) {
			$this->load->model('design/seo_url');
			
			foreach ($this->request->post['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						if (count(array_keys($language, $keyword)) > 1) {
							$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_unique');
						}

						$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);
	
						foreach ($seo_urls as $seo_url) {
							if (($seo_url['store_id'] == $store_id) && (!isset($this->request->get['category_id']) || ($seo_url['query'] != 'category_id=' . $this->request->get['category_id']))) {		
								$this->error['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
				
								break;
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

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/reports_pro/category');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_reports_pro_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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
