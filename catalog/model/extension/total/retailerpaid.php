<?php
class ModelExtensionTotalRetailerpaid extends Model {
   
	public function getTotal($total) {
	    
		$this->load->language('extension/total/retailerpaid');
	if ($this->customer->isLogged() && $this->config->get('config_name') == 'sincehence B2B') {		
    $sql = $this->db->query("SELECT SUM( `value`) as total_value FROM `" . DB_PREFIX . "order_total` WHERE `code` = 'partial_payment_total' AND `order_id` IN (SELECT `refference_order_id` FROM `" . DB_PREFIX . "cart` WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "')");
if($sql->num_rows){
	$retailerpaid = $sql->row['total_value'];
}

		$total['totals'][] = array(
			'code'       => 'retailerpaid',
			'title'      => $this->language->get('text_retailerpaid'),
			'value'      => $retailerpaid,
			'sort_order' => $this->config->get('total_total_sort_order') - 1
		);
	
		$total['total'] -= $retailerpaid;
}
}
}
