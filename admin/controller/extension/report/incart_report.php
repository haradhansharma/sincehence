<?php
class ControllerExtensionReportIncartReport extends Controller {
	public function index() {
		$this->load->language('extension/report/incart_report');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('report_incart_report', $this->request->post);

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
			'href' => $this->url->link('extension/report/incart_report', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/report/incart_report', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=report', true);

		if (isset($this->request->post['report_incart_report_status'])) {
			$data['report_incart_report_status'] = $this->request->post['report_incart_report_status'];
		} else {
			$data['report_incart_report_status'] = $this->config->get('report_incart_report_status');
		}

		if (isset($this->request->post['report_incart_report_sort_order'])) {
			$data['report_incart_report_sort_order'] = $this->request->post['report_incart_report_sort_order'];
		} else {
			$data['report_incart_report_sort_order'] = $this->config->get('report_incart_report_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/report/incart_report_form', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/report/incart_report')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
			
	public function report() {
		$this->load->language('extension/report/incart_report');

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



		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->load->model('extension/report/incart_report');

		$data['customers'] = array();

		$filter_data = array(
			'filter_date_start'			=> $filter_date_start,
			'filter_date_end'			=> $filter_date_end,
			'filter_customer'			=> $filter_customer,
			'start'						=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'						=> $this->config->get('config_limit_admin')
		);

		$customer_total = $this->model_extension_report_incart_report->getTotalCarts($filter_data);

		$results = $this->model_extension_report_incart_report->getCarts($filter_data);

		foreach ($results as $result) {
			$data['customers'][] = array(
				'customer'       => $result['customer'],
				'store_name'       => $result['store_name'],
				'customer_id'       => $result['customer_id'],
				'email'          => $result['email'],
				'phone'          => $result['phone'],
				'carts'         => $result['carts'],
				'products'       => $result['products'],
				// 'edit'           => $this->url->link('customer/customer/edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $result['customer_id'], true)
			);
		}

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



		

		$pagination = new Pagination();
		$pagination->total = $customer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('report/report', 'user_token=' . $this->session->data['user_token'] . '&code=incart_report' . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($customer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customer_total - $this->config->get('config_limit_admin'))) ? $customer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customer_total, ceil($customer_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_customer'] = $filter_customer;


		return $this->load->view('extension/report/incart_report_info', $data);
	}
	public function send() {
		
		$this->load->language('extension/report/incart_report');
		
		
		

		$json = array();
		/**/
		
				$this->load->model('setting/setting');
				$this->load->model('setting/store');
				$this->load->model('customer/customer');
				$this->load->model('extension/report/incart_report');
				$this->load->model('tool/image');
				
		

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
		if (!$json) {
				if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = HTTPS_CATALOG . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}


		
		if(isset($this->request->post['email'])){
		  $email =  $this->request->post['email']; 
		}elseif(isset($this->request->get['email'])){
		  $email =  $this->request->get['email'];   
		}
		
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
       
	}
	
$data['text_subject'] = $this->language->get('text_subject');
	
			$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
					$message .= '<html dir="ltr" lang="en">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= '    <title>'.' <h4>' . $data['text_subject'] . '</h4>'.'</title>' . "\n";
					$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
					$message .= '  </head>' . "\n";
					$message .= '  <body>';
					
					$message .= ' ' . html_entity_decode($mes_body , ENT_QUOTES, 'UTF-8' ). '';
					
					
					 
					
					
					$message .= '</body>' . "\n";					
					$message .= '</html>' . "\n"; 
			


				    $setting = $this->model_setting_setting->getSetting('config');

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