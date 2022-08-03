<?php
class ControllerExtensionDashboardCartReport extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/cart_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('dashboard_cart_report', $this->request->post);

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
			'href' => $this->url->link('extension/dashboard/cart_report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/dashboard/cart_report', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true);

		if (isset($this->request->post['dashboard_cart_report_width'])) {
			$data['dashboard_cart_report_width'] = $this->request->post['dashboard_cart_report_width'];
		} else {
			$data['dashboard_cart_report_width'] = $this->config->get('dashboard_cart_report_width');
		}
	
		$data['columns'] = array();
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}
				
		if (isset($this->request->post['dashboard_cart_report_status'])) {
			$data['dashboard_cart_report_status'] = $this->request->post['dashboard_cart_report_status'];
		} else {
			$data['dashboard_cart_report_status'] = $this->config->get('dashboard_cart_report_status');
		}

		if (isset($this->request->post['dashboard_cart_report_sort_order'])) {
			$data['dashboard_cart_report_sort_order'] = $this->request->post['dashboard_cart_report_sort_order'];
		} else {
			$data['dashboard_cart_report_sort_order'] = $this->config->get('dashboard_cart_report_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/dashboard/cart_report_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/cart_report')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		$this->load->language('extension/dashboard/cart_report');

		$data['user_token'] = $this->session->data['user_token'];

		// Total Orders
		$this->load->model('extension/dashboard/cart_report');

		// Customers cart_report
		$cart_report_total = $this->model_extension_dashboard_cart_report->getTotalCart();

		if ($cart_report_total > 1000000000000) {
			$data['total'] = round($cart_report_total / 1000000000000, 1) . 'T';
		} elseif ($cart_report_total > 1000000000) {
			$data['total'] = round($cart_report_total / 1000000000, 1) . 'B';
		} elseif ($cart_report_total > 1000000) {
			$data['total'] = round($cart_report_total / 1000000, 1) . 'M';
		} elseif ($cart_report_total > 1000) {
			$data['total'] = round($cart_report_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $cart_report_total;
		}
		
		$cart_report_pro = $this->model_extension_dashboard_cart_report->getTotalpro();

		if ($cart_report_pro > 1000000000000) {
			$data['products'] = round($cart_report_pro / 1000000000000, 1) . 'T';
		} elseif ($cart_report_pro > 1000000000) {
			$data['products'] = round($cart_report_pro / 1000000000, 1) . 'B';
		} elseif ($cart_report_pro > 1000000) {
			$data['products'] = round($cart_report_pro / 1000000, 1) . 'M';
		} elseif ($cart_report_pro > 1000) {
			$data['products'] = round($cart_report_pro / 1000, 1) . 'K';
		} else {
			$data['products'] = $cart_report_pro;
		}
        $code = 'cart_report';
		$data['cart_report'] = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token']. '&code=' . $code, true);

		return $this->load->view('extension/dashboard/cart_report_info', $data);
	}
}
