<?php
class Tools extends ToolsCore
{
    /*
    * module: pshoweditor
    * date: 2024-11-10 12:12:21
    * version: 1.5.2
    */
    public static function purifyHTML($html, $uri_unescape = null, $allow_style = false)
    {
        
        if (
            ($module = Module::getInstanceByName('pshoweditor'))
            && $module->isEnabledForShopContext()
        ) {
            return $module->override__Tools_purifyHTML($html, $uri_unescape, $allow_style);
        }
        return parent::purifyHTML($html, $uri_unescape, $allow_style);
    }
    
    
    
    
    
    
    /*
    * module: removeiso
    * date: 2025-03-07 15:30:44
    * version: 1.4.4
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