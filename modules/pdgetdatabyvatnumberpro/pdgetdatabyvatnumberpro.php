<?php
/**
* 2012-2022 Patryk Marek PrestaDev.pl
*
* Patryk Marek PrestaDev.pl - Pd Get data by vat number Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at info@prestadev.pl.
*
* @author    Patryk Marek PrestaDev.pl <info@prestadev.pl>
* @copyright 2012-2022 Patryk Marek - PrestaDev.pl
* @license   License is for use in domain / or one multistore enviroment (do not modify or reuse this code or part of it) if you want any changes please contact with me at info@prestadev.pl
* @link      http://prestadev.pl
* @package   Pd Get data by vat number Pro for - PrestaShop 1.5.x and 1.6.x and 1.7.x
* @version   1.0.2
* @date      7-06-2018
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\ReportTypes;
use GusApi\BulkReportTypes;

use DragonBe\Vies\Vies;
use DragonBe\Vies\ViesException;
use DragonBe\Vies\ViesServiceException;

require_once(dirname(__FILE__) . '/vendor/autoload.php');

class PdGetDataByVatnumberPro extends Module
{
    private static $sid = false;
    private $html = '';
    private $postErrors = array();
    public $regon_user_key;
    public $country_iso_codes;
    public $module_dir;
    public $ps_version_15;
    public $ps_version_16;
    public $ps_version_17;
    public $ps_version_8;
    public $secure_key;

    public static $nip_numbers_cache = [];

    public function __construct()
    {
        $this->name = 'pdgetdatabyvatnumberpro';
        $this->tab = 'front_office_features';
        $this->version = '1.1.7';
        $this->author = 'PrestaDev.pl';
        $this->need_instance = 0;
        $this->module_key = 'da20d948e259d9eb56025b9447c7312b';
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Autocomplete address data by vat number Pro');
        $this->description = $this->l('Allow to fetch customer data by VAT number using VIES or GUS database');

        $this->ps_version_15 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '=')) ? true : false;
        $this->ps_version_16 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')) ? true : false;
        $this->ps_version_17 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.7', '=')) ? true : false;
        $this->ps_version_8 = (version_compare(Tools::substr(_PS_VERSION_, 0, 3), '8.0', '>=')) ? true : false;
        $this->regon_user_key = Configuration::get('PD_GDBVNP_USER_KEY');
        $this->country_iso_codes = $this->vatEUCountries();
        $this->module_dir = _MODULE_DIR_.$this->name.'/';
    }


    public function vatEUCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }
        $coutries_iso_supported = array();
        foreach ($countries as $c) {
            $coutries_iso_supported[] = $c['iso_code'];
        }

        $vat_iso_codes = array(
            '-' => $this->l('None'),
            'PL' => $this->l('Poland (PL)'),
            'AT' => $this->l('Austria (AT)'),
            'BE' => $this->l('Belgium (BE)'),
            'BG' => $this->l('Bulgaria (BG)'),
            'CY' => $this->l('Cyprus (CY)'),
            'CZ' => $this->l('Czech Republic (CZ)'),
            'DK' => $this->l('Denmark (DK)'),
            'FI' => $this->l('Finland (FI)'),
            'FR' => $this->l('France (FR)'),
            'EE' => $this->l('Estonia (ES)'),
            'DE' => $this->l('Germany (DE)'),
            'GR' => $this->l('Greece (EL)'),
            'HU' => $this->l('Hungary (HU)'),
            'IE' => $this->l('Irland (IE)'),
            'IT' => $this->l('Italy (IT)'),
            'LV' => $this->l('Latvia (LV)'),
            'LT' => $this->l('Lithuania (LT)'),
            'LU' => $this->l('Luxembourg (LU)'),
            'NL' => $this->l('Netherlands (NL)'),
            'MT' => $this->l('Malta (MT)'),
            'PT' => $this->l('Portugal (PT)'),
            'RO' => $this->l('Romania (RO)'),
            'SK' => $this->l('Slovakia (SK)'),
            'SI' => $this->l('Slovenia (SI)'),
            'ES' => $this->l('Spain (ES)'),
            'SE' => $this->l('Sweden (SE)'),
            'GB' => $this->l('United Kingdom (GB)'),
        );

        foreach ($vat_iso_codes as $key => &$entry) {
            if (!in_array($key, $coutries_iso_supported)) {
                unset($vat_iso_codes[$key]);
            }
        }

        return $vat_iso_codes;
    }

    public function install()
    {
        if (!parent::install()
        || !$this->registerHook('displaySearchByNip')
        || !$this->registerHook('displaySearchByNipAdmin')
        || !$this->registerHook('displayBackOfficeHeader')
        || !$this->registerHook('displayHeader')
        || !Configuration::updateValue('PD_GDBVNP_USER_KEY', '')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }

    /*
    ** Form Config Methods
    **
    */
    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->postErrors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        } else {
            $this->html .= '<br />';
        }

        $this->html .= '<h2>'.$this->displayName.' (v'.$this->version.')</h2><p>'.$this->description.'</p>';
        $this->html .= $this->renderForm();
        $this->html .= '<br />';
        $this->html .= $this->_displayExtraForm();

        return $this->html;
    }

    public function renderForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('User key'),
                        'desc' => $this->l('User key used to comunicate with GUS servers more info how to get key here: http://bip.stat.gov.pl/dzialalnosc-statystyki-publicznej/rejestr-regon/interfejsyapi/'),
                        'name' => 'PD_GDBVNP_USER_KEY',
                        'size' => 42,
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save settings'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getConfigFieldsValues()
    {
        $return = array();
        $return['PD_GDBVNP_USER_KEY'] = Tools::getValue('PD_GDBVNP_USER_KEY', Configuration::get('PD_GDBVNP_USER_KEY'));
        return $return;
    }

    private function _postValidation()
    {
        if (Tools::getValue('PD_GDBVNP_USER_KEY') == '') {
            $this->postErrors[] = $this->l('Please provide user key');
        }
    }

    private function _postProcess()
    {
        Configuration::updateValue('PD_GDBVNP_USER_KEY', Tools::getValue('PD_GDBVNP_USER_KEY'));
        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }


    public function hookDisplayBackOfficeHeader($params)
    {
        Media::addJsDef(array(
            'pdgetdatabyvatnumberpro_secure_key' => $this->secure_key,
            'pdgetdatabyvatnumberpro_ajax_link' => $this->context->link->getModuleLink('pdgetdatabyvatnumberpro', 'ajax', array()),
            'pdgetdatabyvatnumberpro_response_ok' => Tools::htmlentitiesUTF8($this->l('Data was filled up in form'))
        ));

        if ($this->ps_version_15) {
            $this->context->controller->addCSS($this->_path.'views/css/styles_ps15.css', 'all');
        } elseif ($this->ps_version_16) {
            $this->context->controller->addCSS($this->_path.'views/css/styles_ps16.css', 'all');
            //$this->context->controller->addJS($this->_path.'views/js/scripts.js');
        } else {
            $this->context->controller->addCSS($this->_path.'views/css/styles_ps17.css', 'all');
        }
    }

    public function hookdisplayHeader($params)
    {
        Media::addJsDef(array(
            'pdgetdatabyvatnumberpro_secure_key' => $this->secure_key,
            'pdgetdatabyvatnumberpro_ajax_link' => $this->context->link->getModuleLink('pdgetdatabyvatnumberpro', 'ajax', array()),
            'pdgetdatabyvatnumberpro_response_ok' => Tools::htmlentitiesUTF8($this->l('Data was filled up in form'))
        ));

        if ($this->ps_version_15) {
            $this->context->controller->addCSS($this->_path.'views/css/styles_ps15.css', 'all');
        } elseif ($this->ps_version_16) {
            $this->context->controller->addCSS($this->_path.'views/css/styles_ps16.css', 'all');

        } else {
            $this->context->controller->registerStylesheet('modules-pdgetdatabyvatnumberpro-front', 'modules/'.$this->name.'/views/css/styles_ps17.css', array('media' => 'all', 'priority' => 150));
        }
    }

    public function extractVatInfo($vatNumber, $id_country): array
    {
        $result = [];
        $vatNumber = preg_replace('/[^a-zA-z0-9]/', '', $vatNumber);
        if (preg_match('/^([A-Z]{1,3})([0-9]+)$/i', $vatNumber, $matches)) {
            $code = strtoupper($matches[1]);
            $number = $matches[2];
            $result[] = [
                'code' => $code,
                'number' => $number
            ];
        } elseif (preg_match('/^\d+$/', $vatNumber, $matches)) {
            $number = strtoupper($matches[0]);
            $code =  Country::getIsoById($this->context->country->id);
            $result[] = [
                'code' => $code,
                'number' => $number
            ];
        } elseif (is_numeric($id_country)) {
            $result[] = [
                'code' => Country::getIsoById($id_country),
                'number' => $vatNumber
            ];
        } else {
            $result[] = [
                'code' => '',
                'number' => $vatNumber
            ];
        }

        return $result[0];
    }

    public function callApiByNip($nip, $id_country)
    {
        $results = $this->extractVatInfo($nip, $id_country);
        $nip_country_iso = $results['code'];
        $nip = $results['number'];

        if ($nip_country_iso == 'PL') {
            return $this->callRegonApiByNip($nip);
        } else {
            return $this->callViesApiByNip($nip, $nip_country_iso);
        }
    }

    public function getRegExpByCountryIsoCode()
    {
        $xml = simplexml_load_file(dirname(__FILE__).'/vendor/postalCodeData.xml');
        $postcodes_regexp = array();
        foreach ($xml->postalCodeData->children() as $reg_country) {
            $iso_code = (string)$reg_country->Attributes();
            $reg_exp = (string)$reg_country;
            $postcodes_regexp[$iso_code] = $reg_exp;
        }
        return $postcodes_regexp;
    }

    public function callViesApiByNip($nip, $nip_country_iso)
    {
        $req_expresions_by_iso_code = $this->getRegExpByCountryIsoCode();
        if (!$nip) {
            return array('error' => $this->l('Please provide vat number'));
        }

        if (empty($nip_country_iso)) {
            return array('error' => $this->l('Please provide EU VAT, seems that You provide VAT number without country letters'));
        }

        if (!Validate::isGenericName($nip)) {
            return array('error' => $this->l('Vat number is not valid'));
        }

        $vies = new Vies();

        if (false === $vies->getHeartBeat()->isAlive()) {
            return array('error' => $this->l('VIES Service is not available at the moment, please try again later'));
        }

        if (isset(self::$nip_numbers_cache[$nip])) {
            return self::$nip_numbers_cache[$nip];
        }

        $vatResult = $vies->validateVat(
            $nip_country_iso,   // Trader country code
            $nip,               // Trader VAT ID
            '',                 // Requester country code
            ''                  // Requester VAT ID
        );

        if ($vatResult == 'MS_MAX_CONCURRENT_REQ') {
            return array('error' =>
                sprintf(
                    $this->l('Back-end VIES service cannot validate the VAT number "%s%s" at this moment, the service responded with the critical error "%s", this is probably a temporary
                    problem. Please try again later'),
                    $nip_country_iso,
                    $nip,
                    $vatResult
                )
            );
        }
        if ($vatResult->isValid()) {
            if ($nip_country_iso) {
                $id_country = Country::getByIso($nip_country_iso);
            } else {
                $id_country = $this->context->country->id;
            }

            $address_string = $vatResult->getAddress();
            $address_array = explode(PHP_EOL, $address_string);

            $address1 = '';
            $postcode = '';
            $city = '';

            if (sizeof($address_array) == 2) {
                $address1 = $address_array[0];
                preg_match('/'.$req_expresions_by_iso_code[$nip_country_iso].'/m', $address_array[1], $matches);
                $postcode = $matches[0];

                $address_array_row_1 = preg_split('/\s{1,}/', $address_array[1]);
                $city = $address_array_row_1[1];
            } else {
                $address1 = $address_array[0];
                preg_match('/'.$req_expresions_by_iso_code[$nip_country_iso].'/m', $address_array[2], $matches);
                $postcode = $matches[0];
                $address_array_row_1 = preg_split('/\s{1,}/', $address_array[2]);
                $city = $address_array_row_1[1].' '.$address_array[1];
            }

            self::$nip_numbers_cache[$nip] = array(
                'company' => $vatResult->getName(),
                'postcode' => $postcode,
                'city' => $city ? $city : '',
                'firstname' => '',
                'lastname' => '',
                'address1' => isset($address1) ? $address1 : '',
                'country_iso' => $nip_country_iso,
                'id_country' => $id_country,
                'vat_number' => $nip_country_iso.$nip
            );

            return self::$nip_numbers_cache[$nip];
        } else {
            return array('error' => $this->l('No data found for provided vat number'));
        }
    }

    public function callRegonApiByNip($nip)
    {
        if (!$nip) {
            return array('error' => $this->l('Please provide vat number'));
        }

        if (!Validate::isGenericName($nip)) {
            return array('error' => $this->l('Vat number is not valid'));
        }

        $gus = new GusApi($this->regon_user_key);

        $return_data = array();

        if ($gus->serviceStatus() === \GusApi\RegonConstantsInterface::SERVICE_AVAILABLE) {
            try {
                $gus->login();
                if (isset($nip)) {
                    try {
                        $gusReports = $gus->getByNip($nip);



                        $reportTypeFiz = 'BIR11OsFizycznaDaneOgolne';
                        $data_fiz = $gus->getFullReport(
                            $gusReports[0],
                            $reportTypeFiz
                        );

                        $firstname = '';
                        if (isset($data_fiz[0]['fiz_nazwisko'])
                            && !empty($data_fiz[0]['fiz_nazwisko'])) {
                            $firstname = self::my_mb_ucfirst(strtolower($data_fiz[0]['fiz_nazwisko']));
                        }
                        $lastname = '';
                        if (isset($data_fiz[0]['fiz_imie1'])
                            && !empty($data_fiz[0]['fiz_imie1'])) {
                            $lastname = self::my_mb_ucfirst(strtolower($data_fiz[0]['fiz_imie1']));
                        }

                        $return_data = array(
                            'firstname' => $firstname,
                            'lastname' => $lastname,
                        );

                        ///////////////////////////////////////////////////////////////////////////////////////

                        $reportTypeDzialanoscCeidg = 'BIR11OsFizycznaDzialalnoscCeidg';
                        $data_dzialalnosc_ceidg = $gus->getFullReport(
                            $gusReports[0],
                            $reportTypeDzialanoscCeidg
                        );

                        if (!isset($data_dzialalnosc_ceidg[0]['ErrorCode'])) {
                            $commpany = '';
                            if (isset($data_dzialalnosc_ceidg[0]['fiz_nazwa'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_nazwa'])) {
                                $commpany = $data_dzialalnosc_ceidg[0]['fiz_nazwa'];
                            }

                            $city = '';
                            if (isset($data_dzialalnosc_ceidg[0]['fiz_adSiedzMiejscowosc_Nazwa'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_adSiedzMiejscowosc_Nazwa'])) {
                                $city = $data_dzialalnosc_ceidg[0]['fiz_adSiedzMiejscowosc_Nazwa'];
                            }

                            $address1 = '';
                            if (isset($data_dzialalnosc_ceidg[0]['fiz_adSiedzUlica_Nazwa'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_adSiedzUlica_Nazwa'])) {
                                $address1 = $data_dzialalnosc_ceidg[0]['fiz_adSiedzUlica_Nazwa'];
                            }

                            if (isset($data_dzialalnosc_ceidg[0]['fiz_adSiedzNumerNieruchomosci'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_adSiedzNumerNieruchomosci'])) {
                                $address1 .= ' '.$data_dzialalnosc_ceidg[0]['fiz_adSiedzNumerNieruchomosci'];
                            }

                            if (isset($data_dzialalnosc_ceidg[0]['fiz_adSiedzNumerLokalu'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_adSiedzNumerLokalu'])) {
                                $address1 .= ' '.$data_dzialalnosc_ceidg[0]['fiz_adSiedzNumerLokalu'];
                            }

                            $iso_code = '';
                            if (isset($data_dzialalnosc_ceidg[0]['fiz_adSiedzKraj_Symbol'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_adSiedzKraj_Symbol'])) {
                                $iso_code = $data_dzialalnosc_ceidg[0]['fiz_adSiedzKraj_Symbol'];
                            }

                            $postcode = '';
                            if (isset($data_dzialalnosc_ceidg[0]['fiz_adSiedzKodPocztowy'])
                                && !empty($data_dzialalnosc_ceidg[0]['fiz_adSiedzKodPocztowy'])) {
                                $postcode = $data_dzialalnosc_ceidg[0]['fiz_adSiedzKodPocztowy'];
                                $postcode = substr($postcode, 0, 2) . '-' . substr($postcode, 2);
                            }

                            if ($iso_code) {
                                $id_country = Country::getByIso($iso_code);
                            } else {
                                $id_country = $this->context->country->id;
                            }

                            $return_data = array(
                                'company' => $commpany,
                                'postcode' => $postcode,
                                'city' => $city,
                                'address1' => $address1,
                                'country_iso' => $iso_code,
                                'id_country' => $id_country,
                                'vat_number' => 'PL' . $nip
                            );
                        }

                        //////////////////////////////////////////////////////////////////////////////////////////
                        $reportTypeOrganization = 'BIR11OsPrawna';
                        $data_organization = $gus->getFullReport(
                            $gusReports[0],
                            $reportTypeOrganization
                        );

                        if (!isset($data_organization[0]['ErrorCode'])) {
                            $commpany = '';
                            if (isset($data_organization[0]['praw_nazwa'])
                                && !empty($data_organization[0]['praw_nazwa'])) {
                                $commpany = $data_organization[0]['praw_nazwa'];
                            }

                            $city = '';
                            if (isset($data_organization[0]['praw_adSiedzMiejscowosc_Nazwa'])
                                && !empty($data_organization[0]['praw_adSiedzMiejscowosc_Nazwa'])) {
                                $city = $data_organization[0]['praw_adSiedzMiejscowosc_Nazwa'];
                            }

                            $address1 = '';
                            if (isset($data_organization[0]['praw_adSiedzUlica_Nazwa'])
                                && !empty($data_organization[0]['praw_adSiedzUlica_Nazwa'])) {
                                $address1 = $data_organization[0]['praw_adSiedzUlica_Nazwa'];
                            }

                            if (isset($data_organization[0]['praw_adSiedzNumerNieruchomosci'])
                                && !empty($data_organization[0]['praw_adSiedzNumerNieruchomosci'])) {
                                $address1 .= ' '.$data_organization[0]['praw_adSiedzNumerNieruchomosci'];
                            }

                            if (isset($data_organization[0]['praw_adSiedzNumerLokalu'])
                                && !empty($data_organization[0]['praw_adSiedzNumerLokalu'])) {
                                $address1 .= ' '.$data_organization[0]['praw_adSiedzNumerLokalu'];
                            }

                            $iso_code = '';
                            if (isset($data_organization[0]['praw_adSiedzKraj_Symbol'])
                                && !empty($data_organization[0]['praw_adSiedzKraj_Symbol'])) {
                                $iso_code = $data_organization[0]['praw_adSiedzKraj_Symbol'];
                            }

                            $postcode = '';
                            if (isset($data_organization[0]['praw_adSiedzKodPocztowy'])
                                && !empty($data_organization[0]['praw_adSiedzKodPocztowy'])) {
                                $postcode = $data_organization[0]['praw_adSiedzKodPocztowy'];
                                $postcode = substr($postcode, 0, 2) . '-' . substr($postcode, 2);
                            }

                            if ($iso_code) {
                                $id_country = Country::getByIso($iso_code);
                            } else {
                                $id_country = $this->context->country->id;
                            }

                            $return_data = array(
                                'company' => $commpany,
                                'postcode' => $postcode,
                                'city' => $city,
                                'address1' => $address1,
                                'country_iso' => $iso_code,
                                'id_country' => $id_country,
                                'vat_number' => 'PL' . $nip
                            );
                        }

                        return $return_data;
                    } catch (NotFoundException $e) {
                        return array('error' => $this->l('No data found for provided vat number'));
                    }
                }
            } catch (InvalidUserKeyException $e) {
                return array('error' => $this->l('Bad user key!'));
            }
        } elseif ($gus->serviceStatus() === \GusApi\RegonConstantsInterface::SERVICE_UNAVAILABLE) {
            return array('error' => $this->l('Gus server is unavailable now, please try again later'));
        }
    }

    private static function my_mb_ucfirst($str)
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc.mb_substr($str, 1);
    }

    public function hookDisplaySearchByNip($params)
    {
        $this->smarty->assign(array(
            'img_dir' => $this->module_dir.'views/img/',
            'vat_iso_codes' => $this->country_iso_codes,
            'sl_country_iso' => $this->context->country->iso_code,
            'ps_version_15' => $this->ps_version_15,
            'ps_version_16' => $this->ps_version_16,
            'ps_version_17' => $this->ps_version_17

        ));

        if ($this->ps_version_17 || $this->ps_version_8) {
            return $this->display(__FILE__, 'displaySearchForm_17.tpl');
        } else {
            return $this->display(__FILE__, 'displaySearchForm.tpl');
        }
    }

    public function hookDisplaySearchByNipAdmin($params)
    {
        $this->smarty->assign(array(
            'img_dir' => $this->module_dir.'views/img/',
            'vat_iso_codes' => $this->country_iso_codes,
            'sl_country_iso' => $this->context->country->iso_code,
            'ps_version_15' => $this->ps_version_15,
            'ps_version_16' => $this->ps_version_16,
            'ps_version_17' => $this->ps_version_17,
            'pdgetdatabyvatnumberpro_secure_key' => $this->secure_key,
            'pdgetdatabyvatnumberpro_ajax_link' => $this->context->link->getModuleLink('pdgetdatabyvatnumberpro', 'ajax', array()),
            'pdgetdatabyvatnumberpro_response_ok' => Tools::htmlentitiesUTF8($this->l('Data was filled up in form')),

        ));

        if ($this->ps_version_17 || $this->ps_version_8) {
            return $this->display(__FILE__, 'displaySearchFormAdmin_17.tpl');
        } else {
            return $this->display(__FILE__, 'displaySearchForm.tpl');
        }
    }


    private function _displayExtraForm()
    {
        $theme_name = $this->context->shop->theme_name;
        $admin_address_line_of_code = htmlentities('<label class="control-label col-lg-3 required" for="email">');
        $admin_address_line_of_code_17 = htmlentities("{{ include('@PrestaShop/Admin/Sell/Address/Blocks/form.html.twig', {'addressForm': addressForm}) }}");

        $this->smarty->assign(array(
            'theme_name' => $theme_name,
            'admin_address_line_of_code' => $admin_address_line_of_code,
            'admin_address_line_of_code_17' => $admin_address_line_of_code_17
        ));

        if ($this->ps_version_17 || $this->ps_version_8) {
            return $this->display(__FILE__, 'views/templates/admin/instructions_17.tpl');
        } else {
            return $this->display(__FILE__, 'views/templates/admin/instructions.tpl');
        }
    }



}
