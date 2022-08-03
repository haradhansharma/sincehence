<?php
class ControllerExtensionPaymentbKashWallet extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/bkash_wallet');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_bkash_wallet', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['bkash'])) {
			$data['error_bkash'] = $this->error['bkash'];
		} else {
			$data['error_bkash'] = array();
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
			'href' => $this->url->link('extension/payment/bkash_wallet', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/bkash_wallet', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		$this->load->model('localisation/language');

		$data['payment_bkash_wallet'] = array();

		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (isset($this->request->post['payment_bkash_wallet_bkash' . $language['language_id']])) {
				$data['payment_bkash_wallet_bkash'][$language['language_id']] = $this->request->post['payment_bkash_wallet_bkash' . $language['language_id']];
			} else {
				$data['payment_bkash_wallet_bkash'][$language['language_id']] = $this->config->get('payment_bkash_wallet_bkash' . $language['language_id']);
			}
		}

		$data['languages'] = $languages;

		if (isset($this->request->post['payment_bkash_wallet_total'])) {
			$data['payment_bkash_wallet_total'] = $this->request->post['payment_bkash_wallet_total'];
		} else {
			$data['payment_bkash_wallet_total'] = $this->config->get('payment_bkash_wallet_total');
		}

		if (isset($this->request->post['payment_bkash_wallet_order_status_id'])) {
			$data['payment_bkash_wallet_order_status_id'] = $this->request->post['payment_bkash_wallet_order_status_id'];
		} else {
			$data['payment_bkash_wallet_order_status_id'] = $this->config->get('payment_bkash_wallet_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_bkash_wallet_geo_zone_id'])) {
			$data['payment_bkash_wallet_geo_zone_id'] = $this->request->post['payment_bkash_wallet_geo_zone_id'];
		} else {
			$data['payment_bkash_wallet_geo_zone_id'] = $this->config->get('payment_bkash_wallet_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_bkash_wallet_status'])) {
			$data['payment_bkash_wallet_status'] = $this->request->post['payment_bkash_wallet_status'];
		} else {
			$data['payment_bkash_wallet_status'] = $this->config->get('payment_bkash_wallet_status');
		}

		if (isset($this->request->post['payment_bkash_wallet_sort_order'])) {
			$data['payment_bkash_wallet_sort_order'] = $this->request->post['payment_bkash_wallet_sort_order'];
		} else {
			$data['payment_bkash_wallet_sort_order'] = $this->config->get('payment_bkash_wallet_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/bkash_wallet', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/bkash_wallet')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (empty($this->request->post['payment_bkash_wallet_bkash' . $language['language_id']])) {
				$this->error['bkash'][$language['language_id']] = $this->language->get('error_bkash');
			}
		}

		return !$this->error;
	}
}