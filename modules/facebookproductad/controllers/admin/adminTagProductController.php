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
if (!defined('_PS_VERSION_')) {
    exit;
}

use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Dao\moduleDao;
use FacebookProductAd\Models\featureCategoryTag;
use FacebookProductAd\ModuleLib\moduleTools;

/**
 * Controller to handle the association between product and tags
 */
class AdminTagProductController extends ModuleAdminController
{
    /**
     * init content
     *
     * @since 1.5.0
     *
     * @return mixed
     */
    public function initContent()
    {
        parent::initContent();

        $aShopCategories = moduleDao::getShopCategories(\FacebookProductAd::$iShopId, \FacebookProductAd::$iCurrentLang, \FacebookProductAd::$conf['FPA_HOME_CAT_ID']);

        foreach ($aShopCategories as &$aCat) {
            // get feature by category Id
            $aFeatures = featureCategoryTag::getFeaturesByCategory($aCat['id_category'], \FacebookProductAd::$iShopId);

            if (!empty($aFeatures)) {
                $aCat['material'] = $aFeatures['material'];
                $aCat['pattern'] = $aFeatures['pattern'];
                $aCat['agegroup'] = $aFeatures['agegroup'];
                $aCat['agegroup_product'] = isset($aFeatures['agegroup_product']) ? $aFeatures['agegroup_product'] : [];
                $aCat['gender'] = $aFeatures['gender'];
                $aCat['gender_product'] = isset($aFeatures['gender_product']) ? $aFeatures['gender_product'] : [];
                $aCat['adult'] = $aFeatures['adult'];
                $aCat['adult_product'] = isset($aFeatures['adult_product']) ? $aFeatures['adult_product'] : [];
            } else {
                $aCat['material'] = '';
                $aCat['pattern'] = '';
                $aCat['agegroup'] = '';
                $aCat['agegroup_product'] = '';
                $aCat['gender'] = '';
                $aCat['gender_product'] = '';
                $aCat['adult'] = '';
                $aCat['adult_product'] = '';
            }
        }

        $tagType = \Tools::getValue('tag');
        $redirectTab = $tagType == 'adult' ? 'adult' : 'appreal';

        $this->context->smarty->assign([
            'aShopCategories' => $aShopCategories,
            'aFeatures' => \Feature::getFeatures(\FacebookProductAd::$iCurrentLang),
            'tagType' => $tagType,
            'useMaterial' => \FacebookProductAd::$conf['FPA_INC_MATER'],
            'usePattern' => \FacebookProductAd::$conf['FPA_INC_PATT'],
            'useGender' => \FacebookProductAd::$conf['FPA_INC_GEND'],
            'useAgegroup' => \FacebookProductAd::$conf['FPA_INC_AGE'],
            'useAdult' => \FacebookProductAd::$conf['FPA_INC_TAG_ADULT'],
            'useGenderProduct' => \FacebookProductAd::$conf['FPA_USE_GENDER_PRODUCT'],
            'useAgeGroupProduct' => \FacebookProductAd::$conf['FPA_USE_AGEGROUP_PRODUCT'],
            'useAdultProduct' => \FacebookProductAd::$conf['FPA_USE_ADULT_PRODUCT'],
            'useTag' => \Tools::getValue('tag'),
            'moduleUrl' => \Context::getContext()->link->getAdminLink('AdminModules') . '&configure=facebookproductad&tab=' . $redirectTab,
            'sModuleName' => moduleConfiguration::FPA_MODULE_SET_NAME,
            'currentTagHandle' => \Tools::getValue('tag'),
            'faqLink' => 'http://faq.businesstech.fr',
        ]);

        $this->context->smarty->assign([
            'content' => $this->content . $this->module->fetch('module:facebookproductad/views/templates/admin/tab/tag.tpl'),
        ]);
    }

    /**
     * Handle add JS dependencies
     *
     * @param bool $isNewTheme
     *
     * @return mixed
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia();

        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/admin.css');
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/bootstrap4.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/tag.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/module.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/feature_by_cat.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/feedList.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/custom_label.js');
    }

    /**
     * Handle the post of the form
     *
     * @return mixed
     */
    public function postProcess()
    {
        if (Tools::isSubmit('save_btn')) {
            $isAdded = false;

            try {
                $tagMode = \Tools::getValue('set_tag_mode');
                $tagType = \Tools::getValue('tag');

                if ($tagMode == 'bulk') {
                    if ($tagType == 'gender') {
                        \Configuration::updateValue('FPA_USE_GENDER_PRODUCT', 0);
                    }

                    if ($tagType == 'agegroup') {
                        \Configuration::updateValue('FPA_USE_AGEGROUP_PRODUCT', 0);
                    }

                    if ($tagType == 'adult') {
                        \Configuration::updateValue('FPA_USE_ADULT_PRODUCT', 0);
                    }
                } elseif ($tagMode == 'product_data') {
                    if ($tagType == 'gender') {
                        \Configuration::updateValue('FPA_USE_GENDER_PRODUCT', 1);
                    }

                    if ($tagType == 'agegroup') {
                        \Configuration::updateValue('FPA_USE_AGEGROUP_PRODUCT', 1);
                    }

                    if ($tagType == 'adult') {
                        \Configuration::updateValue('FPA_USE_ADULT_PRODUCT', 1);
                    }
                }

                $categories = [];

                /* USE CASE - handle all tags configured */
                foreach (moduleConfiguration::FPA_TAG_LIST as $sTagType) {
                    if (!empty(\Tools::getValue($sTagType)) && is_array(\Tools::getValue($sTagType))) {
                        foreach (\Tools::getValue($sTagType) as $iCatId => $mVal) {
                            $categories[$iCatId][$sTagType] = strip_tags($mVal);
                        }
                    }
                }
                // Clean
                featureCategoryTag::cleanTable(\FacebookProductAd::$iShopId);

                if (!empty($categories)) {
                    foreach ($categories as $id_category => $value) {
                        FeatureCategoryTag::$definition['table'] = moduleConfiguration::FPA_CAT_TAG;
                        $feature_category = new FeatureCategoryTag();
                        $feature_category->id_cat = (int) $id_category;
                        $feature_category->values = moduleTools::handleSetConfigurationData($value);
                        $feature_category->id_shop = (int) \FacebookProductAd::$iShopId;
                        if ($feature_category->add()) {
                            $isAdded = true;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }

            if (!empty($isAdded)) {
                \Tools::redirect(\Context::getContext()->link->getAdminLink('AdminTagProduct') . '&tag=' . $tagType);
                $this->confirmations[] = $this->module->l('Settings updated');
            }
        }
    }
}
