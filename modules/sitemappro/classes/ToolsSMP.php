<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ToolsSMP
{
    public static function pSQLArrayAndRemoveEmpty(&$array)
    {
        foreach ($array as $key => &$item) {
            if (!empty($item)) {
                $item = pSQL($item);
            } else {
                unset($array[$key]);
            }
        }
    }

    public static function gerIntArrayFromRequest($var_name)
    {
        $items = Tools::getValue($var_name);
        $ids_item = [];
        if (!is_array($items) || !count($items)) {
            return [];
        }

        foreach ($items as $item) {
            $ids_item[] = (int) $item;
        }
        return $ids_item;
    }

    public static function getShopDomain($relative = false)
    {
        if ($relative) {
            return '//' . ShopUrl::getMainShopDomain(Shop::getContextShopID());
        }
        $base_link = '';
        if (ConfSMP::getConf('protocol') == SitemapConfig::PROTOCOL_HTTP) {
            $base_link = 'http://' . ShopUrl::getMainShopDomain(Shop::getContextShopID());
        } elseif (ConfSMP::getConf('protocol') == SitemapConfig::PROTOCOL_HTTPS) {
            $base_link = 'https://' . ShopUrl::getMainShopDomainSSL(Shop::getContextShopID());
        }
        return $base_link;
    }

    public static function getShopDomainWithBase()
    {
        return self::getShopDomain() . __PS_BASE_URI__;
    }

    public static function getSitemaps($id_shop)
    {
        $l = TransModSMP::getInstance();
        $sitemaps = [];

        $sitemaps['simple'] = [];
        $sitemaps['simple']['link'] = [
            'name' => SitemapConfig::getSitemapFilename(),
            'description' => $l->l('Simple sitemap with all active languages', __FILE__),
            'full_link' => SitemapConfig::getFullLinkSitemap(),
            'link' => SitemapConfig::getLinkSitemap(),
            'cron_url' => SitemapConfig::getCronUrl(
                null,
                false,
                false,
                $id_shop
            ),
            'cron' => SitemapConfig::getFullCronUrl(
                null,
                false,
                false,
                $id_shop
            ),
            'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap()),
            'pages' => self::getPages(SitemapConfig::getFullLinkSitemap()),
        ];
        $sitemaps['simple']['lang'] = [];
        foreach (ToolsModuleSMP::getLanguages(true) as $lang) {
            $sitemaps['simple']['lang'][$lang['iso_code']] = [
                'name' => SitemapConfig::getSitemapFilename($lang['id_lang']),
                'description' => sprintf($l->l('Simple sitemap with %s language', __FILE__), $lang['name']),
                'full_link' => SitemapConfig::getFullLinkSitemap($lang['id_lang']),
                'link' => SitemapConfig::getLinkSitemap($lang['id_lang']),
                'cron_url' => SitemapConfig::getCronUrl(
                    $lang['id_lang'],
                    false,
                    false,
                    $id_shop
                ),
                'cron' => SitemapConfig::getFullCronUrl(
                    $lang['id_lang'],
                    false,
                    false,
                    $id_shop
                ),
                'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap($lang['id_lang'])),
                'pages' => self::getPages(SitemapConfig::getFullLinkSitemap($lang['id_lang'])),
            ];
        }

        $sitemaps['product_images'] = [];
        $sitemaps['product_images']['link'] = [
            'name' => SitemapConfig::getSitemapFilename(null, false, true),
            'description' => $l->l('Sitemap with all active languages and product images', __FILE__),
            'full_link' => SitemapConfig::getFullLinkSitemap(null, false, true),
            'link' => SitemapConfig::getLinkSitemap(null, false, true),
            'cron_url' => SitemapConfig::getCronUrl(
                null,
                true,
                false,
                $id_shop
            ),
            'cron' => SitemapConfig::getFullCronUrl(
                null,
                true,
                false,
                $id_shop
            ),
            'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap(null, false, true)),
            'pages' => self::getPages(SitemapConfig::getFullLinkSitemap(null, false, true)),
        ];

        $sitemaps['product_images']['lang'] = [];
        foreach (ToolsModuleSMP::getLanguages(true) as $lang) {
            $sitemaps['product_images']['lang'][$lang['iso_code']] = [
                'name' => SitemapConfig::getSitemapFilename($lang['id_lang'], false, true),
                'description' => sprintf($l->l('Sitemap with %s language and product images', __FILE__), $lang['name']),
                'full_link' => SitemapConfig::getFullLinkSitemap($lang['id_lang'], false, true),
                'link' => SitemapConfig::getLinkSitemap($lang['id_lang'], false, true),
                'cron_url' => SitemapConfig::getCronUrl(
                    $lang['id_lang'],
                    true,
                    false,
                    $id_shop
                ),
                'cron' => SitemapConfig::getFullCronUrl(
                    $lang['id_lang'],
                    true,
                    false,
                    $id_shop
                ),
                'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap($lang['id_lang'], false, true)),
                'pages' => self::getPages(SitemapConfig::getFullLinkSitemap($lang['id_lang'], false, true)),
            ];
        }

        $sitemaps['product_only'] = [];
        $sitemaps['product_only']['link'] = [
            'name' => SitemapConfig::getSitemapFilename(null, false, true, false, 1),
            'description' => $l->l('Sitemap with all active languages and only product images', __FILE__),
            'full_link' => _PS_ROOT_DIR_ . '/' . SitemapConfig::getSitemapFilename(null, false, true, false, 1),
            'link' => SitemapConfig::getLinkSitemap(null, false, true, false, 1),
            'cron_url' => SitemapConfig::getCronUrl(null, true, false, $id_shop) . '&only=1',
            'cron' => SitemapConfig::getFullCronUrl(null, true, false, $id_shop) . '&only=1',
            'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap(null, false, true)),
            'pages' => self::getPages(SitemapConfig::getFullLinkSitemap(null, false, true, false, 1)),
        ];
        $sitemaps['product_only']['lang'] = [];
        foreach (ToolsModuleSMP::getLanguages(true) as $lang) {
            $sitemaps['product_only']['lang'][$lang['iso_code']] = [
                'name' => SitemapConfig::getSitemapFilename($lang['id_lang'], false, true, false, 1),
                'description' => sprintf($l->l('Sitemap with %s language and only product images', __FILE__), $lang['name']),
                'full_link' => _PS_ROOT_DIR_ . '/' . SitemapConfig::getSitemapFilename($lang['id_lang'], false, true, false, 1),
                'link' => SitemapConfig::getLinkSitemap($lang['id_lang'], false, true, false, 1),
                'cron_url' => SitemapConfig::getCronUrl($lang['id_lang'], true, false, $id_shop) . '&only=1',
                'cron' => SitemapConfig::getFullCronUrl($lang['id_lang'], true, false, $id_shop) . '&only=1',
                'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap($lang['id_lang'], false, true)),
                'pages' => self::getPages(_PS_ROOT_DIR_ . '/' . SitemapConfig::getSitemapFilename($lang['id_lang'], false, true, false, 1)),
            ];
        }

        $sitemaps['category_only'] = [];
        $sitemaps['category_only']['link'] = [
            'name' => SitemapConfig::getSitemapFilename(null, false, true, false, 2),
            'description' => $l->l('Sitemap with all active languages and only categories images', __FILE__),
            'full_link' => _PS_ROOT_DIR_ . '/' . SitemapConfig::getSitemapFilename(null, false, true, false, 2),
            'link' => SitemapConfig::getLinkSitemap(null, false, true, false, 2),
            'cron_url' => SitemapConfig::getCronUrl(null, true, false, $id_shop) . '&only=2',
            'cron' => SitemapConfig::getFullCronUrl(null, true, false, $id_shop) . '&only=2',
            'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap(null, false, true)),
            'pages' => self::getPages(SitemapConfig::getFullLinkSitemap(null, false, true, false, 2)),
        ];
        $sitemaps['category_only']['lang'] = [];
        foreach (ToolsModuleSMP::getLanguages(true) as $lang) {
            $sitemaps['category_only']['lang'][$lang['iso_code']] = [
                'name' => SitemapConfig::getSitemapFilename($lang['id_lang'], false, true, false, 2),
                'description' => sprintf($l->l('Sitemap with %s language and only category images', __FILE__), $lang['name']),
                'full_link' => _PS_ROOT_DIR_ . '/' . SitemapConfig::getSitemapFilename($lang['id_lang'], false, true, false, 2),
                'link' => SitemapConfig::getLinkSitemap($lang['id_lang'], false, true, false, 2),
                'cron_url' => SitemapConfig::getCronUrl($lang['id_lang'], true, false, $id_shop) . '&only=2',
                'cron' => SitemapConfig::getFullCronUrl($lang['id_lang'], true, false, $id_shop) . '&only=2',
                'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap($lang['id_lang'], false, true)),
                'pages' => self::getPages(_PS_ROOT_DIR_ . '/' . SitemapConfig::getSitemapFilename($lang['id_lang'], false, true, false, 2)),
            ];
        }

        $sitemaps['alternate_links'] = [];
        $sitemaps['alternate_links']['link'] = [
            'name' => SitemapConfig::getSitemapFilename(null, false, false, true),
            'description' => $l->l('Sitemap with alternate links', __FILE__),
            'full_link' => SitemapConfig::getFullLinkSitemap(null, false, false, true),
            'link' => SitemapConfig::getLinkSitemap(null, false, false, true),
            'cron_url' => SitemapConfig::getCronUrl(
                null,
                false,
                true,
                $id_shop
            ),
            'cron' => SitemapConfig::getFullCronUrl(
                null,
                false,
                true,
                $id_shop
            ),
            'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap(null, false, false, true)),
            'pages' => self::getPages(SitemapConfig::getFullLinkSitemap(null, false, false, true)),
        ];
        $sitemaps['product_images_alternate_links'] = [];
        $sitemaps['product_images_alternate_links']['link'] = [
            'name' => SitemapConfig::getSitemapFilename(null, false, true, true),
            'description' => $l->l('Sitemap with product images and alternate links', __FILE__),
            'full_link' => SitemapConfig::getFullLinkSitemap(null, false, true, true),
            'link' => SitemapConfig::getLinkSitemap(null, false, true, true),
            'cron_url' => SitemapConfig::getCronUrl(
                null,
                true,
                true,
                $id_shop
            ),
            'cron' => SitemapConfig::getFullCronUrl(
                null,
                true,
                true,
                $id_shop
            ),
            'date' => self::getDateSitemap(SitemapConfig::getFullLinkSitemap(null, false, true, true)),
            'pages' => self::getPages(SitemapConfig::getFullLinkSitemap(null, false, true, true)),
        ];

        return $sitemaps;
    }

    public static function getDateSitemap($link)
    {
        if (!file_exists($link)) {
            return '0000-00-00 00:00:00';
        }
        return date('H:i:s d-m-Y', filemtime($link));
    }

    public static function getPages($link)
    {
        $xml = @simplexml_load_string(call_user_func('file_get_contents', $link));
        if (!$xml) {
            return [];
        }
        $pages = [];
        if (isset($xml->sitemap)) {
            foreach ($xml->sitemap as $element) {
                if (isset($element->loc)) {
                    $pages[] = (string) $element->loc;
                }
            }
        }
        return $pages;
    }
}
