<?php
class ControllerExtensionFeedRss extends Controller {
 public function index() {
        // Load models.
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('localisation/currency');
        $this->load->model('catalog/category');


        $data['site_name'] = $this->config->get('config_name');
        $data['meta_description'] = $this->config->get('config_meta_description');

        $start = 0;
        $limit = 100;

        // Xml header.
        $this->response->addHeader('Content-Type: text/xml; charset=utf-8');
        // Store url.
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['store_url'] = $this->config->get('config_ssl');
        } else {
            $data['store_url'] = $this->config->get('config_url');
        }
        
        $data['atoml'] = '<atom:link href="' . $data['store_url'] . 'rss" rel="self" type="application/rss+xml" />';
        
        
        // Build date.
        $data['last_build_date'] = date(DATE_RSS);
        
        // Currency symbol.
        $data['currency'] = $this->session->data['currency'];
  
        // Get items .
        $products = $this->model_catalog_product->getApp($start, $limit);
        // Items array for template.
        $items = [];
        foreach ($products as $product) {
            
            $manu_name = $this->db->query("SELECT  `name` FROM `" . DB_PREFIX . "manufacturer` WHERE `manufacturer_id` ='".$product['manufacturer_id']."'");
            if($manu_name->num_rows){
            $brand = $manu_name->row['name'];
            }else{
              $brand = '';  
            }
            $item = [];
            
            if($product['quantity'] > 0){
              $item['availability']  = 'in stock';
            }else{
                $item['availability']  = 'preorder';
            }
            //brand
            $item['brand'] = $brand;
            
            //guid
            $item['guid'] = $product['product_id'];
            // Title.
            $item['title'] = html_entity_decode($product['name']);
            // Description.
            $item['description'] = html_entity_decode($product['description']);
            // Link
            $item['link'] = $this->url->link('product/product', 'product_id=' . $product['product_id']);
            $item['image_link'] = $product['image'];
            // Created date.
            $item['pubDate'] = date(DATE_RSS, strtotime($product['date_available']));
            
            // Enclosure.
            $item['enclosure'] = $this->model_tool_image->resize($product['image'], 500, 500);
            // Price.
            $item['price'] = round($product['price'], 2);
            // condition.
            $item['condition'] = 'new';
            // Check if product is on sale.
            if (!empty($product['special']) && $product['special'] > 0) {
                $item['price'] = round($product['special'], 2);
                $item['old_price'] = round($product['price'], 2);
                $item['discount'] = round(100 - ($product['special'] / $product['price'] * 100), 2);
            }
            $items[] = $item;
        }
        $data['items'] = $items;
        // Return output.
        $this->response->setOutput($this->load->view('extension/feed/rss', $data));
    }



}
