<?php
class ModelExtensionModuleStorecalculator extends Model {
    private $error = array();
    
    public function getGrandopening(){
        
        $lod_query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
	      foreach ($lod_query->rows as $lod_result) {
	        $go_results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option_value_description` WHERE `option_id` = '".$lod_result['option_id']."' ORDER BY `name` ASC"
	        );
	        return $go_results->rows;
	      }
    }
    public function getTgoname($ovi){
        
        $lod_query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
	      foreach ($lod_query->rows as $lod_result) {
	        $go_results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option_value_description` WHERE `option_value_id` = '".$ovi."' ORDER BY `name` ASC"
	        );
	        return $go_results->rows;
	      }
    }
    
    public function getLod($ovi){
        $lod_query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '999'");
	      foreach ($lod_query->rows as $lod_result) {
       $slot_date_results = $this->db->query("SELECT `slot_date_id` FROM `" . DB_PREFIX . "option_value_description` WHERE `option_value_id` = '".$ovi."'  ");
       foreach($slot_date_results->rows as $slot_date_result){
           $name_query = $this->db->query("SELECT `name` FROM `" . DB_PREFIX . "option_value_description` WHERE `slot_date_id` = '".$slot_date_result['slot_date_id']."' AND `option_id` = '".(int)$lod_result['option_id']."' ");
          }
       
       return $name_query->rows;
    }
    
}


	public function getSpointProduct(){
	    
	    $check_spoint_product = $this->db->query("SELECT p.product_id FROM `". DB_PREFIX ."seo_url` su LEFT JOIN `". DB_PREFIX ."product` p ON su.query = concat('product_id=',p.product_id) where keyword = 'spoint-franchise-product'");
	    return $check_spoint_product->rows;
	}
	
	public function addCaldata($data){
	    
	   $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."calculator_data` (
	  `cd_id` int(11) NOT NULL AUTO_INCREMENT,
	  `primary_product_cost` int(255) NOT NULL,
	  `payable_during_order` int(255) NOT NULL,
	  `payable_before_delivery` int(255) NOT NULL,
	  `product_arraival` varchar(255) NOT NULL,
	  `security` int(255) NOT NULL,
	  `shop_instal_cost` int(255) NOT NULL,
	  `total_capital` int(255) NOT NULL,
	  `target_grandopening` varchar(50) NOT NULL,
	  `your_name` varchar(150) NOT NULL,
	  `your_email` varchar(150) NOT NULL,
	  `mobile` varchar(14) NOT NULL,
	  `targetarea` int(255) NOT NULL,
	  `date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`cd_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	
	$check_customer = $this->db->query("SELECT `email` FROM `".DB_PREFIX."customer`WHERE `email` = '".$data['your_email']."'");
	$check_caldata = $this->db->query("SELECT `your_email` FROM `".DB_PREFIX."calculator_data`WHERE `your_email` = '".$data['your_email']."'");
	if($check_customer->row){
	    $cus_state = 'yes';
	}elseif($check_caldata->row){
	    $cus_state = 'yes';
	}else{
	    $cus_state = 'no';
	}
	
	if($cus_state == 'no'){
	   $this->db->query("INSERT INTO `".DB_PREFIX."calculator_data` SET primary_product_cost = '" . $this->db->escape($data['primary_product_cost']) . "', payable_during_order = '" . $this->db->escape($data['payable_during_order']) . "',payable_before_delivery = '" . $this->db->escape($data['payable_before_delivery']) . "',product_arraival = '" . $this->db->escape($data['pa']) . "',security = '" . $this->db->escape($data['security']) . "',shop_instal_cost = '" . $this->db->escape($data['shop_instal_cost']) . "',total_capital = '" . $this->db->escape($data['total_capital']) . "',target_grandopening = '" . $this->db->escape($data['go_name']) . "',your_name = '" . $this->db->escape($data['your_name']) . "',your_email = '" . $this->db->escape($data['your_email']) . "', targetarea = '" . $this->db->escape($data['tagetarea']) . "', mobile = '" . $this->db->escape($data['mobile']) . "'"); 
	}else{
	   $this->db->query("UPDATE `".DB_PREFIX."calculator_data` SET primary_product_cost = '" . $this->db->escape($data['primary_product_cost']) . "', payable_during_order = '" . $this->db->escape($data['payable_during_order']) . "', payable_before_delivery = '" . $this->db->escape($data['payable_before_delivery']) . "', product_arraival = '" . $this->db->escape($data['pa']) . "', security = '" . $this->db->escape($data['security']) . "', shop_instal_cost = '" . $this->db->escape($data['shop_instal_cost']) . "', total_capital = '" . $this->db->escape($data['total_capital']) . "', target_grandopening = '" . $this->db->escape($data['go_name']) . "', your_name = '" . $this->db->escape($data['your_name']) . "', targetarea = '" . $this->db->escape($data['tagetarea']) . "', mobile = '" . $this->db->escape($data['mobile']) . "' WHERE your_email = '".$data['your_email']."' ");  
	}
	    
	}

}
