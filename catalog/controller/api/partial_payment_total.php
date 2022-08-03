<?php
/* Partial Payment Total for OpenCart v.3.0.x 
 *
* @version 3.3.0
 * @date 16/08/2020
 * @author Kestutis Banisauskas
 * @Smartechas
 */
class ControllerApiPartialPaymentTotal extends Controller {
	public function index() {
		$this->load->language('api/partial_payment_total');
		$this->load->language('extension/total/partial_payment_total');

		// Delete past partial payment in case there is an error
		unset($this->session->data['partial_payment_total']);

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$this->load->model('extension/total/partial_payment_total');

			if (isset($this->request->post['partial_payment_total'])) {
				$partial = $this->request->post['partial_payment_total'];
			} else {
				$partial = false;
			}
			/*
				// Order Totals
				$this->load->model('setting/extension');

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;

				// Because __call can not keep var references so we put them into an array.
				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);
			
				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);
						
						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						
					}
				}

			foreach ($total_data['totals'] as $totals) {
				if ($totals['code'] == 'total') {
					$total = $totals['value'];
				}
			}
			*/
			if (isset($this->session->data['vouchers'])) {
				$voucher = $this->session->data['vouchers'];
			} else {
				$voucher = '';
			}

			if (isset($this->session->data['customer']['customer_id'])) {
				$customer_id = $this->session->data['customer']['customer_id'];
			} else {
				$customer_id = '';
			}

			if ($partial === '1' && $customer_id && !$voucher) {
				$this->session->data['partial_payment_total'] = $this->request->post['partial_payment_total'];

				$json['success'] = $this->language->get('text_success_partial');

			} else if ($partial === '0') {
				$this->session->data['partial_payment_total'] = $this->request->post['partial_payment_total'];

				$json['success'] = $this->language->get('text_success');
				
			} else if ($voucher) {
				$json['error'] = $this->language->get('error_partial_voucher');
			} else {
				$json['error'] = $this->language->get('error_partial_payment');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
