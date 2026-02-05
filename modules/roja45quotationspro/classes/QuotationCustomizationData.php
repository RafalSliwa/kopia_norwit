<?php
/**
 * QuotationCustomizationData.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationCustomizationData
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationCustomizationData.
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

class QuotationCustomizationData extends ObjectModel
{
    public $id_roja45_quotation_customization;
    public $type;
    public $index;
    public $value;
    public $price;
    public $weight;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_customizationdata',
        'primary' => 'id_roja45_quotation_customizationdata',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation_customization' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'type' => array(
                'type' => self::TYPE_BOOL,
                'required' => true
            ),
            'index' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'value' => array(
                'type' => self::TYPE_STRING,
                'required' => true
            ),
            'price' => array(
                'type' => self::TYPE_FLOAT,
                'required' => true
            ),
            'weight' => array(
                'type' => self::TYPE_FLOAT,
                'required' => true
            ),
        ),
    );
}
