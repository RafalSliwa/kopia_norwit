<?php
class Hook extends HookCore
{
    /*
    * module: x13eucookies
    * date: 2025-03-02 09:08:01
    * version: 1.3.5
    */
    public static function getHookModuleExecList($hookName = null)
    {
        $modulesToInvoke = parent::getHookModuleExecList($hookName);
        if (file_exists(_PS_MODULE_DIR_ . 'x13eucookies/x13eucookies.php') && Module::isEnabled('x13eucookies')) {
            
            $x13eucookies = Module::getInstanceByName('x13eucookies');
            $modulesToInvoke = $x13eucookies->filterModules($modulesToInvoke);
        }
        return !empty($modulesToInvoke) ? $modulesToInvoke : false;
    }
}
