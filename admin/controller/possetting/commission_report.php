<?php
class ControllerPossettingCommissionReport extends Controller {
 	private $error = array();
	public function index() {
			
		$this->load->language('possetting/commission_report');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('possetting/commission_report');
		
		$this->getList();
	}

 	public function getList() {
		$this->load->language('possetting/commission_report');
 		
	 	if (isset($this->request->get['filter_username'])) {
			$filter_username = $this->request->get['filter_username'];
		} else {
			$filter_username = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
	 
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_id';
		}
	 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
		 	$order = 'ASC';
		}
	 
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';


	 	if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . $this->request->get['filter_username'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	 	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
	
		$data['users'] = array();

		$filter_data = array(
			'filter_username'   => $filter_username,
			'filter_order_id'   => $filter_order_id,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$this->load->model('possetting/commission_report');
		$commission_total = $this->model_possetting_commission_report->getTotalReport($filter_data);
		$results = $this->model_possetting_commission_report->getReports($filter_data);
		$grandtotale=0;
		$data['grandtotale']=0;

		foreach ($results as $result) {
				
				if(isset($result['username'])){
					$username=$result['username'];         
				} else {
					$username='';
				}

				if(isset($result['commission'])){
					$commissiontype=$result['commission'];         
				} else {
					$commissiontype='';
				}

				if(isset($result['commission_value'])){
					$commission_value=$result['commission_value'];         
				} else {
					$commission_value=0;
				}

				$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
				if(isset($amount_info['total'])){
					$amount=$amount_info['total'];         
				} else {
					$amount=0;
				}	

				if($commissiontype=='Fixed') {
					$commission=$commission_value;
				} else {
					$commission=$amount/100 * $commission_value;
				}
		
			
				$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
				if(isset($amount_info['total'])){
					$totals=$amount_info['total'];         
				} else {
					$totals='';
				}	
				$grandtotale+=$totals;
				$data['users'][] = array(
					'user_id'    => $result['user_id'],
					'username'	=> $username,
					'order_id'	=> $result['order_id'],
					'amount'	=> $this->currency->format($amount, $this->config->get('config_currency'), $this->config->get('currency_value')),
					'commission'=>  $this->currency->format($commission,  $this->config->get('config_currency'), $this->config->get('currency_value')),
				);
			
		}
		
		$data['grandtotale'] = $this->currency->format($grandtotale, $this->config->get('config_currency'), $this->config->get('currency_value'));

		$data['heading_title']       = $this->language->get('heading_title');

		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_none'] 		 	 = $this->language->get('text_none');
		$data['text_print'] 		 = $this->language->get('text_print');

		$data['column_username']	 = $this->language->get('column_username');
		$data['column_order_id']	 = $this->language->get('column_order_id');
		$data['column_amount']	     = $this->language->get('column_amount');
		$data['column_commission']	 = $this->language->get('column_commission');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['user_token']               = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . $this->request->get['filter_username'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_id']	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=order_id' . $url, true);
		$data['sort_username']	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=username' . $url, true);
		$data['sort_commission'] = $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=commission' . $url, true);
		$data['sort_amount']  	= $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . '&sort=amount' . $url, true);
		
		$url = '';

		if (isset($this->request->get['filter_username'])) {
			$url .= '&filter_username=' . $this->request->get['filter_username'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
		 	$url .= '&order=' . $this->request->get['order'];
		}
        
		$pagination 	   = new Pagination();
		$pagination->total = $commission_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('possetting/commission_report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination']= $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($commission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($commission_total - $this->config->get('config_limit_admin'))) ? $commission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $commission_total, ceil($commission_total / $this->config->get('config_limit_admin')));

		$data['filter_username'] = $filter_username;
		$data['filter_order_id'] = $filter_order_id;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['commissionprint'] = $this->url->link('possetting/commission_report/comissionreport', 'user_token=' . $this->session->data['user_token'].$url, true);

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/commission_report', $data));
	}

	public function comissionreport() {
		$this->load->language('possetting/commission_report');
 		$this->load->model('possetting/commission_report');	

		if (isset($this->request->get['filter_username'])) {
			$filter_username = $this->request->get['filter_username'];
		} else {
			$filter_username = null;
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
		 	$sort = 'order_id';
		}
	 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
		 	$order = 'DESC';
		}
	 
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url ='';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	 	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
	 
		$data['users'] = array();

		$filter_data = array(
			'filter_username'   => $filter_username,
			'filter_order_id'   => $filter_order_id,
			'sort'  => $sort,
			'order' => $order,
			
		);

		$commission_total = $this->model_possetting_commission_report->getTotalReport($filter_data);
		$results = $this->model_possetting_commission_report->getReports($filter_data);
				
		foreach ($results as $result) {
			$user_info = $this->model_possetting_commission_report->getUserName($result['user_id']);
			if(isset($user_info)){
			
				if(isset($user_info['username'])){
					$username=$user_info['username'];         
				} else {
					$username='';
				}

				if(isset($user_info['commission'])){
					$commissiontype=$user_info['commission'];         
				} else {
					$commissiontype='';
				}

				if(isset($user_info['commission_value'])){
					$commission_value=$user_info['commission_value'];         
				} else {
					$commission_value='';
				}

				$amount_info = $this->model_possetting_commission_report->getAmount($result['order_id']);
				if(isset($amount_info['total'])){
					$amount=$amount_info['total'];         
				} else {
					$amount='';
				}	
				
				if($commissiontype=='Fixed')
				{
				$commission=$commission_value;
				}
				else
				{
				$commission=$amount*$commission_value/100;
				}

			}
			if($commission) {
				$data['users'][] = array(
					'user_id'    => $result['user_id'],
					'username'	=> $username,
					'order_id'	=> $result['order_id'],
					'amount'	=> $this->currency->format($amount, $this->config->get('config_currency'), $this->config->get('currency_value')),
					'commission'=>  $this->currency->format($commission,  $this->config->get('config_currency'), $this->config->get('currency_value')),
				);
			}
		}
		$data['heading_title']       = $this->language->get('heading_title');
		$data['text_list']           = $this->language->get('text_list');
		$data['text_no_results'] 	 = $this->language->get('text_no_results');
		$data['text_confirm'] 		 = $this->language->get('text_confirm');
		$data['text_none'] 		 	 = $this->language->get('text_none');
		$data['text_print'] 		 = $this->language->get('text_print');

		$data['column_username']	 = $this->language->get('column_username');
		$data['column_order_id']	 = $this->language->get('column_order_id');
		$data['column_amount']	     = $this->language->get('column_amount');
		$data['column_commission']	 = $this->language->get('column_commission');

		$data['button_delete'] 		 = $this->language->get('button_delete');
		$data['button_filter'] 		 = $this->language->get('button_filter');
		$data['button_save'] 		 = $this->language->get('button_save');
		$data['user_token']               = $this->session->data['user_token'];
		
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('possetting/commissionreport_print', $data));
	}

	public function autocomplete(){
		$json = array();
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'username';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$this->load->model('possetting/user');
			
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$accounts = $this->model_possetting_user->getUsers($filter_data);
		foreach ($accounts as $account) {

		$json[] = array(
			'user_id'  => $account['user_id'],
			'username'   => strip_tags(html_entity_decode($account['username'], ENT_QUOTES, 'UTF-8'))
		);
		}
		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['username'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
 

}