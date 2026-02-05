<?php
/**
 * QuotationDocument
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationDocument
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationDocument
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

class QuotationDocument extends ObjectModel
{
    public $id_roja45_document;
    public $id_shop;
    public $enabled;
    public $file_type;
    public $display_name;
    public $file_name;
    public $internal_name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'roja45_quotationspro_document',
        'primary' => 'id_roja45_document',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'enabled' => array('type' => self::TYPE_BOOL),
            'file_type' => array('type' => self::TYPE_STRING, 'lang' => true),
            'display_name' => array('type' => self::TYPE_STRING, 'lang' => true),
            'file_name' => array('type' => self::TYPE_STRING, 'lang' => true),
            'internal_name' => array('type' => self::TYPE_STRING, 'lang' => true),
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function isRemovable()
    {
        return true;
    }

    public function delete()
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'roja45_quotationspro_quotation_document 
            WHERE id_roja45_document='.(int) $this->id_roja45_document;
        Db::getInstance()->execute($sql);
        foreach (Language::getLanguages(false) as $lang) {
            if (!empty($this->internal_name[$lang['id_lang']])) {
                $file = _PS_DOWNLOAD_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$this->internal_name[$lang['id_lang']];
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
        return parent::delete();
    }
    public function getFieldsLang()
    {
        $fields = parent::getFieldsLang();
        if (!file_exists(_PS_DOWNLOAD_DIR_.'roja45quotationspro')) {
            mkdir(_PS_DOWNLOAD_DIR_.'roja45quotationspro');
        }
        foreach (Language::getLanguages(false) as $lang) {
            if (isset($_FILES['document_'.$lang['id_lang']])
                && isset($_FILES['document_'.$lang['id_lang']]['tmp_name'])
                && !empty($_FILES['document_'.$lang['id_lang']]['tmp_name'])) {
                $ext = Tools::substr(
                    $_FILES['document_'.$lang['id_lang']]['name'],
                    strrpos($_FILES['document_'.$lang['id_lang']]['name'], '.') + 1
                );
                $file_name = md5($_FILES['document_'.$lang['id_lang']]['name']).'.'.$ext;

                if (!move_uploaded_file(
                    $_FILES['document_'.$lang['id_lang']]['tmp_name'],
                    _PS_DOWNLOAD_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$file_name
                )) {
                    return $this->displayError(
                        $this->l('An error occurred while attempting to upload the file.')
                    );
                } else {
                    $fields[$lang['id_lang']]['file_type'] = $ext;
                    $fields[$lang['id_lang']]['internal_name'] = $file_name;
                    $fields[$lang['id_lang']]['file_name'] = $_FILES['document_'.$lang['id_lang']]['name'];
                }
            }
        }
        return $fields;
    }

    public static function getDocuments($id_lang = null, $id_shop = null)
    {
        if (!$id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        if (!$id_shop) {
            $id_shop = Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('roja45_quotationspro_document', 'd');
        $sql->where('d.id_shop=' . (int) $id_shop);
        $sql->leftJoin(
            'roja45_quotationspro_document_lang',
            'dl',
            'd.id_roja45_document = dl.id_roja45_document AND dl.id_lang = ' . (int) $id_lang
        );

        if ($documents = Db::getInstance()->executeS($sql)) {
            foreach ($documents as &$document) {
                $document['link'] = _PS_DOWNLOAD_DIR_.'roja45quotationspro'.DIRECTORY_SEPARATOR.$document['internal_name'];
            }
        }
        return $documents;
    }
}
