<?php
/*
 * MIT License
 *
 * Copyright (c) 2025 Norwit
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND.
 *
 * @author   Norwit 
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  1.0.0
 * @package  disable_cod_limit
 * @category PrestaShop Module
 * @link     https://norwit.pl
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

class Disable_Cod_Limit extends Module
{
    public function __construct()
    {
        $this->name = 'disable_cod_limit';
        $this->tab = 'checkout';
        $this->version = '1.0.3';
        $this->author = 'Norwit';
        $this->bootstrap = true;
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->l('Restrict Cash on Delivery by Order Total');
        $this->description = $this->l('Disables Cash on Delivery option for orders above 15,000 zł');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayPaymentTop')
            && $this->registerHook('header');
    }


   public function hookDisplayPaymentTop($params)
    {
        $cart = $this->context->cart;

        if (!Validate::isLoadedObject($cart)) {
            return '';
        }

        $total = $cart->getOrderTotal(true, Cart::BOTH);
        $maxAmount = 15000;

       if (_PS_MODE_DEV_) {
         PrestaShopLogger::addLog('[disable_cod_limit] Cart total: ' . $total);
       }

        if ($total > $maxAmount) {
            $this->context->smarty->assign([
                'cod_warning_text' => $this->l(
                    'Cash on Delivery is available only for orders up to 15,000 zł.'
                ),
            ]);

            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . 'disable_cod_limit/views/templates/hook/displayPaymentTop.tpl'
            );
        }

        return '';
    }


    public function hookHeader()
    {
        if ('order' === Tools::getValue('controller')) {
            $this->context->controller->registerStylesheet(
                'module-disable-cod-css',
                'modules/' . $this->name . '/views/css/front.css',
                ['media' => 'all', 'priority' => 150]
            );
        }
    }

}
