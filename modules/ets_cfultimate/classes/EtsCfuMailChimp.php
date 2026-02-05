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

class EtsCfuMailChimp extends ETS_CFU_Translate
{
    static $instance;

    public static function getInstance()
    {
        if (!self::$instance)
            self::$instance = new EtsCfuMailChimp();
        return self::$instance;
    }

    public function getAudiences($api_key)
    {
        $data_lists = array();
        $arr_ret = array();
        if (!$api_key) {
            return false;
        }
        $api_connect = EtsCfuMailchimpApi::getInstance($api_key);
        $result = $api_connect->get('/lists/?count=100');
        if ($result && isset($result->lists) && !empty($result->lists)) {
            $arr_ret['connect_status'] = true;
            foreach ($result->lists as $list) {
                $data_lists[$list->id] = $list->name;
            }
            $arr_ret['data_list'] = $data_lists;
        } elseif ($result) {
            if (isset($result->lists) && empty($result->lists)) {
                $arr_ret['connect_status'] = true;
                $arr_ret['data_list'] = false;
            }
            if (isset($result->status) && $result->status == '401') {
                $arr_ret['connect_status'] = false;
                $arr_ret['data_list'] = false;
            }
        } else {
            $arr_ret['connect_status'] = false;
            $arr_ret['data_list'] = false;
        }
        return $arr_ret;
    }

    public function getMergeFields($api_key, $list_id)
    {
        $merge_fields = [];
        if (!$api_key) {
            return $merge_fields;
        }
        $api_connect = EtsCfuMailchimpApi::getInstance($api_key);
        $result = $api_connect->get(sprintf('/lists/%s/merge-fields', $list_id));
        if ($result && isset($result->merge_fields) && !empty($result->merge_fields)) {
            foreach ($result->merge_fields as $merge_field) {
                $field = [
                    'id' => $merge_field->merge_id,
                    'tag' => $merge_field->tag,
                    'name' => $merge_field->name,
                    'type' => $merge_field->type,
                    'required' => $merge_field->required,
                    'default_value' => $merge_field->default_value,
                    'options' => $merge_field->options,
                ];
                if ($merge_field->tag === 'ADDRESS') {
                    $merge_fields[$merge_field->tag . '.addr1'] = $field;
                    $merge_fields[$merge_field->tag . '.addr1']['name'] = $this->l('Address1', 'EtsCfuMailChimp');
                    $merge_fields[$merge_field->tag . '.addr1']['tag'] = $merge_field->tag . '.addr1';

                    $merge_fields[$merge_field->tag . '.addr2'] = $field;
                    $merge_fields[$merge_field->tag . '.addr2']['name'] = $this->l('Address2', 'EtsCfuMailChimp');
                    $merge_fields[$merge_field->tag . '.addr2']['tag'] = $merge_field->tag . '.addr2';

                    $merge_fields[$merge_field->tag . '.city'] = $field;
                    $merge_fields[$merge_field->tag . '.city']['name'] = $this->l('City', 'EtsCfuMailChimp');
                    $merge_fields[$merge_field->tag . '.city']['tag'] = $merge_field->tag . '.city';

                    $merge_fields[$merge_field->tag . '.state'] = $field;
                    $merge_fields[$merge_field->tag . '.state']['name'] = $this->l('State', 'EtsCfuMailChimp');
                    $merge_fields[$merge_field->tag . '.state']['tag'] = $merge_field->tag . '.state';

                    $merge_fields[$merge_field->tag . '.zip'] = $field;
                    $merge_fields[$merge_field->tag . '.zip']['name'] = $this->l('Zipcode', 'EtsCfuMailChimp');
                    $merge_fields[$merge_field->tag . '.zip']['tag'] = $merge_field->tag . '.zip';

                    $merge_fields[$merge_field->tag . '.country'] = $field;
                    $merge_fields[$merge_field->tag . '.country']['name'] = $this->l('Country', 'EtsCfuMailChimp');
                    $merge_fields[$merge_field->tag . '.country']['tag'] = $merge_field->tag . '.country';
                } else
                    $merge_fields[$merge_field->tag] = $field;
            }
        }
        return $merge_fields;
    }

    public function addMergeFields($api_key, $list_id, $data)
    {
        if (!$api_key || empty($data) || empty($data['email_address']) || empty($data['merge_fields'])) {
            return false;
        }
        $api_connect = EtsCfuMailchimpApi::getInstance($api_key);
        $subscriber_hash = md5(Tools::strtolower($data['email_address']));
        $data['status'] = 'subscribed';
        $result = $api_connect->put(sprintf('/lists/%s/members/%s', $list_id, $subscriber_hash), $data);
        if ($result && isset($result->merge_fields) && !empty($result->merge_fields)) {

        }
        return true;
    }
}