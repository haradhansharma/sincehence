<?php
class ControllerExtensionAnalyticsProgressiveWebApplication extends Controller {
	private $error = array();

	public function index() {

		$labels = $this->load->language('extension/analytics/progressive_web_application');

		foreach ($labels as $label_key => $label) {

			$data[$label_key] = $label;

		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if (empty($this->request->post['analytics_progressive_web_application_status']['status']) || ($this->request->post['analytics_progressive_web_application_status']['status'] == '0')) {

				$this->model_setting_setting->editSettingValue('analytics_progressive_web_application', 'analytics_progressive_web_application_status', 0, $this->request->get['store_id']);

			} else {

				$this->model_setting_setting->editSetting('analytics_progressive_web_application', $this->request->post, $this->request->get['store_id']);

			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'].'&type=analytics', true));

		}

		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];

		} else {

			$data['error_warning'] = '';

		}

		if (isset($this->error['short_name'])) {

			$data['error_short_name'] = $this->error['short_name'];

		} else {

			$data['error_short_name'] = array();

		}

		if (isset($this->error['long_name'])) {

			$data['error_long_name'] = $this->error['long_name'];

		} else {

			$data['error_long_name'] = array();

		}

		if (isset($this->error['description'])) {

			$data['error_description'] = $this->error['description'];

		} else {

			$data['error_description'] = array();

		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token='.$this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_analytics'),
			'href' => $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'].'&type=analytics', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/analytics/progressive_web_application', 'user_token='.$this->session->data['user_token'].'&store_id='.$this->request->get['store_id'], true)
		);

		$data['action'] = $this->url->link('extension/analytics/progressive_web_application', 'user_token='.$this->session->data['user_token'].'&store_id='.$this->request->get['store_id'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token='.$this->session->data['user_token'].'&type=analytics', true);

		$data['send_ticket'] = str_replace('&amp;', '&', $this->url->link('extension/analytics/progressive_web_application/send_ticket', 'user_token='.$this->session->data['user_token'].'&store_id='.$this->request->get['store_id'], true));

		$data['user_token'] = $this->session->data['user_token'];

		$data['settings'] = array();
		$data['settings'] = json_decode($this->model_setting_setting->getSettingValue('analytics_progressive_web_application_status', $this->request->get['store_id']), true);

		$this->load->model('localisation/language');

		$data['languages'] = array_reverse($this->model_localisation_language->getLanguages());

		if (isset($data['settings']['status'])) {

			$data['status'] = $data['settings']['status'];

		} else {

			$data['status'] = 0;

		}

		if (isset($data['settings']['preloader_status'])) {

			$data['preloader_status'] = $data['settings']['preloader_status'];

		} else {

			$data['preloader_status'] = 0;

		}

		if (isset($data['settings']['preloader_background'])) {

			$data['preloader_background'] = $data['settings']['preloader_background'];

		} else {

			$data['preloader_background'] = '#2199C6';

		}

		if (isset($data['settings']['preloader_fadeout'])) {

			$data['preloader_fadeout'] = (int) $data['settings']['preloader_fadeout'];

		} else {

			$data['preloader_fadeout'] = '400';

		}

		if (isset($data['settings']['preloader_size'])) {

			$data['preloader_size'] = (int) $data['settings']['preloader_size'];

		} else {

			$data['preloader_size'] = '15';

		}

		if (isset($data['settings']['preloader_balls_color'])) {

			$data['preloader_balls_color'] = $data['settings']['preloader_balls_color'];

		} else {

			$data['preloader_balls_color'] = '#FAFAFA';

		}

		if (isset($data['settings']['preloader_balls_size'])) {

			$data['preloader_balls_size'] = (int) $data['settings']['preloader_balls_size'];

		} else {

			$data['preloader_balls_size'] = '15';

		}

		if (isset($data['settings']['preloader_display'])) {

			foreach ($data['settings']['preloader_display'] as $key => $value) {

				if (isset($data['settings']['preloader_display'][$key])) {

					$data['preloader_display'][$key] = $value;

				} else {

					$data['preloader_display'][$key] = null;

				}

			}

		}

		if (isset($data['settings']['offline_enabled'])) {

			$data['offline_enabled'] = $data['settings']['offline_enabled'];

		} else {

			$data['offline_enabled'] = 0;

		}

		if (isset($data['settings']['lang_code'])) {

			foreach ($data['settings']['lang_code'] as $key => $value) {

				if (isset($data['settings']['lang_code'][$key])) {

					$data['lang_code'][$key] = $value;

				} else {

					$data['lang_code'][$key] = '';

				}

			}

		}

		if (isset($data['settings']['short_name'])) {

			foreach ($data['settings']['short_name'] as $key => $value) {

				if (isset($data['settings']['short_name'][$key])) {

					$data['short_name'][$key] = $value;

				} else {

					$data['short_name'][$key] = '';

				}

			}

		}

		if (isset($data['settings']['long_name'])) {

			foreach ($data['settings']['long_name'] as $key => $value) {

				if (isset($data['settings']['long_name'][$key])) {

					$data['long_name'][$key] = $value;

				} else {

					$data['long_name'][$key] = '';

				}

			}

		}

		if (isset($data['settings']['description'])) {

			foreach ($data['settings']['description'] as $key => $value) {

				if (isset($data['settings']['description'][$key])) {

					$data['description'][$key] = $value;

				} else {

					$data['description'][$key] = '';

				}

			}

		}

		if (isset($data['settings']['theme_color'])) {

			$data['theme_color'] = $data['settings']['theme_color'];

		} else {

			$data['theme_color'] = '#2199C6';

		}

		if (isset($data['settings']['background_color'])) {

			$data['background_color'] = $data['settings']['background_color'];

		} else {

			$data['background_color'] = '#2199C6';

		}

		if (isset($data['settings']['display'])) {

			$data['display'] = $data['settings']['display'];

		} else {

			$data['display'] = 'standalone';

		}

		if (isset($data['settings']['orientation'])) {

			$data['orientation'] = $data['settings']['orientation'];

		} else {

			$data['orientation'] = 'portrait';

		}

		if (isset($data['settings']['direction'])) {

			$data['direction'] = $data['settings']['direction'];

		} else {

			$data['direction'] = 'auto';

		}

		$this->load->model('tool/image');

		if (isset($data['settings']['application_image']) and is_file(DIR_IMAGE.$data['settings']['application_image'])) {
			$data['application_image']       = $this->model_tool_image->resize($data['settings']['application_image'], 150, 150);
			$data['thumb_application_image'] = $data['settings']['application_image'];
		} else {
			$data['application_image'] = '';
		}

		$data['image_placeholder'] = $this->model_tool_image->resize('no_image.png', 150, 150);

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/analytics/progressive_web_application', $data));

	}

	public function send_ticket() {

		ini_set("display_errors", 0);
		error_reporting(0);

		$json = array();

		$this->load->language('extension/analytics/progressive_web_application');

		if (!$this->user->hasPermission('modify', 'extension/analytics/progressive_web_application')) {

			$json['error'] = $this->language->get('error_permission');

		}

		if (!isset($this->request->post['progressive_web_application_support_email']) and !filter_var($this->request->post['progressive_web_application_support_email'], FILTER_VALIDATE_EMAIL)) {

			$json['error'] = $this->language->get('error_support_email');

		}

		if (!isset($this->request->post['progressive_web_application_support_subject']) and (utf8_strlen($this->request->post['progressive_web_application_support_subject']) < 10) || (utf8_strlen($this->request->post['progressive_web_application_support_subject']) > 200)) {

			$json['error'] = $this->language->get('error_support_subject');

		}

		if (!isset($this->request->post['progressive_web_application_support_name']) and (utf8_strlen($this->request->post['progressive_web_application_support_name']) < 3) || (utf8_strlen($this->request->post['progressive_web_application_support_name']) > 64)) {

			$json['error'] = $this->language->get('error_support_name');

		}

		if (!isset($this->request->post['progressive_web_application_support_text']) and (utf8_strlen($this->request->post['progressive_web_application_support_text']) < 10) || (utf8_strlen($this->request->post['progressive_web_application_support_text']) > 1024)) {

			$json['error'] = $this->language->get('error_support_text');

		}

		if (!isset($this->request->post['progressive_web_application_support_connections']) and (utf8_strlen($this->request->post['progressive_web_application_support_connections']) < 10) || (utf8_strlen($this->request->post['progressive_web_application_support_connections']) > 1024)) {

			$json['error'] = $this->language->get('error_support_connections');

		}

		/*if (empty($this->config->get('config_mail_protocol'))) {

		$json['error'] = $this->language->get('error_mail_config');

		}*/

		if (empty($this->config->get('config_mail_smtp_hostname'))) {

			$json['error'] = $this->language->get('error_mail_config');

		}

		if (empty($this->config->get('config_mail_smtp_username'))) {

			$json['error'] = $this->language->get('error_mail_config');

		}

		if (empty($this->config->get('config_mail_smtp_password'))) {

			$json['error'] = $this->language->get('error_mail_config');

		}

		if (empty($this->config->get('config_mail_smtp_port'))) {

			$json['error'] = $this->language->get('error_mail_config');

		}

		if (!$json) {

			if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || $_SERVER['SERVER_PORT'] == 443) {

				$protocol = 'https://';

			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {

				$protocol = 'https://';

			} else {

				$protocol = 'http://';

			}

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

				$owner_address = $_SERVER['HTTP_CLIENT_IP'];

			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

				$owner_address = $_SERVER['HTTP_X_FORWARDED_FOR'];

			} else {

				$owner_address = $_SERVER['REMOTE_ADDR'];

			}

			$str_url      = $protocol.$_SERVER['HTTP_HOST'].rtrim(dirname(str_replace("admin/", "", $_SERVER['SCRIPT_NAME'])), '/.\\').'/';
			$subject_mail = "[Opencart Progressive Web Application] ".$this->request->post['progressive_web_application_support_subject']." ".$str_url;
			$text_mail    = "\x53\x74\x6f\x72\x65\x3a\x20".$this->config->get('config_name')."\n";
			$text_mail .= "\x53\x74\x6f\x72\x65\x20\x4f\x77\x6e\x65\x72\x3a\x20".$this->request->post['progressive_web_application_support_name']."\n";
			$text_mail .= "\x53\x74\x6f\x72\x65\x20\x50\x68\x6f\x6e\x65\x3a\x20".$this->config->get('config_telephone')."\n";
			$text_mail .= "\x53\x74\x6f\x72\x65\x20\x45\x6d\x61\x69\x6c\x3a\x20".$this->request->post['progressive_web_application_support_email']."\n";
			$text_mail .= "\x53\x74\x6f\x72\x65\x20\x55\x52\x4c\x3a\x20".$str_url."\n";
			$text_mail .= "\x53\x74\x6f\x72\x65\x20\x4f\x77\x6e\x65\x72\x20\x49\x50\x3a\x20".$owner_address."\n\n";
			$text_mail .= "Issue:\n";
			$text_mail .= $this->request->post['progressive_web_application_support_text']."\n\n";
			$text_mail .= "Access to the store: ".$str_url."\n";
			$text_mail .= $this->request->post['progressive_web_application_support_connections'];
			$mod_author = "\x74\x6f\x64\x6f\x72\x2e\x64\x6f\x6e\x65\x76\x40\x67\x6d\x61\x69\x6c\x2e\x63\x6f\x6d";

			$mail                = new Mail($this->config->get('config_mail_engine'));
			$mail->protocol      = $this->config->get('config_mail_protocol');
			$mail->parameter     = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port     = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout  = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($mod_author);
			$mail->setFrom($this->request->post['progressive_web_application_support_email']);
			$mail->setSender($this->request->post['progressive_web_application_support_name']);
			$mail->setSubject($subject_mail);
			$mail->setText(html_entity_decode($text_mail, ENT_QUOTES, 'UTF-8'));

			$mail->send();

			$json['success'] = $this->language->get('success_ticket_sent');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	protected function validate() {

		if ($this->request->post['analytics_progressive_web_application_status']['status']) {

			if (!in_array($this->request->post['analytics_progressive_web_application_status']['display'], array("fullscreen", "standalone", "minimal-ui", "browser"), true)) {

				$this->error['warning'] = $this->language->get('error_display');

			}

			if (!in_array($this->request->post['analytics_progressive_web_application_status']['orientation'], array("any", "natural", "landscape", "landscape-primary", "landscape-secondary", "portrait", "portrait-primary", "portrait-secondary"), true)) {

				$this->error['warning'] = $this->language->get('error_orientation');

			}

			if (!in_array($this->request->post['analytics_progressive_web_application_status']['direction'], array("auto", "rtl", "ltr"), true)) {

				$this->error['warning'] = $this->language->get('error_direction');

			}

			foreach ($this->request->post['analytics_progressive_web_application_status']['long_name'] as $language_id => $value) {

				if ((utf8_strlen($value) < 32) || (utf8_strlen($value) > 64)) {

					$this->error['long_name'][$language_id] = $this->language->get('error_long_name');

				}

			}

			foreach ($this->request->post['analytics_progressive_web_application_status']['short_name'] as $language_id => $value) {

				if ((utf8_strlen($value) < 3) || (utf8_strlen($value) > 32)) {

					$this->error['short_name'][$language_id] = $this->language->get('error_short_name');

				}

			}

			foreach ($this->request->post['analytics_progressive_web_application_status']['description'] as $language_id => $value) {

				if ((utf8_strlen($value) < 32) || (utf8_strlen($value) > 1024)) {

					$this->error['description'][$language_id] = $this->language->get('error_description');

				}

			}

			if (!in_array($this->request->post['analytics_progressive_web_application_status']['display'], array("fullscreen", "standalone", "minimal-ui", "browser"), true)) {

				$this->error['warning'] = $this->language->get('error_display');

			}

			if (!in_array($this->request->post['analytics_progressive_web_application_status']['orientation'], array("any", "natural", "landscape", "landscape-primary", "landscape-secondary", "portrait", "portrait-primary", "portrait-secondary"), true)) {

				$this->error['warning'] = $this->language->get('error_orientation');
			}

			if (!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $this->request->post['analytics_progressive_web_application_status']['theme_color'])) {

				$this->error['warning'] = $this->language->get('error_theme_color');

			}

			if (!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $this->request->post['analytics_progressive_web_application_status']['background_color'])) {

				$this->error['warning'] = $this->language->get('error_background_color');

			}

			if (empty($this->request->post['analytics_progressive_web_application_status']['application_image'])) {

				$this->error['warning'] = $this->language->get('error_application_image');

			}

			if (!empty($this->request->post['analytics_progressive_web_application_status']['application_image'])) {

				$image_file = pathinfo($this->request->post['analytics_progressive_web_application_status']['application_image']);

				if (!preg_match('/png/i', $image_file['extension'])) {

					$this->error['warning'] = $this->language->get('error_application_image_format');

				}
			}

			if ($this->request->post['analytics_progressive_web_application_status']['preloader_status']) {

				if (!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $this->request->post['analytics_progressive_web_application_status']['preloader_balls_color'])) {

					$this->error['warning'] = $this->language->get('error_preloader_balls_color');

				}

				if (!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $this->request->post['analytics_progressive_web_application_status']['preloader_background'])) {

					$this->error['warning'] = $this->language->get('error_preloader_background');

				}

				if (((int) $this->request->post['analytics_progressive_web_application_status']['preloader_fadeout'] < 5) or ((int) $this->request->post['analytics_progressive_web_application_status']['preloader_fadeout'] > 10000)) {

					$this->error['warning'] = $this->language->get('error_preloader_fadeout');

				}

				if (((int) $this->request->post['analytics_progressive_web_application_status']['preloader_size'] < 1) or ((int) $this->request->post['analytics_progressive_web_application_status']['preloader_size'] > 100)) {

					$this->error['warning'] = $this->language->get('error_preloader_size');

				}

				if (((int) $this->request->post['analytics_progressive_web_application_status']['preloader_balls_size'] < 5) or ((int) $this->request->post['analytics_progressive_web_application_status']['preloader_balls_size'] > 500)) {

					$this->error['warning'] = $this->language->get('error_preloader_balls_size');

				}
				if (!isset($this->request->post['analytics_progressive_web_application_status']['preloader_display'])) {

					$this->error['warning'] = $this->language->get('error_preloader_display');

				}

				if (isset($this->request->post['analytics_progressive_web_application_status']['preloader_display'])) {

					foreach ($this->request->post['analytics_progressive_web_application_status']['preloader_display'] as $key => $value) {
						if (!in_array($key, array("small_phone", "normal_phone", "large_phone", "tablet", "large_tablet", "laptop", "large_laptop", "desktop", "large_desktop", "fhd", "uhd", "fk_uhd"), true)) {

							$this->error['warning'] = $this->language->get('error_preloader_display');

						}
					}

				}

			}

		}

		if (!$this->user->hasPermission('modify', 'extension/analytics/progressive_web_application')) {

			$this->error['warning'] = $this->language->get('error_permission');

		}

		if ($this->error && !isset($this->error['warning'])) {

			$this->error['warning'] = $this->language->get('error_check_form');

		}

		return !$this->error;
	}
}
