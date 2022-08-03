<?php
class ModelExtensionPaymentNagadWallet extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/nagad_wallet');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_nagad_wallet_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_nagad_wallet_total') > 0 && $this->config->get('payment_nagad_wallet_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_nagad_wallet_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
		
		if($this->session->data['centeral_serving'] == 'Yes'){
		 $status = true;
		}else{
		 $status = false;  
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'nagad_wallet',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_nagad_wallet_sort_order')
			);
		}

		return $method_data;
	}
}
