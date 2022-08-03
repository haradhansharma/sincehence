<?php
class ControllerExtensionModuleOptionsInPopup extends Controller
{

    private $base_name = 'options_in_popup';

    private $oc_module_dir = 'extension/module/';
    private $module_path;
    private $setting_code;

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (version_compare(VERSION, '2.3', '<')) {
            $this->oc_module_dir = 'module/';
        }

        $this->module_path  = $this->oc_module_dir . $this->base_name;
        $this->setting_code = 'module_' . $this->base_name;
    }

    public function index()
    {
        // Get config
        $get_config = $this->config->get($this->setting_code . '_config');

        $config_keys = array(
            'not_required_option'  => '',
            'live_price_update'    => '',
            'select_first_values'  => '',
            'load_datetimepicker'  => '',

            'show_product_thumb'   => '',
            'show_product_details' => '',
            'show_description'     => '',
            'show_quantity'        => '',
            'show_total_price'     => '',

            'show_brand'           => '',
            'show_model'           => '',
            'show_sku'             => '',
            'show_reward'          => '',
            'show_stock'           => '',
            'show_price'           => '',
            'show_tax'             => '',
            'show_points'          => '',
            'show_discounts'       => '',

            'popup_width'          => '500',
            'product_thumb_height' => '200',
            'product_thumb_width'  => '200',
            'option_image_height'  => '50',
            'option_image_width'   => '50',
        );

        foreach ($config_keys as $key => $value) {
            if (isset($get_config[$key])) {
                $config[$key] = $get_config[$key];
            } else {
                $config[$key] = $value;
            }
        }

        // Get Product ID
        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $data['product_id'] = $product_id;

        // Get Product Info
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        $data['oip_title'] = $product_info['name'];

        $data['manufacturer']     = $product_info['manufacturer'];
        $data['manufacturer_url'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);

        $data['model']  = $product_info['model'];
        $data['sku']    = $product_info['sku'];
        $data['reward'] = $product_info['reward'];
        $data['points'] = $product_info['points'];

        // Get language
        $this->load->language('product/product');
        $data['text_select']            = $this->language->get('text_select');
        $data['text_brand']             = $this->language->get('text_manufacturer');
        $data['text_model']             = $this->language->get('text_model');
        $data['text_sku']               = 'SKU:';
        $data['text_reward']            = $this->language->get('text_reward');
        $data['text_points']            = $this->language->get('text_points');
        $data['text_stock']             = $this->language->get('text_stock');
        $data['text_discount']          = $this->language->get('text_discount');
        $data['text_tax']               = $this->language->get('text_tax');
        $data['text_option']            = $this->language->get('text_option');
        $data['text_minimum']           = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
        $data['text_loading']           = $this->language->get('text_loading');
        $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
        $data['button_cart']            = $this->language->get('button_cart');
        $data['button_upload']          = $this->language->get('button_upload');
        $data['text_quantity']          = $this->language->get('entry_qty');

        if (version_compare(VERSION, '2.3', '>=')) {
            $this->load->language('extension/total/total');
        } else {
            $this->load->language('total/total');
        }

        $data['text_total'] = $this->language->get('text_total');

        $data['product_page_url'] = $this->url->link('product/product', 'product_id=' . $product_id);

        if ($product_info['quantity'] <= 0) {
            $data['stock'] = $product_info['stock_status'];
        } elseif ($this->config->get('config_stock_display')) {
            $data['stock'] = $product_info['quantity'];
        } else {
            $data['stock'] = $this->language->get('text_instock');
        }

        // Get Product Image
        $this->load->model('tool/image');

        if ($config['show_product_thumb']) {
            if ($product_info['image']) {
                $data['thumb'] = $this->model_tool_image->resize($product_info['image'], $config['product_thumb_width'], $config['product_thumb_height']);
            } else {
                $data['thumb'] = $this->model_tool_image->resize('no_image.png', $config['product_thumb_width'], $config['product_thumb_height']);
            }
        } else {
            $data['thumb'] = '';
        }

        // Get Description
        if ($config['show_description']) {
            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
        } else {
            $data['description'] = false;
        }

        // Get Price and Taxes
        $data['price']   = false;
        $data['special'] = false;
        $data['tax']     = false;

        if ($config['show_price']) {
            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            }

            if ($product_info['special']) {
                $data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            }
        }

        if ($config['show_tax'] && $this->config->get('config_tax')) {
            $data['tax'] = $this->currency->format((float) $product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
        }

        // Get Discounts
        $data['discounts'] = array();

        if ($config['show_discounts']) {
            $discounts = $this->model_catalog_product->getProductDiscounts($product_id);

            foreach ($discounts as $discount) {
                $data['discounts'][] = array(
                    'quantity' => $discount['quantity'],
                    'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                );
            }
        }

        // Get Product Minimum
        if ($product_info['minimum']) {
            $data['minimum'] = $product_info['minimum'];
        } else {
            $data['minimum'] = 1;
        }

        // Get Options
        $data['options'] = array();

        foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
            $product_option_value_data = array();

            foreach ($option['product_option_value'] as $option_value) {
                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                    if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
                        $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }

                    $product_option_value_data[] = array(
                        'price_value'             => $option_value['price'],
                        'product_option_value_id' => $option_value['product_option_value_id'],
                        'option_value_id'         => $option_value['option_value_id'],
                        'name'                    => $option_value['name'],
                        'image'                   => $this->model_tool_image->resize($option_value['image'], $config['option_image_width'], $config['option_image_height']),
                        'price'                   => $price,
                        'price_prefix'            => $option_value['price_prefix'],
                    );
                }
            }

            $data['options'][] = array(
                'product_option_id'    => $option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $option['option_id'],
                'name'                 => $option['name'],
                'type'                 => $option['type'],
                'value'                => $option['value'],
                'required'             => $option['required'],
            );
        }

        // Get Recurrings
        $data['recurrings'] = $this->model_catalog_product->getProfiles($product_id);

        // Make configs available in the template
        foreach ($config as $key => $value) {
            $data[$key] = $value;
        }

        // Load template
        if (VERSION >= 2.2) {
            $this->response->setOutput($this->load->view($this->module_path, $data));
        } else {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $this->module_path . '.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/' . $this->module_path . '.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/' . $this->module_path . '.tpl', $data));
            }
        }
    }

    public function preload()
    {
        // Get module configuration
        $get_config = $this->config->get($this->setting_code . '_config');

        // Load common resources
        if (!empty($get_config['load_datetimepicker'])) {
            if (version_compare(VERSION, '3.0', '>=')) {
                $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
            } else {
                $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
            }

            $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        }

        $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
        $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');

        // Get current theme directory
        if (version_compare(VERSION, '2.2', '>=')) {
            if ($this->config->get('config_theme') == 'default' || $this->config->get('config_theme') == 'theme_default') {
                $theme_dir = $this->config->get('theme_default_directory');
            } else {
                $theme_dir = str_replace('theme_', '', $this->config->get('config_theme'));
            }
        } else {
            $theme_dir = $this->config->get('config_template');
        }

        // Load module resources
        if (file_exists(DIR_TEMPLATE . $theme_dir . '/stylesheet/' . $this->base_name . '.css')) {
            $this->document->addStyle('catalog/view/theme/' . $theme_dir . '/stylesheet/' . $this->base_name . '.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->base_name . '.css');
        }

        if (file_exists(DIR_TEMPLATE . $theme_dir . '/js/' . $this->base_name . '.js')) {
            $this->document->addScript('catalog/view/theme/' . $theme_dir . '/js/' . $this->base_name . '.js');
        } else {
            $this->document->addScript('catalog/view/theme/default/js/' . $this->base_name . '.js');
        }

        // Create html block in <HEAD> element
        $select_first_values = 0;

        if (!empty($get_config['select_first_values'])) {
            $select_first_values = 1;
        }

        $live_price_update = 0;

        if (!empty($get_config['live_price_update'])) {
            $live_price_update = 1;
        }

        $html = '<!-- Options in Pop-up -->';
        $html .= "\n";
        $html .= '<script>';
        $html .= "\n";
        $html .= 'window.oipData = {';
        $html .= 'select_first_values: ' . $select_first_values . ',';
        $html .= 'live_price_update: ' . $live_price_update . ',';
        $html .= 'base_path: \'' . $this->module_path . '\'';
        $html .= '};';
        $html .= "\n";
        $html .= '</script>';
        $html .= "\n";

        $custom_css = html_entity_decode($this->config->get($this->setting_code . '_custom_css'), ENT_QUOTES, 'UTF-8');

        if (!empty($custom_css)) {
            $custom_css = str_replace(array("\r", "\n"), "", $custom_css);
        }

        if (!empty($custom_css)) {
            $html .= '<style>' . $custom_css . '</style>';
            $html .= "\n";
        }

        $html .= '<!-- Options in Pop-up END -->';
        $html .= "\n";

        return preg_replace('/\s\s+/', '', $html);
    }

    public function updatePrice()
    {
        $json = array();

        $get_config = $this->config->get($this->setting_code . '_config');

        if (!empty($get_config['live_price_update']) && !empty($this->request->post['product_id'])) {
            $product_id = $this->request->post['product_id'];

            $this->load->model('catalog/product');

            $product_info = $this->model_catalog_product->getProduct($product_id);

            $price   = $product_info['price'];
            $special = $product_info['special'];
            $points  = $product_info['points'];

            if (!empty($this->request->post['quantity'])) {
                $quantity = $this->request->post['quantity'];
            } else {
                $quantity = $product_info['minimum'];
            }

            // Get Discounts
            $product_discounts = $this->model_catalog_product->getProductDiscounts($product_id);

            foreach ($product_discounts as $product_discount) {
                if ($quantity >= $product_discount['quantity']) {
                    $price = $product_discount['price'];
                }
            }

            // Get Options
            if (!empty($this->request->post['option'])) {
                $selected_options = $this->request->post['option'];

                $product_options = $this->model_catalog_product->getProductOptions($product_id);

                foreach ($product_options as $product_option) {

                    foreach ($product_option['product_option_value'] as $product_option_value) {
                        $product_option_value_data = array(
                            'price'         => $product_option_value['price'],
                            'price_prefix'  => $product_option_value['price_prefix'],
                            'points'        => (isset($product_option_value['points']) ? $product_option_value['points'] : 0),
                            'points_prefix' => (isset($product_option_value['points_prefix']) ? $product_option_value['points_prefix'] : '+'),
                        );

                        foreach ($selected_options as $selected_option) {
                            if (!empty($selected_option)) {
                                if (is_array($selected_option)) {
                                    foreach ($selected_option as $option_checkbox) {
                                        if ($option_checkbox == $product_option_value['product_option_value_id']) {
                                            $selected_options_data[] = $product_option_value_data;
                                        }
                                    }
                                } else {
                                    if ($selected_option == $product_option_value['product_option_value_id']) {
                                        $selected_options_data[] = $product_option_value_data;
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($selected_options_data)) {
                    foreach ($selected_options_data as $option) {
                        // Calculate Price
                        if ($option['price_prefix'] == '=') {
                            if (!empty($special)) {
                                $special = $option['price'];
                            }
                            $price = $option['price'];

                        } elseif ($option['price_prefix'] == '+') {
                            if (!empty($special)) {
                                $special += $option['price'];
                            }
                            $price += $option['price'];

                        } else {
                            if (!empty($special)) {
                                $special -= $option['price'];
                            }
                            $price -= $option['price'];
                        }

                        // Calculate Points
                        if ($option['points_prefix'] == '=') {
                            $points = $option['points'];
                        } elseif ($option['points_prefix'] == '+') {
                            $points += $option['points'];
                        } else {
                            $points -= $option['points'];
                        }
                    }
                }
            }

            $json = array(
                'price'         => $this->currency->format($this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                'special'       => !empty($special) ? $this->currency->format($this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']) : false,
                'ex_tax'        => $this->currency->format($special ? (float) $special : $price, $this->session->data['currency']),
                'points'        => $points,
                'total_price'   => $this->currency->format($this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity, $this->session->data['currency']),
                'total_special' => !empty($special) ? $this->currency->format($this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax')) * $quantity, $this->session->data['currency']) : false,
                'total_ex_tax'  => $this->currency->format(($special ? (float) $special : $price) * $quantity, $this->session->data['currency']),
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
