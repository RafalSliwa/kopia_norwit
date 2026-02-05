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
use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Models\advancedExclusion;
use FacebookProductAd\Models\customLabelDynamicBestSales;
use FacebookProductAd\Models\customLabelDynamicCategories;
use FacebookProductAd\Models\customLabelDynamicFeature;
use FacebookProductAd\Models\customLabelDynamicLastProductOrder;
use FacebookProductAd\Models\customLabelDynamicNewProduct;
use FacebookProductAd\Models\customLabelDynamicPriceRange;
use FacebookProductAd\Models\customLabelDynamicProducts;
use FacebookProductAd\Models\customLabelDynamicPromotion;
use FacebookProductAd\Models\customLabelTags;
use FacebookProductAd\Models\exclusionProduct;
use FacebookProductAd\Models\Feeds;
use FacebookProductAd\ModuleLib\moduleTools;

class adminDelete implements adminInterface
{
    /**
     * @param mixed $sType
     * @param array|null $aParam
     *
     * @return mixed
     */
    public function run($sType, $aParam = [])
    {
        // set variables
        $aDisplayData = [];

        switch ($sType) {
            case 'label': // use case - delete custom label
            case 'exclusionRule': // use case - delete exclusion rules label
            case 'feed': // use case - delete exclusion rules label
                // execute match function
                $aDisplayData = call_user_func_array([$this, 'delete' . ucfirst($sType)], [$aParam]);

                break;
            default:
                break;
        }

        return $aDisplayData;
    }

    /**
     * @param array $aPost
     *
     * @return mixed
     */
    private function deleteLabel(array $aPost)
    {
        if (\FacebookProductAd::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }
        // set
        $aData = [];
        $sDeleteType = \Tools::getValue('sDeleteType');
        $bContinu = false;
        $iTagId = 0;
        $aIdsDelete = [];

        try {
            if (!empty($sDeleteType)) {
                if ($sDeleteType == 'one') {
                    $iTagId = \Tools::getValue('iTagId');
                    $bContinu = true;
                } elseif ($sDeleteType == 'bulk') {
                    $aIdsDelete = explode(',', \Tools::getValue('iTagIds'));
                    $bContinu = true;
                }
            }

            if ($bContinu == false) {
                throw new \Exception(\FacebookProductAd::$oModule->l('Your Custom label ID(s) are not valid', 'adminUpdate') . '.', 700);
            } else {
                // include

                if ($sDeleteType == 'one') {
                    customLabelTags::deleteTag($iTagId, moduleConfiguration::FPA_LABEL_LIST);
                    customLabelDynamicProducts::deleteProductTag($iTagId);
                    customLabelDynamicFeature::deleteFeatureSave($iTagId);
                    customLabelDynamicCategories::deleteDynamicCat($iTagId);
                    customLabelDynamicNewProduct::deleteDynamicNew($iTagId);
                    customLabelDynamicBestSales::deleteDynamicBestSales($iTagId);
                    customLabelDynamicPriceRange::deleteDynamicPriceRange($iTagId);
                    customLabelDynamicLastProductOrder::deleteDynamicLastProductOrdered($iTagId);
                    customLabelDynamicPromotion::deleteDynamicPromotion($iTagId);
                } elseif ($sDeleteType == 'bulk') {
                    foreach ($aIdsDelete as $aCurrentClId) {
                        customLabelTags::deleteTag((int)$aCurrentClId, moduleConfiguration::FPA_LABEL_LIST);
                        customLabelDynamicProducts::deleteProductTag((int)$aCurrentClId);
                        customLabelDynamicFeature::deleteFeatureSave((int)$aCurrentClId);
                        customLabelDynamicCategories::deleteDynamicCat((int)$aCurrentClId);
                        customLabelDynamicNewProduct::deleteDynamicNew((int)$aCurrentClId);
                        customLabelDynamicBestSales::deleteDynamicBestSales((int)$aCurrentClId);
                        customLabelDynamicPriceRange::deleteDynamicPriceRange((int)$aCurrentClId);
                        customLabelDynamicLastProductOrder::deleteDynamicLastProductOrdered($iTagId);
                        customLabelDynamicPromotion::deleteDynamicPromotion($iTagId);
                    }
                }
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // force xhr mode
        \FacebookProductAd::$sQueryMode = 'xhr';

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('facebook');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     *  method delete exclusion rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function deleteFeed(array $aPost)
    {
        if (\FacebookProductAd::$sQueryMode == 'xhr') {
            // clean headers
            @ob_end_clean();
        }

        // set
        $aData = [];
        $exportMode = '';

        try {
            $idFeed = \Tools::getValue('id_feed');
            if (!empty($idFeed)) {
                Feeds::deleteFeed($idFeed);
                $exportMode = \Tools::getValue('export_mode');
            }
            // Todo delete
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('feedList');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
            'exportMode' => $exportMode,
        ], $aData);

        return $aDisplay;
    }

    /**
     *  method delete exclusion rules
     *
     * @param array $aPost
     *
     * @return array
     */
    private function deleteExclusionRule(array $aPost)
    {
        // clean headers
        @ob_end_clean();

        // set
        $aData = [];

        try {
            $iRuleId = \Tools::getValue('iRuleId');
            $sType = \Tools::getValue('sDeleteType');

            if (empty($iRuleId) || empty($sType)) {
                throw new \Exception(\FacebookProductAd::$oModule->l('Your rule ID isn\'t valid or the type of deletion is not valide', 'adminUpdate') . '.', 700);
            } else {
                advancedExclusion::deleteExclusionRule($iRuleId, $sType);
                exclusionProduct::deleteRule($iRuleId);
            }
        } catch (\Exception $e) {
            $aData['aErrors'][] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];
        }

        // get configuration options
        moduleTools::getConfiguration();

        // get run of admin display in order to display first page of admin with basics settings updated
        $aDisplay = adminDisplay::create()->run('feed');

        // use case - empty error and updating status
        $aDisplay['assign'] = array_merge($aDisplay['assign'], [
            'bUpdate' => (empty($aData['aErrors']) ? true : false),
        ], $aData);

        return $aDisplay;
    }

    /**
     * method set singleton
     *
     * @return mixed
     */
    public static function create()
    {
        static $oDelete;

        if (null === $oDelete) {
            $oDelete = new adminDelete();
        }

        return $oDelete;
    }
}
