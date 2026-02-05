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

namespace FacebookProductAd\Exclusion;

if (!defined('_PS_VERSION_')) {
    exit;
}
use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Models\advancedExclusion;
use FacebookProductAd\ModuleLib\moduleTools;

class exclusionRender
{
    // the current lang
    private $iLang = '';

    /**
     * method display all configured data admin tabs
     *
     * @param string $sType => define which method to execute
     * @param array $aParam
     *
     * @return mixed
     */
    public function render($sType, $aParam = [], $aDataRules = null)
    {
        $this->iLang = \FacebookProductAd::$iCurrentLang;

        if (!empty($sType)) {
            return call_user_func_array([$this, 'render' . ucfirst($sType)], [$aParam, $aDataRules]);
        }
    }

    /**
     * method return the suppliers values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderSupplier($aParam = null, $aDataRules = null)
    {
        $aSuppliers = \Supplier::getSuppliers();

        $aIndexedSuppliers = [];
        $iRuleId = \Tools::getValue('iRuleId');

        if (!empty($iRuleId)) {
            $aData = advancedExclusion::getRulesById((int) $iRuleId);
            $aRuleData = moduleTools::handleGetConfigurationData($aData['exclusion_value']);
            $aIndexedSuppliers = $aRuleData['exclusionData'];
        }

        $aFirst = [];

        $aData['aFormatSuppliers'] = moduleTools::recursiveSupplierTree($aSuppliers, $aIndexedSuppliers);

        if (empty($aData['aFormatSuppliers'])) {
            $aData['bNoSupplierMessage'] = 1;
        }

        return $aData;
    }

    /**
     * method return the word values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderWord($aParam = null, $aDataRules = null)
    {
        $aData = [];

        if (!empty($aDataRules)) {
            // get the exclusion value one
            $aTmpData = moduleTools::handleGetConfigurationData($aDataRules['exclusion_value']);
            $aData['sExclusionOn'] = $aTmpData['exclusionOn'];
            $aData['iExclusionData'] = $aTmpData['exclusionData'];
            $aData['bDisplayField'] = false;
        }

        // To manage the refresh element on the form
        $bRefresh = !empty($aParam['bRefresh']) ? true : false;

        if (empty($bRefresh)) {
            $aData['aWordExlusionTypeWord'] = moduleConfiguration::FPA_EXCLUSION_TYPE_WORD;
        } else {
            $aData['bDisplayField'] = true;
        }

        return $aData;
    }

    /**
     * method return the feature values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderFeature($aParam = null, $aDataRules = null)
    {
        $aData = [];

        if (!empty($aDataRules)) {
            // get the exclusion value one
            $aTmpData = moduleTools::handleGetConfigurationData($aDataRules['exclusion_value']);
            $aData['iExclusionData'] = $aTmpData['exclusionData'];
        }

        // To manage the refresh element on the form
        $bRefresh = !empty($aParam['bRefresh']) ? true : false;

        if (empty($bRefresh)) {
            $aData['aFeatures'] = \Feature::getFeatures($this->iLang);
        } else {
            $aFeatureData = !empty($aParam['iFeatureId']) ? \FeatureValue::getFeatureValuesWithLang(
                $this->iLang,
                (int) $aParam['iFeatureId']
            ) : [];
            $aData['aFeaturesValues'] = $aFeatureData;
            $aData['bEmptyFeatureValue'] = empty($aFeatureData) ? true : false;
        }

        return $aData;
    }

    /**
     * method return the attribute values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderAttribute($aParam = null, $aDataRules = null)
    {
        $aData = [];

        if (!empty($aDataRules)) {
            // get the exclusion value one
            $aTmpData = moduleTools::handleGetConfigurationData($aDataRules['exclusion_value']);
            $aData['iExclusionData'] = $aTmpData['exclusionData'];
        }

        // To manage the refresh element on the form
        $bRefresh = !empty($aParam['bRefresh']) ? true : false;

        if (empty($bRefresh)) {
            $aData['aAttributes'] = \AttributeGroup::getAttributesGroups($this->iLang);
        } else {
            $aAttributeData = !empty($aParam['iAttributeId']) ? \AttributeGroup::getAttributes(
                $this->iLang,
                (int) $aParam['iAttributeId']
            ) : [];
            $aData['aAttributeValues'] = $aAttributeData;
            $aData['bEmptyAttributeValue'] = empty($aAttributeData) ? true : false;
        }

        return $aData;
    }

    /**
     * method return the word values
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderSpecificProduct($aParam = null, $aDataRules = null)
    {
        $aData = [];

        $aData['bSpecifiqueProduct'] = true;

        return $aData;
    }

    /**
     * method return the current rules configuration
     *
     * @param array $aParam
     *
     * @return array
     */
    private function renderRules($aParam = null)
    {
        $bNeedUpdate = false;
        $aData = [];

        // To force the update rules data when we don't manage the delete
        if (!empty($aParam['sTmpRules']) && empty($aParam['sDelete'])) {
            $bNeedUpdate = true;
        }

        // Use case for the update simuation of the ajax
        if ($aParam['sTmpRules'] == 'false') {
            $bNeedUpdate = false;
        }

        $aData = exclusionTools::extractTmpRulesData($aParam, $bNeedUpdate);
        $aOutputData = [];

        // Format the output data
        foreach ($aData as $sKey => $sValue) {
            $aOutputData[$sKey]['id'] = $sValue['id'];
            $aOutputData[$sKey]['sType'] = $sValue['type'];
            $aOutputData[$sKey]['data'] = exclusionTools::getRulesLabel($sValue['type']);
            $aOutputData[$sKey]['filter'] = exclusionTools::getRulesDetail($sValue['type'], moduleTools::handleGetConfigurationData($sValue['exclusion_values']));

            // Use case to get the attribute id to manage the good values on the product name + combination
            if ($sValue['type'] == 'attribute') {
                $aOutputData[$sKey]['attributeId'] = moduleTools::handleGetConfigurationData($sValue['exclusion_values'])['filter_2'];
            }
        }

        return $aOutputData;
    }

