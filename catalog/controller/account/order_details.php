<?php
class ControllerAccountorderdetails extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order_details', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/order_details');

		$this->document->setTitle($this->language->get('heading_title'));
		
			$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		

		$this->load->model('account/customer');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/order_details',  true)
		);
		
					if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
	

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}
			if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_id'] = $customer_info['customer_id'];
		} else {
			$data['customer_id'] = '';
		}


	if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}



		/////
// 		$this->load->model('account/order');

// 		$data['retailer_id'] = array();
// 		$result=$this->model_account_order->getretailerid();
// 		foreach ($result as $result) {
// 			$data['retailer_id'] = $result['customer_group_id'];
			
// 		}
// 		$retailer_id=$data['retailer_id'];
		
		
// 		$data['distributer_id'] = array();
// 		$result=$this->model_account_order->getdistributerid();
// 		 foreach ($result as $result) {
// 			$data['distributer_id'] = $result['customer_group_id'];
			
// 		}
// 		$distributer_id=$data['distributer_id'];




//       $customer_group_id=$data['customer_group_id'];
       
       
       
       
        $email=$this->customer->getEmail();
        $data['orders'] = array();
		$this->load->model('account/order');

		
		
        $order_total = $this->model_account_order->getTotalOrderess($email);
		$results = $this->model_account_order->getOrderded($email, ($page - 1) * 10, 10);

		foreach ($results as $result) {
		    
		    	 ////check spmstore  sharma start
		    	 
				$order_id = $result['order_id'];
				$new_order = $result['new_order'];
				
				$dis_paid_order = $this->model_account_order->getNewo($new_order);
                // if($dis_paid_order) {
                    foreach ($dis_paid_order as $dis_paid_order) {
					$dis_paid_order_data[] = array(
						'order_id' => $dis_paid_order['order_id']
					);
                   if(!empty($dis_paid_order['order_id'])){
                       $data['dis_paid_order'] = $dis_paid_order['order_id'];
                   }else{
                       $data['dis_paid_order'] = 0;
                   }
                   
                }
                // }	
                
                
				$emaildata = array();
		        $email_for_orders = $this->model_account_order->getOrderem($order_id);
				foreach ($email_for_orders as $email_for_order) {
					$emaildata[] = array(
						'email' => $email_for_order['email']
					);
					$cusemail = $email_for_order['email'];
					
				

	
					
			
				$spm_store_data = array();
				$spm_store_names = $this->model_account_order->getRestor($cusemail);
				foreach ($spm_store_names as $spm_store_name) {
					$spm_store_data[] = array(
						's_name' => $spm_store_name['s_name']
					);
					


			    
		    	}
		    		if(!empty($spm_store_name['s_name'])){
					    $from_store = $spm_store_name['s_name'];
					}else{
					   $from_store = $result['store_name'] ;
					}
					
		  ////check spmstore  sharma end  	




			$data['orders'][] = array(
			    
			    's_name' => $from_store,
			    'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
				'shop_name'   => $result['shop_name'],
				'new_order'   => $result['new_order'],
				'pre_order'   => $result['pre_order'],
				'new_cus'   => $result['new_cus'],
			    'shop_email'     => $result['shop_email'], 
				'shop_telephone'     => $result['shop_telephone'], 
				'shop_address'     => $result['shop_address'], 
				'shop_city'     => $result['shop_city'],
				'cost'     => $result['cost'],
				
				'order_id'     => $result['order_id'],
				'store_name'     => $result['store_name'],
				'store_url'     => $result['store_url'],
				'customer_id'     => $result['customer_id'],
				'customer_group_id'     => $result['customer_group_id'],
				'firstname'     => $result['firstname'],
				'lastname'     => $result['lastname'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'customer_email'     => $result['customer_email'],
				'customer_telephone'     => $result['customer_telephone'],
				'payment_firstname'     => $result['payment_firstname'],
				'payment_lastname'     => $result['payment_lastname'],
				'payment_company'     => $result['payment_company'],
				'payment_address_1'     => $result['payment_address_1'],
				'payment_address_2'     => $result['payment_address_2'],
				'payment_city'     => $result['payment_city'],
				'payment_country_id'     => $result['payment_country_id'],
				'payment_zone'     => $result['payment_zone'],
				'payment_zone_id'     => $result['payment_zone_id'],
				'payment_method'     => $result['payment_method'],
				'payment_code'     => $result['payment_code'],
				'shipping_firstname'     => $result['shipping_firstname'],
				'shipping_lastname'     => $result['shipping_lastname'],
				'shipping_company'     => $result['shipping_company'],
				'shipping_address_1'     => $result['shipping_address_1'],
				'shipping_address_2'     => $result['shipping_address_2'],
				'shipping_city'     => $result['shipping_city'],
				'shipping_postcode'     => $result['shipping_postcode'],
				'shipping_country'     => $result['shipping_country'],
				'shipping_country_id'     => $result['shipping_country_id'],
				'shipping_zone'     => $result['shipping_zone'],
				'shipping_zone_id'     => $result['shipping_zone_id'],
				'shipping_method'     => $result['shipping_method'],
				'shipping_code'     => $result['shipping_code'],
				'comment'     => $result['comment'],
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				////partial payment sharma
				'pending_total'         => $this->currency->format($result['pending_total'], $result['currency_code'], $result['currency_value']),
				'pending_total_list'    => $result['pending_total'], 
				
				/////partial payment sharma
				
				'order_date'    => date($this->language->get('date_format_short'), strtotime($result['order_date'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'detailsinfo'       => $this->url->link('account/order_details/detailsinfo', 'order_id=' . $result['order_id'], true)
               
			);

		
		}
	}
		$data['this_customer']	= $this->customer->getId();	
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/order_details', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));
		
				////sharma for partial payment
		
			$this->load->language('account/order');
			$this->document->setTitle($this->language->get('heading_title'));
			$data['heading_title'] = $this->language->get('heading_title');

			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->language('extension/total/partial_payment_total');
            $data['text_payment_pending'] = $this->language->get('text_payment_pending');
            $data['button_send'] = $this->language->get('button_send');
            $data['text_request_payment'] = $this->language->get('text_request_payment');
			$data['text_sending'] = $this->language->get('text_sending');
			$data['error_email'] = $this->language->get('error_email');
			
			 $this->request->post['email'] = isset($this->request->post['email']) ? $this->request->post['email'] : '';
            $this->session->data['email'] = $this->request->post['email'];
		////sharma for partial payment

		$data['continue'] = $this->url->link('account/account', '', true);
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/order_details', $data));
		
	}

public function detailsinfo() {
		$this->load->language('account/order_details');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		}
		 else {
			$order_id = 0;
		}
		

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order_details/detailsinfo', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

			$this->load->language('account/order_details');

		$this->document->setTitle($this->language->get('heading_title'));



              $this->load->model('account/order');





		// here need to be edit for order info start

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id);

	
		
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/order_details',  true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_order'),
				'href' => $this->url->link('account/order_details/detailsinfo', 'order_id=' . $this->request->get['order_id'] ,  true)
			);


	
			if ($order_info['invoice_no']) {
				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['order_id'] = $this->request->get['order_id'];






	$data['invoice'] = $this->url->link('account/order_details/invoice','order_id=' . $this->request->get['order_id'] , true);
	$data['shipping'] = $this->url->link('account/order_details/shipping','order_id=' . $this->request->get['order_id'] , true);

			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['payment_method'] = $order_info['payment_method'];

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);

			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['shipping_method'] = $order_info['shipping_method'];

			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 110) . '..' : $value)
					);
				}

				$product_info = $this->model_catalog_product->getProduct($product['product_id']);

				if ($product_info) {
					$reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);
				} else {
					$reorder = '';
				}

				$data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'reorder'  => $reorder,
					'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
				);
			}


			// end

	
			// Totals
			$data['totals'] = array();

			$totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			$data['comment'] = nl2br($order_info['comment']);



			//order status
			
			$this->load->model('localisation/order_status');

			$order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);

			if ($order_status_info) {
				$data['order_status'] = $order_status_info['name'];
			} else {
				$data['order_status'] = '';
			}

			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
					foreach ($data['order_statuses'] as $dd) {
				     $data['ss']=array(
                    'name'     => $dd['name']
				     );
                    
                    if($data['ss']['name']=='Processing' || $data['ss']['name']=='Shipped' || $data['ss']['name']=='Canceled' || $data['ss']['name']=='Complete' || $data['ss']['name']=='Canceled Reversal' || $data['ss']['name']=='Refunded' || $data['ss']['name']=='Reversed' || $data['ss']['name']=='Chargeback' || $data['ss']['name']=='Pending' || $data['ss']['name']=='Processed' ){

				    
				 }
			}

			$data['order_status_id'] = $order_info['order_status_id'];

			



    $this->load->model('account/customer');

	
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}
			if (isset($this->request->post['customer_id'])) {
			$data['customer_id'] = $this->request->post['customer_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_id'] = $customer_info['customer_id'];
		} else {
			$data['customer_id'] = '';
		}


	if (isset($this->request->post['customer_group_id'])) {
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} elseif (!empty($customer_info)) {
			$data['customer_group_id'] = $customer_info['customer_group_id'];
		} else {
			$data['customer_group_id'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}



	
		/////
       $customer_group_id=$data['customer_group_id'];
       
       	$email=$data['email'];
     


    				 

		$this->load->model('account/order');

	

		$results = $this->model_account_order->getOrdersss($email,$order_id);

		foreach ($results as $result) {
		 

			$data['orders'][] = array(
				'shop_name'   => $result['shop_name'],
			    'shop_email'     => $result['shop_email'], 
				'shop_telephone'     => $result['shop_telephone'], 
				'shop_address'     => $result['shop_address'], 
				'shop_city'     => $result['shop_city'],
				'cost'     => $result['cost'],
				'order_date'     => $result['order_date'],
				'order_id'     => $result['order_id'],
				'store_name'     => $result['store_name'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'store_url'     => $result['store_url'],
				'customer_id'     => $result['customer_id'],
				'customer_group_id'     => $result['customer_group_id'],
				'firstname'     => $result['firstname'],
				'lastname'     => $result['lastname'],
				'customer_email'     => $result['customer_email'],
				'customer_telephone'     => $result['customer_telephone'],
				'payment_firstname'     => $result['payment_firstname'],
				'payment_lastname'     => $result['payment_lastname'],
				'payment_company'     => $result['payment_company'],
				'payment_address_1'     => $result['payment_address_1'],
				'payment_address_2'     => $result['payment_address_2'],
				'payment_city'     => $result['payment_city'],
				'payment_country_id'     => $result['payment_country_id'],
				'payment_zone'     => $result['payment_zone'],
				'payment_zone_id'     => $result['payment_zone_id'],
				'payment_method'     => $result['payment_method'],
				'payment_code'     => $result['payment_code'],
				'shipping_firstname'     => $result['shipping_firstname'],
				'shipping_lastname'     => $result['shipping_lastname'],
				'shipping_company'     => $result['shipping_company'],
				'shipping_address_1'     => $result['shipping_address_1'],
				'shipping_address_2'     => $result['shipping_address_2'],
				'shipping_city'     => $result['shipping_city'],
				'shipping_postcode'     => $result['shipping_postcode'],
				'shipping_country'     => $result['shipping_country'],
				'shipping_country_id'     => $result['shipping_country_id'],
				'shipping_zone'     => $result['shipping_zone'],
				'shipping_zone_id'     => $result['shipping_zone_id'],
				'shipping_method'     => $result['shipping_method'],
				'shipping_code'     => $result['shipping_code'],
				'comment'     => $result['comment'],
				'total'     => $result['total'],
				'date_modified'     => $result['date_modified']
				
                
			);
		}


// For history

	 if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('account/order');

		$results = $this->model_account_order->getOrderHistoriess($this->request->get['order_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}


   // end history

				// form submit system
             
		//
		             $this->request->server['REQUEST_METHOD'] == 'POST';
            if(empty($this->request->post['order_status_id'])){
            	$this->request->post['order_status_id']=null;
            }
            if(empty($this->request->post['notify'])){
            	$this->request->post['notify']=null;
            }

		if ($this->request->server['REQUEST_METHOD'] == 'POST')  {
			// $order_id = $data['order_id'];
			
			$this->model_account_order->editOrderstatus( $this->request->post,$order_id);

			$this->response->redirect($this->url->link('account/order_details/detailsinfo', 'order_id=' . $order_id , true));
		}



		$results = $this->model_account_order->getretailergroupid();

		foreach ($results as $result) {
			$data['retailer_group_id']= $result['customer_group_id'];

		}

 
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/detailsinfo', $data));
		
	}



		// invoice
	public function invoice() {
		
		$this->load->language('account/order');

		$data['title'] = $this->language->get('text_invoice_no');

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$this->load->model('account/order');
        $this->load->model('catalog/product');
		$this->load->model('setting/setting');

		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}

		foreach ($orders as $order_id) { 
			$order_info = $this->model_account_order->getOrder($order_id);
			if ($order_info) {
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
				
					
			////check spmstore  sharma start
				$emaildata = array();
		        $email_for_orders = $this->model_account_order->getOrderem($order_id);
				foreach ($email_for_orders as $email_for_order) {
					$emaildata[] = array(
						'email' => $email_for_order['email']
					);
					$cusemail = $email_for_order['email'];
				$spmdata[] = array();
				$spmstore_code = $this->model_account_order->getSpmcode($order_id,$cusemail);
				foreach ($spmstore_code as $spmstore_code) {
					$spmdata[] = array(
						'spmmethodcode' => $spmstore_code['spmmethodcode'],
						'shop_name' => $spmstore_code['shop_name'],
						'customer_email' => $spmstore_code['customer_email'],
						'shop_address' => $spmstore_code['shop_address'],
						'shop_email' => $spmstore_code['shop_email'],
						'shop_telephone' => $spmstore_code['shop_telephone']
					);

			}
			if(empty($spmstore_code['spmmethodcode'])){
			   continue ;
			   }
			if(empty($spmstore_code['shop_name'])){
			   $spmstore_code['shop_name'] = $this->config->get('config_name') ;
			   }
			if(empty($spmstore_code['shop_email'])){
			    $spmstore_code['shop_email'] = $this->config->get('config_email') ;
		    }
	}
			

		////check spmstore  sharma end
		
		
		
		
		
		
				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
				} 
			/////sharma for spm address start
				elseif($store_info && ($order_info['shipping_code'] = $spmstore_code['spmmethodcode'])){
				    $store_address= $spmstore_code['shop_address'];
				    $store_email= $spmstore_code['shop_email'];
				    $store_telephone= $spmstore_code['shop_telephone'];
				    
				}
	         /////sharma for spm address start
				
				
				else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
				}

				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}',
					'{email}'
				);

				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country'],
					'email'   => $order_info['email']
				);
				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_account_order->getOrderProducts($order_id);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

							if ($upload_info) {
								$value = $upload_info['name'];
							} else {
								$value = '';
							}
						}

						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);
					}

					$product_data[] = array(
						'name'     => $product['name'],
						'model'    => $product['model'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}

				$voucher_data = array();

				$vouchers = $this->model_account_order->getOrderVouchers($order_id);

				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}
				


				$total_data = array();

				$totals = $this->model_account_order->getOrderTotals($order_id);

				foreach ($totals as $total) {
					$total_data[] = array(
						'title' => $total['title'],
						'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}

	////replaced else data array for shipping sharma
	if( $order_info['shipping_code'] = $spmstore_code['spmmethodcode'] ){
	    
	    				$data['orders'][] = array(
					'order_id'	       => $order_id,
					'invoice_no'       => $invoice_no,
					'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'       => $spmstore_code['shop_name'],
					'store_url'        => rtrim($order_info['store_url'], '/'),
					'store_address'    => $spmstore_code['shop_address'],
					'store_email'      => $spmstore_code['shop_email'],
					'store_telephone'  => $spmstore_code['shop_telephone'],
					'store_fax'        => $store_fax,
					'email'            => $order_info['email'],
					'telephone'        => $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'shipping_method'  => $order_info['shipping_method'],
					'payment_address'  => $payment_address,
					'payment_method'   => $order_info['payment_method'],
					'product'          => $product_data,
					'voucher'          => $voucher_data,
					'total'            => $total_data,
					'comment'          => nl2br($order_info['comment'])
				);
	    
	}else{
				$data['orders'][] = array(
					'order_id'	       => $order_id,
					'invoice_no'       => $invoice_no,
					'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'       => $order_info['store_name'],
					'store_url'        => rtrim($order_info['store_url'], '/'),
					'store_address'    => nl2br($store_address),
					'store_email'      => $store_email,
					'store_telephone'  => $store_telephone,
					'store_fax'        => $store_fax,
					'email'            => $order_info['email'],
					'telephone'        => $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'shipping_method'  => $order_info['shipping_method'],
					'payment_address'  => $payment_address,
					'payment_method'   => $order_info['payment_method'],
					'product'          => $product_data,
					'voucher'          => $voucher_data,
					'total'            => $total_data,
					'comment'          => nl2br($order_info['comment'])
				);
	}
				
				
			}
			
		
		}
		


		$this->response->setOutput($this->load->view('account/invoice', $data));
	}

