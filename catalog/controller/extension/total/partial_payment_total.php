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

	public function send() {
		
		$this->load->language('extension/total/partial_payment_total');
		
		$data['text_subject'] = $this->language->get('text_subject');
		

		$json = array();
		/**/
		
				$this->load->model('setting/setting');
				$this->load->model('setting/store');
				$this->load->model('account/customer');
				$this->load->model('account/order');
				
		

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			if (!$json) {
				if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = 'image/' . $this->config->get('config_logo');
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
				$cusemail = $query->row['email'];
			} else {
				$cusemail = '';				
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
			
			$spmdata = array();
			
			$store_info = $this->model_account_order->getSpmcode($order_id,$cusemail);
			foreach ($store_info as $store_info){
			    $spmdata[] = array(
			      
			      $store_name = $store_info['shop_name'],
			      $shop_address = $store_info['shop_address'],
			      $shop_url = $store_info['store_url'],
			      $store_email = $store_info['shop_email'],
			      $shop_telephone = $store_info['shop_telephone']
			      
			        
			       );
			       
		if(empty($store_name)){
			 $store_name = $this->config->get('config_name');
		}
		if(empty($shop_address)){
			 $shop_address = $this->config->get('config_address');
		}
		if(empty($shop_url)){
			 $shop_url = $this->config->get('config_url');
		}
		if(empty($store_email)){
			 $store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');
		}
		if(empty($shop_telephone)){
			 $shop_telephone = isset($setting['config_telephone']) ? $setting['config_telephone'] : $this->config->get('config_telephone');
		}
		
		
		
	}         
	
	$from_details = ($store_name. "<br/> $shop_address ".  " <br/> $store_email ". "<br/> $shop_telephone " );
		


				$data['text_thank_you'] = sprintf($this->language->get('text_thank_you'), $from_details);
			
				

				$setting = $this->model_setting_setting->getSetting('config', $store_id);
				
					$json['success'] = $this->language->get('text_success');

					$json['success'] = sprintf($this->language->get('text_sent'), $email);
					
					$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
					$message .= '<html dir="ltr" lang="en">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= ' <title> Product is ready to dispatch!</title>' . "\n";
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
						<td style="padding-bottm: 10px" width="30%"><strong>' . html_entity_decode('Dear '. $firstname, ENT_QUOTES, 'UTF-8') . '</strong></td>
						<td> </td>
				</tr>
				
				<tr>
						<td width="30%">' . html_entity_decode($this->language->get('text_order_id'), ENT_QUOTES, 'UTF-8') . '</td>
						<td> <strong> #' . $order_id . '</strong>  is ready to dispatch at below address! </td>
				</tr>
				<tr>
						<td width="30%">' . html_entity_decode($this->language->get('text_payment_pending'), ENT_QUOTES, 'UTF-8') . '</td>
						<td>' . $this->currency->format($pending_total, $currency_code, $currency_value) . '</td>
				</tr>
				<tr>
						<td width="30%">' . html_entity_decode($this->language->get('text_payment_request'), ENT_QUOTES, 'UTF-8') . '</td>
						<td><a href="'.$shop_url.'?route=account/order/info&order_id='.$order_id . '">'.$shop_url.'?route=account/order/info&order_id='. $order_id . '</a></td>
						
					
				</tr>
			</tbody>
		</table>';
		
		            $message .= '<br />' . 'Then, you are requested to collect the goods within 2 days from below address!' ;
					
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