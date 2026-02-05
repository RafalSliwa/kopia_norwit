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
use FacebookProductAd\Configuration\moduleConfiguration;

class customLabelTags extends \ObjectModel
{
    /** @var int id * */
    public $id_tag;

    /** @var int id_shop * */
    public $id_shop;

    /** @var string name * */
    public $name;

    /** @var string type * */
    public $type;

    /** @var int status * */
    public $active;

    /** @var int status * */
    public $position;

    /** @var string end date * */
    public $end_date;

    /** @var string custom_label_set_postion * */
    public $custom_label_set_postion;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_tags',
        'primary' => 'id_tag',
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => false],
            'end_date' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => false],
            'custom_label_set_postion' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];

    /**
     * insert a specific tag
     *
     * @param int $id_shop
     * @param string $name
     * @param string $type
     * @param bool $active
     * @param bool $custom_label_set_postion
     * @param int $position
     * @param string $end_date
     *
     * @return mixed
     */
    public static function addTag($id_shop, $name, $type, $custom_label_set_postion, $active, $position = null, $end_date = null)
    {
        $tag = new customLabelTags();
        $tag->id_shop = (int) $id_shop;
        $tag->name = (string) $name;
        $tag->type = (string) $type;
        $tag->active = (int) $active;
        $tag->custom_label_set_postion = (string) $custom_label_set_postion;

        if (!empty($position)) {
            $tag->position = (int) $position;
        }

        if (!empty($end_date)) {
            $tag->end_date = (string) $end_date;
        }

        if ($tag->add()) {
            return (int) $tag->id;
        } else {
            return false;
        }
    }

    /**
     * returns specific categories or brands or suppliers for one tag
     *
     * @param int $id_shop
     * @param int $id_tag
     * @param string $table_type
     * @param string $field
     *
     * @return mixed
     */
    public static function getTags($id_shop = null, $id_tag = null, $table_type = null, $field = null)
    {
        $output_data = [];
        $query = new \DbQuery();
        $query->select('*');

        if (empty($table_type)) {
            $query->from('fpa_tags');
        } else {
            $query->from('fpa_tags_' . $table_type);
        }
        if (!empty($id_shop)) {
            $query->where('id_shop=' . (int) $id_shop);
        }
        if (!empty($id_tag)) {
            $query->where('id_tag=' . (int) $id_tag);
        }

        if (empty($id_tag)) {
            $query->orderBy('position ASC');
        }

        $data = \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        if (!empty($data) && $field !== null) {
            foreach ($data as $aCat) {
                $output_data[] = $aCat['id_' . $field];
            }
        } else {
            $output_data = $data;
        }

        return $output_data;
    }

    /**
     * get the end date for a tag
     *
     * @param $id_shop
     *
     * @return array
     */
    public static function getTagDate($id_shop)
    {
        $query = new \DbQuery();
        $query->select('id_tag, end_date');
        $query->from('fpa_tags', 'ft');
        $query->where('ft.id_shop=' . (int) $id_shop);
        $query->where('end_date != "00-00-0000"');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }

    /**
     * get the active tags
     *
     * @param $id_shop
     *
     * @return array
     */
    public static function getActive($id_shop)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_tags', 'ft');
        $query->where('id_shop=' . (int) $id_shop);
        $query->where('active = 1');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }

    /**
     * update a specific tag
     *
     * @param int $id_tag
     * @param string $name
     * @param string $type
     * @param int $active
     * @param string $custom_label_set_postion
     * @param int $position
     * @param string $date_end
     *
     * @return mixed
     */
    public static function updateTag($id_tag, $name, $type, $active, $custom_label_set_postion, $position = null, $date_end = null)
    {
        $tag = new customLabelTags($id_tag);
        $tag->name = \pSQL($name);
        $tag->type = \pSQL($type);
        $tag->active = $active;
        $tag->custom_label_set_postion = $custom_label_set_postion;

        if (!empty($position)) {
            $tag->position = (int) $position;
        }

        if (!empty($date_end)) {
            $tag->end_date = \pSQL($date_end);
        }

        return $tag->update();
    }

    /**
     * update a specific tag
     *
     * @param int $id_tag
     * @param int $status
     *
     * @return mixed
     */
    public static function updateTagStatus($id_tag, $status)
    {
        $tag = new customLabelTags($id_tag);
        $tag->active = $status;

        return $tag->update();
    }

    /**
     * update a tag date
     *
     * @param int $id_tag
     * @param int $status
     * @param int $position
     *
     * @return mixed
     */
    public static function updateProcessDate($id_tag, $status, $position)
    {
        $tag = new customLabelTags($id_tag);
        $tag->active = $status;
        $tag->position = $position;

        return $tag->update();
    }

    /**
     * get tag position
     *
     * @param int $id_tag
     */
    public static function getTagPosition($id_tag)
    {
        $query = new \DbQuery();
        $query->select('position');
        $query->from('fpa_tags', 'ft');
        $query->where('id_tag=' . (int) $id_tag);
        $query->where('active = 1');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    /**
     * insert categories / brands / manufacturers for a specific tag
     *
     * @param int $id_tag
     * @param int $id_category
     * @param string $table_name
     * @param string $field
     *
     * @return mixed
     */
    public static function inserCatTag($id_tag, $id_category, $table_name, $field)
    {
        // todo rewrite later
        \Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . \Tools::strtolower(moduleConfiguration::FPA_MODULE_NAME) . '_tags_' . $table_name . '` (`id_tag`, `id_' . pSQL($field) . '`) VALUES (' . (int) $id_tag . ', ' . (int) $id_category . ')');
    }

    /**
     * method delete a specific tag
     *
     * @param int $id_tag
     * @param array $label_list
     * @param array $custom_label_type
     *
     * @return mixed
     */
    public static function deleteTag($id_tag, array $label_list = null, $custom_label_type = null)
    {
        try {
            if (\Db::getInstance()->delete('fpa_tags', 'id_tag=' . (int) $id_tag)) {
                if (!empty($label_list)) {
                    foreach ($label_list as $table_name => $type) {
                        \Db::getInstance()->delete('fpa_tags_' . $table_name, 'id_tag=' . (int) $id_tag);
                    }
                }
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
        }
    }

    /**
     * delete a specific related categories / brands / manufacturers tag
     *
     * @param int $id_tag
     * @param string $table_type
     *
     * @return mixed
     */
    public static function deleteCatTag($id_tag, $table_type)
    {
        // Todo rewrite later
        return \Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . \Tools::strtolower(moduleConfiguration::FPA_MODULE_NAME) . '_tags_' . pSQL($table_type) . '` WHERE `id_tag` = ' . (int) $id_tag);
    }

    /**
     * update the position between 2 tags
     *
     * @param int $id_tag
     * @param int $position
     * @param int $id_shop
     *
     * @return mixed
     */
    public static function updatePositionTag($id_tag, $position, $id_shop)
    {
        $tag = new customLabelTags($id_tag);
        $tag->position = $position;
        $tag->id_shop = $id_shop;

        return $tag->update();
    }

    /**
     * get last id
     *
     * @return mixed
     */
    public static function getLastId()
    {
        $query = new \DbQuery();
        $query->select('position');
        $query->from('fpa_tags', 'ft');
        $query->orderBy('position DESC');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
