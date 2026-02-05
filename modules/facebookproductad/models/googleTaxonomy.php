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
class googleTaxonomy extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var string value * */
    public $value;

    /** @var string lang * */
    public $lang;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_taxonomy',
        'primary' => 'id_taxonomy',
        'fields' => [
            'value' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'lang' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];

    /**
     * clean value for taxonomies
     *
     * @param string $iso_lang
     *
     * @return mixed
     */
    public static function clean($iso_lang)
    {
        return \Db::getInstance()->delete('fpa_taxonomy', 'lang="' . \pSQL($iso_lang) . '"');
    }

    /**
     * add the taxonomies values
     *
     * @param string $iso_lang
     * @param string $value
     *
     * @return mixed
     */
    public static function addTaxonomy($value, $iso_lang)
    {
        $taxonomy = new googleTaxonomy();
        $taxonomy->value = (string) $value;
        $taxonomy->lang = (string) $iso_lang;

        return $taxonomy->add();
    }

    /**
     * method checks if the current country has already been updated
     *
     * @param string $iso_code
     *
     * @return mixed
     */
    public static function checkTaxonomyUpdate($iso_code)
    {
        $query = new \DbQuery();
        $query->select('COUNT(`id_taxonomy`) as count');
        $query->from('fpa_taxonomy', 'ft');
        $query->where('ft.lang="' . \pSQL($iso_code) . '"');

        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

        return $data['count'] > 1 ? true : false;
    }

    /**
     * method delete taxonomy
     *
     * @param string $iso_code
     * @param array $words
     *
     * @return array
     */
    public static function autocompleteSearch($iso_code, array $words)
    {
        $query = new \DbQuery();
        $query->select('value');
        $query->from('fpa_taxonomy', 'ft');
        $query->where('ft.lang="' . \pSQL($iso_code) . '"');

        foreach ($words as $string) {
            $query->where('ft.value LIKE "%' . \pSQL($string) . '%"');
        }

        return \Db::getInstance()->ExecuteS($query);
    }
}
