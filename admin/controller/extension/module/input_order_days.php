<?php
class ControllerExtensionModuleInputOrderDays extends Controller
{
	
public function install()
	{
	$this->load->model('extension/input_order_days');
	$this->model_extension_input_order_days->install();
	}	

	public function uninstall()
	{
	$this->load->model('extension/input_order_days');
	$this->model_extension_input_order_days->uninstall();
	}



public function index()
{
	$this->load->language('extension/module/input_order_days');
	$this->document->setTitle($this->language->get('heading_title'));



	// date condition
	$slot1input=null;
if ($this->request->server['REQUEST_METHOD'] == 'POST') 	 {

  $slot1input=$this->request->post['icd_slot1'];
 
}

  $slot1input2= date_create($slot1input);
			 
  $slot1input3=date_format($slot1input2,"Y-m");
  $data['test']=$slot1input3;
$db_i_c_d=null;

	$this->load->model('extension/input_order_days');

		$results = $this->model_extension_input_order_days->getdate($slot1input3);

		foreach ($results as $result) {

			      $previous_slot=array(


			       'date'=>$result['ss'],
			   );
                   

			      $db_i_c_d=$result['ss'] ;
		
               }

          $data['test2']=$db_i_c_d;
 

// if($slot1input3!=$db_i_c_d)
// {
	$this->load->model('extension/input_order_days');
	if ($this->request->server['REQUEST_METHOD'] == 'POST') 	 {
	 
			$this->model_extension_input_order_days->additd($this->request->post);
		 
		$this->session->data['success'] = $this->language->get('text_success');
		$this->response->redirect($this->url->link('extension/module/input_order_days', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
	}
// }
// else{

	


// }






	///end

	 

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
			'href' => $this->url->link('extension/module/input_order_days', 'user_token=' . $this->session->data['user_token'], true)
		);
	} else {
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/input_order_days', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
		);
	}
	if (!isset($this->request->get['module_id'])) {
		$data['action'] = $this->url->link('extension/module/input_order_days', 'user_token=' . $this->session->data['user_token'], true);
	} else {
		$data['action'] = $this->url->link('extension/module/input_order_days', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
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
	$this->load->model('localisation/language');
	$data['languages'] = $this->model_localisation_language->getLanguages();
	if (isset($this->request->post['status'])) {
		$data['status'] = $this->request->post['status'];
	} elseif (!empty($module_info)) {
		$data['status'] = $module_info['status'];
	} else {
		$data['status'] = '';
	}
	


	// history slot 
	$data['date_slot'] = array();
	$this->load->model('extension/input_order_days');

		$results = $this->model_extension_input_order_days->getallslotdate();

		foreach ($results as $result) {
		 

			 $data['date_slot'][] = array(


			       'itd_id'=>$result['itd_id'],
			      
			       'itd_deliverydays'=>$result['itd_deliverydays'],
			       'itd_collectiondays'=>$result['itd_collectiondays'],
			       'itd_collection_date_slot1'=>$result['itd_collection_date_slot1'],
			       'date_added'=>$result['date_added'],
			       
			       'edit'          => $this->url->link('extension/module/input_order_days/slot_edit',  'user_token=' . $this->session->data['user_token'] . '&itd_id=' . $result['itd_id'] , true)
			        );

}




	$data['header'] = $this->load->controller('common/header');
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
	$this->response->setOutput($this->load->view('extension/module/input_order_days', $data));
}

	

public function slot_edit()
{
	$this->load->language('extension/module/input_order_days');
	$this->document->setTitle($this->language->get('heading_title'));
	$this->load->model('extension/input_order_days');
	


	if ($this->request->server['REQUEST_METHOD'] == 'POST') 	 {
	 

	   $this->model_extension_input_order_days->slotedit($this->request->get['itd_id'],$this->request->post);
		 
		$this->session->data['success'] = $this->language->get('text_success');
		$this->response->redirect($this->url->link('extension/module/input_order_days', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

		 $slot22input=$this->request->post['itd_deliverydays'];
		 
// 		 $data['action'] = $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
		
}
//  if (!isset($this->request->post['itd_id'])) {
// 		$data['action'] = $this->url->link('extension/module/input_order_days/slot_edit', 'user_token=' . $this->session->data['user_token'], true);
// 	} else {
		$data['action'] = $this->url->link('extension/module/input_order_days/slot_edit', 'user_token=' . $this->session->data['user_token'] . '&itd_id=' . $this->request->get['itd_id'], true);
// 		}

	$data['header'] = $this->load->controller('common/header');
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
	$this->response->setOutput($this->load->view('extension/module/slot_edit', $data));
}


}

