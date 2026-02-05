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
class advancedExclusion extends \ObjectModel
{
    /** @var int id * */
    public $id;

    /** @var int status * */
    public $status;

    /** @var int id_shop * */
    public $id_shop;

    /** @var string name * */
    public $name;

    /** @var string type * */
    public $type;

    /** @var string exclusion_value * */
    public $exclusion_value;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'fpa_advanced_exclusion',
        'primary' => 'id',
        'fields' => [
            'status' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'type' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
            'exclusion_value' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true],
        ],
    ];

    /**
     *  add a rule
     *
     * @param int $status
     * @param int $id_shop
     * @param string $rule_name
     * @param string $type
     * @param string $data
     *
     * @return mixed
     */
    public static function addRule($status, $id_shop, $rule_name, $type, $data)
    {
        $rule = new advancedExclusion();
        $rule->status = (int) $status;
        $rule->id_shop = (int) $id_shop;
        $rule->name = (string) $rule_name;
        $rule->type = (string) $type;
        $rule->exclusion_value = $data;

        return $rule->add();
    }

    /**
     *  add a rule
     *
     * @param int $status
     * @param int $id_shop
     * @param string $rule_name
     * @param string $type
     * @param string $data
     * @param int $id
     *
     * @return mixed
     */
    public static function updateRule($status, $id_shop, $rule_name, $type, $data, $id)
    {
        $rule = new advancedExclusion($id);
        $rule->status = (int) $status;
        $rule->id_shop = (int) $id_shop;
        $rule->name = (string) $rule_name;
        $rule->type = (string) $type;
        $rule->exclusion_value = $data;
        $rule->update();
    }

    /**
     *  update a rule status
     *
     * @param int $id
     * @param string $type
     * @param int $status
     *
     * @return mixed
     */
    public static function updateRuleStatus($id, $type, $status)
    {
        // Use case activate/deactivate in bulk action
        $rules = [];
        if ($type == 'bulk') {
            $rules = explode(',', (string)$id);

            if (!empty($rules)) {
                foreach ($rules as $id_rule) {
                    $rule = new advancedExclusion((int)$id_rule);
                    $rule->status = (int) $status;
                    $rule->update();
                }
            }
        } else {
            $rule = new advancedExclusion($id);
            $rule->status = (int) $status;
            $rule->update();
        }
    }

    /**
     * method returns the exclusion rules
     *
     * @return array
     */
    public static function getRules()
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_advanced_exclusion', 'fae');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
    }

    /**
     * method returns the exclusion rules
     *
     * @param int $id_rule
     *
     * @return array
     */
    public static function getRulesById($id_rule)
    {
        $query = new \DbQuery();
        $query->select('*');
        $query->from('fpa_advanced_exclusion', 'fae');
        $query->where('fae.`id` = ' . (int) $id_rule);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }

    /**
     *  delete a rule status
     *
     * @param string $id
     * @param string $type
     *
     * @return mixed
     */
    public static function deleteExclusionRule($id, $type)
    {
        // Use case activate/deactivate in bulk action
        $rules = [];
        if ($type == 'bulk') {
            $rules = explode(',', $id);

            if (!empty($rules)) {
                foreach ($rules as $id_rule) {
                    \Db::getInstance()->delete('fpa_advanced_exclusion', 'id=' . (int) $id_rule);
                }
            }
        } else {
            \Db::getInstance()->delete('fpa_advanced_exclusion', 'id=' . (int) $id);
        }
    }

    /**
     * method get the last rule id
     *
     * @return mixed
     */
    public static function getLastRuleId()
    {
        $query = new \DbQuery();
        $query->select('MAX(id) as last_id');
        $query->from('fpa_advanced_exclusion', 'fae');

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
    }
}
