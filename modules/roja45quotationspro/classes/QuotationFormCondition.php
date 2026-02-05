<?php
/**
 * QuotationFormCondition
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationFormCondition
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationFormCondition
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QuotationFormCondition extends ObjectModel
{
    public $id_roja45_quotation_formconditiongroup;
    public $type;
    public $value;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_formcondition',
        'primary' => 'id_roja45_quotation_form_condition',
        'fields' => array(
            'id_roja45_quotation_formconditiongroup' => array('type' => self::TYPE_INT, 'required' => true),
            'type' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'value' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    public static function getConditions($id_roja45_quotation_form)
    {
        $sql = new DbQuery();
        $sql->select('dg.*, dc.*');
        $sql->from('roja45_quotationspro_formconditiongroup', 'dg');
        $sql->leftJoin(
            'roja45_quotationspro_formcondition',
            'dc',
            'dc.id_roja45_quotation_formconditiongroup = dg.id_roja45_quotation_formconditiongroup'
        );
        $sql->where('id_roja45_quotation_form=' . (int) $id_roja45_quotation_form);
        return Db::getInstance()->executeS($sql);
    }

    public static function deleteConditions($id_roja45_quotation_form)
    {
        $sql = new DbQuery();
        $sql->select('dg.id_roja45_quotation_formconditiongroup');
        $sql->from('roja45_quotationspro_formconditiongroup', 'dg');
        $sql->where('id_roja45_quotation_form=' . (int)$id_roja45_quotation_form);
        $ids_discount_condition_group = Db::getInstance()->executeS($sql);

        if ($ids_discount_condition_group) {
            foreach ($ids_discount_condition_group as $row) {
                Db::getInstance()->delete(
                    'roja45_quotationspro_formconditiongroup',
                    'id_roja45_quotation_formconditiongroup=' .
                    (int)$row['id_roja45_quotation_formconditiongroup']
                );
                Db::getInstance()->delete(
                    'roja45_quotationspro_formcondition',
                    'id_roja45_quotation_formconditiongroup=' .
                    (int)$row['id_roja45_quotation_formconditiongroup']
                );
            }
        }
        return true;
    }
}
