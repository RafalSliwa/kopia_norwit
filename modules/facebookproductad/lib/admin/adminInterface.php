<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 *           ____    _______
 *          |  _ \  |__   __|
 *          | |_) |    | |
 *          |  _ <     | |
 *          | |_) |    | |
 *          |____/     |_|
 */

namespace FacebookProductAd\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}
interface adminInterface
{
    /**
     * method process display or updating or etc ... admin
     *
     * @param string $sType => defines which method to execute
     * @param array $aParam => $_GET or $_POST
     *
     * @return mixed
     */
    public function run($sType, $aParam = []);
}
