<?php
class ModelExtensionPos extends Model {
	public function install() {
	$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_customer` (
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_product` (
	`product_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `price` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  `shipping` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `store_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tax_class_id` int(11) NOT NULL,
  `status` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_store` (
   `store_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `phone` text NOT NULL,
  `location` text NOT NULL,
  `status` int(10) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `store_id` int(10) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `image` varchar(255) NOT NULL,
  `code` varchar(40) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `commission` text NOT NULL,
  `commission_value` int(100) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."product_option_value_upc` (
  `product_id` int(11) NOT NULL,
  `product_option_value_id` int(11) NOT NULL,
  `upc` varchar(12) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."pos_holdon` (
  `holdon_id` int(11) NOT NULL AUTO_INCREMENT,
  `holdon_no` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `hold_option` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`holdon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$this->db->query("ALTER TABLE  `".DB_PREFIX."pos_holdon` ADD  `cproduct_id` INT NOT NULL AFTER  `product_id`");

$this->db->query("ALTER TABLE  `".DB_PREFIX."order_product` ADD  `cproduct_id` INT NOT NULL AFTER  `product_id`");

$this->db->query("ALTER TABLE `".DB_PREFIX."product` CHANGE `upc` `upc` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$this->db->query("ALTER TABLE `".DB_PREFIX."product` CHANGE `ean` `ean` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$this->db->query("ALTER TABLE `".DB_PREFIX."product` CHANGE `isbn` `isbn` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");

}
	
	public function uninstall() {
  	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_holdon`");
  	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_customer`");
  	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_order`");
  	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_product`");
  	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_store`");
  	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."pos_user`");
    $this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."product_option_value_upc`");
  	
	}
}
