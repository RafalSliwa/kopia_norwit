<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class GmOmni extends Module {

    protected $textColor = '';
    protected $priceColor = '';

    public function __construct() {
        $this->name = 'gmomni';
        $this->prefix = strtoupper($this->name);
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'GreenMouseStudio.com';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Omni');
        $this->description = $this->l('The simplest Omnibus module for fair stores');

        $this->ps_versions_compliancy = array('min' => '1.7.0', 'max' => _PS_VERSION_);
        $this->getConfiguration();
    }

    public function getConfiguration() {
        $this->textColor = Configuration::get($this->prefix . '_TEXT_COLOR');
        $this->priceColor = Configuration::get($this->prefix . '_PRICE_COLOR');
    }

    public function install() {
        if (parent::install() &&
                $this->registerHook('displayProductPriceBlock')
        ) {
            Configuration::updateValue($this->prefix . '_TEXT_COLOR', '#666666');
            Configuration::updateValue($this->prefix . '_PRICE_COLOR', '#666666');
            return true;
        }
        return false;
    }

    public function uninstall() {
        if (!parent::uninstall()) {
            return false;
        }
        Configuration::deleteByName($this->prefix . '_TEXT_COLOR');
        Configuration::deleteByName($this->prefix . '_PRICE_COLOR');
        return true;
    }

    public function getContent() {
        $content = '';
        $content .= $this->postProcess();
        $content .= $this->displayForm();
        $content .= $this->displayInformationPanel();
        $content .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/gms.tpl');
        return $content;
    }

    protected function postProcess() {
        $output = '';
        if (Tools::isSubmit('submit' . $this->name)) {

            $this->textColor = Tools::getValue($this->prefix . '_TEXT_COLOR');
            Configuration::updateValue($this->prefix . '_TEXT_COLOR', $this->textColor);

            $this->priceColor = Tools::getValue($this->prefix . '_PRICE_COLOR');
            Configuration::updateValue($this->prefix . '_PRICE_COLOR', $this->priceColor);

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output;
    }

    public function displayForm() {
        $helper = new HelperForm();
        $groups = Group::getGroups($this->context->language->id);
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => $this->prefix . '_TEXT_COLOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Price color'),
                        'name' => $this->prefix . '_PRICE_COLOR',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value[$this->prefix . '_TEXT_COLOR'] = $this->textColor;
        $helper->fields_value[$this->prefix . '_PRICE_COLOR'] = $this->priceColor;
        return $helper->generateForm(array($fieldsForm));
    }

    public function hookDisplayProductPriceBlock($params) {
        if ($params['type'] == 'after_price') {
            $product = $params['product'];
            $hasDiscount = (bool) $product['has_discount'];
            if ($hasDiscount) {
                $this->context->smarty->assign(
                        [
                            'gmOmniColor' => $this->textColor,
                            'gmOmniPriceColor' => $this->priceColor,
                            'gmOmniLowestPrice' => $product['regular_price']
                        ]
                );
                return $this->display(__FILE__, 'gmomni.tpl');
            }
        }
        return false;
    }

    protected function displayInformationPanel() {
        $output = '<div class="panel">'
                . '<div class="panel-heading"><i class="icon-info"></i> '
                . $this->l('Important information')
                . '</div>';
        $output .= '<p>' . $this->l('You can use this module only if your regular price is the real lowest price before the current promotion and you are able to prove it.') . '</p>';
        $output .= '<p>' . $this->l('If you need a module that will gather the price history for you, check out') . ' <a target="_blank" href="https://1.envato.market/mgJW7Z">OmniPrice</a>.</p>';
        $output .= '</div>';
        return $output;
    }

}
