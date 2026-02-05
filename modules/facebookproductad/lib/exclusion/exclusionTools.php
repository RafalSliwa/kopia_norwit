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
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Models\advancedExclusion;
use FacebookProductAd\Models\tmpRules;
use FacebookProductAd\ModuleLib\moduleTools;

class exclusionTools
{
    /**
     * extract the option data do add it on tmp rules tables
     *
     * @param array $aData
     * @param bool $bNeedUpdate
     *
     * @return mixed
     */
    public static function extractTmpRulesData($aData, $bNeedUpdate)
    {
        $aOutputData = [];

        switch ($aData['sTypeValue']) {
            case 'word': // use case - rules based on word
                $aOutputData = [
                    'filter_1' => $aData['sWordType'],
                    'filter_2' => $aData['sWordValue'],
                ];

                break;
            case 'feature': // use case - rules based on feature
                $aOutputData = [
                    'filter_1' => $aData['sFeature'],
                    'filter_2' => $aData['sFeatureValue'],
                ];

                break;
            case 'attribute': // use case - rules based on attribute
                $aOutputData = [
                    'filter_1' => $aData['sAttribute'],
                    'filter_2' => $aData['sAttributeValue'],
                ];

                break;
            case 'specificProduct': // use case - rules based on attribute
                $aProductIds = [];
                $sExcludedIds = $aData['sProductIds'];
                $aExcludedIds = !empty($sExcludedIds) ? explode(',', $sExcludedIds) : [];

                if (!empty($aExcludedIds)) {
                    array_pop($aExcludedIds);
                }

                // Loop to manage product ids
                foreach ($aExcludedIds as $sProductId) {
                    list($iProdId, $iAttrId) = explode('¤', $sProductId);
                    $aProductIds[$iProdId] = $iAttrId;
                }

                $aOutputData = [
                    'filter_1' => $aProductIds,
                ];

                break;
            case 'supplier': // use case - rules based on supplier
                $aProductIds = explode(',', $aData['aSuppliers']);
                $aOutputData = [
                    'filter_2' => $aProductIds,
                ];

                break;
            default:
                break;
        }

        // Use case when we don't use delete tmp rules
        if (!empty($bNeedUpdate)) {
            tmpRules::addTmpRules(\FacebookProductAd::$iShopId, $aData['sTypeValue'], moduleTools::handleSetConfigurationData($aOutputData));
        }

        $aTmpRules = tmpRules::getTmpRules();

        return $aTmpRules;
    }

    /**
     * get the good label for rules
     *
     * @param string $sData
     *
     * @return mixed
     */
    public static function getRulesLabel($sData)
    {
        $sLang = (\FacebookProductAd::$sCurrentLang == 'en' || \FacebookProductAd::$sCurrentLang == 'fr' || \FacebookProductAd::$sCurrentLang == 'es' || \FacebookProductAd::$sCurrentLang == 'it') ? \FacebookProductAd::$sCurrentLang : 'en';

        $sRulesName = moduleConfiguration::FPA_RULES_LABEL_TYPE[$sData][$sLang];

        return $sRulesName;
    }

