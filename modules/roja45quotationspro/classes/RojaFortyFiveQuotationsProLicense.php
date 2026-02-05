<?php
/**
 * RojaFortyFiveQuotationsProLicense.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaFortyFiveQuotationsProLicense
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaFortyFiveQuotationsProLicense.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Class
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

spl_autoload_register(function ($class_name) {
    if ((0 === strpos($class_name, 'Roja45\\LicenseManager\\'))) {
        $class_name = ltrim($class_name, '\\');
        $fileName  = '';
        if ($lastNsPos = strrpos($class_name, '\\')) {
            $namespace = Tools::substr($class_name, 0, $lastNsPos);
            $class_name = Tools::substr($class_name, $lastNsPos + 1);
            $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

        require_once _PS_MODULE_DIR_ . 'roja45quotationspro/libraries/'.$fileName;
    }
    return;
});

class RojaFortyFiveQuotationsProLicense
{
    protected $html = '';
    const METHOD = 'aes-256-ctr';
    const LICENSE_CONTROLLER = '68747470733a2f2f7777772e726f6a6134352e636f6d2f';
    //const LICENSE_CONTROLLER = '687474703a2f2f726f6a6134352e6c6f63616c2f';
    const ROJA45_API_KEY = '35326166626663332d323931332d343764622d623030622d323933323130376430656564';

    public static function validateUpdate($module)
    {
        if (Configuration::get('ROJA45_UPDATE_' . $module->name) < (date("U") - 86400)) {
            $module_name = $module->name;
            $module_version = $module->version;
            $module_source = $module->source;

            $account_email = Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS');
            $account_order = Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTORDER');
            $account_domain = Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN');
            $helper = Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::getApiHelper(
                Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::V1,
                self::getLicenseController(), // local
                self::getRoja45APIKey()
            );

            Configuration::updateValue('ROJA45_UPDATE_' . $module->name, date('U'));
            try {
                return $helper->heartbeat(
                    $module_name,
                    $module_version,
                    $module_source,
                    ($account_order) ? $account_order : 'Unknown',
                    $_SERVER['HTTP_HOST'],
                    $account_domain,
                    ($account_email) ? $account_email : Configuration::get('PS_SHOP_EMAIL'),
                    (int)Configuration::getGlobalValue('RJ45DISMOD'),
                    isset($_SERVER['HTTPS']) ? 'https' : 'http'
                );
            } catch (Exception $e) {
                // log
                return true;
            }
        }
        return true;
    }

    public static function registerModule(
        $module
    ) {
        try {
            if (function_exists('curl_init')) {
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS',
                    trim(Tools::getValue('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS'))
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ACCOUNTORDER',
                    trim(Tools::getValue('ROJA45_QUOTATIONSPRO_ACCOUNTORDER'))
                );

                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN')
                );
                Configuration::updateGlobalValue(
                    'ROJA45_QUOTATIONSPRO_TESTDOMAIN',
                    Tools::getValue('ROJA45_QUOTATIONSPRO_TESTDOMAIN')
                );

                $helper = Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::getApiHelper(
                    Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::V1,
                    self::getLicenseController(), // local
                    self::getRoja45APIKey()
                );
                if ($auth_key = $helper->registerModule(
                    trim($module->source),
                    trim($module->name),
                    Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS'),
                    Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_ACCOUNTORDER'),
                    Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_ACCOUNTDOMAIN'),
                    Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_TESTDOMAIN'),
                    Tools::getValue('ROJA45_QUOTATIONSPRO_ACCOUNTKEY')
                )) {
                    Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_ACCOUNTKEY', $auth_key);
                    Tools::redirect(Context::getContext()->link->getAdminLink(
                            'AdminModules',
                            true
                        ).'&configure='.$module->name.'&tab_module='.$module->tab.'&module_name='.$module->name);
                }
                throw new Exception($helper->getLastError());
            } else {
                throw new Exception('cURL must installed and enabled for PHP.');
            }
        } catch (Exception $e) {
            $module->html .= $module->displayError($e->getMessage());
        }
    }

    public static function validateModule(
        $auth_key,
        $module_name,
        $module_source,
        $as_html = true
    ) {
        try {
            $helper = Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::getApiHelper(
                Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::V1,
                self::getLicenseController(), // local
                self::getRoja45APIKey()
            );
            if ($auth_key = $helper->validateModule(
                $auth_key,
                $module_source,
                $module_name,
                Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS'),
                $_SERVER['HTTP_HOST'],
                Configuration::getGlobalValue('RJ45DISMOD'),
                $as_html
            )) {
                return $auth_key;
            } else {
                Tools::error_log('Roja45:QuotationsPro:Registration Error: '.$helper->getLastError());
                if (($counter = (int) Configuration::get('ROJA45_AUTHCOUNTER') + 1) >= 3) {
                    Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ACCOUNTKEY');
                    Configuration::deleteByName('ROJA45_AUTHCOUNTER');
                    throw new Exception($helper->getLastError());
                }
                Configuration::updateGlobalValue('ROJA45_AUTHCOUNTER', $counter);
                return true;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function renderModuleForm($module)
    {
        try {
            $context = Context::getContext();
            $tpl = $context->smarty->createTemplate($module->getTemplatePath('hookRoja45Header.tpl'));
            $tpl->assign(
                array(
                    'roja45_auth_key' => Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY')
                )
            );
            $module->html .= $tpl->fetch();

            $helper = Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::getApiHelper(
                Roja45\LicenseManager\Prestashop\Api\RojaFortyFiveApiFactory::V1,
                self::getLicenseController(), // local
                self::getRoja45APIKey()
            );

            $module->params = array_merge(
                $module->params,
                array(
                    'roja45_license_controller' => self::getLicenseController(),
                    'roja45_auth_key' => Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY'),
                    'roja45_auth_email' => Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS'),
                    'roja45_api_key' => self::getRoja45APIKey(),
                    'roja45_registered' => (Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY')) ?
                        Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY') : 0,
                    'ps_version' => _PS_VERSION_,
                    'module_name' => Tools::strtolower($module->name),
                    'module_version' => Tools::strtolower($module->version),
                    'module_source' => Tools::strtolower($module->source),
                    'roja45_domain' => $_SERVER['HTTP_HOST']
                )
            );
            if ($helper->validateModule(
                Configuration::get('ROJA45_QUOTATIONSPRO_ACCOUNTKEY'),
                $module->source,
                $module->name,
                Configuration::getGlobalValue('ROJA45_QUOTATIONSPRO_ACCOUNTEMAILADDRESS'),
                $_SERVER['HTTP_HOST'],
                Configuration::getGlobalValue('RJ45DISMOD'),
                false
            )) {
                $tpl = $context->smarty->createTemplate(
                    $module->getTemplatePath('configuration.tpl')
                );
                $tpl->assign($module->params);
            } else {
                $tpl = $context->smarty->createTemplate(
                    $module->getTemplatePath('registration.tpl')
                );
                $tpl->assign($module->params);
            }

            $module->html .= $tpl->fetch();
            $tpl = $context->smarty->createTemplate(
                _PS_MODULE_DIR_.$module->name.'/views/templates/admin/prestui/ps-tags.tpl'
            );
            $module->html .= $tpl->fetch();

            $tpl = $context->smarty->createTemplate($module->getTemplatePath('hookRoja45Footer.tpl'));
            $module->html .= $tpl->fetch();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function getLicenseController()
    {
        return hex2bin(self::LICENSE_CONTROLLER);
    }

    public static function getRoja45APIKey()
    {
        return hex2bin(self::ROJA45_API_KEY);
    }
}
