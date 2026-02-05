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

use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Dao\customLabelDao;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Models\categoryTaxonomy;
use FacebookProductAd\Models\featureCategoryTag;
use FacebookProductAd\Models\Feeds;
use FacebookProductAd\ModuleLib\moduleReporting;
use FacebookProductAd\ModuleLib\moduleTools;

abstract class baseXml
{
    /**
     * @var bool : define if the product has well added
     */
    protected $bProductProcess = false;

    /**
     * @var array : array of params
     */
    protected $aParams = [];

    /**
     * @var mixed : store currency / shipping / zone / carrier / product data into this obj as properties
     */
    protected $data;

    /**
     * Magic Method __construct
     *
     * @param array $aParams
     */
    protected function __construct(array $aParams = null)
    {
        $this->aParams = $aParams;
        $this->data = new \stdClass();
    }

    /**
     *method load products combination
     *
     * @param int $iProductId
     *
     * @return array
     */
    abstract public function hasCombination($iProductId);

    /**
     * method build product XML tags
     *
     * @return array
     */
    abstract public function buildDetailProductXml();

    /**
     * method get images of one product or one combination
     *
     * @param \Product $oProduct
     * @param int $iProdAttributeId
     *
     * @return array
     */
    abstract public function getImages(\Product $oProduct, $iProdAttributeId = null);

    /**
     * method get supplier reference
     *
     * @param int $iProdId
     * @param int $iSupplierId
     * @param string $sSupplierRef
     * @param string $sProductRef
     * @param int $iProdAttributeId
     * @param string $sCombiSupplierRef
     * @param string $sCombiRef
     *
     * @return mixed
     */
    abstract public function getSupplierReference(
        $iProdId,
        $iSupplierId,
        $sSupplierRef = null,
        $sProductRef = null,
        $iProdAttributeId = null,
        $sCombiSupplierRef = null,
        $sCombiRef = null
    );

    /**
     * method format the product name
     *
     * @param int $iAdvancedProdName
     * @param int $iAdvancedProdTitle
     * @param string $sProdName
     * @param string $sCatName
     * @param string $sManufacturerName
     * @param int $iLength
     * @param int $iProdAttrId
     *
     * @return mixed
     */
    abstract public function formatProductName(
        $iAdvancedProdName,
        $iAdvancedProdTitle,
        $sProdName,
        $sCatName,
        $sManufacturerName,
        $iLength,
        $iProdAttrId = null
    );

    /**
     * method store into the matching object the product and combination
     *
     * @param obj $oData
     * @param \Product $oProduct
     * @param array $aCombination
     *
     * @return mixed
     */
    public function setProductData(&$oData, $oProduct, $aCombination)
    {
        $this->data = $oData;
        $this->data->p = $oProduct;
        $this->data->c = $aCombination;
    }

    /**
     * method define if the current product has been processed or refused for some not requirements matching
     *
     * @return mixed
     */
    public function hasProductProcessed()
    {
        return $this->bProductProcess;
    }

