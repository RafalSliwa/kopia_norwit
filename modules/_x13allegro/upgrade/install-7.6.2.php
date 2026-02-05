<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once (__DIR__ . '/../x13allegro.php');

/**
 * @param $module x13allegro
 * @return bool
 */
function upgrade_module_7_6_2($module)
{
    foreach (XAllegroAccount::getAllIds(false) as $row) {
        $config = new XAllegroConfigurationAccount($row['id_xallegro_account']);
        $config->updateValue('QUANITY_ALLEGRO_OOS', XAllegroConfigurationAccount::GLOBAL_OPTION);
    }

    $module->reinstallTabs();

    return true;
}
