<?php
class ControllerPossettingUser extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('possetting/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/user');

		$this->getList();
	}

	public function add() {
		$this->load->language('possetting/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/user');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_possetting_user->addUser($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('possetting/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/user');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_possetting_user->editUser($this->request->get['user_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('possetting/user');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('possetting/user');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $user_id) {
				$this->model_possetting_user->deleteUser($user_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		
		if (isset($this->request->get['filter_store'])) {
			
			$filter_store = $this->request->get['filter_store'];
		} else {
			$filter_store = false;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'username';
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

		$url = '';
		
		if ($order == 'ASC') {
		$url .= '&order=DESC';
		} else {
		$url .= '&order=ASC';
		}
		
		if (isset($this->request->get['filter_store'])) {
		$url .= '&filter_store=' . $this->request->get['filter_store'];
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
			'href' => $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('possetting/user/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('possetting/user/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['users'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'filter_store'    => $filter_store,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$user_total = $this->model_possetting_user->getTotalUsers($filter_data);
		$results = $this->model_possetting_user->getUsers($filter_data);
		$this->load->model('possetting/store');
		
		foreach ($results as $result) {
			$store_info = $this->model_possetting_store->getStore($result['store_id']);
			if(isset($store_info['name']))
			{
				$store=$store_info['name'];         
			}
			else
			{
				$store='';
			}	

			$data['users'][] = array(
				'user_id'    => $result['user_id'],
				'username'   => $result['username'],
				'store'   	 => $store,
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				//'commission' => ($result['commission'] ? $this->language->get('text_percent') : $this->language->get('text_fixed')),
				'commission' => $result['commission'],
				'commission_value' => $result['commission_value'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'       => $this->url->link('possetting/user/edit', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $result['user_id'] . $url, true)
			);
			//print_r($data['users']);
		}

		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['text_list'] 			= $this->language->get('text_list');
		$data['text_no_results'] 	= $this->language->get('text_no_results');
		$data['text_confirm'] 		= $this->language->get('text_confirm');
		$data['text_none'] 			= $this->language->get('text_none');
		$data['text_select'] 		= $this->language->get('text_select');
		$data['text_percent'] 		= $this->language->get('text_percent');
		$data['text_fixed'] 		= $this->language->get('text_fixed');
		$data['column_username'] 	= $this->language->get('column_username');
		$data['column_store'] 		= $this->language->get('column_store');
		$data['column_commission'] 	= $this->language->get('column_commission');
		$data['entry_store'] 		= $this->language->get('entry_store');
		$data['column_status'] 		= $this->language->get('column_status');
		$data['column_date_added'] 	= $this->language->get('column_date_added');
		$data['column_action'] 		= $this->language->get('column_action');
		$data['button_add'] 		= $this->language->get('button_add');
		$data['button_edit'] 		= $this->language->get('button_edit');
		$data['button_delete'] 		= $this->language->get('button_delete');
		$data['button_filter']		= $this->language->get('button_filter');
		$data['user_token']         		= $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_store']    = $filter_store;
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_username'] = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_store'] = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . '&sort=store' . $url, true);
		$data['sort_status'] = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_commission'] = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . '&sort=commission' . $url, true);
		$data['sort_date_added'] = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $user_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($user_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($user_total - $this->config->get('config_limit_admin'))) ? $user_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $user_total, ceil($user_total / $this->config->get('config_limit_admin')));

		$data['sort'] 		  = $sort;
		$data['order'] 		  = $order;
		$data['filter_store'] = $filter_store;
		
		
		$data['header'] 	 = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] 	 = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/user_list', $data));
	}

	protected function getForm() {
		$data['heading_title']		= $this->language->get('heading_title');
		$data['text_form'] 			= !isset($this->request->get['user_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] 		= $this->language->get('text_enabled');
		$data['text_disabled'] 		= $this->language->get('text_disabled');
		$data['entry_username'] 	= $this->language->get('entry_username');
		$data['entry_store'] 		= $this->language->get('entry_store');
		$data['entry_password'] 	= $this->language->get('entry_password');
		$data['entry_confirm'] 		= $this->language->get('entry_confirm');
		$data['entry_firstname'] 	= $this->language->get('entry_firstname');
		$data['entry_lastname'] 	= $this->language->get('entry_lastname');
		$data['entry_email'] 		= $this->language->get('entry_email');
		$data['entry_image'] 		= $this->language->get('entry_image');
		$data['entry_status'] 		= $this->language->get('entry_status');
		$data['entry_commission'] 	= $this->language->get('entry_commission');
		$data['entry_value'] 		= $this->language->get('entry_value');
		$data['button_save'] 		= $this->language->get('button_save');
		$data['button_cancel'] 		= $this->language->get('button_cancel');
		$data['text_select'] 		= $this->language->get('text_select');
		$data['text_percent']		= $this->language->get('text_percent');
		$data['text_fixed'] 		= $this->language->get('text_fixed');
		$data['text_none'] 			= $this->language->get('text_none');
		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		$url = '';

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
			'href' => $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['commissions'] = array();
				
		$data['commissions'][] = array(
			'commission'    => $this->language->get('text_percent'),
			'value' 		=> 'Pecentage'
		);

		$data['commissions'][] = array(
			'commission'    => $this->language->get('text_fixed'),
			'value' 		=> 'Fixed'
		);

		if (!isset($this->request->get['user_id'])) {
			$data['action'] = $this->url->link('possetting/user/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('possetting/user/edit', 'user_token=' . $this->session->data['user_token'] . '&user_id=' . $this->request->get['user_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('possetting/user', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['user_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$user_info = $this->model_possetting_user->getUser($this->request->get['user_id']);
		}

		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} elseif (!empty($user_info)) {
			$data['username'] = $user_info['username'];
		} else {
			$data['username'] = '';
		}

		if (isset($this->request->post['store_id'])) {
			$data['store_id'] = $this->request->post['store_id'];
		} elseif (!empty($user_info)) {
			$data['store_id'] = $user_info['store_id'];
		} else {
			$data['store_id'] = '';
		}

		$this->load->model('possetting/store');
		$data['stores'] = $this->model_possetting_store->getStores($data);

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} else {
			$data['confirm'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($user_info)) {
			$data['firstname'] = $user_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($user_info)) {
			$data['lastname'] = $user_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($user_info)) {
			$data['email'] = $user_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($user_info)) {
			$data['image'] = $user_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($user_info) && $user_info['image'] && is_file(DIR_IMAGE . $user_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($user_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($user_info)) {
			$data['status'] = $user_info['status'];
		} else {
			$data['status'] = 0;
		}

		if (isset($this->request->post['commission'])) {
			$data['commission'] = $this->request->post['commission'];
		} elseif (isset($user_info['commission'])) {
			$data['commission'] = $user_info['commission'];
		} else {
			$data['commission'] = '';
		}

		if (isset($this->request->post['commission_value'])) {
			$data['commission_value'] = $this->request->post['commission_value'];
		} elseif (isset($user_info['commission_value'])) {
			$data['commission_value'] = $user_info['commission_value'];
		} else {
			$data['commission_value'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('possetting/user_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'possetting/user')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['username']) < 3) || (utf8_strlen($this->request->post['username']) > 20)) {
			$this->error['username'] = $this->language->get('error_username');
		}

		$user_info = $this->model_possetting_user->getUserByUsername($this->request->post['username']);

		if (!isset($this->request->get['user_id'])) {
			if ($user_info) {
				$this->error['warning'] = $this->language->get('error_exists_username');
			}
		} else {
			if ($user_info && ($this->request->get['user_id'] != $user_info['user_id'])) {
				$this->error['warning'] = $this->language->get('error_exists_username');
			}
		}

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$user_info = $this->model_possetting_user->getUserByEmail($this->request->post['email']);

		if (!isset($this->request->get['user_id'])) {
			if ($user_info) {
				$this->error['warning'] = $this->language->get('error_exists_email');
			}
		} else {
			if ($user_info && ($this->request->get['user_id'] != $user_info['user_id'])) {
				$this->error['warning'] = $this->language->get('error_exists_email');
			}
		}

		if ($this->request->post['password'] || (!isset($this->request->get['user_id']))) {
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'possetting/user')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		

		return !$this->error;
	}
}