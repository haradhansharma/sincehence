<?php
class ControllerExtensionPaymentbKashWallet extends Controller {
    private $error = array();
	public function index() {
		$this->load->language('extension/payment/bkash_wallet');

		$data['bkash'] = nl2br($this->config->get('payment_bkash_wallet_bkash' . $this->config->get('config_language_id')));

		return $this->load->view('extension/payment/bkash_wallet', $data);
	}

	public function confirm() {
		$json = array();
		
		if (isset($this->request->post['mobilenumber'])) {
			$mobilenumber = str_replace(' ', '', $this->request->post['mobilenumber']);
		} else {
			$mobilenumber = '' ;
		}
		
		if (isset($this->request->post['trxId'])) {
			$trxId = str_replace(' ', '', $this->request->post['trxId']);
		} else {
			$trxId = '' ;
		}
		
		$this->checkTrxId($trxId);
		
		if ($this->session->data['payment_method']['code'] == 'bkash_wallet') {
			$this->load->language('extension/payment/bkash_wallet');
			
			if(!$this->validateForm()){
		    $this->addPayment($mobilenumber, $trxId);

			$this->load->model('checkout/order');

			$comment  = $this->language->get('text_instruction') . "\n\n";
			$comment .= $this->config->get('payment_bkash_wallet_bkash' . $this->config->get('config_language_id')) . "\n\n";
			$comment .= $this->language->get('text_payment');
			$comment .= $this->language->get('text_mobilenumber') . '<br>' . $mobilenumber . '<br>' ;
			$comment .= $this->language->get('text_trxId') . '<br>' . $trxId . '<br>' ;

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_bkash_wallet_order_status_id'), $comment, true);
		
			$json['redirect'] = $this->url->link('checkout/success');
		}else{
		   if (isset($this->error['warning'])) {
			$json['error_warning'] = $this->error['warning'];
		} else {
			$json['error_warning'] = '';
		}
		
		if (isset($this->error['mobilenumber'])) {
			$json['error_mobilenumber'] = $this->error['mobilenumber'];
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
	public function addPayment($mobilenumber, $trxId){
	 $this->db->query("UPDATE " . DB_PREFIX . "order SET payment_mobile_number = '" . $mobilenumber . "', payment_trxID = '". $trxId ."' WHERE order_id = '" . (int)$this->session->data['order_id'] . "'");
	}
	
	protected function validateForm() {
	    $trxId = str_replace(' ', '', $this->request->post['trxId']);
	    $mobilenumber = str_replace(' ', '', $this->request->post['mobilenumber']);
	    $chtrxId = str_replace(' ', '', $this->checkTrxId($trxId));
	    
	    
	    $mobile_pattern = '/^[0]{1}[0-9]{10}$/';
		if (!preg_match($mobile_pattern, $mobilenumber)) {
			$this->error['mobilenumber'] = $this->language->get('error_mobilenumber');
		}
 		if (!$trxId || $chtrxId) {
			$this->error['trxId'] = $this->language->get('error_trxId');
		}
		
	return $this->error;	
		
	}
}