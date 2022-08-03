<?php
class ControllerExtensionReportCartReport extends Controller {
	public function index() {
		$this->load->language('extension/report/cart_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_cart_report', $this->request->post);

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
			'href' => $this->url->link('extension/report/cart_report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/cart_report', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_cart_report_status'])) {
			$data['report_cart_report_status'] = $this->request->post['report_cart_report_status'];
		} else {
			$data['report_cart_report_status'] = $this->config->get('report_cart_report_status');
		}

		if (isset($this->request->post['report_cart_report_sort_order'])) {
			$data['report_cart_report_sort_order'] = $this->request->post['report_cart_report_sort_order'];
		} else {
			$data['report_cart_report_sort_order'] = $this->config->get('report_cart_report_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/cart_report_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/cart_report')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
			
	public function report() {
		$this->load->language('extension/report/cart_report');

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

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = 0;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->load->model('extension/report/cart_report');

		$data['customers'] = array();

		$filter_data = array(
			'filter_date_start'			=> $filter_date_start,
			'filter_date_end'			=> $filter_date_end,
			'filter_customer'			=> $filter_customer,
			'filter_order_status_id'	=> $filter_order_status_id,
			'start'						=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'						=> $this->config->get('config_limit_admin')
		);

		$customer_total = $this->model_extension_report_cart_report->getTotalCarts($filter_data);

		$results = $this->model_extension_report_cart_report->getCarts($filter_data);

		foreach ($results as $result) {
			$data['customers'][] = array(
				'customer'       => $result['customer'],
				'customer_id'       => $result['customer_id'],
				'email'          => $result['email'],
				'customer_group' => $result['customer_group'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'carts'         => $result['carts'],
				'products'       => $result['products'],
				// 'edit'           => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'], true)
			);
		}
        $this->request->post['email'] = isset($this->request->post['email']) ? $this->request->post['email'] : '';            
        $this->session->data['email'] = $this->request->post['email'];
		$data['user_token'] = $this->session->data['user_token'];
        

		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode($this->request->get['filter_customer']);
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		

		$pagination = new Pagination();
		$pagination->total = $customer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=cart_report' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_customer'] = $filter_customer;
		$data['filter_order_status_id'] = $filter_order_status_id;

		return $this->load->view('extension/report/cart_report_info', $data);
	}
	public function send() {
		
		$this->load->language('extension/report/cart_report');
		
		$data['text_subject'] = $this->language->get('text_subject');
		

		$json = array();
		/**/
		
				$this->load->model('setting/setting');
				$this->load->model('setting/store');
				$this->load->model('customer/customer');
				$this->load->model('extension/report/cart_report');
				$this->load->model('tool/image');
				
		

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			if (!$json) {
				if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = HTTPS_CATALOG . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}			

		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
		} elseif (isset($_POST['customer_id'])) {
			$customer_id = $_POST['customer_id'];
		} else {
			$customer_id = '';
		}
		

		
				
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
			if ($query->num_rows) {
				$email = $query->row['email'];
			} else {
				$email = '';				
			}
			
			if ($query->num_rows) {
				$firstname = $query->row['firstname'];
			} else {
				$firstname = '';
			}
    	$customer_store = $this->db->query("SELECT `store_id` FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
       if($customer_store->num_rows){
       $store_id = $customer_store->row['store_id'];
       }
       $store_info = $this->model_setting_store->getStore($store_id);

				if ($store_info) {
					$store_name = $store_info['name'];
					$surl = $store_info['ssl'];
					$link = $surl . 'checkout-checkout'  ;
				} else {
					$store_name = $this->config->get('config_name');
					$link = HTTPS_CATALOG . 'checkout-checkout'  ;
				}
		$data['text_thank_you'] = sprintf($this->language->get('text_thank_you'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));	
		$carts = $this->db->query("SELECT sum(`quantity`) as quantity FROM `" . DB_PREFIX . "cart` WHERE customer_id = '" . (int)$customer_id . "'");
       if($carts->num_rows){
       $products = $carts->row['quantity'];
       }
       
       
       
       $mes_body = 'Dear '.$firstname.', There are '.$products.' products in your cart! Click the  <a href = '.$link.' >link</a> to check it out! ';
			
			
			
			}	
			$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
					$message .= '<html dir="ltr" lang="en">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= '    <title>'.' <h4>' . $data['text_subject'] . '</h4>'.'</title>' . "\n";
					$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
					$message .= '  </head>' . "\n";
					$message .= '  <body>';
					
					$message .= ' ' . html_entity_decode($mes_body , ENT_QUOTES, 'UTF-8' ). '';
					
					

					
					
					$message .= '<br /><br />' . $data['text_thank_you'];
					
					$message .= '</body>' . "\n";					
					$message .= '</html>' . "\n"; 
			


				    $setting = $this->model_setting_setting->getSetting('config', $store_id);
				    $store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');
					$json['success'] = $this->language->get('text_success');
					$json['success'] = sprintf($this->language->get('text_sent'), $email);
					
							$mail = new Mail();
							$mail->protocol = $this->config->get('config_mail_protocol');
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
							$mail->smtp_username = $this->config->get('config_mail_smtp_username');
							$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
							$mail->smtp_port = $this->config->get('config_mail_smtp_port');
							$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

							$mail->setTo($email);
							$mail->setFrom($store_email);
							$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
							$mail->setSubject(html_entity_decode($this->language->get('text_subject'), ENT_QUOTES, 'UTF-8'));
							$mail->setHtml($message);
							$mail->send();
								
			

	}	
				
				if ($email) {	
				

					$json['success'] = sprintf($this->language->get('text_sent'), $email);
				} else {
					$json['error']['email'] = $this->language->get('error_email');
				}
				
			
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		}
}