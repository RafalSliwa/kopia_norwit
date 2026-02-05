<?php
/**
 * QuotationConversation.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationConversation
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationConversation.
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

class QuotationConversation extends ObjectModel
{
    public $id_roja45_quotation_conversation;
    public $id_roja45_quotation;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_conversation',
        'primary' => 'id_roja45_quotation_conversation',
        'multilang' => false,
        'fields' => array(
            'id_roja45_quotation' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
        ),
    );
}
