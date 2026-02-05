<?php
/**
 * NOTICE OF LICENSE.
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Ceneo
 * @copyright 2024, Ceneo
 * @license   LICENSE.txt
 */
namespace CeneoBs\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Html
{
    protected $module;
    protected $context;

    public function __construct($module, $context)
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function displayInfoHeader()
    {
        $secureKey = md5(_COOKIE_KEY_ . \Configuration::get('PS_SHOP_NAME'));

        $this->context->smarty->assign([
            'secureKey' => $secureKey,
            'moduleLink' => $this->context->link->getModuleLink($this->module->name, 'default'),
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/admin/info_header.tpl'
        );
    }

    public function displayAdminOrder($message, $shipping, $payment)
    {
        $this->context->smarty->assign([
            'message' => $message,
            'shipping' => $shipping,
            'payment' => $payment,
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/admin/admin_order.tpl'
        );
    }
}
