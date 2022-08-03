<?php
class ControllerExtensionModuleDigitCartAdminTab extends Controller {
	private $error = array();
	private $moduleName = 'digitcart_admin_tab';
	private $moduleFilePath = 'extension/module/digitcart_admin_tab';
	private $moduleVersion = '1.0';
	private $eventFilePath = 'setting/event';
	private $eventModel = 'model_setting_event';
	private function moduleEvents(){
		return array(
			array(
				'trigger' => 'admin/view/common/footer/after',
				'action' => $this->moduleFilePath . '/adminViewCommonFooterAfter'
			),
		);
	}
	public function index() {
		$data['view_type'] = 'module_settings';
		$lang = $this->load->language($this->moduleFilePath);
		$data['heading_title'] = $this->language->get('heading_title');
		$this->document->setTitle(STRIP_TAGS($this->language->get('heading_title')));
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_' . $this->moduleName, $this->request->post);
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
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->moduleFilePath, 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['action'] = $this->url->link($this->moduleFilePath, 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$vars = array(
			'status' => 0,
		);
		foreach($vars as $var => $default){
			if (isset($this->request->post['module_' . $this->moduleName . '_' . $var])) {
				$data['module_' . $this->moduleName . '_' . $var] = $this->request->post['module_' . $this->moduleName . '_' . $var];
			} elseif($this->config->get('module_' . $this->moduleName . '_' . $var)) {
				$data['module_' . $this->moduleName . '_' . $var] = $this->config->get('module_' . $this->moduleName . '_' . $var);
			} else {
				$data['module_' . $this->moduleName . '_' . $var] = $default;
			}
		}
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view($this->moduleFilePath, $data));
	}
	public function adminViewCommonFooterAfter(&$route = false, &$data = false, &$output = false){
		if(!$this->config->get('module_digitcart_admin_tab_status')) return;
		$data['view_type'] = 'js_code';
		$js_code = $this->load->view($this->moduleFilePath, $data);
		$output = str_replace(
			'<footer id="footer">',
			$js_code . '<footer id="footer">',
			$output
		);
	}
	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->moduleFilePath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
	public function install(){
		$this->load->model($this->eventFilePath);
		foreach($this->moduleEvents() as $event){
			$this->{$this->eventModel}->addEvent($this->moduleName, $event['trigger'], $event['action']);
		}
	}
	public function uninstall(){
		$this->load->model($this->eventFilePath);
		$this->{$this->eventModel}->deleteEventByCode($this->moduleName);
	}
}