public function shipping() {
	$this->load->language('account/order');

		$data['title'] = $this->language->get('text_shipping');
		

		if ($this->request->server['HTTPS']) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}

		$data['direction'] = $this->language->get('direction');
		$data['lang'] = $this->language->get('code');

		$this->load->model('account/order');

		$this->load->model('catalog/product');

		$this->load->model('setting/setting');

		$data['orders'] = array();

		$orders = array();

		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}

		foreach ($orders as $order_id) {
			$order_info = $this->model_account_order->getOrder($order_id);

			// Make sure there is a shipping method
			if ($order_info && $order_info['shipping_code']) {
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
				
					
			////check spmstore  sharma start
				$emaildata = array();
		        $email_for_orders = $this->model_account_order->getOrderem($order_id);
				foreach ($email_for_orders as $email_for_order) {
					$emaildata[] = array(
						'email' => $email_for_order['email']
					);
					$cusemail = $email_for_order['email'];
				$spmdata[] = array();
				$spmstore_code = $this->model_account_order->getSpmcode($order_id,$cusemail);
				foreach ($spmstore_code as $spmstore_code) {
					$spmdata[] = array(
						'spmmethodcode' => $spmstore_code['spmmethodcode'],
						'shop_name' => $spmstore_code['shop_name'],
						'customer_email' => $spmstore_code['customer_email'],
						'shop_address' => $spmstore_code['shop_address'],
						'shop_email' => $spmstore_code['shop_email'],
						'shop_telephone' => $spmstore_code['shop_telephone']
					);

			}
			if(empty($spmstore_code['spmmethodcode'])){
			   continue ;
			   }
			if(empty($spmstore_code['shop_name'])){
			   $spmstore_code['shop_name'] = $this->config->get('config_name') ;
			   }
			if(empty($spmstore_code['shop_email'])){
			    $spmstore_code['shop_email'] = $this->config->get('config_email') ;
		    }
	}
			

		////check spmstore  sharma end
				

				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
				} 
				/////sharma for spm address start
				elseif($store_info && ($order_info['shipping_code'] = $spmstore_code['spmmethodcode'])){
				    $store_address= $spmstore_code['shop_address'];
				    $store_email= $spmstore_code['shop_email'];
				    $store_telephone= $spmstore_code['shop_telephone'];
				    
				}
	          /////sharma for spm address start
				
				
				else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
				}

				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}

				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);

				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);

				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

				$this->load->model('tool/upload');

				$product_data = array();

				$products = $this->model_account_order->getOrderProducts($order_id);
