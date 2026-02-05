<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace CeneoXml\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Html
{
    public $_html;
    public $module;

    public function __construct()
    {
        $this->module = \Module::getInstanceByName('ceneo_xml');
    }

    public function displayInfoHeader()
    {
        $secureKey = md5(_COOKIE_KEY_ . \Configuration::get('PS_SHOP_NAME'));
        $this->_html = '';
        $friendly_url_active = \Configuration::get('PS_REWRITING_SETTINGS');

        if ($friendly_url_active !== '1') {
            $this->_html .= $this->module->getFriendlyInfo();
        }

        if (!\Shop::isFeatureActive()) {
            $file_url = \Context::getContext()->link->getModuleLink(
                'ceneo_xml',
                'generate',
                [
                    'secure_key' => $secureKey,
                    'id_shop' => \Context::getContext()->shop->id,
                    'show_output' => 1,
                ]
            );

            $file_url_no_output = \Context::getContext()->link->getModuleLink(
                'ceneo_xml',
                'generate',
                [
                    'secure_key' => $secureKey,
                    'id_shop' => \Context::getContext()->shop->id,
                    'show_output' => 0,
                ]
            );

            $this->_html .= '<div class="alert alert-info">';
            $this->_html .= '<p>' . $this->l('Sending offers to Ceneo') . '</p>';
            $this->_html .= '<p>';
            $this->_html .= $this->l('To place your offers in Ceneo, you need an XML file containing all the information about them. ') .
                $this->l('Our plugin will create this file for you. Just make sure you properly combine your categories and attributes with those on the Ceneo side. ') .
                $this->l('Thanks to this, your offers will appear in the correct categories in Ceneo.');
            $this->_html .= '</p>';
            $this->_html .= '<p>' . $this->l('Use this link in Ceneo Panel')
                . ': <a href="' . $file_url . '" style="color:#ff6400">' . $file_url . '</a></p>';
            $this->_html .= '<p>' .
                $this->l('Remember! The offer file will not be generated automatically until you set the cron in the server panel.') . '</p>';
            $this->_html .= '</div>';

            $this->_html .= '<div class="alert alert-success">';
            $this->_html .= '<p>' . $this->l('Date of the last file generation') . ': ' .
                \Configuration::get('CENEO_XML_LAST')
                . ' (' . \Configuration::get('CENEO_XML_COUNT') . ' ofert)<a style="margin-left: 20px;" 
                target="_blank" href="' . $file_url . '" class="btn btn-success">'
                . $this->l('View feed') . '</a></p><p>' .
                $this->l('Remember: if you want to generate a feed to a file on the server, paste this link') .
                ' <a style="" target="_blank" href="' . $file_url_no_output . '"> ' . $file_url_no_output . '</a> ' .
                $this->l('into the cron job') . '</a></p></div>';
        } else {
            $kernel = new \AppKernel(_PS_ENV_, _PS_MODE_DEV_);
            $kernel->boot();
            $shopContext = $kernel->getContainer()->get('prestashop.adapter.shop.context');
            $isSingleShopContext = $shopContext->isSingleShopContext();

            if ($isSingleShopContext) {
                $urlController = \Context::getContext()->link->getModuleLink('ceneo_xml', 'generate', ['secure_key' => $secureKey, 'id_shop' => \Context::getContext()->shop->id, 'show_output' => 1]);
                $urlControllerNoOutput = \Context::getContext()->link->getModuleLink('ceneo_xml', 'generate', ['secure_key' => $secureKey, 'id_shop' => \Context::getContext()->shop->id, 'show_output' => 0]);

                $this->_html .= '<div class="alert alert-info">';
                $this->_html .= '<p>' . $this->l('Sending offers to Ceneo') . '</p>';
                $this->_html .= '<p>';
                $this->_html .=
                    $this->l('To place your offers in Ceneo, you need an XML file containing all the information about them. ') .
                    $this->l('Our plugin will create this file for you. Just make sure you properly combine your categories and attributes with those on the Ceneo side. ') .
                    $this->l('Thanks to this, your offers will appear in the correct categories in Ceneo.');
                $this->_html .= '</p>';
                $this->_html .= '<p>' . $this->l('Use this link in Ceneo Panel')
                    . ': <a target="_blank" href="' . $urlController . '" style="color:#ff6400">' . $urlController . '</a></p>';
                $this->_html .= '<p>' .
                    $this->l('Remember! The offer file will not be generated automatically until you set the cron in the server panel.') . '</p>';
                $this->_html .= '</div>';
                $this->_html .= '<div class="alert alert-success">';
                $this->_html .= '<p>' . $this->l('Date of the last file generation') . ': 
                ' . \Configuration::get('CENEO_XML_LAST')
                    . ' (' . \Configuration::get('CENEO_XML_COUNT') . ' ' . $this->l('offers') . ')<a style="margin-left: 20px;" target="_blank" href="'
                    . $urlController . '" class="btn btn-success">'
                    . $this->l('View feed') . '</a></p><p>' .
                    $this->l('Remember: if you want to generate a feed to a file on the server, paste this link') . ' <a style="" target="_blank" href="' . $urlControllerNoOutput . '"> ' . $urlControllerNoOutput . '</a> ' .
                    $this->l('into the cron job') . '</a></p></div>';
            } else {
                $this->_html .= '<div class="alert alert-info">';
                $this->_html .= '<p>' . $this->l('Sending offers to Ceneo') . '</p>';
                $this->_html .= '<p>';
                $this->_html .=
                    $this->l('To place your offers in Ceneo, you need an XML file containing all the information about them. ') .
                    $this->l('Our plugin will create this file for you. Just make sure you properly combine your categories and attributes with those on the Ceneo side. ') .
                    $this->l('Thanks to this, your offers will appear in the correct categories in Ceneo.');
                $this->_html .= '</p>';
                foreach (\Shop::getShops() as $shop) {
                    $urlShopController = \Context::getContext()->link->getModuleLink('ceneo_xml', 'generate', ['secure_key' => $secureKey, 'id_shop' => $shop['id_shop'], 'show_output' => 1]);
                    $this->_html .= '<span style="font-weight: bold">' . $shop['name'] . '</span><p>' .
                        $this->l('Use this link in Ceneo Panel')
                        . ': <a target="_blank" href="' . $urlShopController . '" style="color:#ff6400">'
                        . $urlShopController . '</a></p>';
                }
                $this->_html .= '<p>' . $this->l('Remember! The offer file will not be generated automatically until you set the cron in the server panel.') . '</p>';
                $this->_html .= '</div>';
                foreach (\Shop::getShops() as $shop) {
                    $urlShopControllerNoOutput = \Context::getContext()->link->getModuleLink('ceneo_xml', 'generate', ['secure_key' => $secureKey, 'id_shop' => $shop['id_shop'], 'show_output' => 0]);
                    $this->_html .= '<div class="alert alert-success">';
                    $this->_html .= '<p style="font-weight: bold">' . $shop['name'] . '</p>';
                    $this->_html .= '<p>' . $this->l('Date of the last file generation') . ': ' .
                        \Configuration::get('CENEO_XML_LAST', null, null, $shop['id_shop'])
                        . ' (' . \Configuration::get('CENEO_XML_COUNT', null, null, $shop['id_shop']) . ' ' . $this->l('offers') . ')<a style="margin-left: 20px;" target="_blank" href="'
                        . $urlShopControllerNoOutput . '" class="btn btn-success">'
                        . $this->l('View feed') . '</a></p><p>' .
                        $this->l('Remember: if you want to generate a feed to a file on the server, paste this link')
                        . ' <a style="" target="_blank" href="' . $urlShopControllerNoOutput . '"> ' . $urlShopControllerNoOutput . '</a> ' . $this->l('into the cron job') .
                        '</a></p></div>';
                }
            }
        }

        return $this->_html;
    }

    public function displayShopHeader()
    {
        $this->_html = '<div class="alert alert-info">';
        $this->_html .= '<p>' . $this->l('The module has been launched in Multistore mode. To view the plug-in settings, change the "All Stores" view in the upper right corner to the view of the selected store for which you want to view the settings.') . '</p>';
        $this->_html .= '</div>';

        return $this->_html;
    }

    public function l($string, $specific = false, $locale = null)
    {
        return $this->module->l($string, 'html');
    }
}
