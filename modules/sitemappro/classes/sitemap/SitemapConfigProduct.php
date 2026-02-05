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

class SitemapConfigProduct extends SitemapConfig
{
    protected static function getConfigSql()
    {
        $sql = parent::getConfigSql();
        $sql->select('pl.`name`');
        $sql->select(
            '(SELECT group_concat(DISTINCT cp.`id_category`)
                FROM '._DB_PREFIX_.'category_product cp
                WHERE cp.`id_product` = a.`id_object`) as categories'
        );
        $sql->leftJoin(
            'product_lang',
            'pl',
            'pl.`id_product` = a.`id_object` AND pl.`id_lang` = '
            . (int)Context::getContext()->language->id
        );

        return $sql;
    }

    protected static function getItemsSql($id_lang = null)
    {
        $id_shop = Context::getContext()->shop->id;
        if (_PS_VERSION_ < 1.7) {
            $default_category = ConfSMP::hasConf('category_default_category', null, $id_shop)
                ? ConfSMP::getConf('category_default_category', null, $id_shop)
                : self::$default_params['default_category'];
        } else {
            $cat = ImageType::getFormattedName('category');
            $default_category = ConfSMP::hasConf($cat.'_category', null, $id_shop)
                ? ConfSMP::getConf($cat.'_category', null, $id_shop)
                : self::$default_params[$cat];
        }
        $sql = parent::getItemsSql();
        $sql->select(
            'p.`id_product`, p.`date_upd`, pl.`link_rewrite`,
             pl.`description_short`, pl.`id_lang`, ps.`id_category_default`, cp.`id_category`'
        );
        $sql->select('IF(a.`priority` = NULL, NULL, a.`priority`) as `priority`');
        $sql->select('IF(a.`changefreq` = NULL, NULL, a.`changefreq`) as `changefreq`');
        $sql->select('a.`is_export`');
        $sql->from('product', 'p');
        $sql->leftJoin('product_shop', 'ps', 'p.`id_product` = ps.`id_product`');
        $sql->leftJoin(
            'product_lang',
            'pl',
            'p.`id_product` = pl.`id_product` AND pl.`id_shop` = '.(int)Shop::getContextShopID().' '
            .(!is_null($id_lang) ? 'AND pl.`id_lang` = '.(int)$id_lang : '')
        );
        $sql->leftJoin('category_product', 'cp', 'cp.`id_product` = p.`id_product`');
        $sql->leftJoin(
            self::$definition['table'],
            'a',
            'a.`id_object` = p.`id_product` AND a.`type_object` = "'.pSQL(self::getType()).'"'
        );
        $category_ids = SitemapConfig::getSitemapCategories();
        if ($default_category == 0) {
            $sql->where(
                'ps.`id_shop` = '.(int)Shop::getContextShopID().' AND ps.`active` = 1 AND ps.`visibility` <> "none" 
               '
            );
        } else {
            $sql->where(
                'ps.`id_shop` = '.(int)Shop::getContextShopID().' AND ps.`active` = 1 AND ps.`visibility` <> "none"
                AND p.`id_category_default` IN('.(count($category_ids) ?
                    implode(',', array_map('intval', $category_ids)) : 'NULL').')'
            );
        }
        $sql->where(
            'pl.`id_lang` IN('.implode(
                ',',
                array_map('intval', ToolsModuleSMP::getLanguageIds())
            ).')'
        );
        $sql->groupBy('p.`id_product`, pl.`id_lang`');

        return $sql;
    }

    public static function getItems($id_lang = null, $include_link = false, $with_image = false)
    {
        $items = parent::getItems($id_lang);
        $nb_languages = count(ToolsModuleSMP::getLanguages(true));
        foreach ($items as &$item) {
            if ($include_link && $nb_languages > 0) {
                $item['links'] = self::getLinks($item['id_product']);
            } else {
                $item['links'] = [];
            }
            if ($with_image) {
                $item['images'] = Image::getImages($item['id_lang'], $item['id_product']);
            } else {
                $item['images'] = [];
            }
        }

        return $items;
    }

    public static function getLinks($id_product)
    {
        $links = Db::getInstance()->executeS(
            'SELECT pl.`id_product`, pl.`id_lang`, pl.`link_rewrite`
            FROM `'._DB_PREFIX_.'product_lang` pl
             LEFT JOIN '._DB_PREFIX_.'lang l ON l.`id_lang` = pl.`id_lang` 
             WHERE pl.`id_product` = '.(int)$id_product
            .' AND l.`active` = 1'
        );

        return is_array($links) && count($links) ? $links : [];
    }

    public static function getType()
    {
        return 'product';
    }
}
