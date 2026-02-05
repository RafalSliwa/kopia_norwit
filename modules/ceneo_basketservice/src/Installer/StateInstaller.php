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
namespace CeneoBs\Installer;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Configuration as Cfg;

class StateInstaller
{
    protected $module;
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function addOrderState($name, $context)
    {
        $state_exist = false;
        $states = \OrderState::getOrderStates((int) $context->language->id);

        foreach ($states as $state) {
            if (in_array($name, $state)) {
                $state_exist = true;
                break;
            }
        }

        if (!$state_exist) {
            $order_state = new \OrderState();
            $order_state->color = '#00ffff';
            $order_state->send_email = false;
            $order_state->module_name = $this->module->name;
            $order_state->template = 'ceneo_basketservice';
            $order_state->name = [];
            $languages = \Language::getLanguages(false);
            foreach ($languages as $language) {
                $order_state->name[$language['id_lang']] = $name;
            }

            $order_state->add();
            Cfg::updateValue('CENEO_BS_STATE', $order_state->id);
        }

        return true;
    }
}
