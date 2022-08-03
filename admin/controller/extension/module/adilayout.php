<?php
class ControllerExtensionModuleadilayout extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/adilayout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_adilayout', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/adilayout', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
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
			'href' => $this->url->link('extension/module/adilayout', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/adilayout', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_adilayout_status'])) {
			$data['module_adilayout_status'] = $this->request->post['module_adilayout_status'];
		} else {
			$data['module_adilayout_status'] = $this->config->get('module_adilayout_status');
		}
		if (isset($this->request->post['module_adilayout_layout'])) {
			$data['module_adilayout_layout'] = $this->request->post['module_adilayout_layout'];
		} else {
			$data['module_adilayout_layout'] = $this->config->get('module_adilayout_layout');
		}

		
        if($this->config->get('module_adilayout_layout') == 'One'){
        $data['layout'] = DIR_IMAGE . 'layoutone.png';
        }elseif($this->config->get('module_adilayout_layout') == 'Two'){
        $data['layout'] = DIR_IMAGE . 'layouttwo.png';
        }elseif($this->config->get('module_adilayout_layout') == 'Three'){
        $data['layout'] = DIR_IMAGE . 'layoutthree.png';
        }elseif($this->config->get('module_adilayout_layout') == 'Four'){
        $data['layout'] = DIR_IMAGE . 'layoutfour.png';
        }elseif($this->config->get('module_adilayout_layout') == 'Five'){
        $data['layout'] = DIR_IMAGE . 'layoutfive.png';
        }else{
        $data['layout'] = 'No Layout Selected'	;
        }



		

		$data['layoutes'] = array(
			array(
				'name' => 'One',
				
			),
			array(
				'name' => 'Two',
				
			),
			array(
				'name' => 'Three',
				
			),
			array(
				'name' => 'Four',
				
			),
			array(
				'name' => 'Five',
				
			)

			);


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/adilayout', $data));
	}
	

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/adilayout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}