    /**
     * get rules detail
     *
     * @param string $sType
     * @param array $aData
     *
     * @return array
     */
    public static function getRulesDetail($sType, $aData)
    {
        $sLang = (\FacebookProductAd::$sCurrentLang == 'en' || \FacebookProductAd::$sCurrentLang == 'fr' || \FacebookProductAd::$sCurrentLang == 'es' || \FacebookProductAd::$sCurrentLang == 'it') ? \FacebookProductAd::$sCurrentLang : 'en';
        $aOutputData = [];

        if (is_array($aData)) {
            $aProducts = [];
            $aOutputDataSupplierName = [];

            switch ($sType) {
                case 'supplier': // use case - rules based on supplier
                    if (is_array($aData['filter_2']) && !empty($aData['filter_2'])
                    ) {
                        $aProducts = self::getProductFromSuppliers($aData['filter_2'], \FacebookProductAd::$iShopId);
                    }
                    // Get the supplier name for the rules summary display
                    foreach ($aData['filter_2'] as $iSupplierId) {
                        $oSupplier = new \Supplier($iSupplierId);
                        $aOutputDataSupplierName[] = $oSupplier->name;
                    }

                    // Manage the numbers of checked element
                    $aOutputData['sType'] = $sType;
                    $aOutputData['iCheckedTreeElem'] = count($aData['filter_2']);
                    $aOutputData['iNumberOfProducts'] = count($aProducts);
                    $aOutputData['iSupplierId'] = $aData['filter_2'];
                    $aOutputData['aSupplierName'] = $aOutputDataSupplierName;

                    break;
                case 'word': // use case - rules based on word
                    if (
                        is_string($aData['filter_2'])
                        && !empty($aData['filter_2'])
                        && !empty($aData['filter_1'])
                    ) {
                        $aProducts = self::getProductFromWords($aData['filter_1'], $aData['filter_2']);
                    }

                    $aOutputData = [
                        'filter_1' => moduleConfiguration::FPA_RULES_WORD_TYPE[$aData['filter_1']][$sLang],
                        'filter_2' => $aData['filter_2'],
                        'iNumberOfProducts' => count($aProducts),
                    ];

                    break;
                case 'feature': // use case - rules based on feature
                    $aOutputData = [];
                    // Get all features values
                    $aFeaturesValues = \FeatureValue::getFeatureValuesWithLang(
                        \FacebookProductAd::$iCurrentLang,
                        (int) $aData['filter_1']
                    );

                    // Set the 1st filter
                    $aOutputData['filter_1'] = \Feature::getFeature(
                        \FacebookProductAd::$iCurrentLang,
                        (int) $aData['filter_1']
                    )['name'];

                    // Search the good value nane
                    foreach ($aFeaturesValues as $aFeaturesValue) {
                        if ($aFeaturesValue['id_feature_value'] == $aData['filter_2']) {
                            $aOutputData['filter_2'] = $aFeaturesValue['value'];
                        }
                    }
                    $aOutputData['sType'] = $sType;
                    $aOutputData['iNumberOfProducts'] = count(moduleDao::getProductIdsByFeature((int) $aData['filter_2']));

                    break;
                case 'attribute': // use case - rules based on attribute
                    $aAttributes = \AttributeGroup::getAttributesGroups(\FacebookProductAd::$iCurrentLang);

                    if (empty(\FacebookProductAd::$bCompare80)) {
                        $aAttributesValues = \Attribute::getAttributes(\FacebookProductAd::$iCurrentLang);
                    } else {
                        $aAttributesValues = \ProductAttribute::getAttributes(\FacebookProductAd::$iCurrentLang);
                    }

                    foreach ($aAttributes as $aAttribute) {
                        if ($aAttribute['id_attribute_group'] == $aData['filter_1']) {
                            $aOutputData['filter_1'] = $aAttribute['public_name'];
                        }
                    }

                    foreach ($aAttributesValues as $aAttributesValue) {
                        if ($aAttributesValue['id_attribute'] == $aData['filter_2']) {
                            $aOutputData['filter_2'] = $aAttributesValue['name'];
                        }
                    }
                    $aOutputData['sType'] = $sType;
                    $aOutputData['iNumberOfProducts'] = count(moduleDao::getProductsIdFromAttribute((int) $aData['filter_2']));

                    break;
                case 'specificProduct': // use case - rules based on specific product
                    $aOutputData['sType'] = $sType;
                    $aOutputData['iNumberOfProducts'] = count($aData['filter_1']);

                    break;
                default:
                    break;
            }
        }

        return $aOutputData;
    }

