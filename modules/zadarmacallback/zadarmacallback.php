<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/ZadarmaClient.php';

class ZadarmaCallback extends Module
{
    public function __construct()
    {
        $this->name = 'zadarmacallback';
        $this->version = '1.0.3';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Zadarma Callback');
        $this->description = $this->l('Zadarma API callback form.');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayFooter')
            && Configuration::updateValue('ZADARMA_API_KEY', '')
            && Configuration::updateValue('ZADARMA_API_SECRET', '')
            && Configuration::updateValue('ZADARMA_FROM_NUMBER', '')
            && Configuration::updateValue('ZADARMA_WEBHOOK_URL', '');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('ZADARMA_API_KEY')
            && Configuration::deleteByName('ZADARMA_API_SECRET')
            && Configuration::deleteByName('ZADARMA_FROM_NUMBER')
            && Configuration::deleteByName('ZADARMA_WEBHOOK_URL');
    }

    /**
     * Load JS and CSS
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/zadarma.js');
        $this->context->controller->addCSS($this->_path . 'views/css/zadarma.css');
        
        // Pass configuration to JavaScript
        $webhookUrl = Configuration::get('ZADARMA_WEBHOOK_URL');
        if (empty($webhookUrl)) {
            // Default URL directly to webhook.php
            $webhookUrl = Tools::getHttpHost(true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/webhook.php';
        }
        
        // ✅ DODAJ NUMER FROM DO JAVASCRIPT
        $fromNumber = Configuration::get('ZADARMA_FROM_NUMBER');
        
        Media::addJsDef([
            'zadarma_webhook_url' => $webhookUrl,
            'zadarma_from_number' => $fromNumber // ← DODANE
        ]);
    }

    /**
     * Footer form (modal)
     */
    public function hookDisplayFooter($params)
    {
        $this->context->smarty->assign([
            'link' => $this->context->link,
            'token' => Tools::getToken(false),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/form.tpl');
    }

    /**
     * Configuration panel in admin
     */
    public function getContent()
    {
        $this->_html = '';

        // ✅ Save configuration
        if (Tools::isSubmit('submitZadarmaConfig')) {
            Configuration::updateValue('ZADARMA_API_KEY', Tools::getValue('ZADARMA_API_KEY'));
            Configuration::updateValue('ZADARMA_API_SECRET', Tools::getValue('ZADARMA_API_SECRET'));
            Configuration::updateValue('ZADARMA_FROM_NUMBER', Tools::getValue('ZADARMA_FROM_NUMBER'));
            Configuration::updateValue('ZADARMA_WEBHOOK_URL', Tools::getValue('ZADARMA_WEBHOOK_URL'));
            $this->_html .= $this->displayConfirmation($this->l('Settings have been saved.'));
        }

        // 🧪 Test API connection
        if (Tools::isSubmit('testZadarmaConnection')) {
            try {
                $client = new ZadarmaClient();
                $result = $client->testConnection();
                if ($result === true) {
                    $this->_html .= $this->displayConfirmation($this->l('Zadarma connection works correctly.'));
                } else {
                    $this->_html .= $this->displayConfirmation($this->l('Zadarma connection works correctly. API response: ') . print_r($result, true));
                }
            } catch (Exception $e) {
                $this->_html .= $this->displayError($this->l('Zadarma connection error: ') . $e->getMessage());
            }
        }

        return $this->_html . $this->renderForm();
    }

    /**
     * Configuration form
     */
    private function renderForm()
    {
        $fields = new HelperForm();

        $fields->module = $this;
        $fields->name_controller = $this->name;
        $fields->token = Tools::getAdminTokenLite('AdminModules');
        $fields->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $fields->submit_action = 'submitZadarmaConfig';

        $fields->fields_value = [
            'ZADARMA_API_KEY' => Configuration::get('ZADARMA_API_KEY'),
            'ZADARMA_API_SECRET' => Configuration::get('ZADARMA_API_SECRET'),
            'ZADARMA_FROM_NUMBER' => Configuration::get('ZADARMA_FROM_NUMBER'),
            'ZADARMA_WEBHOOK_URL' => Configuration::get('ZADARMA_WEBHOOK_URL'),
        ];

        $form = [
            'form' => [
                'legend' => ['title' => $this->l('Zadarma Settings')],
                'input' => [
                    [
                        'type' => 'text',
                        'name' => 'ZADARMA_API_KEY',
                        'label' => $this->l('API Key'),
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'ZADARMA_API_SECRET',
                        'label' => $this->l('API Secret'),
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'ZADARMA_FROM_NUMBER',
                        'label' => $this->l('FROM phone number'),
                        'desc' => $this->l('Optional. Only if the number is not dynamically retrieved from the page.'),
                        'required' => false,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'ZADARMA_WEBHOOK_URL',
                        'label' => $this->l('Webhook URL'),
                        'desc' => $this->l('Endpoint URL for handling callbacks. If empty, will use default webhook.php'),
                        'required' => false,
                        'placeholder' => 'https://',
                    ],
                ],
                'buttons' => [
                    [
                        'title' => $this->l('Save'),
                        'type' => 'submit',
                        'name' => 'submitZadarmaConfig',
                        'class' => 'btn btn-default pull-right',
                    ],
                    [
                        'title' => $this->l('Test connection'),
                        'type' => 'submit',
                        'name' => 'testZadarmaConnection',
                        'icon' => 'process-icon-refresh',
                        'class' => 'btn btn-info',
                    ],
                ],
            ]
        ];

        return $fields->generateForm([$form]);
    }
}
