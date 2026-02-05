<?php

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;

/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 *    @author    Jpresta
 *    @copyright Jpresta
 *    @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 */

class JprestaUpgradeApi {

    const JPRESTA_PROTO = 'https://';
    const JPRESTA_DOMAIN_EXT = '.com';
    const JPRESTA_DOMAIN = 'jpresta';
    const JPRESTA_PATH_API_LICENSES = '/fr/module/jprestacrm/licenses';

    /**
     * @var string JPresta Account Key
     */
    private $jak;

    /**
     * @var string The string that identify this Prestashop instance
     */
    private $psToken;

    /**
     * JprestaUpgradeApi constructor.
     * @param string $jak
     * @param string $psToken
     */
    public function __construct($jak = null, $psToken = null)
    {
        $this->jak = $jak ? $jak : self::getJPrestaAccountKey();
        $this->psToken = $psToken ? $psToken : self::getPrestashopToken();
    }

    public static function getInstance() {
        return new JprestaUpgradeApi();
    }

    public static function getJPrestaThemes()
    {
        $themes = [];
        // There is no JPresta theme for PS1.6
        if (Tools::version_compare(_PS_VERSION_,'1.7.1.0','>=')) {
            $theme_repository = (new PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder(Context::getContext(),
                Db::getInstance()))->buildRepository();
            $files = scandir(_PS_ALL_THEMES_DIR_);
            if ($files && is_array($files)) {
                foreach ($files as $file) {
                    if (is_dir(_PS_ALL_THEMES_DIR_ . $file)) {
                        try {
                            $theme = $theme_repository->getInstanceByName($file);
                            if ($theme->get('commercial_name')
                                && !$theme->get('parent')
                                && strpos($theme->get('commercial_name'), 'jpresta') === 0
                            ) {
                                $themes[] = $theme;
                            }
                        } catch (Exception $e) {
                            // Ignore this directory
                        }
                    }
                }
            }
        }
        return $themes;
    }