    /**
     * get the product according to the rules filter values
     *
     * @param $iRuleId
     * @param $sComeFrom
     *
     * @return array
     */
    public static function getProductFromRules($iRuleId = 0, $sComeFromList = false)
    {
        // To stock the product ids from rules condition
        $aProductIdsToExclude = [];
        $aExcludedFromRule = [];

        // If we make update from list of rules for the update
        if (!empty($sComeFromList)) {
            $aRulesData = advancedExclusion::getRules();

            foreach ($aRulesData as $aRuleData) {
                if ($aRuleData['id'] == $iRuleId) {
                    $aFilterValues = is_string($aRuleData['exclusion_value']) ? moduleTools::handleGetConfigurationData($aRuleData['exclusion_value']) : $aRuleData['exclusion_value'];
                    $aExcludedFromRule[] = $aFilterValues['aProductIds'];
                }
            }

            foreach ($aExcludedFromRule as $sKey => $aProductData) {
                foreach ($aProductData as $aData) {
                    if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                        $aProductIdsToExclude[] = $aData['id_product'];
                    } else {
                        $aProductIdsToExclude[] = [
                            'id_product' => $aData['id_product'],
                            'id_product_attribute' => $aData['id_product_attribute'],
                        ];
                    }
                }
            }
        } else {
            $aRules = tmpRules::getTmpRules();

            if (!empty($aRules)) {
                foreach ($aRules as $sKey => $aRule) {
                    // Get the filter values
                    $aFilterValues = is_string($aRule['exclusion_values']) ? moduleTools::handleGetConfigurationData($aRule['exclusion_values']) : $aRule['exclusion_values'];

                    // Use case on supplier
                    if ($aRule['type'] == 'supplier') {
                        $aProductIds = self::getProductFromSuppliers($aFilterValues['filter_2'], \FacebookProductAd::$iShopId);

                        if (!empty($aProductIds)) {
                            foreach ($aProductIds as $aProductId) {
                                // Use case for exportation without the combination
                                if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                                    $aProductIdsToExclude[] = $aProductId['id_product'];
                                } else {
                                    $oProduct = new \Product($aProductId['id_product'], true, \FacebookProductAd::$iCurrentLang);
                                    $aAttributes = $oProduct->getAttributeCombinations(\FacebookProductAd::$iCurrentLang);

                                    if (!empty($aAttributes)) {
                                        foreach ($aAttributes as $aAttribute) {
                                            $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aAttribute['id_product_attribute']] = [
                                                'id_product' => (int)$aProductId['id_product'],
                                                'id_product_attribute' => (int)$aAttribute['id_product_attribute'],
                                            ];
                                        }
                                    } else {
                                        $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aProductId['id_product_attribute']] = [
                                            'id_product' => (int)$aProductId['id_product'],
                                            'id_product_attribute' => (int)$aProductId['id_product_attribute'],
                                        ];
                                    }
                                }
                            }
                        }
                    }

