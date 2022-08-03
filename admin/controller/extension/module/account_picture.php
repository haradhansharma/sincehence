<?php
class ControllerExtensionModuleAccountPicture extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/account_picture');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_account_picture', $this->request->post);

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
			'href' => $this->url->link('extension/module/account_picture', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/account_picture', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_account_picture_status'])) {
			$data['module_account_picture_status'] = $this->request->post['module_account_picture_status'];
		} else {
			$data['module_account_picture_status'] = $this->config->get('module_account_picture_status');
        }

        if (isset($this->request->post['module_account_picture_height'])) {
			$data['module_account_picture_height'] = $this->request->post['module_account_picture_height'];
		} else {
			$data['module_account_picture_height'] = $this->config->get('module_account_picture_height');
		}

		if (isset($this->request->post['module_account_picture_width'])) {
			$data['module_account_picture_width'] = $this->request->post['module_account_picture_width'];
		} else {
			$data['module_account_picture_width'] = $this->config->get('module_account_picture_width');
		}
        
		$data['anyhow_logo'] = 'view/image/anyhowinfo-logo.png';
		$data['blog_link'] = 'https://store.anyhowinfo.com/account-picture-blog';
		$data['text_ah_blog'] = $this->language->get('text_ah_blog');
        $data['text_ah_footer'] = $this->language->get('text_ah_footer');
		$data['text_ah_version'] = sprintf($this->language->get('text_ah_version'), '1.0.0.0');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/account_picture', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/account_picture')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
    }
    
    public function install() {
		$this->load->model('extension/module/account_picture');
		$this->model_extension_module_account_picture->createTable();
	}

	public function uninstall() {
		$this->load->model('extension/module/account_picture');
		$this->model_extension_module_account_picture->dropTable();
	}
}