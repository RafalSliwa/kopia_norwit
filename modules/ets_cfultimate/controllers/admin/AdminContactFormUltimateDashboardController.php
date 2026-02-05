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

require_once(dirname(__FILE__) . '/AdminModuleAdapterController.php');

class AdminContactFormUltimateDashboardController extends AdminModuleAdapterController
{
    public $_html;
    public $filters = array();

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->filters = array(
            'mon' => array(
                'label' => $this->l('This month'),
                'id' => 'month',
            ),
            'yea' => array(
                'label' => $this->l('This year'),
                'id' => 'year',
            ),
            'all' => array(
                'label' => $this->l('All time'),
                'id' => 'all',
            ),
        );
        $this->charts = array(
            'mes' => array(
                'label' => $this->l('Messages'),
            ),
            'vie' => array(
                'label' => $this->l('Views'),
            ),
            'rep' => array(
                'label' => $this->l('Replies'),
            ),
            'use' => array(
                'label' => $this->l('Users'),
            ),
        );
    }

    public function initContent()
    {
        parent::initContent();
        $this->getCharts(Tools::getValue('ajax'));
    }

    public function renderList()
    {
        if (!$this->module->active) {
            return $this->module->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
        }
        $assigns = $this->getCharts();
        $contacts = ETS_CFU_Contact::getContactsAllShop($this->context);
        $logs = ETS_CFU_Contact::getLogList($this->context);
        if ($logs) {
            $black_list = explode("\n", Configuration::get('ETS_CFU_IP_BLACK_LIST'));
            foreach ($logs as &$log) {
                if (in_array($log['ip'], $black_list))
                    $log['black_list'] = true;
                else
                    $log['black_list'] = false;
                $browser = explode(' ', $log['browser']);
                if (isset($browser[0]))
                    $log['class'] = Tools::strtolower($browser[0]);
                else
                    $log['class'] = 'default';
            }
        }
        $assigns = array_merge($assigns, array(
            'ets_cfu_month' => (string)Tools::getValue('ets_cfu_months', date('m')),
            'action' => $this->context->link->getAdminLink('AdminContactFormUltimateDashboard'),
            'ets_cfu_contacts' => $contacts,
            'ets_cfu_year' => Tools::getValue('years', date('Y')),
            'ets_cfu_cfu_contact' => (int)Tools::getValue('id_contact'),
            'ets_cfu_js_dir_path' => $this->module->getPathUri() . 'views/js/',
            'ets_cfu_img_dir_path' => $this->module->getPathUri() . 'views/img/',
            'ets_cfu_logs' => $logs,
            'cfu_tab_ets' => Tools::getValue('cfu_tab_ets', 'chart'),
            'ets_cfu_show_reset' => Tools::isSubmit('etsCfuSubmitFilterChart'),
            'ets_cfu_link' => $this->context->link,
            'ets_cfu_total_message' => ETS_CFU_Contact_Message::getCountUnreadMessage(),
            'ets_cfu_stats' => ETS_CFU_Presenter::getInstance()->getDashboardStats(),
            'filters' => $this->filters,
            'filter_active' => 'month'
        ));
        $this->context->smarty->assign($assigns);
        $this->_html .= $this->module->display($this->module->getLocalPath(), 'dashboard.tpl');
        return $this->_html;
    }
}
