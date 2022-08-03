<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
$_['spm_module_type']  = 'shipping';
$_['spm_module_name']  = 'shipping_storepickup_map';
$_['spm_module_path']  = 'extension/shipping/storepickup_map';
$_['spm_module_model'] = 'model_extension_shipping_storepickup_map';
$_['spm_fields'] = array(
	'status'             => array('default' => '0', 'decode' => false, 'required' => true),
	'name'               => array('default' => '', 'decode' => false, 'required' => true),
	'apikey'             => array('default' => '', 'decode' => false, 'required' => false),
	'categories'         => array('default' => array(), 'decode' => false, 'required' => false),
	'notify_status'      => array('default' => '0', 'decode' => false, 'required' => false),
	'sort_order'         => array('default' => '0', 'decode' => false, 'required' => false),
	'tax_class_id'       => array('default' => '', 'decode' => false, 'required' => false),
	'geo_zone_id'        => array('default' => '', 'decode' => false, 'required' => false),
	'cost_status'        => array('default' => '0', 'decode' => false, 'required' => false),
	'distance_status'    => array('default' => '0', 'decode' => false, 'required' => false),
	'distance_unit'      => array('default' => 'k', 'decode' => false, 'required' => false),
	'coordinate_status'  => array('default' => '0', 'decode' => false, 'required' => false),
	'filter_status'      => array('default' => '0', 'decode' => false, 'required' => false),
	'map_status'         => array('default' => '0', 'decode' => false, 'required' => false),
	'map_width'          => array('default' => '400px', 'decode' => false, 'required' => false),
	'map_height'         => array('default' => '300px', 'decode' => false, 'required' => false),
	'pickup_list_status' => array('default' => '0', 'decode' => false, 'required' => false),
	'limit'              => array('default' => '0', 'decode' => false, 'required' => false)
);
$_['spm_units'] = array(
	'k' => array('km', 1.60934),
	'm' => array('miles', 1)
);
$_['spm_menu'] = array(
	array(
		'name'   => 'Stores',
		'action' => $_['spm_module_path'] . '/store'
	),
	array(
		'name'   => '',
		'action' => ''
	),
	array(
		'name'   => 'Reports',
		'action' => $_['spm_module_path'] . '/report'
	),
	array(
		'name'   => 'Settings',
		'action' => $_['spm_module_path']
	)
);