    /**
     * @param string $commercialName
     * @param string $moduleOrThemeVersion
     * @return array
     */
    public static function getLicenseInfos($commercialName, $moduleOrThemeVersion = null)
    {
        $infosLicense = null;
        try {
            $licenses = self::getLicenses(true);
            if (isset($licenses[$commercialName])) {
                $infosLicense = $licenses[$commercialName];
                if (isset($licenses[$commercialName]['download'])) {
                    $infosLicense['download']['can_upgrade'] = true;
                    if ($moduleOrThemeVersion
                        && ($commercialName != 'pagecache' || !$infosLicense['is_migration_pcu2sp'])
                        && version_compare($moduleOrThemeVersion, $licenses[$commercialName]['download']['version'],
                            '>=')) {
                        // Already up-to-date, no need to upgrade
                        unset($infosLicense['download']);
                    } elseif (isset($licenses[$commercialName]['download']['dependencies'])) {
                        $deps = $licenses[$commercialName]['download']['dependencies'];
                        $depsMessage = '';
                        foreach ($deps as $moduleName => $moduleVersion) {
                            $moduleDepInstance = Module::getInstanceByName($moduleName);
                            if ($moduleDepInstance) {
                                if (version_compare($moduleDepInstance->version, $moduleVersion, '<')) {
                                    $infosLicense['download']['can_upgrade'] = false;
                                    $depsMessage .= '<li>' . $moduleDepInstance->displayName . ' &gt;= ' . $moduleVersion . '</li>';
                                }
                            }
                        }
                        if ($depsMessage) {
                            $depsMessage = '<ul>' . $depsMessage . '</ul>';
                            $infosLicense['download']['message'] = $depsMessage;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            JprestaUpgradeUtils::addLog('Cannot retrieve your licenses : ' . $e->getMessage() . ". " . JprestaUpgradeUtils::jTraceEx($e), 2);
        }
        return $infosLicense;
    }

    /**
     * @return string[] All installed JPresta module names
     */
    public static function getJPrestaModules() {
        $modulesName = [];
        $rows = DB::getInstance()->executeS('SELECT name FROM `' . _DB_PREFIX_ . 'module` WHERE name LIKE \'jpresta%\' OR name IN (\'pagecache\',\'pagecachestd\')');
        foreach ($rows as $row) {
            $modulesName[] = $row['name'];
        }
        return $modulesName;
    }

    /**
     * @param $psIsTest boolean true if this is a Prestashop instance for test, not production
     * @return boolean|string true if ok, error message if not ok
     */
    public function attach($psIsTest) {

        if (function_exists('curl_init')) {
            $curl = curl_init();

            $defaultShop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
            $post_data = array(
                'action' => 'attach_module',
                'ajax' => 1,
                'ps_token' => $this->psToken,
                'shop_url' => $defaultShop->getBaseURL(true),
                'ps_version' => _PS_VERSION_,
                'modules' => implode(',', self::getJPrestaModules()),
                'ps_is_test' => (bool)$psIsTest
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_API_LICENSES);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'x-jpresta-account-key: '.$this->jak,
                'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'JPresta Upgrade')
            ));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

            $content = curl_exec($curl);

            if (false === $content) {
                $res = sprintf('error code %d - %s',
                    curl_errno($curl),
                    curl_error($curl)
                );
            }
            else {
                $jsonContent = json_decode($content, true);
                if (!is_array($jsonContent) || !array_key_exists('status', $jsonContent)) {
                    $res = 'JPresta server returned response in incorrect format';
                }
                else {
                    if ($jsonContent['status'] === 'ok') {
                        $res = true;
                    }
                    else {
                        if (array_key_exists('message', $jsonContent)) {
                            $res = $jsonContent['message'];
                        }
                        else {
                            $res = 'The account has not been attached for an unknown reason';
                        }
                    }
                }
            }

            curl_close($curl);
        }
        else {
            $res = 'CURL must be available';
        }

        return $res;
    }

    public function detach() {

        if (function_exists('curl_init')) {
            Tools::refreshCACertFile();
            $curl = curl_init();

            $post_data = array(
                'action' => 'detach',
                'ajax' => 1,
                'ps_token' => $this->psToken
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_API_LICENSES);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'x-jpresta-account-key: '.$this->jak,
                'User-Agent: ' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'JPresta Upgrade')
            ));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_MAXREDIRS, 5);

            $content = curl_exec($curl);

            if (false === $content) {
                $res = sprintf('error code %d - %s',
                    curl_errno($curl),
                    curl_error($curl)
                );
                self::addLog('Detach JAK - ' . $res, 2);
            }
            else {
                $jsonContent = json_decode($content, true);
                if (!is_array($jsonContent) || !array_key_exists('status', $jsonContent)) {
                    $res = 'JPresta server returned response in incorrect format';
                    self::addLog('Detach JAK - ' . $res, 2);
                }
                else {
                    if ($jsonContent['status'] === 'ok') {
                        $res = true;
                    }
                    elseif ($jsonContent['status'] === 'jak_invalid') {
                        if (array_key_exists('message', $jsonContent)) {
                            self::addLog('Ignored error: cannot detach JAK ' . $this->jak . ' - ' . $jsonContent['message'], 2);
                        }
                        else {
                            self::addLog('Ignored error: cannot detach JAK ' . $this->jak, 2);
                        }
                        $res = true;
                    }
                    else {
                        if (array_key_exists('message', $jsonContent)) {
                            $res = $jsonContent['message'];
                        }
                        else {
                            $res = 'The account has not been detached for an unknown reason';
                        }
                        self::addLog('Detach JAK - ' . $res, 2);
                    }
                }
            }

