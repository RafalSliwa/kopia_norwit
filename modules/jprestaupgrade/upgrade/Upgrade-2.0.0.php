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
 * Add tab
 */
function upgrade_module_2_0_0($module)
{
    $module->installTab('AdminJprestaMigPCU2SP', $module->l('Migration to Speed Pack module'));

    return true;
}
