<?php
/**
 * Dynamic Ads + Pixel
 *
 * @author    businesstech.fr <modules@businesstech.fr> - https://www.businesstech.fr/
 * @copyright Business Tech 2025 - https://www.businesstech.fr/
 * @license   see file: LICENSE.txt
 *
 * @version   1.5.18
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

require_once dirname(__FILE__) . '/vendor/autoload.php';

use FacebookProductAd\Admin\baseController;
use FacebookProductAd\Configuration\moduleConfiguration;
use FacebookProductAd\Hook\hookController;
use FacebookProductAd\Install\installController;
use FacebookProductAd\ModuleLib\moduleTools;
use FacebookProductAd\ModuleLib\moduleUpdate;
use FacebookProductAd\ModuleLib\moduleWarning;

class FacebookProductAd extends Module
{
    /**
     * @var array : array of set configuration
     */
    public static $conf = [];

    /**
     * @var int : store id of default lang
     */
    public static $iCurrentLang;

    /**
     * @var int : store iso of default lang
     */
    public static $sCurrentLang;

    /**
     * @var \Cookie : store cookie obj
     */
    public static $oCookie;

    /**
     * @var FacebookProductAd : obj module itself
     */
    public static $oModule = null;

    /**
     * @var string : query mode - detect XHR
     */
    public static $sQueryMode;

    /**
     * @var string : base of URI in prestashop
     */
    public static $sBASE_URI;

    /**
     * @var string : store the current domain
     */
    public static $sHost = '';

    /**
     * @var int : shop id used for 1.5 and for multi shop
     */
    public static $iShopId = 1;

    /**
     * @var bool : get compare version for PS 1.7.7.0
     */
    public static $bCompare1770 = false;

    /**
     * @var bool : get compare version for PS 8.0.0
     */
    public static $bCompare80 = false;

    /**
     * @var \Context : get context object
     */
    public static $oContext;

    /**
     * @var array : store the available languages
     */
    public static $aAvailableLanguages = [];

    /**
     * @var array : store the available related languages / countries / currencies
     */
    public static $aAvailableLangCurrencyCountry = [];

    /**
     * @var string : store the XML file's prefix
     */
    public static $sFilePrefix = '';

    /**
     * @var bool : check advanced pack module installation
     */
    public static $bAdvancedPack = false;

    /**
     * @var bool : check advanced pack module installation
     */
    public static $bFacebookChats = false;

    /**
     * @var array : array get error
     */
    public $aErrors;

    /**
     * Magic Method __construct assigns few information about module and instantiate parent class
     */
    public function __construct()
    {
        $this->name = 'facebookproductad';
        $this->module_key = '53cde7913d291517cb39b65684eed8ec';
        $this->tab = 'seo';
        $this->version = '1.5.18';
        $this->author = 'Business Tech';
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('Facebook Dynamic Ads + Pixel & Conversions API');
        $this->description = $this->l('Automatically promote relevant products from your entire catalog on Facebook across any device.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module (your configuration will be lost)?');
        self::$bCompare1770 = version_compare(_PS_VERSION_, '1.7.7.0', '>=');
        self::$bCompare80 = version_compare(_PS_VERSION_, '8.0.0', '>=');
        self::$bAdvancedPack = moduleTools::isInstalled('pm_advancedpack');
        self::$bFacebookChats = moduleTools::isInstalled('btfacebookchats', [], false, true);
        self::$oContext = $this->context;
        self::$iShopId = self::$oContext->shop->id;
        self::$iCurrentLang = self::$oContext->cookie->id_lang;
        self::$sCurrentLang = moduleTools::getLangIso(\FacebookProductAd::$iCurrentLang);
        self::$oModule = $this;
        self::$sBASE_URI = $this->_path;
        self::$sHost = moduleTools::setHost();
        self::$aAvailableLanguages = moduleTools::getAvailableLanguages(self::$iShopId);
        self::$aAvailableLangCurrencyCountry = [];
        self::$sQueryMode = \Tools::getValue('sMode');

        moduleTools::getConfiguration(['FPA_HOME_CAT', 'FPA_COLOR_OPT', 'FPA_SIZE_OPT', 'FPA_SHIP_CARRIERS', 'FPA_CHECK_EXPORT', 'FPA_NO_TAX_SHIP_CARRIERS', 'FPA_FREE_SHIP_CARRIERS', 'FPA_FREE_PROD_PRICE_SHIP_CARRIERS'], self::$iShopId);
    }

    /**
     * install() method installs all mandatory structure (DB or Files) => sql queries and update values and hooks registered
     *
     * @return mixed
     */
    public function install()
    {
        // set return
        $return = true;

        if (
            !parent::install()
            || !installController::run('install', 'sql', moduleConfiguration::FPA_PATH_SQL . moduleConfiguration::FPA_INSTALL_SQL_FILE)
            || !installController::run('install', 'config', ['bConfigOnly' => true, 'moduleConfVar' => moduleConfiguration::getConfVar(), 'module' => \FacebookProductAd::$oModule, 'hook' => moduleConfiguration::FPA_HOOKS])
        ) {
            $return = false;
        }

        return $return;
    }

    /**
     * uninstall() method uninstalls all mandatory structure (DB or Files)
     *
     * @return mixed
     */
    public function uninstall()
    {
        // set return
        $return = true;

        // clean up all generated XML files
        moduleTools::cleanUpFiles();

        if (
            !parent::uninstall()
            || !installController::run('uninstall', 'sql', moduleConfiguration::FPA_PATH_SQL . moduleConfiguration::FPA_UNINSTALL_SQL_FILE)
            || !installController::run('uninstall', 'config', ['moduleConfVar' => moduleConfiguration::getConfVar()])
        ) {
            $return = false;
        }

        return $return;
    }

    /**
     * Displays the module configuration page in the back office.
     *
     * Handles the different actions based on the user input and displays the appropriate template.
     *
     * @return mixed The HTML content to display
     */
    public function getContent()
    {
        self::$aAvailableLangCurrencyCountry = moduleTools::getLangCurrencyCountry(self::$aAvailableLanguages);

        try {
            // transverse execution
            self::$sFilePrefix = moduleTools::setXmlFilePrefix();

            // get controller type
            $sControllerType = (!Tools::getIsset(moduleConfiguration::FPA_PARAM_CTRL_NAME) || (Tools::getIsset(moduleConfiguration::FPA_PARAM_CTRL_NAME) && 'admin' == Tools::getValue(moduleConfiguration::FPA_PARAM_CTRL_NAME))) ? (Tools::getIsset(moduleConfiguration::FPA_PARAM_CTRL_NAME) ? Tools::getValue(moduleConfiguration::FPA_PARAM_CTRL_NAME) : 'admin') : Tools::getValue(moduleConfiguration::FPA_PARAM_CTRL_NAME);

            // instantiate matched controller object
            $oCtrl = baseController::get($sControllerType);

            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template
            $aDisplay = $oCtrl->run(array_merge($_GET, $_POST));

            // free memory
            unset($oCtrl);

            if (!empty($aDisplay)) {
                $aDisplay['assign'] = array_merge($aDisplay['assign'], [
                    'oJsTranslatedMsg' => \json_encode(moduleConfiguration::getJsMessage()),
                    'bAddJsCss' => true,
                ]);

                // get content
                $sContent = $this->displayModule($aDisplay['tpl'], $aDisplay['assign']);

                if (!empty(self::$sQueryMode)) {
                    echo $sContent;
                } else {
                    return $sContent;
                }
            } else {
                throw new Exception('action returns empty content', 110);
            }
        } catch (Exception $e) {
            \PrestaShopLogger::addLog($e->getMessage(), 1, $e->getCode(), null, null, true);
            $this->aErrors[] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];

            // get content
            $sContent = $this->displayErrorModule();

            if (!empty(self::$sQueryMode)) {
                echo $sContent;
            } else {
                return $sContent;
            }
        }
        // exit clean with XHR mode
        if (!empty(self::$sQueryMode)) {
            exit;
        }
    }

    /**
     * hookDisplayHeader() method displays customized module content on header
     *
     * @return mixed
     */
    public function hookDisplayHeader()
    {
        return $this->_execHook('display', 'header');
    }

    /**
     * _execHook() method displays selected hook content
     *
     * @param string $sHookType
     * @param array $aParams
     *
     * @return mixed
     */
    private function _execHook($sHookType, $sAction, array $aParams = null)
    {
        // set
        $aDisplay = [];

        try {
            // use cache or not
            if (
                !empty($aParams['cache'])
                && !empty($aParams['template'])
                && !empty($aParams['cacheId'])
            ) {
                $bUseCache = !$this->isCached(
                    $aParams['template'],
                    $this->getCacheId($aParams['cacheId'])
                ) ? false : true;

                if ($bUseCache) {
                    $aDisplay['tpl'] = $aParams['template'];
                    $aDisplay['assign'] = [];
                }
            } else {
                $bUseCache = false;
            }

            // detect cache or not
            if (!$bUseCache) {
                // define which hook class is executed in order to display good content in good zone in shop
                $oHook = new hookController($sHookType, $sAction);

                // displays good block content
                $aDisplay = $oHook->run($aParams);

                // free memory
                unset($oHook);
            }

            // execute good action in admin
            // only displayed with key : tpl and assign in order to display good smarty template

            if (!empty($aDisplay)) {
                return $this->displayModule(
                    $aDisplay['tpl'],
                    $aDisplay['assign'],
                    $bUseCache,
                    !empty($aParams['cacheId']) ? $aParams['cacheId'] : null
                );
            } else {
                throw new Exception('Chosen hook returned empty content', 110);
            }
        } catch (Exception $e) {
            $this->aErrors[] = ['msg' => $e->getMessage(), 'code' => $e->getCode()];

            return $this->displayErrorModule();
        }
    }

    /**
     * setErrorHandler() method manages module error
     *
     * @param int $iErrno
     * @param string $sErrstr
     * @param string $sErrFile
     * @param int $iErrLine
     * @param array $aErrContext
     */
    public function setErrorHandler($iErrno, $sErrstr, $sErrFile, $iErrLine, $aErrContext)
    {
        switch ($iErrno) {
            case E_USER_ERROR:
                $this->aErrors[] = [
                    'msg' => 'Fatal error <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];

                break;
            case E_USER_WARNING:
                $this->aErrors[] = [
                    'msg' => 'Warning <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];

                break;
            case E_USER_NOTICE:
                $this->aErrors[] = [
                    'msg' => 'Notice <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];

                break;
            default:
                $this->aErrors[] = [
                    'msg' => 'Unknow error <b>' . $sErrstr . '</b>',
                    'code' => $iErrno,
                    'file' => $sErrFile,
                    'line' => $iErrLine,
                    'context' => $aErrContext,
                ];

                break;
        }

        return $this->displayErrorModule();
    }

    /**
     * displayModule() method displays views
     *
     * @param string $sTplName
     * @param array $aAssign
     * @param bool $bUseCache
     * @param int $iICacheId
     *
     * @return mixed
     */
    public function displayModule($sTplName, $aAssign, $bUseCache = false, $iICacheId = null)
    {
        if (file_exists(_PS_MODULE_DIR_ . 'facebookproductad/views/templates/' . $sTplName) && is_file(_PS_MODULE_DIR_ . 'facebookproductad/views/templates/' . $sTplName)) {
            $aAssign = array_merge($aAssign, ['sModuleName' => Tools::strtolower(moduleConfiguration::FPA_MODULE_NAME), 'bDebug' => moduleConfiguration::FPA_DEBUG]);

            // use cache
            if (!empty($bUseCache) && !empty($iICacheId)) {
                return $this->display(__FILE__, $sTplName, $this->getCacheId($iICacheId));
            } // not use cache
            else {
                self::$oContext->smarty->assign($aAssign);

                return $this->display(__FILE__, 'views/templates/' . $sTplName);
            }
        } else {
            throw new Exception('Template "' . $sTplName . '" doesn\'t exists', 120);
        }
    }

    /**
     * displayErrorModule() method displays view with error
     *
     * @return mixed
     */
    public function displayErrorModule()
    {
        self::$oContext->smarty->assign(
            [
                'sHomeURI' => moduleTools::truncateUri(),
                'aErrors' => $this->aErrors,
                'sModuleName' => \Tools::strtolower(moduleConfiguration::FPA_MODULE_NAME),
                'bDebug' => moduleConfiguration::FPA_DEBUG,
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/error.tpl');
    }

    /**
     * updateModule() method updates module as necessary
     *
     * @return array
     */
    public function updateModule()
    {
        moduleUpdate::create()->run('tables');
        moduleUpdate::create()->run('fields');
        moduleUpdate::create()->run('templates');
        moduleUpdate::create()->run('hooks');
        moduleUpdate::create()->run('configuration', 'languages');
        moduleUpdate::create()->run('configuration', 'color');
        moduleUpdate::create()->run('configuration', 'cronlang');
        moduleUpdate::create()->run('configuration', 'feed');
        moduleUpdate::create()->run('feedsDatabaseMigration');
        moduleUpdate::create()->run('moduleAdminTab');
        moduleUpdate::create()->run('secureTaxonomies');

        $aErrors = moduleUpdate::create()->getErrors();

        // initialize XML files
        moduleUpdate::create()->run('xmlFiles', ['aAvailableData' => FacebookProductAd::$aAvailableLangCurrencyCountry]);

        if (
            empty($aErrors)
            && moduleUpdate::create()->getErrors()
        ) {
            moduleWarning::create()->bStopExecution = true;
        }

        return moduleUpdate::create()->getErrors();
    }
}
