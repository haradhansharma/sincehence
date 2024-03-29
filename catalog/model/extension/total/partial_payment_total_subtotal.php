<?php
/* Partial Payment Total for OpenCart v.3.0.x 
 *
 * @version 3.2.0
 * @date 15/03/2018
 * @author Kestutis Banisauskas
 * @Smartechas
 */
class ModelExtensionTotalPartialPaymentTotalSubtotal extends Model {
	public function getTotal($total) {
		if (isset($this->session->data['partial_payment_total'])) {
			$data['partial_payment_total'] = $this->session->data['partial_payment_total'];
		} else {
			$data['partial_payment_total'] = false;
		}
			$data['amount'] = max(0, $total['total']);
			
			if ($data['partial_payment_total']) {
			$this->load->language('extension/total/partial_payment_total');
			
				$percents = explode(',', $this->config->get('total_partial_payment_total_percent'));
				$data['total_partial_payment_total_percent'] = '';
				
				$this->load->model('checkout/order');
        		

				foreach ($percents as $percent) {
					
					$data = explode(':', $percent);
					$data['amount'] = max(0, $total['total']);
					
					if ($data[0] >= $data['amount']) {
						if (isset($data[1])) {
							$data['total_partial_payment_total_percent'] = $data[1];
						} 
						
						break;
				}
			}
		
			$data['total_partial_payment_total_percent'] = isset($data['total_partial_payment_total_percent']) ? $data['total_partial_payment_total_percent'] : '';
			$data['partial_amount'] = $data['amount']*$data['total_partial_payment_total_percent']/100;
			if ($data['partial_amount'] != '0') {
			foreach ($this->cart->getProducts() as $product) {
			$data['partial_amount_subtotal'] = $data['partial_amount']/($this->tax->getTax($data['partial_amount'], $product['tax_class_id'])/$data['partial_amount']+1);
			}
			} else {
				$data['partial_amount_tax'] = '';
			}
			
			if ($data['partial_amount'] != '0') {
				$total['totals'][] = array(
					'code'       => 'partial_payment_total_subtotal',
					'title'      => $this->language->get('text_partial_payment_total_subtotal'),
					'value'      => $data['partial_amount_subtotal'],
					'sort_order' => $this->config->get('total_partial_payment_total_subtotal_sort_order')
				);

			}
		}
	}
}