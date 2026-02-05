<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class FrontController extends FrontControllerCore
{
    public function init()
    {
        if (Module::isInstalled('seoredirect')) {
            $seoredirect = Module::getInstanceByName('seoredirect');
            $seoredirect->runSeoRedirect();
        }
        parent::init();
    }
}