                    // Use case on word
                    if ($aRule['type'] == 'word') {
                        $aProductIds = self::getProductFromWords($aFilterValues['filter_1'], $aFilterValues['filter_2']);

                        if (!empty($aProductIds)) {
                            foreach ($aProductIds as $aProductId) {
                                // Use case for exportation without the combination
                                if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                                    $aProductIdsToExclude[] = [
                                        'id_product' => $aProductId['id_product'],
                                        'id_product_attribute' => 0,
                                    ];
                                } else {
                                    $oProduct = new \Product($aProductId['id_product'], true, \FacebookProductAd::$iCurrentLang);
                                    $aAttributes = $oProduct->getAttributeCombinations(\FacebookProductAd::$iCurrentLang);

                                    if (!empty($aAttributes)) {
                                        foreach ($aAttributes as $aAttribute) {
                                            $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aAttribute['id_product_attribute']] = [
                                                'id_product' => (int)$aProductId['id_product'],
                                                'id_product_attribute' => (int)$aAttribute['id_product_attribute'],
                                            ];
                                        }
                                    } else {
                                        $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aProductId['id_product_attribute']] = [
                                            'id_product' => (int)$aProductId['id_product'],
                                            'id_product_attribute' => (int)$aProductId['id_product_attribute'],
                                        ];
                                    }
                                }
                            }
                        }
                    }

                    // Use case on feature
                    if ($aRule['type'] == 'feature') {
                        $aProductIds = moduleDao::getProductIdsByFeature($aFilterValues['filter_2']);

                        if (!empty($aProductIds)) {
                            foreach ($aProductIds as $aProductId) {
                                // Use case for exportation without the combination
                                if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                                    $aProductIdsToExclude[] = (int)$aProductId['id_product'];
                                } else {
                                    $oProduct = new \Product($aProductId['id_product'],true, \FacebookProductAd::$iCurrentLang);
                                    $aAttributes = $oProduct->getAttributeCombinations(\FacebookProductAd::$iCurrentLang);

                                    if (!empty($aAttributes)) {
                                        foreach ($aAttributes as $aAttribute) {
                                            $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aAttribute['id_product_attribute']] = [
                                                'id_product' => $aProductId['id_product'],
                                                'id_product_attribute' => $aAttribute['id_product_attribute'],
                                            ];
                                        }
                                    } else {
                                        $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aProductId['id_product_attribute']] = [
                                            'id_product' => $aProductId['id_product'],
                                            'id_product_attribute' => $aProductId['id_product_attribute'],
                                        ];
                                    }
                                }
                            }
                        }
                    }

                    // Use case on attribute
                    if ($aRule['type'] == 'attribute') {
                        $aProductIds = moduleDao::getProductsIdFromAttribute($aFilterValues['filter_2']);
                        if (!empty($aProductIds)) {
                            foreach ($aProductIds as $aProductId) {
                                // Use case for exportation without the combination
                                if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                                    $aProductIdsToExclude[] = (int)$aProductId['id_product'];
                                } else {
                                    $aProductIdsToExclude[(int)$aProductId['id_product'] . '-' . (int)$aProductId['id_product_attribute']] = [
                                        'id_product' => (int)$aProductId['id_product'],
                                        'id_product_attribute' => (int)$aProductId['id_product_attribute'],
                                    ];
                                }
                            }
                        }
                    }

                    // Use case for specific products
                    if ($aRule['type'] == 'specificProduct') {
                        $aProductIds = $aFilterValues['filter_1'];
                        if (!empty($aProductIds)) {
                            foreach ($aProductIds as $iProductId => $iAttrId) {
                                // Use case for exportation without the combination
                                if (empty(\FacebookProductAd::$conf['FPA_P_COMBOS'])) {
                                    $aProductIdsToExclude[] = [
                                        'id_product' => $iProductId,
                                        'id_product_attribute' => 0,
                                    ];
                                } else {
                                    $aProductIdsToExclude[] = [
                                        'id_product' => $iProductId,
                                        'id_product_attribute' => $iAttrId,
                                    ];
                                }
                            }
                        }
                    }

                    unset($oProduct);
                }
            }
        }

        return $aProductIdsToExclude;
    }

    /**
     * get the products from a or somes suppliers
     *
     * @param array $aSuppliers
     * @param int $ishopId
     */
    public static function getProductFromSuppliers($aSuppliers, $ishopId)
    {
        $sQuery = 'SELECT p.id_product'
            . ' FROM `' . _DB_PREFIX_ . 'product` p '
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'supplier_shop` ss ON p.`id_supplier` = ss.`id_supplier`'
            . ' WHERE ss.`id_supplier` IN (' . implode(',', $aSuppliers) . ')'
            . ' AND ss.`id_shop`=' . $ishopId;

        return \Db::getInstance()->ExecuteS($sQuery);
    }

    /**
     * get the products from a or somes suppliers
     *
     * @param string $sType
     * @param string $sSentences
     */
    public static function getProductFromWords($sType, $sSentences)
    {
        $sQuery = 'SELECT p.id_product'
            . ' FROM `' . _DB_PREFIX_ . 'product` p '
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`';

        if ($sType == 'title') {
            $sQuery .= 'WHERE `name` LIKE  \'%' . \pSQL($sSentences) . '%\'';
        }
        if ($sType == 'description') {
            $sQuery .= 'WHERE `description` LIKE  \'%' . \pSQL($sSentences) . '%\''
                . ' OR `description_short` LIKE  \'%' . \pSQL($sSentences) . '%\'';
        }
        if ($sType == 'both') {
            $sQuery .= 'WHERE `name` LIKE  \'%' . \pSQL($sSentences) . '%\''
                . ' OR `description` LIKE  \'%' . \pSQL($sSentences) . '%\''
                . ' OR `description_short` LIKE  \'%' . \pSQL($sSentences) . '%\'';
        }

        $sQuery .= ' AND pl.id_lang = ' . \FacebookProductAd::$iCurrentLang
            . ' AND pl.id_shop = ' . \FacebookProductAd::$iShopId
            . ' GROUP BY  p.id_product';

        return \Db::getInstance()->ExecuteS($sQuery);
    }
}
