<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
class ModelExtensionShippingStorePickupMap extends Model {
	protected $compatibility = null;

	/*
	  Set compatibility for all versions of Opencart
	*/
	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->config('storepickup_map');

		include_once DIR_SYSTEM . 'library/vendors/storepickup_map/compatibility.php';

		$this->compatibility = new OVCompatibility_13($registry);
		$this->compatibility->setApp('front');
	}

	/*
	  Return compatibility instance
	*/
	public function compatibility() {
		return $this->compatibility;
	}

	/*
	  Shipping Method
	*/
	public function getQuote($address) {
		$method_data = array();

		$settings = $this->compatibility->getSetting($this->config->get('spm_module_name'), $this->config->get('config_store_id'));

		if (isset($settings['status']) && $settings['status']) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$settings['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if (!$settings['geo_zone_id']) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}

			if (isset($settings['categories']) && $settings['categories']) {
				$found = false;

				foreach ($this->cart->getProducts() as $product) {
					$categories = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product['product_id'] . "'");

					foreach ($categories->rows as $category) {
						if (in_array($category['category_id'], (array)$settings['categories'])) {
							$found = true;

							break;
						}
					}
				}

				if (!$found) {
					$status = false;
				}
			}

			if ($status) {
				if ($settings['pickup_list_status'] && $address) {
					$coords = $this->getCoordinatesCustomer($address['address_1'] . ' ' . $address['city'] . ' ' . $address['zone'] . ' ' . $address['country'], $settings['apikey']);
				} else {
					$coords = array('lng' => 0, 'lat' => 0);
				}

				if ($coords) {
					$this->load->language($this->config->get('spm_module_path'));

					$units = (array)$this->config->get('spm_units');
					$unit = $units[$settings['distance_unit']];

					$quote_data = array();
					$countries = array();
					$zones = array();
					$i = 0;

					$filter_data = array(
						'longitude' => $coords['lng'],
						'latitude'  => $coords['lat']
					);
					/////sharma replace
				// 	$results = $this->getStores($filter_data);
					$dis_res = $this->getStores($filter_data);
					foreach ($dis_res as $dis_re) {
					$dis = 0;    
					$dis = round($dis_re['distance'] * $unit[1], 2);
					
                    if($dis < 11 ){
                    $results = $this->getStores($filter_data);
					$ss = ''; 
					if(isset($this->session->data['centeral_serving'])){
					unset($this->session->data['centeral_serving']);
					}
                    }elseif($this->cart->hasStock()){
                     $results = $this->getStores($filter_data);
					$ss = '';  
					if(isset($this->session->data['centeral_serving'])){
					unset($this->session->data['centeral_serving']);
					}
                    }elseif(!$this->cart->hasStock() && $this->config->get('config_stock_checkout')){
                    $results = $this->getCentralStores($filter_data);
					$ss = ' will serve!';
					$this->session->data['centeral_serving'] = 'Yes';
                    }else{
					$results = $this->getCentralStores($filter_data);
					$ss = ' will serve!';
					$this->session->data['centeral_serving'] = 'Yes';
                    }
					}
                    
                    
                    /////sharma replace

					foreach ($results as $result) {
						$distance = '';
						$coordinate = '';

						if ($settings['distance_status']) {
							$distance = sprintf($this->language->get('text_distance'), round($result['distance'] * $unit[1], 2), $unit[0]);
						}

						if ($settings['coordinate_status']) {
							$coordinate = sprintf($this->language->get('text_coordinate'), $result['latitude'], $result['longitude']);
						}
						

						if ((!$settings['limit']) || ($settings['limit'] && $i++ < $settings['limit'])) {
							$quote_data['storepickup_map_' . $result['storepickup_id']] = array(
								'storepickup_id'     => $result['storepickup_id'],
								'code'               => 'storepickup_map.storepickup_map_' . $result['storepickup_id'],
								'title'              => $result['name'] . $ss . ' - ' . $result['address'] . ', ' . $result['city'] . ', ' . $result['country'] . $distance . $coordinate,
								'cost'               => $result['cost'],
								'tax_class_id'       => $settings['tax_class_id'],
								'text'               => $this->currency->format($this->tax->calculate($result['cost'], $settings['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
							);
						}

						$countries[$result['country_id']] = '<option value="' . $result['country_id'] . '">' . $result['country'] . '</option>';
						$zones[$result['zone_id']] = '<option value="' . $result['zone_id'] . '">' . $result['zone'] . '</option>';
					}

					if ($quote_data) {
						if ($settings['filter_status']) {
							if (sizeof($countries) > 1) {
								$col = 3;
								$zones = array();
							} else {
								$col = 5;
							}

							$filter = '<div class="form-group" id="spm_filter">';
							$filter .= ' <div class="row">';

							if (sizeof($countries) > 1) {
								$filter .= '<div class="col-sm-' . $col . '">';
								$filter .= '  <select name="spm_country_id" class="form-control" data-select-type="country">';
								$filter .= '    <option value="">' . $this->language->get('text_select_country') . '</option>';
								$filter .=      implode('', $countries);
								$filter .= '  </select>';
								$filter .= '</div>';
							}

							$filter .= '   <div class="col-sm-' . $col . '">';
							$filter .= '     <select name="spm_zone_id" class="form-control" data-select-type="zone">';
							$filter .= '       <option value="">' . $this->language->get('text_select_zone') . '</option>';

							if ($zones) {
								$filter .= implode('', $zones);
							}

							$filter .= '     </select>';
							$filter .= '   </div>';
							$filter .= '   <div class="col-sm-' . $col . '">';
							$filter .= '     <select name="spm_city" class="form-control" data-select-type="city">';
							$filter .= '       <option value="">' . $this->language->get('text_select_city') . '</option>';
							$filter .= '     </select>';
							$filter .= '   </div>';
							$filter .= ' </div>';
							$filter .= '</div>';
						} else {
							$filter = '';
						}

						if ($settings['map_status']) {
							$map = sprintf($this->language->get('text_map'), $this->compatibility->link($this->config->get('spm_module_path') . '/map'));
						} else {
							$map = '';
						}

						$method_data = array(
							'code'       => 'storepickup_map',
							'title'      => (isset($settings['name'][$this->config->get('config_language_id')]) ? $settings['name'][$this->config->get('config_language_id')] : $this->language->get('text_title')) . $map . $filter,
							'quote'      => $quote_data,
							'sort_order' => $settings['sort_order'],
							'error'      => false
						);
					}
				}
			}
		}

		return $method_data;
	}

	/*
	  Stores
	*/
	public function getStores($data = array()) {
		$sql = "SELECT s.*, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = s.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = s.zone_id) AS zone, (3959 * ACOS(COS(RADIANS(" . $this->db->escape($data['latitude']) . ")) * COS(RADIANS(s.latitude)) * COS(RADIANS(s.longitude) - RADIANS(" . $this->db->escape($data['longitude']) . ")) + SIN(RADIANS(" . $this->db->escape($data['latitude']) . ")) * SIN(RADIANS(s.latitude)))) AS distance FROM " . DB_PREFIX . "spm_store s WHERE s.status = '1' AND s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_country'])) {
			$sql .= " AND s.country_id = '" . (int)$data['filter_country'] . "' ";
		}

		if (!empty($data['filter_zone'])) {
			$sql .= " AND s.zone_id = '" . (int)$data['filter_zone'] . "' ";
		}

		if (!empty($data['filter_city'])) {
			$sql .= " AND LOWER(s.city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_city'])) . "%' ";
		}

		$sql .= " ORDER BY " . ($this->config->get($this->config->get('spm_module_name') . '_distance_status') ? 'distance' : 's.sort_order') . " ASC";

		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	//////sharma
	public function getCentralStores($data = array()) {
		$sql = "SELECT s.*, (SELECT name FROM " . DB_PREFIX . "country c WHERE c.country_id = s.country_id) AS country, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = s.zone_id) AS zone, (3959 * ACOS(COS(RADIANS(" . $this->db->escape($data['latitude']) . ")) * COS(RADIANS(s.latitude)) * COS(RADIANS(s.longitude) - RADIANS(" . $this->db->escape($data['longitude']) . ")) + SIN(RADIANS(" . $this->db->escape($data['latitude']) . ")) * SIN(RADIANS(s.latitude)))) AS distance FROM " . DB_PREFIX . "spm_store s WHERE s.status = '1' AND s.central = '1' AND s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_country'])) {
			$sql .= " AND s.country_id = '" . (int)$data['filter_country'] . "' ";
		}

		if (!empty($data['filter_zone'])) {
			$sql .= " AND s.zone_id = '" . (int)$data['filter_zone'] . "' ";
		}

		if (!empty($data['filter_city'])) {
			$sql .= " AND LOWER(s.city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_city'])) . "%' ";
		}

		$sql .= " ORDER BY " . ($this->config->get($this->config->get('spm_module_name') . '_distance_status') ? 'distance' : 's.sort_order') . " ASC";

		if (!empty($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
/////sharma
	public function getLocalisations($data = array()) {
		$sql = "SELECT s.city, s.zone_id, (SELECT name FROM " . DB_PREFIX . "zone z WHERE z.zone_id = s.zone_id) AS zone FROM " . DB_PREFIX . "spm_store s WHERE s.status = '1' AND s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_country'])) {
			$sql .= " AND s.country_id = '" . (int)$this->request->get['filter_country'] . "'";
		}

		if (!empty($data['filter_zone'])) {
			$sql .= " AND s.zone_id = '" . (int)$this->request->get['filter_zone'] . "'";
		}

		if (!empty($data['filter_country'])) {
			$sql .= " GROUP BY s.zone_id";
		} elseif (!empty($data['filter_zone'])) {
			$sql .= " GROUP BY s.city";
		}

		$sql .= " ORDER BY s.sort_order ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/*
	  Other
	*/
	public function getCoordinatesCustomer($address, $key) {
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $key;

		if (extension_loaded('curl')) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$result = curl_exec($ch);

			curl_close($ch);
		} elseif (ini_get('allow_url_fopen')) {
			$result = file_get_contents($url);
		}

		if (isset($result)) {
			$response = json_decode($result, true);

			if (isset($response['status']) && $response['status'] == 'OK') {
				$geog = (isset($response['results'][0])) ? $response['results'][0]['geometry']['location'] : $response['results']['geometry']['location'];

				return array('lng' => $geog['lng'], 'lat' => $geog['lat']);
			} elseif (isset($response['error_message'])) {
				$this->log->write('Google Maps STATUS: ' . $response['status'] . ' - ' . $response['error_message']);
			} else {
				$this->log->write('Google Maps STATUS: Unspecified Error. Customer Address: ' . $address);
			}
		}

		return array();
	}
}