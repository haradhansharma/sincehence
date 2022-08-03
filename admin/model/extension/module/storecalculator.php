<?php
class ModelExtensionModuleStoreCalculator extends Model {
    
    public function getGrandopening(){
        
        $lod_query=$this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` where `type` = 'select' AND `sort_order` = '7777'");
	      foreach ($lod_query->rows as $lod_result) {
	        $go_results = $this->db->query("SELECT * FROM `sh_option_value_description` WHERE `option_id` = '".$lod_result['option_id']."' ORDER BY `name` ASC"
	        );
	        return $go_results->rows;
	      }
        
        
    }
    	public function getCalculators($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "calculator_data";
		

		
		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "your_name LIKE '%" . $this->db->escape($data['filter_your_name']) . "%'";
		}

		if (!empty($data['filter_your_email'])) {
			$implode[] = "your_email LIKE '" . $this->db->escape($data['filter_your_email']) . "%'";
		}



		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'your_name',
			'your_email',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY your_name";
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
	}
	
	public function getTotalCalculators($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "calculator_data";

		$implode = array();

		if (!empty($data['filter_your_name'])) {
			$implode[] = "your_name LIKE '%" . $this->db->escape($data['filter_your_name']) . "%'";
		}

		if (!empty($data['filter_your_email'])) {
			$implode[] = "your_email LIKE '" . $this->db->escape($data['filter_your_email']) . "%'";
		}



		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	  public function getCalculatordatas($email) {
		$sql = "SELECT * FROM " . DB_PREFIX . "calculator_data WHERE your_email = '".$email."' ";


		$query = $this->db->query($sql);

		return $query->rows;
	}
		public function getSpointProduct(){
	    
	    $check_spoint_product = $this->db->query("SELECT p.product_id FROM `". DB_PREFIX ."seo_url` su LEFT JOIN `". DB_PREFIX ."product` p ON su.query = concat('product_id=',p.product_id) where keyword = 'spoint-franchise-product'");
	    return $check_spoint_product->rows;
	}
    


}
