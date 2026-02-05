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

class ProductLabel extends ObjectModel
{
    public $stickersbanners_id;
    public $color;
    public $bg_color;
    public $font;
    public $font_size;
    public $border_color;
    public $font_weight;
    public $start_date;
    public $expiry_date;
    public $banner_status;

    public static $definition = [
        'table' => 'fmm_stickersbanners',
        'primary' => 'stickersbanners_id',
        'multilang' => true,
        'fields' => [
            'color' => ['type' => self::TYPE_STRING],
            'bg_color' => ['type' => self::TYPE_STRING],
            'font' => ['type' => self::TYPE_STRING],
            'font_size' => ['type' => self::TYPE_STRING],
            'border_color' => ['type' => self::TYPE_STRING],
            'font_weight' => ['type' => self::TYPE_STRING],
            'expiry_date' => ['type' => self::TYPE_DATE],
            'start_date' => ['type' => self::TYPE_DATE],
            'banner_status' => ['type' => self::TYPE_INT],
        ],
    ];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function delete()
    {
        $res = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners` WHERE `stickersbanners_id` = ' . (int) $this->stickersbanners_id);
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

    public static function getFieldTitle($id, $id_lang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `title`
        FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_lang`
        WHERE `stickersbanners_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);
        if ($result && isset($result['title'])) {
            return $result['title'];
        }

        // Handle the case where the query did not return a valid result
        return null;
    }

    public static function getColors($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `color`, `bg_color`, `border_color`
        FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners`
        WHERE `stickersbanners_id` = ' . (int) $id);

        return $result;
    }

    public static function getStickerIdStatic($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `stickersbanners_id`
        FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_lang`
        WHERE `stickersbanners_id` = ' . (int) $id);

        return $result['stickersbanners_id'];
    }

    public function updateLabelText($id, $id_lang, $title)
    {
        Db::getInstance()->execute(' UPDATE ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang
            SET `title` = "' . pSQL($title) . '"
            WHERE `stickersbanners_id` = ' . (int) $id . ' AND `id_lang` = ' . (int) $id_lang);
    }

    public function insertLabelText($id, $id_lang, $title)
    {
        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang (`stickersbanners_id`, `id_lang`, `title`)
            VALUES(' . (int) $id . ', ' . (int) $id_lang . ', "' . pSQL($title) . '")
        ');
    }

    public static function removeShopLabels($stickersbanners_id)
    {
        return (bool) Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'fmm_stickersbanners_shop
            WHERE `stickersbanners_id` = ' . (int) $stickersbanners_id);
    }

    public static function insertShopLabels($stickersbanners_id, $id_shop)
    {
        return Db::getInstance()->execute(' INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickersbanners_shop (`stickersbanners_id`, `id_shop`)
            VALUES(' . (int) $stickersbanners_id . ', ' . (int) $id_shop . ')');
    }

    public static function getShopLabels($stickersbanners_id)
    {
        $result = Db::getInstance()->ExecuteS('SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_shop`
            WHERE stickersbanners_id = ' . (int) $stickersbanners_id);

        if ($result) {
            foreach ($result as $key => $value) {
                $result[$key] = $value['id_shop'];
            }
        }

        return $result;
    }

    public static function getShopBannerStickers($stickersbanners_id)
    {
        $result = Db::getInstance()->ExecuteS('SELECT `id_shop` FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_shop`
            WHERE stickersbanners_id = ' . (int) $stickersbanners_id);
        if ($result) {
            foreach ($result as $key => $value) {
                $result[$key] = $value['id_shop'];
            }
        }

        return $result;
    }

    public static function getBannerStickerById($id_sticker, $id_shop)
    {
        $result = Db::getInstance()->getRow('SELECT fbs.*, fsr.*, fss.* FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners` as fbs 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_rules` as fsr on fbs.`stickersbanners_id`=fsr.`stickerbanner_id` 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickersbanners_shop` as fss on fbs.`stickersbanners_id`=fss.`stickersbanners_id` WHERE fbs.`stickersbanners_id` = ' . (int) $id_sticker . ' and fss.`id_shop`=' . $id_shop);

        return $result;
    }

    public static function deleteBannerStickerById($id_sticker, $id_shop)
    {
        $result = Db::getInstance()->getRow('SELECT fbs.`stickersbanners_id`, fsr.`fmm_stickers_rules_id`, fss.`id_shop` FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners` as fbs 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_rules` as fsr on fbs.`stickersbanners_id`=fsr.`stickerbanner_id` 
            LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickersbanners_shop` as fss on fbs.`stickersbanners_id`=fss.`stickersbanners_id` WHERE fbs.`stickersbanners_id` = ' . (int) $id_sticker . ' and fss.`id_shop`=' . $id_shop);

        if (!empty($result)) {
            $delete_fsbs_result = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_shop` WHERE `stickersbanners_id` = ' . (int) $result['stickersbanners_id']);

            $delete_fsbrs_result = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop` WHERE `fmm_stickers_rules_id` = ' . (int) $result['fmm_stickers_rules_id']);

            /* delete sticker rule */
            $delete_fsbl_result = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners_lang` where `stickersbanners_id`=' . (int) $result['stickersbanners_id']);

            $delete_fsr_result = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules` where `stickerbanner_id`=' . (int) $result['stickersbanners_id']);

            $delete_fsb_result = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickersbanners` where `stickersbanners_id`=' . (int) $result['stickersbanners_id']);

            if (
                $delete_fsbs_result
                && $delete_fsbrs_result
                && $delete_fsbl_result
                && $delete_fsr_result
                && $delete_fsb_result
            ) {
                return true;
            } else {
                return false;
            }
        }
    }
}
