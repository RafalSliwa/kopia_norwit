<?php
/**
 * QuotationStatus.
 *
 * @author    Roja45 <support@roja45.com>
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationStatus
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationStatus.
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

class QuotationStatus extends ObjectModel
{
    public static $RCVD = 'RCVD';
    public static $OPEN = 'OPEN';
    public static $SENT = 'SENT';
    public static $ACPT = 'ACPT';
    public static $CART = 'CART';
    public static $ORDR = 'ORDR';
    public static $CLSD = 'CLSD';
    public static $INCP = 'INCP';
    public static $DLTD = 'DLTD';
    public static $CUSR = 'CUSR';
    public static $MESG = 'MESG';
    public static $CCLD = 'CCLD';
    public static $CORD = 'CORD';
    public static $NWQT = 'NWQT';

    /** @var string Name */
    public $status;

    /** @var int Customer Answer Id if using new templates. */
    public $id_roja45_quotation_answer;

    /** @var int Admin Answer Id if using new templates. */
    public $id_roja45_quotation_answer_admin;

    /** @var string Template name if there is any e-mail to send */
    public $answer_template;

    /** @var bool Send an e-mail to customer ? */
    public $send_email;

    /** @var bool Send an e-mail to customer ? */
    public $notify_admin;

    public $code;
    public $display_code;

    /** @var string Display state in the specified color */
    public $color;

    public $unremovable;

    public $customer_pdf_ids;
    public $admin_pdf_ids;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_status',
        'primary' => 'id_roja45_quotation_status',
        'multilang' => true,
        'fields' => array(
            'send_email' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'notify_admin' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'code' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'color' => array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
            'unremovable' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'answer_template' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
            'id_roja45_quotation_answer' => array('type' => self::TYPE_INT),
            'id_roja45_quotation_answer_admin' => array('type' => self::TYPE_INT),
            /* Lang fields */
            'status' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 64
            ),
            'display_code' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true
            ),
            'customer_pdf_ids' => array('type' => self::TYPE_STRING),
            'admin_pdf_ids' => array('type' => self::TYPE_STRING),
        ),
    );

    public function add($auto_date = true, $null_values = false)
    {
        if (QuotationStatus::getQuotationStatusByType($this->code)) {
            throw new PrestaShopException('Quotation status code is not unique.');
        }
        parent::add($auto_date, $null_values);
        $id = Db::getInstance()->Insert_ID();
        Configuration::updateGlobalValue('ROJA45_QUOTATIONSPRO_STATUS_'.$this->code, $id);
        return true;
    }

    public function delete()
    {
        Configuration::deleteByName('ROJA45_QUOTATIONSPRO_STATUS_'.$this->code);
        return parent::delete();
    }

    /**
     * Get all available order statuses.
     *
     * @param int $id_lang Language id for status name
     *
     * @return array Order statuses
     */
    public static function getQuotationStates($id_lang)
    {
        $cache_id = 'QuotationStatus::getQuotationStates'.(int) $id_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('roja45_quotationspro_status', 'qs');
            $sql->leftJoin(
                'roja45_quotationspro_status_lang',
                'qsl',
                'qs.`id_roja45_quotation_status` = qsl.`id_roja45_quotation_status` AND qsl.`id_lang` = '.(int) $id_lang
            );
            $sql->orderBy('qs.`id_roja45_quotation_status` ASC');
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            Cache::store($cache_id, $result);
        }

        return Cache::retrieve($cache_id);
    }


    public static function getQuotationStatusByType($status_code)
    {
        $sql = new DbQuery();
        $sql->select('qs.id_roja45_quotation_status');
        $sql->from('roja45_quotationspro_status', 'qs');
        $sql->where('qs.code="'.pSQL($status_code).'"');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function isRemovable()
    {
        return !($this->unremovable);
    }
}
