<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
class ControllerExtensionShippingStorePickupMapStore extends Controller {
	private $module_type = '';
	private $module_name = '';
	private $module_path = '';
	private $module_model = '';

	private $compatibility = null;

	private $language_data = array();
	private $error = array();
	private $token = '';

	private $default_filter = array('filter_name', 'filter_address', 'filter_country_id', 'filter_store_id', 'filter_status', 'filter_date_added');

	const SEPARATOR_CSV = ',';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->config('storepickup_map');

		$this->module_type = $this->config->get('spm_module_type');
		$this->module_name = $this->config->get('spm_module_name');
		$this->module_path = $this->config->get('spm_module_path');

		$this->load->model($this->module_path);

		$this->module_model = $this->{$this->config->get('spm_module_model')};

		$this->compatibility = $this->module_model->compatibility();

		$this->language_data = $this->language->load($this->module_path . '/store');

		$token_name = $this->compatibility->getAdminTokenName();
		$this->token = $token_name . '=' . $this->compatibility->getSessionValue($token_name);
	}

	public function index() {
		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function add() {
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->module_model->addStore($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('sort', 'order', 'page')));

			$this->compatibility->redirect($this->compatibility->link($this->module_path . '/store', $this->token . $url));
		}

		$this->getForm();
	}

	public function edit() {
	    

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->module_model->editStore($this->request->get['storepickup_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('sort', 'order', 'page')));

			$this->compatibility->redirect($this->compatibility->link($this->module_path . '/store', $this->token . $url));
		
		}

		$this->getForm();
	}

	public function delete() {
		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->validateDelete()) {
			if (isset($this->request->post['selected'])) {
				foreach ($this->request->post['selected'] as $storepickup_id) {
					$this->module_model->deleteStore($storepickup_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');
			} elseif (isset($this->request->get['storepickup_id'])) {
				$this->module_model->deleteStore($this->request->get['storepickup_id']);

				$this->session->data['success'] = $this->language->get('text_success');
			}

			$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('sort', 'order', 'page')));

			$this->compatibility->redirect($this->compatibility->link($this->module_path . '/store', $this->token . $url));
		}

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

		if (isset($this->request->get['filter_address'])) {
			$filter_address = $this->request->get['filter_address'];
		} else {
			$filter_address = '';
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = (int)$this->request->get['filter_country_id'];
		} else {
			$filter_country_id = '';
		}

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = (int)$this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = (int)$this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}
		/////
		if (isset($this->request->get['filter_central'])) {
			$filter_central = (int)$this->request->get['filter_central'];
		} else {
			$filter_central = '';
		}
		/////

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 's.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('sort', 'order', 'page')));

		$data['add'] = $this->compatibility->link($this->module_path . '/store/add', $this->token . $url);
		$data['export'] = $this->compatibility->link($this->module_path . '/store/export', $this->token . $url);
		$data['delete'] = $this->compatibility->link($this->module_path . '/store/delete', $this->token . $url);

		$limit = $this->compatibility->paginationLimit(20);

		$data['locations'] = array();

		$filter_data = array(
			'filter_name'       => $filter_name,
			'filter_address'    => $filter_address,
			'filter_country_id' => $filter_country_id,
			'filter_store_id'   => $filter_store_id,
			'filter_status'     => $filter_status,
			/////
			'filter_central'     => $filter_central,
			/////
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $limit,
			'limit'             => $limit
		);

		$store_total = $this->module_model->getTotalStores($filter_data);

		$results = $this->module_model->getStores($filter_data);

		foreach ($results as $result) {
			$data['locations'][] = array(
				'storepickup_id' => $result['storepickup_id'],
				'name'           => $result['name'],
				'address'        => $result['address'],
				'city'           => $result['city'],
				'zone'           => $result['zone'],
				'country'        => $result['country'],
				'cost'           => $this->currency->format($result['cost'], $this->config->get('config_currency')),
				'sort_order'     => $result['sort_order'],
				'status'         => $result['status'],
				////
				'central'         => $result['central'],
				////
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'           => $this->compatibility->link($this->module_path . '/store/edit', $this->token . '&storepickup_id=' . $result['storepickup_id'] . $url)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (is_file(DIR_DOWNLOAD . 'temp-storepickup-map.csv')) {
			$data['process'] = $this->language->get('text_process');
		} else {
			$data['process'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('page')));

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_id'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.storepickup_id' . $url);
		$data['sort_name'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.name' . $url);
		$data['sort_city'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.city' . $url);
		$data['sort_cost'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.cost' . $url);
		$data['sort_sort_order'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.sort_order' . $url);
		$data['sort_status'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.status' . $url);
		/////
		$data['sort_central'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.central' . $url);
		////
		$data['sort_date_added'] = $this->compatibility->link($this->module_path . '/store', $this->token . '&sort=s.date_added' . $url);

		$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('sort', 'order')));

		$data['pagination'] = $this->compatibility->pagination(
			$store_total,
			$page,
			$limit,
			$this->compatibility->link($this->module_path . '/store', $this->token . $url . '&page={page}')
		);

		$data['results'] = $this->compatibility->paginationText($page, $limit, $store_total);

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['filter_name'] = $filter_name;
		$data['filter_address'] = $filter_address;
		$data['filter_country_id'] = $filter_country_id;
		$data['filter_store_id'] = $filter_store_id;
		$data['filter_status'] = $filter_status;
		/////
		$data['filter_central'] = $filter_central;
		/////
		$data['filter_date_added'] = $filter_date_added;

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['stores'] = $this->compatibility->getStores();

		$data['links'] = $this->getManageLinks();

		$data['module_path'] = $this->module_path;

		$data['token'] = $this->token;

		foreach ($this->compatibility->getChildren() as $key => $child) {
			$data[$key] = ($key == 'header') ? $this->compatibility->jquery($child) : $child;
		}

		$this->response->setOutput($this->compatibility->view($this->module_path . '/store_list', $data));
	}

	private function getForm() {
		$this->document->addScript('https://maps.googleapis.com/maps/api/js?key=' . $this->config->get($this->config->get('spm_module_name') . '_apikey'));

		$this->compatibility->loadStyles(str_replace($this->module_type . '_', '', $this->module_name));

		$data = array_merge(array(), $this->language_data);

		$data['text_form'] = !isset($this->request->get['storepickup_id']) ? $this->language->get('text_add') : sprintf($this->language->get('text_edit'), $this->request->get['storepickup_id']);

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

		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}

		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}

		$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array('sort', 'order', 'page')));

		if (!isset($this->request->get['storepickup_id'])) {
			$data['action'] = $this->compatibility->link($this->module_path . '/store/add', $this->token . $url);
			$data['delete'] = '';
		} else {
			$data['action'] = $this->compatibility->link($this->module_path . '/store/edit', $this->token . '&storepickup_id=' . $this->request->get['storepickup_id'] . $url);
			$data['delete'] = $this->compatibility->link($this->module_path . '/store/delete', $this->token . '&storepickup_id=' . $this->request->get['storepickup_id'] . $url);
		}

		$data['cancel'] = $this->compatibility->link($this->module_path . '/store', $this->token . $url);

		if (isset($this->request->get['storepickup_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$store_info = $this->module_model->getStore($this->request->get['storepickup_id']);
		}

		if (isset($this->request->get['storepickup_id'])) {
			$data['storepickup_id'] = $this->request->get['storepickup_id'];
		} else {
			$data['storepickup_id'] = 0;
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($store_info)) {
			$data['name'] = $store_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($store_info)) {
			$data['email'] = $store_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['address'])) {
			$data['address'] = $this->request->post['address'];
		} elseif (!empty($store_info)) {
			$data['address'] = $store_info['address'];
		} else {
			$data['address'] = '';
		}

		if (isset($this->request->post['city'])) {
			$data['city'] = $this->request->post['city'];
		} elseif (!empty($store_info)) {
			$data['city'] = $store_info['city'];
		} else {
			$data['city'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($store_info)) {
			$data['country_id'] = $store_info['country_id'];
		} else {
			$data['country_id'] = '';
		}

		if (isset($this->request->post['zone_id'])) {
			$data['zone_id'] = $this->request->post['zone_id'];
		} elseif (!empty($store_info)) {
			$data['zone_id'] = $store_info['zone_id'];
		} else {
			$data['zone_id'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($store_info)) {
			$data['telephone'] = $store_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['cost'])) {
			$data['cost'] = $this->request->post['cost'];
		} elseif (!empty($store_info)) {
			$data['cost'] = $store_info['cost'];
		} else {
			$data['cost'] = '';
		}

		if (isset($this->request->post['icon'])) {
			$data['icon'] = $this->request->post['icon'];
		} elseif (!empty($store_info)) {
			$data['icon'] = $store_info['icon'];
		} else {
			$data['icon'] = '';
		}

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize($this->compatibility->getNoImage(), 40, 40);

		if (isset($this->request->post['icon']) && is_file(DIR_IMAGE . $this->request->post['icon'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['icon'], 40, 40);
		} elseif (!empty($store_info) && is_file(DIR_IMAGE . $store_info['icon'])) {
			$data['thumb'] = $this->model_tool_image->resize($store_info['icon'], 40, 40);
		} else {
			$data['thumb'] = '';
		}

		if (isset($this->request->post['store_id'])) {
			$data['store_id'] = $this->request->post['store_id'];
		} elseif (!empty($store_info)) {
			$data['store_id'] = $store_info['store_id'];
		} else {
			$data['store_id'] = 0;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($store_info)) {
			$data['sort_order'] = $store_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($store_info)) {
			$data['status'] = $store_info['status'];
		} else {
			$data['status'] = true;
		}
		///////
		if (isset($this->request->post['central'])) {
			$data['central'] = $this->request->post['central'];
		} elseif (!empty($store_info)) {
			$data['central'] = $store_info['central'];
		} else {
			$data['central'] = true;
		}
		//////

		if (isset($this->request->post['latitude'])) {
			$data['latitude'] = $this->request->post['latitude'];
		} elseif (!empty($store_info)) {
			$data['latitude'] = $store_info['latitude'];
		} else {
			$data['latitude'] = '';
		}

		if (isset($this->request->post['longitude'])) {
			$data['longitude'] = $this->request->post['longitude'];
		} elseif (!empty($store_info)) {
			$data['longitude'] = $store_info['longitude'];
		} else {
			$data['longitude'] = '';
		}

		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['stores'] = $this->compatibility->getStores();
		$data['languages'] = $this->compatibility->getLanguages();

		$data['module_path'] = $this->module_path;

		$data['token'] = $this->token;

		foreach ($this->compatibility->getChildren() as $key => $child) {
			$data[$key] = ($key == 'header') ? $this->compatibility->jquery($child) : $child;
		}

		$this->response->setOutput($this->compatibility->view($this->module_path . '/store_form', $data));
	}

	public function status() {
		$json = array();

		if (!$this->user->hasPermission('modify', $this->module_path)) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (isset($this->request->post['status'])) {
			foreach ($this->request->post['status'] as $storepickup_id => $status) {
				$this->module_model->statusStore($storepickup_id, $status);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function export() {
		if ($this->user->hasPermission('modify', $this->module_path)) {
			$this->load->model('localisation/country');

			$countries = array();

			foreach ($this->model_localisation_country->getCountries() as $country) {
				$countries[$country['country_id']] = $country;
			}

			$this->load->model('localisation/zone');

			$zones = array();

			foreach ($this->model_localisation_zone->getZones() as $zone) {
				$zones[$zone['zone_id']] = $zone;
			}
////////
			$heading = array('Store Name', 'Email', 'Telephone', 'Address', 'City', 'Country', 'Zone', 'Latitude', 'Longitude', 'Cost', 'Icon', 'Store ID (0 - Default)', 'Sort Order', 'Status (1 - Enabled, 0 - Disabled)', 'Central (1 - Enabled, 0 - Disabled)');

			$output = implode(self::SEPARATOR_CSV, array_map(array($this, 'add_quote'), $heading)) . "\n";

			$results = $this->module_model->getStores();

			foreach ($results as $result) {
				$row = array();

				$row[] = str_replace('"', '', $result['name']);
				$row[] = $result['email'];
				$row[] = $result['telephone'];
				$row[] = $result['address'];
				$row[] = $result['city'];
				$row[] = isset($countries[$result['country_id']]) ? $countries[$result['country_id']]['name'] : '';
				$row[] = isset($zones[$result['zone_id']]) ? $zones[$result['zone_id']]['name'] : '';
				$row[] = $result['latitude'];
				$row[] = $result['longitude'];
				$row[] = $result['cost'];
				$row[] = $result['icon'];
				$row[] = $result['store_id'];
				$row[] = $result['sort_order'];
				$row[] = $result['status'];
				$row[] = $result['central'];

				$output .= implode(self::SEPARATOR_CSV, array_map(array($this, 'add_quote'), $row)) . "\n";
			}

			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename="storepickup_' . date('Y-m-d_H-i-s') . '_backup.csv"');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->response->setOutput($output);
		} else {
			$this->session->data['warning'] = $this->language->get('error_permission');

			$url = $this->compatibility->getParams($this->request->get, array_merge((array)$this->default_filter, array()));

			$this->compatibility->redirect($this->compatibility->link($this->module_path . '/store', $this->token . $url));
		}
	}

	public function import() {
		$json = array();

		if (!$this->user->hasPermission('modify', $this->module_path)) {
			$json['error'] = $this->language->get('error_permission');
		}

		$file = DIR_DOWNLOAD . 'temp-storepickup-map.csv';

		if (!$json) {
			if (isset($this->request->files['file']['name'])) {
				if (isset($this->request->get['reset']) && is_file($file)) {
					unlink($file);
				}

				if (!empty($this->request->files['file']['name'])) {
					if (substr($this->request->files['file']['name'], -4) != '.csv') {
						$json['error'] = $this->language->get('error_filetype');
					}

					if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
						$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
					}
				} else {
					$json['error'] = $this->language->get('error_upload');
				}

				if (!$json) {
					move_uploaded_file($this->request->files['file']['tmp_name'], $file);
				}
			}
		}

		if (!$json) {
			if (is_file($file)) {
				$handle = fopen($file, 'r');
			} else {
				$handle = false;
			}

			if ($handle) {
				$headings = fgetcsv($handle, 0, self::SEPARATOR_CSV);

				if (sizeof($headings) < 14) {
					$json['error'] = $this->language->get('error_heading');
				} else {
					if (isset($this->request->get['skip'])) {
						$skip = (int)$this->request->get['skip'];
					} else {
						$skip = 0;
					}

					$this->load->model('localisation/country');

					$countries = array();

					foreach ($this->model_localisation_country->getCountries() as $country) {
						$countries[$country['name']] = $country;
					}

					$this->load->model('localisation/zone');

					$zones = array();

					foreach ($this->model_localisation_zone->getZones() as $zone) {
						$zones[$zone['name']] = $zone;
					}

					$i = 0;

					while (($row = fgetcsv($handle, 0, self::SEPARATOR_CSV)) !== false) {
						if ($i++ < $skip) continue;

						$post_data = array(
							'name'       => $row[0],
							'email'      => $row[1],
							'telephone'  => $row[2],
							'address'    => $row[3],
							'city'       => $row[4],
							'country_id' => isset($countries[$row[5]]) ? $countries[$row[5]]['country_id'] : 0,
							'zone_id'    => isset($zones[$row[6]]) ? $zones[$row[6]]['zone_id'] : 0,
							'latitude'   => $row[7],
							'longitude'  => $row[8],
							'cost'       => $row[9],
							'icon'       => $row[10],
							'store_id'   => $row[11],
							'sort_order' => $row[12],
							'status'     => $row[13],
							'central'     => $row[14]
						);

						$this->module_model->addStore($post_data);
					}

					$this->session->data['success'] = $this->language->get('text_success');

					$json['success'] = true;
				}

				fclose($handle);

				unlink($file);
			} else {
				$json['error'] = $this->language->get('error_empty');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function country() {
		$json = array();

		if (isset($this->request->get['country_id'])) {
			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

			if ($country_info) {
				$this->load->model('localisation/zone');

				foreach ($this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']) as $zone) {
					$json['zone'][] = array(
						'zone_id' => $zone['zone_id'],
						'name'    => $zone['name']
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', $this->module_path)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 128)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['address']) < 2) || (utf8_strlen($this->request->post['address']) > 255)) {
			$this->error['address'] = $this->language->get('error_address');
		}

		if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
			$this->error['city'] = $this->language->get('error_city');
		}

		if ((utf8_strlen($this->request->post['latitude']) < 1) || (utf8_strlen($this->request->post['longitude']) < 1)) {
			$this->error['warning'] = $this->language->get('error_coordinate');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_required');
		}

		return (!$this->error) ? true : false;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', $this->module_path)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return (!$this->error) ? true : false;
	}

	private function add_quote($value) {
		return sprintf('"%s"', $value);
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