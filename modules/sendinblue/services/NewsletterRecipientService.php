<?php
/**
 * 2007-2025 Sendinblue
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@sendinblue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Sendinblue <contact@sendinblue.com>
 * @copyright 2007-2025 Sendinblue
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Sendinblue
 */

namespace Sendinblue\Services;

if (!defined('_PS_VERSION_')) {
    exit;
}

class NewsletterRecipientService
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function isNewsletterRecipientExist($email)
    {
        return \Db::getInstance()->getValue(
            'SELECT LOWER(`email`)
                FROM `' . _DB_PREFIX_ . 'emailsubscription`
                WHERE LOWER(`email`) = "' . pSQL(strtolower($email)) . '"
                AND `active` = 1'
        ) === strtolower($email);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function unsubscribe($email)
    {
        return \Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'emailsubscription WHERE LOWER(email)="' . pSQL(strtolower($email)) . '"'
        );
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array
     *
     * @throws \PrestaShopException
     */
    public function getNewsletterRecipients($limit, $offset = 0)
    {
        $dbquery = new \DbQuery();
        $dbquery->select('CONCAT("N", e.`id`) AS `id`, LOWER(e.`email`) AS email, 
        e.`active` as `newsletter`, l.`iso_code` as `id_lang`');
        $dbquery->from('emailsubscription', 'e');
        $dbquery->limit($limit, $offset);
        $dbquery->leftJoin('shop', 's', 's.id_shop = e.id_shop');
        $dbquery->leftJoin('lang', 'l', 'l.id_lang = e.id_lang');

        $where = 'e.`active` = 1';
        $shop_id = \Shop::getContextShopID(true);
        if (!empty($shop_id)) {
            $where .= ' AND e.`id_shop` = ' . $shop_id;
            $shop_group = \Shop::getContextShopGroupID();
            if (!empty($shop_group)) {
                $where .= ' AND e.`id_shop_group` = ' . $shop_group;
            }
        }
        $dbquery->where($where);

        return ['customers' => \Db::getInstance()->executeS($dbquery->build())];
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return int
     *
     * @throws \PrestaShopException
     */
    public function getNewsletterRecipientsCount()
    {
        $dbquery = new \DbQuery();
        $dbquery->select('count(CONCAT("N", e.`id`)) AS `count`');
        $dbquery->from('emailsubscription', 'e');
        $dbquery->leftJoin('shop', 's', 's.id_shop = e.id_shop');

        $where = 'e.`active` = 1';
        $shop_id = \Shop::getContextShopID(true);
        if (!empty($shop_id)) {
            $where .= ' AND e.`id_shop` = ' . $shop_id;
            $shop_group = \Shop::getContextShopGroupID();
            if (!empty($shop_group)) {
                $where .= ' AND e.`id_shop_group` = ' . $shop_group;
            }
        }
        $dbquery->where($where);
        $result = \Db::getInstance()->executeS($dbquery->build());

        return isset($result[0]['count']) ? (int) $result[0]['count'] : 0;
    }
}
