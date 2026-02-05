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

namespace FacebookProductAd\Xml;

if (!defined('_PS_VERSION_')) {
    exit;
}
use FacebookProductAd\Dao\moduleDao;

final class xmlStrategy
{
    /**
     * @var string : store the XML content
     */
    public $sContent = '';

    /**
     * @var array : array of params
     */
    public $aParams = [];

    /**
     * @var string : define the separator
     */
    public $sSep = "\n";

    /**
     * @var string : define the tabulation
     */
    public $sTab = "\t";

    /**
     * @var string : define the double separator
     */
    public $sDblSep = "\n\n";

    /**
     * @var string : store file name
     */
    public $sFileName = '';

    /**
     * @var int : count the number of product processed
     */
    public $iCounter = 0;

    /**
     * @var xmlStrategy : store the current obj to handle
     */
    protected $oCurrentObj;

    /**
     * @var mixed : store the file object
     */
    protected $oFile;

    /**
     * @var bool : define the export mode
     */
    protected $bExport;

    /**
     * @var bool : define if we display directly the content
     */
    protected $bOutput;

    /**
     * @var mixed : store currency / shipping / zone / carrier
     */
    public $data;

    /**
     * Magic Method __construct
     *
     * @param array $aParams
     * @param string $sType : define the tpy of the object we need to load for product or combination product
     */
    public function __construct(array $aParams = [], $sType = null)
    {
        $this->data = new \stdClass();
        $this->sContent = '';
        $this->aParams = $aParams;
        $this->iCounter = 0;
        $this->bExport = isset($aParams['bExport']) ? $aParams['bExport'] : 0;
        $this->bOutput = isset($aParams['bOutput']) ? $aParams['bOutput'] : 0;

        if ($sType !== null) {
            $this->oCurrentObj = $this->get($sType, $aParams);
        }
    }

    /**
     * Magic Method __destruct
     */
    public function __destruct()
    {
    }

    /**
     * header() method set the XML header
     *
     * @return mixed
     */
    public function header()
    {
        // get meta
        $aMeta = \Meta::getMetaByPage('index', (int) $this->aParams['iLangId']);

        $sContent = ''
            . '<?xml version="1.0" encoding="UTF-8"?>' . $this->sSep
            . '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . $this->sSep
            . '<channel>' . $this->sSep
            . "\t" . '<title><![CDATA[' . stripslashes(\Configuration::get('PS_SHOP_NAME')) . ']]></title>' . $this->sSep
            . "\t" . '<description><![CDATA[' . stripslashes($aMeta['description']) . ']]></description>' . $this->sSep
            . "\t" . '<link>' . $this->aParams['sFpaLink'] . '</link>' . $this->sSep;

        unset($aMeta);

        if (!empty($this->bOutput)) {
            echo $sContent;
        } else {
            $this->sContent .= $sContent;
        }
        unset($sContent);

        return true;
    }

    /**
     * footer() method set the XML footer
     *
     * @return mixed
     */
    public function footer()
    {
        $sContent = ''
            . '</channel>' . $this->sSep
            . '</rss>';

        if (!empty($this->bOutput)) {
            echo $sContent;
        } else {
            $this->sContent .= $sContent;
        }
        unset($sContent);

        return true;
    }

    /**
     * setFile() method set the File obj
     *
     * @param mixed $oFile
     *
     * @return mixed
     */
    public function setFile($oFile)
    {
        $this->oFile = $oFile;
    }

    /**
     * write() method write the XML file content
     *
     * @param string $sFileName
     * @param string $sContent
     * @param bool $bVerbose - display comments
     * @param bool $bAdd - adding data
     * @param bool $bStripTag - strip all HTML tags
     *
     * @return mixed
     */
    public function write($sFileName, $sContent, $bVerbose = false, $bAdd = false, $bStripTag = false)
    {
        if (empty($this->bOutput)) {
            $this->oFile->write($sFileName, $sContent, $bVerbose, $bAdd, $bStripTag);
        }

        return true;
    }

