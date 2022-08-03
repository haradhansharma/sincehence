<?php
// Heading
$_['heading_title']         = 'False Admin Login Security';

// Text
$_['text_extension']        = 'Extensions';
$_['text_success']          = 'Success: You have modified False Admin Login Security module!';
$_['text_edit']             = 'Edit False Admin Login Security Module';

// Entry
$_['entry_false_count']     = 'Login try count';
$_['entry_disable_time']    = 'Login disable time';
$_['entry_disable_time_ph'] = 'Login disable time in minutes';
$_['entry_status']          = 'Status';


$_['column_ip']             = 'IP Address';
$_['column_access']         = 'Status';
$_['column_date']           = 'Date Added';
$_['column_action']         = 'Action';

$_['modal_ip']              = 'IP Address';
$_['modal_ip_pl']           = 'i.e. 192.167.5.4';
$_['modal_type']            = 'Access Type';
$_['modal_title']           = 'Add New IP Address';
$_['modal_save']            = 'Add IP Address';

$_['tooltip_false_count']   = 'The count after admin login page will be disabled. Default count is 3.';
$_['tooltip_disable']       = 'The time for which admin login page will be disable. Default time is 10 minutes.';
$_['tooltip_blacklist']     = 'The fail login count after user IP address will be added into the IP black list. Default count is 5 and it should be greater than login try count.';
$_['tooltip_email']         = 'The email will be sent to the Admin email address on every fail login attempt.';
$_['tooltip_filter']        = 'Enable or Disable IP Black List or IP white list.';
$_['tooltip_status']        = 'Enable or Disable alse Admin Login Security module status.';

// Error
$_['error_login']           = 'No match for Username or Password. You have cnt login attempts remaining.';

$_['error_black_list']      = '<p style="background: #f6f6f6;padding: 5px;">Seems like your IP address is not added into the Allowed IP list so please add your IP address into the allowed IP List from <a href="XXX" target="_blank" >here</a> then you can enable this option. Please make sure you have <a href="https://vpnoverview.com/vpn-information/what-is-dedicated-ip/" target="_blank">dedicated IP Address</a> for your network to enable this option</p>';