<?php
class ControllerExtensionModuleManulist extends Controller {
	public function index($setting) {
		$this->load->language('product/manufacturer');
		$this->load->language('extension/module/manulist');

		$this->load->model('catalog/manufacturer');
		$this->load->model('extension/module/manulist');

		$this->load->model('tool/image');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['manufacturers'] = array();
		
		$filter_data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']  
		);

        $manufacturer_total = $this->model_extension_module_manulist->getTotalManufacturer($filter_data);		
		$results = $this->model_extension_module_manulist->getManufacturerss($filter_data);	

		foreach ($results as $result) {			

			if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}
			
			$data['manufacturers'][] = array(
				'name' => $result['name'],
				'image' => $image,				
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
			);
		}
            $data['manulink'] = $this->url->link('product/manufacturer');		

		return $this->load->view('extension/module/manulist', $data);
		
}
}