    /**
     * delete() method delete XML file
     *
     * @param string $sFileName
     *
     * @return mixed
     */
    public function delete($sFileName)
    {
        return is_file($sFileName) && $this->oFile->delete($sFileName) ? true : false;
    }

    /**
     * method load Products for XML
     * bool $bExportCombination
     * bool $bExcludedProduct
     */
    public function loadProduct($bExportCombination = false, $bExcludedProduct = false)
    {
        $this->data->currencyId = \Currency::getIdByIsoCode(\Tools::strtolower($this->aParams['sCurrencyIso']));
        $this->data->currency = new \stdClass();
        $this->data->currency = new \Currency($this->data->currencyId);

        // store the current carrier
        $this->data->currentCarrier = new \stdClass();
        if (!empty(\FacebookProductAd::$conf['FPA_SHIP_CARRIERS'][\Tools::strtoupper($this->aParams['sCountryIso'])])) {
            $carrier = new \Carrier((int) \FacebookProductAd::$conf['FPA_SHIP_CARRIERS'][\Tools::strtoupper($this->aParams['sCountryIso'])]);

            if ((int) $carrier->id == (int) $carrier->id_reference) {
                $this->data->currentCarrier = $carrier;
            } else {
                $carrier_updated = \Carrier::getCarrierByReference($carrier->id_reference);
                $this->data->currentCarrier = $carrier_updated;
            }
        }
        $this->data->countryId = \Country::getByIso($this->aParams['sCountryIso']);
        $this->data->currentZone = new \stdClass();
        $this->data->currentZone = new \Zone((int) \Country::getIdZone((int) $this->data->countryId));
        $this->data->shippingConfig = \Configuration::getMultiple(['PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_FREE_WEIGHT', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD']);

        // check version
        \Context::getContext()->currency = new \Currency((int) $this->data->currencyId);

        return moduleDao::getProductIds($this->aParams['iShopId'], $this->bExport, false, $this->aParams['iFloor'], $this->aParams['iStep'], $bExportCombination, $bExcludedProduct);
    }

    /**
     * hasCombination() method check if combinations and return them
     *
     * @param int $iProdId
     * @param bool $bExcludedProduct
     */
    public function hasCombination($iProdId, $bExcludedProduct)
    {
        // check if combinations
        return $this->oCurrentObj->hasCombination($iProdId, $bExcludedProduct);
    }

    /**
     * getProcessedProduct() method the number of products processed
     *
     * @return int
     */
    public function getProcessedProduct()
    {
        return (int) $this->iCounter;
    }

    /**
     * buildProductXml() method construct the XML content
     *
     * @param mixed $oData
     * @param \Product $oProduct
     * @param array $aCombination
     */
    public function buildProductXml(&$oData, $oProduct, $aCombination)
    {
        // load the product and combination into the matching object
        $this->oCurrentObj->setProductData($oData, $oProduct, $aCombination);

        // build the common and specific part between different type of export
        if ($this->oCurrentObj->buildProductXml($oData, $oProduct, $aCombination)) {
            if (!empty($this->bOutput)) {
                // echo the output
                echo $this->oCurrentObj->buildXmlTags();
            } else {
                $this->sContent .= $this->oCurrentObj->buildXmlTags();
            }

            if ($this->oCurrentObj->hasProductProcessed()) {
                ++$this->iCounter;
            }
        }
    }

    /**
     * get() method instantiate matched product object
     *
     * @param string $sProductType
     * @param array $aParams
     *
     * @return mixed ctrl type
     *
     */
    public function get($sProductType, array $aParams = null)
    {
        $sClassName = 'xml' . ucfirst(strtolower($sProductType));

        if ($sClassName == 'xmlProduct') {
            $this->oCurrentObj = new xmlProduct($aParams);
        }

        if ($sClassName == 'xmlCombination') {
            $this->oCurrentObj = new xmlCombination($aParams);
        }
    }

    /**
     * create() method creates singleton
     *
     * @param string $sType
     * @param array $aParams
     *
     * @return mixed
     */
    public static function create($sType, array $aParams = [])
    {
        static $oXml;

        if (null === $oXml) {
            $oXml = new xmlStrategy($aParams, $sType);
        }

        return $oXml;
    }
}
