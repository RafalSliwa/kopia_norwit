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
class customLabelDynamicProducts extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var int id_product * */
    public $id_product;

    /** @var string from_date * */
    public $product_name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_tags_products',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'product_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
        ],
    ];

    /**
     * insert add specific product id for one product
     *
     * @param int $id_tag
     * @param int $id_product
     * @param string $product_name
     *
     * @return mixed
     */
    public static function insertProductTag($id_tag, $id_product, $product_name)
    {
        $tag = new customLabelDynamicProducts();
        $tag->id_tag = (int) $id_tag;
        $tag->id_product = (int) $id_product;
        $tag->product_name = (string) $product_name;

        return $tag->add();
    }

    /**
     * delete Product tag add save for one custom_label_id
     *
     * @param int $id_tag
     *
     * @return mixed
     */
    public static function deleteProductTag($id_tag)
    {
        return \Db::getInstance()->delete('fpa_tags_products', 'id_tag=' . (int) $id_tag);
    }

    /**
     * return id_product for the tag
     *
     * @param int $id_tag
     *
     * @return mixed
     */
    public static function getFpaTagsProduct($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_tags_products', 'ftp');
        $query->where('ftp.id_tag=' . (int) $id_tag);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }
}
