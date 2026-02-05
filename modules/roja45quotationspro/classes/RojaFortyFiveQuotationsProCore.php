<?php
/**
 * RojaFortyFiveQuotationsProCore.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaFortyFiveQuotationsProCore
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaFortyFiveQuotationsProCore.
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

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RojaFortyFiveQuotationsProCore
{
    protected $html = '';

    public static function renderHeader($module)
    {
        return self::displayTemplate(
            $module,
            'hookRoja45Header',
            array(),
            'hookRoja45Header'
        );
    }

    public static function renderFooter($module)
    {
        return self::displayTemplate(
            $module,
            'hookRoja45Footer',
            array(),
            'hookRoja45Footer'
        );
    }

    public static function substrReplace($string, $replacement, $start, $length = null)
    {
        if (is_array($string)) {
            $num = count($string);
            // $replacement
            $replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
            // $start
            if (is_array($start)) {
                $start = array_slice($start, 0, $num);
                foreach ($start as $key => $value) {
                    $start[$key] = is_int($value) ? $value : 0;
                }
            } else {
                $start = array_pad(array($start), $num, $start);
            }
            // $length
            if (!isset($length)) {
                $length = array_fill(0, $num, 0);
            } elseif (is_array($length)) {
                $length = array_slice($length, 0, $num);
                foreach ($length as $key => $value) {
                    $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
                }
            } else {
                $length = array_pad(array($length), $num, $length);
            }
            // Recursive call
            return array_map(__FUNCTION__, $string, $replacement, $start, $length);
        }
        preg_match_all('/./us', (string) $string, $smatches);
        preg_match_all('/./us', (string) $replacement, $rmatches);
        if ($length === null) {
            $length = mb_strlen($string);
        }
        array_splice($smatches[0], $start, $length, $rmatches[0]);
        return join($smatches[0]);
    }

    public static function addJs($controller, $uri, $check_path = true)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $controller->addJs($uri . '.js', $check_path);
        } else {
            $controller->addJs($uri . '17.js', $check_path);
        }
    }

    public static function removeJs($controller, $js_uri, $check_path = true)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $controller->addJs($js_uri, $check_path);
        } else {
        }
    }

    public static function addCss($controller, $css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $controller->addCss($css_uri, $css_media_type, $offset, $check_path);
        } else {
        }
    }

    public function removeCss($controller, $css_uri, $css_media_type = 'all', $check_path = true)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $controller->addCss($css_uri, $css_media_type, $check_path);
        } else {
        }
    }

    public static function displayTemplate($module, $hook_name, $params, $template, $cache_id = null)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            return $module->display($module->name . '.php', $template . '.tpl', $cache_id);
        } else {
            $params['cache_id'] = $cache_id;
            return $module->renderWidget($hook_name, $params);
        }
    }

    public static function createTemplate($module, $template)
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
            $template = 'PS17_' . $template;
        }

        if (file_exists(_PS_THEME_DIR_ . 'modules/' . $module . '/views/templates/hook/' . $template . '.tpl')) {
            return Context::getContext()->smarty->createTemplate(
                _PS_THEME_DIR_ . 'modules/' . $module . '/views/templates/hook/' . $template . '.tpl'
            );
        } elseif (file_exists(_PS_ROOT_DIR_ . '/modules/' . $module . '/views/templates/hook/' . $template . '.tpl')) {
            return Context::getContext()->smarty->createTemplate(
                _PS_ROOT_DIR_ . '/modules/' . $module . '/views/templates/hook/' . $template . '.tpl'
            );
        } else {
            return false;
        }
    }

    public static function createAdminTemplate($module, $template)
    {
        if (file_exists(_PS_THEME_DIR_ . 'modules/' . $module . '/views/templates/admin/' . $template . '.tpl')) {
            return Context::getContext()->smarty->createTemplate(
                _PS_THEME_DIR_ . 'modules/' . $module . '/views/templates/admin/' . $template . '.tpl'
            );
        } elseif (file_exists(_PS_ROOT_DIR_ . '/modules/' . $module . '/views/templates/admin/' . $template . '.tpl')) {
            return Context::getContext()->smarty->createTemplate(
                _PS_ROOT_DIR_ . '/modules/' . $module . '/views/templates/admin/' . $template . '.tpl'
            );
        } else {
            return false;
        }
    }

    public static function setFrontControllerTemplate($controller, $template)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $controller->setTemplate($template);
        } else {
            $controller->setTemplate('module:' . $controller->module->name . '/views/templates/front/PS17_' . $template);
        }
    }

    public static function getIncludedTemplate($controller, $template)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            return Context::getContext()->smarty->createTemplate(
                $controller->getTemplatePath('booking-page-totals.tpl')
            );
        } else {
            return Context::getContext()->smarty->createTemplate(
                'module:' . $controller->module->name . '/views/templates/front/PS17_' . $template
            );
        }
    }

    public static function getLocalTranslation($module, $key, $lang)
    {
        if ($translation = Cache::getInstance()->get('local.' . $lang['iso_code'])) {
            return $translation[$key];
        } else {
            if (file_exists(_PS_MODULE_DIR_ . $module->name . '/translations/local.' . $lang['iso_code'])) {
                $data = file(_PS_MODULE_DIR_ . $module->name . '/translations/local.' . $lang['iso_code']);
            } elseif (file_exists(_PS_MODULE_DIR_ . $module->name . '/translations/local.en')) {
                $data = file(_PS_MODULE_DIR_ . $module->name . '/translations/local.en');
            }
            $returnArray = array();
            foreach ($data as $line) {
                $explode = explode("=", $line);
                $returnArray[$explode[0]] = trim(preg_replace('/\s+/', ' ', $explode[1]));
            }
            Cache::getInstance()->store('local.' . $lang['iso_code'], $returnArray);
            return $returnArray[$key];
        }
    }

    public static function isValidDateTimeString($str_dt, $str_dateformat)
    {
        $date = DateTime::createFromFormat($str_dateformat, $str_dt);
        return $date && $date->format($str_dateformat) == $str_dt;
    }

    public static function convertDateFormat($php_format)
    {
        $SYMBOLS_MATCHING = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => 'hh',
            'H' => 'hh',
            'i' => 'mm',
            's' => 'ss',
            'u' => '',
        );
        $jqueryui_format = "";
        $escaping = false;
        for ($i = 0; $i < Tools::strlen($php_format); $i++) {
            $char = $php_format[$i];
            if ($char === '\\') {
                $i++;
                if ($escaping) {
                    $jqueryui_format .= $php_format[$i];
                } else {
                    $jqueryui_format .= '\'' . $php_format[$i];
                }
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($SYMBOLS_MATCHING[$char])) {
                    $jqueryui_format .= $SYMBOLS_MATCHING[$char];
                } else {
                    $jqueryui_format .= $char;
                }
            }
        }
        return $jqueryui_format;
    }

    public static function saveCustomerRequirements($requirements)
    {
        foreach ($requirements as $key => $requirement) {
            Context::getContext()->cookie->__set($key, $requirement);
        }
    }

    public static function saveCustomerRequirement($requirement, $value)
    {
        return Context::getContext()->cookie->__set($requirement, $value);
    }

    public static function getCustomerRequirement($requirement)
    {
        return Context::getContext()->cookie->__get($requirement);
    }

    public static function getCustomerRequirements($family)
    {
        return Context::getContext()->cookie->getFamily($family);
    }

    public static function clearCustomerRequirement($requirement)
    {
        return Context::getContext()->cookie->__unset($requirement);
    }

    public static function clearCustomerRequirements($requirements)
    {
        foreach ($requirements as $requirement) {
            Context::getContext()->cookie->__unset($requirement);
        }
    }

    public static function clearAllCustomerRequirements($family)
    {
        Context::getContext()->cookie->unsetFamily($family);
    }

    public static function getAsBytes($val)
    {
        $val = trim($val);
        $last = Tools::strtolower($val[Tools::strlen($val) - 1]);
        $val = Tools::substr($val, 0, -1);

        switch ($last) {
            case 'g':
                $val *= 1024;
                break;
            case 'm':
                $val *= 1024;
                break;
            case 'k':
                $val *= 1024;
                break;
            default:
                $val = 0;
        }
        return $val;
    }

    public static function clearCache($module, $template)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $module->clearCache($template . '.tpl', $module->name . '::' . $template . '.tpl');
        } else {
            $module->clearCache('module:' . $module->name . '/views/templates/hook/PS17_' . $template . '.tpl');
        }
    }

    public static function isCached($module, $template)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            return $module->isCached($template . '.tpl', $module->name . '::' . $template . '.tpl');
        } else {
            return $module->isCached(
                'module:' . $module->name . '/views/templates/hook/PS17_' . $template . '.tpl',
                $module->getCacheId('module:' . $module->name . '/views/templates/hook/PS17_' . $template . '.tpl')
            );
        }
    }

    public static function getCacheId($module, $name = null)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            return $module->getCacheId($name . '.tpl');
        } else {
            return $module->getCacheId('module:' . $module->name . '/views/templates/hook/PS17_' . $name . '.tpl');
        }
    }

    /**
     * @param $price
     * @param Currency $currency
     * @return false|float|int|string
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public static function formatPrice($price, $currency = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.8', '>') == true) {
            if (!$currency) {
                $currency = Currency::getDefaultCurrency();
            }
            if (!$price || empty($price)) {
                $price = 0;
            }
            if (!$locale = Context::getContext()->getCurrentLocale()) {
                $locale = Tools::getContextLocale(Context::getContext());
            }
            return $locale->formatPrice($price, $currency->iso_code);
        } else {
            return Tools::displayPrice($price, $currency);
        }
    }

    /**
     * @param $price
     * @param Currency $currency
     * @return false|float|int|string
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public static function displayNumber($number, $currency = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.8', '>') == true) {
            if (!$currency) {
                $currency = Currency::getDefaultCurrency();
            }
            return Context::getContext()->getCurrentLocale()->formatNumber($number);
        } else {
            return Tools::displayNumber($number, $currency);
        }
    }

    /**
     * @param Currency $currency
     * @return false|int|
     */
    public static function getCurrencyPrecision($currency)
    {
        if (version_compare(_PS_VERSION_, '1.7.6', '>') == true) {
            return $currency->precision;
        } else {
            return $currency->decimals;
        }
    }

    /**
     * @param Currency $currency
     * @return false|int|
     */
    public static function getCurrencySymbol($currency)
    {
        if (version_compare(_PS_VERSION_, '1.7.6', '>') == true) {
            return $currency->symbol;
        } else {
            return $currency->sign;
        }
    }
    /**
     * @param Currency $currency
     * @return false|int|
     */
    public static function getTaxDefaultPrecision()
    {
        if (version_compare(_PS_VERSION_, '1.7.7', '>') == true) {
            return Tax::TAX_DEFAULT_PRECISION;
        } else {
            return 3;
        }
    }

    public static function disableModule()
    {
        Configuration::updateGlobalValue('RJ45DISMOD', 1);
    }

    public static function enableModule()
    {
        Configuration::deleteByName('RJ45DISMOD');
    }

    public static function resetModule()
    {
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_ACCOUNTKEY');
    }

    public static function encrypt($message, $key)
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        $ciphertext = openssl_encrypt(
            $message,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return $nonce . $ciphertext;
    }

    public static function decrypt($message, $key)
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }

    public static function errorLog($object, $message_type = null, $destination = null, $extra_headers = null)
    {
        return error_log(print_r($object, true), $message_type, $destination, $extra_headers);
    }

    public static function pregGrepKey($pattern, $input)
    {
        return preg_grep($pattern, array_keys($input));
    }

    public static function getBytesValue($val)
    {
        $val = trim($val);

        if (is_numeric($val)) {
            return $val;
        }
        $last = Tools::strtolower($val[Tools::strlen($val) - 1]);
        $val = Tools::substr($val, 0, -1);

        /* Get the bytes value for GB, MB, or KB*/
        switch ($last) {
            case 'g':
                $val *= 1024; /* Fall through to calculate next size */
            case 'm':
                $val *= 1024; /* Fall through to calculate next size */
            case 'k':
                $val *= 1024; /* Fall through to calculate next size */
        }

        return $val;
    }

    public static function isValidFile($filename, $mimeTypeList)
    {
        // Detect mime content type
        $mimeType = false;

        // Try 4 different methods to determine the mime type
        if (function_exists('finfo_open')) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $finfo = finfo_open($const);
            $mimeType = finfo_file($finfo, $filename);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($filename);
        }

        // For each allowed MIME type, we are looking for it inside the current MIME type
        foreach ($mimeTypeList as $type) {
            if (strstr($mimeType, $type)) {
                return true;
            }
        }

        return false;
    }

    public static function getAllCategoriesName(
        $root_category = null,
        $id_lang = false,
        $active = true,
        $groups = null,
        $use_shop_restriction = true,
        $sql_filter = '',
        $sql_sort = '',
        $sql_limit = ''
    ) {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array) $groups;
        }

        $result = Db::getInstance()->executeS(
            'SELECT c.id_category, cl.name
            FROM `' . _DB_PREFIX_ . 'category` c
            ' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' .
            Shop::addSqlRestrictionOnLang('cl') . '
            ' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON
            c.`id_category` = cg.`id_category`' : '') . '
            ' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' .
                (int) $root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
            WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int) $id_lang : '') . '
            ' . ($active ? ' AND c.`active` = 1' : '') . '
            ' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(',', $groups) . ')' : '') . '
            ' . (!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
            ' . ($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC') . '
            ' . ($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '') . '
            ' . ($sql_limit != '' ? $sql_limit : '')
        );
        return $result;
    }

    public static function deleteCustomerCartProductSpecificPrice(
        $id_customer,
        $id_cart,
        $id_product,
        $id_product_attribute = null
    ) {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'specific_price`
        WHERE id_cart = ' . (int) $id_cart . '
        AND id_customer = ' . (int) $id_customer . '
        AND id_product = ' . (int) $id_product;
        if ($id_product_attribute) {
            $sql .= ' AND id_product_attribute=' . (int) $id_product_attribute;
        }
        return Db::getInstance()->execute($sql);
    }

    /**
     * @param Currency $currency
     * @return false|int|mixed|string
     */
    public static function getComputingPrecision($currency)
    {
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=') == true) {
            return Context::getContext()->getComputingPrecision();
        } elseif (isset($currency)) {
            if (version_compare(_PS_VERSION_, '1.7.6', '>') == true) {
                return $currency->precision;
            } else {
                return $currency->decimals;
            }
        } else {
            return _PS_PRICE_DISPLAY_PRECISION_;
        }
    }

    public static function getEditorTemplatePath($template)
    {
        if (file_exists(_PS_THEME_DIR_ . 'modules/roja45quotationspro/views/templates/admin/custom/' . $template . '.tpl')) {
            return _PS_THEME_DIR_ . 'modules/roja45quotationspro/views/templates/admin/custom/' . $template . '.tpl';
        } elseif (file_exists(_PS_ROOT_DIR_ . '/modules/roja45quotationspro/views/templates/admin/custom/' . $template . '.tpl')) {
            return _PS_ROOT_DIR_ . '/modules/roja45quotationspro/views/templates/admin/custom/' . $template . '.tpl';
        } else {
            return false;
        }
    }

    public static function getCurrencyValue($value)
    {
        $decimals = substr($value, strrpos($value, '.'), strlen($value));
        $number = substr($value, 0, strrpos($value, '.'));
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $number);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $number);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot);

        $number = str_replace(',', '.', $removedThousandSeparator);
        return (float) $number . $decimals;
    }

    public static function formatCurrencyValue($value, $with_symbol)
    {
        $locale_code = '';
        if (version_compare(_PS_VERSION_, '1.7.8', '>') == true) {
            if (!$locale = Context::getContext()->getCurrentLocale()) {
                $locale = Tools::getContextLocale(Context::getContext());
            }
            $locale_code = $locale->getCode();
        } else {
            $locale_code = Context::getContext()->language->language_code;
        }
        $fmt = new \NumberFormatter($locale_code, NumberFormatter::CURRENCY);
        if (!$with_symbol) {
            $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        }
        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 6);

        return $fmt->format($value);
    }

    public static function getImageTypeFormattedName($name)
    {
        if (version_compare(_PS_VERSION_, '8', '>=') == true) {
            return ImageType::getFormattedName($name);
        } else {
            return ImageType::getFormatedName($name);
        }
    }

    public static function passwdGen($length)
    {
        if (version_compare(_PS_VERSION_, '8', '>=') == true) {
            return Tools::passwdGen($length, Tools::PASSWORDGEN_FLAG_RANDOM);
        } else {
            return Tools::passwdGen($length);
        }
    }

    public static function referenceGen($length)
    {
        return Tools::passwdGen($length);
    }

    public static function encryptPassword($password)
    {
        if (version_compare(_PS_VERSION_, '8', '>=') == true) {
            $crypto = ServiceLocator::get(Hashing::class);
            return $crypto->hash($password);
        } else {
            return Tools::encrypt($password);;
        }
    }
    public static function getLocale($language)
    {
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            return $language->language_code;
        } else {
            return $language->locale;
        }
    }

    public static function sortAttributeById($x, $y) {
        return $x['id_attribute'] > $y['id_attribute'];
    }

    public static function sortAttributeByGroupName($x, $y) {
        return $x['public_group_name'] > $y['public_group_name'];
    }

    public static function sortAttributeByAttrName($x, $y) {
        return $x['attribute_name'] > $y['attribute_name'];
    }
}
