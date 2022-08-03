<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
class ControllerExtensionShippingStorePickupMap extends Controller {
	private $module_type = '';
	private $module_name = '';
	private $module_path = '';
	private $module_model = '';

	private $compatibility = null;

	private $language_data = array();
	private $settings = array();
	private $error = array();

	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->config('storepickup_map');

		$this->module_type = $this->config->get('spm_module_type');
		$this->module_name = $this->config->get('spm_module_name');
		$this->module_path = $this->config->get('spm_module_path');

		$this->load->model($this->module_path);

		$this->module_model = $this->{$this->config->get('spm_module_model')};

		$this->compatibility = $this->module_model->compatibility();

		$this->language_data = $this->language->load($this->module_path);

		$this->settings = $this->compatibility->getSetting($this->module_name, $this->config->get('config_store_id'));
	}

	public function index($setting = array()) {
		if (isset($this->settings['status']) && $this->settings['status']) {
			$data = array_merge(array(), $this->language_data);

			if (isset($this->session->data['shipping_address']) && $this->session->data['shipping_address']) {
				$address = $this->session->data['shipping_address']['address_1'] . ' ' . $this->session->data['shipping_address']['city'] . ' ' . $this->session->data['shipping_address']['zone'] . ' ' . $this->session->data['shipping_address']['country'];
			} elseif (isset($this->session->data['guest']['shipping']) && $this->session->data['guest']['shipping']) {
				$address = $this->session->data['guest']['shipping']['address_1'] . ' ' . $this->session->data['guest']['shipping']['city'] . ' ' . $this->session->data['guest']['shipping']['zone'] . ' ' . $this->session->data['guest']['shipping']['country'];
			} else {
				$address = '';
			}

			if ($this->settings['pickup_list_status'] && $address) {
				$coords = $this->module_model->getCoordinatesCustomer($address, $this->settings['apikey']);
			} else {
				$coords = array('lng' => 0, 'lat' => 0);
			}

			if ($coords) {
				$units = (array)$this->config->get('spm_units');
				$unit = $units[$this->settings['distance_unit']];

				$data['stores'] = array();

				$filter_data = array(
					'longitude' => $coords['lng'],
					'latitude'  => $coords['lat']
				);

				$results = $this->module_model->getStores($filter_data);

				foreach ($results as $result) {
					$distance = '';
					$coordinate = '';

					if ($this->settings['distance_status']) {
						$distance = sprintf($this->language->get('text_distance'), round($result['distance'] * $unit[1], 2), $unit[0]);
					}

					if ($this->settings['coordinate_status']) {
						$coordinate = sprintf($this->language->get('text_coordinate'), $result['latitude'], $result['longitude']);
					}

					if ($result['icon'] && file_exists(DIR_IMAGE . $result['icon'])) {
						if ($this->compatibility->isHttps()) {
							$icon = HTTPS_SERVER . 'image/' . $result['icon'];
						} else {
							$icon = HTTP_SERVER . 'image/' . $result['icon'];
						}
					} else {
						$icon = '';
					}

					$data['stores'][] = array(
						'storepickup_id'     => $result['storepickup_id'],
						'code'               => 'storepickup_map.storepickup_map_' . $result['storepickup_id'],
						'title'              => $result['name'] . ' - ' . $result['address'] . ', ' . $result['city'] . ', ' . $result['country'] . $distance . $coordinate,
						'cost'               => $this->currency->format($this->tax->calculate($result['cost'], $this->settings['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
						'name'               => $result['name'],
						'address'            => $result['address'],
						'city'               => $result['city'],
						'zone'               => $result['zone'],
						'country'            => $result['country'],
						'telephone'          => $result['telephone'],
						'icon'               => $icon,
						'latitude'           => $result['latitude'],
						'longitude'          => $result['longitude'],
						'customer_latitude'  => $coords['lat'],
						'customer_longitude' => $coords['lng']
					);
				}

				$data['address'] = $address;

				$data['apikey'] = $this->settings['apikey'];
				$data['cost_status'] = $this->settings['cost_status'];
				$data['filter_status'] = $this->settings['filter_status'];
				$data['map_status'] = $this->settings['map_status'];
				$data['map_width'] = $this->settings['map_width'] ? $this->settings['map_width'] : '400px';
				$data['map_height'] = $this->settings['map_height'] ? $this->settings['map_height'] : '300px';

				$data['module_path'] = $this->module_path;

				return $this->compatibility->view($this->module_path, $data);
			}
		}
	}

	public function filter() {
		if (isset($this->settings['status']) && $this->settings['status']) {
			$json = array();

			if (isset($this->request->get['filter_country'])) {
				$filter_country = $this->request->get['filter_country'];
			} else {
				$filter_country = '';
			}

			if (isset($this->request->get['filter_zone'])) {
				$filter_zone = $this->request->get['filter_zone'];
			} else {
				$filter_zone = '';
			}

			$filter_data = array(
				'filter_country' => $filter_country,
				'filter_zone'    => $filter_zone
			);

			$results = $this->module_model->getLocalisations($filter_data);

			if ($results) {
				if ($filter_country) {
					foreach ($results as $result) {
						if ($result['zone']) {
							$json['item'][] = array(
								'id'   => $result['zone_id'],
								'name' => $result['zone']
							);
						}
					}
				} elseif ($filter_zone) {
					foreach ($results as $result) {
						if ($result['city']) {
							$json['item'][] = array(
								'id'   => $result['city'],
								'name' => $result['city']
							);
						}
					}
				}
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}

	public function stores() {
		if (isset($this->settings['status']) && $this->settings['status']) {
			$json['store'] = array();

			if (isset($this->request->get['address'])) {
				$address = $this->request->get['address'];
			} else {
				$address = '';
			}

			if ($this->settings['pickup_list_status'] && $address) {
				$coords = $this->module_model->getCoordinatesCustomer($address, $this->settings['apikey']);
			} else {
				$coords = array('lng' => 0, 'lat' => 0);
			}

			if ($coords) {
				$units = (array)$this->config->get('spm_units');
				$unit = $units[$this->settings['distance_unit']];

				if (isset($this->request->get['filter_country'])) {
					$filter_country = $this->request->get['filter_country'];
				} else {
					$filter_country = '';
				}

				if (isset($this->request->get['filter_zone'])) {
					$filter_zone = $this->request->get['filter_zone'];
				} else {
					$filter_zone = '';
				}

				if (isset($this->request->get['filter_city'])) {
					$filter_city = $this->request->get['filter_city'];
				} else {
					$filter_city = '';
				}

				$filter_data = array(
					'filter_country' => $filter_country,
					'filter_zone'    => $filter_zone,
					'filter_city'    => $filter_city,
					'longitude'      => $coords['lng'],
					'latitude'       => $coords['lat'],
					'limit'          => $this->settings['limit']
				);

				$results = $this->module_model->getStores($filter_data);

				foreach ($results as $result) {
					$distance = '';
					$coordinate = '';

					if ($this->settings['distance_status']) {
						$distance = sprintf($this->language->get('text_distance'), round($result['distance'] * $unit[1], 2), $unit[0]);
					}

					if ($this->settings['coordinate_status']) {
						$coordinate = sprintf($this->language->get('text_coordinate'), $result['latitude'], $result['longitude']);
					}
					$json['store'][] = array(
						'storepickup_id' => $result['storepickup_id'],
						'code'           => 'storepickup_map.storepickup_map_' . $result['storepickup_id'],
						'title'          => $result['name'] . ' - ' . $result['address'] . ', ' . $result['city'] . ', ' . $result['country'] . $distance . $coordinate,
						'cost'           => $this->currency->format($this->tax->calculate($result['cost'], $this->settings['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
						'name'           => $result['name'],
						'address'        => $result['address'],
						'city'           => $result['city'],
						'zone'           => $result['zone'],
						'country'        => $result['country'],
						'telephone'      => $result['telephone'],
						'latitude'       => $result['latitude'],
						'longitude'      => $result['longitude']
					);

				}
			}

			if (!$json['store']) {
				$json['error'] = $this->language->get('text_empty');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}