<?php
class ControllerExtensionPaymentCredit extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['continue'] = $this->url->link('checkout/success');
		return $this->load->view('extension/payment/credit', $data);
	}

	public function confirm() {
		$json = array();

		if ($this->session->data['payment_method']['code'] == 'credit') {
			$this->load->model('checkout/order');
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_credit_order_status_id'));
			$json['redirect'] = $this->url->link('checkout/success');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
}