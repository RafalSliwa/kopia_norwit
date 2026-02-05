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

class ETS_CFU_Presenter extends ETS_CFU_Translate
{
    static $INSTANCE;
    public $module;
    public $context;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->module = Module::getInstanceByName('ets_cfultimate');
    }

    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new ETS_CFU_Presenter();
        }

        return self::$INSTANCE;
    }

    public function getDashboardStats()
    {
        $stats = array();
        $sql = '
            SELECT COUNT(%1$s) 
            FROM ' . _DB_PREFIX_ . 'ets_cfu_contact%3$s b
            LEFT JOIN ' . _DB_PREFIX_ . 'ets_cfu_contact%2$s_shop a ON (a.id_contact%2$s = b.id_contact%2$s)
            WHERE a.id_contact%2$s is NOT NULL 
                AND a.id_shop = ' . (int)$this->context->shop->id;

        $sql_forms = sprintf($sql, '*', '', '');
        $total_forms = (int)Db::getInstance()->getValue($sql_forms);

        $query_msg = sprintf($sql, '*', '_message', '_message');
        $total_msg = (int)Db::getInstance()->getValue($query_msg);

        $query_forms = sprintf($sql, 'DISTINCT a.id_contact', '', '_message');
        $msg_in_forms = (int)Db::getInstance()->getValue($query_forms);

        $stats[] = array(
            'label' => $this->l('Messages received', 'ETS_CFU_Presenter'),
            'color' => 'blue',
            'icon' => 'sign-in',
            'value1' => $total_msg,
            'value2' => sprintf(($total_forms > 1 ? $this->l('From %s forms', 'ETS_CFU_Presenter') : $this->l('From %s form', 'ETS_CFU_Presenter')), $msg_in_forms),
            'percent' => Tools::ps_round(($total_forms > 0 ? $msg_in_forms / $total_forms : 0) * 100, 0),
            'link' => $this->context->link->getAdminLink('AdminContactFormUltimateMessage', true),
        );
        $total_reps = ETS_CFU_Contact_Message::nbMessageReplied($this->context->shop->id);
        $stats[] = array(
            'label' => $this->l('Replied messages', 'ETS_CFU_Presenter'),
            'color' => 'green',
            'icon' => 'share',
            'value1' => $total_reps,
            'value2' => Tools::ps_round(($percent = ($total_msg > 0 ? $total_reps / $total_msg : 0) * 100), 0) . '%',
            'percent' => $percent,
            'link' => $this->context->link->getAdminLink('AdminContactFormUltimateMessage', true),
        );
        $query_reads = $query_msg . ' AND b.readed <= 0';
        $total_reads = (int)Db::getInstance()->getValue($query_reads);
        $stats[] = array(
            'label' => $this->l('Unread messages', 'ETS_CFU_Presenter'),
            'color' => 'brown',
            'icon' => 'envelope',
            'value1' => $total_reads,
            'value2' => Tools::ps_round(($percent = ($total_msg > 0 ? $total_reads / $total_msg : 0) * 100), 0) . '%',
            'percent' => $percent,
            'link' => $this->context->link->getAdminLink('AdminContactFormUltimateMessage', true),
        );
        $contact_is_reg = ETS_CFU_Contact_Message::nbMessageContactByCustomer($this->context->shop->id);
        $total_contacts = ETS_CFU_Contact_Message::nbMessageContact($this->context->shop->id);
        $stats[] = array(
            'label' => $this->l('Users contacted', 'ETS_CFU_Presenter'),
            'color' => 'yellow',
            'icon' => 'users',
            'value1' => $total_contacts,
            'value2' => sprintf($this->l('%s registered', 'ETS_CFU_Presenter'), $contact_is_reg),
            'percent' => Tools::ps_round(($total_contacts > 0 ? $contact_is_reg / $total_contacts : 0) * 100, 0),
            'link' => $this->context->link->getAdminLink('AdminCustomers', true),
        );
        $sql_form_enabled = $sql_forms . ' AND b.active > 0';
        $total_form_enabled = (int)Db::getInstance()->getValue($sql_form_enabled);
        $stats[] = array(
            'label' => $this->l('Contact forms', 'ETS_CFU_Presenter'),
            'color' => 'pink',
            'icon' => 'th-large',
            'value1' => $total_forms,
            'value2' => sprintf($this->l('%s enabled', 'ETS_CFU_Presenter'), ($total_form_enabled != $total_forms ? $total_form_enabled : $this->l('All', 'ETS_CFU_Presenter'))),
            'percent' => Tools::ps_round(($total_forms > 0 ? $total_form_enabled / $total_forms : 0) * 100, 0),
            'link' => $this->context->link->getAdminLink('AdminContactFormUltimateContactForm', true),
        );
        return $stats;
    }
}