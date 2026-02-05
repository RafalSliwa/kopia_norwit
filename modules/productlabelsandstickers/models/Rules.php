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
 *
 * @category  FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Rules extends ObjectModel
{
    public $sticker_id;
    public $title;
    public $stickerbanner_id;
    public $rule_type;
    public $excluded_p;
    public $value;
    public $status;

    public static $definition = [
        'table' => 'fmm_stickers_rules',
        'primary' => 'fmm_stickers_rules_id',
        'multilang' => false,
        'fields' => [
            'title' => ['type' => self::TYPE_STRING],
            'stickerbanner_id' => ['type' => self::TYPE_INT],
            'rule_type' => ['type' => self::TYPE_STRING],
            'excluded_p' => ['type' => self::TYPE_STRING],
            'value' => ['type' => self::TYPE_STRING],
            'status' => ['type' => self::TYPE_INT],
        ],
    ];

    public function saveAll($id_sticker, $stickerbanner_id, $status, $title, $r_type, $value, $start_date, $expiry_date, $excluded_p)
    {
        $start_date = (empty($start_date)) ? '0000-00-00 00:00:00' : $start_date;
        $expiry_date = (empty($start_date)) ? '0000-00-00 00:00:00' : $expiry_date;

        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'fmm_stickers_rules` (
                `sticker_id`,
                `stickerbanner_id`,
                `status`,
                `title`,
                `rule_type`,
                `value`,
                `start_date`,
                `expiry_date`,
                `excluded_p`
            ) VALUES (
                ' . (int) $id_sticker . ',
                ' . (int) $stickerbanner_id . ',
                ' . (int) $status . ',
                "' . pSQL($title) . '",
                "' . pSQL($r_type) . '",
                "' . pSQL($value) . '",
                "' . pSQL($start_date) . '",
                "' . pSQL($expiry_date) . '",
                "' . $excluded_p . '"
            )'
        );

        $last_id = (int) Db::getInstance()->Insert_ID();

        return $last_id;
    }

    public function saveShops($id, $shops)
    {
        $store_shops = Shop::getShops(true, null, false);

        if ($shops[0] == 0) {
            foreach ($store_shops as $shop) {
                Db::getInstance()->execute('
					INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_rules_shop (fmm_stickers_rules_id, id_shop)
					VALUES(' . (int) $id . ', ' . (int) $shop['id_shop'] . ')
				');
            }
        } else {
            foreach ($shops as $shop) {
                Db::getInstance()->execute('
					INSERT INTO ' . _DB_PREFIX_ . 'fmm_stickers_rules_shop (fmm_stickers_rules_id, id_shop)
					VALUES(' . (int) $id . ', ' . (int) $shop . ')
				');
            }
        }
    }

    /* getAllEditData function is modified for 4.0.0 update.
     * the original version is commented above.
     */
    public function getAllEditData($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules`
        WHERE `sticker_id` = ' . (int) $id);

        return $result;
    }

    /* for banner */
    public function getAllEditDataBanner($id)
    {
        $result = Db::getInstance()->executeS('
        SELECT *
        FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules`
        WHERE `fmm_stickers_rules_id` = ' . (int) $id);

        return $result;
    }

    public static function getStickerData($id)
    {
        $result = Db::getInstance()->getValue('
		SELECT `excluded_p`
		FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules`
		WHERE `fmm_stickers_rules_id` = ' . (int) $id);

        return $result;
    }

    public function getAllEditDataShop($id)
    {
        $result = Db::getInstance()->executeS('
		SELECT `id_shop`
		FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop`
		WHERE `fmm_stickers_rules_id` = ' . (int) $id);
        $new_array = [];

        foreach ($result as $key => $value) {
            $new_array[$key] = $value['id_shop'];
        }

        return $new_array;
    }

    public function resetShops($id)
    {
        Db::getInstance()->execute('
		DELETE FROM ' . _DB_PREFIX_ . 'fmm_stickers_rules_shop
		WHERE `fmm_stickers_rules_id` = ' . (int) $id);
    }

    public function changeAll($id, $id_sticker, $stickerbanner_id, $status, $title, $r_type, $value, $start_date, $expiry_date, $excluded_p)
    {
        $start_date = (empty($start_date)) ? '0000-00-00 00:00:00' : $start_date;
        $expiry_date = (empty($start_date)) ? '0000-00-00 00:00:00' : $expiry_date;

        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickers_rules`
			SET `sticker_id` = ' . (int) $id_sticker . ',`stickerbanner_id` = ' . (int) $stickerbanner_id . ', `status` = ' . (int) $status . ', `title` = "' . pSQL($title) . '", `rule_type` = "' . pSQL($r_type) . '", `value` = "' . pSQL($value) . '", `start_date` = "' . pSQL($start_date) . '", `expiry_date` = "' . pSQL($expiry_date) . '", `excluded_p` = "' . pSQL($excluded_p) . '"
			WHERE `fmm_stickers_rules_id` = ' . (int) $id);
    }

    public function getAllApplicable($type)
    {
        $id_shop = (int) Context::getContext()->shop->id;
        $now = date('Y-m-d H:i:s');
        $result = Db::getInstance()->executeS('
		SELECT fsr.*
		FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules` fsr
		LEFT JOIN `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop` fsrs ON (fsr.`fmm_stickers_rules_id` = fsrs.`fmm_stickers_rules_id`)
		WHERE fsr.`status` = 1
		AND fsr.`rule_type` = "' . pSQL($type) . '"
		AND fsrs.`id_shop` = ' . (int) $id_shop . '
            AND
            (
                (fsr.`start_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' >= fsr.`start_date`)
                AND
                (fsr.`expiry_date` = \'0000-00-00 00:00:00\' OR \'' . pSQL($now) . '\' <= fsr.`expiry_date`)
            )');

        return $result;
    }

    public function keyTagExists($tags)
    {
        $stickers = [];
        $banners = [];
        $tags = array_shift($tags);
        $rules = new Rules();
        $get_all_applicable = $rules->getAllApplicable('tag');

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                if (in_array($stick['value'], $tags) && strpos($stick['value'], ',') === false) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                } elseif (strpos($stick['value'], ',') !== false) {
                    $sticks = explode(',', $stick['value']);
                    foreach ($sticks as $_stick) {
                        if (in_array($_stick, $tags)) {
                            array_push($stickers, $stick['sticker_id']);
                            array_push($banners, $stick['stickerbanner_id']);
                        }
                    }
                }
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keyRefExists($ref)
    {
        $stickers = [];
        $banners = [];
        $rules = new Rules();
        $get_all_applicable = $rules->getAllApplicable('reference');

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                if ($stick['value'] == $ref && strpos($stick['value'], ',') === false) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                } elseif (strpos($stick['value'], ',') !== false) {
                    $sticks = explode(',', $stick['value']);
                    foreach ($sticks as $_stick) {
                        if ($_stick == $ref) {
                            array_push($stickers, $stick['sticker_id']);
                            array_push($banners, $stick['stickerbanner_id']);
                        }
                    }
                }
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keyPriceExists($price, $id = null)
    {
        $stickers = [];
        $banners = [];
        $fmm_stickers_rules_id = [];
        $rules = new Rules();
        $get_all_applicable = $rules->getAllApplicable('price_less');

        foreach ($get_all_applicable as $key => $value) {
            $fmm_stickers_rules_id[] = $value['fmm_stickers_rules_id'];
            $excluded_p = Rules::getStickerData($value['fmm_stickers_rules_id']);
            $excluded_p = explode(',', $excluded_p);
            $inarr = in_array($id, $excluded_p);
            if ($inarr) {
                unset($get_all_applicable[$key]);
            }
        }

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                if ($price < $stick['value']) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['sticker_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keyPriceGreaterExists($price, $id = null)
    {
        $fmm_stickers_rules_id = [];
        $stickers = [];
        $banners = [];
        $rules = new Rules();
        $get_all_applicable = $rules->getAllApplicable('price_greater');

        foreach ($get_all_applicable as $key => $value) {
            $fmm_stickers_rules_id[] = $value['fmm_stickers_rules_id'];
            $excluded_p = Rules::getStickerData($value['fmm_stickers_rules_id']);
            $excluded_p = explode(',', $excluded_p);

            $inarr = in_array($id, $excluded_p);
            if ($inarr) {
                unset($get_all_applicable[$key]);
            }
        }

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                if ($price > $stick['value']) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['sticker_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keyNewExists($id = null)
    {
        $stickers = [];
        $banners = [];
        $rules = new Rules();
        $fmm_stickers_rules_id = [];
        $get_all_applicable = $rules->getAllApplicable('new');

        foreach ($get_all_applicable as $key => $value) {
            $fmm_stickers_rules_id[] = $value['fmm_stickers_rules_id'];
            $excluded_p = Rules::getStickerData($value['fmm_stickers_rules_id']);
            $excluded_p = explode(',', $excluded_p);

            $inarr = in_array($id, $excluded_p);
            if ($inarr) {
                unset($get_all_applicable[$key]);
            }
        }

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                array_push($stickers, $stick['sticker_id']);
                array_push($banners, $stick['stickerbanner_id']);
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keySaleExists($id = null)
    {
        $stickers = [];
        $banners = [];
        $rules = new Rules();
        $fmm_stickers_rules_id = [];
        $get_all_applicable = $rules->getAllApplicable('onsale');

        foreach ($get_all_applicable as $key => $value) {
            $fmm_stickers_rules_id[] = $value['fmm_stickers_rules_id'];
            $excluded_p = Rules::getStickerData($value['fmm_stickers_rules_id']);
            $excluded_p = explode(',', $excluded_p);

            $inarr = in_array($id, $excluded_p);
            if ($inarr) {
                unset($get_all_applicable[$key]);
            }
        }

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                array_push($stickers, $stick['sticker_id']);
                array_push($banners, $stick['stickerbanner_id']);
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function getIsCategoryStickerApplicable($id, $id_pro)
    {
        $stickers = [];
        $banners = [];
        $rules = new Rules();
        $get_all_applicable = $rules->getAllApplicable('category');
        $fmm_stickers_rules_id = [];

        foreach ($get_all_applicable as $key => $value) {
            $fmm_stickers_rules_id[] = $value['fmm_stickers_rules_id'];
            $excluded_p = Rules::getStickerData($value['fmm_stickers_rules_id']);
            $excluded_p = explode(',', $excluded_p);
            $inarr = in_array($id_pro, $excluded_p);

            if ($inarr) {
                unset($get_all_applicable[$key]);
            }
        }

        if (!empty($get_all_applicable)) {
            foreach ($get_all_applicable as $stick) {
                $exp_separate = explode(',', $stick['value']);
                foreach ($exp_separate as $key) {
                    if ($key == $id) {
                        array_push($stickers, $stick['sticker_id']);
                        array_push($banners, $stick['stickerbanner_id']);
                    }
                }
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keyBrandsExists($brands, $id)
    {
        $stickers = [];
        $banners = [];

        if (!empty($brands) && $id > 0) {
            foreach ($brands as $stick) {
                $brands_collection = explode(',', $stick['value']);
                if (in_array($id, $brands_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => $stickers,
            'banners' => $banners,
        ];

        return $associativeArray;
    }

    public function keyFeatureExists($rule_feature, $allfeature)
    {
        $stickers = [];
        $banners = [];

        foreach ($rule_feature as $key => $value) {
            $ruless = explode(',', $value['value']);
            foreach ($allfeature as $kky => $vval) {
                $id_feature_value = $vval['id_feature_value'];
                foreach ($ruless as $rulkey => $rulval) {
                    if ($id_feature_value == $rulval) {
                        $stickers[] = $value['sticker_id'];
                        $banners[] = $value['stickerbanner_id'];
                    }
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public function keyConditionExists($conditions, $type)
    {
        if ($type == 'refurbished') {
            $type = 3;
        }

        if ($type == 'new') {
            $type = 1;
        }

        if ($type == 'used') {
            $type = 2;
        }

        $stickers = [];
        $banners = [];
        if (!empty($conditions)) {
            foreach ($conditions as $stick) {
                $condition_collection = explode(',', $stick['value']);

                if (in_array($type, $condition_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public function keyTypeExists($types, $product)
    {
        $stickers = [];
        $banners = [];
        $product_type = $product->getType();

        if (!empty($types)) {
            foreach ($types as $stick) {
                $condition_collection = explode(',', $stick['value']);

                if (in_array($product_type, $condition_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public function keySupplierExists($suppliers, $id)
    {
        $stickers = [];
        $banners = [];

        if (!empty($suppliers) && $id > 0) {
            foreach ($suppliers as $stick) {
                $suppliers_collection = explode(',', $stick['value']);
                if (in_array($id, $suppliers_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public function keyProductsExists($products, $id)
    {
        $stickers = [];
        $banners = [];

        if (!empty($products) && $id > 0) {
            foreach ($products as $stick) {
                $products_collection = explode(',', $stick['value']);
                if (in_array($id, $products_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    // array_push($banners, $stick['stickerbanner_id']);
                    array_push($banners, $stick['sticker_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public function keyProductsExists2($products, $id)
    {
        $stickers = [];
        $banners = [];

        if (!empty($products) && $id > 0) {
            foreach ($products as $stick) {
                $products_collection = explode(',', $stick['value']);
                if (in_array($id, $products_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['sticker_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public function keyBestsellerExists($besties, $id)
    {
        $stickers = [];
        $banners = [];
        if (!empty($besties) && $id > 0) {
            foreach ($besties as $stick) {
                $besties_collection = explode(',', $stick['value']);
                if (in_array($id, $besties_collection)) {
                    array_push($stickers, $stick['sticker_id']);
                    array_push($banners, $stick['stickerbanner_id']);
                }
            }
        }

        $associativeArray = [
            'stickers' => array_unique($stickers),
            'banners' => array_unique($banners),
        ];

        return $associativeArray;
    }

    public static function getRuleByStickerId($id_sticker, $id_shop)
    {
        $result = Db::getInstance()->getRow('select * from `' . _DB_PREFIX_ . 'fmm_stickers_rules` as fsr left join `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop` as fsrs on fsr.`fmm_stickers_rules_id`=fsrs.`fmm_stickers_rules_id` WHERE fsr.`sticker_id`=' . (int) $id_sticker . ' and fsrs.`id_shop`=' . (int) $id_shop);

        return $result;
    }

    public static function getRuleByBannerStickerId($stickersbanners_id, $id_shop)
    {
        $result = Db::getInstance()->getRow('select * from `' . _DB_PREFIX_ . 'fmm_stickers_rules` as fsr left join `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop` as fsrs on fsr.`fmm_stickers_rules_id`=fsrs.`fmm_stickers_rules_id` WHERE fsr.`stickerbanner_id`=' . (int) $stickersbanners_id . ' and fsrs.`id_shop`=' . (int) $id_shop);

        return $result;
    }

    public static function deleteStickerRulesShop($id_sticker_rule)
    {
        $result = Db::getInstance()->execute(
            '
            DELETE FROM `' . _DB_PREFIX_ . 'fmm_stickers_rules_shop`
            WHERE `fmm_stickers_rules_id` = ' . (int) $id_sticker_rule . ' and `id_shop`=' . Context::getContext()->shop->id
        );

        if ($result) {
            return true;
        }

        return false;
    }
}
