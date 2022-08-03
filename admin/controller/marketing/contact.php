<?php
class ControllerMarketingContact extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/contact');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['user_token'] = $this->session->data['user_token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/contact', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('marketing/contact', 'user_token=' . $this->session->data['user_token'], true);

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/contact', $data));
	}

	public function send() {
		$this->load->language('marketing/contact');
		


		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'marketing/contact')) {
				$json['error']['warning'] = $this->language->get('error_permission');
			}

			if (!$this->request->post['subject']) {
				$json['error']['subject'] = $this->language->get('error_subject');
			}

			if (!$this->request->post['message']) {
				$json['error']['message'] = $this->language->get('error_message');
			}
			
			

			if (!$json) {
				$this->load->model('setting/store');

				$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);

				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}
				
				$this->load->model('setting/setting');
				$setting = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
				$store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');

				$this->load->model('customer/customer');

				$this->load->model('customer/customer_group');

				$this->load->model('sale/order');
				
				$this->load->model('extension/module/storecalculator');
				$this->load->model('extension/report/incart_report');
				$this->load->model('tool/image');

				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				$email_total = 0;

				$emails = array();

				switch ($this->request->post['to']) {
					case 'newsletter':
						$customer_data = array(
							'filter_newsletter' => 1,
							'start'             => ($page - 1) * 10,
							'limit'             => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

						foreach ($results as $result) {
							$emails[] = $result['email'];
						}
						break;
						/////sharma-calculator all
					case 'calculator_all':
						$calculator_data = array(
							'start' => ($page - 1) * 10,
							'limit' => 10
						);

						$email_total = $this->model_extension_module_storecalculator->getTotalCalculators($calculator_data);

						$results = $this->model_extension_module_storecalculator->getCalculators($calculator_data);

						foreach ($results as $result) {
							$emails[] = $result['your_email'];
							
						}
						break;	
						/////sharma-calculator all
						/////sharma-incomplete cart all
					case 'incart_all':
						$incart_data = array(
							'start' => ($page - 1) * 10,
							'limit' => 10
						);

						$email_total = $this->model_extension_report_incart_report->getTotalCarts($incart_data);

						$results = $this->model_extension_report_incart_report->getCarts($incart_data);

						foreach ($results as $result) {
							$emails[] = $result['email'];
							
						}
						break;	
						/////sharma-incomplete cart all
					case 'customer_all':
						$customer_data = array(
							'start' => ($page - 1) * 10,
							'limit' => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

						foreach ($results as $result) {
							$emails[] = $result['email'];
						}
						break;
					case 'customer_group':
						$customer_data = array(
							'filter_customer_group_id' => $this->request->post['customer_group_id'],
							'start'                    => ($page - 1) * 10,
							'limit'                    => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($customer_data);

						$results = $this->model_customer_customer->getCustomers($customer_data);

						foreach ($results as $result) {
							$emails[$result['customer_id']] = $result['email'];
						}
						break;
					case 'customer':
						if (!empty($this->request->post['customer'])) {
							foreach ($this->request->post['customer'] as $customer_id) {
								$customer_info = $this->model_customer_customer->getCustomer($customer_id);

								if ($customer_info) {
									$emails[] = $customer_info['email'];
								}
							}
						}
						break;
					case 'affiliate_all':
						$affiliate_data = array(
							'filter_affiliate' => 1,
							'start'            => ($page - 1) * 10,
							'limit'            => 10
						);

						$email_total = $this->model_customer_customer->getTotalCustomers($affiliate_data);

						$results = $this->model_customer_customer->getCustomers($affiliate_data);

						foreach ($results as $result) {
							$emails[] = $result['email'];
						}
						break;
					case 'affiliate':
						if (!empty($this->request->post['affiliate'])) {
							foreach ($this->request->post['affiliate'] as $affiliate_id) {
								$affiliate_info = $this->model_customer_customer->getCustomer($affiliate_id);

								if ($affiliate_info) {
									$emails[] = $affiliate_info['email'];
								}
							}
						}
						break;
					case 'product':
						if (isset($this->request->post['product'])) {
							$email_total = $this->model_sale_order->getTotalEmailsByProductsOrdered($this->request->post['product']);

							$results = $this->model_sale_order->getEmailsByProductsOrdered($this->request->post['product'], ($page - 1) * 10, 10);

							foreach ($results as $result) {
								$emails[] = $result['email'];
							}
						}
						break;
				}

				if ($emails) {
					$json['success'] = $this->language->get('text_success');

					$start = ($page - 1) * 10;
					$end = $start + 10;

					$json['success'] = sprintf($this->language->get('text_sent'), $start, $email_total);

					if ($end < $email_total) {
						$json['next'] = str_replace('&amp;', '&', $this->url->link('marketing/contact/send', 'user_token=' . $this->session->data['user_token'] . '&page=' . ($page + 1), true));
					} else {
						$json['next'] = '';
					}

					$message  = '<html dir="ltr" lang="en">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
					$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
					$message .= '  </head>' . "\n";
					$message .= '  <body>' . html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
					$message .= '</html>' . "\n";

					foreach ($emails as $email) {
				 if($this->request->post['to'] == 'calculator_all'){ /////sharma
					       
					    $cal_results = $this->model_extension_module_storecalculator->getCalculatordatas($email);
					    foreach($cal_results as $cal_result){
					        
					        $primary_product_cost = $cal_result['primary_product_cost'];
					        $payable_during_order = $cal_result['payable_during_order'];
					        $payable_before_delivery = $cal_result['payable_before_delivery'];
					        $product_arraival = $cal_result['product_arraival'];
					        $security = $cal_result['security'];
					        $shop_instal_cost = $cal_result['shop_instal_cost'];
					        $total_capital = $cal_result['total_capital'];
					        $target_grandopening = $cal_result['target_grandopening'];
					        $your_name = $cal_result['your_name'];
					        
					    }
					    
		$spoint_product_results = $this->model_extension_module_storecalculator->getSpointProduct();
		if($spoint_product_results){
		    $data['spointproduct'] = array();
		    foreach ($spoint_product_results as $spoint_product_result) {
	    	$data['spointproduct']=array(
	    	      'product_id' => $spoint_product_result['product_id']
	    	      );
	    	}
		}
		
       $store_url = $this->db->query("SELECT `ssl`, `name` FROM `" . DB_PREFIX . "store` WHERE name = 'sincehence Spoint'");
       if($store_url->num_rows){
       $surl = $store_url->row['ssl'];
       $sname = $store_url->row['name'];
       
       }
		$route = $surl.'index.php?route=product/product' ; 
		$pd = '&product_id=' . $spoint_product_result['product_id'];



		
	    $data['action'] =  $route.$pd;  
	    $data['action'] =  $surl.'index.php?route=product/product';  
					    
		$data['label_legend'] = $this->language->get('text_legend');
	    $data['label_result'] = $this->language->get('text_result');
	    
	    $data['label_ppc'] = $this->language->get('text_primary_product_cost');
	    $data['label_pdo'] = $this->language->get('text_payable_during_order');
	    $data['label_pbd'] = $this->language->get('text_payable_before_delivery');
	    $data['label_pa'] = $this->language->get('text_product_arraival');
	    $data['label_go'] = $this->language->get('text_grand_opening');
	    $data['label_sic'] = $this->language->get('text_shop_instal_cost');
	    $data['label_tc'] = $this->language->get('text_total_capital');
	    $data['label_security'] = $this->language->get('text_security');
					    
$msg  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
$msg .= '<html dir="ltr" lang="en">' . "\n";
$msg .= '  <head>' . "\n";
$msg .= ' <title> Product is ready to dispatch!</title>' . "\n";
$msg .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
$msg .= '  </head>' . "\n";
$msg .= '  <body>';
$msg .= 
			'
			<h3> Dear '.$your_name.', <br /><br />Your calculation information regarding the outlet is as follows:</h3>
			
			<div class="table-responsive">    
<table  class="table table-bordered table-hover">
  <thead>
  <tr>
    <td class="text-right" style="width:50%">'.$data['label_legend'].'</td>
    <td style="width:50%">'.$data['label_result'].'</td> 

  </tr>
        </thead>
<tbody>
  <tr>
    <td class="text-right" style="width:50%; ">'.$data['label_ppc'].'</td>
    <td style="width:50%">'.$primary_product_cost.'</td>
  
  </tr>
  <tr>
    <td class="text-right" style="width:50%">'.$data['label_pdo'].'</td>
    <td style="width:50%">'.$payable_during_order.'</td>
  
  </tr>
  <tr>
    <td class="text-right" style="width:50%">'.$data['label_pbd'].'</td>
    <td style="width:50%">'.$payable_before_delivery.'</td>
  
  </tr>
    <tr>
    <td class="text-right" style="width:50%">'.$data['label_go'].'</td>
    <td style="width:50%">'.$target_grandopening.'</td>
  
  </tr>
  <tr>
    <td class="text-right" style="width:50%">'.$data['label_pa'].'</td>
    <td style="width:50%">
        '.$product_arraival.'
        
        </td>
  
  </tr>
  <tr>
    <td class="text-right" style="width:50%">'.$data['label_sic'].'</td>
    <td style="width:50%">'.$shop_instal_cost.'</td>
  
  </tr>
  <tr>
    <td class="text-right" style="width:50%">'.$data['label_security'].'</td>
    <td style="width:50%">'.$security.'</td>
  
  </tr>
  
  
  
  <tr>
    <td class="text-right" style="width:50%;"><h4>'.$data['label_tc'].'<h4></td>
    <td style="width:50%; "><h4>'.$total_capital.'</h4></td>
  
  </tr>
  </tbody>
</table>
</div>  ';
		
$msg .= '<br />' . ' <h3>You may calculate again by <a href = '.$data['action'].'> clicking here!</a></h3>
<br />

<h4>***These results are based on the information you provide.</h4>


' ;
					
$msg .= '<br /><br />' . html_entity_decode($sname, ENT_QUOTES, 'UTF-8');
					
$msg .= '</body>' . "\n";					
$msg .= '</html>' . "\n";      


	}	////sharma for calculator end
if($this->request->post['to'] == 'incart_all'){ /////sharma	
	

	$reg_cus =  $this->db->query("SELECT * FROM `" . DB_PREFIX . "cart_incomplete`WHERE email = '".$email."'");
   if($reg_cus->num_rows){
	   $firstname = $reg_cus->row['name'];
	   $customer_id = $reg_cus->row['customer_id'];
	   $store_name = $reg_cus->row['store_name'];
	   $store_email = $reg_cus->row['store_email'];
	   $surl = $reg_cus->row['store_url'];
	   $link = $surl . 'checkout-checkout'  ;
	   
	   $carts = $this->db->query("SELECT sum(`quantity`) as quantity FROM `" . DB_PREFIX . "cart_incomplete` WHERE email = '" . $email . "'");
       if($carts->num_rows){
       $product_count = $carts->row['quantity'];
       }
       
	}
	$data['products']=array();
	$product_results = $this->model_extension_report_incart_report->getProducts($email);
	
	foreach ($product_results as $product){
	    if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = '';
				}
	    
	   $data['products'][]=array( 
	    'name' => $product['name'],
	    'product_id' => $product['product_id'],
	    'description' => utf8_substr(trim(strip_tags(html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
	    'image' => $image
	    
	    
	    
	    
	    );
	    
	}
       
       
       $mes_body = '
       
       <div style = "display: block; width:98%; float:left; position: relative; margin: 1%;" >
       
       <p>Dear '.$firstname.', </p>
       <p>
       <br><br>We noticed that during your last visit to our store you placed the following products to your shopping cart and proceeding through checkout, but for some reason you did not complete the order:
       <br><br>
       </p>';
       if($customer_id > 0){
       $mes_body .= '
       
       <p> We do not know why you decided not to purchase this time, but you may
       
       Click the  <a href = '.$link.' >link</a> to check it out! </p>
       <br>
       
       ';
		}
       $mes_body .= '
       <p> There are '.$product_count.' number of below products in your cart! </p>
       </div>
       
       ';
       
       foreach ($data['products'] as  $data) {  
           
        $seo_results = $this->db->query("SELECT `keyword` FROM  `" . DB_PREFIX . "seo_url` WHERE `query` = 'product_id=" . (int)$data['product_id'] . "'");   
		if($seo_results->num_rows){
		    
		    $suurl = $surl.$seo_results->row['keyword'];
		}
	 	
					
        $mes_body .= '
       
       <a href = "'.$suurl.'">
          <div style = "display: inline-block; width:48%; float:left; position: relative; margin: 1%; background-color: #f2ded7; min-height: 200px;" >
           <div style="width: 98%; display: inline-block; float: left; position:relative; padding: 1%; ">  
             <h3>'.html_entity_decode($data['name']).'</h3>
             </div>
            <img style="width: 98%; height: 500px; display: inline-block; float: left; position:relative; padding: 1%;" src = "'.$data['image'].' " alt = "img"/>
            <div style="width: 98%; display: inline-block; float: left; position:relative; padding: 1%; ">        
            
            <p>'.html_entity_decode($data['description']).'</p>
            
            
            
            
            
            </div>
          </div>              
         </a>
             
             ';
					
		}
       
 
       $mes_body .= '
       <div style = "display: block; width:98%; float:left; position: relative; margin: 1%;" >
      
       <br><br>
       
      
       
      <p> Thank You!<br><br></p>
       
      <p> '.$store_name.'</p>
       
       </div>
       
       ';
       

	
$data['text_subject'] = $this->language->get('text_subject');

	
}	/////sharma for incart end
					    
						if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
						    
							$mail = new Mail($this->config->get('config_mail_engine'));
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
							$mail->smtp_username = $this->config->get('config_mail_smtp_username');
							$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
							$mail->smtp_port = $this->config->get('config_mail_smtp_port');
							$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

							$mail->setTo($email);
							$mail->setFrom($store_email);
							$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
							if($this->request->post['to'] == 'calculator_all'){ 
							$mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $your_name, ENT_QUOTES, 'UTF-8')));
							}elseif($this->request->post['to'] == 'incart_all'){
							$mail->setSubject(html_entity_decode(sprintf($data['text_subject'], $firstname, ENT_QUOTES, 'UTF-8')));
							}else{
							$mail->setSubject(html_entity_decode($this->request->post['subject'], ENT_QUOTES, 'UTF-8'));
							}
							if($this->request->post['to'] == 'calculator_all'){ 
							$mail->setHtml($msg);  
							}elseif($this->request->post['to'] == 'incart_all'){
							$mail->setHtml($mes_body);
							}else{
							$mail->setHtml($message);
							}
							$mail->send();
						}
					
					    
				 }
				} else {
					$json['error']['email'] = $this->language->get('error_email');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
