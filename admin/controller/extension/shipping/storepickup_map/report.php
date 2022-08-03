<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
class ControllerExtensionShippingStorePickupMapReport extends Controller {
	private $module_type = '';
	private $module_name = '';
	private $module_path = '';
	private $module_model = '';

	private $compatibility = null;

	private $language_data = array();
	private $error = array();
	private $token = '';

	private $default_filter = array('filter_name', 'filter_date_start', 'filter_date_end', 'filter_store_id');

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->config('storepickup_map');

		$this->module_type = $this->config->get('spm_module_type');
		$this->module_name = $this->config->get('spm_module_name');
		$this->module_path = $this->config->get('spm_module_path');

		$this->load->model($this->module_path);

		$this->module_model = $this->{$this->config->get('spm_module_model')};

		$this->compatibility = $this->module_model->compatibility();

		$this->language_data = $this->language->load($this->module_path . '/report');

		$token_name = $this->compatibility->getAdminTokenName();
		$this->token = $token_name . '=' . $this->compatibility->getSessionValue($token_name);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	protected function getList() {
		$this->compatibility->loadStyles(str_replace($this->module_type . '_', '', $this->module_name));

		$data = array_merge(array(), $this->language_data);

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = (int)$this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}

		$filter_data = array(
			'filter_name'       => $filter_name,
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'filter_store_id'   => $filter_store_id
		);

		$data['store_total'] = $this->module_model->getTotalUsedStores($filter_data);
		$data['order_total'] = $this->module_model->getTotalOrders($filter_data);
		$data['total'] = $this->currency->format($this->module_model->getTotalSales($filter_data), $this->config->get('config_currency'));

		$data['filter_name'] = $filter_name;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_store_id'] = $filter_store_id;

		$data['stores'] = $this->compatibility->getStores();

		$data['links'] = $this->getManageLinks();

		$data['module_path'] = $this->module_path;

		$data['token'] = $this->token;

		foreach ($this->compatibility->getChildren() as $key => $child) {
			$data[$key] = ($key == 'header') ? $this->compatibility->jquery($child) : $child;
		}

		$this->response->setOutput($this->compatibility->view($this->module_path . '/report_list', $data));
	}

	public function store() {
		$data = array_merge(array(), $this->language_data);

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = (int)$this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = $this->compatibility->paginationLimit(20);

		$data['stores'] = array();

		$filter_data = array(
			'filter_name'       => $filter_name,
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'filter_store_id'   => $filter_store_id,
			'start'             => ($page - 1) * $limit,
			'limit'             => $limit
		);

		$store_total = $this->module_model->getTotalUsedStores($filter_data);

		$results = $this->module_model->getUsedStores($filter_data);

		foreach ($results as $result) {
			$data['stores'][] = array(
				'storepickup_id' => $result['storepickup_id'],
				'name'           => $result['name'],
				'orders'         => $result['orders'],
				'products'       => $result['products'],
				'total'          => $this->currency->format($result['total'], $this->config->get('config_currency')),
				'last_used'      => date($this->language->get('date_format_long'), strtotime($result['last_used']))
			);
		}

		$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array()));

		$data['pagination'] = $this->compatibility->pagination(
			$store_total,
			$page,
			$limit,
			$this->compatibility->link($this->module_path . '/report/store', $this->token . $url . '&format=raw&page={page}')
		);

		$data['results'] = $this->compatibility->paginationText($page, $limit, $store_total);

		$data['module_path'] = $this->module_path;

		$data['token'] = $this->token;

		$this->response->setOutput($this->compatibility->view($this->module_path . '/report_store', $data));
	}

	private function getManageLinks() {
		$links = array();

		foreach ((array)$this->config->get('spm_menu') as $menu) {
			$links[] = array(
				'name' => $menu['name'],
				'href' => $menu['action'] ? $this->compatibility->link($menu['action'], $this->token) : ''
			);
		}

		return $links;
	}
}
?>