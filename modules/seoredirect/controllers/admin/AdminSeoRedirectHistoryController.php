<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
 * @copyright 2010-2024 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
require_once _PS_MODULE_DIR_ . 'seoredirect/seoredirect.php';

class AdminSeoRedirectHistoryController extends ModuleAdminController
{
    protected $position_identifier = 'id_seor_history';

    public function __construct()
    {
        $this->table = 'seor_history';
        $this->className = 'seoRedirectHistory';
        $this->lang = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->getTranslator()->trans('Delete selected', [], 'Modules.Seoredirect.History'),
                'confirm' => $this->module->getTranslator()->trans('Delete selected items?', [], 'Modules.Seoredirect.History')
            )
        );
        $this->bootstrap = true;
        $this->_orderBy = 'id_seor_history';
        $this->_orderWay = 'DESC';
        $this->fields_list = array(
            'id_seor_history' => array(
                'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Seoredirect.History'),
                'align' => 'left',
                'orderby' => true,
                'width' => 20
            ),
            'url' => array(
                'title' => $this->module->getTranslator()->trans('URL', [], 'Modules.Seoredirect.History'),
                'align' => 'left',
                'orderby' => true,
                'width' => 170
            ),
            'new' => array(
                'title' => $this->module->getTranslator()->trans('Redirected to', [], 'Modules.Seoredirect.History'),
                'align' => 'left',
                'orderby' => true,
                'width' => 170
            ),
            'date_add' => array(
                'title' => $this->module->getTranslator()->trans('Redirection date',[], 'Modules.Seoredirect.History'),
                'align' => 'left',
                'orderby' => true,
                'width' => 170
            ),
        );
    }


    public function renderList()
    {
        $this->context->controller->informations[] = $this->module->getTranslator()->trans('This section contain logs of all redirections executed by the module. Log by default is disabled. Module will start to store entries here when you will activate "Enable logs" option in the settings section of the module.', [], 'Modules.Seoredirect.History');
        $this->initToolbar();
        return parent::renderList();
    }

    public function init()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive())
        {
            $this->_where = 'AND a.id_shop=' . Context::getContext()->shop->id;
        }
        parent::init();
    }

}