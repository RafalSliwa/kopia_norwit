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

require_once(dirname(__FILE__) . '/../../classes/ETS_CFU_Pagination.php');
require_once(dirname(__FILE__) . '/AdminModuleAdapterController.php');

class AdminContactFormUltimateStatisticsController extends AdminModuleAdapterController
{
    public function __construct()
    {
        parent::__construct();
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
        if ($ip = Tools::getValue('etsCfuAddToBlackList')) {
            $black_list = explode("\n", Configuration::get('ETS_CFU_IP_BLACK_LIST'));
            $black_list[] = $ip;
            Configuration::updateValue('ETS_CFU_IP_BLACK_LIST', implode("\n", $black_list));
            if (Tools::isSubmit('etsCfuAjax')) {
                die(json_encode(array(
                    'ok' => true,
                )));
            }
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateStatistics') . '&cfu_tab_ets=view-log');
        }
        if (Tools::isSubmit('etsCfuClearLogSubmit')) {
            ETS_CFU_Contact::cleanLogByIdShop($this->context->shop->id);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminContactFormUltimateStatistics') . '&cfu_tab_ets=view-log&conf=1');
        }
    }

    public function renderList()
    {
        if (!$this->module->active) {
            return $this->module->displayWarning($this->l('You must enable "Contact Form Ultimate" module to configure its features'));
        }
        $assigns = $this->getCharts();
        $contacts = ETS_CFU_Contact::getContactsAllShop($this->context);
        $total = ETS_CFU_Contact::countContactLogByIdLang($this->context->language->id);
        $limit = 20;
        $page = Tools::getValue('page', 1);
        if ($page <= 0)
            $page = 1;
        $start = ($page - 1) * $limit;
        $pagination = new ETS_CFU_Pagination();
        $pagination->url = $this->context->link->getAdminLink('AdminContactFormUltimateStatistics') . '&cfu_tab_ets=view-log&page=_page_';
        $pagination->limit = $limit;
        $pagination->page = $page;
        $pagination->total = $total;
        $pagination->controller = Tools::getValue('controller');
        $logs = ETS_CFU_Contact::getLogList($this->context, $limit, $start);
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
            'action' => $this->context->link->getAdminLink('AdminContactFormUltimateStatistics'),
            'ets_cfu_js_dir_path' => $this->module->getPathUri() . 'views/js/',
            'ets_cfu_logs' => $logs,
            'cfu_tab_ets' => Tools::getValue('cfu_tab_ets', 'chart'),
            'ets_cfu_pagination_text' => $pagination->render2(),
            'ets_cfu_show_reset' => Tools::isSubmit('etsCfuSubmitFilterChart'),
            'ets_cfu_contacts' => $contacts,
        ));
        $this->context->smarty->assign($assigns);
        return $this->module->display($this->module->getLocalPath(), 'statistics.tpl');
    }
}