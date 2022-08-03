<?php
class ControllerExtensionPaymentPayoneer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/payoneer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_payoneer', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['payoneer'])) {
			$data['error_payoneer'] = $this->error['payoneer'];
		} else {
			$data['error_payoneer'] = array();
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/payoneer', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/payoneer', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		$this->load->model('localisation/language');

		$data['payment_payoneer'] = array();

		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (isset($this->request->post['payment_payoneer_payoneer' . $language['language_id']])) {
				$data['payment_payoneer_payoneer'][$language['language_id']] = $this->request->post['payment_payoneer_payoneer' . $language['language_id']];
			} else {
				$data['payment_payoneer_payoneer'][$language['language_id']] = $this->config->get('payment_payoneer_payoneer' . $language['language_id']);
			}
		}

		$data['languages'] = $languages;

		if (isset($this->request->post['payment_payoneer_total'])) {
			$data['payment_payoneer_total'] = $this->request->post['payment_payoneer_total'];
		} else {
			$data['payment_payoneer_total'] = $this->config->get('payment_payoneer_total');
		}

		if (isset($this->request->post['payment_payoneer_order_status_id'])) {
			$data['payment_payoneer_order_status_id'] = $this->request->post['payment_payoneer_order_status_id'];
		} else {
			$data['payment_payoneer_order_status_id'] = $this->config->get('payment_payoneer_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_payoneer_geo_zone_id'])) {
			$data['payment_payoneer_geo_zone_id'] = $this->request->post['payment_payoneer_geo_zone_id'];
		} else {
			$data['payment_payoneer_geo_zone_id'] = $this->config->get('payment_payoneer_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_payoneer_status'])) {
			$data['payment_payoneer_status'] = $this->request->post['payment_payoneer_status'];
		} else {
			$data['payment_payoneer_status'] = $this->config->get('payment_payoneer_status');
		}

		if (isset($this->request->post['payment_payoneer_sort_order'])) {
			$data['payment_payoneer_sort_order'] = $this->request->post['payment_payoneer_sort_order'];
		} else {
			$data['payment_payoneer_sort_order'] = $this->config->get('payment_payoneer_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/payoneer', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/payoneer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (empty($this->request->post['payment_payoneer_payoneer' . $language['language_id']])) {
				$this->error['payoneer'][$language['language_id']] = $this->language->get('error_payoneer');
			}
		}

		return !$this->error;
	}
	public function install(){ 
	    
	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_email'");
	if (!$check->num_rows) {
	    $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `payment_email` varchar(100) NOT NULL");
	} 
	$check2 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'payment_trxID'");
	if (!$check2->num_rows) {
	    $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `payment_trxID` varchar(100) NOT NULL");
	}
	    
	}
}