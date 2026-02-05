<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once (__DIR__ . '/../x13allegro.php');

use x13allegro\Adapter\Module\x13gpsrAdapter;
use x13allegro\Api\Model\Offers\ProductSet\SafetyInformationType;

/**
 * @param $module x13allegro
 * @return bool
 */
function upgrade_module_7_7_0($module)
{
    $module->registerHook('actionX13GPSRModuleRenderConfigurationForm');

    XAllegroConfiguration::updateValue('PRODUCTIZATION_GPSR', ((new x13gpsrAdapter())->getInstance() ? 'x13gpsr' : 'allegro'));
    XAllegroConfiguration::updateValue('X13GPSR_SAFETY_INFORMATION_PRIORITY', SafetyInformationType::TEXT);
    XAllegroConfiguration::updateValue('X13GPSR_SAFETY_INFORMATION_TEXT_COMBINE', 0);
    XAllegroConfiguration::updateValue('X13GPSR_SAFETY_INFORMATION_TEXT_GENERAL', 1);
    XAllegroConfiguration::updateValue('X13GPSR_SAFETY_INFORMATION_TEXT_LABEL', 0);
    XAllegroConfiguration::updateValue('X13GPSR_SAFETY_INFORMATION_TEXT_RESPONSIBLE_PRODUCER', 0);
    XAllegroConfiguration::updateValue('X13GPSR_SAFETY_INFORMATION_TEXT_RESPONSIBLE_PERSON', 0);

    foreach (explode(';', X13_ION_ALLEGRO_VERSIONS) as $phpVersion) {
        Tools::deleteFile(X13_ALLEGRO_DIR . 'classes/' . $phpVersion . '/Api/DataUpdater/Entity/GPSRObligation.php');
    }

    Tools::deleteDirectory(X13_ALLEGRO_DIR . 'views/templates/admin/AuctionUpdater/GPSRObligation');

    return true;
}
