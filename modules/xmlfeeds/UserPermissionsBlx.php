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

class UserPermissionsBlx
{
    public function updatePassword($password)
    {
        $contextCookie = Context::getContext()->cookie;
        $password = $this->getEncryptedPassword($password);

        ConfigurationBlx::update('admin_password', $password);
        $this->addPasswordToCookie($contextCookie, $password);
    }

    public function getPassword()
    {
        return ConfigurationBlx::get('admin_password');
    }

    public function isPasswordValid($password)
    {
        return $this->getEncryptedPassword($password) == $this->getPassword();
    }

    public function isAllowViewPage($contextCookie)
    {
        $passwordFromDb = $this->getPassword();

        if (empty($passwordFromDb)) {
            return true;
        }

        if ($this->getPasswordFromCookie($contextCookie) != $passwordFromDb) {
            return false;
        }

        return true;
    }

    public function addPasswordToCookie($contextCookie, $encryptedPassword)
    {
        $contextCookie->blproductimporter_ap = $encryptedPassword;
        $contextCookie->write();
    }

    public function getPasswordFromCookie($contextCookie)
    {
        return !empty($contextCookie->blproductimporter_ap) ? $contextCookie->blproductimporter_ap : '';
    }

    public function getEncryptedPassword($password)
    {
        if (empty($password)) {
            return '';
        }

        return md5($password.'aLwP5'.$password.'48lOp.e4W#80tP;kfeqn@4lF');
    }
}
