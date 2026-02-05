<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

if (!defined('_PS_VERSION_')) {exit;}

class Context extends ContextCore
{
    public function getMobileDetect()
    {
        if ($this->mobile_detect === null) {
            if (!Module::isEnabled('jprestaspeedpack') || !file_exists(_PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php')) {
                return parent::getMobileDetect();
            } else {
                require_once _PS_MODULE_DIR_ . 'jprestaspeedpack/jprestaspeedpack.php';
                if ($this->mobile_detect === null) {
                    if (Jprestaspeedpack::isCacheWarmer()) {
                        $this->mobile_detect = new JprestaUtilsMobileDetect();
                    } else {
                        return parent::getMobileDetect();
                    }
                }
            }
        }
        return $this->mobile_detect;
    }
}
