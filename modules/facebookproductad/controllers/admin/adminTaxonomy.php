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
use FacebookProductAd\Models\categoryTaxonomy;
use FacebookProductAd\Models\googleTaxonomy;
use FacebookProductAd\ModuleLib\moduleTools;

/**
 * Controller to handle the taxonomies association
 */
class AdminTaxonomyController extends \ModuleAdminController
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

        $isoLang = \Tools::getValue('sLangIso');

        if (!empty($isoLang)) {
            $isoExplode = explode('-', \Tools::getValue('sLangIso'));
            $id_lang = \Language::getIdByIso($isoExplode[0]);

            // Use case if the installed id_lang is deleted, we force use the current default lang of the shop
            if (empty($id_lang)) {
                $id_lang = \Configuration::get('PS_LANG_DEFAULT');
            }

            $jsDefs = [];
            $jsDefs['taxonomyController'] = $this->context->link->getAdminLink('AdminTaxonomy') . '&iLangId=' . $id_lang . '&sLangIso=' . $isoLang;
            \Media::addJsDef(['btFbda' => $jsDefs]);

            $shopCategories = moduleDao::getShopCategories(\FacebookProductAd::$iShopId, (int) $id_lang, \FacebookProductAd::$conf['FPA_HOME_CAT_ID']);

            foreach ($shopCategories as &$category) {
                // get google taxonomy
                $aGoogleCat = categoryTaxonomy::getFacebookCategories(\FacebookProductAd::$iShopId, $category['id_category'], $isoLang);
                // assign the current taxonomy
                $category['google_category_name'] = !empty($aGoogleCat['txt_taxonomy']) ? json_decode($aGoogleCat['txt_taxonomy']) : '';
            }

            $this->context->smarty->assign([
                'moduleUrl' => \Context::getContext()->link->getAdminLink('AdminModules') . '&configure=facebookproductad&tab=taxonomies',
                'idLang' => $id_lang,
                'isoLang' => $isoLang,
                'currencyIso' => \Language::getIsoById(\FacebookProductAd::$iCurrentLang),
                'maxPostVar' => ini_get('max_input_vars'),
                'shopCategories' => $shopCategories,
                'shopCategoriesCount' => count($shopCategories),
                'faqLink' => 'http://faq.businesstech.fr',
                'taxonomiesToImport' => moduleTools::getTaxonomiesToImport($isoLang),
                'sModuleName' => moduleConfiguration::FPA_MODULE_SET_NAME,
            ]);

            // execute the ajax call
            if (\Tools::getValue('action') == 'autocomplete') {
                $this->processAutocomplete();
            }

            $this->context->smarty->assign([
                'content' => $this->content . $this->module->fetch('module:facebookproductad/views/templates/admin/tab/taxonomies.tpl'),
            ]);
        } else {
            \Tools::redirect(\Context::getContext()->link->getAdminLink('AdminModules') . '&configure=facebookproductad&tab=taxonomies');
        }
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
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/taxonomie.css');
        $this->addCss(_MODULE_DIR_ . $this->module->name . '/views/css/bootstrap4.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/module.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/taxonomies.js');
    }

    /**
     * Handle the post of the form
     *
     * @return mixed
     */
    public function postProcess()
    {
        $isoLang = \Tools::getValue('sLangIso');
        $id_lang = \Configuration::get('PS_LANG_DEFAULT');

        if (Tools::isSubmit('save_btn')) {
            try {
                $iso = explode('-', \Tools::getValue('sLangIso'));

                $facebookCategories = \Tools::getValue('bt_facebook-cat');

                // delete previous facebook matching categories
                if (categoryTaxonomy::deleteFacebookCategory(\FacebookProductAd::$iShopId, $isoLang)) {
                    foreach ($facebookCategories as $idShopCategorie => $facebookCategoryValue) {
                        if (!empty($facebookCategoryValue)) {
                            // insert each category
                            categoryTaxonomy::insertFacebookCategory(\FacebookProductAd::$iShopId, $idShopCategorie, $facebookCategoryValue, $isoLang);
                        }
                    }
                }

                $this->confirmations[] = $this->module->l('The correspondence with Facebook categories has been updated.');
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        if (Tools::isSubmit('gmcTaxonomies') || Tools::isSubmit('gmcpTaxonomies') || Tools::isSubmit('tkpTaxonomies')) {
            try {
                $moduleSource = '';
                $is_json = false;

                if (!empty(Tools::isSubmit('gmcTaxonomies'))) {
                    $moduleSource = 'gmcTaxonomies';
                } elseif (!empty(Tools::isSubmit('gmcpTaxonomies'))) {
                    $moduleSource = 'gmcpTaxonomies';
                } elseif (!empty(Tools::isSubmit('tkpTaxonomies'))) {
                    $moduleSource = 'tkpTaxonomies';
                }

                if (!empty($moduleSource)) {
                    $dataToImport = moduleTools::getTaxonomiesToImport($isoLang);

                    if (categoryTaxonomy::deleteFacebookCategory(\FacebookProductAd::$iShopId, $isoLang)) {
                        if (!empty($dataToImport)) {
                            foreach ($dataToImport[$moduleSource] as $data) {
                                if (!empty($data['txt_taxonomy'])) {
                                    $is_json = is_string($data['txt_taxonomy']) && json_decode($data['txt_taxonomy'], true) ? true : false;
                                }

                                if (empty($is_json)) {
                                    categoryTaxonomy::insertFacebookCategory(\FacebookProductAd::$iShopId, $data['id_category'], $data['txt_taxonomy'], $data['lang']);
                                } else {
                                    categoryTaxonomy::insertFacebookCategory(\FacebookProductAd::$iShopId, $data['id_category'], json_decode($data['txt_taxonomy']), $data['lang']);
                                }
                            }
                        }
                    }
                    $this->confirmations[] = $this->module->l('The correspondence with Facebook categories has been imported.');
                }
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }

    /**
     * manages the search customer autcomplete
     *
     * @return mixed
     */
    public function processAutocomplete()
    {
        $items = [];
        $isoLang = \Tools::getValue('sLangIso');
        $query = \Tools::getValue('query');
        $words = explode(' ', $query);
        $taxonomyFound = [];

        if (strlen($query) >= 4) {
            $items = [];
            $items = googleTaxonomy::autocompleteSearch($isoLang, $words);

            if (!empty($items) && is_array($items)) {
                foreach ($items as $data) {
                    $taxonomyFound[] = $data['value'];
                }
            }
        }

        exit(json_encode($taxonomyFound));
    }
}
