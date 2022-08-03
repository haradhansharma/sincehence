<?php
class ModelSaleOrder extends Model {
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

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

			$reward = 0;

			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

			foreach ($order_product_query->rows as $product) {
				$reward += $product['reward'];
			}
			
			$this->load->model('customer/customer');

			$affiliate_info = $this->model_customer_customer->getCustomer($order_query->row['affiliate_id']);

			if ($affiliate_info) {
				$affiliate_firstname = $affiliate_info['firstname'];
				$affiliate_lastname = $affiliate_info['lastname'];
			} else {
				$affiliate_firstname = '';
				$affiliate_lastname = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $order_query->row['store_name'],
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $order_query->row['customer'],
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'firstname'               => $order_query->row['firstname'],
				'lastname'                => $order_query->row['lastname'],
				'email'                   => $order_query->row['email'],
				'telephone'               => $order_query->row['telephone'],
				'custom_field'            => json_decode($order_query->row['custom_field'], true),
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
				'payment_custom_field'    => json_decode($order_query->row['payment_custom_field'], true),
				'payment_method'          => $order_query->row['payment_method'],
				'payment_code'            => $order_query->row['payment_code'],
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
				'shipping_custom_field'   => json_decode($order_query->row['shipping_custom_field'], true),
				'shipping_method'         => $order_query->row['shipping_method'],
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $order_query->row['comment'],
				'total'                   => $order_query->row['total'],
				'reward'                  => $reward,
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $order_query->row['order_status'],
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'affiliate_firstname'     => $affiliate_firstname,
				'affiliate_lastname'      => $affiliate_lastname,
				'commission'              => $order_query->row['commission'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'],
				'user_agent'              => $order_query->row['user_agent'],
				'accept_language'         => $order_query->row['accept_language'],
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			);
		} else {
			return;
		}
	}






	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}
	/////sharma
	if (!empty($data['filter_store_name'])) {
			$sql .= " AND o.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}
		if (!empty($data['filter_payment_trxID'])) {
			$sql .= " AND o.payment_trxID LIKE '%" . $this->db->escape($data['filter_payment_trxID']) . "%'";
		}
	/////sharma
		
		

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'customer',
			///sharma
			'o.store_name',
			'o.payment_trxID',
			////sharma
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
		}

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

