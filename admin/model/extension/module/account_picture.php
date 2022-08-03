<?php
class ModelExtensionModuleAccountpicture extends Model {
	public function createTable() {
		$create_table = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "account_picture` (
             `account_picture_id` int(11) NOT NULL AUTO_INCREMENT,             
             `customer_id` int(11) NOT NULL,
			 `extension` varchar(255) NOT NULL,
			 PRIMARY KEY (`account_picture_id`)) ENGINE=MyISAM COLLATE=utf8_general_ci;";
        
        $this->db->query($create_table);
	}
	
	public function dropTable() {
		$drop_table = "DROP TABLE IF EXISTS " . DB_PREFIX . "account_picture";
		$this->db->query($drop_table);
    }
}
