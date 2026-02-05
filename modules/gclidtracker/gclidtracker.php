<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class GclidTracker extends Module
{
    public function __construct()
    {
        $this->name = 'gclidtracker';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Norwit';
        $this->need_instance = 0;
        $this->bootstrap = true;

         $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];


        parent::__construct();

        $this->displayName = $this->l('GCLID Tracker');
        $this->description = $this->l('Saves gclid, utm and other parameters to cookies for better conversion tracking.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('header');
    }

    public function hookHeader($params)
    {
        $this->context->controller->registerJavascript(
            'module-gclidtracker-js',
            'modules/' . $this->name . '/views/js/tracker.js',
            ['position' => 'head', 'priority' => 100]
        );
    }
}
