<?php
class ControllerExtensionModuleproductoptioncolor extends Controller {
	private $error = array();
public function install()
	{
	$this->load->model('extension/productoptioncolor');
	$this->model_extension_productoptioncolor->install();
	}	
	public function uninstall()
	{
	$this->load->model('extension/productoptioncolor');
	$this->model_extension_productoptioncolor->uninstall();
	}
	
	public function index() {
		$this->load->language('extension/module/productoptioncolor');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_productoptioncolor', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_image'] = $this->language->get('text_image');
		$data['text_color'] = $this->language->get('text_color');
		$data['entry_status'] = $this->language->get('entry_status');
        $data['entry_type'] = $this->language->get('entry_type');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
			'href' => $this->url->link('extension/module/productoptioncolor', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/productoptioncolor', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_productoptioncolor_status'])) {
			$data['module_productoptioncolor_status'] = $this->request->post['module_productoptioncolor_status'];
		} else {
			$data['module_productoptioncolor_status'] = $this->config->get('module_productoptioncolor_status');
		}
		if (isset($this->request->post['module_productoptioncolor_type'])) {
			$data['module_productoptioncolor_type'] = $this->request->post['module_productoptioncolor_type'];
		} else {
			$data['module_productoptioncolor_type'] = $this->config->get('module_productoptioncolor_type');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/productoptioncolor', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/productoptioncolor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}