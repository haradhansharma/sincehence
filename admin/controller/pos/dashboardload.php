<?php
class ControllerPosDashboardload extends Controller {
	public function index() {
		$this->load->language('pos/pos');
		$this->load->model('pos/order');
		$this->load->model('tool/image');

		$data['user_token'] = $this->session->data['user_token'];
		
		$data['text_todaysale'] = $this->language->get('text_todaysale');


		if (isset($this->request->post['setting_dashboard'])) {
			$setting_dashboards = $this->request->post['setting_dashboard'];
		} else {
			$setting_dashboards = $this->config->get('setting_dashboard');
		}
		
		$data['setting_dashboards'] = array();
		
		if(isset($setting_dashboards)) {
			foreach ($setting_dashboards as $dashboards) {
				

				$orderstatus_data=array();
				if (isset($dashboards['dashboard_orderstatus'])) {
					foreach ($dashboards['dashboard_orderstatus'] as $dashboard_orderstatus) {
						$orderstatus_data[]=$dashboard_orderstatus['order_status_id'];
					}
				}

				$payment_method_data=array();
				if (isset($dashboards['dashboard_paymentmethod'])) {
					foreach ($dashboards['dashboard_paymentmethod'] as $dashboard_paymentmethod) {
						$payment_method_data[]=$dashboard_paymentmethod['method'];
					}
				}
				
				$totalamount = $this->model_pos_order->getDashboardOrderAmount($orderstatus_data,$payment_method_data,$dashboards['daytype']);
			
				$data['setting_dashboards'][] = array(
					'name' 	 			=> $dashboards['name'],
					'sort_order' 	 	=> $dashboards['sort_order'],
					'daytype' 	 		=> $dashboards['daytype'],
					'dashboard_status' 	=> $dashboards['dashboard_status'],
					'text_color' 	 	=> $dashboards['text_color'],
					'bg_color' 	 		=> $dashboards['bg_color'],
					'text_color' 	 	=> $dashboards['text_color'],
					'icon'      		=> $dashboards['icon'],
					'dashboard_paymentmethod'=> $payment_method_data,
					'dashboard_orderstatus'=> $orderstatus_data,
					'totalamount'		=> $this->currency->format($totalamount, $this->config->get('config_currency')),
					
				);
			
			}
		}
		
	
		
		return $this->load->view('pos/dashboardload', $data);
	}
}