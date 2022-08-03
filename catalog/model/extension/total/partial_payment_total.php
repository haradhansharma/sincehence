<?php
/* Partial Payment Total for OpenCart v.3.0.x 
 *
* @version 3.3.0
 * @date 16/08/2020
 * @author Kestutis Banisauskas
 * @Smartechas
 */
class ModelExtensionTotalPartialPaymentTotal extends Model {
	public function getTotal($total) {
		$this->load->language('extension/total/partial_payment_total');
		
		if (isset($this->session->data['partial_payment_total'])) {
			$data['partial_payment_total'] = $this->session->data['partial_payment_total'];
		} else {
			$data['partial_payment_total'] = '';
		}
			$data['amount'] = max(0, $total['total']);
			
			/* Condition for customer group */
			$status = true;
		if ($status && $this->config->get('total_partial_payment_total_customer_group')) {
			if (isset($this->session->data['guest']) && in_array(0, $this->config->get('total_partial_payment_total_customer_group'))) {
				$status = true;
			} elseif ($this->customer->isLogged() && $this->session->data['customer_id']) {
				$this->load->model('account/customer');

				$customer = $this->model_account_customer->getCustomer($this->session->data['customer_id']);

				if (in_array($customer['customer_group_id'], $this->config->get('total_partial_payment_total_customer_group'))) {
					$this->session->data['customer_group_id'] = $customer['customer_group_id'];

					$status = true;
				} else {
					$status = false;
				}
			} else {
				$status = false;
			}
		}
		
		/* Condition for categories and products */
		if ($status && ($this->config->get('total_partial_payment_total_category') || count(explode(',', $this->config->get('total_partial_payment_total_xproducts'))) > 0)) {
			$allowed_categories = $this->config->get('total_partial_payment_total_category');

			$xproducts = explode(',', $this->config->get('total_partial_payment_total_xproducts'));

			$cart_products = $this->cart->getProducts();

			foreach ($cart_products as $cart_product) {
				$product = array();

				if ($xproducts && in_array($cart_product['product_id'], $xproducts)) {
					$status = false;
					break;
				} else {
					$product = $this->db->query("SELECT GROUP_CONCAT(`category_id`) as `categories` FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$cart_product['product_id'] . "'");
					$product = $product->row;

					$product = explode(',', $product['categories']);

					if ($allowed_categories){

					if ($product && count(array_diff($product, $allowed_categories)) > 0) {
						$status = false;
						break;
						}
					}
				}
			}
		}
		
		if ($data['partial_payment_total'] && $status) {
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
			$total['totals'][] = array(
				'code'       => 'partial_payment_total',
				'title'      => sprintf($this->language->get('text_partial_payment_total'), $data['total_partial_payment_total_percent']),
				'value'      => $data['partial_amount'],
				'sort_order' => $this->config->get('total_partial_payment_total_sort_order')
				
			);
	
			$total['total'] = $data['partial_amount'];
			}
		}
	}
	 
