<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0(Module $module)
{
    if (!($module instanceof AvailabilitySort)) {
        return true;
    }

    $hooks = [
        'actionProductSearchProviderRunQueryBefore',
        'actionProductSearchProviderRunQueryAfter',
    ];

    foreach ($hooks as $hookName) {
        if (!$module->isRegisteredInHook($hookName) && !$module->registerHook($hookName)) {
            \PrestaShopLogger::addLog(sprintf('[%s] Failed to register hook %s during upgrade 1.1.0.', $module->name, $hookName), 3, null, $module->name);

            return false;
        }
    }

    return true;
}
