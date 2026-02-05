<?php

/**
 * File from https://prestashow.pl
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @authors     PrestaShow.pl <kontakt@prestashow.pl>
 * @copyright   2021 PrestaShow.pl
 * @license     https://prestashow.pl/license
 */

use Prestashow\PrestaCore\Model\ModuleSettings;

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$loggingLevel = (int)ModuleSettings::getInstance(__FILE__)->get('LOGGING_LEVEL');
if (!$loggingLevel) {
    $loggingLevel = 400; // \Monolog\Logger::ERROR
}
define('PSHOW_FBREVIEWS_LOGGING_LEVEL', $loggingLevel);

define(
    'PSHOW_FBREVIEWS_FB_PIXEL_ID',
    trim((string)Configuration::get('PSHOW_FBREVIEWS_FBPIXEL_ID'))
);
define(
    'PSHOW_FBREVIEWS_FB_ACCESS_TOKEN',
    trim((string)Configuration::get('PSHOW_FBREVIEWS_FBPIXEL_TOKEN'))
);
define(
    'PSHOW_FBREVIEWS_FB_API_CONFIGURED',
    PSHOW_FBREVIEWS_FB_PIXEL_ID && PSHOW_FBREVIEWS_FB_ACCESS_TOKEN
);

define(
    'PSHOW_FBREVIEWS_CRON_TOKEN',
    sha1(_COOKIE_KEY_ . 'pshowfbreviews')
);