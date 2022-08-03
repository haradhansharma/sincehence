<?php
class ModelAccountOrder extends Model {
	public function getOrder($order_id) {
	    //////replaced query by shukriti but findout and corrected as req
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' AND order_status_id > '0'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'telephone'               => $order_query->row['telephone'],
				'email'                   => $order_query->row['email'],
				'payment_firstname'       => $order_query->row['payment_firstname'],
				'payment_lastname'        => $order_query->row['payment_lastname'],
				'payment_company'         => $order_query->row['payment_company'],
				'payment_address_1'       => $order_query->row['payment_address_1'],
				'payment_address_2'       => $order_query->row['payment_address_2'],
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $order_query->row['payment_city'],
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $order_query->row['payment_zone'],
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $order_query->row['payment_country'],
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_method'          => $order_query->row['payment_method'],
				'shipping_firstname'      => $order_query->row['shipping_firstname'],
				'shipping_lastname'       => $order_query->row['shipping_lastname'],
				'shipping_company'        => $order_query->row['shipping_company'],
				'shipping_address_1'      => $order_query->row['shipping_address_1'],
				'shipping_address_2'      => $order_query->row['shipping_address_2'],
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $order_query->row['shipping_city'],
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $order_query->row['shipping_zone'],
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $order_query->row['shipping_country'],
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_method'         => $order_query->row['shipping_method'],
				////sharma
				'shipping_code'           => $order_query->row['shipping_code'],
				////sharma
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'order_status_id'         => $order_query->row['order_status_id'],
				'language_id'             => $order_query->row['language_id'],
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'date_modified'           => $order_query->row['date_modified'],
				'date_added'              => $order_query->row['date_added'],
				'ip'                      => $order_query->row['ip']
			);
		} else {
			return false;
		}
	}

	public function getOrders($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT o.order_id, o.firstname, o.lastname,o.store_name, o.store_id, o.telephone, o.email, os.name as status, o.date_added, o.total, o.currency_code, o.currency_value FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id) WHERE o.customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		
		

		

		return $query->rows;
	}
	
		/////////////sharma for spmaddress in invoive and shipping
		
		public function getNewo($new_order) {
		$query = $this->db->query("SELECT DISTINCT `order_id` FROM `gag_order_history` WHERE `order_id` = '".$new_order."' AND `order_status_id` IN (SELECT `order_status_id` FROM `gag_order_status` WHERE `name` = 'Distributer Paid')");
		return $query->rows;
			}
		
		public function getOrderem($order_id) {
		$query = $this->db->query("SELECT `email` FROM `" . DB_PREFIX . "order` where order_id = '". $order_id ."'");
		return $query->rows;
			}
	
		public function getSpmcode($order_id,$cusemail){
		
		$query = $this->db->query("SELECT CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) AS spmmethodcode , s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,currency_code, currency_value, comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE o.email ='" . $cusemail . "' AND o.order_id = '". $order_id ."' AND o.order_status_id > '0'");

		return $query->rows;
	

	}
	public function getRestor($cusemail){
	    
	  	$query = $this->db->query("
	  	
	  	SELECT `name` as s_name FROM `" . DB_PREFIX . "spm_store` WHERE `email` = '" . $cusemail . "'
	  	
	  	");  
	  	return $query->rows;
	}
	

	
	/////////////sharma for spmaddress in invoive and shipping	

	public function getOrderProduct($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getOrderVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderHistories($order_id) {
		$query = $this->db->query("SELECT date_added, os.name AS status, oh.comment , oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added");

		return $query->rows;
	}

	public function getTotalOrders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE customer_id = '" . (int)$this->customer->getId() . "' AND o.order_status_id > '0' AND o.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $query->row['total'];
	}

	public function getTotalOrderProductsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderVouchersByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}













//////////sharma
		public function getOrderded($email, $start = 0, $limit = 20) {
		    
		 if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}
		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name , s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, o.date_added as order_date, o.date_modified as date_modified, o.order_id, (SELECT `new_order` FROM `" . DB_PREFIX . "pre_record` WHERE `pre_order` = o.order_id ) as new_order,
(SELECT `pre_order` FROM `" . DB_PREFIX . "pre_record` WHERE `new_order` = o.order_id ) as pre_order, (SELECT `new_customer` FROM `" . DB_PREFIX . "pre_record` WHERE `pre_order`= o.order_id ) as new_cus, o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total, o.pending_total, o.currency_code, o.currency_value, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, date_modified FROM `" . DB_PREFIX . "spm_store` s RIGHT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $email . "'AND o.order_status_id > '0'  ORDER BY o.order_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		



		return $query->rows;
	}
	public function getTotalOrderess($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "spm_store` s RIGHT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $email . "'AND o.order_status_id > '0' ");

		return $query->row['total'];
	}
