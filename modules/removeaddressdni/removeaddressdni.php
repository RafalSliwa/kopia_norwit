<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

class RemoveAddressDni extends Module
{
    public function __construct()
    {
        $this->name = 'removeaddressdni';
        $this->version = '1.1.3';
        $this->author = 'Norwit';
        $this->tab = 'administration';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Remove National Identity Document (DNI) - Force VAT Prefix');
        $this->description = $this->l('Removes the National Identity Document field and ensures VAT number has country prefix for PL.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionAddressFormBuilderModifier')
            && $this->registerHook('actionCustomerAddressFormBuilderModifier')
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayCustomerTypeSelector');
            
    }

    /**
     * BO: Remove DNI + force PL VAT
     */
    public function hookActionAddressFormBuilderModifier($params)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];

        // Remove DNI
        $formBuilder->remove('dni');

        // Modify VAT
        $this->addVatField($formBuilder, $params);
    }

    /**
     * FO: Remove DNI + force PL VAT
     */
    public function hookActionCustomerAddressFormBuilderModifier($params)
    {
        /** @var FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];

        // Remove DNI
        $formBuilder->remove('dni');

        // Modify VAT
        $this->addVatField($formBuilder, $params);
    }

    /**
     * Format VAT number: add prefix, remove non-digits, handle any country.
     */
    private function formatVatNumber($vatNumber, $countryIso)
    {
        $vatDigits = preg_replace('/^' . preg_quote($countryIso, '/') . '/i', '', $vatNumber);
        $vatDigits = preg_replace('/\D/', '', $vatDigits);
        return $countryIso . $vatDigits;
    }

    /**
     * Add or modify VAT field: prefill + validation.
     */
    private function addVatField(FormBuilderInterface $formBuilder, $params)
    {
        $useSameAddress = isset($params['data']['use_same_address']) ? $params['data']['use_same_address'] : '0';

        // VAT wymagany tylko dla faktury, firmy i zaznaczonego checkboxa
        if (
            (isset($params['type']) && $params['type'] === 'invoice') &&
            ($useSameAddress == '1' || $useSameAddress == 1) &&
            (isset($params['data']['customer_type']) && $params['data']['customer_type'] === 'company')
        ) {
            $vatNumber = $params['data']['vat_number'] ?? '';
            $idCountry = $params['data']['id_country'] ?? \Configuration::get('PS_COUNTRY_DEFAULT');
            $countryIso = '';
            if ($idCountry) {
                $countryIso = \Country::getIsoById($idCountry);
            }
            if ($countryIso === 'PL') {
                $vatNumber = $this->formatVatNumber($vatNumber, 'PL');
                $formBuilder->add('vat_number', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'label' => $this->l('EU VAT (PL + 10 digits)'),
                    'required' => true,
                    'data' => $vatNumber,
                    'attr' => [
                        'placeholder' => 'PL1234567890',
                        'maxlength' => 12,
                        'oninput' => "if(!this.value.startsWith('PL')){this.value='PL'+this.value.replace(/[^0-9]/g,'').slice(0,10);}else{var digits=this.value.slice(2).replace(/[^0-9]/g,'').slice(0,10);this.value='PL'+digits;}",
                    ],
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^PL[0-9]{10}$/',
                            'message' => $this->l('Enter VAT in format PL and 10 digits, no spaces or dashes.'),
                        ]),
                    ],
                ]);
            } else {
                // Dla innych krajów: VAT z prefixem kraju, wymagane
                if ($countryIso) {
                    $vatNumber = $this->formatVatNumber($vatNumber, $countryIso);
                }
                $formBuilder->add('vat_number', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                    'label' => $this->l('VAT number'),
                    'required' => true,
                    'data' => $vatNumber,
                    'attr' => [
                        'placeholder' => $this->l('Enter VAT number (required)'),
                    ],
                    // 'constraints' => [],
                ]);
            }
            return;
        }

        // W pozostałych przypadkach VAT nie jest wymagany
        $vatNumber = $params['data']['vat_number'] ?? '';
        $idCountry = $params['data']['id_country'] ?? \Configuration::get('PS_COUNTRY_DEFAULT');
        $countryIso = '';
        if ($idCountry) {
            $countryIso = \Country::getIsoById($idCountry);
        }
        if ($countryIso) {
            $vatNumber = $this->formatVatNumber($vatNumber, $countryIso);
        }
        $formBuilder->add('vat_number', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            'label' => $this->l('VAT number'),
            'required' => false,
            'data' => $vatNumber,
            'attr' => [
                'placeholder' => $this->l('Enter VAT number (optional)'),
            ],
            // 'constraints' => [],
        ]);
    }

    public function hookDisplayHeader($params)
    {
        // Pass customer_type from POST to Smarty to keep selection after refresh
        $customerType = Tools::getValue('customer_type', null);
        if ($customerType !== null) {
            $this->context->smarty->assign('customer_type', $customerType);
        }
        // Pass use_same_address from POST to Smarty to keep checkbox state after refresh
        if (Tools::isSubmit('use_same_address')) {
            $useSameAddress = Tools::getValue('use_same_address', '1');
            $this->context->smarty->assign('use_same_address', $useSameAddress);
        }
        // Add custom CSS
        $this->context->controller->registerStylesheet(
            'module-removeaddressdni',
            'modules/' . $this->name . '/views/css/removeaddressdni.css',
            ['media' => 'all', 'priority' => 150]
        );
        // Add JS for customer type selection
        $this->context->controller->registerJavascript(
            'module-removeaddressdni-customer-type-toggle',
            'modules/' . $this->name . '/views/js/customer-type-toggle.js',
            ['position' => 'bottom', 'priority' => 150]
        );
        // Pass VAT validation message to Smarty for JS translation
        $this->context->smarty->assign('vatValidationMsg', $this->l('Enter exactly 10 digits after PL in the VAT field!'));

        // Przykład użycia formatVatNumber do przekazania VAT do Smarty (jeśli chcesz wyświetlić w JS lub tpl)
        $vatNumber = Tools::getValue('vat_number', '');
        $idCountry = Tools::getValue('id_country', \Configuration::get('PS_COUNTRY_DEFAULT'));
        $countryIso = '';
        if ($idCountry) {
            $countryIso = \Country::getIsoById($idCountry);
        }
        if ($vatNumber && $countryIso) {
            $vatNumber = $this->formatVatNumber($vatNumber, $countryIso);
            $this->context->smarty->assign('vat_number_formatted', $vatNumber);
        }
    }

    public function hookDisplayCustomerTypeSelector($params)
    {
        // Pass variables to template if needed
        $this->context->smarty->assign([
            'customer_type' => $this->context->cookie->customer_type ?? 'private',
        ]);
        return $this->display(__FILE__, 'views/templates/hook/customer_type_selector.tpl');
    }
}