/////////sharma

     
        
	   $cart_with_slot_date = $this->db->query("
	   
SELECT c.cart_id, c.product_id, c.refference_order_id, oo.order_id, pov.product_option_value_id, ovd.option_value_id, ovd.slot_date_id FROM `". DB_PREFIX . "cart` c INNER JOIN `". DB_PREFIX . "order_option` oo ON c.refference_order_id = oo.order_id INNER JOIN `". DB_PREFIX . "product_option_value`pov ON pov.product_option_value_id = oo.product_option_value_id INNER JOIN `". DB_PREFIX . "option_value_description` ovd ON ovd.option_value_id = pov.option_value_id WHERE c.refference_order_id > 0 AND ovd.slot_date_id != '' AND ovd.slot_date_id < adddate(curdate(), 2)
	   
	   ");
	   
	   $data_cart[] = array();
        foreach ($cart_with_slot_date->rows as $cart_data) {
           $data_cart[] = array(
               
			'cart_id' => $cart_data['cart_id'],
			'refference_order_id' => $cart_data['refference_order_id'],
			'order_id' => $cart_data['order_id'],
			'slot_date_id' => $cart_data['slot_date_id'],
			'product_id' => $cart_data['product_id']
			
			);
			If(empty($cart_data['cart_id'])){
			    $cart_data['cart_id'] = 0 ;
			}
			If(empty($cart_data['refference_order_id'])){
			    $cart_data['refference_order_id'] = 0 ;
			}
			If(empty($cart_data['order_id'])){
			    $cart_data['order_id'] = 0 ;
			}
			If(empty($cart_data['slot_date_id'])){
			    $cart_data['slot_date_id'] = 0 ;
			}
			If(empty($cart_data['product_id'])){
			    $cart_data['product_id'] = 0 ;
			}			
			
		 $dis_failed_status=$this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order_status` where `name` = 'Distributer Failed' ");
        foreach ($dis_failed_status->rows as $result) {
			$dis_failed_status_id = $result['order_status_id'];

        }
        
        $a=date("Y-m-d");
$date=date_create($a);
date_add($date,date_interval_create_from_date_string("2 days"));
$add_2_date=date_format($date,"Y-m-d");
        
        if($cart_data['slot_date_id'] < $add_2_date){
        $this->db->query("INSERT INTO `". DB_PREFIX . "order_history` SET order_status_id='".$dis_failed_status_id."', order_id='".$cart_data['order_id']."', comment='When a Distributor failed to pick the order within date mentioned in the agrement, this message is generated by system autometically!' ,date_added=NOW()");
        
	        $this->db->query("UPDATE `". DB_PREFIX . "order` SET order_status_id='".$dis_failed_status_id."' where order_id='".$cart_data['order_id']."'");
        
       
        
if($this->db->query("DELETE from `".DB_PREFIX."cart` where product_id = '".$cart_data['product_id']."' ")){
     	$this->db->query("DELETE from `".DB_PREFIX."product` where product_id = '".$cart_data['product_id']."'");
        $this->db->query("DELETE from `".DB_PREFIX."product_description` where product_id = '".$cart_data['product_id']."'");
        $this->db->query("DELETE from `".DB_PREFIX."product_to_category` where product_id = '".$cart_data['product_id']."'");
        $this->db->query("DELETE from `".DB_PREFIX."product_option_value` where product_id = '".$cart_data['product_id']."'");
     }
        }
        
        
			
		}
		

/////////sharma


		return $query->rows;
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
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderVoucherByVoucherId($voucher_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_voucher` WHERE voucher_id = '" . (int)$voucher_id . "'");

		return $query->row;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}
	
	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalOrdersByStoreId($store_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE store_id = '" . (int)$store_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrdersByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByProcessingStatus() {
		$implode = array();

		$order_statuses = $this->config->get('config_processing_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode));

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByCompleteStatus() {
		$implode = array();

		$order_statuses = $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "");

			return $query->row['total'];
		} else {
			return 0;
		}
	}

	public function getTotalOrdersByLanguageId($language_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE language_id = '" . (int)$language_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalOrdersByCurrencyId($currency_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE currency_id = '" . (int)$currency_id . "' AND order_status_id > '0'");

		return $query->row['total'];
	}
	
	public function getTotalSales($data = array()) {
		$sql = "SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order`";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}

	public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalOrderHistories($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalOrderHistoriesByOrderStatusId($order_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_status_id = '" . (int)$order_status_id . "'");

		return $query->row['total'];
	}
	
	public function getEmailsByProductsOrdered($products, $start, $end) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT email FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);

		return $query->rows;
	}

	public function getTotalEmailsByProductsOrdered($products) {
		$implode = array();

		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}

		$query = $this->db->query("SELECT COUNT(DISTINCT email) AS total FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");

		return $query->row['total'];
	}
	
	/////////////sharma for spmaddress in invoive and shipping
	
		public function getSpmcode($order_id,$cusemail){
		
		$query = $this->db->query("SELECT CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) AS spmmethodcode , s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE o.email ='" . $cusemail . "' AND o.order_id = '". $order_id ."' ");

		return $query->rows;
	

	}
	/////////////sharma for spmaddress in invoive and shipping	


	/////

	public function getdatabyorderid($order_id){

		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE order_id='" . $order_id . "'");

		return $query->rows;
	

	}

		public function getdatabyshopemail($shopemail){

		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $shopemail . "'");

		return $query->rows;
	

	}

			public function getdatabycustomeremail($customeremail){

		
		$query = $this->db->query("SELECT s.storepickup_id ,s.name as shop_name ,s.email as shop_email,s.telephone as shop_telephone,s.address as shop_address ,s.city as shop_city ,s.country_id as shop_country_id , cost, s.date_added as order_date, o.order_id,o.store_name , o.store_url,o.customer_id,o.customer_group_id, o.firstname, o.lastname,o.email as customer_email, o.telephone as customer_telephone,o.payment_firstname,o.payment_lastname,o.payment_company,o.payment_address_1,o.payment_address_2,o.payment_city,o.payment_country_id,o.payment_zone,o.payment_zone_id,o.payment_method,o.payment_code,shipping_firstname,shipping_lastname,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_postcode,shipping_country,shipping_country_id,shipping_zone,shipping_zone_id,shipping_method,shipping_code,comment,total,date_modified FROM `" . DB_PREFIX . "spm_store` s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) WHERE s.email='" . $customeremail . "'");

		return $query->rows;
	

	}

	  public function missingactiontakenid(){
	  	$query=$this->db->query("SELECT order_status_id from `".DB_PREFIX."order_status` where name='Missing Action taken'" );
	  	return $query->rows;
	  }
    public function editorderstatusmissingactiontaken($refference_id){

		$get_distributer_email= $this->db->query("SELECT s.email as shop_email  FROM " . DB_PREFIX . "spm_store s LEFT JOIN `" . DB_PREFIX . "order` o ON (CONCAT('storepickup_map.storepickup_map_', CAST(s.storepickup_id AS CHAR)) = o.shipping_code) where order_id='".$refference_id."'");
		
		
			
			 foreach ($get_distributer_email->rows as $result) {
			
			 $distributer_email = $result['shop_email'];	
			

		}
// 		$distributer_customer_id =0;
		$get_distributer_customer_id= $this->db->query("SELECT customer_id from `".DB_PREFIX."customer` where email= '".$distributer_email."'");
			 foreach ($get_distributer_customer_id->rows as $result) {
			$distributer_customer_id = $result['customer_id'];

		}
	

			$this->db->query("UPDATE `".DB_PREFIX."customer` SET `status`='0' where `customer_id`='".$distributer_customer_id."'");
		
	}
	    public function adddatacart($data,$emailandname,$refference_id,$distributer_customer_id) {

            $query=$this->db->query("SELECT * FROM `".DB_PREFIX."order` where order_id ='".$refference_id."'" );
	    	$data['name']=array();
	    	foreach ($query->rows as $result) {
	    	$data['name']=array(
	    	      'firstname' => $result['firstname'],
	    	      'lastname' => $result['lastname'],
	    	      'payment_firstname' => $result['payment_firstname'],
	    	      'payment_lastname' => $result['payment_lastname'],
	    	      'payment_address_1' => $result['payment_address_1'],
	    	      'payment_address_2' => $result['payment_address_2'],
	    	      'payment_city' => $result['payment_city'],
	    	      'payment_country' => $result['payment_country'],
	    	      'shipping_firstname' => $result['shipping_firstname'],
	    	      'shipping_lastname' => $result['shipping_lastname'],
	    	      'shipping_address_1' => $result['shipping_address_1'],
	    	      'shipping_address_2' => $result['shipping_address_2'],
	    	      'shipping_city' => $result['shipping_city'],
	    	      'shipping_country' => $result['shipping_country'],
	    	      'total' => $result['total'],
	    	      'date_added' => $result['date_added'],
	    	      'email' => $result['email']);	
	    	}
	    	
	    	$orderer_email = $data['name']['email'];
	    	$payment_address ='Payment Address:'.'<br/>' .$data['name']['payment_firstname']. ' ' .$data['name']['payment_lastname']. '</br> ' .$data['name']['payment_address_1']. ' </br>' .$data['name']['payment_address_2']. '</br> ' .$data['name']['payment_city']. '</br> ' .$data['name']['payment_country']. '</br> ' ;
	    	$shipping_address = 'Shipping Address'.'<br/>' .$data['name']['shipping_firstname']. ' ' .$data['name']['shipping_lastname']. '</br> ' .$data['name']['shipping_address_1']. ' </br>' .$data['name']['shipping_address_2']. '</br> ' .$data['name']['shipping_city']. '</br> ' .$data['name']['shipping_country']. '</br> ' ;
	    	$order_value = 'Order Value:'.'<br/>' .$data['name']['total'];
	    	$order_created = 'Order Created'.'<br/>' .$data['name']['date_added'];
	    	
	    	$query=$this->db->query("SELECT `name` FROM `".DB_PREFIX."spm_store` WHERE `email` = '".$orderer_email."'" );
	    	$data['storename']=array();
	    	foreach ($query->rows as $result) {
	    	$data['storename']=array(
	    	      'name' => $result['name']);
	    	}
	    	
	    	$spm_store_name = 'Order From:'. '</br>' .$data['storename']['name'];
	    	
	    	
	    	$query=$this->db->query("SELECT * FROM `".DB_PREFIX."order_total` WHERE `code`= 'partial_payment_total' AND order_id ='".$refference_id."'" );
	    	$data['paidpartial']=array();
	    	foreach ($query->rows as $result) {
	    	$data['paidpartial']=array(
	    	      'value' => $result['value']);
	    	}
	    	$retailer_partial_paid ='Retailer Paid:'.'<br/>' .$data['paidpartial']['value'];
	    	
	    	$query=$this->db->query("SELECT DISTINCT `value` FROM `".DB_PREFIX."order_option` WHERE `name`= 'Last Order Date' AND order_id ='".$refference_id."'" );
	    	$data['deadline']=array();
	    	foreach ($query->rows as $result) {
	    	$data['deadline']=array(
	    	      'value' => $result['value']);
	    	}
	    	
	    	$deadline_info = 'last Order Date:' .$data['deadline']['value'];
	    	
	    	$alarming = 'After 2 days of Last order date mentioned, order will remove  automatically from your cart, and you will not be able to make order and order will be marked as Distributor failed!';
	    	
	    	$inform = 'You may click on the button below to know more about this order' ;
            
            $customer_name=$data['name']['firstname']. ' ' .$data['name']['lastname'];
	    	

	    	$button='<a href="index.php?route=account/order_details/detailsinfo&order_id='.$refference_id.'" class="btn btn-info" data-toggle="tooltip" title="Click here to see details of this order!" target="_blank" >Click to see what is inside</a>';
           
	    	$description= '<br/>Order Product Created by:'.'<strong>'.$customer_name.'</strong>'. '</br>'.'</br>' ;
	    	

					$description .= 
			'<table width="70%" border="1px" cellpadding="5">
				<tbody>
				<tr>
						<td style="padding: 10px" width="60%" colspan="2">' . $spm_store_name . '</td>
						
				
				</tr>

				<tr>
						<td style="padding: 10px" width="30%">' . $payment_address . '</td>
						
						<td style="padding: 10px" width="30%">' . $shipping_address . '</td>
						
				</tr>
				<tr>
						<td style="padding: 10px" width="30%">' . $order_value . '</td>
						
						<td style="padding: 10px" width="30%">' . $retailer_partial_paid . '</td>
						
				</tr>
				<tr>
						<td style="padding: 10px" width="30%">' . $order_created . '</td>
					
						<td style="padding: 10px" width="30%">' . $deadline_info . '</td>
						
				</tr>
				<tr>
						<td style="padding: 10px" width="60%" colspan="2">' . $alarming . '</td>
						
			    </tr>
			    <tr>
						<td style="padding: 10px" width="60%" colspan="2">' . $inform . '<br/>'.'<center>' .$button.'</center>'.'</td>
						
			    </tr>



			</tbody>
		</table>';
					
					$description .= '<br />' . 'Thanks in Advance for your co-operation!';
					

	    	

	    	$query=$this->db->query("SELECT category_id from `".DB_PREFIX."category_description` where name='Distributer Franchisee'" );
	    	foreach ($query->rows as $result) {
	    		$category_id=$result['category_id'];
	    	}
	    	$query=$this->db->query("SELECT value from `".DB_PREFIX."order_total` where order_id='".$refference_id."' and  title='Sub-Total'" );
	    	foreach ($query->rows as $result) {
	    		$price=$result['value'];
	    	}
	    	
	    	$name="Order(#".$refference_id.")";
	    	$query=$this->db->query("SELECT model from `".DB_PREFIX."product` where model='".$refference_id."'" );
	    	foreach ($query->rows as $result) {
	    		$model_name=$result['model'];
	    	}
	    	if(empty($model_name)){
	    		$model_name=null;
	    	}


	    	$query=$this->db->query("SELECT model from `".DB_PREFIX."product` where model='".$refference_id."'" );
	    	foreach ($query->rows as $result) {
	    		$model_name=$result['model'];
	    	}

	    	$queryy=$this->db->query("SELECT value from `".DB_PREFIX."order_option` where order_id='".$refference_id."' AND name='Last Order Date'" );
	    	if(!empty($queryy)){
	    	foreach ($queryy->rows as $result) {
	    		$value=$result['value'];
	    	}
	    	}
	    	if(empty($value)){
	    	   	$value= null;
	    	}

	    	$queryz=$this->db->query("SELECT slot_date_id,option_id,option_value_id from `".DB_PREFIX."option_value_description` where name='".$value."'" );
	   // 		if(!empty($queryz)){
	    	foreach ($queryz->rows as $result) {
	    		$slot_date_id=$result['slot_date_id'];
	    		$option_id=$result['option_id'];
	    		$option_value_id=$result['option_value_id'];
	    	}
	    	if(empty($slot_date_id)){
	    	    $slot_date_id=null;
	    	}
	    	if(empty($option_id)){
	    	    $option_id=null;
	    	}
	    	if(empty($option_value_id)){
	    	    $option_value_id=null;
	    	}
	    	
	    	
	   // 		}

 

	    	if($refference_id !=$model_name ){ 



	    $this->db->query("INSERT INTO `".DB_PREFIX."product` SET `model`='".$refference_id."',`quantity`='1',`tax_class_id`='9', `status`='1', price='".$price."', enable_add_to_cart='0',`date_available` = subdate(curdate(), 1), date_added = NOW()");
	    $product_id=$this->db->getLastId();

	    $this->db->query("INSERT INTO `".DB_PREFIX."product_option` SET `product_id`='".$product_id."',`option_id`='".$option_id."',required='1'");
	    $product_option_id=$this->db->getLastId();

	     $this->db->query("INSERT INTO `".DB_PREFIX."product_option_value` SET `product_option_id`='".$product_option_id."',`product_id`='".$product_id."',`option_id`='".$option_id."',`option_value_id`='".$option_value_id."',quantity='1'");

	            $queryzy=$this->db->query("SELECT store_id from `".DB_PREFIX."store` where name='sincehence B2B'" );
         foreach ($queryzy->rows as $result) {
         		$store_id= $result['store_id'];
         	}	
         	
         	$query7=$this->db->query("SELECT customer_group_id from `".DB_PREFIX."customer_group` where sort_order='99999'" );
         	foreach ($query7->rows as $result) {
         		$customer_group_id= $result['customer_group_id'];
         	}
         	$this->db->query("INSERT INTO `".DB_PREFIX."product_to_customer_group` SET `product_id`='".$product_id."',`customer_group_id`='".$customer_group_id."'");


	    $this->db->query("INSERT INTO `".DB_PREFIX."product_to_store` SET `product_id`='".$product_id."',`store_id`='".$store_id."'");

	    $this->db->query("INSERT INTO `".DB_PREFIX."product_description` SET `product_id`='".$product_id."',`language_id`='1',`name`='".$name."', `description`='".$description."',`meta_title`='".$name."'");

	    $this->db->query("INSERT INTO `".DB_PREFIX."product_to_category` SET `product_id`='".$product_id."',`category_id`='".$category_id."'");
	    /////////
	    
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],

					'slot_date_id'            => $product_option_value['slot_date_id'],
		
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
					
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		$option_dataa = array($product_option['product_option_id'] => $product_option_value['product_option_value_id']);
	    



	 	 $this->db->query("INSERT INTO `".DB_PREFIX."cart` SET `customer_id`='".$distributer_customer_id."',`product_id`='" . $product_id . "',`quantity`='1',`option`='".json_encode($option_dataa)."', `refference_spm_with_owner`='".$emailandname."', `session_id`= '', `refference_order_id`='".$refference_id."',date_added = NOW()");
	 	 


	}
	
 ////////sharma to add previous paid
	$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cart` LIKE 'previous_paid'");
	if ($check->num_rows > 0) {
	   
	} else {
	   $this->db->query("ALTER TABLE `" . DB_PREFIX . "cart` ADD `previous_paid` decimal(15,4) NOT NULL");
	}	    
	 	 	    
	 	 $cart_pre_data = array();	    
	     $cart_pre_payment = $this->db->query("SELECT c.product_id as product_id, c.refference_order_id as refference_order_id, ot.value as value FROM `".DB_PREFIX."cart` c LEFT JOIN `".DB_PREFIX."order_total` ot ON c.refference_order_id = ot.order_id WHERE  ot.code = 'partial_payment_total' AND c.customer_id ='".$distributer_customer_id."' ");
        
              foreach ($cart_pre_payment->rows as $cpp_result) {
                  $cart_pre_data[] = array(
           	'product_id' => $cpp_result['product_id'],
           	'refference_order_id' => $cpp_result['refference_order_id'],
           	'value' => $cpp_result['value']

         );
        
$this->db->query("UPDATE `" . DB_PREFIX . "cart` SET `previous_paid`='" . $cpp_result['value'] . "' WHERE `product_id` ='" . $cpp_result['product_id'] . "' AND `refference_order_id` = '" . $cpp_result['refference_order_id'] . "'");
           }
       
 //////sharma to add previous paid


     }



      


		public function getdatarefference_order($order_id) {
	    $query =$this->db->query("SELECT refference_order_id FROM`" . DB_PREFIX . "cart` where `refference_order_id`='".$order_id."'");
	    return $query->rows;
	}
			public function getcustomergroupid() {
	    $query =$this->db->query("SELECT customer_group_id FROM`" . DB_PREFIX . "customer_group` where `sort_order`='99999' and `approval`='1'");
	    return $query->rows;
	}
	    		public function getorderstatusid() {
	    $query =$this->db->query("SELECT order_status_id FROM`" . DB_PREFIX . "order_status` where `name`='Retailer Paid'");
	    return $query->rows;
	}


	    		public function getdistributer_customer_id($shopemail) {
	    $query =$this->db->query("SELECT customer_id,customer_group_id FROM`" . DB_PREFIX . "customer` where `email`='".$shopemail."'");
	    return $query->rows;
	}
	/////
}
