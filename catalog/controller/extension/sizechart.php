<?php
class ControllerExtensionSizechart extends Controller {
	public function index() {
		$this->load->language('common/sizechart');
		
		$this->load->model('extension/sizechart');
		
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		$this->load->model('extension/sizechart');
		$sizechart_info = $this->model_extension_sizechart->getSizeChartProduct($product_id);
		if(!$sizechart_info) {
			$sizechart_info = $this->model_extension_sizechart->getSizeChartCategory($product_id);
		}
		
		if($sizechart_info) {
			$data['sizechart_display'] = $sizechart_info['display'];
			$data['sizechart_title'] = $sizechart_info['title'];
			$data['sizechart_rows'] = (!empty($sizechart_info['sizechart'])) ? unserialize($sizechart_info['sizechart']) : array();
			$data['sizechart_description'] = html_entity_decode($sizechart_info['description'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['sizechart_display'] = '';
			$data['sizechart_title'] = '';
			$data['sizechart_rows'] = array();
			$data['sizechart_description'] = '';
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/sizechart')) {
			return $this->load->view($this->config->get('config_template') . '/template/extension/sizechart', $data);
		} else {
			return $this->load->view('extension/sizechart', $data);
		}
	}
	
	public function popup() {
		$this->load->language('common/sizechart');
		
		$this->load->model('extension/sizechart');
		
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		$this->load->model('extension/sizechart');
		$sizechart_info = $this->model_extension_sizechart->getSizeChartProduct($product_id);
		if(!$sizechart_info) {
			$sizechart_info = $this->model_extension_sizechart->getSizeChartCategory($product_id);
		}
		
		if($sizechart_info) {
			$data['sizechart_display'] = $sizechart_info['display'];
			$data['sizechart_title'] = $sizechart_info['title'];
			$data['sizechart_rows'] = (!empty($sizechart_info['sizechart'])) ? unserialize($sizechart_info['sizechart']) : array();
			$data['sizechart_description'] = html_entity_decode($sizechart_info['description'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['sizechart_display'] = '';
			$data['sizechart_title'] = '';
			$data['sizechart_rows'] = array();
			$data['sizechart_description'] = '';
		}
		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/sizechart_popup')) {
			return $this->load->view($this->config->get('config_template') . '/template/extension/sizechart_popup', $data);
		} else {
			return $this->load->view('extension/sizechart_popup', $data);
		}
	}
}