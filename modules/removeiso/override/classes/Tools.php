<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */

class Tools extends ToolsCore
{
    /**
     * Change language in cookie while clicking on a flag
     *
     * @return string iso code
     */
    public static function setCookieLanguage($cookie = null)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            return parent::setCookieLanguage($cookie = null);
        }

        if (!$cookie) {
            $cookie = Context::getContext()->cookie;
        }

        if (!Configuration::get('PS_DETECT_LANG')) {
            unset($cookie->detect_language);
        }


        /* Automatically detect language if not already defined, detect_language is set in Cookie::update */
        if (!Tools::getValue('isolang') && !Tools::getValue('id_lang') && (!$cookie->id_lang || isset($cookie->detect_language)) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $array = explode(',', Tools::strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            $string = $array[0];

            if (Validate::isLanguageCode($string)) {
                $lang = Language::getLanguageByIETFCode($string);
                if (Validate::isLoadedObject($lang) && $lang->active && $lang->isAssociatedToShop()) {
                    Context::getContext()->language = $lang;
                    Context::getContext()->cookie->id_lang = (int)$lang->id;
                    $cookie->id_lang = (int)$lang->id;
                }
            }
            $iso = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
        } else {
            if (Tools::getValue('isolang', 'false') != 'false' && Tools::getValue('id_lang', 'false') != 'false') {
                $languag_defined = 1;
                $lang = new Language(Tools::getValue('id_lang', 'false'));
                if (Validate::isLoadedObject($lang) && $lang->active && $lang->isAssociatedToShop()) {
                    Context::getContext()->language = $lang;
                    Context::getContext()->cookie->id_lang = (int)$lang->id;
                    $cookie->id_lang = (int)$lang->id;
                }
                $iso = Language::getIsoById((int)$cookie->id_lang);
            }

            if (!isset($languag_defined)) {

                $lang = new Language(Configuration::get('PS_LANG_DEFAULT'));
                if (Validate::isLoadedObject($lang) && $lang->active && $lang->isAssociatedToShop()) {
                    Context::getContext()->language = $lang;
                    Context::getContext()->cookie->id_lang = (int)$lang->id;
                    $cookie->id_lang = (int)$lang->id;
                }
                $iso = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
            }
        }

        @include_once(_PS_THEME_DIR_ . 'lang/' . $iso . '.php');

        return $iso;
    }
}