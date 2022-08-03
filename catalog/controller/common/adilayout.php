<?php
class ControllerCommonAdilayout extends Controller {
	public function index() {

		
		$data['content_one'] = $this->load->controller('common/content_one');	    	
		$data['content_two'] = $this->load->controller('common/content_two');	    
		$data['content_thr'] = $this->load->controller('common/content_thr');	    
		$data['content_four'] = $this->load->controller('common/content_four'); 
	
	    if($this->config->get('module_adilayout_layout') == 'One'){
	    	return $this->load->view('common/layoutone', $data);
	    }elseif($this->config->get('module_adilayout_layout') == 'Two'){
	    	return $this->load->view('common/layouttwo', $data);
        }elseif($this->config->get('module_adilayout_layout') == 'Three'){
        	return $this->load->view('common/layoutthree', $data);
        }elseif($this->config->get('module_adilayout_layout') == 'Four'){
            return $this->load->view('common/layoutfour', $data);	
        }elseif($this->config->get('module_adilayout_layout') == 'Five'){
        	return $this->load->view('common/layoutfive', $data);
        }
        
	}
}
