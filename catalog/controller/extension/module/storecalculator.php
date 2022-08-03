<?php
class ControllerExtensionModuleStorecalculator extends Controller {
	private $error = array();
	public function index($setting) {
		$this->load->language('extension/module/storecalculator');
		$this->load->model('extension/module/storecalculator');		
		//current product & SPI
		$spoint_product_results = $this->model_extension_module_storecalculator->getSpointProduct();		
		if (isset($this->request->get['product_id'])) {
			$data['current_product_id'] = $this->request->get['product_id'];
		}
		
		if($spoint_product_results){			
			$data['spointproduct'] = array();
			foreach ($spoint_product_results as $spoint_product_result) {
				$data['spointproduct']=array(
					'product_id' => $spoint_product_result['product_id']
				);
			}
			$data['spi'] = $spoint_product_result['product_id'];			
		}
		//calculator field form
		if (isset($this->request->post['tagetarea'])) {
			$data['tagetarea'] = $this->request->post['tagetarea'];
		} elseif(isset($this->session->data['tagetarea'])){
            $data['tagetarea'] = $this->session->data['tagetarea'];
		} else {
			$data['tagetarea'] = $setting['tagetarea'];
		}
		$this->session->data['tagetarea'] = $data['tagetarea'];
		
		
		if (isset($this->request->post['product_persqft'])) {
			$data['product_persqft'] = $this->request->post['product_persqft'];
		}  elseif(isset($this->session->data['product_persqft'])){
            $data['product_persqft'] = $this->session->data['product_persqft'];
		} else {
			$data['product_persqft'] = $setting['product_persqft'];
		}
		$this->session->data['product_persqft'] = $data['product_persqft'];		
		
		$data['target_grandopening'] = $this->model_extension_module_storecalculator->getGrandopening();
		if (isset($this->request->post['target_grandopening'])) {
			$data['option_value_id'] = $this->request->post['target_grandopening'];
		}   elseif(isset($this->session->data['target_grandopening'])){
            $data['option_value_id'] = $this->session->data['target_grandopening'];
		} else{
			$data['option_value_id'] = $setting['target_grandopening'];
		}
		$this->session->data['target_grandopening'] = $data['option_value_id'];
		
		
		$ovi =  $data['option_value_id'];
		$tgo_results = $this->model_extension_module_storecalculator->getTgoname($ovi);
		foreach($tgo_results as $tgo){
			$data['go_name'] = $tgo['name']; 
		}
		
		if (isset($this->request->post['security_deposite'])) {
			$data['security_deposite'] = $this->request->post['security_deposite'];
		}  elseif(isset($this->session->data['security_deposite'])){
            $data['security_deposite'] = $this->session->data['security_deposite'];
		} else {
			$data['security_deposite'] = $setting['security_deposite'];
		}
		$this->session->data['security_deposite'] = $data['security_deposite'];
		
		if (isset($this->request->post['franchise_fee'])) {
			$data['franchise_fee'] = $this->request->post['franchise_fee'];
		}  elseif(isset($this->session->data['franchise_fee'])){
            $data['franchise_fee'] = $this->session->data['franchise_fee'];
		} else {
			$data['franchise_fee'] = $setting['franchise_fee'];
		}
		$this->session->data['franchise_fee'] = $data['franchise_fee'];
		
		if (isset($this->request->post['your_name'])) {
			$data['your_name'] = $this->request->post['your_name'];
		}  elseif(isset($this->session->data['your_name'])){
            $data['your_name'] = $this->session->data['your_name'];
		} else {
			$data['your_name'] = '';
		}
		$this->session->data['your_name'] = $data['your_name'];
		
		if (isset($this->request->post['your_email'])) {
			$data['your_email'] = $this->request->post['your_email'];
		}  elseif(isset($this->session->data['your_email'])){
            $data['your_email'] = $this->session->data['your_email'];
		} else {
			$data['your_email'] = '';
		}
		$this->session->data['your_email'] = $data['your_email'];
		if (isset($this->request->post['mobile'])) {
			$data['mobile'] = $this->request->post['mobile'];
		}  elseif(isset($this->session->data['mobile'])){
            $data['mobile'] = $this->session->data['mobile'];
		} else {
			$data['mobile'] = '';
		}
		$this->session->data['mobile'] = $data['mobile'];
		if (isset($this->request->post['shop_advanced'])) {
			$data['shop_advanced'] = $this->request->post['shop_advanced'];
		}  elseif(isset($this->session->data['shop_advanced'])){
            $data['shop_advanced'] = $this->session->data['shop_advanced'];
		} else {
			$data['shop_advanced'] = 0;
		}
		$this->session->data['shop_advanced'] = $data['shop_advanced'];
		if (isset($this->request->post['decoration_cost'])) {
			$data['decoration_cost'] = $this->request->post['decoration_cost'];
		}  elseif(isset($this->session->data['decoration_cost'])){
            $data['decoration_cost'] = $this->session->data['decoration_cost'];
		} else {
			$data['decoration_cost'] = 0;
		}
		$this->session->data['decoration_cost'] = $data['decoration_cost'];
		if (isset($this->request->post['other_charge'])) {
			$data['other_charge'] = $this->request->post['other_charge'];
		}  elseif(isset($this->session->data['other_charge'])){
            $data['other_charge'] = $this->session->data['other_charge'];
		} else {
			$data['other_charge'] = 0;
		}
		$this->session->data['other_charge'] = $data['other_charge'];
		
        //form actions
		$data['action'] = $this->url->link('product/product', 'product_id=' . $spoint_product_result['product_id'], true);
		$data['button_submit'] = $this->language->get('button_submit');
		

        //results label
		$data['label_legend'] = $this->language->get('text_legend');
		$data['label_result'] = $this->language->get('text_result');
		$data['label_result_profit'] = $this->language->get('text_result_profit');
		$data['label_avgsale'] = $this->language->get('text_avgsale');
		$data['label_tentative_profit'] = $this->language->get('text_tentative_profit');
		$data['label_ppc'] = $this->language->get('text_primary_product_cost');
		$data['label_pdo'] = $this->language->get('text_payable_during_order');
		$data['label_pbd'] = $this->language->get('text_payable_before_delivery');
		$data['label_pa'] = $this->language->get('text_product_arraival');
		$data['label_go'] = $this->language->get('text_grand_opening');
		$data['label_sic'] = $this->language->get('text_shop_instal_cost');
		$data['label_tc'] = $this->language->get('text_total_capital');
		$data['label_security'] = $this->language->get('text_security');		


        //Results
        $data['default_primary_product_cost'] = (int)$setting['tagetarea'] * (int)$setting['product_persqft'];
		$data['primary_product_cost']= (int)$data['tagetarea'] * (int)$data['product_persqft'] ;			
		$data['payable_during_order'] = (int)$data['primary_product_cost']*(int)$setting['advance_payment_order']/100;
		$data['payable_before_delivery'] = (int)$data['primary_product_cost'] - (int)$data['payable_during_order'];
		$lodd_results = $this->model_extension_module_storecalculator->getLod($ovi);
		foreach($lodd_results as $lodd_result){
			$data['product_arrivals'] = array(
				'product_arraival' => 'Last Order Deadline:'.$lodd_result['name']);
		}
		$data['pa'] = 'Last Order Deadline:'.$lodd_result['name'];
		$data['security'] = $data['security_deposite'] ;
		$data['shop_instal_cost'] = (int)$data['shop_advanced'] + (int)$data['decoration_cost'] + (int)$data['other_charge'] + (int)$data['franchise_fee'] ;
		$data['total_capital'] = (int)$data['primary_product_cost'] + (int)$data['shop_instal_cost']+(int)$data['security'];
		
		$data['comission'] = $this->customer->getGroupComission();
		$data['retail_value'] = (((int)$data['primary_product_cost']) * (int)$data['comission'])/100;
		$data['avg_sale'] = round($data['retail_value']/30);
		$data['t_profit'] = ((int)$data['primary_product_cost'] + $data['retail_value']) - ((int)$data['primary_product_cost']);


        //record data and send email
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_extension_module_storecalculator->addCaldata($data);
			
			$message  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			$message .= '<html dir="ltr" lang="en">' . "\n";
			$message .= '  <head>' . "\n";
			$message .= ' <title> Product is ready to dispatch!</title>' . "\n";
			$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$message .= '  </head>' . "\n";
			$message .= '  <body>';
			$message .= 
			'
			<h3> Dear '.$data['your_name'].', <br /><br />Your calculation information regarding the outlet is as follows:</h3>
			
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
			<td style="width:50%">'.$data['primary_product_cost'].'</td>
			
			</tr>
			<tr>
			<td class="text-right" style="width:50%">'.$data['label_pdo'].'</td>
			<td style="width:50%">'.$data['payable_during_order'].'</td>
			
			</tr>
			<tr>
			<td class="text-right" style="width:50%">'.$data['label_pbd'].'</td>
			<td style="width:50%">'.$data['payable_before_delivery'].'</td>
			
			</tr>
			<tr>
			<td class="text-right" style="width:50%">'.$data['label_go'].'</td>
			<td style="width:50%">'.$data['go_name'].'</td>
			
			</tr>
			<tr>
			<td class="text-right" style="width:50%">'.$data['label_pa'].'</td>
			<td style="width:50%">
			'.$data['pa'].'
			
			</td>
			
			</tr>
			<tr>
			<td class="text-right" style="width:50%">'.$data['label_sic'].'</td>
			<td style="width:50%">'.$data['shop_instal_cost'].'</td>
			
			</tr>
			<tr>
			<td class="text-right" style="width:50%">'.$data['label_security'].'</td>
			<td style="width:50%">'.$data['security'].'</td>
			
			</tr>
			
			
			
			<tr>
			<td class="text-right" style="width:50%;"><h4>'.$data['label_tc'].'<h4></td>
			<td style="width:50%; "><h4>'.$data['total_capital'].'</h4></td>
			
			</tr>
			</tbody>
			</table>
			</div>  ';
			
			$message .= '<br />' . ' <h3>You may calculate again by <a href = '.$data['action'].'> clicking here!</a></h3>
			<br />

			<h4>***These results are based on the information you provide.</h4>


			' ;
			
			$message .= '<br /><br />' . html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');			
			$message .= '</body>' . "\n";					
			$message .= '</html>' . "\n";            
			
            //mail parameter
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setTo($this->request->post['your_email']);			
			$mail->setFrom($this->config->get('config_email'));
			$mail->setReplyTo($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $this->request->post['your_name']), ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($message);
			$mail->send();

			$this->response->redirect($this->url->link('product/product', 'product_id=' . $spoint_product_result['product_id'], true));

		}
		//errors
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['tagetarea'])) {
			$data['error_tagetarea'] = $this->error['tagetarea'];
		} else {
			$data['error_tagetarea'] = '';
		}
		
		if (isset($this->error['product_persqft'])) {
			$data['error_product_persqft'] = $this->error['product_persqft'];
		} else {
			$data['error_product_persqft'] = '';
		}
		if (isset($this->error['target_grandopening'])) {
			$data['error_target_grandopening'] = $this->error['target_grandopening'];
		} else {
			$data['error_target_grandopening'] = '';
		}
		if (isset($this->error['security_deposite'])) {
			$data['error_security_deposite'] = $this->error['security_deposite'];
		} else {
			$data['error_security_deposite'] = '';
		}
		if (isset($this->error['franchise_fee'])) {
			$data['error_franchise_fee'] = $this->error['franchise_fee'];
		} else {
			$data['error_franchise_fee'] = '';
		}
		if (isset($this->error['your_name'])) {
			$data['error_your_name'] = $this->error['your_name'];
		} else {
			$data['error_your_name'] = '';
		}
		if (isset($this->error['your_email'])) {
			$data['error_your_email'] = $this->error['your_email'];
		} else {
			$data['error_your_email'] = '';
		}
		if (isset($this->error['mobile'])) {
			$data['error_mobile'] = $this->error['mobile'];
		} else {
			$data['error_mobile'] = '';
		}
		if (isset($this->error['shop_advanced'])) {
			$data['error_shop_advanced'] = $this->error['shop_advanced'];
		} else {
			$data['error_shop_advanced'] = '';
		}
		if (isset($this->error['decoration_cost'])) {
			$data['error_decoration_cost'] = $this->error['decoration_cost'];
		} else {
			$data['error_decoration_cost'] = '';
		}
		if (isset($this->error['other_charge'])) {
			$data['error_other_charge'] = $this->error['other_charge'];
		} else {
			$data['error_other_charge'] = '';
		}
        //conditions
		$data['err'] = !$this->error;
		$data['posting'] = ($this->request->server['REQUEST_METHOD'] == 'POST');		
		$data['mms'] = $this->language->get('error_there');
		$data['instruct'] = $this->language->get('text_instruct');	
		$data['home'] = $this->url->link('common/home');
		$data['curi'] = $this->url->link(isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home');	
		$data['reqtocart'] = $this->language->get('text_reqtocart');		
		
		return $this->load->view('extension/module/storecalculator', $data);

	}
	
	protected function validate() {
		

		if ((utf8_strlen($this->request->post['your_name']) < 3) || (utf8_strlen($this->request->post['your_name']) > 64)) {
			$this->error['your_name'] = $this->language->get('error_your_name');
		}
		
		if (!filter_var($this->request->post['your_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['your_email'] = $this->language->get('error_your_email');
		}
		
		$mobile_pattern = '/\+[0-9]{3}+[0-9]{10}/';
		
		if (!preg_match($mobile_pattern, $this->request->post['mobile'])) {
			$this->error['mobile'] = $this->language->get('error_mobile');
		}
		

		
		if (!is_numeric($this->request->post['tagetarea']) || $this->request->post['tagetarea'] <  99) {
			$this->error['tagetarea'] = $this->language->get('error_tagetarea');
		}
		
		if (!is_numeric($this->request->post['product_persqft']) || $this->request->post['product_persqft'] <  999) {
			$this->error['product_persqft'] = $this->language->get('error_product_persqft');
		}
		
		if (!is_numeric($this->request->post['security_deposite']) || $this->request->post['security_deposite'] <  99999) {
			$this->error['security_deposite'] = $this->language->get('error_security_deposite');
		}
		
		if (!is_numeric($this->request->post['franchise_fee']) || $this->request->post['franchise_fee'] <  29999) {
			$this->error['franchise_fee'] = $this->language->get('error_franchise_fee');
		}
		
		if (!is_numeric($this->request->post['shop_advanced'])) {
			$this->error['shop_advanced'] = $this->language->get('error_numeric_require');
		}
		
		if ( !is_numeric($this->request->post['decoration_cost']) ) {
			$this->error['decoration_cost'] = $this->language->get('error_numeric_require');
		}
		
		if (!is_numeric($this->request->post['other_charge'])) {
			$this->error['other_charge'] = $this->language->get('error_numeric_require');
		}
		
		return !$this->error;
	}
}
