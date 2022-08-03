<?php
class ControllerExtensionModuleAccountPicture extends Controller {
	public function index() {
		$this->load->language('extension/module/account_picture');
		$this->load->model('extension/module/account_picture');
		$data['logged'] = $this->customer->isLogged();
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_change'] = $this->language->get('text_change');

		if (isset($this->request->post['change']) && !empty($this->request->files['account_picture']['name'])) {

			$image = $this->request->files['account_picture']['name'];
			$extension =  pathinfo($image);		
			if($extension['extension'] == 'jpg' OR $extension['extension'] == 'png' OR $extension['extension'] == 'jpeg'){
				$image = $this->request->files['account_picture']['name'];
				$image_tmp = $this->request->files['account_picture']['tmp_name'];
				$this->model_extension_module_account_picture->ChangePhoto($image,$image_tmp);
			}else{
				$this->session->data['extension'] = $this->language->get('error_extension');
			}		
		}

		$customer_info = $this->model_extension_module_account_picture->getCustomerInfo($this->customer->getId());

		if($customer_info){
			$data['firstname'] = $customer_info['firstname'];	
			$data['customer_id'] = $customer_info['customer_id'];	
			$data['customer_ext'] = $customer_info['extension'];
			$data['image'] = 'catalog/view/theme/default/image/'.$customer_info['customer_id'].'.'.$customer_info['extension'];	
		}
		if (isset($this->session->data['extension'])) {
    		$data['extension'] = $this->session->data['extension'];
			unset($this->session->data['extension']);
		} else {
			$data['extension'] = '';
		}

		if ($this->config->get('account_picture_height')) {
			$data['height'] = $this->config->get('account_picture_height');
		} else {
			$data['height'] = '120';
		}

		if ($this->config->get('account_picture_width')) {
			$data['width'] = $this->config->get('account_picture_width');
		} else {
			$data['width'] = '120';
		}

		$data['action'] = $this->url->link('account/account', '', 'SSL');

		return $this->load->view('extension/module/account_picture', $data);
	}
}