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

class EtsCfuMailchimpApi
{
    private $api_key;
    private $api_url = 'https://api.mailchimp.com/3.0/';
    private $last_response;
    private $last_request;

    private static $instance;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
        $dash_position = strpos($api_key, '-');
        if ($dash_position !== false) {
            $this->api_url = str_replace('//api.', '//' . Tools::substr($api_key, $dash_position + 1) . ".api.", $this->api_url);
        }
    }

    public static function getInstance($api_key)
    {
        if (!self::$instance || !isset(self::$instance[$api_key]) || empty(self::$instance[$api_key])) {
            self::$instance[$api_key] = new EtsCfuMailchimpApi($api_key);
        }
        return self::$instance[$api_key];
    }

    public function request($method, $resource = false, array $data = array())
    {
        $url = $this->api_url . ltrim($resource, '/');
        if ($method == 'GET')
            $url .= '?' . http_build_query($data);
        if (empty($this->api_key)) {
            return false;
        }
        $mch = curl_init();
        curl_setopt($mch, CURLOPT_URL, $url);
        curl_setopt($mch, CURLOPT_HTTPHEADER, $this->getHeaders());
        curl_setopt($mch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($mch, CURLOPT_TIMEOUT, 10);
        curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false);
        if ($method != 'GET') {
            curl_setopt($mch, CURLOPT_POST, true);
            curl_setopt($mch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        return json_decode(curl_exec($mch));
    }

    private function getHeaders()
    {
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Basic ' . call_user_func('base64_encode', 'user:' . $this->api_key);
        $headers[] = 'Accept: application/json';
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $headers[] = 'Accept-Language:' . $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        return $headers;
    }

    /**
     * @link https://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
     */
    public function getListMember($list_id, $email_address, array $args = array())
    {
        $subscriber_hash = md5(Tools::strtolower(trim($email_address)));
        $resource = sprintf('/lists/%s/members/%s', $list_id, $subscriber_hash);
        return $this->get($resource, $args);
    }

    /**
     * Gets information about all members of a MailChimp list.
     * @see http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/#read-get_lists_list_id_members
     */
    public function checkMemberExit($id_list, $email_check)
    {
        $api_id_list = $id_list;
        if (!$this->api_key || !$api_id_list) {
            return false;
        }
        try {
            $data = $this->getListMember($api_id_list, $email_check);
        } catch (Exception $e) {
            die('Error connect');
        }
        return !empty($data->id) && $data->status === 'subscribed';
    }

    public function getAllMemberList($list_id, $args = array())
    {
        $resource = sprintf('/lists/%s/members', $list_id);
        return $this->get($resource, $args);
    }

    public function getCountMemberList($list_id)
    {
        $resource = sprintf('/lists/%s/members?count=1', $list_id);
        return $this->get($resource);
    }

    public function get($resource, array $args = array())
    {
        return $this->request('GET', $resource, $args);
    }

    public function post($resource, array $data)
    {
        return $this->request('POST', $resource, $data);
    }

    public function put($resource, array $data)
    {
        return $this->request('PUT', $resource, $data);
    }

    public function patch($resource, array $data)
    {
        return $this->request('PATCH', $resource, $data);
    }

    public function delete($resource)
    {
        return $this->request('DELETE', $resource);
    }

}
