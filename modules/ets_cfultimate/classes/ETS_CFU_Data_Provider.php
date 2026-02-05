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

class ETS_CFU_Data_Provider extends ETS_CFU_Translate
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
            self::$INSTANCE = new ETS_CFU_Data_Provider();
        }

        return self::$INSTANCE;
    }

    public function getMediaLink($file)
    {
        $filepath = _PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $file;
        return trim($file) !== '' && @file_exists($filepath) ? $this->context->link->getMediaLink(_PS_IMG_ . $this->module->name . DIRECTORY_SEPARATOR . trim($file)) : '';
    }

    public function getIcons($icon)
    {
        return $this->fetch('icons.tpl', ['icon' => $icon]);
    }

    public function fetch($template, $assign_smarty = array())
    {
        if ($assign_smarty) {
            $this->context->smarty->assign($assign_smarty);
        }
        if (file_exists(($filename = $this->module->getLocalPath() . 'views/templates/hook/' . $template))) {
            return $this->context->smarty->fetch($filename);
        }
    }

    public function checkUploadError($error_code, $file_name)
    {
        switch ($error_code) {
            case 1:
                return sprintf($this->l('File "%1s" uploaded exceeds %2s', 'ETS_CFU_Data_Provider'), $file_name, ini_get('upload_max_filesize'));
            case 2:
                return sprintf($this->l('The uploaded file exceeds %s', 'ETS_CFU_Data_Provider'), ini_get('post_max_size'));
            case 3:
                return sprintf($this->l('Uploaded file "%s" was only partially uploaded', 'ETS_CFU_Data_Provider'), $file_name);
            case 6:
                return $this->l('Missing temporary folder', 'ETS_CFU_Data_Provider');
            case 7:
                return sprintf($this->l('Failed to write file "%s" to disk', 'ETS_CFU_Data_Provider'), $file_name);
            case 8:
                return sprintf($this->l('A PHP extension stopped the file "%s" to upload', 'ETS_CFU_Data_Provider'), $file_name);
        }
        return false;
    }
}