<?php

class ModelExtensionReportsProReport extends Model
{
	// Products Report
	public function getTotalProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product")->rows;
	}

	public function getActiveProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE status = 1 ")->rows;
	}

	public function getOutOfStockProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE quantity <= 0 ")->rows;
	}

	public function getSeoUrlProducts()
	{
		return $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'product_id=%' ")->rows;
	}

	public function getTagProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_description WHERE tag != '' ")->rows;
	}
	
	public function getMetaTitleProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_description WHERE meta_title != '' ")->rows;
	}

	public function getMetaDescriptionProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_description WHERE meta_description != '' ")->rows;
	}

	public function getMetaKeywordProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_description WHERE meta_keyword != '' ")->rows;
	}

	public function getImageCustomTitleProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE image_custom_title != '' ")->rows;
	}

	public function getImageCustomAltProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE image_custom_alt != '' ")->rows;
	}

	public function getH1TagProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_description WHERE description LIKE '%&lt;/h1&gt;%' ")->rows;
	}

	public function getH2TagProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product_description WHERE description LIKE '%&lt;/h2&gt;%' ")->rows;
	}

	public function getImageProducts()
	{
		return $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE image != '' ")->rows;
	}

	

	// Categories Report 
	public function getTotalCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category")->rows;
	}

	public function getActiveCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category WHERE status = 1 ")->rows;
	}


	public function getSeoUrlCategories()
	{
		return $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'category_id=%' ")->rows;
	}

	public function getMetaTitleCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description WHERE meta_title != '' ")->rows;
	}

	public function getMetaDescriptionCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description WHERE meta_description != '' ")->rows;
	}

	public function getMetaKeywordCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description WHERE meta_keyword != '' ")->rows;
	}

	public function getImageCustomTitleCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category WHERE image_custom_title != '' ")->rows;
	}

	public function getImageCustomAltCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category WHERE image_custom_alt != '' ")->rows;
	}

	public function getTagCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description WHERE tag != '' ")->rows;
	}

	public function getH1TagCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category_description WHERE description LIKE '%&lt;/h1&gt;%' ")->rows;
	}

	public function getImageCategories()
	{
		return $this->db->query("SELECT category_id FROM ".DB_PREFIX."category WHERE image != '' ")->rows;
	}

	

	// Information Pages Report 
	public function getTotalInformations()
	{
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."information")->rows;
	}

	public function getActiveInformations()
	{
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."information WHERE status = 1 ")->rows;
	}


	public function getSeoUrlInformations()
	{
		return $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'information_id=%' ")->rows;
	}

	public function getMetaTitleInformations()
	{
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."information_description WHERE meta_title != '' ")->rows;
	}

	public function getMetaDescriptionInformations()
	{
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."information_description WHERE meta_description != '' ")->rows;
	}

	public function getMetaKeywordInformations()
	{
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."information_description WHERE meta_keyword != '' ")->rows;
	}

	public function getH1TagInformations()
	{
		return $this->db->query("SELECT information_id FROM ".DB_PREFIX."information_description WHERE description LIKE '%&lt;/h1&gt;%' ")->rows;
	}


	// Manufacturers Report
	public function getTotalManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer")->rows;
	}

	public function getSeoUrlManufacturers()
	{
		return $this->db->query("SELECT query FROM ".DB_PREFIX."seo_url WHERE query LIKE 'manufacturer_id=%' ")->rows;
	}

		public function getMetaTitleManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE meta_title != '' ")->rows;
	}

	public function getMetaDescriptionManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE meta_description != '' ")->rows;
	}

	public function getMetaKeywordManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE meta_keyword != '' ")->rows;
	}

	public function getImageCustomTitleManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE image_custom_title != '' ")->rows;
	}

	public function getImageCustomAltManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE image_custom_alt != '' ")->rows;
	}

	public function getTagManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE tag != '' ")->rows;
	}

	public function getH1TagManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE description LIKE '%&lt;/h1&gt;%' ")->rows;
	}

	public function getImageManufacturers()
	{
		return $this->db->query("SELECT manufacturer_id FROM ".DB_PREFIX."manufacturer WHERE image != '' ")->rows;
	}

	

	// Orders Report
	public function getTotalOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order")->rows;
	}

	public function getTotalPendingOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 1")->rows;	
	}

	public function getTotalProcessingOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 2")->rows;	
	}

	public function getTotalShippedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 3")->rows;	
	}

	public function getTotalCompleteOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 5")->rows;	
	}

	public function getTotalCanceledOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 7")->rows;	
	}

	public function getTotalDeniedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 8")->rows;	
	}

	public function getTotalCanceledReversalOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 9")->rows;	
	}

	public function getTotalFailedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 10")->rows;	
	}

	public function getTotalRefundedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 11")->rows;	
	}

	public function getTotalReversedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 12")->rows;	
	}

	public function getTotalChargebackOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 13")->rows;	
	}

	public function getTotalExpiredOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 14")->rows;	
	}

	public function getTotalProcessedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 15")->rows;	
	}

	public function getTotalVoidedOrders()
	{
		return $this->db->query("SELECT order_id FROM ".DB_PREFIX."order WHERE order_status_id = 16")->rows;	
	}



}