    /**
     * method build common product XML tags
     *
     * @return mixed
     */
    public function buildProductXml()
    {
        // reset the current step data obj
        $this->data->step = new \stdClass();

        // define the product Id for reporting
        $this->data->step->attrId = !empty($this->data->c['id_product_attribute']) ? $this->data->c['id_product_attribute'] : 0;
        $this->data->step->id_reporting = $this->data->p->id . '_' . (!empty($this->data->c['id_product_attribute']) ? $this->data->c['id_product_attribute'] : 0);

        // check if there is an excluded products list
        if (!empty($this->aParams['excluded'])) {
            if ((isset($this->aParams['excluded'][$this->data->p->id])
                    && $this->data->step->attrId != 0
                    && in_array($this->data->step->attrId, $this->aParams['excluded'][$this->data->p->id]))
                || (isset($this->aParams['excluded'][$this->data->p->id])
                    && $this->data->step->attrId == 0
                    && in_array($this->data->step->attrId, $this->aParams['excluded'][$this->data->p->id]))
                || (isset($this->aParams['excluded'][$this->data->p->id])
                    && $this->data->step->attrId != 0
                    && !in_array($this->data->step->attrId, $this->aParams['excluded'][$this->data->p->id]))
            ) {
                moduleReporting::create()->set('excluded', ['productId' => $this->data->step->id_reporting]);

                return false;
            }
        }

        // check qty , export type and the product name, available for order
        if (
            !isset($this->data->p->available_for_order)
            || (isset($this->data->p->available_for_order)
                && $this->data->p->available_for_order == 1)
        ) {
            if (!empty($this->data->p->name)) {
                if (
                    (int) $this->data->p->quantity > 0
                    || (int) \FacebookProductAd::$conf['FPA_EXPORT_OOS'] == 1
                ) {
                    // get  the product category object
                    $this->data->step->category = new \Category(
                        (int) $this->data->p->id_category_default,
                        (int) $this->aParams['iLangId']
                    );

                    // set the product ID
                    $this->data->step->id = $this->data->p->id;

                    // Sanitize
                    $this->data->p->name = moduleTools::sanitizeProductProperty($this->data->p->name, $this->aParams['iLangId']);
                    $this->data->p->name = moduleTools::handleExcludedWords($this->data->p->name);

                    // format product name
                    if (empty(\FacebookProductAd::$bAdvancedPack)) {
                        $this->data->step->name = $this->formatProductName(\FacebookProductAd::$conf['FPA_ADV_PRODUCT_NAME'], \FacebookProductAd::$conf['FPA_ADV_PROD_TITLE'], $this->data->p->name, $this->data->step->category->name, $this->data->p->manufacturer_name, moduleConfiguration::FPA_FEED_TITLE_LENGTH, !empty($this->data->c['id_product_attribute']) ? $this->data->c['id_product_attribute'] : null);
                    } else {
                        if (\AdvancedPack::isValidPack($this->data->p->id)) {
                            $this->data->step->name = $this->formatProductName(\FacebookProductAd::$conf['FPA_ADV_PRODUCT_NAME'], \FacebookProductAd::$conf['FPA_ADV_PROD_TITLE'], $this->data->p->name, $this->data->step->category->name, $this->data->p->manufacturer_name, moduleConfiguration::FPA_FEED_TITLE_LENGTH, null);
                        } else {
                            $this->data->step->name = $this->formatProductName(\FacebookProductAd::$conf['FPA_ADV_PRODUCT_NAME'], \FacebookProductAd::$conf['FPA_ADV_PROD_TITLE'], $this->data->p->name, $this->data->step->category->name, $this->data->p->manufacturer_name, moduleConfiguration::FPA_FEED_TITLE_LENGTH, !empty($this->data->c['id_product_attribute']) ? $this->data->c['id_product_attribute'] : null);
                        }
                    }
                    // use case export title with brands in suffix
                    if (
                        \FacebookProductAd::$conf['FPA_ADV_PRODUCT_NAME'] != 0
                        && \Tools::strlen($this->data->step->name) >= (int) moduleConfiguration::FPA_FEED_TITLE_LENGTH
                    ) {
                        moduleReporting::create()->set(
                            'title_length',
                            ['productId' => $this->data->step->id_reporting]
                        );
                    }

                    // Sanitize
                    $this->data->p->description_short = moduleTools::sanitizeProductProperty($this->data->p->description_short, $this->aParams['iLangId']);
                    $this->data->p->description = moduleTools::sanitizeProductProperty($this->data->p->description, $this->aParams['iLangId']);
                    $this->data->p->meta_description = moduleTools::sanitizeProductProperty($this->data->p->meta_description, $this->aParams['iLangId']);

                    // set product description
                    // // tododo get
                    $this->data->step->desc = $this->getProductDesc($this->data->p->description_short, $this->data->p->description, $this->data->p->meta_description);

                    // Add rich text content if we detect HTML content for improve catalogue export
                    if (!empty(moduleTools::isHtml($this->getProductDesc($this->data->p->description_short, $this->data->p->description, $this->data->p->meta_description, true)))) {
                        $this->data->step->rich_desc = $this->getProductDesc($this->data->p->description_short, $this->data->p->description, $this->data->p->meta_description, true);
                    }

                    // use case - reporting if product has no description as the merchant selected as type option
                    if (empty($this->data->step->desc)) {
                        moduleReporting::create()->set('description', ['productId' => $this->data->step->id_reporting]);

                        return false;
                    }

                    // set product URL
                    $this->data->step->url = moduleTools::buildProductUrl($this->data->p, $this->aParams['iLangId'], $this->data->currencyId, $this->aParams['iShopId'], null);

                    // use case - reporting if product has no valid URL
                    if (empty($this->data->step->url)) {
                        moduleReporting::create()->set('link', ['productId' => $this->data->step->id_reporting]);

                        return false;
                    }

                    $this->data->step->url_default = $this->data->step->url;

                    // set the product path
                    $this->data->step->path = $this->getProductPath($this->data->p->id_category_default, $this->aParams['iLangId']);

                    // get the condition
                    $condition = (!empty($this->data->p->condition) ? $this->data->p->condition : null);
                    $this->data->step->condition = moduleTools::getProductCondition($condition);

                    $this->data->step->carrier_tax = true;
                    if (!empty(\FacebookProductAd::$conf['FPA_NO_TAX_SHIP_CARRIERS'])) {
                        if (!empty(\FacebookProductAd::$conf['FPA_NO_TAX_SHIP_CARRIERS'][$this->aParams['sCountryIso']])) {
                            $this->data->step->carrier_tax = false;
                        }
                    }

                    $this->data->step->carrier_free = false;
                    if (!empty(\FacebookProductAd::$conf['FPA_FREE_SHIP_CARRIERS'])) {
                        if (!empty(\FacebookProductAd::$conf['FPA_FREE_SHIP_CARRIERS'][$this->aParams['sCountryIso']])) {
                            $this->data->step->carrier_free = true;
                        }
                    }

                    $this->data->step->carrier_product_price_free = 0;
                    if (!empty(\FacebookProductAd::$conf['FPA_FREE_PROD_PRICE_SHIP_CARRIERS'])) {
                        if (!empty(\FacebookProductAd::$conf['FPA_FREE_PROD_PRICE_SHIP_CARRIERS'][$this->aParams['sCountryIso']])) {
                            $this->data->step->carrier_product_price_free = floatval(\FacebookProductAd::$conf['FPA_FREE_PROD_PRICE_SHIP_CARRIERS'][$this->aParams['sCountryIso']]);
                        }
                    }

                    // execute the detail part
                    if ($this->buildDetailProductXml()) {
                        // get the default image
                        $this->data->step->image_link = moduleTools::getProductImage($this->data->p, !empty(\FacebookProductAd::$conf['FPA_IMG_SIZE']) ? \FacebookProductAd::$conf['FPA_IMG_SIZE'] : null, $this->data->step->images['image']);

                        // use case - reporting if product has no cover image
                        if (empty($this->data->step->image_link)) {
                            moduleReporting::create()->set(
                                'image_link',
                                ['productId' => $this->data->step->id_reporting]
                            );

                            return false;
                        }

                        if (!empty(\FacebookProductAd::$conf['FPA_ADD_IMAGES'])) {
                            // get additional images
                            if (!empty($this->data->step->images['others']) && is_array($this->data->step->images['others'])) {
                                $this->data->step->additional_images = [];
                                foreach ($this->data->step->images['others'] as $aImage) {
                                    $sExtraImgLink = moduleTools::getProductImage($this->data->p, !empty(\FacebookProductAd::$conf['FPA_IMG_SIZE']) ? \FacebookProductAd::$conf['FPA_IMG_SIZE'] : null, $aImage);
                                    if (!empty($sExtraImgLink)) {
                                        $this->data->step->additional_images[] = $sExtraImgLink;
                                    }
                                }
                            }
                        }

                        // get Facebook Categories
                        $taxonomy_code = Feeds::getFeedTaxonomy($this->aParams['sLangIso'], $this->aParams['sCountryIso'], $this->aParams['sCurrencyIso'], (int) \FacebookProductAd::$iShopId);

                        if (!empty($taxonomy_code)) {
                            $this->data->step->google_cat = categoryTaxonomy::getFacebookCategories($this->aParams['iShopId'], $this->data->p->id_category_default, $taxonomy_code);
                            // Use case for package language problem, and didn't let us identify the taxonomy with the good current lang
                            if (empty($this->data->step->google_cat)) {
                                $this->data->step->google_cat = categoryTaxonomy::getFacebookCategories($this->aParams['iShopId'], $this->data->p->id_category_default, 'en-US');
                            }
                        }
                        // get all product categories
                        $oProduct = new \Product($this->data->p->id);
                        $aProductCategories = $oProduct->getCategories();

                        // get google adwords tags
                        $this->data->step->google_tags = customLabelDao::getTagsForXml($this->data->p->id, $aProductCategories, $this->data->p->id_manufacturer, $this->data->p->id_supplier, (int) $this->aParams['iLangId']);

                        // get features by category
                        $this->data->step->features = featureCategoryTag::getFeaturesByCategory($this->data->p->id_category_default, \FacebookProductAd::$iShopId);

                        // get color options
                        $this->data->step->colors = $this->getColorOptions($this->data->p->id, (int) $this->aParams['iLangId'], !empty($this->data->c['id_product_attribute']) ? $this->data->c['id_product_attribute'] : 0);

                        // get size options
                        $this->data->step->sizes = $this->getSizeOptions($this->data->p->id, (int) $this->aParams['iLangId'], !empty($this->data->c['id_product_attribute']) ? $this->data->c['id_product_attribute'] : 0);

                        // get material options
                        if (
                            !empty(\FacebookProductAd::$conf['FPA_INC_MATER'])
                            && !empty($this->data->step->features['material'])
                        ) {
                            $this->data->step->material = $this->getFeaturesOptions($this->data->p->id, $this->data->step->features['material'], (int) $this->aParams['iLangId']);
                        }

                        // get pattern options
                        if (
                            !empty(\FacebookProductAd::$conf['FPA_INC_PATT'])
                            && !empty($this->data->step->features['pattern'])
                        ) {
                            $this->data->step->pattern = $this->getFeaturesOptions($this->data->p->id, $this->data->step->features['pattern'], (int) $this->aParams['iLangId']);
                        }

                        return true;
                    }
                } // use case - reporting if product was excluded due to no_stock
                else {
                    moduleReporting::create()->set(
                        '_no_export_no_stock',
                        ['productId' => $this->data->step->id_reporting]
                    );
                }
            } // use case - reporting if product was excluded due to the empty name
            else {
                moduleReporting::create()->set(
                    '_no_product_name',
                    ['productId' => $this->data->step->id_reporting]
                );
            }
        } // use case - reporting if product isn't available for order
        else {
            moduleReporting::create()->set(
                '_no_available_for_order',
                ['productId' => $this->data->step->id_reporting]
            );
        }

        return false;
    }

