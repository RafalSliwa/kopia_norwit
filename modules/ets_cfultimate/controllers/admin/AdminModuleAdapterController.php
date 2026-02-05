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

class AdminModuleAdapterController extends ModuleAdminController
{
    public $y_max_value = 0;
    public $charts;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function getMaxY($top)
    {
        $top = (int)$top;
        if ($top < 10)
            return ($top <= 5) ? $top + 1 : $top + 2;
        elseif ($top < 100)
            return ($top % 10 < 5) ? (floor($top / 10) + 1) * 10 : (floor($top / 10) + 2) * 10;
        elseif ($top < 1000)
            return ($top % 100 < 5) ? (floor($top / 100) + 1) * 100 : (floor($top / 100) + 2) * 100;
        else
            return ($top % 1000 < 5) ? (floor($top / 1000) + 1) * 1000 : (floor($top / 1000) + 1) * 1000;
    }

    public function getColor($num)
    {
        $hash = md5('color' . $num);
        $rgb = array(
            hexdec(Tools::substr($hash, 0, 2)),
            hexdec(Tools::substr($hash, 2, 2)),
            hexdec(Tools::substr($hash, 4, 2)));
        return 'rgba(' . implode(',', $rgb) . ', %s)';
    }

    public function getCharts($ajax = false)
    {
        $this->y_max_value = 0;
        $is_dashboard = $this->context->controller instanceof AdminContactFormUltimateStatisticsController;
        $labels = $messages = $views = $replies = $users = $years = array();
        $months = Tools::dateMonths();
        $max_year = date('Y');
        $min_year = ETS_CFU_Contact::getMinYear(Tools::getValue('id_contact'));
        $distance = ($max_year - $min_year);
        if ($distance <= 0) {
            $min_year = $max_year - 1;
        }
        if ($min_year) {
            for ($i = $min_year; $i <= $max_year; $i++) {
                $years[] = $i;
            }
        }
        $sl_year = Tools::getValue('ets_cfu_years', ($distance < 5 ? date('Y') : false));
        $sl_month = Tools::getValue('ets_cfu_months');
        $args = array(
            'year' => '',
            'month' => '',
            'day' => '',
            'id_contact' => Tools::getValue('id_contact', false)
        );
        $filter = Tools::getValue('filter', ($is_dashboard ? ($distance >= 5 ? 'all' : 'year') : ''));
        if (($filter == 'all' || (!$sl_year && !$is_dashboard)) && $years) {
            foreach ($years as $year) {
                $args['year'] = $labels[] = $year;
                $messages[] = ETS_CFU_Contact_Message::getCountMessage($args, $this->y_max_value, $this->context);
                $views[] = ETS_CFU_Contact::getCountView($args, $this->y_max_value, $this->context);
                $replies[] = ETS_CFU_Contact_Reply::getCountReplies($args, $this->y_max_value, $this->context);
                $users[] = ETS_CFU_Contact::getCountUsers($args, $this->y_max_value, $this->context);
            }
        } elseif (($filter == 'year' || (!$sl_month && !$is_dashboard)) && $months) {
            $args['year'] = ($sl_year ?: date('Y'));
            foreach ($months as $key => $month) {
                $args['month'] = $labels[] = $key;
                $messages[] = ETS_CFU_Contact_Message::getCountMessage($args, $this->y_max_value, $this->context);
                $views[] = ETS_CFU_Contact::getCountView($args, $this->y_max_value, $this->context);
                $replies[] = ETS_CFU_Contact_Reply::getCountReplies($args, $this->y_max_value, $this->context);
                $users[] = ETS_CFU_Contact::getCountUsers($args, $this->y_max_value, $this->context);
            }
        } elseif ($filter == 'month' || ($sl_month && $sl_year && !$is_dashboard)) {
            $args['year'] = ($year = ($sl_year ?: (int)date('Y')));
            $args['month'] = ($month = ($sl_month ?: (int)date('m')));
            if (($days = function_exists('cal_days_in_month') ? cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year) : (int)date('t', mktime(0, 0, 0, (int)$month, 1, (int)$year)))) {
                for ($day = 1; $day <= $days; $day++) {
                    $args['day'] = $labels[] = $day;
                    $messages[] = ETS_CFU_Contact_Message::getCountMessage($args, $this->y_max_value, $this->context);
                    $views[] = ETS_CFU_Contact::getCountView($args, $this->y_max_value, $this->context);
                    $replies[] = ETS_CFU_Contact_Reply::getCountReplies($args, $this->y_max_value, $this->context);
                    $users[] = ETS_CFU_Contact::getCountUsers($args, $this->y_max_value, $this->context);
                }
            }
        }
        $this->charts['mes'] += array(
            'data' => $messages,
            'color' => 'rgba(243, 166, 180, %s)'
        );
        $this->charts['vie'] += array(
            'data' => $views,
            'color' => 'rgba(108, 206, 216, %s)'
        );
        $this->charts['rep'] += array(
            'data' => $replies,
            'color' => 'rgba(251, 227, 185, %s)'
        );
        $this->charts['use'] += array(
            'data' => $users,
            'color' => 'rgba(111, 208, 136, %s)'
        );

        foreach ($this->charts as &$item) {
            $item['backgroundColor'] = sprintf($item['color'], 0.3);
            $item['borderColor'] = sprintf($item['color'], 1);
            $item['borderWidth'] = 1;
            $item['pointRadius'] = 2;
            $item['fill'] = true;
        }
        $assigns = array(
            'ets_cfu_years' => $years,
            'ets_cfu_months' => $months,
            'ets_cfu_line_chart' => array_values($this->charts),
            'ets_cfu_lc_labels' => $labels,
            'y_max_value' => $this->getMaxY($this->y_max_value),
            'filter_active' => $filter,
            'sl_year' => $sl_year,
            'sl_month' => $sl_month,
            'sl_contact' => (int)Tools::getValue('id_contact'),
        );
        if ($ajax) {
            die(json_encode($assigns));
        }
        return $assigns;
    }
}