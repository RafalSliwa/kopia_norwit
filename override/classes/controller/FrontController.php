<?php
class FrontController extends FrontControllerCore
{
    /*
    * module: seoredirect
    * date: 2025-03-13 10:29:45
    * version: 2.3.2
    */
    public function init()
    {
        if (Module::isInstalled('seoredirect')) {
            $seoredirect = Module::getInstanceByName('seoredirect');
            $seoredirect->runSeoRedirect();
        }
        parent::init();
    }
}