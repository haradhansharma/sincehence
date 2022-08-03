<?php
class ModelExtensionModuleAccountpicture extends Model {
	public function addCustomerPhoto($customer_id, $image, $file_tmp) {

		if($image){
			$pathinfo = pathinfo($image);
			$extension = $pathinfo['extension'];
			$this->db->query("INSERT INTO " . DB_PREFIX . "account_picture SET customer_id = '" . $customer_id . "', extension = '" . $extension . "'");
			$pathinfo = pathinfo($image);
			move_uploaded_file($file_tmp, "catalog/view/theme/default/image/" . $customer_id . '.' . $extension);
		}
	}

	public function getCustomerInfo($customer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer c RIGHT JOIN " . DB_PREFIX . "account_picture i ON (i.customer_id = c.customer_id) WHERE c.customer_id = '" . (int)$customer_id . "'");
		
		return $query->row;
	}

	public function ChangePhoto($image, $file_tmp) {
		if($image){
			$customer_id = $this->customer->getId();
			$pathinfo = pathinfo($image);
			$extension = $pathinfo['extension'];
			$customer_info = $this->getCustomerInfo($customer_id);
			if(isset($customer_info['extension']) && $customer_info['extension']){
				unlink("catalog/view/theme/default/image/" . $customer_id . '.' . $customer_info['extension']);
			}
			$this->db->query("DELETE FROM " . DB_PREFIX . "account_picture WHERE customer_id = '" . $customer_id . "'");
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "account_picture SET customer_id = '" . $customer_id . "', extension = '" . $extension . "'");
			$pathinfo = pathinfo($image);
			move_uploaded_file($file_tmp, "catalog/view/theme/default/image/" . $customer_id . '.' . $extension);
		}
	}	
}
