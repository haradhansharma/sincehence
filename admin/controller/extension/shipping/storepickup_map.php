<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
class ControllerExtensionShippingStorePickupMap extends Controller {
	const MODULE_VERSION = "v1.6";

	private $module_type = '';
	private $module_name = '';
	private $module_path = '';
	private $module_model = '';
	private $module_fields = '';

	private $compatibility = null;

	private $language_data = array();
	private $error = array();
	private $token = '';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->config('storepickup_map');

		$this->module_type = $this->config->get('spm_module_type');
		$this->module_name = $this->config->get('spm_module_name');
		$this->module_path = $this->config->get('spm_module_path');
		$this->module_fields = $this->config->get('spm_fields');

		$this->load->model($this->module_path);

		$this->module_model = $this->{$this->config->get('spm_module_model')};

		$this->install();

		$this->compatibility = $this->module_model->compatibility();

		$this->language_data = $this->language->load($this->module_path);

		$token_name = $this->compatibility->getAdminTokenName();
		$this->token = $token_name . '=' . $this->compatibility->getSessionValue($token_name);
	}

	public function install() {
		$this->module_model->install();
	}

	public function uninstall() {
		$this->module_model->uninstall();

		$this->compatibility->deleteSetting($this->module_name);
	}

	public function index() {
		$this->compatibility->loadStyles(str_replace($this->module_type . '_', '', $this->module_name));

		$data = array_merge(array(), $this->language_data);

		$data['heading_title'] = $this->language->get('heading_title') . ' ' . self::MODULE_VERSION;

		$this->document->setTitle($data['heading_title']);

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = (int)$this->request->get['filter_store_id'];
		} else {
			$filter_store_id = 0;
		}

		if (isset($this->request->get['filter_tab_show'])) {
			$filter_tab_show = (string)$this->request->get['filter_tab_show'];
		} else {
			$filter_tab_show = '';
		}

		$url = '&filter_store_id=' . (int)$filter_store_id;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSetting()) {
			$this->compatibility->editSetting($this->module_name, $this->request->post[$this->module_name], $filter_store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->compatibility->redirect($this->compatibility->link($this->module_path, $this->token . $url));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error'])) {
			$data['error'] = $this->error['error'];
		} else {
			$data['error'] = array();
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('tool/image');

		$data['action'] = $this->compatibility->link($this->module_path, $this->token . $url);

		if (version_compare(VERSION, '2.3') < 0) {
			$data['cancel'] = $this->compatibility->link('extension/shipping', $this->token . $url);
		} elseif (version_compare(VERSION, '3') < 0) {
			$data['cancel'] = $this->compatibility->link('extension/extension', $this->token . $url);
		} else {
			$data['cancel'] = $this->compatibility->link('marketplace/extension', $this->token . $url);
		}

		$placeholder = $this->compatibility->getNoImage();

		$data['placeholder'] = $this->model_tool_image->resize($placeholder, 100, 100);

		$config_store = $this->compatibility->getSetting($this->module_name, $filter_store_id);

		foreach ((array)$this->module_fields as $key => $value) {
			if (isset($this->request->post[$this->module_name][$key]) || isset($config_store[$key])) {
				$data[$key] = isset($this->request->post[$this->module_name][$key]) ? $this->request->post[$this->module_name][$key] : $config_store[$key];

				if (is_array($value['default'])) {
					foreach ($value['default'] as $key2 => $value2) {
						if (!isset($data[$key][$key2])) {
							$data[$key][$key2] = $value2;
						}
					}
				}
			} else {
				if ($value['decode']) {
					if (is_array($value['default'])) {
						$_tmp = $value['default'];

						foreach ($_tmp as $key2 => $value2) {
							$value['default'][$key2] = base64_decode($value2);
						}
					} else {
						$value['default'] = base64_decode($value['default']);
					}
				}

				$data[$key] = $value['default'];
			}
		}

		$data['bGljZW5zZV9kb21haW4'] = base64_encode($this->compatibility->getDomain());

		$categories = array();

		if (isset($data['categories'])) {
			$this->load->model('catalog/category');

			foreach ($data['categories'] as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$categories[] = array(
						'category_id' => $category_info['category_id'],
						'name'        => $category_info['name']
					);
				}
			}
		}

		$data['categories'] = $categories;

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['stores'] = $this->compatibility->getStores($this->module_path);
		$data['languages'] = $this->compatibility->getLanguages();

		$data['filter_store_id'] = $filter_store_id;
		$data['filter_tab_show'] = $filter_tab_show;

		$data['links'] = $this->getManageLinks();

		$data['module_name'] = $this->module_name;
		$data['module_path'] = $this->module_path;

		$data['token'] = $this->token;

		foreach ($this->compatibility->getChildren() as $key => $child) {
			$data[$key] = ($key == 'header') ? $this->compatibility->jquery($child) : $child;
		}

		$this->response->setOutput($this->compatibility->view($this->module_path, $data));
	}

	private function validateSetting() {
		if (!$this->user->hasPermission('modify', $this->module_path)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->request->post[$this->module_name]) {
			foreach ((array)$this->module_fields as $key => $value) {
				if ($value['required']) {
					if (isset($this->request->post[$this->module_name][$key]) && is_array($this->request->post[$this->module_name][$key])) {
						foreach ($this->request->post[$this->module_name][$key] as $key2 => $setting) {
							if (is_array($setting)) {
								foreach ($setting as $key3 => $value2) {
									if (is_array($value2)) {
										foreach ($value2 as $value3) {
											if (!$value3) {
												$this->error['error'][$key] = $this->language->get('error_' . $key);
											}
										}
									} elseif (!isset($value2) || empty($value2)) {
										$this->error['error'][$key] = $this->language->get('error_' . $key);
									}
								}
							} elseif (!isset($setting) || $setting === null || $setting == '') {
								$this->error['error'][$key] = $this->language->get('error_' . $key);
							}
						}
					} elseif (!isset($this->request->post[$this->module_name][$key]) || $this->request->post[$this->module_name][$key] === null || $this->request->post[$this->module_name][$key] == '') {
						$this->error['error'][$key] = $this->language->get('error_' . $key);
					}
				}
			}

			if (isset($this->error['error'])) {
				$this->error['warning'] = $this->language->get('error_required');
			}
		} else {
			$this->error['warning'] = $this->language->get('error_module');
		}

		return (!$this->error) ? true : false;
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