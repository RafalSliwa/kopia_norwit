<?php
/**
 * ZammadApiFactory
 *
 * @category  ZammadApiFactory
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * ZammadApiFactory
 *
 * @category  Class
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

namespace Roja45\LicenseManager\Prestashop\Api;

use Roja45\LicenseManager\APIHelper\APIHelper;
use Roja45\LicenseManager\Prestashop\Api\v1\RojaFortyFiveAPIV1Helper;

class RojaFortyFiveApiFactory {
    const V1 = 1;

    public static function getApiHelper(
        $api_version,
        $api_url,
        $api_key
    ) {
        $here = '';
        switch ($api_version) {
            case 1:
                return new RojaFortyFiveAPIV1Helper(
                    $api_url,
                    $api_key,
                    APIHelper::LIVE_MODE
                );
                break;
            default:
                return new RojaFortyFiveAPIV1Helper(
                    $api_url,
                    $api_key
                );
                break;
        }
    }
}
