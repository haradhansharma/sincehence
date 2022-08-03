<?php
class ControllerExtensionDashboardAffiliate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/affiliate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_affiliate', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/affiliate', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/affiliate', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_affiliate_width'])) {
			$data['dashboard_affiliate_width'] = $this->request->post['dashboard_affiliate_width'];
		} else {
			$data['dashboard_affiliate_width'] = $this->config->get('dashboard_affiliate_width');
		}

		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
		if (isset($this->request->post['dashboard_affiliate_status'])) {
			$data['dashboard_affiliate_status'] = $this->request->post['dashboard_affiliate_status'];
		} else {
			$data['dashboard_affiliate_status'] = $this->config->get('dashboard_affiliate_status');
		}

		if (isset($this->request->post['dashboard_affiliate_sort_order'])) {
			$data['dashboard_affiliate_sort_order'] = $this->request->post['dashboard_affiliate_sort_order'];
		} else {
			$data['dashboard_affiliate_sort_order'] = $this->config->get('dashboard_affiliate_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/affiliate_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/affiliate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
		
	public function dashboard() {
		$this->load->language('extension/dashboard/affiliate');

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('customer/customer');

		$customer_total  = $this->model_customer_customer->getTotalCustomers(array('filter_status' => '1'));
		$affiliate_total = $this->getTotalAffiliates();
		
		$affiliate_total = round($affiliate_total,1);

		if ($customer_total && $affiliate_total) {
			$data['percentage'] = round(($affiliate_total / $customer_total) * 100);
		} else {
			$data['percentage'] = 0;
		}

		if ($affiliate_total > 1000000000000) {
			$data['total'] = round($affiliate_total / 1000000000000, 1) . 'T';
		} elseif ($affiliate_total > 1000000000) {
			$data['total'] = round($affiliate_total / 1000000000, 1) . 'B';
		} elseif ($affiliate_total > 1000000) {
			$data['total'] = round($affiliate_total / 1000000, 1) . 'M';
		} elseif ($affiliate_total > 1000) {
			$data['total'] = round($affiliate_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $affiliate_total;
		}

		$data['affiliate'] = $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'], true);

		return $this->load->view('extension/dashboard/affiliate_info', $data);
	}

	protected function getTotalAffiliates($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_affiliate";
		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
