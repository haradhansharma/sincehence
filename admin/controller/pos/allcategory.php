<?php
class ControllerPosAllCategory extends Controller {
	public function index() {
		$this->load->language('pos/pos');
		$this->load->model('pos/pos');
		
		$data['user_token'] = $this->session->data['user_token'];
		$url = '';
		
		
		$data['categories'] = array();

		$categories = $this->model_pos_pos->getCategories(0);
		
		foreach ($categories as $category) {
			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'],
				'path'        => $category['category_id'],
				
			);
		}		
			
		return $this->load->view('pos/allcategory', $data);
	}
	
	
}