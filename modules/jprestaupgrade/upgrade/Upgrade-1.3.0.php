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
 * Update (again) jpresta_ps_token
 */
function upgrade_module_1_3_0($module)
{
    // Get the token using the name used since v8.1.24
    $key = substr(md5($_SERVER['HTTP_HOST'].(isset($_SERVER['BASE']) ? $_SERVER['BASE'] : '')), 0, 14);
    $currentToken = Configuration::get('jpresta_ps_token_' . $key, null, 0, 0, false);
    if ($currentToken) {
        // Store it using the original name (we now use an other way to detect clones)
        Configuration::updateValue('jpresta_ps_token', $currentToken, false, 0, 0);
        Configuration::deleteByName('jpresta_ps_token_' . $key);
    }

    JprestaUpgradeApi::setPrestashopIsClone(false);

    return true;
}
