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

/**
 * declare Dynamic tags Exception class
 */

namespace FacebookProductAd\Pixel;

if (!defined('_PS_VERSION_')) {
    exit;
}
use FacebookProductAd\Configuration\moduleConfiguration;

class BT_DynPixelException extends \Exception
{
}

abstract class basePixel
{
    /**
     * @var string : stock tag type name
     */
    public static $sName = '';

    /**
     * @var string : character used for tagging values
     */
    public static $sQuote = '\'';

    /**
     * @var string : character used for open complex tag
     */
    public static $sOpenTag = '[';

    /**
     * @var string : character used for open complex tag
     */
    public static $sCloseTag = ']';

    /**
     * @var array : current page information
     */
    public $aPageInfo = [];

    /**
     * @var bool : current object valid or not
     */
    public $bValid = false;

    /**
     * @var string : current object valid or not
     */
    public $sTrakingType = '';

    /**
     * @var string : type of content ( see FPA_AUDIENCE_TYPE to have allow values )
     */
    public $sContent_type;

    /**
     * @var string : the content ids for the tag
     */
    public $sContent_ids;

    /**
     * @var string : content the name
     */
    public $sContent_name;

    /**
     * @var string : content cotent category path
     */
    public $sContent_Category;

    /**
     * @var float : the float value ex price
     */
    public $fValue;

    /**
     * @var string : the currency
     */
    public $sCurrency;

    /**
     * @var string : the search result
     */
    public $sQuerySearch;

    /**
     * @var array : get the JS params for some pages need to include JS code
     */
    public $aJsParams = [];

    /**
     * @var string : the JS code
     */
    public $sJsCode = '';

    /**
     * @var array : current products
     */
    public $aProducts = [];

    /**
     * get params keys
     *
     * @param array $aParams
     */
    abstract public function __construct(array $aParams);

    /**
     * method set content type
     */
    abstract public function setTrackingType();

    /**
     * method set content type
     */
    abstract public function setContentType();

    /**
     * method set ContentIds
     */
    abstract public function setContentIds();

    /**
     * method set content name
     */
    abstract public function setContentName();

    /**
     * setCategory() method set Content Categoru
     */
    abstract public function setContentCategory();

    /**
     * method set value like a price
     */
    abstract public function setValue();

    /**
     * method set currency
     */
    abstract public function setCurrency();

    /**
     * method set query search
     */
    abstract public function setQuerySearch();

    /**
     * method set values
     *
     * @return mixed tags type abstract type
     */
    public function set()
    {
        // set tracking type
        $this->setTrackingType();

        // set content type
        $this->setContentType();

        // set Content ids
        $this->setContentIds();

        // set Content name
        $this->setContentName();

        // set the content category
        $this->setContentCategory();

        // set price value
        $this->setValue();

        // set the currency
        $this->setCurrency();

        // set the query search
        $this->setQuerySearch();
    }

    /**
     * method display properties
     *
     * @return array of properties + labels
     */
    public function display()
    {
        $aProperties = [];

        if (!empty($this->sTrakingType)) {
            $aProperties['tracking_type'] = ['label' => 'tracking_type', 'value' => $this->sTrakingType];
        }

        if (!empty($this->sContent_type)) {
            $aProperties['content_type'] = ['label' => 'content_type', 'value' => $this->sContent_type];
        }

        if (!empty($this->sContent_ids)) {
            $aProperties['content_ids'] = ['label' => 'content_ids', 'value' => $this->sContent_ids];
        }

        $aProperties['value'] = ['label' => 'value', 'value' => number_format($this->fValue, 2, '.', '')];

        if (!empty($this->sQuerySearch)) {
            $aProperties['search_string'] = ['label' => 'search_string', 'value' => $this->sQuerySearch];
        }

        if (!empty($this->sCurrency)) {
            $aProperties['currency'] = ['label' => 'currency', 'value' => $this->sCurrency];
        }

        if (!empty($this->sContent_name)) {
            $aProperties['content_name'] = ['label' => 'content_name', 'value' => $this->sContent_name];
        }

        if (!empty($this->sContent_Category)) {
            $aProperties['content_category'] = [
                'label' => 'content_category',
                'value' => $this->sContent_Category,
            ];
        }

        if (!empty($this->sJsCode)) {
            $aProperties['js_code'] = ['label' => 'js_code', 'value' => $this->sJsCode];
        }

        return $aProperties;
    }

    /**
     * method instantiate matched connector object
     *
     * @param string $sTagsType
     * @param array $aParams
     *
     * @return mixed tags type abstract type
     *
     */
    public static function get($sTagsType, array $aParams = null)
    {
        try {
            // only call on predefined events
            if (in_array($sTagsType, array_keys(moduleConfiguration::FPA_TAGS_TYPE))) {
                if ($sTagsType == 'home') {
                    return new pixelHome($aParams);
                } elseif ($sTagsType == 'bestsales') {
                    return new pixelBestSales($aParams);
                } elseif ($sTagsType == 'category') {
                    return new pixelCategory($aParams);
                } elseif ($sTagsType == 'search') {
                    return new pixelSearch($aParams);
                } elseif ($sTagsType == 'promotion') {
                    return new pixelPromotion($aParams);
                } elseif ($sTagsType == 'product') {
                    return new pixelProduct($aParams);
                } elseif ($sTagsType == 'other') {
                    return new pixelOther($aParams);
                } elseif ($sTagsType == 'newproducts') {
                    return new pixelNewProducts($aParams);
                } elseif ($sTagsType == 'manufacturer') {
                    return new pixelManufacturer($aParams);
                } elseif ($sTagsType == 'cart') {
                    return new pixelCart($aParams);
                } elseif ($sTagsType == 'checkout') {
                    return new pixelCheckout($aParams);
                } elseif ($sTagsType == 'purchase') {
                    return new pixelPurchase($aParams);
                } elseif ($sTagsType == 'contact') {
                    return new pixelContact($aParams);
                }
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
        }
    }
}
