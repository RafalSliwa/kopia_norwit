<?php

namespace Roja45\LicenseManager\Prestashop\Api;

interface RojaFortyFiveApiHelper
{
    public function registerModule(
        $module_source,
        $module_name,
        $customer_email,
        $order_reference,
        $domain,
        $test_domain,
        $account_key = null
    );

    public function validateModule(
        $auth_key,
        $module_source,
        $module_name,
        $customer_email,
        $domain,
        $status,
        $as_html
    );

    public function heartbeat(
        $module_name,
        $module_version,
        $module_source,
        $order_reference,
        $sending_host,
        $account_domain,
        $customer_email,
        $status,
        $connection
    );
    //public function checkModuleRegistration();
    //public function checkModuleUpdates();
}
