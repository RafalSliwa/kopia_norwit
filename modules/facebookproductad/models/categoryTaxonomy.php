<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}
class categoryTaxonomy extends \ObjectModel
{
    /** @var int id_category * */
    public $id_category;

    /** @var int id_shop * */
    public $id_shop;

    /** @var string value * */
    public $txt_taxonomy;

    /** @var string lang * */
    public $lang;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_taxonomy_categories',
        'primary' => 'id_category',
        'fields' => [
            'id_category' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'txt_taxonomy' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'lang' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];

    /**
     * method returns facebook's categories
     *
     * @param int $id_shop
     * @param int $id_cat
     * @param string $iso_lang
     *
     * @return array
     */
    public static function getFacebookCategories($id_shop, $id_cat, $iso_lang)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_taxonomy_categories', 'ftc');
        $query->where('ftc.id_shop=' . (int) $id_shop);
        $query->where('ftc.id_category=' . (int) $id_cat);
        $query->where('ftc.lang="' . \pSQL($iso_lang) . '"');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    /**
     * method delete facebook categories
     *
     * @param int $id_shop
     * @param string $iso_lang
     *
     * @return mixed
     */
    public static function deleteFacebookCategory($id_shop, $iso_lang)
    {
        return \Db::getInstance()->delete('fpa_taxonomy_categories', 'lang="' . \pSQL($iso_lang) . '" AND id_shop =' . (int) $id_shop);
    }

    /**
     * method add facebook categories
     *
     * @param int $id_shop
     * @param int $id_category
     * @param string $facebook_category
     * @param string $iso_lang
     *
     * @return mixed
     */
    public static function insertFacebookCategory($id_shop, $id_category, $facebook_category, $iso_lang)
    {
        $taxonomy = new categoryTaxonomy();
        $taxonomy->id_shop = (int) $id_shop;
        $taxonomy->id_category = (int) $id_category;
        $taxonomy->txt_taxonomy = json_encode($facebook_category);
        $taxonomy->lang = (string) $iso_lang;

        return $taxonomy->add();
    }

    /**
     * method method check if already has taxonomy
     *
     * @param int $id_shop
     *
     * @return mixed
     */
    public static function hasTaxonomies($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_taxonomy_categories', 'gtc');
        $query->where('gtc.id_shop=' . (int) $id_shop);

        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        return empty($data) ? false : true;
    }

    /**
     * method method get all saved taxonomies for one shop
     *
     * @param int $id_shop
     *
     * @return array
     */
    public static function getAllTaxonomies($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_taxonomy_categories', 'gtc');
        $query->where('gtc.id_shop=' . (int) $id_shop);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }

    /**
     * method delete google categories
     *
     * @param int $id_shop
     * @param string $iso_lang
     *
     * @return mixed
     */
    public static function deleteGoogleCategory($id_shop, $iso_lang)
    {
        return \Db::getInstance()->delete('fpa_taxonomy_categories', 'lang="' . \pSQL($iso_lang) . '" AND id_shop =' . (int) $id_shop);
    }

    /**
     * method delete google categories
     *
     * @param int $id_shop
     * @param string $iso_lang
     * @param string $id_category
     *
     * @return mixed
     */
    public static function deleteSpecificGoogleCategory($id_shop, $iso_lang, $id_category)
    {
        return \Db::getInstance()->delete('fpa_taxonomy_categories', 'lang="' . \pSQL($iso_lang) . '" AND id_shop =' . (int) $id_shop . ' AND id_category =' . (int) $id_category);
    }

    /**
     * method add google categories
     *
     * @param int $id_shop
     * @param int $id_category
     * @param string $google_category
     * @param string $iso_lang
     *
     * @return mixed
     */
    public static function insertGoogleCategory($id_shop, $id_category, $google_category, $iso_lang)
    {
        $taxonomy = new categoryTaxonomy();
        $taxonomy->id_shop = (int) $id_shop;
        $taxonomy->id_category = (int) $id_category;
        $taxonomy->txt_taxonomy = json_encode($google_category);
        $taxonomy->lang = \pSQL($iso_lang);

        return $taxonomy->add();
    }
}