/////// placed 0 IO '' by sharma
				foreach ($products as $product) {
					$option_weight = 0 ;

					$product_info = $this->model_catalog_product->getProduct($product['product_id']);

					if ($product_info) {
						$option_data = array();

						$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);

						foreach ($options as $option) {
							if ($option['type'] != 'file') {
								$value = $option['value'];
							} else {
								$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

								if ($upload_info) {
									$value = $upload_info['name'];
								} else {
									$value = '';
								}
							}

							$option_data[] = array(
								'name'  => $option['name'],
								'value' => $value
							);

							$product_option_value_info = $this->model_catalog_product->getProductOptionValue($product['product_id'], $option['product_option_value_id']);

							if ($product_option_value_info) {
								if ($product_option_value_info['weight_prefix'] == '+') {
									$option_weight += $product_option_value_info['weight'];
								} elseif ($product_option_value_info['weight_prefix'] == '-') {
									$option_weight -= $product_option_value_info['weight'];
								}
							}
						}

						$product_data[] = array(
							'name'     => $product_info['name'],
							'model'    => $product_info['model'],
							'option'   => $option_data,
							'quantity' => $product['quantity'],
							'location' => $product_info['location'],
							'sku'      => $product_info['sku'],
							'upc'      => $product_info['upc'],
							'ean'      => $product_info['ean'],
							'jan'      => $product_info['jan'],
							'isbn'     => $product_info['isbn'],
							'mpn'      => $product_info['mpn'],
							'weight'   => $this->weight->format(($product_info['weight'] + (float)$option_weight) * $product['quantity'], $product_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'))
						);
					}
				}

	////replaced else data array for shipping sharma	
	if( $order_info['shipping_code'] = $spmstore_code['spmmethodcode'] ){
				$data['orders'][] = array(
					'order_id'	       => $order_id,
					'invoice_no'       => $invoice_no,
					'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'       => $spmstore_code['shop_name'],
					'store_url'        => rtrim($order_info['store_url'], '/'),
					'store_address'    => $spmstore_code['shop_address'],
					'store_email'      => $spmstore_code['shop_email'],
					'store_telephone'  => $spmstore_code['shop_telephone'],
					'email'            => $order_info['email'],
					'telephone'        => $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'shipping_method'  => $order_info['shipping_method'],
					'product'          => $product_data,
					'comment'          => nl2br($order_info['comment'])
				  
				);
	}else{
	  				$data['orders'][] = array(
					'order_id'	       => $order_id,
					'invoice_no'       => $invoice_no,
					'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
					'store_name'       => $order_info['store_name'],
					'store_url'        => rtrim($order_info['store_url'], '/'),
					'store_address'    => nl2br($store_address),
					'store_email'      => $store_email,
					'store_telephone'  => $store_telephone,
					'email'            => $order_info['email'],
					'telephone'        => $order_info['telephone'],
					'shipping_address' => $shipping_address,
					'shipping_method'  => $order_info['shipping_method'],
					'product'          => $product_data,
					'comment'          => nl2br($order_info['comment'])
				  
				);  
	    
	}
		////replaced else data array for shipping sharma
			}
		}

		$this->response->setOutput($this->load->view('account/shipping', $data));
	}


}