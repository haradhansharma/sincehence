<?php
class ModelExtensionModuleAdminLoginSecurity extends Model {
		
	public function createDBTable() {
        
		$query = $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "fx_ip (fx_ip_id INT(11) AUTO_INCREMENT, ip VARCHAR(40), count INT(2), time_check VARCHAR(20), date_added datetime,  date_modified datetime, PRIMARY KEY (fx_ip_id))");  
	}

	public function setIP($ip){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fx_ip WHERE ip = '". $this->db->escape($ip) ."'");
		
		$disabled_time = $this->config->get('module_admin_login_security_disable_time');
		$time_check = strtotime("+".$disabled_time." minutes");
		if($query->num_rows > 0){

			$fx_ip_id = $query->row['fx_ip_id'];

			if( $query->row['count'] >= $this->config->get('module_admin_login_security_false_count')){
				$count = 0;
			}else{
				$count = $query->row['count']+1;
			}
			
			$this->db->query("UPDATE " . DB_PREFIX . "fx_ip SET count = '". (int)$count ."', time_check = '".$time_check."', date_modified = NOW() WHERE fx_ip_id = '".$fx_ip_id."'");
		}else{

			$this->db->query("INSERT INTO " . DB_PREFIX . "fx_ip SET ip = '" . $this->db->escape($ip) . "', count = '1', time_check = '".$time_check."', date_added = NOW(), date_modified = NOW()");
			$count = 1;
		}
		return $count;
	}

	public function loginSuccess($ip){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fx_ip WHERE ip = '". $this->db->escape($ip) ."'");
		
		if($query->num_rows > 0){

			$fx_ip_id = $query->row['fx_ip_id'];
			$this->db->query("UPDATE " . DB_PREFIX . "fx_ip SET count = '0', date_modified = NOW() WHERE fx_ip_id = '".$fx_ip_id."'");
		}
	}

	public function getRemainingCount($ip){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fx_ip WHERE ip = '". $this->db->escape($ip) ."'");
		if($query->num_rows > 0){

			return $query->row;
		}else{

			return 0;
		}
	}
}