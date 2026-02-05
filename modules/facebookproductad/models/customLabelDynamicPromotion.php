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
class customLabelDynamicPromotion extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var string start_date * */
    public $start_date;

    /** @var string start_date * */
    public $end_date;

    /** @var int id_product * */
    public $id_product;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_tags_dynamic_promotion',
        'primary' => 'id_tag',
        'fields' => [
            'id_tag' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'start_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
        ],
    ];

    /**
     * insert dynamic product promotion
     *
     * @param int $id_tag
     * @param string $start_date
     * @param string $end_date
     * @param int $id_product
     *
     * @return mixed
     */
    public static function insertDynamicPromotion($id_tag, $start_date = null, $end_date = null, $id_product = null)
    {
        $tag = new customLabelDynamicPromotion();

        $tag->id_tag = (int) $id_tag;
        $tag->start_date = (string) $start_date;
        $tag->end_date = (string) $end_date;
        $tag->id_product = (int) $id_product;

        return $tag->add();
    }

    /**
     * clean value for dynamic promotion
     *
     * @param int $id_tag
     *
     * @return mixed
     */
    public static function deleteDynamicPromotion($id_tag)
    {
        return \Db::getInstance()->delete('fpa_tags_dynamic_promotion', 'id_tag=' . (int) $id_tag);
    }

    /**
     * record for promotion cl
     *
     * @param int $id_tag
     *
     * @return mixed
     */
    public static function getDynamicLastDynamicPromotion($id_tag)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_tags_dynamic_promotion', 'ftdp');
        $query->where('ftdp.id_tag=' . (int) $id_tag);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