    /**
     * method build XML tags from the current stored data
     *
     * @return mixed
     */
    public function buildXmlTags()
    {
        // set vars
        $sContent = '';
        $aReporting = [];

        $this->bProductProcess = false;

        // check if data are ok - 4 data are mandatory to fill the product out
        if (
            !empty($this->data->step)
            && !empty($this->data->step->name)
            && !empty($this->data->step->desc)
            && !empty($this->data->step->url)
            && !empty($this->data->step->image_link)
            && $this->data->step->visibility != 'none'
        ) {
            $sContent .= "\t" . '<item>' . "\n";

            // Build the ID to match with the pixel
            $sContent .= "\t\t" . '<g:id>' . str_replace('\'', '', $this->data->step->id) . '</g:id>' . "\n";
            $sContent .= "\t\t" . '<override><![CDATA[' . \Tools::strtoupper($this->aParams['sCountryIso']) . ']]></override>' . "\n";

            // ****** PRODUCT NAME ******
            $sTitle = $this->data->step->name;
            if (!empty($sTitle)) {
                $sContent .= "\t\t" . '<title><![CDATA[' . moduleTools::cleanUp($this->data->step->name) . ']]></title>' . "\n";
            } else {
                $aReporting[] = 'title';
            }

            // ****** DESCRIPTION ******
            if (!empty($this->data->step->desc)) {
                $sContent .= "\t\t" . '<description><![CDATA[' . $this->data->step->desc . ']]></description>' . "\n";
            } else {
                $aReporting[] = 'description';
            }

            if (!empty($this->data->step->rich_desc)) {
                $sContent .= "\t\t" . '<rich_text_description><![CDATA[' . $this->data->step->rich_desc . ']]></rich_text_description>' . "\n";
            }

            // ****** PRODUCT LINK ******
            if (!empty($this->data->step->url)) {
                $sContent .= "\t\t" . '<link><![CDATA[' . $this->data->step->url . ']]></link>' . "\n";
            } else {
                $aReporting[] = 'link';
            }

            // ****** IMAGE LINK ******
            if (!empty($this->data->step->image_link)) {
                $sContent .= "\t\t" . '<g:image_link><![CDATA[' . $this->data->step->image_link . ']]></g:image_link>' . "\n";
            } else {
                $aReporting[] = 'image_link';
            }

            // ****** PRODUCT CONDITION ******
            $sContent .= "\t\t" . '<g:condition>' . $this->data->step->condition . '</g:condition>' . "\n";

            // ****** ADDITIONAL IMAGES ******
            if (!empty($this->data->step->additional_images)) {
                foreach ($this->data->step->additional_images as $sImgLink) {
                    $sContent .= "\t\t" . '<g:additional_image_link><![CDATA[' . $sImgLink . ']]></g:additional_image_link>' . "\n";
                }
            }

            // ****** PRODUCT TYPE ******
            if (!empty($this->data->step->path)) {
                $sContent .= "\t\t" . '<g:product_type><![CDATA[' . $this->data->step->path . ']]></g:product_type>' . "\n";
            } else {
                $aReporting[] = 'product_type';
            }

            // Use case to use generic taxable an prevent issue with removed tax from Facebook
            $sContent .= "\t\t" . '<g:commerce_tax_category><![CDATA[FB_GENERIC_TAXABLE]]></g:commerce_tax_category>' . "\n";

            // ****** FACEBOOK MATCHING CATEGORY ******
            if (!empty($this->data->step->google_cat['txt_taxonomy'])) {
                $sContent .= "\t\t" . '<g:google_product_category><![CDATA[' . json_decode($this->data->step->google_cat['txt_taxonomy']) . ']]></g:google_product_category>' . "\n";
            } else {
                $aReporting[] = 'google_product_category';
            }

            // ****** FACEBOOK CUSTOM LABELS ******
            if (!empty($this->data->step->google_tags['custom_label'])) {
                foreach ($this->data->step->google_tags['custom_label'] as $sLabel) {
                    $sContent .= "\t\t" . '<g:' . $sLabel['position'] . '><![CDATA[' . $sLabel['value'] . ']]></g:' . $sLabel['position'] . '>' . "\n";
                }
            }

            // ****** PRODUCT AVAILABILITY ******
            if (\FacebookProductAd::$conf['FPA_INC_STOCK'] == 2) {
                if ($this->data->step->quantity > 0) {
                    $sContent .= "\t\t" . '<g:quantity_to_sell_on_facebook>' . (int) $this->data->step->quantity . '</g:quantity_to_sell_on_facebook>' . "\n";

                    if (empty($this->data->step->availabilty_date)) {
                        $sContent .= "\t\t" . '<g:availability>in stock</g:availability>' . "\n";
                    } else {
                        $sContent .= "\t\t" . '<g:availability>in stock</g:availability>' . "\n";
                        $sContent .= "\t\t" . '<g:availability_date>' . moduleTools::formatDateISO8601($this->data->step->availabilty_date) . '</g:availability_date>' . "\n";
                    }
                } else {
                    $sContent .= "\t\t" . '<g:quantity_to_sell_on_facebook>1</g:quantity_to_sell_on_facebook>' . "\n";

                    if (empty($this->data->step->availabilty_date)) {
                        $sContent .= "\t\t" . '<g:availability>in stock</g:availability>' . "\n";
                    } else {
                        $sContent .= "\t\t" . '<g:availability>available for order</g:availability>' . "\n";
                        $sContent .= "\t\t" . '<g:availability_date>' . moduleTools::formatDateISO8601($this->data->step->availabilty_date) . '</g:availability_date>' . "\n";
                    }
                }
            } elseif ($this->data->step->quantity > 0) {
                $sContent .= "\t\t" . '<g:quantity_to_sell_on_facebook>' . (int) $this->data->step->quantity . '</g:quantity_to_sell_on_facebook>' . "\n";

                if (empty($this->data->step->availabilty_date)) {
                    $sContent .= "\t\t" . '<g:availability>in stock</g:availability>' . "\n";
                } else {
                    $sContent .= "\t\t" . '<g:availability>available for order</g:availability>' . "\n";
                    $sContent .= "\t\t" . '<g:availability_date>' . moduleTools::formatDateISO8601($this->data->step->availabilty_date) . '</g:availability_date>' . "\n";
                }
            } else {
                if (empty($this->data->step->availabilty_date)) {
                    $sContent .= "\t\t" . '<g:availability>out of stock</g:availability>' . "\n";
                } else {
                    $sContent .= "\t\t" . '<g:availability>available for order</g:availability>' . "\n";
                    $sContent .= "\t\t" . '<g:availability_date>' . moduleTools::formatDateISO8601($this->data->step->availabilty_date) . '</g:availability_date>' . "\n";
                }
            }

            // ****** PRODUCT PRICES ******
            if ($this->data->step->price_raw < $this->data->step->price_raw_no_discount) {
                $sContent .= "\t\t" . '<g:price>' . $this->data->step->price_no_discount . '</g:price>' . "\n"
                    . "\t\t" . '<g:sale_price>' . $this->data->step->price . '</g:sale_price>' . "\n";
            } else {
                $sContent .= "\t\t" . '<g:price>' . $this->data->step->price . '</g:price>' . "\n";
            }

            // ****** UNIQUE PRODUCT IDENTIFIERS ******
            // ****** GTIN - EAN13 AND UPC ******
            if (!empty($this->data->step->gtin)) {
                $sContent .= "\t\t" . '<g:gtin>' . $this->data->step->gtin . '</g:gtin>' . "\n";
            } else {
                $aReporting[] = 'gtin';
            }

            // ****** MANUFACTURER ******
            if (!empty($this->data->p->manufacturer_name)) {
                $sContent .= "\t\t" . '<g:brand><![CDATA[' . moduleTools::cleanUp($this->data->p->manufacturer_name) . ']]></g:brand>' . "\n";
            } else {
                $aReporting[] = 'brand';
            }

            // ****** MPN ******
            if (!empty($this->data->step->mpn)) {
                $sContent .= "\t\t" . '<g:mpn><![CDATA[' . $this->data->step->mpn . ']]></g:mpn>' . "\n";
            } elseif (empty(\FacebookProductAd::$conf['FPA_INC_ID_EXISTS'])) {
                $aReporting[] = 'mpn';
            }

            // ****** IDENTIFIER EXISTS ******
            if (
                \Tools::strlen($this->data->step->upc) != 12
                && \Tools::strlen($this->data->step->ean13) != 8
                && \Tools::strlen($this->data->step->ean13) != 13
            ) {
                $sContent .= "\t\t" . '<g:identifier_exists>FALSE</g:identifier_exists>' . "\n";
            }

            // ****** APPAREL PRODUCTS ******
            // ****** TAG ADULT ******
            // Use case when the option is activated
            if (!empty(\FacebookProductAd::$conf['FPA_INC_TAG_ADULT'])) {
                // USe case when we use the bulk action mode
                if (empty(\FacebookProductAd::$conf['FPA_USE_ADULT_PRODUCT'])) {
                    // Use case build tag only if we have the data
                    if (!empty($this->data->step->features['adult'])) {
                        $sContent .= "\t\t" . '<g:adult><![CDATA[' . stripslashes(\Tools::strtoupper($this->data->step->features['adult'])) . ']]></g:adult>' . "\n";
                    }
                } else { // use case when we use product feature
                    if (!empty($this->data->step->features['adult_product'])) {
                        $adultFeatureValue = $this->getFeaturesOptions($this->data->p->id, $this->data->step->features['adult_product'], (int) $this->aParams['iLangId']);

                        // Only add the value on data feed if we find a feature value on the product
                        if (!empty($adultFeatureValue)) {
                            $sContent .= "\t\t" . '<g:adult><![CDATA[' . rtrim(stripslashes(\Tools::strtoupper($adultFeatureValue))) . ']]></g:adult>' . "\n";
                        }
                    }
                }
            }

            // ****** TAG GENDER ******
            // Use case when the option is activated
            if (!empty(\FacebookProductAd::$conf['FPA_INC_GEND'])) {
                // USe case when we use the bulk action mode
                if (empty(\FacebookProductAd::$conf['FPA_USE_GENDER_PRODUCT'])) {
                    // Use case build tag only if we have the data
                    if (!empty($this->data->step->features['gender'])) {
                        $sContent .= "\t\t" . '<g:gender><![CDATA[' . stripslashes(\Tools::strtoupper($this->data->step->features['gender'])) . ']]></g:gender>' . "\n";
                    }
                } else { // use case when we use product feature
                    if (!empty($this->data->step->features['gender_product'])) {
                        $genderFeatureValue = $this->getFeaturesOptions($this->data->p->id, $this->data->step->features['gender_product'], (int) $this->aParams['iLangId']);

                        // Only add the value on data feed if we find a feature value on the product
                        if (!empty($genderFeatureValue)) {
                            $sContent .= "\t\t" . '<g:gender><![CDATA[' . rtrim(stripslashes(\Tools::strtoupper($genderFeatureValue))) . ']]></g:gender>' . "\n";
                        }
                    }
                }
            }

            // ****** TAG AGE GROUP ******

            // Use case when the option is activated
            if (!empty(\FacebookProductAd::$conf['FPA_INC_AGE'])) {
                // USe case when we use the bulk action mode
                if (empty(\FacebookProductAd::$conf['FPA_USE_AGEGROUP_PRODUCT'])) {
                    // Use case build tag only if we have the data
                    if (!empty($this->data->step->features['agegroup'])) {
                        $sContent .= "\t\t" . '<g:age_group><![CDATA[' . stripslashes(\Tools::strtoupper($this->data->step->features['agegroup'])) . ']]></g:age_group>' . "\n";
                    }
                } else { // use case when we use product feature
                    if (!empty($this->data->step->features['agegroup_product'])) {
                        $ageGroupFeatureValue = $this->getFeaturesOptions($this->data->p->id, $this->data->step->features['agegroup_product'], (int) $this->aParams['iLangId']);

                        // Only add the value on data feed if we find a feature value on the product
                        if (!empty($ageGroupFeatureValue)) {
                            $sContent .= "\t\t" . '<g:age_group><![CDATA[' . rtrim(stripslashes(\Tools::strtoupper($ageGroupFeatureValue))) . ']]></g:age_group>' . "\n";
                        }
                    }
                }
            }

            // ****** TAG COLOR ******
            if (
                !empty($this->data->step->colors)
                && is_array($this->data->step->colors)
            ) {
                foreach ($this->data->step->colors as $aColor) {
                    $sContent .= "\t\t" . '<g:color><![CDATA[' . stripslashes($aColor['name']) . ']]></g:color>' . "\n";
                }
            } elseif (!empty(\FacebookProductAd::$conf['FPA_INC_COLOR'])) {
                $aReporting[] = 'color';
            }

            // ****** TAG SIZE ******
            if (
                !empty($this->data->step->sizes)
                && is_array($this->data->step->sizes)
            ) {
                foreach ($this->data->step->sizes as $aSize) {
                    $sContent .= "\t\t" . '<g:size><![CDATA[' . stripslashes($aSize['name']) . ']]></g:size>' . "\n";
                }
            } elseif (!empty(\FacebookProductAd::$conf['FPA_INC_SIZE'])) {
                $aReporting[] = 'size';
            }

            // ****** VARIANTS PRODUCTS ******
            // ****** TAG MATERIAL ******
            if (!empty($this->data->step->material)) {
                $sContent .= "\t\t" . '<g:material><![CDATA[' . $this->data->step->material . ']]></g:material>' . "\n";
            } elseif (!empty(\FacebookProductAd::$conf['FPA_INC_MATER'])) {
                $aReporting[] = 'material';
            }

            // ****** TAG PATTERN ******
            if (!empty($this->data->step->pattern)) {
                $sContent .= "\t\t" . '<g:pattern><![CDATA[' . $this->data->step->pattern . ']]></g:pattern>' . "\n";
            } elseif (!empty(\FacebookProductAd::$conf['FPA_INC_PATT'])) {
                $aReporting[] = 'pattern';
            }

            // ****** ITEM GROUP ID ******
            if (!empty($this->data->step->id_no_combo)) {
                $sContent .= "\t\t" . '<g:item_group_id>' . \Tools::strtoupper(\FacebookProductAd::$conf['FPA_ID_PREFIX']) . $this->aParams['sCountryIso'] . '-' . $this->data->step->id_no_combo . '</g:item_group_id>' . "\n";
            }

            // ****** TAX AND SHIPPING ******
            $sWeightUnit = \Configuration::get('PS_WEIGHT_UNIT');
            if (!empty($this->data->step->weight) && !empty($sWeightUnit)) {
                if (in_array($sWeightUnit, moduleConfiguration::FPA_WEIGHT_UNITS)) {
                    $sContent .= "\t\t" . '<g:shipping_weight>' . number_format($this->data->step->weight, 2, '.', '') . ' ' . \Tools::strtolower($sWeightUnit) . '</g:shipping_weight>' . "\n";
                } else {
                    $aReporting[] = 'shipping_weight';
                }
            }

            if (!empty(\FacebookProductAd::$conf['FPA_SHIPPING_USE'])) {
                $sContent .= "\t\t" . '<g:shipping>' . "\n"
                    . "\t\t\t" . '<g:country>' . $this->aParams['sCountryIso'] . '</g:country>' . "\n"
                    . "\t\t\t" . '<g:price>' . $this->data->step->shipping_fees . '</g:price>' . "\n"
                    . "\t\t" . '</g:shipping>' . "\n";
            }

            $sContent .= "\t" . '</item>' . "\n";

            $this->bProductProcess = true;
        } else {
            $aReporting[] = '_no_required_data';
        }

        // execute the reporting
        if (!empty($aReporting)) {
            foreach ($aReporting as $sLabel) {
                moduleReporting::create()->set($sLabel, ['productId' => $this->data->step->id_reporting]);
            }
        }

        return $sContent;
    }

