<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class EasyCarouselsAjaxModuleFrontController extends ModuleFrontControllerCore
{
    public function initContent()
    {
        if (Tools::getValue('token') == $this->module->getAjaxToken()) {
            if ($action = Tools::getValue('action')) {
                $this->module->ajaxAction($action);
            }
        }
    }
}
