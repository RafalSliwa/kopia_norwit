<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class AdminContactFormUltimateImportExportController extends ModuleAdminController
{
    public $_html;

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function renderList()
    {
        if (!$this->module->active) {
            return $this->module->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
        }
        $errors = array();
        if (Tools::isSubmit('etsCfuImportContactSubmit')) {
            $this->module->processImport();
            $errors = $this->module->getErrors();
        }
        $this->context->smarty->assign(array(
            'controller' => Tools::getValue('controller'),
            'link' => $this->context->link,
            'errors' => $errors?$this->module->displayError($errors) : false,
        ));
        $this->_html .= $this->module->display($this->module->getLocalPath(), 'form_import.tpl');
        return $this->_html;
    }
}