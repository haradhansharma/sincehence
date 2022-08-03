<?php
class ControllerExtensionModuleStorecalculator extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/storecalculator');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('storecalculator', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->cache->delete('product');

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['advance_payment_order'])) {
			$data['error_advance_payment_order'] = $this->error['advance_payment_order'];
		} else {
			$data['error_advance_payment_order'] = '';
		}
		
		if (isset($this->error['tagetarea'])) {
			$data['error_tagetarea'] = $this->error['tagetarea'];
		} else {
			$data['error_tagetarea'] = '';
		}
		
		if (isset($this->error['product_persqft'])) {
			$data['error_product_persqft'] = $this->error['product_persqft'];
		} else {
			$data['error_product_persqft'] = '';
		}
		if (isset($this->error['target_grandopening'])) {
			$data['error_target_grandopening'] = $this->error['target_grandopening'];
		} else {
			$data['error_target_grandopening'] = '';
		}
		if (isset($this->error['security_deposite'])) {
			$data['error_security_deposite'] = $this->error['security_deposite'];
		} else {
			$data['error_security_deposite'] = '';
		}
		if (isset($this->error['franchise_fee'])) {
			$data['error_franchise_fee'] = $this->error['franchise_fee'];
		} else {
			$data['error_franchise_fee'] = '';
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
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

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/storecalculator', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/storecalculator', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/storecalculator', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/storecalculator', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['tagetarea'])) {
			$data['tagetarea'] = $this->request->post['tagetarea'];
		} elseif (!empty($module_info)) {
			$data['tagetarea'] = $module_info['tagetarea'];
		} else {
			$data['tagetarea'] = 250;
		}
		
		if (isset($this->request->post['advance_payment_order'])) {
			$data['advance_payment_order'] = $this->request->post['advance_payment_order'];
		} elseif (!empty($module_info)) {
			$data['advance_payment_order'] = $module_info['advance_payment_order'];
		} else {
			$data['advance_payment_order'] = 30;
		}
		
		if (isset($this->request->post['product_persqft'])) {
			$data['product_persqft'] = $this->request->post['product_persqft'];
		} elseif (!empty($module_info)) {
			$data['product_persqft'] = $module_info['product_persqft'];
		} else {
			$data['product_persqft'] = 1000;
		}
		
		$this->load->model('extension/module/storecalculator');
		
		$data['target_grandopening'] = $this->model_extension_module_storecalculator->getGrandopening();

		if (isset($this->request->post['option_value_id'])) {
			$data['option_value_id'] = $this->request->post['option_value_id'];
		} elseif (!empty($module_info)) {
			$data['option_value_id'] = $module_info['target_grandopening'];
		} else {
			$data['option_value_id'] = '0';
		}


		
		if (isset($this->request->post['security_deposite'])) {
			$data['security_deposite'] = $this->request->post['security_deposite'];
		} elseif (!empty($module_info)) {
			$data['security_deposite'] = $module_info['security_deposite'];
		} else {
			$data['security_deposite'] = 250000;
		}
		
		if (isset($this->request->post['franchise_fee'])) {
			$data['franchise_fee'] = $this->request->post['franchise_fee'];
		} elseif (!empty($module_info)) {
			$data['franchise_fee'] = $module_info['franchise_fee'];
		} else {
			$data['franchise_fee'] = 30000;
		}
		

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 200;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 200;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/storecalculator', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/storecalculator')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
	
		if (!$this->request->post['tagetarea']) {
			$this->error['tagetarea'] = $this->language->get('error_tagetarea');
		}
		
		if (!$this->request->post['advance_payment_order']) {
			$this->error['advance_payment_order'] = $this->language->get('error_advance_payment_order');
		}
		if (!$this->request->post['product_persqft']) {
			$this->error['product_persqft'] = $this->language->get('error_product_persqft');
		}
		if (!$this->request->post['security_deposite']) {
			$this->error['security_deposite'] = $this->language->get('error_security_deposite');
		}
		if (!$this->request->post['franchise_fee']) {
			$this->error['franchise_fee'] = $this->language->get('error_franchise_fee');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}
