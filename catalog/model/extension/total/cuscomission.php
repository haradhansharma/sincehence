<?php
class ModelExtensionTotalCuscomission extends Model {
	public function getTotal($total) {
		$this->load->language('extension/total/cuscomission');
		
		if($this->customer->isLogged()){
		   $group_id = $this->customer->getGroupId(); 
		}else{
		   $group_id = $this->config->get('config_customer_group_id'); 
		}

		if ( !$this->cart->hasStock() && $this->config->get('config_stock_checkout')) {
		$query = $this->db->query("SELECT `comission` FROM `" . DB_PREFIX . "customer_group` WHERE `customer_group_id`='".(int)$group_id."'");
		if($query->num_rows){
		    $cuscomission = $query->row['comission'];
		}
		
		
    $outlet_sub_total = $this->cart->getOpSubTotal();
	$sub_total =   $outlet_sub_total - $this->cart->getSubTotal();
	
	$comissionamount = ($sub_total*$cuscomission)/100;

		$total['totals'][] = array(
			'code'       => 'cuscomission',
			'title'      => $this->language->get('text_cuscomission').'('.$cuscomission. '%'.')',
			'value'      => $comissionamount,
			'sort_order' => $this->config->get('total_total_sort_order') - 1
		);

		$total['total'] += $comissionamount;
	}
	}
}