	public function addPartialPayment($total, $partial_amount, $order_id, $order_status_id, $customer_id) {
		$this->load->language('extension/total/partial_payment_total');
		$this->load->language('extension/total/credit');
			
		if (isset($this->session->data['partial_payment_total']) && $this->session->data['partial_payment_total']) {
			$pending_total = $total - $partial_amount;
			$transaction_amount = $partial_amount - $total;
			
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_id)) . "', amount = '" . (float)$transaction_amount . "', date_added = NOW()");
				
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET   total = '" . (float)$total . "', pending_total = '" . (float)$pending_total . "', date_added = NOW() WHERE  order_id = '" . (int)$order_id . "'");
		} 
	}
	
	public function editPartialPayment($total, $partial_amount, $order_id, $order_status_id, $customer_id) {
		$this->load->language('extension/total/partial_payment_total');
		$this->load->language('extension/total/credit');
		
		if (isset($this->session->data['partial_payment_total']) && $this->session->data['partial_payment_total']) {
			$pending_total = $total - $partial_amount;
			$transaction_amount = $partial_amount - $total;
			$query_transaction = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$order_id . "'");
			if ($query_transaction->num_rows) {
				$this->db->query("UPDATE `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$customer_id . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_id)) . "', amount = '" . (float)$transaction_amount . "', date_added = NOW() WHERE  order_id = '" . (int)$order_id . "'");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_id)) . "', amount = '" . (float)$transaction_amount . "', date_added = NOW()");
			}
			
		} else {
			$pending_total = 0;
			$this->db->query("DELETE FROM`" . DB_PREFIX . "customer_transaction` WHERE  order_id = '" . (int)$order_id . "'");
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET   total = '" . (float)$total . "', pending_total = '" . (float)$pending_total . "', date_added = NOW() WHERE  order_id = '" . (int)$order_id . "'");
	}
	
	public function successPartialPayment($order_id) {
		if ($this->customer->isLogged()) {
        
			$this->load->language('extension/total/credit');
			$this->load->language('extension/total/partial_payment_total');
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($order_id);

			$query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = " . (int)$order_info['order_id'] . " AND `code` = 'total'");

			if ($query->num_rows) {
				$data['total'] = $query->row['value'];
			} else {
				$data['total'] = '';
			}

			$data['order_id'] = $order_info['order_id'];
			//////
			$percents = explode(',', $this->config->get('total_partial_payment_total_percent'));

			//$status_percent = '';
			$total = $data['total'];
			foreach ($percents as $percent) {
				$data = explode(':', $percent);

				if ($data[0] >= $total) {
					if (isset($data[1])) {
						$data['total_partial_payment_total_percent'] = $data[1];
						$status_percent = true;
					} else {
						$status_percent = false;
					}
				}
			}
			/////        
			$data['total_partial_payment_total_percent'] = isset($data['total_partial_payment_total_percent']) ? $data['total_partial_payment_total_percent'] : '';
			$data['partial_amount'] = $total*$data['total_partial_payment_total_percent']/100;
			$data['total_partial_payment_total_total'] = $this->config->get('total_partial_payment_total_total');

			if (isset($this->session->data['partial_payment_total'])) {
				$data['partial_payment_total'] = $this->session->data['partial_payment_total'];
			} else {
				$data['partial_payment_total'] = '';
			}

			if ($data['partial_payment_total']) {
				$order_total['value'] = $data['partial_amount'] - $total;
			} else {
				$order_total['value'] = '';
			}


			$pending_order_id = isset($this->session->data['pending_order_id']) ? $this->session->data['pending_order_id'] : '';
			$pending_amount = isset($this->session->data['pending_amount']) ? $this->session->data['pending_amount'] : '';

			$totals = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$pending_order_id . "' AND code = 'total'");
			if ($totals->num_rows) {
				$total_value = $totals->row['value'];
			} else {
				$total_value = '';                
			}

			$partial_totals = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$pending_order_id . "' AND code = 'partial_payment_total'");
			if ($partial_totals->num_rows) {
				$partial_total = $partial_totals->row['value'];
			} else {
				$partial_total = '';                
			}

			if ($data['partial_payment_total']) {
				$pending_total  = $total - $data['partial_amount'];
			} else {
				$pending_total  = '0';
			}


			$pending_customer_ids = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id = '" . (int)$pending_order_id . "'");
			if ($pending_customer_ids->num_rows) {
				$pending_customer_id = $pending_customer_ids->row['customer_id'];
			} else {
				$pending_customer_id = '';                
			}

			$pending_total_topay = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$pending_order_id . "'");
			if ($pending_total_topay->num_rows) {
				$pending_total_pay = (float)$pending_total_topay->row['pending_total'];
			} else {
				$pending_total_pay = '0';                
			}
			if (isset($this->session->data['vouchers'])) {
				$data['vouchers'] = $this->session->data['vouchers'];
			} else {
				$data['vouchers'] = array();
			}

			if (!$data['vouchers']) {
				$pending_total_paid  = (float)$pending_total_pay - (float)$pending_amount;
			} else {
				$pending_total_paid  = '0';
			}

			if (!empty($this->config->get('total_partial_payment_total_order_status'))) {
				$order_status_id = $this->config->get('total_partial_payment_total_order_status');	
			} else {
				$order_status_id = $this->config->get('config_order_status_id');
			}

			if (($pending_amount == 0) && ($data['partial_payment_total'])) {

				$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$order_info['customer_id'] . "', order_id = '" . (int)$order_info['order_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_order_id'), (int)$order_info['order_id'])) . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET   total = '" . (float)$total . "', pending_total = '" . (float)$pending_total . "', date_added = NOW() WHERE  order_id = '" . (int)$order_info['order_id'] . "'");

			} elseif  ($pending_amount) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_transaction` SET customer_id = '" . (int)$pending_customer_id . "', order_id = '" . (int)$order_info['order_id'] . "', description = '" . $this->db->escape(sprintf($this->language->get('text_for'), (int)$pending_order_id)) . "', amount = '" . (float)$pending_amount . "', date_added = NOW()");

				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total_value . "', pending_total = '" . (float)$pending_total_paid . "', date_added = NOW() WHERE  order_id = '" . (int)$pending_order_id . "'");

				// Update the DB with the new statuses
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$pending_order_id . "'");
			}

			unset($this->session->data['pending_order_id']);
			unset($this->session->data['pending_amount']);
			unset($this->session->data['partial_payment_total']);
		}
	}
}