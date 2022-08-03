<?php
class ControllerExtensionTotalCredit extends Controller {
	public function index($setting) {

		$this->load->language('extension/total/credit');

		$balance = $this->currency->format($this->tax->calculate($this->customer->getBalance(), '', $this->config->get('config_tax')), $this->session->data['currency']);
		$max_use = $this->config->get('total_credit_minimum_amount_to_disverse');
		$pay_after = $this->config->get('total_credit_pay_after_days');		
	
     if ($balance && $this->config->get('total_credit_status')) {
		$data['heading_title'] = sprintf($this->language->get('heading_title'), $balance);

		$data['entry_credit'] = sprintf($this->language->get('entry_credit'), $max_use);

		
		$data['credit'] = $this->currency->format($this->tax->calculate($max_use, '', $this->config->get('config_tax')), $this->session->data['currency']);

		


		

			return $this->load->view('extension/total/credit', $data);
		}
	}

	public function credit() { 
		

		$json = array();

		$this->load->language('extension/total/credit');

		$balance = $this->customer->getBalance();
		$max_use = $this->config->get('total_credit_minimum_amount_to_disverse');
		$pay_after = $this->config->get('total_credit_pay_after_days');

		

		if (empty($this->request->post['credit'])) {
			$json['error'] = $this->language->get('error_credit');
		}

		if ($this->request->post['credit'] > $balance) {
			$json['error'] = sprintf($this->language->get('error_credits'), $this->request->post['credit']);
		}

		if ($this->request->post['credit'] < $max_use) {
			$json['error'] = sprintf($this->language->get('error_maximum'), $max_use);
		}

		if (!$json) {
			$this->session->data['credit'] = abs($this->request->post['credit']);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['redirect'])) {
				$json['redirect'] = $this->url->link($this->request->post['redirect']);
			} else {
				$json['redirect'] = $this->url->link('checkout/cart');	
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
