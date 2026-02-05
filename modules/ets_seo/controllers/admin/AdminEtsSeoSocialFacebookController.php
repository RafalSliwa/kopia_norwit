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
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AdminEtsSeoSocialFacebookController
 *
 * @property Ets_Seo $module
 */
class AdminEtsSeoSocialFacebookController extends ModuleAdminController
{
    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $seoDef = Ets_Seo_Define::getInstance();
        $this->fields_options = [
            'facebook_setting' => [
                'title' => $this->l('Facebook settings'),
                'fields' => $seoDef->fields_config()['facebook_setting'],
                'icon' => '',
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->l('You must enable module SEO Audit to configure its features');
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $this->setDefaultValue('ETS_SEO_FACEBOOK_FP_IMG_URL');
            $this->setDefaultValue('ETS_SEO_FACEBOOK_DEFULT_IMG_URL');
            $fbImgUrl = ($fbImgUrl = Tools::getValue('ETS_SEO_FACEBOOK_FP_IMG_URL')) && Validate::isCleanHtml($fbImgUrl) ? $fbImgUrl : '';
            if (isset($_FILES['ETS_SEO_FACEBOOK_FP_IMG_URL']) && $fbImgUrl) {
                $this->checkImageUploaded('ETS_SEO_FACEBOOK_FP_IMG_URL');
            }
            $defaultImgUrl = ($defaultImgUrl = Tools::getValue('ETS_SEO_FACEBOOK_DEFULT_IMG_URL')) && Validate::isCleanHtml($defaultImgUrl) ? $defaultImgUrl : '';
            if (isset($_FILES['ETS_SEO_FACEBOOK_DEFULT_IMG_URL']) && $defaultImgUrl) {
                $this->checkImageUploaded('ETS_SEO_FACEBOOK_DEFULT_IMG_URL');
            }
            $this->module->_clearCache('*');
        }
        parent::postProcess();
    }

    protected function checkImageUploaded($file_name)
    {
        $image = $_FILES[$file_name];
        if (!$image['name'] || $image['error'] > 0) {
            return false;
        }
        $allowExtentions = ['png', 'jpg', 'jpeg', 'gif'];
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        if (!Validate::isFileName(str_replace([' ', '(', ')', '!', '@', '#', '+'], '_', $image['name']))) {
            $this->errors[] = sprintf($this->l('The file name "%s" is invalid'), $image['name']);
        } elseif (!in_array($ext, $allowExtentions)) {
            $this->errors[] = 'ETS_SEO_FACEBOOK_DEFULT_IMG_URL' == $file_name ? $this->l('The image default is not in the correct format, accepted formats: png, jgg, jpeg, gif') : $this->l('The frontpage image logo is not in the correct format, accepted formats: png, jgg, jpeg, gif');
        } elseif (($max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')) && ImageManager::validateUpload($image, $max_file_size * 1024 * 1024)) {
            $this->errors[] = 'ETS_SEO_FACEBOOK_DEFULT_IMG_URL' == $file_name ? sprintf($this->l('The image default logo is too large. Maximum size: %dMb'), $max_file_size) : sprintf($this->l('The front page image logo is too large. Maximum size: %dMb'), $max_file_size);
        } else {
            if (($file = Configuration::get($file_name)) && file_exists(_PS_ROOT_DIR_ . '/img/social/' . $file)) {
                unlink(_PS_ROOT_DIR_ . '/img/social/' . $file);
            }
            $this->uploadLogoImage($image, $file_name);
        }
    }

    protected function uploadLogoImage($image, $name)
    {
        if (!$image['name'] || $image['error'] > 0) {
            return false;
        }
        $image_name = time() . rand(11111, 99999) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);

        if (move_uploaded_file($image['tmp_name'], _PS_ROOT_DIR_ . '/img/social/' . $image_name)) {
            Configuration::updateValue($name, $image_name);
            $_POST[$name] = $image_name;

            return true;
        }

        return false;
    }

    protected function setDefaultValue($key)
    {
        $requestValue = Tools::getValue($key);
        if (!is_array($requestValue) && !Validate::isCleanHtml($requestValue)) {
            $requestValue = '';
        }
        if (Configuration::get($key) && !$requestValue) {
            $_POST[$key] = Configuration::get($key);
        }
    }
}
