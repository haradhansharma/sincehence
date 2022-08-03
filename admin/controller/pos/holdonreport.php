<?php
class ControllerPosHoldonReport extends Controller {
	private $error = array();

	public function index() {
		
		$this->load->language('pos/holdon');
		$this->load->model('pos/holdon');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pos/order');

		if (isset($this->request->get['filter_date_to'])) {
			$filter_date_to = $this->request->get['filter_date_to'];
		} else {
			$filter_date_to = false;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'product_id';
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

		$url = '';

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
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
			'href' => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['export'] = $this->url->link('sale/order/export', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
		$data['add'] = $this->url->link('sale/order/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['holdreports'] = array();

		$filter_data = array(
			'filter_date_to' 	   => $filter_date_to,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$order_total = $this->model_pos_holdon->getTotalHoldOn($filter_data);

		$results = $this->model_pos_holdon->getholdoreport($filter_data);

		foreach ($results as $result) {

			$data['holdreports'][] = array(
				'holdon_id'     => $result['holdon_id'],
				'holdon_no'     => $result['holdon_no'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
			);

		}
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing'] = $this->language->get('text_missing');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_select'] = $this->language->get('text_select');

		$data['column_holdon'] = $this->language->get('column_holdon');
		$data['column_proname'] = $this->language->get('column_proname');
		$data['column_prooption'] = $this->language->get('column_prooption');
		$data['column_dateadded'] = $this->language->get('column_dateadded');
		$data['column_action'] = $this->language->get('column_action');
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
		$data['sort_customer'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
		$data['sort_status'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=order_status' . $url, true);
		$data['sort_total'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_date_to'])) {
			$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('pos/orderlist', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pos/holdonreport', $data));
	}

	public function holdaddCart() {
		$this->load->language('pos/holdonreport');

		$json = array();

		if (isset($this->request->get['holdon_id'])) {
			$holdon_id = (int)$this->request->get['holdon_id'];
		} else {
			$holdon_id = 0;
		}

		$this->load->model('pos/holdon');

		$h1product_infos = $this->model_pos_holdon->getholdProductId($holdon_id);
		if(isset($h1product_infos['hold_option'])) {
			$products = unserialize($h1product_infos['hold_option']);
		} else {
			$products =array();
		}

		foreach($products as $product) {
			
			if (!empty($product['product_id'])) {
				$product_id = $product['product_id'];
				// Options
				if (!empty($product['option'])) {
					$options = $product['option'];
				} else {
					$options = array();
				}
				
				$option=array();
				foreach($options as $product_option_id => $value) {
					$option[$product_option_id]=$value;
				}
				$recurring_id=0;
				$h1 = $this->pos->add($product_id, $product['quantity'], $option, $recurring_id);
				
				if($this->session->data['cart']) {
					$h1product_infos = $this->model_pos_holdon->deleteHoldOn($holdon_id);
				}
			} else {
				if (isset($product['cproduct_id'])) {
					$cproduct_id = (int)$product['cproduct_id'];
				} else {
					$cproduct_id = 0;
				}

				if (isset($product['quantity'])) {
					$quantity = $product['quantity'];
				} else {
					$quantity = 0;
				}
				$option=array();
				
				$recurring_id=0;
				$this->pos->add(0,$quantity, '','',$cproduct_id);
				$json['success'] ='success';
				$json['total'] = '';
				
				/*if($this->session->data['cart']) {
					$h1product_infos = $this->model_pos_holdon->deleteHoldOn($holdon_id);
				}*/
			}

		}
		$json['success'] ='success';
		$json['total'] = '';
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}		

}
