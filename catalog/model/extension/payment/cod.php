<?php
class ModelExtensionPaymentCOD extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/cod');
	////sharma	
	if(!empty($this->session->data['pending_order_id'])){
       $pending_order_id = $this->session->data['pending_order_id'];
      }else{
       $pending_order_id = '';
      }
      

//////sharma
        if(isset($this->session->data['centeral_serving'])){
          $head_office_serving = 1;  
        }else{
          $head_office_serving = 0;  
        }

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_cod_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
//////sharma
    
    if ($this->config->get('payment_cod_total') > 0 && $this->config->get('payment_cod_total') > $total) {
			$status = false;
		} elseif (!$this->cart->hasShipping()) {
			$status = false;
		} elseif($head_office_serving == 1){
		   $status = false; 
		}elseif (!$this->config->get('payment_cod_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}



// if($this->config->get('config_name') == 'sincehence eCommerce'){
// 		if ($this->config->get('payment_cod_total') > 0 && $this->config->get('payment_cod_total') > $total ) {
// 			$status = false;
// 		} elseif (!$this->cart->hasShipping()) {
// 			$status = false;
// 		} elseif($head_office_serving == 1){
//             $status = false;  
//         }elseif (!$this->config->get('payment_cod_geo_zone_id')) {
// 			$status = true;
// 		} elseif ($query->num_rows) {
// 			$status = true;
// 		} else {
// 			$status = false;
// 		}
//       }elseif($this->config->get('config_name') == 'sincehence Spoint'){
//       	if ($this->config->get('payment_cod_total') > 0 && $this->config->get('payment_cod_total') > $total) {
// 			$status = false;
// 		} elseif ($this->cart->hasShipping() && !$this->cart->hasPending($pending_order_id)) {
// 			$status = false;
// 		} elseif($head_office_serving == 1){
//             $status = false;  
//         } elseif (!$this->config->get('payment_cod_geo_zone_id')) {
// 			$status = true;
// 		} elseif ($query->num_rows) {
// 			$status = true;
// 		} else {
// 			$status = false;
// 		}
// 	}else{
// 		$status = false;
// 	}
//////sharma
		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'cod',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_cod_sort_order')
			);
		}

		return $method_data;
	}
}