    /**
     * method return the current rules configuration
     *
     * @param array $aParam
     * @param array $aDataRules
     *
     * @return array
     */
    private function renderProducts($aParam = null, $aDataRules = null)
    {
        $aOutputDataProduct = [];

        foreach ($aDataRules as $aDataRule) {
            // For all cases except attribute because the behavior can be different
            if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                $aProducts = array_unique(exclusionTools::getProductFromRules());

                foreach ($aProducts as $sKey => $aProductIds) {
                    // Init product data to get details
                    $oProduct = new \Product((int)$aProductIds['id_product'], true, (int)\FacebookProductAd::$iCurrentLang);
                    if (is_object($oProduct)) {
                        $aOutputDataProduct[$aProductIds['id_product']]['id'] = $oProduct->id;
                        $aOutputDataProduct[$aProductIds['id_product']]['name'] = $oProduct->name;
                    }
                }
            } else {
                $aProducts = (array)exclusionTools::getProductFromRules();
                foreach ($aProducts as $sProductKey => $aProductId) {
                    $oProduct = new \Product((int) $aProductId['id_product'], true, (int) \FacebookProductAd::$iCurrentLang);
                    $aCombinationAttrData = moduleTools::getProductCombinationName($aProductId['id_product_attribute'], \FacebookProductAd::$iCurrentLang, \FacebookProductAd::$iShopId);

                    $aOutputDataProduct[$sProductKey]['id'] = $aProductId['id_product'];
                    $aOutputDataProduct[$sProductKey]['name'] = $oProduct->name . ' - ' . $aCombinationAttrData;
                }
            }
        }

        return $aOutputDataProduct;
    }
}
