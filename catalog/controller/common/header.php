<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
			
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();
		
		////sharma	    
$homepage = "/";
$currentpage = $_SERVER['REQUEST_URI'];
if($homepage==$currentpage) {
   $data['homepage'] = 'yes';
}else{
   $data['homepage'] = 'no'; 
}
////sharma
		
		/////sharma header back image
		$this->load->model('design/banner');
		$this->load->model('tool/image');
		$bnrname = 'headback';
		$bannerresult = $this->model_design_banner->getBannerId($bnrname);
		if($bannerresult){
		   $banner_id =  $bannerresult['banner_id'];
		}else{
		   $banner_id =  ''; 
		}
		$data['banners'] = array();
		$results = $this->model_design_banner->getBannerImage($banner_id);
		if($results){
		    $backimage = $this->model_tool_image->resize($results['image'], 1400, 600);
		}else{
		    $backimage ='';
		}
		$data['backimage'] = $backimage;

		/////sharma header back image
		
		

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');
		
		// RF - Start 
							if (isset($this->request->get['product_id'])) {
							$product_id = (int)$this->request->get['product_id'];
							} else {
							 $product_id = 0;
							}

							$this->load->model('catalog/product');
							$product_info = $this->model_catalog_product->getProduct($product_id);      
							//$this->data['product_info'] = $product_info;
							
							if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}        
				$data['images'] = array();
							// RF - End

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
// 		////////sharma
// 		 if($this->customer->islogged() && $this->customer->getGroupId() != 1){
//           $data['regi_info'] = "<b>BETA-v1.2: </b>This is a business management system! A-site for traders who have signed a contract with Gagann, which is automated to carry out business activities.<br/>
// If you have not signed the contract, the information below in this page is for you. <p> If you found any error please click <span class = 'red'> <a href='mailto:admin@gagann.com'>Send email</a></span> and let us know!</p>";
//         }else{
//           $data['regi_info'] =   "<b>BETA-v1.2: </b>If you found any error please click <span class = 'red'> <a href='mailto:admin@gagann.com'>Send email</a></span> and let us know!";
//         }
// 	/////sharma	
	    /////advance Order Date start
	        $this->load->model('catalog/category');
	    
            $data['site_ur'] = $_SERVER['HTTP_HOST'] ;
            
	        $cusgroup_id = $this->config->get('config_customer_group_id');
	        
	        $data['group_comissions'] = array();
			$group_comissions = $this->model_catalog_category->getGroupComissions($cusgroup_id);
			foreach ($group_comissions as $group_comission){
			    $data['group_comissions'][] = array(
			    'comission' => $group_comission['comission']
			    );
			    $data['comission']= $group_comission['comission'];
			}
			
	   
      
	    $data['customer_group'] = $this->customer->getGroupId();
	    
	    $data['next_slots'] = array();
	    $next_slots = $this->model_catalog_category->getNextDate();
	    foreach ($next_slots as $next_slot){
	        if($next_slot['itd_deliverydays']){
	            $next_date = date('Y-m-d',strtotime($next_slot['next_date']));
	        }else{
	           $next_date = ''; 
	        }
	        
	        
	       $data['next_slots'][] = array(
	           'itd_deliverydays' => $next_slot['itd_deliverydays'],
	           'next_date' => date('Y-m-d',strtotime($next_slot['next_date'])),
	           'delidate' =>  date('Y-m-d', strtotime($next_date. " + ".(int)$next_slot['itd_deliverydays']." days"))
	           );
	    }
	    
	    /////advance Order Date end


		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');
		
		

		return $this->load->view('common/header', $data);
	}
}