    /**
     * method returns the product path according to the category ID
     *
     * @param int $iProdCatId
     * @param int $iLangId
     *
     * @return mixed
     */
    public function getProductPath($iProdCatId, $iLangId)
    {
        if (is_string(\FacebookProductAd::$conf['FPA_HOME_CAT'])) {
            \FacebookProductAd::$conf['FPA_HOME_CAT'] = moduleTools::handleGetConfigurationData(\FacebookProductAd::$conf['FPA_HOME_CAT']);
        }

        if (
            $iProdCatId == \FacebookProductAd::$conf['FPA_HOME_CAT_ID']
            && !empty(\FacebookProductAd::$conf['FPA_HOME_CAT'][$iLangId])
        ) {
            $sPath = stripslashes(\FacebookProductAd::$conf['FPA_HOME_CAT'][$iLangId]);
        } else {
            $sPath = moduleTools::getProductPath((int) $iProdCatId, (int) $iLangId, '', false);
        }

        return $sPath;
    }

    /**
     * method handle the shipping cost
     *
     * @param float $product_price
     *
     * @return float
     */
    public function getProductShippingFees($product_price)
    {
        // set vars
        $shipping_cost = (float) 0;
        $process = true;

        // Free shipping on price ?
        if (((float) $this->data->shippingConfig['PS_SHIPPING_FREE_PRICE'] > 0) && ((float) $product_price >= (float) $this->data->shippingConfig['PS_SHIPPING_FREE_PRICE'])) {
            $process = false;
        }
        // Free shipping on weight ?
        if (((float) $this->data->shippingConfig['PS_SHIPPING_FREE_WEIGHT'] > 0) && ((float) $this->data->step->weight >= (float) $this->data->shippingConfig['PS_SHIPPING_FREE_WEIGHT'])) {
            $process = false;
        }

        // Only handle shiping cost if don't have free shipping option set to yes
        if (empty($this->data->step->carrier_free)) {
            // only in case of not free shipping weight or price
            if ($process && !empty($this->data->currentCarrier->id)) {
                $shipping_method = ($this->data->currentCarrier->getShippingMethod() == \Carrier::SHIPPING_METHOD_WEIGHT) ? 'weight' : 'price';

                // Get main shipping fee
                if ($shipping_method == 'weight') {
                    $shipping_cost += $this->data->currentCarrier->getDeliveryPriceByWeight($this->data->step->weight, $this->data->currentZone->id);
                } else {
                    $shipping_cost += $this->data->currentCarrier->getDeliveryPriceByPrice($product_price, $this->data->currentZone->id);
                }
                unset($shipping_method);

                // Add handling fees if applicable
                if (!empty($this->data->shippingConfig['PS_SHIPPING_HANDLING']) && !empty($this->data->currentCarrier->shipping_handling)) {
                    $shipping_cost += (float) $this->data->shippingConfig['PS_SHIPPING_HANDLING'];
                }

                // Apply tax
                if (!empty($this->data->step->carrier_tax)) {
                    $carrier_tax = \Tax::getCarrierTaxRate((int) $this->data->currentCarrier->id);
                    $shipping_cost *= (1 + ($carrier_tax / 100));
                }

                // Covert to correct currency and format
                $shipping_cost = \Tools::convertPrice((float) $shipping_cost, $this->data->currency);
                $shipping_cost = number_format((float) $shipping_cost, 2, '.', '') . $this->data->currency->iso_code;
            }
        } else {
            $shipping_cost = number_format((float) $shipping_cost, 2, '.', '') . $this->data->currency->iso_code;
        }

        if ($product_price >= $this->data->step->carrier_product_price_free && $this->data->step->carrier_product_price_free > 0) {
            $shipping_cost = 0;
            $shipping_cost = number_format((float) $shipping_cost, 2, '.', '') . $this->data->currency->iso_code;
        }

        return $shipping_cost;
    }

