<?php
/**
 * NOTICE OF LICENSE
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2023 Ceneo
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Ceneo_XmlDownloadModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $shop_id = Tools::getValue('id_shop');
        if ($shop_id) {
            $this->download($shop_id);
        }
    }

    private function download($id_shop)
    {
        $file_path = _PS_MODULE_DIR_ . 'ceneo_xml/export/ceneo-' . (int) $id_shop . '.xml';
        if (Tools::getValue('secure_key')) {
            $secureKey = md5(_COOKIE_KEY_ . Configuration::get('PS_SHOP_NAME'));
            if (!empty($secureKey) && $secureKey === Tools::getValue('secure_key')) {
                header('Content-disposition: attachment; filename="ceneo-' . $id_shop . '.xml"');
                header('Content-type: "text/xml"; charset="utf8"');
                readfile($file_path);
                exit;
            }
        }
    }
}
