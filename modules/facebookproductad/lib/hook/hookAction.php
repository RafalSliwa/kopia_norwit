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

namespace FacebookProductAd\Hook;

if (!defined('_PS_VERSION_')) {
    exit;
}
class hookAction extends hookBase
{
    /**
     * @var string : define the hook type
     */
    protected $sHookAction;

    public function __construct($sHookAction)
    {
        $this->sHookAction = $sHookAction;
    }

    /**
     * method execute hook
     *
     * @param array $aParams
     *
     * @return array
     */
    public function run(array $aParams = null)
    {
        // to handle DAO

        // set variables
        $aDisplayHook = [];

        switch ($this->sHookAction) {
            default:
                break;
        }

        return $aDisplayHook;
    }
}
