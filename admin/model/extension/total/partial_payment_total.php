<?php
/* Partial Payment Total for OpenCart v.3.0.x 
 *
* @version 3.2.0
 * @date 15/03/2018
 * @author Kestutis Banisauskas
 * @Smartechas
 */
class ModelExtensionTotalPartialPaymentTotal extends Model {
    
 public function install(){  
 
 
 $query = $this->db->query("DESC `".DB_PREFIX."order` pending_total");
if (!$query->num_rows) {
   $this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `pending_total`  DECIMAL( 15, 4 ) NOT NULL");
}
  
  
  
    }
}





