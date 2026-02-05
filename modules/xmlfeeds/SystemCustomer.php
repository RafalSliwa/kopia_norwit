<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SystemCustomer
{
    const CUSTOMER_EMAIL = 'support@blmodules.com';

    public function addIfNotExists()
    {
        $customerId = $this->getId();

        if (!empty($customerId)) {
            return $customerId;
        }

        return $this->add();
    }

    public function getId()
    {
        return Customer::customerExists(self::CUSTOMER_EMAIL, true);
    }

    public function updateGroup($groupId)
    {
        $customerId = $this->getId();

        if (empty($customerId)) {
            $this->add();
        }

        $customer = new Customer($customerId);
        $customer->id_default_group = $groupId;
        $customer->update();
    }

    protected function add()
    {
        try {
            $customer = new Customer();
            $customer->id_default_group = 0;
            $customer->email = self::CUSTOMER_EMAIL;
            $customer->firstname = 'BlModules';
            $customer->lastname = 'SystemUser';
            $customer->setWsPasswd('f@.P;'.substr(md5(rand(10, 9999).date('YmdHis')), 0, 20));
            $customer->add();
        } catch (Exception $e) {
        }

        return $this->getId();
    }
}
