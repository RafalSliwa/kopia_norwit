<?php
/**
 * QuotationDocumentsController
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  QuotationDocumentsController
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * QuotationDocumentsController
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

class QuotationDocumentsController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();

        $this->override_folder = 'quotation_documents/';
        $this->tpl_folder = 'quotation_documents/';
        $this->bootstrap = true;
        $this->table = 'roja45_quotationspro_document';
        $this->identifier = 'id_roja45_document';
        $this->submit_action = 'submitAdd' . $this->table;
        $this->show_cancel_button = true;
        $this->className = 'QuotationDocument';
        $this->action = 'QuotationDocument';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'QuotationDocuments'),
                'confirm' => $this->l('Delete selected items?', 'QuotationDocuments'),
            ),
        );

        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os',
        );
        $this->toolbar_title = $this->l('Quotation Documents', 'QuotationDocuments');

        $this->_defaultOrderBy = $this->identifier = 'id_roja45_document';
        $this->list_id = 'roja45_quotationspro_document';
        $this->deleted = false;
        $this->_orderBy = null;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected', 'QuotationDocuments'),
                'confirm' => $this->l('Delete selected documents?', 'QuotationDocuments'),
                'icon' => 'icon-trash',
            ),
        );

        $this->fields_list = array(
            'id_roja45_document' => array(
                'title' => $this->l('Id', 'QuotationDocuments'),
                'align' => 'text-center',
                'class' => 'fixed-width-sm',
            ),
            'display_name' => array(
                'title' => $this->l('Name', 'QuotationDocuments'),
                'width' => 'auto',
            ),
        );

        Shop::setContext(Shop::CONTEXT_ALL);
        $this->tabAccess = Profile::getProfileAccess(
            $this->context->employee->id_profile,
            Tab::getIdFromClassName('QuotationDocuments')
        );
    }

    public function childValidation()
    {
        $max_file_size = RojaFortyFiveQuotationsProCore::getBytesValue(ini_get('upload_max_filesize'));
        foreach (Language::getLanguages(false) as $lang) {
            if (isset($_FILES['document_' . $lang['id_lang']])
                && isset($_FILES['document_' . $lang['id_lang']]['tmp_name'])
                && !empty($_FILES['document_' . $lang['id_lang']]['tmp_name'])) {
                if ($_FILES['document_' . $lang['id_lang']]['size']
                    > $max_file_size) {
                    $this->errors[] = 'File too large: ' . $_FILES['document_' . $lang['id_lang']]['size'];
                    return false;
                } else {
                    if (!RojaFortyFiveQuotationsProCore::isValidFile(
                        $_FILES['document_' . $lang['id_lang']]['tmp_name'],
                        array('application/pdf')
                    )) {
                        $this->errors[] = 'File type not allowed: ' . $_FILES['document_' . $lang['id_lang']]['type'];
                        return false;
                    }
                }
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $this->deleted = false;
            return parent::postProcess();
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            $document = new QuotationDocument(
                Tools::getValue('id_roja45_document')
            );
            if (!$document->isRemovable()) {
                $this->errors[] = $this->l('For security reasons, you cannot delete this document.', 'QuotationDocuments');
            } else {
                return parent::postProcess();
            }
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            return parent::postProcess();
        } else {
            return parent::postProcess();
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_product_document'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add new document', 'QuotationDocuments'),
                'icon' => 'process-icon-new',
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Document', 'QuotationDocuments'),
                'icon' => 'icon-time',
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_shop',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name', 'QuotationDocuments'),
                    'name' => 'display_name',
                    'required' => true,
                    'lang' => true,
                ),
                array(
                    'type' => 'file_lang',
                    'label' => $this->l('Document', 'QuotationDocuments'),
                    'name' => 'document',
                    'required' => true,
                    'accept' => '.pdf',
                    'lang' => true,
                    'desc' => sprintf(
                        $this->l('Maximum document size: %s.', 'QuotationDocuments'),
                        ini_get('upload_max_filesize')
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save', 'QuotationDocuments'),
            ),
        );

        return parent::renderForm();
    }

    public function getFieldsValue($obj)
    {
        $fields = parent::getFieldsValue($obj);
        $languages = Language::getLanguages(false);
        $fields['id_shop'] = $this->context->shop->id;

        if (Validate::isLoadedObject($obj)) {
            foreach ($languages as $lang) {
                $fields['display_name'][$lang['id_lang']] = $obj->display_name[$lang['id_lang']];
                $fields['file_name'][$lang['id_lang']] = $obj->file_name[$lang['id_lang']];
                $fields['internal_name'][$lang['id_lang']] = $obj->internal_name[$lang['id_lang']];
            }
        }

        return $fields;
    }

    public function ajaxProcessenableCustomerAccountroja45ProductrentalProductDocument()
    {
        $id = (int) Tools::getValue($this->identifier);
        $sql = '
            UPDATE ' . _DB_PREFIX_ . 'roja45_quotationspro_document
            SET `customer_account`= NOT `customer_account`
            WHERE ' . $this->identifier . '=' . $id;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            $json = json_encode(array(
                'success' => 1,
                'text' => $this->l('The status has been updated successfully.', 'QuotationDocuments'),
            ));
            die($json);
        } else {
            $json = json_encode(array(
                'success' => 0,
                'text' => $this->l('An error occurred while updating this status.', 'QuotationDocuments'),
            ));
            die($json);
        }
    }
}
