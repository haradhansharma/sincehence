<?php
class ControllerExtensionModuleOptionsInPopup extends Controller
{

    private $name          = 'Options in Pop-up';
    private $version       = '1.8.2';
    private $founded       = '2016';
    private $author        = 'MagDevel';
    private $support_email = 'support@magdevel.com';

    private $base_name = 'options_in_popup';
    private $error     = array();

    private $extension_dir;
    private $extension_type_dir;
    private $token_url;
    private $extension_type;
    private $tpl_ext;
    private $base_path;
    private $setting_code;

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (version_compare(VERSION, '3.0', '>=')) {
            $this->extension_dir      = 'marketplace/extension';
            $this->extension_type_dir = 'extension/module';
            $this->token_url          = 'user_token=' . $this->session->data['user_token'];
            $this->extension_type     = '&type=module';
            $this->tpl_ext            = '';
        } elseif (version_compare(VERSION, '2.3', '>=')) {
            $this->extension_dir      = 'extension/extension';
            $this->extension_type_dir = 'extension/module';
            $this->token_url          = 'token=' . $this->session->data['token'];
            $this->extension_type     = '&type=module';
            $this->tpl_ext            = '.tpl';
        } else {
            $this->extension_dir      = 'extension/module';
            $this->extension_type_dir = 'module';
            $this->token_url          = 'token=' . $this->session->data['token'];
            $this->extension_type     = '';
            $this->tpl_ext            = '.tpl';
        }

        $this->base_path    = $this->extension_type_dir . '/' . $this->base_name;
        $this->setting_code = 'module_' . $this->base_name;
    }

    public function index()
    {
        $this->load->language($this->base_path);

        $this->document->setTitle(strip_tags($this->language->get('heading_title')));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $settings = array();

            foreach ($this->request->post as $key => $value) {
                $settings[$this->setting_code . '_' . $key] = $value;
            }

            $this->model_setting_setting->editSetting($this->setting_code, $settings);

            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link($this->extension_dir, $this->token_url . '&type=module', 'SSL'));
        }

        $data['apply_url'] = $this->url->link($this->base_path . '/apply&' . $this->token_url, '', 'SSL');

        // Get Language
        $language_variables = array(
            'heading_title',
            'button_save',
            'button_apply',
            'button_cancel',
            'text_edit',
            'text_success',
            'text_enabled',
            'text_disabled',

            'tab_general',
            'entry_status',
            'entry_not_required_option',
            'entry_live_price_update',
            'entry_select_first_value',
            'entry_load_datetimepicker',

            'tab_layout',
            'entry_show_product_thumb',
            'entry_show_product_details',
            'entry_show_description',
            'entry_show_quantity',
            'entry_show_total_price',

            'tab_product_details',
            'entry_show_brand',
            'entry_show_model',
            'entry_show_sku',
            'entry_show_reward',
            'entry_show_stock',
            'entry_show_price',
            'entry_show_tax',
            'entry_show_points',
            'entry_show_discounts',

            'tab_additional_settings',
            'entry_popup_style',
            'text_max_width',
            'entry_product_thumb_size',
            'entry_option_image_size',
            'text_height',
            'text_width',
            'entry_custom_css',
            'text_enter_your_code',
            'tab_support',
            'text_need_help',
            'text_contact_us',
            'text_provide_credentials',
            'text_version',
        );

        foreach ($language_variables as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['support_href']   = 'mailto:' . $this->support_email . '?Subject=Request Support: ' . $this->name . '&body=Shop: ' . HTTP_CATALOG . ', OpenCart: ' . VERSION . ', ' . $this->name . ': ' . $this->version;
        $data['support_email']  = $this->support_email;
        $data['module_version'] = $this->version;
        $data['text_copyright'] = $this->author . ' © ' . $this->founded . '-' . date('Y');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token_url, 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link($this->extension_dir, $this->token_url . $this->extension_type, 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->base_path, $this->token_url, 'SSL'),
        );

        $data['action'] = $this->url->link($this->base_path, $this->token_url, 'SSL');
        $data['cancel'] = $this->url->link($this->extension_dir, $this->token_url . $this->extension_type, 'SSL');

        // Status
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (null !== $this->config->get($this->setting_code . '_status')) {
            $data['status'] = $this->config->get($this->setting_code . '_status');
        } else {
            $data['status'] = '';
        }

        // Settings
        $get_config = $this->config->get($this->setting_code . '_config');

        $config_variables = array(
            'not_required_option'  => '1',
            'live_price_update'    => '1',
            'select_first_values'  => '0',
            'load_datetimepicker'  => '0',

            'show_product_thumb'   => '1',
            'show_product_details' => '1',
            'show_description'     => '0',
            'show_quantity'        => '1',
            'show_total_price'     => '1',

            'show_brand'           => '1',
            'show_model'           => '1',
            'show_sku'             => '0',
            'show_reward'          => '1',
            'show_stock'           => '1',
            'show_price'           => '1',
            'show_tax'             => '1',
            'show_points'          => '0',
            'show_discounts'       => '0',

            'popup_width'          => '500',
            'product_thumb_height' => '200',
            'product_thumb_width'  => '200',
            'option_image_height'  => '50',
            'option_image_width'   => '50',
        );

        foreach ($config_variables as $key => $value) {
            if (isset($this->request->post['config'][$key])) {
                $data[$key] = $this->request->post['config'][$key];
            } elseif (isset($get_config[$key])) {
                $data[$key] = $get_config[$key];
            } elseif (empty($get_config)) {
                $data[$key] = $value;
            } else {
                $data[$key] = '';
            }
        }

        // Custom CSS
        if (isset($this->request->post['custom_css'])) {
            $data['custom_css'] = $this->request->post['custom_css'];
        } elseif (null !== $this->config->get($this->setting_code . '_custom_css')) {
            $data['custom_css'] = $this->config->get($this->setting_code . '_custom_css');
        } else {
            $data['custom_css'] = '';
        }

        // Load resources
        $this->document->addStyle('view/javascript/' . $this->base_name . '/bootstrap-toggle/css/bootstrap-toggle.min.css');
        $this->document->addScript('view/javascript/' . $this->base_name . '/bootstrap-toggle/js/bootstrap-toggle.min.js');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->base_path . $this->tpl_ext, $data));
    }

    public function apply()
    {
        $this->load->language($this->base_path);

        $this->load->model('setting/setting');

        $json = array();

        $settings = $this->request->post;

        if ($this->validate()) {
            foreach ($settings as $key => $value) {
                $config_key            = $this->setting_code . '_' . $key;
                $settings[$config_key] = $value;
            }
            $this->model_setting_setting->editSetting($this->setting_code, $settings);

            $json['success'] = $this->language->get('text_success');
        } else {
            $json['error'] = $this->error['warning'];
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', $this->base_path)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function uninstall()
    {
        if ($this->validate()) {
            $this->load->model('setting/store');
            $this->load->model('setting/setting');

            $this->model_setting_setting->deleteSetting($this->setting_code, 0);

            $stores = $this->model_setting_store->getStores();

            foreach ($stores as $store) {
                $this->model_setting_setting->deleteSetting($this->setting_code, $store['store_id']);
            }
        }
    }
}
