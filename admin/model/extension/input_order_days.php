<?php

	class ModelExtensionInputOrderDays extends Model {

		public function install() {


	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'refference_order_id'");
	if ($check->num_rows > 0) {
	   
	} else {
	    $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `refference_order_id` int(30) NOT NULL");
	}
	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE 'enable_add_to_cart'");
	if ($check->num_rows > 0) {
	   
	} else {
	    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `enable_add_to_cart` tinyint(1) NOT NULL");
	}




	$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."input_time_difference` (
	  `itd_id` int(11) NOT NULL AUTO_INCREMENT,
	  `itd_deliverydays` int(11) NOT NULL,
	  `itd_collectiondays` int(11) NOT NULL,
	  `itd_collection_date_slot1` varchar(50) NOT NULL,
	  `itd_collection_date_slot2` varchar(50) ,
	  `date_added` datetime NOT NULL,
	  PRIMARY KEY (`itd_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	
	$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."forcart` (
	  `id` int(50) NOT NULL AUTO_INCREMENT,
	  `option` varchar(500) NOT NULL,
	  `product_id` int(50) NOT NULL,
	  `quantity` int(50) NOT NULL,
	  `customer_id` int(50) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
	

// 	  $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."refference` (
// 	  `order_id` int(20) NOT NULL ,
// 	  `customer_id` int(20) NOT NULL,
// 	  `refference_order_id` int(20) NOT NULL,
// 	  `date_added` datetime NOT NULL
	  
// 	) ");

	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "input_time_difference` LIKE 'icon'");
	if ($check->num_rows > 0) {
	   
	} else {
	   $this->db->query("ALTER TABLE `" . DB_PREFIX . "input_time_difference` ADD `icon` int(11) NOT NULL");
	}

		
	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cart` LIKE 'refference_spm_with_owner'");
	if ($check->num_rows > 0) {
	   
	} else {
	   $this->db->query("ALTER TABLE `" . DB_PREFIX . "cart` ADD `refference_spm_with_owner` varchar(100) NOT NULL");
	}
	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cart` LIKE 'refference_order_id'");
	if ($check->num_rows > 0) {
	   
	} else {
	   $this->db->query("ALTER TABLE `" . DB_PREFIX . "cart` ADD `refference_order_id` int(30) NOT NULL");
	}

	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "option_value_description` LIKE 'slot_date_id'");
	if ($check->num_rows > 0) {
	   
	} else {
	   $this->db->query("ALTER TABLE `" . DB_PREFIX . "option_value_description` ADD `slot_date_id` varchar(30) NOT NULL");
	}


	    

    $query=$this->db->query("SELECT sort_order FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '999'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['sort_order'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}

		if($sqlcheck != '999'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."option` SET `type` = 'select',`sort_order` = '999'");

 $query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '999'");
 
 
        foreach ($query->rows as $result) {
			$sql = $result['option_id'];
		}
		



    $this->db->query("INSERT INTO ". DB_PREFIX ."option_description SET `option_id` = '".$sql."',`name` = 'Last Order Date ',`language_id` = '1'");

	}
		
////Expecting Grand Opening -sharma
$check_option_grand_opening = $this->db->query("SELECT sort_order FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
if(!$check_option_grand_opening->rows){
    $this->db->query("INSERT INTO `". DB_PREFIX ."option` SET `type` = 'select',`sort_order` = '7777'");
   $get_ption_ids = $this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
        foreach ($get_ption_ids->rows as $get_ption_idd) {
			$get_ption_id = $get_ption_idd['option_id'];
		}
  $this->db->query("INSERT INTO ". DB_PREFIX ."option_description SET `option_id` = '".(int)$get_ption_id."',`name` = 'Expecting grand Opening',`language_id` = '1'");
}
////Expecting Grand Opening -sharma


    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "stock_status` where `name` = 'As per last order date' and `language_id` = '1'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		} 
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}

		if($sqlcheck != 'As per last order date'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."stock_status` SET `name` = 'As per last order date',`language_id` = '1'");
		}	

    $query=$this->db->query("SELECT sort_order FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' and `sort_order` = '9999'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['sort_order'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}

		if($sqlcheck != '9999'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."customer_group` SET `approval` = '1',`sort_order` = '9999'");
		}

		$query=$this->db->query("SELECT sort_order FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' and `sort_order` = '99999'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['sort_order'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}

		if($sqlcheck != '99999'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."customer_group` SET `approval` = '1',`sort_order` = '9999'");
			   $this->db->query("INSERT INTO `". DB_PREFIX ."customer_group` SET `approval` = '1',`sort_order` = '99999'");


         $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '9999'");
        foreach ($query->rows as $result) {
			$sql2 = $result['customer_group_id'];
		}
        $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '99999'");
        foreach ($query->rows as $result) {
			$sql3 = $result['customer_group_id'];
		}



        $this->db->query("INSERT INTO `". DB_PREFIX ."customer_group_description` SET `customer_group_id` = '".$sql2."',`language_id` = '1',`name` = 'Retailer franchise',`description` = 'Module generated'");
        $shukriti=$this->db->getLastId();
        $this->db->query("INSERT INTO `". DB_PREFIX ."customer_group_description` SET `customer_group_id` = '".$sql3."',`language_id` = '1',`name` = 'Distributer franchise',`description` = 'Module generated'");
		}

////////////////
	$query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "store` where name = 'sincehence B2B'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}

		if(empty($sqlcheck)){
			$sqlcheck=null;
		}

		if($sqlcheck != 'sincehence B2B'){ 

       $this->db->query("INSERT INTO `". DB_PREFIX ."store` SET name = 'sincehence B2B'");
       $b2b_store_id=$this->db->getLastId();
       $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '" . $b2b_store_id . "', `code` = 'config', `key` = 'config_name', `value` = 'sincehence B2B'");
       $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '" . $b2b_store_id . "', `code` = 'config', `key` = 'config_meta_title', `value` = 'sincehence B2B'");

       }

       	$query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "store` where name = 'sincehence Spoint'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'sincehence Spoint'){ 

	   $this->db->query("INSERT INTO `". DB_PREFIX ."store` SET name = 'sincehence Spoint'");
	    $spoint_store_id=$this->db->getLastId();
       $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '" . $spoint_store_id . "', `code` = 'config', `key` = 'config_name', `value` = 'sincehence Spoint'");
       $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '" . $spoint_store_id . "', `code` = 'config', `key` = 'config_meta_title', `value` = 'sincehence Spoint'");

	    
	   }

	    $query=$this->db->query("SELECT sort_order FROM `" . DB_PREFIX . "category` where status = '1' and sort_order='888'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['sort_order'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != '888'){ 

	   	   $this->db->query("INSERT INTO `". DB_PREFIX ."category` SET status = '1', top = '1',sort_order='888',date_added= NOW()");
	    $category_id_re=$this->db->getLastId();
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_description` SET category_id = '".$category_id_re."',language_id='1', name='Retailer Franchisee',meta_title='Retailer Franchisee'");
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_path` SET category_id = '".$category_id_re."',path_id='".$category_id_re."'");
	    //
        $queryy=$this->db->query("SELECT store_id FROM `" . DB_PREFIX . "store` where  name='sincehence Spoint'");
        if(!empty($queryy)){ 
	    foreach ($queryy->rows as $result) {
			$store_id = $result['store_id'];
		}
        
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_to_store` SET category_id = '".$category_id_re."',store_id='".$store_id."'");
        }
	    $queryyy=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group_description` where  name='Retailer franchise'");
	    if(!empty($queryyy)){ 
	    foreach ($queryyy->rows as $result) {
			$customer_group_id = $result['customer_group_id'];
		}
       
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_to_customer_group` SET category_id = '".$category_id_re."',customer_group_id='".$customer_group_id."'");
	     }
        //

        }


       	$query=$this->db->query("SELECT sort_order FROM `" . DB_PREFIX . "category` where status = '1' and sort_order='8888'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['sort_order'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != '8888'){ 
		$this->db->query("INSERT INTO `". DB_PREFIX ."category` SET status = '1', top = '1', sort_order='8888',date_added= NOW()");
	    $category_id_di=$this->db->getLastId();
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_description` SET category_id = '".$category_id_di."',language_id='1', name='Distributer Franchisee',meta_title='Distributer Franchisee'");
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_path` SET category_id = '".$category_id_di."',path_id='".$category_id_di."'");
	    //
	    $queryy=$this->db->query("SELECT store_id FROM `" . DB_PREFIX . "store` where  name=sincehence B2B'");
       if(!empty($queryy)){ 
	    foreach ($queryy->rows as $result) {
			$store_id = $result['store_id'];
		}
        
	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_to_store` SET category_id = '".$category_id_di."',store_id='".$store_id."'");
	    }

	    $queryyy=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group_description` where  name='Distributer franchise'");
	      if(!empty($queryyy)){ 
	    foreach ($queryyy->rows as $result) {
			$customer_group_id = $result['customer_group_id'];
		}
        

	    $this->db->query("INSERT INTO `". DB_PREFIX ."category_to_customer_group` SET category_id = '".$category_id_di."',customer_group_id='".$customer_group_id."'");
	    }
       
	    
	    }
////////
	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Retailer Paid'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Retailer Paid'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Retailer Paid'");
	    }

	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Distributer Paid'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Distributer Paid'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Distributer Paid'");
	    }

	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Distributer Delivered'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Distributer Delivered'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Distributer Delivered'");
	    }

	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'HeadOffice Delivered'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'HeadOffice Delivered'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'HeadOffice Delivered'");
	    }

	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Distributer Failed'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Distributer Failed'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Distributer Failed'");
	    }

	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Product MIssing'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Product MIssing'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Product MIssing'");
	    }

	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Retailer Failed'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Retailer Failed'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Retailer Failed'");

	    }
	    $query=$this->db->query("SELECT name FROM `" . DB_PREFIX . "order_status` where `language_id` = '1' and `name` = 'Missing Action taken'");
        foreach ($query->rows as $result) {
			$sqlcheck = $result['name'];
		}
		if(empty($sqlcheck)){
			$sqlcheck=null;
		}
		if($sqlcheck != 'Missing Action taken'){ 

		$this->db->query("INSERT INTO `". DB_PREFIX ."order_status` SET `language_id` = '1',`name` = 'Missing Action taken'");

	    }


	   
	 
			
		}



	public function additd( $data) {
	    
	    
	    
		$this->db->query("INSERT INTO `". DB_PREFIX ."input_time_difference` SET `itd_deliverydays`='" . $this->db->escape($data['itd_deliverydays']) . "',`itd_collectiondays`='" . $this->db->escape($data['itd_collectiondays']) . "',`itd_collection_date_slot1`='" . $this->db->escape($data['icd_slot1']) . "',
			date_added = NOW()");



   $query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '999'");
        foreach ($query->rows as $result) {
			$sql = $result['option_id'];
		}



	$this->db->query("INSERT INTO `". DB_PREFIX ."option_value` SET `option_id` = '".$sql."'");


	$option_value_id = $this->db->getLastId();

	$aas=date_create($this->db->escape($data['icd_slot1']));
	$ccs=$this->db->escape($data['itd_deliverydays']);
	date_add($aas,date_interval_create_from_date_string("$ccs days"));
	$bbs=date_format($aas,"Y-m-d");


		$this->db->query("INSERT INTO `". DB_PREFIX ."option_value_description` SET `option_value_id` = '".$option_value_id."',`option_id` = '".$sql."',`name` ='" . $this->db->escape($data['icd_slot1'])   . "<br><u> Estimate delivery: $bbs</u>',`slot_date_id` = '".$this->db->escape($data['icd_slot1'])."', `language_id` = '1' ");
		
	////Expecting Grand Opening -sharma
	
	$get_option_ids = $this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
	
        foreach ($get_option_ids->rows as $get_option_idd) {
			$go_option_id = $get_option_idd['option_id'];
		}
	$this->db->query("INSERT INTO `". DB_PREFIX ."option_value` SET `option_id` = '".$go_option_id."'");
	$get_option_value_id = $this->db->getLastId();
	
	$slot = date_create($this->db->escape($data['icd_slot1']));
	$go_interv = $this->db->escape($data['itd_deliverydays']) + 3;
	date_add($slot,date_interval_create_from_date_string("$go_interv days"));
	$grand_opening_date = date_format($slot,"Y-m-d");
	
	$this->db->query("INSERT INTO `". DB_PREFIX ."option_value_description` SET `option_value_id` = '".$get_option_value_id."',`option_id` = '".$go_option_id."',`name` ='" .$grand_opening_date."',`slot_date_id` = '".$this->db->escape($data['icd_slot1'])."', `language_id` = '1' ");
	
	////Expecting Grand Opening -sharma
	
		////add spoint francishe product --sharma
	
	$check_spoint_product = $this->db->query("SELECT p.product_id FROM `". DB_PREFIX ."seo_url` su LEFT JOIN `". DB_PREFIX ."product` p ON su.query = concat('product_id=',p.product_id) where keyword = 'spoint-franchise-product'");
	if(!$check_spoint_product->rows){
	    
	   $this->db->query("INSERT INTO `". DB_PREFIX ."product` SET `model` = 'SPF1', `quantity` = 1000, `status` = 0, `subtract` = 0 "); 
	   $product_id = $this->db->getLastId();
	   $this->db->query("INSERT INTO `". DB_PREFIX ."product_description` SET `product_id` = '".(int)$product_id."', `language_id` = 1, `name` = 'Sales Point Franchise Product', `meta_title` = 'Sales Point Franchise Product' ");
	   $spoint_query = $this->db->query("SELECT store_id FROM `" . DB_PREFIX . "store` where name = 'sincehence Spoint'");
        foreach ($spoint_query->rows as $spoint) {
			$store_id = $spoint['store_id'];
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$go_option_id . "', required = 1 ");
		$product_option_id = $this->db->getLastId();
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$go_option_id . "', option_value_id = '" .(int)$get_option_value_id . "', quantity = 1000, subtract = 0 ");
		$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = 1, query = 'product_id=" . (int)$product_id . "', keyword = 'spoint-franchise-product'");
	   
	}else{
	    
	    foreach ($check_spoint_product->rows as $spp_id) {
			$sp_product_id = $spp_id['product_id'];
		}
		$optin_in_product = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$sp_product_id . "' AND option_id = '" . (int)$go_option_id . "'  ");
		if(!$optin_in_product->rows){
		    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$sp_product_id . "', option_id = '" . (int)$go_option_id . "', required = 1 ");
		   $product_option_id = $this->db->getLastId();
	 	   $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$sp_product_id . "', option_id = '" . (int)$go_option_id . "', option_value_id = '" .(int)$get_option_value_id . "', quantity = 1000, subtract = 0 ");
		}else{
		foreach ($optin_in_product->rows as $spp_option) {
			$spp_product_option_id = $spp_option['product_option_id'];
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$spp_product_option_id . "', product_id = '" . (int)$sp_product_id . "', option_id = '" . (int)$go_option_id . "', option_value_id = '" .(int)$get_option_value_id . "', quantity = 1000, subtract = 0 ");
	}
	
	
	////add spoint francishe product --sharma
		

	}
	    
	}





			public function getdate($slot1input3) {
		
		$query = $this->db->query("SELECT DATE_FORMAT(itd_collection_date_slot1, '%Y-%m') as ss FROM `" . DB_PREFIX . "input_time_difference` WHERE DATE_FORMAT(itd_collection_date_slot1, '%Y-%m')
			='" . $slot1input3 . "'");

		return $query->rows;
	}

			public function getallslotdate() {
		
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "input_time_difference` ");

		return $query->rows;
	}


	public function slotedit($itd_id, $data) {
	 $preslot_query=$this->db->query("SELECT itd_collection_date_slot1 FROM `" . DB_PREFIX . "input_time_difference` where `itd_id` = '".$itd_id."'");
	 foreach ($preslot_query->rows as $preslot_result) {
			$preslot = $preslot_result['itd_collection_date_slot1'];
		}
	    
	$lod_query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '999'");
	    foreach ($lod_query->rows as $lod_result) {
			$lodoption_id = $lod_result['option_id'];
		}
	$go_query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
	    foreach ($go_query->rows as $go_result) {
			$gooption_id = $go_result['option_id'];
		}
	    
	$lodovi_query=$this->db->query("SELECT option_value_id FROM `" . DB_PREFIX . "option_value_description` where `slot_date_id` = '".$preslot."' AND `option_id` = '".$lodoption_id."' ");
       foreach ($lodovi_query->rows as $lodovi_result) {
			$lodoption_value_id = $lodovi_result['option_value_id'];
		}
	$goovi_query=$this->db->query("SELECT option_value_id FROM `" . DB_PREFIX . "option_value_description` where `slot_date_id` = '".$preslot."' AND `option_id` = '".$gooption_id."' ");
       foreach ($goovi_query->rows as $goovi_result) {
			$gooption_value_id = $goovi_result['option_value_id'];
		}

	$aas=date_create($this->db->escape($data['icd_slot1']));
	$ccs=$this->db->escape($data['itd_deliverydays']);
	date_add($aas,date_interval_create_from_date_string("$ccs days"));
	$bbs=date_format($aas,"Y-m-d");

    $this->db->query("UPDATE `". DB_PREFIX ."option_value_description` SET `name` ='" . $this->db->escape($data['icd_slot1'])   . "<br><u> Estimate delivery: $bbs</u>', slot_date_id='".$this->db->escape($data['icd_slot1'])."',`language_id` = '1'  WHERE `option_value_id` = '".$lodoption_value_id."'");
    
    $slot = date_create($this->db->escape($data['icd_slot1']));
	$go_interv = $this->db->escape($data['itd_deliverydays']) + 3;
	date_add($slot,date_interval_create_from_date_string("$go_interv days"));
	$grand_opening_date = date_format($slot,"Y-m-d");


    $this->db->query("UPDATE `". DB_PREFIX ."option_value_description` SET `name` ='" .$grand_opening_date."',slot_date_id='".$this->db->escape($data['icd_slot1'])."',`language_id` = '1'  WHERE `option_value_id` = '".$gooption_value_id."'");
    
    $this->db->query("UPDATE `" . DB_PREFIX . "input_time_difference` SET `itd_deliverydays`='" . $this->db->escape($data['itd_deliverydays']) . "',`itd_collectiondays`='" . $this->db->escape($data['itd_collectiondays']) . "',`itd_collection_date_slot1`='" . $this->db->escape($data['icd_slot1']) . "',
			date_added = NOW() where  `itd_id`= '".$itd_id."' ");






	}



	public function inactiveoption(){
		    $query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '999'");
       
		return $query->rows;

	}
	public function inactiveoption2(){
		    $query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
       
		return $query->rows;

	}

	public function inactivecustomergroup1(){
		
		 $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '9999'");
		 return $query->rows;
		}

		public function inactivecustomergroup2(){
		
		 $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '99999'");
		 return $query->rows;
		}

		public function inactivestockstatus(){
		
		 $query=  $this->db->query("SELECT stock_status_id FROM `". DB_PREFIX ."stock_status` where `name` = 'As per last order date'");
		 return $query->rows;
		}

		public function inactiveorderstatus1(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Retailer Paid'");
		 return $query->rows;
		}
		public function inactiveorderstatus2(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Distributer Paid'");
		 return $query->rows;
		}
		public function inactiveorderstatus3(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Distributer Delivered'");
		 return $query->rows;
		}
		public function inactiveorderstatus4(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'HeadOffice Delivered'");
		 return $query->rows;
		}
		
		public function inactiveorderstatus5(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Distributer Failed'");
		 return $query->rows;
		}

		public function inactiveorderstatus6(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Product MIssing'");
		 return $query->rows;
		}

		public function inactiveorderstatus7(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Retailer Failed'");
		 return $query->rows;
		}
		public function inactiveorderstatus8(){
		
		 $query=  $this->db->query("SELECT order_status_id FROM `". DB_PREFIX ."order_status` where `name` = 'Missing Action taken'");
		 return $query->rows;
		}
      


	public function uninstall() {


	}






		
	

}
