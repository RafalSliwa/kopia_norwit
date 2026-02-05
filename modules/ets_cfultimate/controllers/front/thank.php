<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class Ets_CfUltimateThankModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */

    public function initContent()
    {
        parent::initContent();
        $id_contact = Tools::getValue('id_contact');

        if (!$id_contact && ($alias = Tools::getValue('url_alias'))) {
            $id_contact = ETS_CFU_Contact::getIdContactByAlias($alias, null, true);
        }

        if (Configuration::get('PS_REWRITING_SETTINGS') && $id_contact && (Tools::strpos($_SERVER['REQUEST_URI'], 'url_alias') !== false || Tools::strpos($_SERVER['REQUEST_URI'], 'id_contact') !== false)) {
            $url = $this->module->getLinkContactForm($id_contact, $this->context->language->id);
            Tools::redirect($url);
        }

        $this->module->setMetas($id_contact, true);
        $ip = Tools::getRemoteAddr();
        $browser = $this->module->getDevice();
        ETS_CFU_Contact::addLog($id_contact, $ip, $browser);
        if (Tools::getValue('action') == 'etsCfuAddLogger') {
            die(json_encode(array(
                'sus' => true
            )));
        }
        $contact = new ETS_CFU_Contact($id_contact, $this->context->language->id);
        if ($contact->id
            && $contact->active
            && ETS_CFU_Contact::getContactById($id_contact, $this->context->shop->id)
        ) {
            $contact_form = $this->module->ets_cfu_contact_form($contact->id);
            $base_url = Ets_CfUltimate::getLinkContactForm($id_contact, (int)Context::getContext()->language->id, 'thank');
            $base_url .= $contact->thank_you_alias;
            $this->context->smarty->assign(array(
                'contact' => $contact,
                'link_contact' => $base_url,
                'thank_you_page_title' => $contact_form->thank_you_page_title,
                'thank_you_message' => $contact_form->thank_you_message
            ));
            if (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('thank-page16.tpl');
            } else {
                $this->setTemplate('module:ets_cfultimate/views/templates/front/thank-page.tpl');
            }
        } elseif (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->setTemplate('not-found16.tpl');
        } else {
            $this->setTemplate('module:ets_cfultimate/views/templates/front/not-found.tpl');
        }
    }


    public function getBreadcrumbLinks()
    {
        if (version_compare(_PS_VERSION_, '1.7', '<'))
            return;
        $breadcrumb = parent::getBreadcrumbLinks();
        $id_contact = Tools::getValue('id_contact');
        if (!$id_contact && ($alias = Tools::getValue('url_alias'))) {
            $id_contact = ETS_CFU_Contact::getIdContactByAlias($alias, null, true);
        }
        $contact = new ETS_CFU_Contact($id_contact, $this->context->language->id);
        $base_url = Ets_CfUltimate::getLinkContactForm($id_contact, (int)Context::getContext()->language->id, 'thank');
        $breadcrumb['links'][] = array(
            'title' => $contact->thank_you_page_title,
            'url' => $base_url,
        );
        return $breadcrumb;
    }
}