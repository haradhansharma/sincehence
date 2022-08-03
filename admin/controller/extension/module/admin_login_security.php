<?php
class ControllerExtensionModuleAdminLoginSecurity extends Controller {

	private $error = array();
	
	public function index() {

		$this->load->language('extension/module/admin_login_security');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('module_admin_login_security', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/admin_login_security', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/admin_login_security', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_admin_login_security_status'])) {
			$data['module_admin_login_security_status'] = $this->request->post['module_admin_login_security_status'];
		} else {
			$data['module_admin_login_security_status'] = $this->config->get('module_admin_login_security_status');
		}

		if (isset($this->request->post['module_admin_login_security_false_count'])) {
			$data['module_admin_login_security_false_count'] = $this->request->post['module_admin_login_security_false_count'];
		} else {
			$data['module_admin_login_security_false_count'] = $this->config->get('module_admin_login_security_false_count') == ''?3: $this->config->get('module_admin_login_security_false_count');
		}

		if (isset($this->request->post['module_admin_login_security_disable_time'])) {
			$data['module_admin_login_security_disable_time'] = $this->request->post['module_admin_login_security_disable_time'];
		} else {
			$data['module_admin_login_security_disable_time'] = $this->config->get('module_admin_login_security_disable_time') == ''?2:$this->config->get('module_admin_login_security_disable_time');
		}

		$data['error_black_list'] = str_replace("XXX", $this->url->link('extension/module/admin_login_security/iplist', 'user_token=' . $this->session->data['user_token'], true), $this->language->get('error_black_list'));

		$ip = $this->getClientIP();
		$this->load->model('extension/module/admin_login_security');
		$data['check_host_ip'] = $this->model_extension_module_admin_login_security->createDBTable();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/admin_login_security', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/admin_login_security')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getClientIP() {

		$ip = '';
		if (getenv('HTTP_CLIENT_IP')){
			$ip = getenv('HTTP_CLIENT_IP');
		}else if(getenv('HTTP_X_FORWARDED_FOR')){
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		}else if(getenv('HTTP_X_FORWARDED')){
			$ip = getenv('HTTP_X_FORWARDED');
		}else if(getenv('HTTP_FORWARDED_FOR')){
			$ip = getenv('HTTP_FORWARDED_FOR');
		}else if(getenv('HTTP_FORWARDED')){
			$ip = getenv('HTTP_FORWARDED');
		}else if(getenv('REMOTE_ADDR')){
			$ip = getenv('REMOTE_ADDR');
		}else{
			$ip = false;
		}
		return $ip;
	}
}