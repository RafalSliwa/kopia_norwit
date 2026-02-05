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

namespace FacebookProductAd\ModuleLib;

if (!defined('_PS_VERSION_')) {
    exit;
}
class moduleReporting
{
    /**
     * @var mixed
     */
    public static $oReporting;

    /**
     * @var array : stock msg reported
     */
    public $aReport = [];

    /**
     * @var string : store file name
     */
    public $sFileName = '';

    /**
     * @var bool : activate or not reporting
     */
    public $bActivate;

    /**
     * Magic Method __construct
     *
     * @param bool $bActivate
     */
    public function __construct($bActivate = true)
    {
        $this->bActivate = $bActivate;
    }

    /**
     * method stock reporting
     *
     * @param string $Key
     * @param array $aParams
     *
     * @return mixed
     */
    public function set($Key, $aParams)
    {
        if ($this->bActivate) {
            $this->aReport[$Key][] = $aParams;
        }
    }

    /**
     * method return available serialized content
     *
     * @return array
     */
    public function get()
    {
        $aData = [];

        if ($this->bActivate && file_exists($this->sFileName) && filesize($this->sFileName)) {
            $sContent = method_exists('Tools', 'file_get_contents') ? \Tools::file_get_contents($this->sFileName) : file_get_contents($this->sFileName);

            if (!empty($sContent)) {
                $aData = moduleTools::handleGetConfigurationData($sContent);
            }
        }

        return $aData;
    }

    /**
     * merge data between current data and stored data in reporting file
     *
     * @return array
     */
    public function mergeData()
    {
        $aReport = [];

        if ($this->bActivate && !empty($this->aReport)) {
            // get unserialized reporting
            $aReport = $this->get();

            if (!empty($aReport) && is_array($aReport)) {
                foreach ($this->aReport as $sKeyName => $aProducts) {
                    foreach ($this->aReport[$sKeyName] as $iKey => $mValue) {
                        $aReport[$sKeyName][] = $mValue;
                    }
                }
            } else {
                $aReport = $this->aReport;
            }
            $this->aReport = [];
        }

        return $aReport;
    }

    /**
     * method creates singleton
     *
     * @param bool $bActivate
     *
     * @return mixed
     */
    public static function create($bActivate = true)
    {
        if (null === self::$oReporting) {
            self::$oReporting = new moduleReporting($bActivate);
        }

        return self::$oReporting;
    }

    /**
     * cmethod creates singleton
     *
     * @return mixed
     */
    public static function destruct()
    {
        self::$oReporting = null;
    }
}
