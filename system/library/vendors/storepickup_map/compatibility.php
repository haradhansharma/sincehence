<?php
/*
  Opencart Version Compatibility
  Version 1.3
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
if (!class_exists('OVCompatibility_13')) {
	final class OVCompatibility_13 {
		private static $VERSION = '1.3';
		private static $APP = null;

		private $registry;

		public function __construct($registry) {
			$this->registry = $registry;
		}

		public function setApp($application) {
			self::$APP = $application;
		}

		public function view($file, $data) {
			$template_directory = '';

			if (self::$APP == 'admin') {
				$template_directory = '';

				if (version_compare(VERSION, '3', '<')) {
					$template_extension = 'tpl';
				} elseif (is_file(DIR_TEMPLATE . $file . '.twig')) {
					$template_extension = 'twig';
				} else {
					$template_extension = 'tpl';
				}
			} else {
				if (version_compare(VERSION, '3', '<')) {
					$template_extension = 'tpl';

					if (file_exists(DIR_TEMPLATE . $this->registry->get('config')->get('config_template') . '/template/' . $file . '.tpl')) {
						$template_directory = $this->registry->get('config')->get('config_template') . '/template/';
					} else {
						$template_directory = 'default/template/';
					}
				} else {
					if ($this->registry->get('config')->get('config_theme') == 'default') {
						$theme = $this->registry->get('config')->get('theme_default_directory');
					} else {
						$theme = $this->registry->get('config')->get('config_theme');
					}

					if (is_file(DIR_TEMPLATE . $theme . '/template/' . $file . '.twig')) {
						$template_directory = $theme . '/template/';

						$template_extension = 'twig';
					} elseif (is_file(DIR_TEMPLATE . 'default/template/' . $file . '.twig')) {
						$template_directory = 'default/template/';

						$template_extension = 'twig';
					} elseif (is_file(DIR_TEMPLATE . $theme . '/template/' . $file . '.tpl')) {
						$template_directory = $theme . '/template/';

						$template_extension = 'tpl';
					} elseif (is_file(DIR_TEMPLATE . 'default/template/' . $file . '.tpl')) {
						$template_directory = 'default/template/';

						$template_extension = 'tpl';
					} else {
						//template do not exists
						$template_extension = '';
					}
				}
			}

			if (is_file(DIR_TEMPLATE . $template_directory . $file . '.' . $template_extension)) {
				if ($template_extension == 'twig') {
					$reflection = new ReflectionClass('Template');

					if ($reflection->getConstructor()->getNumberOfParameters() == 1) {
						$template = new Template('twig');
					} else {
						$template = new Template('twig', $this->registry);
					}

					foreach ($data as $key => $value) {
						$template->set($key, $value);
					}

					$output = $template->render($template_directory . $file);
				} else {
					extract($data);

					ob_start();

					if (class_exists('VQMod')) {
						require(VQMod::modCheck(DIR_TEMPLATE . $template_directory . $file . '.' . $template_extension));
					} else {
						require(DIR_TEMPLATE . $template_directory . $file . '.' . $template_extension);
					}

					$output = ob_get_clean();
				}
			} else {
				trigger_error('Error: Could not load template ' . $file . '.' . $template_extension . '!');
				exit();
			}

			return $output;
		}

		public function getChild($child, $args = array()) {
			if (!$child) {
				return '';
			} else {
				if (version_compare(VERSION, '2.0', '<')) {
					$action = new Action($child, $args);

					if (file_exists($action->getFile())) {
						if (class_exists('VQMod')) {
							include_once (VQMod::modCheck($action->getFile()));
						} else {
							include_once($action->getFile());
						}

						$class = $action->getClass();

						$controller = new $class($this->registry);

						$reflection = new ReflectionClass($class);

						if ($reflection->hasMethod($action->getMethod()) && $reflection->getMethod($action->getMethod())->getNumberOfRequiredParameters() <= count($args)) {
							$output = call_user_func_array(array($controller, $action->getMethod()), array($args));

							if ($reflection->hasProperty("output")) {
								$property = $reflection->getProperty('output');
								$property->setAccessible(true);
								$result = $property->getValue($controller);

								if ($result) {
									$output = $result;
								}
							}

							return $output;
						} else {
							trigger_error('Error: Could not load controller ' . $child . '!');
							exit();
						}
					} else {
						trigger_error('Error: Could not load controller ' . $child . '!');
						exit();
					}
				} else {
					return $this->registry->get('load')->controller($child, $args);
				}
			}
		}

		public function getChildren($routes = array()) {
			if (!$routes) {
				if (self::$APP == 'admin') {
					if (version_compare(VERSION, '2.0', '<')) {
						$routes = array(
							'footer'      => 'common/footer',
							'column_left' => '',
							'header'      => 'common/header'
						);
					} else {
						$routes = array(
							'header'      => 'common/header',
							'column_left' => 'common/column_left',
							'footer'      => 'common/footer'
						);
					}
				} else {
					$routes = array(
						'column_left'    => 'common/column_left',
						'column_right'   => 'common/column_right',
						'content_top'    => 'common/content_top',
						'content_bottom' => 'common/content_bottom',
						'footer'         => 'common/footer',
						'header'         => 'common/header'
					);
				}
			}

			$children = array();

			foreach ($routes as $key => $child) {
				$children[$key] = $this->getChild($child);
			}

			return $children;
		}

		public function mail() {
			if (version_compare(VERSION, '2', '>=')) {
				if (version_compare(VERSION, '2.0.2', '<')) {
					$mail = new Mail($this->registry->get('config')->get('config_mail'));
				} else {
					if (version_compare(VERSION, '3', '<')) {
						$mail = new Mail();
						$mail->protocol = $this->registry->get('config')->get('config_mail_protocol');
					} else {
						$mail = new Mail($this->registry->get('config')->get('config_mail_engine'));
					}

					$mail->parameter = $this->registry->get('config')->get('config_mail_parameter');
					$mail->smtp_hostname = $this->registry->get('config')->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->registry->get('config')->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->registry->get('config')->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->registry->get('config')->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->registry->get('config')->get('config_mail_smtp_timeout');
				}
			} else {
				$mail = new Mail();
				$mail->protocol = $this->registry->get('config')->get('config_mail_protocol');
				$mail->parameter = $this->registry->get('config')->get('config_mail_parameter');
				$mail->hostname = $this->registry->get('config')->get('config_smtp_host');
				$mail->username = $this->registry->get('config')->get('config_smtp_username');
				$mail->password = $this->registry->get('config')->get('config_smtp_password');
				$mail->port = $this->registry->get('config')->get('config_smtp_port');
				$mail->timeout = $this->registry->get('config')->get('config_smtp_timeout');
			}

			return $mail;
		}

		public function getLanguages($data = array()) {
			$languages = array();

			$sql = "SELECT * FROM `" . DB_PREFIX . "language`";

			$implode = array();

			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$implode[] = "status = '" . (int)$data['filter_status'] . "'";
			}

			if ($implode) {
				$sql .= " WHERE " . implode(" AND ", $implode);
			}

			$sql .= " ORDER BY sort_order, name";

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->registry->get('db')->query($sql);

			foreach ($query->rows as $language) {
				$languages[$language['language_id']] = $language;

				if (version_compare(VERSION, '2.2', '<')) {
					$languages[$language['language_id']]['image'] = ((self::$APP == 'admin') ? 'view/' : '') . 'image/flags/' . $language['image'];
				} else {
					$languages[$language['language_id']]['image'] = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
				}
			}

			return (array)$languages;
		}

		public function getStores($route = '') {
			$stores = array();

			if (self::$APP == 'admin') {
				$token_name = $this->getAdminTokenName();
				$token = $token_name . '=' . $this->getSessionValue($token_name);

				$url = HTTP_CATALOG;
				$ssl = defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG;
				$filter = $this->link($route, $token . '&filter_store_id=0');
			} else {
				$token = '';

				$url = HTTP_SERVER;
				$ssl = defined('HTTPS_SERVER') ? HTTPS_SERVER : HTTP_SERVER;
				$filter = $url;
			}

			$stores[0] = array('store_id' => '0', 'url' => $url, 'ssl' => $ssl, 'filter' => $filter, 'name' => 'Default');

			$query = $this->registry->get('db')->query("SELECT * FROM `" . DB_PREFIX . "store` ORDER BY url");

			foreach ($query->rows as $store) {
				$url = preg_match('#^http#i', $store['url']) ? $store['url'] : 'http://' . $store['url'];
				$ssl = ($store['ssl'] && !preg_match('#^http#i', $store['ssl'])) ? 'https://' . $store['ssl'] : $store['ssl'];

				if (self::$APP == 'admin') {
					$filter = $this->link($route, $token . '&filter_store_id=' . $store['store_id']);
				} else {
					$filter = $url;
				}

				$stores[$store['store_id']] = array(
					'store_id'   => $store['store_id'],
					'url'        => $url,
					'ssl'        => $ssl ? $ssl : $url,
					'filter'     => $filter,
					'name'       => $store['name']
				);
			}

			return (array)$stores;
		}

		public function getParams($data, $allow = array()) {
			$param = '';

			if ($data) {
				foreach ($data as $key => $value) {
					if (!$allow || in_array($key, $allow)) {
						$param .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
					}
				}
			}

			return $param;
		}

		public function link($route, $params = '', $ssl = true) {
			if (!$route) {
				$route = 'common/home';
			}

			return $this->registry->get('url')->link($route, $params, ($ssl ? (version_compare(VERSION, '2.0', '<') ? 'SSL' : true) : (version_compare(VERSION, '2.0', '<') ? 'NONSSL' : false)));
		}

		public function mathCaptcha($name = 'math_captcha', $format = '{digit_1} {operator} {digit_2}') {
			$symbols = array('+', '-', '*');

			$digit_1 = mt_rand(1, 20);
			$digit_2 = mt_rand(0, 20);
			$operator = $symbols[mt_rand(0, count($symbols) - 1)];

			switch ($operator) {
				case '+':
					$result = ($digit_1 + $digit_2);
					break;
				case '-':
					$result = ($digit_1 - $digit_2);
					break;
				case '*':
					$result = ($digit_1 * $digit_2);
					break;
			}

			$this->registry->get('session')->data[$name] = $result;

			return str_replace(array('{digit_1}', '{digit_2}', '{operator}'), array($digit_1, $digit_2, $operator), $format);
		}

		public function validateCaptcha($value = '', $name = 'captcha', $math_captcha = false) {
			$validate = true;

			if ($math_captcha || version_compare(VERSION, '2.1', '<')) {
				$session_value = $this->getSessionValue($name);

				if (empty($session_value) || ($session_value != $value)) {
					$validate = false;
				}
			} else {
				$error = $this->getChild((version_compare(VERSION, '2.3', '<') ? '' : 'extension/') . 'captcha/' . $this->registry->get('config')->get('config_captcha') . '/validate');

				if ($error) {
					$validate = false;
				}
			}

			return $validate;
		}

		public function pagination($total, $page, $limit, $url) {
			if (version_compare(VERSION, '3.1', '<')) {
				$pagination = new Pagination();
				$pagination->total = $total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->url = $url;

				return $pagination->render();
			} else {
				return $this->getChild('common/pagination', array(
					'total' => $total,
					'page'  => $page,
					'limit' => $limit,
					'url'   => $url
				));
			}
		}

		public function paginationText($page, $limit, $total) {
			return version_compare(VERSION, '2.0', '<') ? '' : sprintf($this->registry->get('language')->get('text_pagination'), ($total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit));
		}

		public function paginationLimit($limit = 20, $override = false) {
			if ($override) {
				return $limit;
			} else {
				if (self::$APP == 'admin') {
					return ($this->registry->get('config')->get('config_admin_limit')) ? $this->registry->get('config')->get('config_admin_limit') : ($this->registry->get('config')->get('config_limit_admin') ? $this->registry->get('config')->get('config_limit_admin') : $limit);
				} else {
					if ($this->registry->get('config')->get('theme_' . $this->registry->get('config')->get('config_theme') . '_product_limit')) {
						return $this->registry->get('config')->get('theme_' . $this->registry->get('config')->get('config_theme') . '_product_limit');
					} elseif ($this->registry->get('config')->get($this->registry->get('config')->get('config_theme') . '_product_limit')) {
						return $this->registry->get('config')->get($this->registry->get('config')->get('config_theme') . '_product_limit');
					} elseif ($this->registry->get('config')->get('config_product_limit')) {
						return $this->registry->get('config')->get('config_product_limit');
					} elseif ($this->registry->get('config')->get('config_catalog_limit')) {
						return $this->registry->get('config')->get('config_catalog_limit');
					} else {
						return $limit;
					}
				}
			}
		}

		public function redirect($url, $status = 302) {
			header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, $status);
			header("Connection: close");

			exit();
		}

		public function getSessionValue($name) {
			return isset($this->registry->get('session')->data[$name]) ? $this->registry->get('session')->data[$name] : '';
		}

		public function getCookieValue($name) {
			return isset($this->registry->get('request')->cookie[$name]) ? $this->registry->get('request')->cookie[$name] : '';
		}

		public function getAdminTokenName() {
			return version_compare(VERSION, '3', '>=') ? 'user_token' : 'token';
		}

		public function getNoImage() {
			return version_compare(VERSION, '2.0', '<') ? 'no_image.jpg' : 'no_image.png';
		}

		public function loadStyles($module) {
			if (version_compare(VERSION, '2.0', '<')) {
				$this->registry->get('document')->addStyle('../system/library/vendors/' . $module . '/font-awesome/css/font-awesome.min.css?ver=' . self::$VERSION);
				$this->registry->get('document')->addScript('../system/library/vendors/' . $module . '/bootstrap/js/bootstrap.min.js?ver=' . self::$VERSION);
				$this->registry->get('document')->addScript('../system/library/vendors/' . $module . '/admin.js?ver=' . self::$VERSION);
				$this->registry->get('document')->addStyle('../system/library/vendors/' . $module . '/bootstrap/css/bootstrap.css?ver=' . self::$VERSION);
				$this->registry->get('document')->addStyle('../system/library/vendors/' . $module . '/compatibility.css?ver=' . self::$VERSION);
				$this->registry->get('document')->addScript('../system/library/vendors/' . $module . '/datetimepicker/moment.js?ver=' . self::$VERSION);
				$this->registry->get('document')->addScript('../system/library/vendors/' . $module . '/datetimepicker/bootstrap-datetimepicker.min.js?ver=' . self::$VERSION);
				$this->registry->get('document')->addStyle('../system/library/vendors/' . $module . '/datetimepicker/bootstrap-datetimepicker.min.css?ver=' . self::$VERSION);
			}

			$this->registry->get('document')->addStyle('../system/library/vendors/' . $module . '/admin.css?ver=' . self::$VERSION);
		}

		public function getDomain() {
			$host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : (!empty($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_HOST) : ''));

			return (substr($host, -1) != '/') ? $host .= '/' : $host;
		}

		public function isHttps() {
			return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'));
		}

		public function jquery($text) {
			return str_replace(array('view/javascript/jquery/jquery-1.6.1.min.js', 'catalog/view/javascript/jquery/jquery-1.7.1.min.js', 'view/javascript/jquery/jquery-1.7.1.min.js', 'http://code.jquery.com/jquery-1.7.2.min.js', '../catalog/view/javascript/jquery/jquery-1.7.2.min.js', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'), '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', $text);
		}

		public function calculateFilesize($size) {
			$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
			$power = $size > 0 ? floor(log($size, 1024)) : 0;

			return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
		}

		public function proportionalResize($width, $height, $new_width = false, $new_height = false) {
			$old_ratio = $width / $height;

			if ($new_width === false && $new_height === false) {
				return false;
			} elseif ($new_width === false) {
				$new_width = $new_height * $old_ratio;
			} elseif ($new_height === false) {
				$new_height = $new_width / $old_ratio;
			}

			$new_ratio = $new_width / $new_height;

			if ($new_ratio == $old_ratio) {
				
			} elseif ($new_ratio < $old_ratio) {
				$new_height = $new_width / $old_ratio;
			} elseif ($new_ratio > $old_ratio) {
				$new_width = $new_height * $old_ratio;
			}

			return array(intval(round($new_width)), intval(round($new_height)));
		}

		/* Setting */
		public function getSetting($group, $store_id = 0) {
			$data = array();

			$query = $this->registry->get('db')->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `" . (version_compare(VERSION, '2.0.1', '<') ? 'group' : 'code') . "` = '" . $this->registry->get('db')->escape($group) . "'");

			foreach ($query->rows as $result) {
				$result['key'] = str_replace($group . '_', '', $result['key']);

				if (!$result['serialized']) {
					$data[$result['key']] = $result['value'];
				} else {
					if (version_compare(VERSION, '2.1', '>')) {
						$data[$result['key']] = json_decode($result['value'], true);
					} else {
						$data[$result['key']] = @unserialize($result['value']);
					}
				}
			}

			return $data;
		}

		public function getSettingValue($key, $store_id = 0) {
			$query = $this->registry->get('db')->query("SELECT value, serialized FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->registry->get('db')->escape($key) . "'");

			if ($query->num_rows) {
				if (!$result['serialized']) {
					return $query->row['value'];
				} else {
					if (version_compare(VERSION, '2.1', '>')) {
						return json_decode($query->row['value'], true);
					} else {
						return @unserialize($query->row['value']);
					}
				}
			} else {
				return null;
			}
		}

		public function editSetting($group, $data, $store_id = 0) {
			$this->deleteSetting($group, $store_id);

			foreach ($data as $key => $value) {
				if (!is_array($value)) {
					$this->registry->get('db')->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '" . (int)$store_id . "', `" . (version_compare(VERSION, '2.0.1', '<') ? 'group' : 'code') . "` = '" . $this->registry->get('db')->escape($group) . "', `key` = '" . $this->registry->get('db')->escape($group . '_' . $key) . "', `value` = '" . $this->registry->get('db')->escape($value) . "'");
				} else {
					$this->registry->get('db')->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '" . (int)$store_id . "', `" . (version_compare(VERSION, '2.0.1', '<') ? 'group' : 'code') . "` = '" . $this->registry->get('db')->escape($group) . "', `key` = '" . $this->registry->get('db')->escape($group . '_' . $key) . "', `value` = '" . (version_compare(VERSION, '2.1', '>') ? $this->registry->get('db')->escape(json_encode($value)) : $this->registry->get('db')->escape(serialize($value))) . "', serialized = '1'");
				}
			}
		}

		public function editSettingValue($key = '', $value = '', $store_id = 0) {
			if (!is_array($value)) {
				$this->registry->get('db')->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '" . $this->registry->get('db')->escape($value) . "', serialized = '0'  WHERE `key` = '" . $this->registry->get('db')->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
			} else {
				$this->registry->get('db')->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '" . (version_compare(VERSION, '2.1', '>') ? $this->registry->get('db')->escape(json_encode($value)) : $this->registry->get('db')->escape(serialize($value))) . "', serialized = '1' WHERE `key` = '" . $this->registry->get('db')->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
			}
		}

		public function changeSettingGroup($group, $new_group, $store_id = null, $override = true) {
			$sql = "UPDATE `" . DB_PREFIX . "setting` SET `" . (version_compare(VERSION, '2.0.1', '<') ? 'group' : 'code') . "` = '" . $this->registry->get('db')->escape($new_group) . "'";

			if ($override) {
				$sql .= ", `key` = REPLACE (`key`, '" . $group . "', '" . $new_group . "')";
			}

			$sql .= " WHERE `" . (version_compare(VERSION, '2.0.1', '<') ? 'group' : 'code') . "` = '" . $this->registry->get('db')->escape($group) . "'";

			if (isset($store_id) && $store_id !== '') {
				$sql .= " AND store_id = '" . (int)$store_id . "'";
			}

			$this->registry->get('db')->query($sql);
		}

		public function deleteSetting($group, $store_id = null) {
			$sql = "DELETE FROM `" . DB_PREFIX . "setting` WHERE `" . (version_compare(VERSION, '2.0.1', '<') ? 'group' : 'code') . "` = '" . $this->registry->get('db')->escape($group) . "'";

			if (isset($store_id) && $store_id !== '') {
				$sql .= " AND store_id = '" . (int)$store_id . "'";
			}

			$this->registry->get('db')->query($sql);
		}
	}
}
?>