<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
// Heading
$_['heading_title']                    = 'StorePickup Map';

$_['date_format_short']                = 'Y/m/d';
$_['date_format_long']                 = 'Y/m/d H:i:s';

// Text
$_['text_shipping']                    = 'Shipping';
$_['text_success']                     = 'Success: You have modified StorePickup Map!';

// Entry
$_['entry_module_status']              = 'Module Status';
$_['entry_name']                       = 'Shipping Name';
$_['entry_apikey']                     = 'Google Maps Api Key';
$_['entry_category']                   = 'Category Restrict';
$_['entry_notify_status']              = 'Store Notify';
$_['entry_tax_class']                  = 'Tax Class';
$_['entry_geo_zone']                   = 'Geo Zone';
$_['entry_sort_order']                 = 'Sort Order';
$_['entry_cost_status']                = 'Display Cost Shipping';
$_['entry_distance_status']            = 'Distance Status';
$_['entry_coordinate_status']          = 'Coordinate Status';
$_['entry_filter_status']              = 'Allow Filter Stores';
$_['entry_map_status']                 = 'Display Map';
$_['entry_map_width']                  = 'Popup Width';
$_['entry_map_height']                 = 'Popup Height';
$_['entry_pickup_list_status']         = 'Pickup List';
$_['entry_limit']                      = 'Limit';

// Caption
$_['caption_general']                  = 'General';
$_['caption_appearance']               = 'Appearance';

// Help
$_['help_apikey']                      = '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key?hl=en" target="_blank">Get</a> the Api Key.';
$_['help_category']                    = 'Select product categories for which the shipping method will be displayed or leave blank to show for all products.';
$_['help_notify_status']               = 'Enable it, if you want to send an email to the pick-up store about the order.';
$_['help_distance_status']             = 'Enable it, if you want to show the distance between customer and store.';
$_['help_coordinate_status']           = 'Enable it, if you want to show the coordinates of the store.';
$_['help_filter_status']               = 'Allow customers to filter stores by country, zone and city.';
$_['help_map_status']                  = 'Display link to the list of stores on Google map and allow customers choose the store directly from the map.';
$_['help_pickup_list_status']          = 'Enable it, if you want to show list of stores by customer coordinates. <b>Warning:</b> The customer must enter the correct shipping address.';
$_['help_limit']                       = 'Set the limit of available stores list (0 - unlimited).';

// Tab
$_['tab_setting']                      = 'Module Settings';

// Error
$_['error_permission']                 = 'Warning: You do not have permission to modify module StorePickup Map!';
$_['error_required']                   = 'Warning: Please check the form carefully for errors!';
$_['error_module']                     = 'Module does not exist!';
$_['error_name']                       = 'Shipping Name must be between 2 and 64 characters!';
?>