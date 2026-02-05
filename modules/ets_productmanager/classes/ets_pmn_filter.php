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
class Ets_pmn_filter
{
    public static function getFilter($id_employee)
    {
        $filters = Db::getInstance()->getValue('SELECT filters FROM `'._DB_PREFIX_.'ets_pmn_filter_employee` WHERE id_employee='.(int)$id_employee);
        if($filters)
            return json_decode($filters,true);
        else
            return array();
    }
    public static function updateFilter($id_employee, $filters)
    {  
        $fields = Ets_pmn_defines::getInstance()->getFieldsByIdEmployee($id_employee);
        $list_filters = array();
        if($fields)
        {
            foreach($fields as $field)
            {
                $list_filters['filter_column_'.$field] ='';
            }
        }
        $filters = array_intersect_key(array_map('strval',$filters),$list_filters);
        $filters = json_encode($filters);
        if(Db::getInstance()->getRow('SELECT id_employee FROM `'._DB_PREFIX_.'ets_pmn_filter_employee` WHERE id_employee='.(int)$id_employee))
        {
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_pmn_filter_employee` SET filters="'.pSQL($filters,true).'" WHERE id_employee='.(int)$id_employee);
        }    
        else
        {
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_pmn_filter_employee`(id_employee,filters) VALUES('.(int)$id_employee.',"'.pSQL($filters,true).'")');
        }
    }
    public static function deleteFilter($id_employee)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_pmn_filter_employee` WHERE id_employee='.(int)$id_employee);
    }
}