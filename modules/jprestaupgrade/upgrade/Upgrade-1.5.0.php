<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

/*
 * Hook to displayAdminAfterHeader
 */
function upgrade_module_1_5_0($module)
{
    $module->registerHook('displayAdminAfterHeader');

    return true;
}
