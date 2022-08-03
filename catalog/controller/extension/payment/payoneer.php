<?php
class ControllerExtensionPaymentPayoneer extends Controller {
    private $error = array();
	public function index() {
		$this->load->language('extension/payment/payoneer');

		$data['payoneer'] = nl2br($this->config->get('payment_payoneer_payoneer' . $this->config->get('config_language_id')));

		return $this->load->view('extension/payment/payoneer', $data);
	}

	public function confirm() {
		$json = array();
		
		if (isset($this->request->post['payment_email'])) {
			$payment_email = str_replace(' ', '', $this->request->post['payment_email']);
		} else {
			$payment_email = '' ;
		}
		
		if (isset($this->request->post['trxId'])) {
			$trxId = str_replace(' ', '', $this->request->post['trxId']);
		} else {
			$trxId = '' ;
		}
		
		$this->checkTrxId($trxId);
		
		if ($this->session->data['payment_method']['code'] == 'payoneer') {
			$this->load->language('extension/payment/payoneer');
			
			if(!$this->validateForm()){
		    $this->addPayment($payment_email, $trxId);

			$this->load->model('checkout/order');

			$comment  = $this->language->get('text_instruction') . "\n\n";
			$comment .= $this->config->get('payment_payoneer_payoneer' . $this->config->get('config_language_id')) . "\n\n";
			$comment .= $this->language->get('text_payment');
			$comment .= $this->language->get('text_payment_email') . '<br>' . $payment_email . '<br>' ;
			$comment .= $this->language->get('text_trxId') . '<br>' . $trxId . '<br>' ;

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_payoneer_order_status_id'), $comment, true);
		
			$json['redirect'] = $this->url->link('checkout/success');
		}else{
		   if (isset($this->error['warning'])) {
			$json['error_warning'] = $this->error['warning'];
		} else {
			$json['error_warning'] = '';
		}
		
		if (isset($this->error['payment_email'])) {
			$json['error_payment_email'] = $this->error['payment_email'];
		} 
		
		if (isset($this->error['trxId'])) {
			$json['error_trxId'] = $this->error['trxId'];
		} 
		}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
		public function checkTrxId($trxId){
	   $check_trxId = $this->db->query("SELECT `payment_trxID` FROM `" . DB_PREFIX . "order` WHERE `payment_trxID` ='". $trxId ."'"); 
	   if($check_trxId->num_rows){
	      return $check_trxId->row['payment_trxID']; 
	   }else{
	       return false;
	   }
	}
	public function addPayment($payment_email, $trxId){
	 $this->db->query("UPDATE " . DB_PREFIX . "order SET payment_email = '" . $payment_email . "', payment_trxID = '". $trxId ."' WHERE order_id = '" . (int)$this->session->data['order_id'] . "'");
	}
	
	protected function validateForm() {
	    $trxId = str_replace(' ', '', $this->request->post['trxId']);
	    $payment_email = str_replace(' ', '', $this->request->post['payment_email']);
	    $chtrxId = str_replace(' ', '', $this->checkTrxId($trxId));
	    
	    
	    if ((utf8_strlen($this->request->post['payment_email']) > 96) || !filter_var($this->request->post['payment_email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['payment_email'] = $this->language->get('error_payment_email');
		}
 		if (!$trxId || $chtrxId) {
			$this->error['trxId'] = $this->language->get('error_trxId');
		}
		
	return $this->error;	
		
	}
}