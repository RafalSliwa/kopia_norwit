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

use FacebookProductAd\Common\dirReader;
use FacebookProductAd\Common\fileClass;
use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Install\installController;
use FacebookProductAd\Models\categoryTaxonomy;
use FacebookProductAd\Models\Feeds;

class moduleUpdate
{
    /**
     * @var : store errors
     */
    protected $aErrors = [];

    /**
     * Magic Method __construct
     */
    public function __construct()
    {
    }

    /**
     * run() method execute required function
     *
     * @param string $sType
     * @param mixed $aParam
     */
    public function run($sType, $aParam = null)
    {
        // get type
        $sType = empty($sType) ? 'tables' : $sType;

        switch ($sType) {
            case 'tables':
            case 'fields':
            case 'hooks':
            case 'templates':
            case 'moduleAdminTab':
            case 'xmlFiles':
            case 'feedsDatabaseMigration':
            case 'configuration':
            case 'secureTaxonomies':
                call_user_func([$this, 'update' . ucfirst($sType)], $aParam);

                break;
            default:
                break;
        }
    }

    /**
     * _updateTables() method update tables if required
     *
     * @param array $aParam
     */
    private function updateTables(array $aParam = null)
    {
        // set transaction
        if (\Tools::getValue('controller') == 'AdminModules') {
            \Db::getInstance()->Execute('BEGIN');

            if (!empty(moduleConfiguration::getSqlUpdateData()['table'])) {
                $iCount = 1;
                // loop on each elt to update SQL
                foreach (moduleConfiguration::getSqlUpdateData()['table'] as $sTable => $sSqlFile) {
                    // execute query
                    $bResult = \Db::getInstance()->ExecuteS('SHOW TABLES LIKE "' . _DB_PREFIX_ . strtolower(moduleConfiguration::FPA_MODULE_NAME) . '_' . \bqSQL($sTable) . '"');

                    // if empty - update
                    if (empty($bResult)) {
                        // use case - KO update
                        if (!installController::run('install', 'sql', moduleConfiguration::FPA_PATH_SQL . $sSqlFile)) {
                            $this->aErrors[] = [
                                'msg' => \FacebookProductAd::$oModule->l('There is an error around the SQL table update', 'moduleUpdate'),
                                'code' => intval(190 + $iCount),
                                'file' => $sSqlFile,
                                'context' => \FacebookProductAd::$oModule->l('Issue around table update for: ', 'moduleUpdate') . $sTable,
                            ];
                            ++$iCount;
                        }
                    }
                }
            }

            if (empty($this->aErrors)) {
                \Db::getInstance()->Execute('COMMIT');
            } else {
                \Db::getInstance()->Execute('ROLLBACK');
            }
        }
    }

    /**
     * _updateFields() method update fields if required
     *
     * @param array $aParam
     */
    private function updateFields(array $aParam = null)
    {
        // set transaction
        \Db::getInstance()->Execute('BEGIN');

        if (!empty(moduleConfiguration::getSqlUpdateData()['field'])) {
            $iCount = 1;
            // loop on each elt to update SQL
            foreach (moduleConfiguration::getSqlUpdateData()['field'] as $sFieldName => $aOption) {
                // execute query
                $bResult = \Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . strtolower(moduleConfiguration::FPA_MODULE_NAME) . '_' . \bqSQL($aOption['table']) . ' LIKE "' . \pSQL($sFieldName) . '"');

                // if empty - update
                if (empty($bResult)) {
                    // use case - KO update
                    if (!installController::run('install', 'sql', moduleConfiguration::FPA_PATH_SQL . $aOption['file'])) {
                        $aErrors[] = [
                            'field' => $sFieldName,
                            'linked' => $aOption['table'],
                            'file' => $aOption['file'],
                        ];
                        $this->aErrors[] = [
                            'msg' => \FacebookProductAd::$oModule->l('There is an error around the SQL field update', 'moduleUpdate'),
                            'code' => intval(180 + $iCount),
                            'file' => $aOption['file'],
                            'context' => \FacebookProductAd::$oModule->l('Issue around field update for: ', 'moduleUpdate') . $sFieldName,
                        ];
                        ++$iCount;
                    }
                }
            }
        }

