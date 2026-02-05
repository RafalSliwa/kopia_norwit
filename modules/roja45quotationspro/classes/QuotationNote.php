<?php
/**
 * QuotationNote.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationNote
 *
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationNote.
 *
 * @category  Class
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

class QuotationNote extends ObjectModel
{
    public $id_roja45_quotation_note;
    public $id_roja45_quotation;
    public $note;
    public $added;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_note',
        'primary' => 'id_roja45_quotation_note',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'note' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true),
            'added' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    public static function getList($id_quotation)
    {
        $sql = '
            SELECT * 
            FROM `'._DB_PREFIX_.'roja45_quotationspro_note` 
            WHERE `id_roja45_quotation` = '.(int) $id_quotation;
        return Db::getInstance()->executeS($sql);
    }
}