    /**
     * method returns a cleaned desc string
     *
     * @param string $sShortDesc
     * @param string $sLongDesc
     * @param string $sMetaDesc
     * @param bool $useRich
     *
     * @return mixed
     */
    public function getProductDesc($sShortDesc, $sLongDesc, $sMetaDesc, $useRich = false)
    {
        // set product description
        switch (\FacebookProductAd::$conf['FPA_P_DESCR_TYPE']) {
            case 1:
                $sDesc = !empty($sShortDesc) ? $sShortDesc : '';

                break;
            case 2:
                $sDesc = !empty($sLongDesc) ? $sLongDesc : '';

                break;
            case 3:
                $sDesc = '';
                if (!empty($sShortDesc)) {
                    $sDesc = $sShortDesc;
                }
                if (!empty($sLongDesc)) {
                    $sDesc .= (!empty($sDesc) ? ' ' : '') . $sLongDesc;
                }

                break;
            case 4:
                $sDesc = !empty($sMetaDesc) ? $sMetaDesc : '';

                break;
            default:
                $sDesc = !empty($sLongDesc) ? $sLongDesc : '';

                break;
        }

        if (!empty($sDesc)) {
            if (empty($useRich)) {
                $sDesc = \Tools::substr(moduleTools::cleanUp($sDesc), 0, 4999);
            } else {
                if (!moduleTools::isHtml($sDesc)) {
                    $sDesc = \Tools::substr(moduleTools::cleanUp($sDesc), 0, 4999);
                }
            }

            strlen($sDesc) == 1 ? $sDesc = '' : '';
        }

        return $sDesc;
    }

