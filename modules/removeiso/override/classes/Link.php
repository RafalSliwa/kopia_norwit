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
class Link extends LinkCore
{
    protected function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        if (Language::isMultiLanguageActivated()) {
            if (!$id_lang) {
                if (is_null($context)) {
                    $context = Context::getContext();
                }

                $id_lang = $context->language->id;
            }

            if ($id_lang == Configuration::get('PS_LANG_DEFAULT', null, null, $id_shop)) {
                return '';
            }
        }

        return parent::getLangLink($id_lang, $context, $id_shop);
    }
}