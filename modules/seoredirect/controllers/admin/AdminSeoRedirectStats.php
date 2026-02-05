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
require_once _PS_MODULE_DIR_ . 'seoredirect/models/seoRedirectList.php';

class AdminSeoRedirectStatsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->className = 'Configuration';
        $this->table = 'configuration';
        parent::__construct();
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        if ($part == 0) {
            return $exp[0];
        }
        if ($part == 1) {
            if ($exp[0] >= 8) {
                return 7;
            }
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function setMedia($var = false)
    {
        parent::setMedia($var);
        if ($this->psversion() != 5)
        {
            $this->addJS(array(
                _PS_JS_DIR_ . 'vendor/d3.v3.min.js',
                __PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/vendor/nv.d3.min.js'
            ));
        }
    }

    public function initContent()
    {
        if ($this->psversion() != 5)
        {
            $this->initPageHeaderToolbar();
        }
        $last_week_data = Db::getInstance()->ExecuteS('SELECT count(id_seor_stats) as total, stat_date AS `date`, DAY(stat_date) AS `day` FROM `' . _DB_PREFIX_ . 'seor_stats` WHERE (stat_date BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE()) GROUP BY DAY(stat_date)');
        $last_week_data_modified = array();
        foreach ($last_week_data as $key => $value)
        {
            $last_week_data_modified['values'][$key]['date'] = date("Y-m-d", strtotime($value['date']));
            $last_week_data_modified['values'][$key]['total'] = $value['total'];
        }
        $last_week_data_modified['key'] = $this->module->getTranslator()->trans('Redirections', [], 'Modules.Seoredirect.seoredirect');
        $last_week_data_modified['bar'] = true;
        $last_month_data = Db::getInstance()->ExecuteS('SELECT count(id_seor_stats) as total, stat_date AS `date`, DAY(stat_date) AS `day` FROM `' . _DB_PREFIX_ . 'seor_stats` WHERE (stat_date BETWEEN CURDATE()-INTERVAL 1 MONTH AND CURDATE()) GROUP BY DAY(stat_date)');
        $last_month_data_modified = array();
        foreach ($last_month_data as $key => $value)
        {
            $last_month_data_modified['values'][$key]['date'] = date("Y-m-d", strtotime($value['date']));
            $last_month_data_modified['values'][$key]['total'] = $value['total'];
        }
        $last_month_data_modified['key'] = $this->module->getTranslator()->trans('Redirections', [], 'Modules.Seoredirect.seoredirect');
        $last_month_data_modified['bar'] = true;
        $this->context->smarty->assign(array(
            'last_week_data' => (isset($last_week_data_modified['values']) ? json_encode($last_week_data_modified) : false),
            'last_month_data' => (isset($last_month_data_modified['values']) ? json_encode($last_month_data_modified) : false)
        ));
        $this->context->smarty->assign(array(
            'maintenance_mode' => !(bool)Configuration::get('PS_SHOP_ENABLE'),
            'lite_display' => false,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => true,
            'page_header_toolbar_title' => true,
            'title' => $this->module->getTranslator()->trans('Statistics', [], 'Modules.Seoredirect.seoredirect'),
            'toolbar_btn' => true,
            'psver' => $this->psversion(1),
        ));
        $this->content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seoredirect/views/admin/stats.tpl');
        $this->context->smarty->assign(array(
            'content' => $this->content
        ));
    }
}