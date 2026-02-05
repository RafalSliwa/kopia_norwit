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

class ETS_CFU_Recaptcha extends ETS_CFU_Service
{
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    private static $instance;
    private $sitekeys = array();

    private function __construct()
    {
        $site_key = Configuration::get('ETS_CFU_RECAPTCHA_TYPE') == 'v2' ? Configuration::get('ETS_CFU_SITE_KEY') : Configuration::get('ETS_CFU_SITE_KEY_V3');
        $secret_key = Configuration::get('ETS_CFU_RECAPTCHA_TYPE') == 'v2' ? Configuration::get('ETS_CFU_SECRET_KEY') : Configuration::get('ETS_CFU_SECRET_KEY_V3');
        $this->sitekeys[$site_key] = $secret_key;
    }

    public static function get_instance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function is_active()
    {
        $sitekey = $this->get_sitekey();
        $secret = $this->get_secret($sitekey);
        return $sitekey && $secret;
    }

    public function get_sitekey()
    {
        if (empty($this->sitekeys) || !is_array($this->sitekeys)) {
            return false;
        }
        $sitekeys = array_keys($this->sitekeys);
        return $sitekeys[0];
    }

    public function get_secret($sitekey)
    {
        $sitekeys = (array)$this->sitekeys;

        if (isset($sitekeys[$sitekey])) {
            return $sitekeys[$sitekey];
        } else {
            return false;
        }
    }

    public function get_categories()
    {
        return array('captcha');
    }

    public function icon()
    {
    }

    public function link()
    {
        echo sprintf(ETS_CFU_Tools::displayText('%2$s', 'a', ['href' => '%1$s']),
            'https://www.google.com/recaptcha/intro/index.html',
            'google.com/recaptcha');
    }

    public function verify($response_token)
    {
        if (empty($response_token)) {
            return false;
        }
        $sitekey = $this->get_sitekey();
        $secret = $this->get_secret($sitekey);
        $link_capcha = "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret . "&response=" . $response_token . "&remoteip=" . Tools::getRemoteAddr();
        $response = json_decode(Tools::file_get_contents($link_capcha), true);
	    $score = (float) Configuration::get('ETS_CFU_SCORE_CAPTCHA_V3');
        if ($response['success'] == false || (isset($response['score']) && $response['score'] < $score) ) {
            return false;
        } else
            return true;
    }

    public function get_title()
    {
        return 'reCAPTCHA';
    }
}