    /**
     * method returns attributes and features
     *
     * @param int $iProdId
     * @param int $iLangId
     * @param int $iProdAttrId
     *
     * @return array
     */
    public function getColorOptions($iProdId, $iLangId, $iProdAttrId = 0)
    {
        // set
        $aColors = [];
        $iFeature = 0;

        if (!empty(\FacebookProductAd::$conf['FPA_INC_COLOR'])) {
            if (!empty(\FacebookProductAd::$conf['FPA_COLOR_OPT']['attribute'])) {
                $mapAttributes = array_map('intval', \FacebookProductAd::$conf['FPA_COLOR_OPT']['attribute']);
                $sAttributes = implode(',', $mapAttributes);
            }
            if (!empty(\FacebookProductAd::$conf['FPA_COLOR_OPT']['feature'])) {
                $mapFeature = array_map('intval', \FacebookProductAd::$conf['FPA_COLOR_OPT']['feature']);
                $iFeature = implode(',', $mapFeature);
            }
            if (!empty($sAttributes)) {
                $aColors = moduleDao::getProductAttribute((int) $this->data->p->id, (string) $sAttributes, (int) $iLangId, (int) $iProdAttrId);
            }

            // use case - feature selected and not empty
            if (!empty($iFeature)) {
                $sFeature = moduleDao::getProductFeature((int) $this->data->p->id, (int) $iFeature, (int) $iLangId);
                if (!empty($sFeature)) {
                    $aColors[] = ['name' => $sFeature];
                }
                unset($sFeature);
            }
            // clear
            unset($iFeature);
            unset($sAttributes);
        }

        return $aColors;
    }