        if (empty($this->aErrors)) {
            \Db::getInstance()->Execute('COMMIT');
        } else {
            \Db::getInstance()->Execute('ROLLBACK');
        }
    }

    /**
     * _updateHooks() method update hooks if required
     *
     * @param array $aParam
     */
    private function updateHooks(array $aParam = null)
    {
        // use case - hook register ko
        if (!installController::run('install', 'config', ['bHookOnly' => true])) {
            $this->aErrors[] = [
                'msg' => \FacebookProductAd::$oModule->l('There is an error around the hooks update', 'moduleUpdate'),
                'code' => 170,
                'file' => \FacebookProductAd::$oModule->l('There is an error around the hooks update', 'moduleUpdate'),
                'context' => \FacebookProductAd::$oModule->l('Issue around hook update', 'moduleUpdate'),
            ];
        }
    }

    /**
     * _updateTemplates() method update templates if required
     *
     * @param array $aParam
     */
    private function updateTemplates(array $aParam = null)
    {
        // get templates files
        $aTplFiles = dirReader::create()->run([
            'path' => moduleConfiguration::FPA_PATH_TPL,
            'recursive' => true,
            'extension' => 'tpl',
            'subpath' => true,
        ]);

        if (!empty($aTplFiles)) {
            $smarty = \Context::getContext()->smarty;

            if (method_exists($smarty, 'clearCompiledTemplate')) {
                $smarty->clearCompiledTemplate();
            } elseif (method_exists($smarty, 'clear_compiled_tpl')) {
                foreach ($aTplFiles as $aFile) {
                    $smarty->clear_compiled_tpl($aFile['filename']);
                }
            }
        }
    }

    /**
     * _updateModuleAdminTab() method update module admin tab in case of an update
     *
     * @param array $aParam
     */
    private function updateModuleAdminTab(array $aParam = null)
    {
        foreach (moduleConfiguration::FPA_TABS as $sModuleTabName => $aTab) {
            if (isset($aTab['oldName'])) {
                if (\Tab::getIdFromClassName($aTab['oldName']) != false) {
                    // use case - if uninstall succeeded
                    if (installController::run('uninstall', 'tab', ['name' => $aTab['oldName']])) {
                        // install new admin tab
                        installController::run('install', 'tab', ['name' => $sModuleTabName]);
                    }
                }
            } else {
                installController::run('install', 'tab', ['name' => $sModuleTabName]);
            }
        }
    }

    /**
     * _updateXmlFiles() method initialize XML files
     *
     * @param array $aParam
     */
    private function updateXmlFiles(array $aParam = null)
    {
        if (
            !empty($aParam['aAvailableData'])
            && is_array($aParam['aAvailableData'])
        ) {
            $iCount = 1;
            foreach ($aParam['aAvailableData'] as $aData) {
                // check if file exist
                $sFileSuffix = moduleTools::buildFileSuffix($aData['langIso'], $aData['countryIso']);
                $sFilePath = \FacebookProductAd::$sFilePrefix . '.' . $sFileSuffix . '.xml';

                if (!is_file(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilePath)) {
                    try {
                        fileClass::create()->write(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilePath, '');

                        // test if file exists
                        $bFileExists = is_file(moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilePath);
                        //					    $bFileExists = false;
                    } catch (\Exception $e) {
                        $bFileExists = false;
                    }

                    if (!$bFileExists) {
                        $aError = [
                            'msg' => \FacebookProductAd::$oModule->l('There is an error around the creation of the data feed XML file in the shop\'s root directory', 'moduleUpdate'),
                            'code' => intval(160 + $iCount),
                            'file' => moduleConfiguration::FPA_SHOP_PATH_ROOT . $sFilePath,
                            'context' => \FacebookProductAd::$oModule->l('Issue around the xml files which have to be generated in the shop\'s root directory', 'moduleUpdate'),
                            'howTo' => \FacebookProductAd::$oModule->l('Please follow our FAQ about problems when creating XML files at the root of your shop', 'moduleUpdate') . '&nbsp;=>&nbsp;<i class="icon-question-sign"></i>&nbsp;<a href="http://faq.businesstech.fr/faq/21" target="_blank">FAQ</a>',
                        ];
                        $this->aErrors[] = $aError;
                        ++$iCount;
                    }
                }
            }
        }
    }

    /**
     * _updateConfiguration() method update specific configuration options
     *
     * @param string $sType
     */
    private function updateConfiguration($sType)
    {
        switch ($sType) {
            case 'languages':
                $aHomeCat = \Configuration::get('FPA_HOME_CAT');
                if (empty($aHomeCat)) {
                    $aHomeCat = [];
                    foreach (\FacebookProductAd::$aAvailableLanguages as $aLanguage) {
                        $aHomeCat[$aLanguage['id_lang']] = !empty(moduleConfiguration::FPA_HOME_CAT_NAME[$aLanguage['iso_code']]) ? moduleConfiguration::FPA_HOME_CAT_NAME[$aLanguage['iso_code']] : '';
                    }
                    // update
                    \Configuration::updateValue('FPA_HOME_CAT', moduleTools::handleSetConfigurationData($aHomeCat));
                    unset($aHomeCat);
                } elseif (is_array(\FacebookProductAd::$conf['FPA_HOME_CAT'])) {
                    // update
                    \Configuration::updateValue(
                        'FPA_HOME_CAT',
                        moduleTools::handleSetConfigurationData(\FacebookProductAd::$conf['FPA_HOME_CAT'])
                    );
                }

                break;
            case 'color':
                if (!empty(\FacebookProductAd::$conf['FPA_COLOR_OPT'])) {
                    if (is_numeric(\FacebookProductAd::$conf['FPA_COLOR_OPT'])) {
                        \FacebookProductAd::$conf['FPA_COLOR_OPT'] = [\FacebookProductAd::$conf['FPA_COLOR_OPT']];

                        $aAttributeIds = [];
                        foreach (\FacebookProductAd::$conf['FPA_COLOR_OPT'] as $iAttributeId) {
                            $aAttributeIds['attribute'][] = $iAttributeId;
                        }
                        \Configuration::updateValue('FPA_COLOR_OPT', moduleTools::handleSetConfigurationData($aAttributeIds));
                    }
                }

                break;
            default:
                break;
        }
    }

    /**
     * updateFeedsDatabaseMigration() method made the migration in database for the data feeds to replace global used before the version 1.4.0
     */
    private function updateFeedsDatabaseMigration()
    {
        $hasData = Feeds::hasSavedData(\FacebookProductAd::$iShopId);

        if (!empty(moduleConfiguration::FPA_AVAILABLE_COUNTRIES) && empty($hasData)) {
            foreach (moduleConfiguration::FPA_AVAILABLE_COUNTRIES as $lang_code => $data) {
                if (is_array($data)) {
                    foreach ($data as $country_code => $data_entry) {
                        if (is_array($data_entry) && isset($data_entry['currency'])) {
                            foreach ($data_entry['currency'] as $currency) {
                                $feed = new Feeds();
                                $feed->iso_lang = $lang_code;
                                $feed->iso_country = $country_code;
                                $feed->iso_currency = $currency;
                                $feed->taxonomy = $data_entry['taxonomy'];
                                $feed->id_shop = \FacebookProductAd::$iShopId;
                                $feed->feed_is_default = 1;
                                $feed->add();
                            }
                        }
                    }
                }
            }

            return \Tools::redirectAdmin(\Context::getContext()->link->getAdminLink('AdminModules') . '&configure=facebookproductad');
        }
    }

    /**
     * @return mixed
     */
    private function updateSecureTaxonomies()
    {
        try {
            if (empty(\FacebookProductAd::$conf['FPA_HANDLE_TAXO_JSON'])) {
                if (!empty(categoryTaxonomy::hasTaxonomies(\FacebookProductAd::$iShopId))) {
                    $taxonomies = categoryTaxonomy::getAllTaxonomies(\FacebookProductAd::$iShopId);
                    foreach ($taxonomies as $taxonomy) {
                        if (!empty($taxonomy['txt_taxonomy'])) {
                            $is_json = is_string($taxonomy['txt_taxonomy']) && !empty(json_decode($taxonomy['txt_taxonomy'])) ? true : false;

                            if (empty($is_json)) {
                                categoryTaxonomy::deleteSpecificGoogleCategory($taxonomy['id_shop'], $taxonomy['lang'], $taxonomy['id_category']);
                                categoryTaxonomy::insertGoogleCategory($taxonomy['id_shop'], $taxonomy['id_category'], $taxonomy['txt_taxonomy'], $taxonomy['lang']);
                            }
                        }
                    }
                }

                \Configuration::updateValue('FPA_HANDLE_TAXO_JSON', 1);

                return \Tools::redirectAdmin(\Context::getContext()->link->getAdminLink('AdminModules') . '&configure=facebookproductad');
            }
        } catch (\Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 3, $e->getCode(), null, null, true);
        }
    }

    /**
     * getErrors() method returns errors
     *
     * @return array
     */
    public function getErrors()
    {
        return empty($this->aErrors) ? false : $this->aErrors;
    }

    /**
     * create() method manages singleton
     *
     * @return moduleUpdate
     */
    public static function create()
    {
        static $oModuleUpdate;

        if (null === $oModuleUpdate) {
            $oModuleUpdate = new moduleUpdate();
        }

        return $oModuleUpdate;
    }
}