            curl_close($curl);
        }
        else {
            $res = 'CURL must be available';
        }

        return $res;
    }

    private function getLicensesDatas() {
        if (self::isLocal()) {
            return $this->getLicensesLocal();
        }
        if (!$this->jak || !$this->psToken) {
            return [];
        }

        $get_data = array(
            'action' => 'get_licenses',
            'ajax' => 1,
            'ps_token' => $this->psToken,
            'lang' => Context::getContext()->language->iso_code
        );

        $url = self::JPRESTA_PROTO . self::JPRESTA_DOMAIN . self::JPRESTA_DOMAIN_EXT . self::JPRESTA_PATH_API_LICENSES . '?' . http_build_query($get_data);

        try {
            $content = JprestaUpgradeUtils::downloadContent($url, $this->jak);
            if ($content) {
                $jsonContent = json_decode($content, true);
                if (!is_array($jsonContent) || !array_key_exists('status', $jsonContent)) {
                    $res = 'JPresta server returned response in incorrect format';
                } else {
                    if ($jsonContent['status'] === 'ok') {
                        $res = $jsonContent['licenses'];
                    } else {
                        if (array_key_exists('message', $jsonContent)) {
                            $res = $jsonContent['message'];
                        } else {
                            $res = 'Cannot retreive licenses for an unknown reason (status not OK but no message)';
                        }
                    }
                }
            }
            else {
                $res = 'Cannot retreive licenses for an unknown reason (empty without error)';
            }
        }
        catch (Exception $e) {
            $res = $e->getMessage();
        }
        return $res;
    }

    private static function isLocal()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        return in_array($_SERVER['REMOTE_ADDR'], $whitelist) && is_dir('C:\m2\com\jpresta');
    }

    private function getLicensesLocal() {
        $licences = [];
        $M2_DIR_JPRESTA = 'C:\m2\com\jpresta';
        $scanModules = scandir($M2_DIR_JPRESTA);
        foreach($scanModules as $moduleName) {
            if (is_dir("$M2_DIR_JPRESTA/$moduleName") && Validate::isModuleName($moduleName)) {
                $scanModuleVersions = scandir("$M2_DIR_JPRESTA/$moduleName");
                foreach($scanModuleVersions as $scanModuleVersion) {
                    if ($scanModuleVersion != '.' && $scanModuleVersion != '..' && is_dir("$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/")) {
                        switch ($moduleName) {
                            case 'pagecache':
                                if (!isset($licences['pagecache'])
                                    || version_compare($licences['pagecache']['download']['version'], $scanModuleVersion) < 0
                                ) {
                                    $licences[$moduleName] = [
                                        "status" => '<span class="badge badge-success">Valid for tests</span>',
                                        "is_valid" => true,
                                        "download" => [
                                            "version" => $scanModuleVersion,
                                            "link" => "$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/$moduleName-$scanModuleVersion-ultimate-multiple.zip",
                                            "changelogs" => [$scanModuleVersion => 'Fake changelogs'],
                                            "dependencies" => []
                                        ],
                                        "latest" => [
                                            "version" => "10.1.1",
                                            //"link" => "$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/$moduleName-$scanModuleVersion-ultimate-multiple.zip",
                                            "changelogs" => [$scanModuleVersion => 'Fake changelogs'],
                                            "dependencies" => []
                                        ],
                                        "is_migration_pcu2sp" => false
                                    ];
                                    $licences['jprestaspeedpack'] = [
                                        "status" => '<span class="badge badge-success">Valid for tests</span>',
                                        "is_valid" => false,
                                        "link_renew" => "https://jpresta.com",
                                        "download" => [
                                            "version" => $scanModuleVersion,
                                            "link" => "$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/$moduleName-$scanModuleVersion-jprestaspeedpack-PS1.7.zip",
                                            "changelogs" => [$scanModuleVersion => 'Fake changelogs'],
                                            "dependencies" => []
                                        ],
                                        "latest" => [
                                            "version" => "10.1.1",
                                            //"link" => "$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/$moduleName-$scanModuleVersion-ultimate-multiple.zip",
                                            "changelogs" => [$scanModuleVersion => 'Fake changelogs'],
                                            "dependencies" => []
                                        ],
                                        "is_migration_pcu2sp" => false
                                    ];
                                }
                                break;
                            case 'jpresta-origin':
                                if (!isset($licences['jprestathemeorigin'])
                                    || version_compare($licences['jprestathemeorigin']['download']['version'], $scanModuleVersion) < 0
                                ) {
                                    $licences['jprestathemeorigin'] = [
                                        "status" => '<span class="badge badge-success">Valid for tests</span>',
                                        "is_valid" => true,
                                        "download" => [
                                            "version" => $scanModuleVersion,
                                            "link" => "$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/$moduleName-$scanModuleVersion-origin.zip",
                                            "changelogs" => [$scanModuleVersion => 'Fake changelogs'],
                                            "dependencies" => []
                                        ]
                                    ];
                                }
                                break;
                            default:
                                if (!isset($licences[$moduleName])
                                    || version_compare($licences[$moduleName]['download']['version'], $scanModuleVersion) < 0
                                ) {
                                    $licences[$moduleName] = [
                                        "status" => '<span class="badge badge-success">Valid for tests</span>',
                                        "is_valid" => true,
                                        "download" => [
                                            "version" => $scanModuleVersion,
                                            "link" => "$M2_DIR_JPRESTA/$moduleName/$scanModuleVersion/$moduleName-$scanModuleVersion.zip",
                                            "changelogs" => [$scanModuleVersion => 'Fake changelogs'],
                                            "dependencies" => []
                                        ]
                                    ];
                                }
                                break;
                        }
                    }
                }
            }
        }
        return $licences;
    }

    /**
     * @return bool true if this Prestashop instance seems to be a clone of an other Prestashop
     */
    public static function getPrestashopIsClone()
    {
        if (method_exists('JprestaApi', 'getPrestashopIsClone')) {
            return JprestaApi::getPrestashopIsClone();
        }
        if (self::getJPrestaAccountKey()) {
            $currentPrestashopChecksum = self::getPrestashopChecksum();
            $storedPrestashopChecksum = Configuration::get('jpresta_ps_checksum', null, 0, 0, false);
            if ($storedPrestashopChecksum === false) {
                self::setPrestashopChecksum();
                return false;
            }
            else {
                return $currentPrestashopChecksum != $storedPrestashopChecksum;
            }
        }
        return false;
    }

    /**
     * @param $isClone bool If true then a new Prestashop token is generated, if false it updates the current checksum of Prestashop
     */
    public static function setPrestashopIsClone($isClone)
    {
        if (method_exists('JprestaApi', 'setPrestashopIsClone')) {
            JprestaApi::setPrestashopIsClone($isClone);
        }
        else {
            if ($isClone) {
                Configuration::deleteByName('jpresta_ps_token');
                Configuration::deleteByName('jpresta_account_key');
                self::getPrestashopToken(true);
            }
            self::setPrestashopChecksum();
        }
    }

    /**
     * Make this Prestashop instance an original one (store the current checksum as the new one)
     */
    private static function setPrestashopChecksum()
    {
        Configuration::updateValue('jpresta_ps_checksum', self::getPrestashopChecksum(), false, 0, 0);
    }

    /**
     * @return string A checksum to identify the current Prestashop instance
     */
    private static function getPrestashopChecksum()
    {
        $checksum = '';
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>')) {
            // PS1.7
            $configFile = dirname(__FILE__) . '/../../../app/config/parameters.php';
            if (file_exists($configFile)) {
                $config = require $configFile;
                $checksum .= $config['parameters']['database_host'] . '|';
                $checksum .= $config['parameters']['database_name'] . '|';
            }
            else {
                // Is it possible?
                $checksum .= _DB_SERVER_ . '|';
                $checksum .= _DB_NAME_ . '|';
            }
        }
        else {
            // PS1.5 PS1.6
            $checksum .= _DB_SERVER_ . '|';
            $checksum .= _DB_NAME_ . '|';
        }
        return md5($checksum);
    }

    /**
     * @param bool $reset If true a new Prestashop token is generated
     * @return string A string that identify this Prestashop instance
     */
    public static function getPrestashopToken($reset = false)
    {
        if (method_exists('JprestaApi', 'getPrestashopToken')) {
            return JprestaApi::getPrestashopToken($reset);
        }
        if ($reset) {
            Configuration::deleteByName('jpresta_ps_token');
        }
        $token = Configuration::get('jpresta_ps_token', null, 0, 0, false);
        if (!$token) {
            // Generate a new token
            $token = 'PS-' . Tools::strtoupper(self::generateRandomString(12));
            Configuration::updateValue('jpresta_ps_token', $token, false, 0, 0);
            self::setPrestashopChecksum();
        }
        return $token;
    }

    public static function getPrestashopType() {
        if (method_exists('JprestaApi', 'getPrestashopType')) {
            return JprestaApi::getPrestashopType();
        }
        return Configuration::get('jpresta_ps_type', null, 0, 0, null);
    }

    public static function setPrestashopType($type) {
        if (method_exists('JprestaApi', 'setPrestashopType')) {
            JprestaApi::setPrestashopType($type);
        }
        else {
            Configuration::updateValue('jpresta_ps_type', $type === 'test' ? 'test' : 'prod', false, 0, 0);
        }
    }

    public static function getJPrestaAccountKey() {
        if (method_exists('JprestaApi', 'getJPrestaAccountKey')) {
            return JprestaApi::getJPrestaAccountKey();
        }
        return Configuration::get('jpresta_account_key', null, 0, 0, null);
    }

    public static function setJPrestaAccountKey($key) {
        if (method_exists('JprestaApi', 'setJPrestaAccountKey')) {
            JprestaApi::setJPrestaAccountKey($key);
        }
        else {
            Configuration::updateValue('jpresta_account_key', $key, false, 0, 0);
        }
    }

    private static function generateRandomString($length = 16) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $final_rand = '';
        for($i = 0; $i < $length; $i ++) {
            $final_rand .= $chars [rand ( 0, Tools::strlen ( $chars ) - 1 )];
        }
        return $final_rand;
    }

    /**
     * @param $message
     * @param int $severity 1 = info, 2 = warning, 3 = error, 4 = critical error
     * @param null $errorCode
     * @param null $objectType
     * @param null $objectId
     * @param bool $allowDuplicate
     * @param null $idEmployee
     */
    private static function addLog($message, $severity = 1, $errorCode = null, $objectType = null, $objectId = null, $allowDuplicate = false, $idEmployee = null)
    {
        if (class_exists('PrestaShopLogger')) {
            // Since PS 1.6.0.2
            PrestaShopLogger::addLog($message, $severity, $errorCode, $objectType, $objectId, $allowDuplicate, $idEmployee);
        }
        else {
            Logger::addLog($message, $severity, $errorCode, $objectType, $objectId, $allowDuplicate, $idEmployee);
        }
    }

    /**
     * @throws PrestaShopException
     */
    public static function getLicenses($useCache = true) {
        //$licences = $this->addJprestaUpgradeDep(json_decode(file_get_contents('C:\wamp314\www\ps1785\modules\jprestaupgrade\src\test\php\licenses_1.json'), true));
        $useCache = false;
        $cacheFile = _PS_MODULE_DIR_ . 'jprestaupgrade/' . md5(JprestaUpgradeApi::getJPrestaAccountKey()) . '.cache.tmp';
        $cacheTime = @filemtime($cacheFile);
        if (!$useCache || !$cacheTime || (time() - $cacheTime) > (24 * 60 * 60)) {
            $datas = JprestaUpgradeApi::getInstance()->getLicensesDatas();
            if (!is_array($datas)) {
                throw new PrestaShopException('Error while trying to get your licenses from your JPresta account: ' . $datas);
            }
            file_put_contents($cacheFile, serialize($datas));
            $licences = $datas;
        }
        else {
            $licences = unserialize(Tools::file_get_contents($cacheFile, false, null, 30, true));
        }

        return $licences;
    }

    public static function getModulesOrThemesInfos() {
        $infos = [];
        foreach (self::getUpdatableModulesAndThemes() as $moduleAndThemeInstance) {
            $datas = self::getModuleOrThemeInfos($moduleAndThemeInstance);
            $infos[$datas['name']] = $datas;
        }
        uasort($infos, [self::class, "cmpModulesOrThemesInfos"]);
        return $infos;
    }

    public static function getUpdatableCount() {
        $count = 0;
        foreach (self::getModulesOrThemesInfos() as $info) {
            if (isset($info['license']['download']) && $info['license']['download']['can_upgrade']) {
                $count++;
            }
        }
        return $count;
    }

    private static function getUpdatableModulesAndThemes()
    {
        $modulesAndThemesInstances = [];
        foreach (JprestaUpgradeApi::getJPrestaModules() as $jprestaModuleName) {
            $moduleInstance = Module::getInstanceByName($jprestaModuleName);
            // Child modules (anywhere) cannot be upgraded
            if ($moduleInstance && !property_exists($moduleInstance, 'jpresta_parent')) {
                $modulesAndThemesInstances[] = $moduleInstance;
            }
        }
        foreach (JprestaUpgradeApi::getJPrestaThemes() as $jprestaTheme) {
            $modulesAndThemesInstances[] = $jprestaTheme;
        }
        return $modulesAndThemesInstances;
    }

    private static function getModuleOrThemeInfos($moduleAndThemeInstance) {
        $infos = [];
        if ($moduleAndThemeInstance instanceof Module) {
            $commercialName = $moduleAndThemeInstance->name;
            $moduleOrThemeName = $moduleAndThemeInstance->name;
            $moduleOrThemeVersion = $moduleAndThemeInstance->version;
            $infos['displayName'] = $moduleAndThemeInstance->displayName;
            $infos['author'] = $moduleAndThemeInstance->author;
            $infos['description'] = $moduleAndThemeInstance->description;
            $infos['type'] = 'module';
            if (method_exists($moduleAndThemeInstance, 'getContent')) {
                $infos['configure_link'] = Context::getContext()->link->getAdminLink('AdminModules', true, [], ['configure' => $moduleAndThemeInstance->name, 'module_name' => $moduleAndThemeInstance->name]);
            }
        }
        else {
            $commercialName = $moduleAndThemeInstance->get('commercial_name');
            $moduleOrThemeName = $moduleAndThemeInstance->get('name');
            $moduleOrThemeVersion = $moduleAndThemeInstance->get('commercial_version');
            $initialModel = $moduleAndThemeInstance->get('initial_model');
            if ($initialModel == 'jpresta-model-odeco') {
                // A special patch for this version
                $commercialName = 'jprestathemeodeco';
            }
            $infos['displayName'] = $moduleAndThemeInstance->get('display_name');
            $infos['author'] = $moduleAndThemeInstance->get('author.name');
            $infos['description'] = '';
            $infos['type'] = 'theme';
        }
        $infos['commercial_name'] = $commercialName;
        $infos['name'] = $moduleOrThemeName;
        $infos['version'] = $moduleOrThemeVersion;
        $infosLicense = self::getLicenseInfos($commercialName, $moduleOrThemeVersion);
        if ($infosLicense) {
            $infos['license'] = $infosLicense;
        }
        return $infos;
    }

    public static function getModuleOrThemeInfosByName($moduleOrThemeName) {
        $infos = null;
        $moduleInstance = Module::getInstanceByName($moduleOrThemeName);
        if ($moduleInstance) {
            $infos = self::getModuleOrThemeInfos($moduleInstance);
        }
        else {
            $theme_repository = (new ThemeManagerBuilder(Context::getContext(), Db::getInstance()))->buildRepository();
            try {
                $theme = $theme_repository->getInstanceByName($moduleOrThemeName);
                $infos = self::getModuleOrThemeInfos($theme);
            }
            catch (PrestaShopException $e) {
                // Ignore
            }
        }
        return $infos;
    }

    public static function cmpModulesOrThemesInfos($a, $b)
    {
        // Negative to be the first
        if ($a == $b) {
            return 0;
        }
        if (isset($a['license']) !== isset($b['license'])) {
            return isset($a['license']) ? -1 : 1;
        }
        if (isset($a['license']['download']) !== isset($b['license']['download'])) {
            return isset($a['license']['download']) ? -1 : 1;
        }
        if (isset($a['license']['download']['dependencies']) !== isset($b['license']['download']['dependencies'])) {
            return isset($a['license']['download']['dependencies']) ? 1 : -1;
        }
        if (isset($a['license']['download']['dependencies']) && (count($a['license']['download']['dependencies']) - count($b['license']['download']['dependencies']) !== 0)) {
            // The module that have more dependencies will be the last
            return count($a['license']['download']['dependencies']) - count($b['license']['download']['dependencies']);
        }
        return strcmp($a['displayName'], $b['displayName']);
    }
}
