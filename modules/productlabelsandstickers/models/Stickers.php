<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Stickers extends ObjectModel
{
    public $sticker_id;
    public $sticker_name;
    public $sticker_type;
    public $sticker_size;
    public $sticker_opacity;
    public $sticker_size_list;
    public $sticker_size_home;
    public $sticker_image;
    public $x_align;
    public $y_align;
    public $transparency;
    public $medium_width;
    public $medium_height;
    public $medium_x;
    public $medium_y;
    public $small_width;
    public $small_height;
    public $small_x;
    public $small_y;
    public $thickbox_width;
    public $thickbox_height;
    public $thickbox_x;
    public $thickbox_y;
    public $large_width;
    public $large_height;
    public $large_x;
    public $large_y;
    public $home_width;
    public $home_height;
    public $home_x;
    public $home_y;
    public $cart_width;
    public $cart_height;
    public $cart_x;
    public $cart_y;
    public $creation_date;
    public $updation_date;
    public $color;
    public $bg_color;
    public $font;
    public $font_size;
    public $font_size_listing;
    public $font_size_product;
    public $text_status;
    public $expiry_date;
    public $start_date;
    public $y_coordinate_listing;
    public $y_coordinate_product;
    public $url;
    public $tip;
    public $tip_bg;
    public $tip_color;
    public $tip_txt;
    public $tip_pos;
    public $tip_width;
    public $product;
    public $listing;
    public $home;
    public $status;

    public static $definition = [
        'table' => 'fmm_stickers',
        'primary' => 'sticker_id',
        'multilang' => true,
        'fields' => [
            'sticker_name' => ['type' => self::TYPE_STRING],
            'sticker_type' => ['type' => self::TYPE_STRING],
            'sticker_size' => ['type' => self::TYPE_STRING],
            'sticker_opacity' => ['type' => self::TYPE_STRING],
            'sticker_image' => ['type' => self::TYPE_STRING],
            'x_align' => ['type' => self::TYPE_STRING],
            'y_align' => ['type' => self::TYPE_STRING],
            'transparency' => ['type' => self::TYPE_INT],
            'medium_width' => ['type' => self::TYPE_INT],
            'medium_height' => ['type' => self::TYPE_INT],
            'medium_x' => ['type' => self::TYPE_INT],
            'medium_y' => ['type' => self::TYPE_INT],
            'small_width' => ['type' => self::TYPE_INT],
            'small_height' => ['type' => self::TYPE_INT],
            'small_x' => ['type' => self::TYPE_INT],
            'small_y' => ['type' => self::TYPE_INT],
            'thickbox_width' => ['type' => self::TYPE_INT],
            'thickbox_height' => ['type' => self::TYPE_INT],
            'thickbox_x' => ['type' => self::TYPE_INT],
            'thickbox_y' => ['type' => self::TYPE_INT],
            'large_width' => ['type' => self::TYPE_INT],
            'large_height' => ['type' => self::TYPE_INT],
            'large_x' => ['type' => self::TYPE_INT],
            'large_y' => ['type' => self::TYPE_INT],
            'home_width' => ['type' => self::TYPE_INT],
            'home_height' => ['type' => self::TYPE_INT],
            'home_x' => ['type' => self::TYPE_INT],
            'home_y' => ['type' => self::TYPE_INT],
            'cart_width' => ['type' => self::TYPE_INT],
            'cart_height' => ['type' => self::TYPE_INT],
            'cart_x' => ['type' => self::TYPE_INT],
            'cart_y' => ['type' => self::TYPE_INT],
            'creation_date' => ['type' => self::TYPE_DATE],
            'updation_date' => ['type' => self::TYPE_DATE],
            'sticker_size_list' => ['type' => self::TYPE_STRING],
            'sticker_size_home' => ['type' => self::TYPE_STRING],
            'color' => ['type' => self::TYPE_STRING],
            'bg_color' => ['type' => self::TYPE_STRING],
            'font' => ['type' => self::TYPE_STRING],
            'font_size' => ['type' => self::TYPE_STRING],
            'font_size_listing' => ['type' => self::TYPE_STRING],
            'font_size_product' => ['type' => self::TYPE_STRING],
            'text_status' => ['type' => self::TYPE_INT],
            'expiry_date' => ['type' => self::TYPE_DATE],
            'start_date' => ['type' => self::TYPE_DATE],
            'y_coordinate_listing' => ['type' => self::TYPE_INT],
            'y_coordinate_product' => ['type' => self::TYPE_INT],
            'url' => ['type' => self::TYPE_STRING],
            'tip' => ['type' => self::TYPE_INT],
            'tip_width' => ['type' => self::TYPE_INT],
            'tip_pos' => ['type' => self::TYPE_INT],
            'tip_bg' => ['type' => self::TYPE_STRING],
            'tip_color' => ['type' => self::TYPE_STRING],
            'tip_txt' => ['type' => self::TYPE_STRING, 'lang' => true],
            'product' => ['type' => self::TYPE_INT],
            'listing' => ['type' => self::TYPE_INT],
            'home' => ['type' => self::TYPE_INT],
            'status' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function delete()
    {
        $res = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers` WHERE `sticker_id` = ' . (int) $this->sticker_id);
        $res &= parent::delete();

        return $res;
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            exit(Tools::displayError());
        }

        $result = true;
        foreach ($selection as $id) {
            $this->id = (int) $id;
            $result = $result && $this->delete();
        }

        return $result;
    }

    public function getPids()
    {
        $sql = 'SELECT *
            FROM ' . _DB_PREFIX_ . 'fmm_stickers_products
            ORDER BY sticker_id DESC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getAllStickers()
    {
        $now = date('Y-m-d H:i:s');
        $sql = 'SELECT *
            FROM ' . _DB_PREFIX_ . 'fmm_stickers
            WHERE \'' . pSQL($now) . '\' < `expiry_date` OR `expiry_date` = \'0000-00-00 00:00:00\'
            ORDER BY sticker_id DESC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getProductStickersStatic($id_product)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT ps1.*
            FROM `' . _DB_PREFIX_ . 'fmm_stickers` ps1
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickers_products ps2 ON (ps1.sticker_id = ps2.sticker_id)
            WHERE ps2.`id_product` = ' . (int) $id_product);
        foreach ($sql as &$row) {
            $row['title'] = $this->getTitleSticker($row['sticker_id'], $id_lang);
            $row['tip_txt'] = $this->getHintTxtSticker($row['sticker_id'], $id_lang);
        }

        return $sql;
    }

    public function getProductStickers($id_product, $type = 'product')
    {
        $id_lang = (int) Context::getContext()->language->id;
        $now = date('Y-m-d H:i:s');
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT ps1.*
            FROM `' . _DB_PREFIX_ . 'fmm_stickers` ps1
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickers_products ps2 ON (ps1.sticker_id = ps2.sticker_id)
            WHERE ps2.`id_product` = ' . (int) $id_product . '
            AND ps1.`' . pSQL($type) . '` > 0
            AND
            (
                (ps1.`start_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' >= ps1.`start_date`)
                AND
                (ps1.`expiry_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' <= ps1.`expiry_date`)
            )');

        if (isset($sql) && $sql) {
            foreach ($sql as &$row) {
                $row['x_align'] = (empty($row['x_align'])) ? 'right' : $row['x_align'];
                $row['y_align'] = (empty($row['y_align'])) ? 'top' : $row['y_align'];
                $row['title'] = $this->getTitleSticker($row['sticker_id'], $id_lang);
                $row['tip_txt'] = $this->getHintTxtSticker($row['sticker_id'], $id_lang);
            }
        }

        return $sql;
    }

    public function getSticker($id, $type = 'product')
    {
        $id_lang = (int) Context::getContext()->language->id;
        $now = date('Y-m-d H:i:s');
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT *
            FROM `' . _DB_PREFIX_ . 'fmm_stickers`
            WHERE `sticker_id` = ' . (int) $id . '
            AND `' . pSQL($type) . '` > 0
            AND
            (
                (`start_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' >= `start_date`)
                AND
                (`expiry_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' <= `expiry_date`)
            )');
        if (isset($sql) && $sql) {
            foreach ($sql as &$row) {
                $row['x_align'] = (empty($row['x_align'])) ? 'right' : $row['x_align'];
                $row['y_align'] = (empty($row['y_align'])) ? 'top' : $row['y_align'];
                $row['title'] = $this->getTitleSticker($row['sticker_id'], $id_lang);
                $row['tip_txt'] = $this->getHintTxtSticker($row['sticker_id'], $id_lang);
            }
        }

        return array_shift($sql);
    }

    public static function getColors($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `color`, `bg_color`
        FROM `' . _DB_PREFIX_ . 'fmm_stickers`
        WHERE `sticker_id` = ' . (int) $id);

        return $result;
    }

    public static function getStickerIdStatic($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `sticker_id`
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang`
        WHERE `sticker_id` = ' . (int) $id);

        if ($result && isset($result['sticker_id'])) {
            return $result['sticker_id'];
        }

        // Handle the case where the query did not return a valid result
        return null;
    }

    public function deleteStickersById($id)
    {
        $query = 'DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang` WHERE `sticker_id` = ' . (int) $id;
        $success = Db::getInstance()->execute($query);

        return $success ? true : false;
    }

    public function updateLabelText($id, $id_lang, $title)
    {
        Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . 'fmm_stickers_lang`  
            SET `title` = "' . pSQL($title) . '"
            WHERE `sticker_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);
    }

    public function insertLabelText($id, $id_lang, $title)
    {
        if (self::checkSticker($id, $id_lang)) {
            if (self::deleteStickersById($id, $id_lang, $title)) {
                Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_lang (`sticker_id`, `id_lang`, `title`) VALUES(' . (int) $id . ', ' . (int) $id_lang . ', "' . pSQL($title) . '")');
            }
        } else {
            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_lang (`sticker_id`, `id_lang`, `title`) VALUES(' . (int) $id . ', ' . (int) $id_lang . ', "' . pSQL($title) . '")');
        }
    }

    public function insertLabelImage($id, $id_lang, $title)
    {
        if (self::checkSticker($id, $id_lang)) {
            if (self::deleteStickersById($id, $id_lang, $title)) {
                Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_lang (`sticker_id`, `id_lang`, `title`) VALUES(' . (int) $id . ', ' . (int) $id_lang . ', "' . pSQL($title) . '")');
            }
        } else {
            Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_lang (`sticker_id`, `id_lang`, `title`) VALUES(' . (int) $id . ', ' . (int) $id_lang . ', "' . pSQL($title) . '")');
        }
    }

    public function checkSticker($id, $id_lang)
    {
        $query = 'SELECT COUNT(*) as count FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang` WHERE `sticker_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang;
        $result = Db::getInstance()->getValue($query);

        return $result > 0;
    }

    public static function getFieldTitle($id, $id_lang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `title`
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang`
        WHERE `sticker_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);
        if ($result && isset($result['title'])) {
            return $result['title'];
        }

        // Handle the case where the query did not return a valid result
        return null;
    }

    public static function getFieldHint($id, $id_lang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `tip_txt`
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang`
        WHERE `sticker_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);

        if ($result && isset($result['tip_txt'])) {
            return $result['tip_txt'];
        }

        // Handle the case where the query did not return a valid result
        return null;
    }

    public static function getTitleSticker($id, $id_lang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `title`
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang`
        WHERE `sticker_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);

        if ($result && isset($result['title'])) {
            return $result['title'];
        }
    }

    public static function getHintTxtSticker($id, $id_lang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `tip_txt`
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang`
        WHERE `sticker_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);

        if ($result && isset($result['tip_txt'])) {
            return $result['tip_txt'];
        }
    }

    public function getAllBanners()
    {
        $id_lang = (int) Context::getContext()->language->id;
        $sql = 'SELECT b.*, bl.`title`
            FROM ' . _DB_PREFIX_ . 'fmm_stickersbanners b
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang bl ON (b.stickersbanners_id = bl.stickersbanners_id)
            WHERE id_lang = ' . (int) $id_lang;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getAllBannersWithRules()
    {
        $id_lang = (int) Context::getContext()->language->id;

        $sql = 'SELECT b.*, bl.`title` AS `banner_title`, br.*
                FROM ' . _DB_PREFIX_ . 'fmm_stickersbanners b
                LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang bl ON (b.`stickersbanners_id` = bl.`stickersbanners_id`)
                LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickers_rules br ON (b.`stickersbanners_id` = br.`stickerbanner_id`)
                WHERE bl.`id_lang`=' . (int) $id_lang;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getRule($id_sticker)
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'fmm_stickers_rules
                WHERE stickerbanner_id = ' . (int) $id_sticker;

        $result = Db::getInstance()->getRow($sql);

        return $result;
    }

    public function getStickerRule($id_sticker)
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'fmm_stickers_rules
                WHERE sticker_id = ' . (int) $id_sticker;

        $result = Db::getInstance()->getRow($sql);

        return $result;
    }

    public function getAllRuleShop($id_rule)
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'fmm_stickers_rules_shop
                WHERE fmm_stickers_rules_id = ' . (int) $id_rule;

        $result = Db::getInstance()->getRow($sql);

        return $result;
    }

    public static function getSelectedBanners($id)
    {
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `stickersbanners_id`
        FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_products`
        WHERE `id_product` = ' . (int) $id);

        $selectedBanners = [];
        if ($results) {
            foreach ($results as $result) {
                if (isset($result['stickersbanners_id'])) {
                    $selectedBanners[] = $result['stickersbanners_id'];
                }
            }
        }

        return $selectedBanners;
    }

    public static function getBannerIdFromRules($ruleid)
    {
        $result = Db::getInstance()->getRow('SELECT `stickerbanner_id` from `' . _DB_PREFIX_ . 'fmm_stickers_rules` WHERE `sticker_id` = ' . (int) $ruleid);

        if ($result && isset($result['stickerbanner_id'])) {
            return $result['stickerbanner_id'];
        }
    }

    public static function getBannersByBannerId($id)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $now = date('Y-m-d H:i:s');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT b.*, bl.`title`
            FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners` b
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang bl
                ON (b.stickersbanners_id = bl.stickersbanners_id)
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_shop ssh
                ON (b.stickersbanners_id = ssh.stickersbanners_id)
            WHERE b.`stickersbanners_id` = ' . (int) $id . '
            AND `id_lang` = ' . (int) $id_lang . '
            AND ssh.id_shop = ' . (int) $id_shop . '
            AND
            (
                (b.`start_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' >= b.`start_date`)
                AND
                (b.`expiry_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' <= b.`expiry_date`)
            )');

        return $result;
    }

    public static function getProductBanner($id)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $id_lang = (int) Context::getContext()->language->id;
        $now = date('Y-m-d H:i:s');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT b.*, bl.`title`
            FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners` b
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_products bp
                ON (b.stickersbanners_id = bp.stickersbanners_id)
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang bl
                ON (b.stickersbanners_id = bl.stickersbanners_id)
            LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_shop ssh
                ON (b.stickersbanners_id = ssh.stickersbanners_id)
            WHERE bp.`id_product` = ' . (int) $id . '
            AND `id_lang` = ' . (int) $id_lang . '
            AND ssh.id_shop = ' . (int) $id_shop . '
            AND
            (
                (b.`start_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' >= b.`start_date`)
                AND
                (b.`expiry_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' <= b.`expiry_date`)
            )');

        return $result;
    }

    public static function removeShopStickers($id_sticker)
    {
        return (bool) Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'fmm_stickers_shop
            WHERE `sticker_id` = ' . (int) $id_sticker);
    }

    public static function insertShopStickers($id_sticker, $id_shop)
    {
        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_shop (`sticker_id`, `id_shop`)
            VALUES(' . (int) $id_sticker . ', ' . (int) $id_shop . ')');
    }

    public static function getShopStickers($id_sticker)
    {
        $result = Db::getInstance()->ExecuteS('SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'fmm_stickers_shop`
            WHERE sticker_id = ' . (int) $id_sticker);
        if ($result) {
            foreach ($result as $key => $value) {
                $result[$key] = $value['id_shop'];
            }
        }

        return $result;
    }

    public static function getStickerById($id_sticker)
    {
        $result = Db::getInstance()->getRow('SELECT fs.*, fsr.*, fss.* FROM `' . _DB_PREFIX_ . 'fmm_stickers` as fs 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_rules` as fsr on fs.`sticker_id`=fsr.`sticker_id` 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_shop` as fss on fs.`sticker_id`=fss.`sticker_id` WHERE fs.`sticker_id` = ' . (int) $id_sticker);

        return $result;
    }

    public static function deleteStickerById($id_sticker)
    {
        $result = Db::getInstance()->getRow('SELECT fs.*, fsr.*, fss.* FROM `' . _DB_PREFIX_ . 'fmm_stickers` as fs 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_rules` as fsr on fs.`sticker_id`=fsr.`sticker_id` 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_shop` as fss on fs.`sticker_id`=fss.`sticker_id` WHERE fs.`sticker_id` = ' . (int) $id_sticker);

        if (!empty($result)) {
            $delete_sticker_shop = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_shop` WHERE `sticker_id` = ' . (int) $result['sticker_id']);

            /* delete sticker rule shop */
            $delete_sticker_rules_shop = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop` WHERE `fmm_stickers_rules_id` = ' . (int) $result['fmm_stickers_rules_id']);

            /* delete sticker rule */
            $delete_sticker_lang = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_lang` where `sticker_id`=' . (int) $result['sticker_id']);

            $delete_sticker_rule = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules` where `sticker_id`=' . (int) $result['sticker_id']);

            $delete_sticker = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers` where `sticker_id`=' . (int) $result['sticker_id']);

            if (
                $delete_sticker_shop
                && $delete_sticker_rules_shop
                && $delete_sticker_lang
                && $delete_sticker_rule
                && $delete_sticker
            ) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function DuplicateRow($sticker)
    {
        $sticker['sticker_opacity'] = !empty($sticker['sticker_opacity']) ? $sticker['sticker_opacity'] : 'Null';

        $sticker['sticker_size'] = !empty($sticker['sticker_size']) ? $sticker['sticker_size'] : 'Null';
        $sticker['sticker_size_list'] = !empty($sticker['sticker_size_list']) ? $sticker['sticker_size_list'] : 'Null';

        $sticker['sticker_size_home'] = !empty($sticker['sticker_size_home']) ? $sticker['sticker_size_home'] : 'Null';

        $insertdupli = 'INSERT INTO `' . _DB_PREFIX_ . 'fmm_stickers` (
            `sticker_name`,
            `sticker_type`,
            `sticker_size`,
            `sticker_opacity`,
            `sticker_size_list`,
            `sticker_size_home`,
            `sticker_image`,
            `x_align`,
            `y_align`,
            `transparency`,
            `medium_width`,
            `medium_height`,
            `medium_x`,
            `medium_y`,
            `small_width`,
            `small_height`,
            `small_x`,
            `small_y`,
            `thickbox_width`,
            `thickbox_height`,
            `thickbox_x`,
            `thickbox_y`,
            `large_width`,
            `large_height`,
            `large_x`,
            `large_y`,
            `home_width`,
            `home_height`,
            `home_x`,
            `home_y`,
            `cart_width`,
            `cart_height`,
            `cart_x`,
            `cart_y`,
            `creation_date`,
            `updation_date`,
            `color`,
            `bg_color`,
            `font`,
            `font_size`,
            `text_status`,
            `tip`,
            `tip_pos`,
            `tip_width`,
            `tip_color`,
            `tip_bg`,
            `expiry_date`,
            `start_date`,
            `url`,
            `y_coordinate_listing`,
            `y_coordinate_product`,
            `product`,
            `listing`,
            `home`
            ) 
            VALUES (
                "' . $sticker['sticker_name'] . '",
                "' . $sticker['sticker_type'] . '",
                ' . $sticker['sticker_size'] . ',
                ' . $sticker['sticker_opacity'] . ',
                ' . $sticker['sticker_size_list'] . ',
                ' . $sticker['sticker_size_home'] . ',
                "' . $sticker['sticker_image'] . '",
                "' . $sticker['x_align'] . '",
                "' . $sticker['y_align'] . '",
                ' . $sticker['transparency'] . ',
                ' . $sticker['medium_width'] . ',
                ' . $sticker['medium_height'] . ',
                ' . $sticker['medium_x'] . ',
                ' . $sticker['medium_y'] . ',
                ' . $sticker['small_width'] . ',
                ' . $sticker['small_height'] . ',
                ' . $sticker['small_x'] . ',
                ' . $sticker['small_y'] . ',
                ' . $sticker['thickbox_width'] . ',
                ' . $sticker['thickbox_height'] . ',
                ' . $sticker['thickbox_x'] . ',
                ' . $sticker['thickbox_y'] . ',
                ' . $sticker['large_width'] . ',
                ' . $sticker['large_height'] . ',
                ' . $sticker['large_x'] . ',
                ' . $sticker['large_y'] . ',
                ' . $sticker['home_width'] . ',
                ' . $sticker['home_height'] . ',
                ' . $sticker['home_x'] . ',
                ' . $sticker['home_y'] . ',
                ' . $sticker['cart_width'] . ',
                ' . $sticker['cart_height'] . ',
                ' . $sticker['cart_x'] . ',
                ' . $sticker['cart_y'] . ',
                "' . $sticker['creation_date'] . '",
                "' . $sticker['updation_date'] . '",
                "' . $sticker['color'] . '",
                "' . $sticker['bg_color'] . '",
                "' . $sticker['font'] . '",
                ' . $sticker['font_size'] . ',
                ' . $sticker['text_status'] . ',
                ' . $sticker['tip'] . ',
                ' . $sticker['tip_pos'] . ',
                ' . $sticker['tip_width'] . ',
                "' . $sticker['tip_color'] . '",
                "' . $sticker['tip_bg'] . '",
                "' . $sticker['expiry_date'] . '",
                "' . $sticker['start_date'] . '",
                "' . $sticker['url'] . '",
                ' . $sticker['y_coordinate_listing'] . ',
                ' . $sticker['y_coordinate_product'] . ',
                ' . $sticker['product'] . ',
                ' . $sticker['listing'] . ',
                ' . $sticker['home'] . ')';

        Db::getInstance()->execute($insertdupli);

        $current_insertedid = Db::getInstance()->Insert_ID();

        /* insert lang ========================================================= */
        $sql_lang = 'select * from `' . _DB_PREFIX_ . 'fmm_stickers_lang` where `sticker_id`=' . (int) $sticker['sticker_id'];
        $dupli_sticker_lang = Db::getInstance()->executeS($sql_lang);

        foreach ($dupli_sticker_lang as $value) {
            $sql = 'insert into `' . _DB_PREFIX_ . 'fmm_stickers_lang` (
                `sticker_id`,
                `id_lang`,
                `title`,
                `tip_txt`
                ) values (
                    "' . $current_insertedid . '",
                    "' . $value['id_lang'] . '",
                    "' . $value['title'] . '",
                    "' . $value['tip_txt'] . '"
                )';

            Db::getInstance()->execute($sql);
        }
        /* insert rule ========================================================= */
        $sql_rule = 'select * from `' . _DB_PREFIX_ . 'fmm_stickers_rules` where `sticker_id`=' . (int) $sticker['sticker_id'];
        $res_rul = Db::getInstance()->getRow($sql_rule);

        $res_rul['title'] = !empty($res_rul['title']) ? $res_rul['title'] : 'NULL';
        $res_rul['excluded_p'] = !empty($res_rul['excluded_p']) ? $res_rul['excluded_p'] : 'NULL';

        Db::getInstance()->execute('insert into `' . _DB_PREFIX_ . 'fmm_stickers_rules` 
                (
                    `sticker_id`,
                    `stickerbanner_id`,
                    `title`, 
                    `rule_type`, 
                    `value`, 
                    `status`, 
                    `start_date`, 
                    `expiry_date`, 
                    `excluded_p`
                ) values ('
            . $res_rul['sticker_id'] . ',
                    ' . $res_rul['stickerbanner_id'] . ', "' . $res_rul['title'] . '", "' . $res_rul['rule_type'] . '", "' . $res_rul['value'] . '", ' . $res_rul['status'] . ', "' . $res_rul['start_date'] . '", "' . $res_rul['expiry_date'] . '", "' . $res_rul['excluded_p'] . '")');

        $current_rule = Db::getInstance()->Insert_ID();

        /* insert sticker shop =========================== */
        $sql_sticker_shop = 'select * from `' . _DB_PREFIX_ . 'fmm_stickers_shop` where `sticker_id`=' . (int) $sticker['sticker_id'];

        $res_sticker_shop = Db::getInstance()->getRow($sql_sticker_shop);

        Db::getInstance()->execute('insert into `' . _DB_PREFIX_ . 'fmm_stickers_shop` (`sticker_id`, `id_shop`) values (' . $current_insertedid . ', ' . $res_sticker_shop['id_shop'] . ')');

        /* insert sticker rule shop =========================== */
        $sql_sticker_rule_shop = 'select * from `' . _DB_PREFIX_ . 'fmm_stickers_shop` where `sticker_id`=' . (int) $sticker['sticker_id'];

        $res_sticker_rule_shop = Db::getInstance()->getRow($sql_sticker_rule_shop);

        Db::getInstance()->execute('insert into `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop` (`sticker_id`, `id_shop`) values (' . $current_rule . ', ' . $res_sticker_shop['id_shop'] . ')');
    }

    public static function overrideTextSticker($sticker_id)
    {
        Db::getInstance()->execute(
            '
            UPDATE `' . _DB_PREFIX_ . 'fmm_stickers`
            SET `bg_color` = NULL,
                `color` = NULL,
                `font` = NULL,
                `font_size` = NULL,
                `font_size_listing` = NULL,
                `font_size_product` = NULL
            WHERE `sticker_id` = ' . (int) $sticker_id
        );
    }
}