    /**
     * returns attributes and features
     *
     * @param int $iProdId
     * @param int $iLangId
     * @param int $iProdAttrId
     *
     * @return array
     */
    public function getSizeOptions($iProdId, $iLangId, $iProdAttrId = 0)
    {
        // set
        $aSize = [];
        $sAttributes = 0;
        $iFeature = 0;

        if (!empty(\FacebookProductAd::$conf['FPA_SIZE_OPT'])) {
            if (!empty(\FacebookProductAd::$conf['FPA_SIZE_OPT']['attribute'])) {
                $mapAttributes = array_map('intval', \FacebookProductAd::$conf['FPA_SIZE_OPT']['attribute']);
                $sAttributes = implode(',', $mapAttributes);
            }
            if (!empty(\FacebookProductAd::$conf['FPA_SIZE_OPT']['feature'])) {
                $mapAttributes = array_map('intval', \FacebookProductAd::$conf['FPA_SIZE_OPT']['feature']);
                $sAttributes = implode(',', $mapAttributes);
            }
            if (!empty($sAttributes)) {
                $aSize = moduleDao::getProductAttribute((int) $this->data->p->id, (string) $sAttributes, (int) $iLangId, (int) $iProdAttrId);
            }

            // use case - feature selected and not empty
            if (!empty($sAttributes)) {
                $sFeature = moduleDao::getProductFeature((int) $this->data->p->id, (int) $iFeature, (int) $iLangId);

                if (!empty($sFeature)) {
                    $aSize[] = ['name' => $sFeature];
                }
            }
        }

        return $aSize;
    }

    /**
     * method features for material or pattern
     *
     * @param int $iProdId
     * @param int $iFeatureId
     * @param int $iLangId
     *
     * @return mixed
     */
    public function getFeaturesOptions($iProdId, $iFeatureId, $iLangId)
    {
        // set
        $sFeatureVal = '';
        $aFeatureProduct = \Product::getFeaturesStatic($iProdId);

        if (!empty($aFeatureProduct) && is_array($aFeatureProduct)) {
            foreach ($aFeatureProduct as $aFeature) {
                if ($aFeature['id_feature'] == $iFeatureId) {
                    $aFeatureValues = \FeatureValue::getFeatureValueLang((int) $aFeature['id_feature_value']);

                    foreach ($aFeatureValues as $aFeatureVal) {
                        if ($aFeatureVal['id_lang'] == $iLangId) {
                            $sFeatureVal .= rtrim($aFeatureVal['value']) . ' ';
                        }
                    }
                }
            }
        }

        return $sFeatureVal;
    }
}