/////////sharma
		public function getOrderss($email) {
		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $email . "'AND o.order_status_id > '0'");

		return $query->rows;
	}
///////sharma for spmname start	
			public function getSpmStores($order_id) {
		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $this->customer->getEmail() . "'AND o.order_status_id > '0'");

		return $query->rows;
	}

///////sharma for spmname end




public function getOrderStatus() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status ");

		return $query->row;
	}

	public function getOrderStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));

			if (!$order_status_data) {
				$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$order_status_data = $query->rows;

				$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
			}

			return $order_status_data;
		}
	}




	public function editOrderstatus($data,$order_id) {
		$this->db->query("INSERT INTO `".DB_PREFIX."order_history` SET `order_id`='" . $this->db->escape($data['order_id']) . "',`comment`='" . $this->db->escape($data['comment']) . "',`notify`='" . $this->db->escape($data['notify']) . "',`order_status_id`='" . $this->db->escape($data['order_status_id']) . "',
			date_added = NOW()");
		$this->db->query("UPDATE `".DB_PREFIX."order` SET `order_status_id`='" . $this->db->escape($data['order_status_id']) . "' where order_id='".$this->db->escape($data['order_id'])."'");
	}
		public function editOrderstatus2($data,$order_id,$distributerdeliverdid) {
		$this->db->query("INSERT INTO `".DB_PREFIX."order_history` SET `order_id`='" . $this->db->escape($data['order_id']) . "',`comment`='" . $this->db->escape($data['comment']) . "',`notify`='" . $this->db->escape($data['notify']) . "',`order_status_id`='" . $this->db->escape($data['order_status_id']) . "',
			date_added = NOW()");

		$this->db->query("UPDATE `".DB_PREFIX."order` SET `order_status_id`='" . $this->db->escape($data['order_status_id']) . "' where order_id='".$this->db->escape($data['order_id'])."'");

		$query=$this->db->query("SELECT order_status_id from `".DB_PREFIX."order_status` where name='Complete'" );
		 foreach ($query->rows as $result) {
			$sql = $result['order_status_id'];
		}

		if($distributerdeliverdid==$this->db->escape($data['order_status_id'])){

			$this->db->query("INSERT INTO `".DB_PREFIX."order_history` SET `order_id`='" . $this->db->escape($data['order_id']) . "',`comment`='" . $this->db->escape($data['comment']) . "',`notify`='" . $this->db->escape($data['notify']) . "',`order_status_id`='" . $sql . "', date_added = NOW()");

			$this->db->query("UPDATE `".DB_PREFIX."order` SET `order_status_id`='" . $sql . "' where order_id='".$this->db->escape($data['order_id'])."'");

		}

		$query=$this->db->query("SELECT order_status_id from `".DB_PREFIX."order_status` where name='Retailer Failed'" );
		 foreach ($query->rows as $result) {
			$r_faild_order_status_id = $result['order_status_id'];

		}


			
	}
	public function getorderstatusidforretailerfaild(){
		$query=$this->db->query("SELECT order_status_id from `".DB_PREFIX."order_status` where name='Retailer Failed'" );
		return $query->rows;

	}

//
     


	public function getOrderHistoriess($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM `" . DB_PREFIX . "order_history` oh LEFT JOIN `" . DB_PREFIX . "order_status` os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

/////
			public function getitd($o_c_d) {
		
		$query = $this->db->query("SELECT itd_deliverydays,DATE_FORMAT(itd_collection_date_slot1, '%Y-%m') as ss,itd_collection_date_slot1 FROM `" . DB_PREFIX . "input_time_difference` WHERE DATE_FORMAT(itd_collection_date_slot1, '%Y-%m')
			='" . $o_c_d . "'");

		return $query->rows;
	}

		public function getretailerid(){
		
		 $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '9999'");
		 return $query->rows;
		}

		public function getdistributerid(){
		
		 $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '99999'");
		 return $query->rows;
		}

		public function getcustomergroupidfororder($order_id){
		
		 $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "order` where order_id = '".$order_id."'");
		 return $query->rows;
		}

		public function getdistributerdeliverdid(){
		
		 $query=$this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order_status` where name = 'Distributer Delivered'");
		 return $query->rows;
		}


		public function getretailergroupid(){
		
		 $query=$this->db->query("SELECT customer_group_id FROM `" . DB_PREFIX . "customer_group` where `approval` = '1' AND `sort_order` = '9999'");
		 return $query->rows;
		}
		public function getshippingmethod($order_id){
		
		 $query=$this->db->query("SELECT shipping_method FROM `" . DB_PREFIX . "order` where  `order_id` = '".$order_id."'");
		 return $query->rows;
		}
		
			public function getOrdersss($email,$order_id) {
		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM " . DB_PREFIX . "spm_store s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $email . "' and o.order_id='".$order_id."'");

		return $query->rows;
	}


}