<?php
/**
 * 2024 Wild Fortress, Lda
 *
 * NOTICE OF LICENSE
 *
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @author    Hélder Duarte <cossou@gmail.com>
 * @copyright 2024 Wild Fortress, Lda
 * @license   Proprietary and confidential
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/*
 * File: /upgrade/Upgrade-2.8.0.php
 */
function upgrade_module_2_8_0($module)
{
    Configuration::updateValue('WEBHOOKS_CLEAN_EXECUTION_LOGS', 0);

    return true;
}
