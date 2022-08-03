<?php
/* Partial Payment Total for OpenCart v.3.0.x 
 *
* @version 3.3.0
 * @date 16/08/2020
 * @author Kestutis Banisauskas
 * @Smartechas
 */
class ControllerExtensionTotalPartialPaymentTotal extends Controller {
	private $error = array();
	public function install(){ 
           $this->load->model('extension/total/partial_payment_total');
           $this->model_extension_total_partial_payment_total->install();
             }

	public function index() {
		$this->load->language('extension/total/partial_payment_total');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('total_partial_payment_total', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
		}
		
		$this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
		
		$data['text_all_zones'] = $this->language->get('text_all_zones');

		$data['entry_partial'] = $this->language->get('entry_partial');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_percent'] = $this->language->get('entry_percent');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_product_ids'] = $this->language->get('entry_product_ids');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');

		
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['help_total'] = $this->language->get('help_total');
		$data['help_percent'] = $this->language->get('help_percent');
		$data['help_total_sort'] = $this->language->get('help_total_sort');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_product_ids'] = $this->language->get('help_product_ids');
		$data['help_customer_group'] = $this->language->get('help_customer_group');

		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];
		$data['download'] = ' Extension you can download<a style=" text-decoration: underline; font-weight: bold; color: green;" target="_blank" href="http://agnetgroup.eu/extensions-partial-payment-advanced-pro-partial-a-p-2779">  here...</a>';

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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/partial_payment_total', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/total/partial_payment_total', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		if (isset($this->request->post['total_partial_payment_total_total'])) {
			$data['total_partial_payment_total_total'] = $this->request->post['total_partial_payment_total_total'];
		} else {
			$data['total_partial_payment_total_total'] = $this->config->get('total_partial_payment_total_total');
		}
		
		if (isset($this->request->post['total_partial_payment_total_category'])) {
			$data['total_partial_payment_total_category'] = $this->request->post['total_partial_payment_total_category'];
		} elseif ($this->config->get('total_partial_payment_total_category')) {
			$data['total_partial_payment_total_category'] = $this->config->get('total_partial_payment_total_category');
		} else {
			$data['total_partial_payment_total_category'] = array();
		}

		$data['categories'] = array();

		$this->load->model('catalog/category');

		foreach ($data['total_partial_payment_total_category'] as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['categories'][] = array(
					'category_id' 	=> $category_info['category_id'],
					'name' 			=> ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}

		if (isset($this->request->post['total_partial_payment_total_xproducts'])) {
			$data['total_partial_payment_total_xproducts'] = $this->request->post['total_partial_payment_total_xproducts'];
		} else {
			$data['total_partial_payment_total_xproducts'] = $this->config->get('total_partial_payment_total_xproducts');
		}

		if (isset($this->request->post['total_partial_payment_total_customer_group'])) {
			$data['total_partial_payment_total_customer_group'] = $this->request->post['total_partial_payment_total_customer_group'];
		} elseif ($this->config->get('total_partial_payment_total_customer_group')) {
			$data['total_partial_payment_total_customer_group'] = $this->config->get('total_partial_payment_total_customer_group');
		} else {
			$data['total_partial_payment_total_customer_group'] = array();
		}

		$data['customer_groups'] = array();

		$this->load->model('customer/customer_group');

		foreach ($data['total_partial_payment_total_customer_group'] as $customer_group_id) {
			$customer_group_info = $this->model_customer_customer_group->getCustomerGroup($customer_group_id);

			if ($customer_group_info) {
				$data['customer_groups'][] = array(
					'customer_group_id' => $customer_group_info['customer_group_id'],
					'name'				=> $customer_group_info['name']
				);
			}
		}
		
		
				
		if (isset($this->request->post['total_partial_payment_total_percent'])) {
			$data['total_partial_payment_total_percent'] = $this->request->post['total_partial_payment_total_percent'];
		} else {
			$data['total_partial_payment_total_percent'] = $this->config->get('total_partial_payment_total_percent');
		}
		
		
		
		
		if (isset($this->request->post['total_partial_payment_total_geo_zone_id'])) {
			$data['total_partial_payment_total_geo_zone_id'] = $this->request->post['total_partial_payment_total_geo_zone_id'];
		} else {
			$data['total_partial_payment_total_geo_zone_id'] = $this->config->get('total_partial_payment_total_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['total_partial_payment_total_tax_class_id'])) {
			$data['total_partial_payment_total_tax_class_id'] = $this->request->post['total_partial_payment_total_tax_class_id'];
		} else {
			$data['total_partial_payment_total_tax_class_id'] = $this->config->get('total_partial_payment_total_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		
		if (isset($this->request->post['total_partial_payment_total_status'])) {
			$data['total_partial_payment_total_status'] = $this->request->post['total_partial_payment_total_status'];
		} else {
			$data['total_partial_payment_total_status'] = $this->config->get('total_partial_payment_total_status');
		}

		if (isset($this->request->post['total_partial_payment_total_sort_order'])) {
			$data['total_partial_payment_total_sort_order'] = $this->request->post['total_partial_payment_total_sort_order'];
		} else {
			$data['total_partial_payment_total_sort_order'] = $this->config->get('total_partial_payment_total_sort_order');
		}
		
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['total_partial_payment_total_order_status'])) {
			$data['total_partial_payment_total_order_status'] = $this->request->post['total_partial_payment_total_order_status'];
		} else {
			$data['total_partial_payment_total_order_status'] = $this->config->get('total_partial_payment_total_order_status');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/partial_payment_total', $data));
	}
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_customer_group'])) {
			$this->load->model('customer/customer_group');

			$results = $this->model_customer_customer_group->getCustomerGroups();

			foreach ($results as $result) {
				$json[] = array(
					'customer_group_id' => $result['customer_group_id'],
					'name'       		=> strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/partial_payment_total')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function send() {
		
		$this->load->language('extension/total/partial_payment_total');
		
		$data['text_subject'] = $this->language->get('text_subject');
		

		$json = array();
		/**/
		
				$this->load->model('setting/setting');
				$this->load->model('setting/store');
				$this->load->model('customer/customer');
				$this->load->model('sale/order');
				
		

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			if (!$json) {
				if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = HTTP_CATALOG . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}
				

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} elseif (isset($_POST['order_id'])) {
			$order_id = $_POST['order_id'];
		} else {
			$order_id = '';
		}
	
				
				
				
				
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
			if ($query->num_rows) {
				$email = $query->row['email'];
			} else {
				$email = '';				
			}
			if ($query->num_rows) {
				$store_id = $query->row['store_id'];
			} else {
				$store_id = '';
			}
			if ($query->num_rows) {
				$pending_total= $query->row['pending_total'];
			} else {
				$pending_total = '';
			}
			if ($query->num_rows) {
				$firstname = $query->row['firstname'];
			} else {
				$firstname = '';
			}
			if ($query->num_rows) {
				$currency_value = $query->row['currency_value'];
			} else {
				$currency_value = '';
			}
			if ($query->num_rows) {
				$currency_code = $query->row['currency_code'];
			} else {
				$currency_code = '';
			}
			
			$store_info = $this->model_setting_store->getStore($store_id);

				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}
				
				$data['text_thank_you'] = sprintf($this->language->get('text_thank_you'), $store_name);
			
					

				$setting = $this->model_setting_setting->getSetting('config', $store_id);
				$store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');
					$json['success'] = $this->language->get('text_success');

					$json['success'] = sprintf($this->language->get('text_sent'), $email);
					
					$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
					$message .= '<html dir="ltr" lang="en">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= '    <title> <h4>' . $this->config->get('total_partial_payment_total_subject') . '</h4></title>' . "\n";
					$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
					$message .= '  </head>' . "\n";
					$message .= '  <body>';
					$message .= 
			'<table width="70%" border="0" cellpadding="5">
				<tbody>
			     <tr>	
						<th colspan="2"><img style="float: left;" src="' . $data['logo'] . '"></th>
				</tr>
				<tr>
						<td style="padding-bottm: 10px" width="30%"><strong>' . html_entity_decode($firstname, ENT_QUOTES, 'UTF-8') . '</strong></td>
						<td> </td>
				</tr>
				
				<tr>
						<td width="30%">' . html_entity_decode($this->language->get('text_order_id'), ENT_QUOTES, 'UTF-8') . '</td>
						<td> #' . $order_id . '</td>
				</tr>
				<tr>
						<td width="30%">' . html_entity_decode($this->language->get('text_payment_pending'), ENT_QUOTES, 'UTF-8') . '</td>
						<td>' . $this->currency->format($pending_total, $currency_code, $currency_value) . '</td>
				</tr>
				<tr>
						<td width="30%">' . html_entity_decode($this->language->get('text_payment_request'), ENT_QUOTES, 'UTF-8') . '</td>
						<td><a href="' . HTTP_CATALOG . 'index.php?route=account/order/info&order_id='.$order_id . '">'  . HTTP_CATALOG . 'index.php?route=account/order/info&order_id='. $order_id . '</a></td>
				</tr>
			</tbody>
		</table>';
					
					$message .= '<br /><br />' . $data['text_thank_you'];
					
					$message .= '</body>' . "\n";					
					$message .= '</html>' . "\n";

				
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
						/*		
						$mail = new Mail();
                        $mail->protocol = $this->config->get('config_mail_protocol');
                        $mail->parameter = $this->config->get('config_mail_parameter');
                        $mail->hostname = $this->config->get('config_smtp_host');
                        $mail->username = $this->config->get('config_smtp_username');
                        $mail->password = $this->config->get('config_smtp_password');
                        $mail->port = $this->config->get('config_smtp_port');
                        $mail->timeout = $this->config->get('config_smtp_timeout');
                        //$mail->setFrom($this->config->get('config_email'));
						$mail->setFrom($store_email);
                        $mail->setTo($email);
                        $mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
						$mail->setSubject(html_entity_decode($this->config->get('total_partial_payment_total_subject'), ENT_QUOTES, 'UTF-8'));
   						$mail->setHtml($message);
						$mail->setText($message . html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                        $mail->send();
						*/
								
			}
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