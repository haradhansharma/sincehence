<?php
class ControllerExtensionReportCalculatorReport extends Controller {
	public function index() {
		$this->load->language('extension/report/calculator_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_calculator_report', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true));
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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/report/calculator_report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/calculator_report', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_calculator_report_status'])) {
			$data['report_calculator_report_status'] = $this->request->post['report_calculator_report_status'];
		} else {
			$data['report_calculator_report_status'] = $this->config->get('report_calculator_report_status');
		}

		if (isset($this->request->post['report_calculator_report_sort_order'])) {
			$data['report_calculator_report_sort_order'] = $this->request->post['report_calculator_report_sort_order'];
		} else {
			$data['report_calculator_report_sort_order'] = $this->config->get('report_calculator_report_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/calculator_report_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/calculator_report')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
		
	public function report() {
		$this->load->language('extension/report/calculator_report');

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

		if (isset($this->request->get['filter_calculator'])) {
			$filter_calculator = $this->request->get['filter_calculator'];
		} else {
			$filter_calculator = '';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->load->model('extension/report/calculator_report');
		
		$data['calculators'] = array();

		$filter_data = array(
			'filter_date_start'	=> $filter_date_start,
			'filter_date_end'	=> $filter_date_end,
			'filter_calculator'	=> $filter_calculator,
			'start'				=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'				=> $this->config->get('config_limit_admin')
		);

		$calculator_total = $this->model_extension_report_calculator_report->getTotalCalculators($filter_data);

		$results = $this->model_extension_report_calculator_report->getCalulators($filter_data);

		foreach ($results as $result) {
			$data['calculators'][] = array(
			    'cd_id'       => $result['cd_id'],
				'calculator'       => $result['your_name'],
				'email'          => $result['your_email'],
				'mobile'          => $result['mobile'],
				'target_grandopening'         => $result['target_grandopening'],
				'total'          => $this->currency->format($result['total_capital'], $this->config->get('config_currency')),
				'targetarea'           => $result['targetarea'],
				'product_arraival'           => $result['product_arraival']
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_calculator'])) {
			$url .= '&filter_calculator=' . urlencode($this->request->get['filter_calculator']);
		}

		$pagination = new Pagination();
		$pagination->total = $calculator_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=calculator_report' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($calculator_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($calculator_total - $this->config->get('config_limit_admin'))) ? $calculator_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $calculator_total, ceil($calculator_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_calculator'] = $filter_calculator;

		return $this->load->view('extension/report/calculator_report_info', $data);
	}
public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_calculator']) ) {
			if (isset($this->request->get['filter_calculator'])) {
				$filter_calculator = $this->request->get['filter_calculator'];
			} else {
				$filter_calculator = '';
			}
			
			$this->load->model('extension/report/calculator_report');

			$filter_data = array(
				'filter_calculator'      => $filter_calculator,
				'start'            => 0,
				'limit'            => 5
			);

			$results = $this->model_extension_report_calculator_report->getCustomers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'cd_id'       => $result['cd_id'],
					'name'              =>strip_tags(html_entity_decode($result['your_name'], ENT_QUOTES, 'UTF-8'))
	
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}