<?php
/**
 * Shipping Countdown Module
 * Displays countdown timer for same-day shipping
 *
 * @author Norwit
 * @copyright 2026
 * @license MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ShippingCountdown extends Module
{
    public function __construct()
    {
        $this->name = 'shippingcountdown';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Shipping Countdown');
        $this->description = $this->l('Displays countdown timer for same-day shipping deadline.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayShippingCountdown')
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('SHIPPING_COUNTDOWN_HOUR', 13)
            && Configuration::updateValue('SHIPPING_COUNTDOWN_MINUTE', 0)
            && Configuration::updateValue('SHIPPING_COUNTDOWN_ENABLED', 1)
            && Configuration::updateValue('SHIPPING_COUNTDOWN_WEEKEND', 0);
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('SHIPPING_COUNTDOWN_HOUR')
            && Configuration::deleteByName('SHIPPING_COUNTDOWN_MINUTE')
            && Configuration::deleteByName('SHIPPING_COUNTDOWN_ENABLED')
            && Configuration::deleteByName('SHIPPING_COUNTDOWN_WEEKEND');
    }

    /**
     * Module configuration page
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitShippingCountdown')) {
            $hour = (int) Tools::getValue('SHIPPING_COUNTDOWN_HOUR');
            $minute = (int) Tools::getValue('SHIPPING_COUNTDOWN_MINUTE');
            $enabled = (int) Tools::getValue('SHIPPING_COUNTDOWN_ENABLED');
            $weekend = (int) Tools::getValue('SHIPPING_COUNTDOWN_WEEKEND');

            if ($hour < 0 || $hour > 23) {
                $output .= $this->displayError($this->l('Invalid hour. Please enter a value between 0 and 23.'));
            } elseif ($minute < 0 || $minute > 59) {
                $output .= $this->displayError($this->l('Invalid minute. Please enter a value between 0 and 59.'));
            } else {
                Configuration::updateValue('SHIPPING_COUNTDOWN_HOUR', $hour);
                Configuration::updateValue('SHIPPING_COUNTDOWN_MINUTE', $minute);
                Configuration::updateValue('SHIPPING_COUNTDOWN_ENABLED', $enabled);
                Configuration::updateValue('SHIPPING_COUNTDOWN_WEEKEND', $weekend);
                $output .= $this->displayConfirmation($this->l('Settings updated successfully.'));
            }
        }

        return $output . $this->displayForm();
    }

    /**
     * Build configuration form
     */
    protected function displayForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Shipping Countdown Settings'),
                    'icon' => 'icon-time',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable countdown'),
                        'name' => 'SHIPPING_COUNTDOWN_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Shipping deadline hour'),
                        'name' => 'SHIPPING_COUNTDOWN_HOUR',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Hour (0-23) when same-day shipping ends.'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Shipping deadline minute'),
                        'name' => 'SHIPPING_COUNTDOWN_MINUTE',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Minute (0-59) when same-day shipping ends.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Show on weekends'),
                        'name' => 'SHIPPING_COUNTDOWN_WEEKEND',
                        'is_bool' => true,
                        'desc' => $this->l('If disabled, countdown will not show on Saturday and Sunday.'),
                        'values' => [
                            ['id' => 'weekend_on', 'value' => 1, 'label' => $this->l('Yes')],
                            ['id' => 'weekend_off', 'value' => 0, 'label' => $this->l('No')],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitShippingCountdown';

        $helper->fields_value['SHIPPING_COUNTDOWN_ENABLED'] = Configuration::get('SHIPPING_COUNTDOWN_ENABLED');
        $helper->fields_value['SHIPPING_COUNTDOWN_HOUR'] = Configuration::get('SHIPPING_COUNTDOWN_HOUR');
        $helper->fields_value['SHIPPING_COUNTDOWN_MINUTE'] = Configuration::get('SHIPPING_COUNTDOWN_MINUTE');
        $helper->fields_value['SHIPPING_COUNTDOWN_WEEKEND'] = Configuration::get('SHIPPING_COUNTDOWN_WEEKEND');

        return $helper->generateForm([$fields_form]);
    }

    /**
     * Hook to add CSS and JS
     */
    public function hookActionFrontControllerSetMedia()
    {
        if (!Configuration::get('SHIPPING_COUNTDOWN_ENABLED')) {
            return;
        }

        $this->context->controller->registerStylesheet(
            'shippingcountdown-css',
            'modules/' . $this->name . '/views/css/shippingcountdown.css',
            ['media' => 'all', 'priority' => 200]
        );

        $this->context->controller->registerJavascript(
            'shippingcountdown-js',
            'modules/' . $this->name . '/views/js/shippingcountdown.js',
            ['position' => 'bottom', 'priority' => 200]
        );

        // Pass configuration to JavaScript with translations based on current language
        $langIso = $this->context->language->iso_code;

        if ($langIso === 'pl') {
            $texts = [
                'textPrefix' => 'Nie zwlekaj z zamówieniem!',
                'textOrder' => 'zamów teraz',
                'textShip' => 'a wyślemy za',
                'textTomorrow' => 'Zamów dziś, wyślemy jutro!',
                'textWeekend' => 'Zamów teraz, wyślemy w poniedziałek!',
            ];
        } else {
            $texts = [
                'textPrefix' => 'Do not delay your order!',
                'textOrder' => 'order now',
                'textShip' => 'and we ship in',
                'textTomorrow' => 'Order today, we ship tomorrow!',
                'textWeekend' => 'Order now, we ship on Monday!',
            ];
        }

        Media::addJsDef([
            'shippingCountdown' => array_merge([
                'hour' => (int) Configuration::get('SHIPPING_COUNTDOWN_HOUR'),
                'minute' => (int) Configuration::get('SHIPPING_COUNTDOWN_MINUTE'),
                'showOnWeekend' => (bool) Configuration::get('SHIPPING_COUNTDOWN_WEEKEND'),
            ], $texts),
        ]);
    }

    /**
     * Custom hook for shipping countdown display
     */
    public function hookDisplayShippingCountdown($params)
    {
        if (!Configuration::get('SHIPPING_COUNTDOWN_ENABLED')) {
            return '';
        }

        if (!$this->shouldShowCountdown()) {
            return '';
        }

        // Check if cart is empty
        if (!isset($this->context->cart) || $this->context->cart->nbProducts() == 0) {
            return '';
        }

        return $this->fetch('module:shippingcountdown/views/templates/hook/countdown.tpl');
    }

    /**
     * Check if countdown should be displayed
     */
    private function shouldShowCountdown()
    {
        $showOnWeekend = (bool) Configuration::get('SHIPPING_COUNTDOWN_WEEKEND');
        $dayOfWeek = (int) date('N'); // 1 = Monday, 7 = Sunday

        // If weekend display is disabled and it's Saturday (6) or Sunday (7)
        if (!$showOnWeekend && $dayOfWeek >= 6) {
            return false;
        }

        return true;
    }
}
