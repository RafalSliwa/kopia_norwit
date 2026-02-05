<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
 * @copyright 2010-2024 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 *
 *
 * \$this\-\>l\([\'\"]([a-zA-Z0-9\ \.\,\-\:\;\?\!\(\)\[\]\{\}\\\"\\/\&']*)[\'\"]
 * \$this\-\>trans\(\'$1\'\, \[\]\, \'Modules\.Seoredirect\.Seoredirect\'\)
 *
 */
require_once _PS_MODULE_DIR_ . 'seoredirect/models/seoRedirectList.php';
require_once _PS_MODULE_DIR_ . 'seoredirect/models/seoRedirectHistory.php';

class seoredirect extends Module
{
    public $searchTool;
    public function __construct()
    {
        $this->name = 'seoredirect';
        $this->tab = 'seo';
        $this->version = '2.3.2';
        $this->author = 'MyPresta.eu';
        $this->mypresta_link = 'https://mypresta.eu/modules/seo/seo-redirects-301-302-303.html';
        $this->bootstrap = true;
        parent::__construct();
        $this->secure_key = Tools::encrypt($this->name);
        $this->displayName = $this->getTranslator()->trans('SEO Redirect', [], 'Modules.Seoredirect.seoredirect');
        $this->description = $this->getTranslator()->trans('Module allows to create an unlimited number of 301, 302, 303 URL redirects to optimize the SEO of your shop.', [], 'Modules.Seoredirect.Seoredirect');
        $this->checkforupdates();
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        if ($part == 0) {
            return $exp[0];
        }
        if ($part == 1) {
            if ($exp[0] >= 8) {
                return 7;
            }
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function hookactionAdminControllerSetMedia($params)
    {
        //for update feature purposes
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->getTranslator()->trans('MyPresta updates', [], 'Modules.Seoredirect.Seoredirect') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->getTranslator()->trans('MyPresta updates', [], 'Modules.Seoredirect.Seoredirect') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->getTranslator()->trans('Check updates', [], 'Modules.Seoredirect.Seoredirect') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->getTranslator()->trans('Check now', [], 'Modules.Seoredirect.Seoredirect') . '
                                </button>
                            </div>
                            <label>' . $this->getTranslator()->trans('Updates notifications', [], 'Modules.Seoredirect.Seoredirect') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->getTranslator()->trans('-- select --', [], 'Modules.Seoredirect.Seoredirect') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->getTranslator()->trans('Enable', [], 'Modules.Seoredirect.Seoredirect') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->getTranslator()->trans('Disable', [], 'Modules.Seoredirect.Seoredirect') . '</option>
                                </select>
                                <p class="clear">' . $this->getTranslator()->trans('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.', [], 'Modules.Seoredirect.Seoredirect') . '</p>
                            </div>
                            <label>' . $this->getTranslator()->trans('Module page', [], 'Modules.Seoredirect.Seoredirect') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->getTranslator()->trans('This is direct link to official addon page, where you can read about changes in the module (changelog)', [], 'Modules.Seoredirect.Seoredirect') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates" class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->getTranslator()->trans('Save', [], 'Modules.Seoredirect.Seoredirect') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = seoredirectUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (seoredirectUpdate::version($this->version) < seoredirectUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->trans('New version available, check http://MyPresta.eu for more informations', [], 'Modules.Seoredirect.Seoredirect') .' <a href="' . $this->mypresta_link . '">' . $this->getTranslator()->trans('More details in changelog', [], 'Modules.Seoredirect.Seoredirect') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = seoredirectUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (seoredirectUpdate::version($this->version) < seoredirectUpdate::version(seoredirectUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->getTranslator()->trans('New version available!', [], 'Modules.Seoredirect.Seoredirect') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->getTranslator()->trans('Module is up to date!', [], 'Modules.Seoredirect.Seoredirect') . "</span>";
                    }
                }
            }
        }
    }

    public function getContent()
    {

        $this->searchTool = new searchToolseoredirect('seoredirect');
        return $this->checkforupdates(0, 1);
    }

    private function maybeUpdateDatabase($table, $column, $type = "int(8)", $default = "1", $null = "NULL", $onUpdate = '')
    {
        $sql = 'DESCRIBE ' . _DB_PREFIX_ . $table;
        $columns = Db::getInstance()->executeS($sql);
        $found = false;
        foreach ($columns as $col) {
            if ($col['Field'] == $column) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            if (!Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ADD `' . $column . '` ' . $type . ' DEFAULT ' . $default . ' ' . $null . ' ' . $onUpdate)) {
                return false;
            }
        }
        return true;
    }

    public function inconsistency($return_report = 1)
    {
        $this->maybeUpdateDatabase('seor', 'regexp', "VARCHAR(1)", 0, "NOT NULL");
        $this->maybeUpdateDatabase('seor', 'wildcard', "VARCHAR(1)", 0, "NOT NULL");
        $this->maybeUpdateDatabase('seor', 'id_shop', "INT(4)", 0, "NOT NULL");
        $this->maybeUpdateDatabase('seor', 'position', "INT(5)", 0, "NOT NULL");
        return true;
    }

    private function installdb()
    {
        $prefix = _DB_PREFIX_;
        $statements = array();
        $statements[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seor` (`id_seor` int(11) NOT NULL AUTO_INCREMENT,`old` text NOT NULL,`new` text NOT NULL,`redirect_type` varchar(10) NOT NULL,`date_add` datetime NOT NULL,`date_update` datetime NOT NULL, `active` int(1) DEFAULT 0, PRIMARY KEY (`id_seor`)) CHARSET=UTF8';
        $statements[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seor_stats` (`id_seor_stats` int(11) NOT NULL AUTO_INCREMENT, `id_seor` int(11), `stat_date` datetime NOT NULL, PRIMARY KEY (`id_seor_stats`)) CHARSET=UTF8';
        $statements[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seor_history` (`id_seor_history` int(11) NOT NULL AUTO_INCREMENT, url text NOT NULL, new text NOT NULL, `date_add` datetime NOT NULL, id_shop INT(6) DEFAULT 0, PRIMARY KEY (`id_seor_history`)) CHARSET=UTF8';
        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }
        $this->inconsistency(0);
        return true;
    }

    public function runStatement($statement)
    {
        if (@!Db:: getInstance()->Execute($statement)) {
            return false;
        }
        return true;
    }

    public function createMenu()
    {
        //parent menu
        $parent_tab = new Tab();
        $parent_tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $parent_tab->name[$lang['id_lang']] = 'Seo Redirect';
        }
        $parent_tab->class_name = 'AdminSeoRedirect';
        $parent_tab->id_parent = 0;
        $parent_tab->module = $this->name;
        $parent_tab->add();
        //settings
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Settings';
        }
        $tab->class_name = 'AdminSeoRedirectSettings';
        $tab->id_parent = $parent_tab->id;
        $tab->module = $this->name;
        $tab->add();
        //seo
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Redirections';
        }
        $tab->class_name = 'AdminSeoRedirectList';
        $tab->id_parent = $parent_tab->id;
        $tab->module = $this->name;
        $tab->add();

        //stats
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Statistics';
        }
        $tab->class_name = 'AdminSeoRedirectStats';
        $tab->id_parent = $parent_tab->id;
        $tab->module = $this->name;
        $tab->add();

        //history
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->getTranslator()->trans('Redirections log', [], 'Modules.Seoredirect.Seoredirect');
        }
        $tab->class_name = 'AdminSeoRedirectHistory';
        $tab->id_parent = $parent_tab->id;
        $tab->module = $this->name;
        $tab->add();

        //import
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Import from CSV';
        }
        $tab->class_name = 'AdminSeoRedirectImport';
        $tab->id_parent = $parent_tab->id;
        $tab->module = $this->name;
        $tab->add();
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminSeoRedirectSettings');
        $idTabs[] = Tab::getIdFromClassName('AdminSeoRedirect');
        $idTabs[] = Tab::getIdFromClassName('AdminSeoRedirectList');
        $idTabs[] = Tab::getIdFromClassName('AdminSeoRedirectStats');
        $idTabs[] = Tab::getIdFromClassName('AdminSeoRedirectImport');
        $idTabs[] = Tab::getIdFromClassName('AdminSeoRedirectHistory');
        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
        return true;
    }

    public function install()
    {
        if (!parent::install() or
            !Configuration::updateValue('IV_ROW_DELIMITER', '\r') or
            !Configuration::updateValue('IV_COL_DELIMITER', ';') ||
            !$this->installdb() || !$this->createMenu() ||
            !$this->registerHook('actionDispatcherBefore') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('actionUpdateQuantity') ||
            !$this->registerHook('actionProductUpdate') ||
            !$this->registerHook('actionValidateOrder')) {
            return false;
        } else {
            return true;
        }
    }

    public function runSeoRedirect()
    {
        $seor_logs = Configuration::get('seor_logs');
        if ((Configuration::get('seor_preview_on') == 1 && Tools::getValue('preview') == 1) || (Configuration::get('seor_preview_on') == 1 && Tools::getValue('ad', 'false') != 'false' && Tools::getValue('id_employee', 'false') != 'false' && Tools::getValue('adtoken', 'false') != 'false')) {
            return;
        }

        if (is_object(Context::getContext()->controller)) {
            if (isset(Context::getContext()->controller->php_self)) {
                if (Context::getContext()->controller->php_self == 'pagenotfound' && Configuration::get('seor_auto404') == 1) {
                    Tools::Redirect(Context::getContext()->shop->getBaseURL());
                }
            }
        }

        if (Module::isInstalled('seoredirect')) {
            $prefix = _DB_PREFIX_;
            $engine = _MYSQL_ENGINE_;
            $website = $_SERVER['REQUEST_URI'];
            $website_url = Tools::getShopProtocol() . $_SERVER['HTTP_HOST'] . $website;


            $shop_base_url = Context::getContext()->shop->getBaseURL(true, false);
            if (Configuration::get('seor_wildcards') == 1) {
                $array_of_entries_wildcards = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'seor WHERE active=1 AND id_shop="' . Context::getContext()->shop->id . '" ORDER BY position DESC');

                if (is_array($array_of_entries_wildcards)) {
                    foreach ($array_of_entries_wildcards as $redir) {
                        if ($redir['regexp'] == 1) {
                            $url = Tools::getShopProtocol() . $_SERVER['HTTP_HOST'] . $website;
                            $preg = $redir['old'];
                            $new = $redir['new'];
                            @preg_match('/^' . $preg . '/', $url, $match);

                            if ($match != NULL) {
                                if (is_array($match)) {
                                    $count = @count($match);
                                    if ($count > 0) {
                                        for ($i = 1; $i <= ($count - 1); $i++) {
                                            //echo $match[$i];
                                            //echo "<br>";
                                            $new = str_replace('{' . $i . '}', $match[$i], $new);
                                        }
                                        if (Configuration::get('seor_savestats') == 1) {
                                            Db::getInstance()->Execute('INSERT INTO `' . $prefix . 'seor_stats` (id_seor,stat_date) VALUES (' . $redir['id_seor'] . ',"' . date("y-m-d h:i:s).'") . '")');
                                        }
                                        if ($redir['redirect_type'] == 301) {
                                            $headers = 'HTTP/1.1 301 Moved Permanently';
                                        }
                                        if ($redir['redirect_type'] == 302) {
                                            $headers = 'HTTP/1.1 302 Moved Temporarily';
                                        }
                                        if ($redir['redirect_type'] == 303) {
                                            $headers = 'HTTP/1.1 303 See Other';
                                        }
                                        if ($redir['redirect_type'] == 410) {
                                            $headers = 'HTTP/1.1 410 Gone';
                                        }

                                        if ($seor_logs == true) {
                                            $log = new seoRedirectHistory();
                                            $log->url=$website_url;
                                            $log->new=$new;
                                            $log->id_shop = Context::getContext()->shop->id;
                                            $log->date_add = date("Y-m-d H:i:s");
                                            $log->add();
                                        }

                                        if ($headers) {
                                            if (!is_array($headers)) {
                                                $headers = array($headers);
                                            }
                                            foreach ($headers as $header) {
                                                header($header);
                                            }
                                        }
                                        header('Location: ' . $new);
                                        exit;
                                    }
                                }
                            }
                        }
                        if ($redir['wildcard'] == 1) {
                            if (fnmatch($redir['old'], $website)) {
                                if (Configuration::get('seor_savestats') == 1) {
                                    Db::getInstance()->Execute('INSERT INTO `' . $prefix . 'seor_stats` (id_seor,stat_date) VALUES (' . $redir['id_seor'] . ',"' . date("y-m-d h:i:s).'") . '")');
                                }
                                if ($redir['redirect_type'] == 301) {
                                    $headers = 'HTTP/1.1 301 Moved Permanently';
                                }
                                if ($redir['redirect_type'] == 302) {
                                    $headers = 'HTTP/1.1 302 Moved Temporarily';
                                }
                                if ($redir['redirect_type'] == 303) {
                                    $headers = 'HTTP/1.1 303 See Other';
                                }
                                if ($redir['redirect_type'] == 410) {
                                    $headers = 'HTTP/1.1 410 Gone';
                                }

                                if ($seor_logs == true) {
                                    $log = new seoRedirectHistory();
                                    $log->url = $website_url;
                                    $log->new = $redir['new'];
                                    $log->id_shop = Context::getContext()->shop->id;
                                    $log->date_add = date("Y-m-d H:i:s");
                                    $log->add();
                                }

                                if ($headers) {
                                    if (!is_array($headers)) {
                                        $headers = array($headers);
                                    }
                                    foreach ($headers as $header) {
                                        header($header);
                                    }
                                }
                                header('Location: ' . $redir['new']);
                                exit;
                            }
                        }
                    }
                }
            }

            $website = psql($website);
            $website_url = psql($website_url);

            $array_of_entries = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'seor WHERE (
            old="' . trim(urldecode($website)) . '" OR
            old="' . trim(str_replace(" ", "+", urldecode($website))) . '" OR

            old="' . trim(urldecode($website_url)) . '" OR
            old="' . trim(str_replace(" ", "+", urldecode($website_url))) . '" OR

            old="' . trim(urldecode($shop_base_url . $website)) . '" OR
            old="' . trim(str_replace(" ", "+", urldecode($shop_base_url . $website))) . '" OR

            old="' . trim($website_url) . '" OR
            old="' . trim(str_replace(" ", "+", $website_url)) . '" OR

            old="' . trim($website) . '" OR
            old="' . trim(str_replace(" ", "+", $website)) . '" OR

            old="' . trim($shop_base_url . $website) . '" OR
            old="' . trim(str_replace(" ", "+", $shop_base_url . $website)) . '") AND active=1 AND id_shop="' . Context::getContext()->shop->id . '"');

            if (is_array($array_of_entries)) {
                foreach ($array_of_entries as $redir) {
                    if ((trim(urldecode($website)) == $redir['old']) OR
                        (trim(str_replace(" ", "+", urldecode($website)) == $redir['old'])) OR
                        (trim(urldecode($shop_base_url . $website))) == $redir['old'] OR
                        (trim(str_replace(" ", "+", urldecode($shop_base_url . $website)))) == $redir['old'] OR
                        ($website == $redir['old']) OR $shop_base_url . $website == $redir['old'] OR
                        trim($website_url) == $redir['old']) {
                        if (Configuration::get('seor_savestats') == 1) {
                            Db::getInstance()->Execute('INSERT INTO `' . $prefix . 'seor_stats` (id_seor,stat_date) VALUES (' . $redir['id_seor'] . ',"' . date("y-m-d h:i:s).'") . '")');
                        }
                        if ($redir['redirect_type'] == 301) {
                            $headers = 'HTTP/1.1 301 Moved Permanently';
                        }
                        if ($redir['redirect_type'] == 302) {
                            $headers = 'HTTP/1.1 302 Moved Temporarily';
                        }
                        if ($redir['redirect_type'] == 303) {
                            $headers = 'HTTP/1.1 303 See Other';
                        }
                        if ($redir['redirect_type'] == 410) {
                            $headers = 'HTTP/1.1 410 Gone';
                        }

                        if ($seor_logs == true) {
                            $log = new seoRedirectHistory();
                            $log->url = $website_url;
                            $log->new = $redir['new'];
                            $log->id_shop = Context::getContext()->shop->id;
                            $log->date_add = date("Y-m-d H:i:s");
                            $log->add();
                        }

                        if ($headers) {
                            if (!is_array($headers)) {
                                $headers = array($headers);
                            }
                            foreach ($headers as $header) {
                                header($header);
                            }
                        }
                        header('Location: ' . $redir['new']);
                        exit;
                    }
                }
            }

            if (Configuration::get('seor_dm') == 1) {
                if (Tools::getValue('id_manufacturer', 'false') != 'false' && Tools::getValue('controller') == 'manufacturer') {
                    $manufacturer = new Manufacturer(Tools::getValue('id_manufacturer'));
                    if ($manufacturer->active == 0) {
                        if (Configuration::get('seor_dm_redirect_type') == 0) {
                            $headers = 'HTTP/1.1 301 Moved Permanently';
                        }
                        if (Configuration::get('seor_dm_redirect_type') == 1) {
                            $headers = 'HTTP/1.1 302 Moved Temporarily';
                        }
                        if (Configuration::get('seor_dm_redirect_type') == 2) {
                            $headers = 'HTTP/1.1 303 See Other';
                        }

                        if ($seor_logs == true) {
                            $log = new seoRedirectHistory();
                            $log->url = $website_url;
                            $log->new = $this->context->shop->getBaseURL();
                            $log->id_shop = Context::getContext()->shop->id;
                            $log->date_add = date("Y-m-d H:i:s");
                            $log->add();
                        }

                        header($headers);
                        header('Location: ' . $this->context->shop->getBaseURL());
                        exit;
                    }
                }
            }
            if (Tools::getValue('id_category', 'false') != 'false' && Tools::getValue('controller') == "category") {
                $category = new Category(Tools::getValue('id_category'));

                if (Configuration::get('seor_emptycat_products') == 1) {
                    $nb_products_in_category = $this->getNbProductsRecursive($category);
                } else {
                    $nb_products_in_category = $category->getWsNbProductsRecursive();
                }

                if (0 == 1) {
                    $nb_products_in_category_stock = 0;
                    //SELECT sum(quantity) as stock, sa.id_product as id_product FROM ps_category_product cp
                    //LEFT JOIN ps_stock_available sa ON cp.id_product = sa.id_product
                    //WHERE cp.id_category = 4
                    //GROUP BY sa.id_product
                }

                /**
                 * if ($category->id != NULL) {
                 * if (Configuration::get('seor_emptycat_oos') == 1 && $nb_products_in_category_stock <= 0 && Tools::getValue('id_category', 'false') != 2 && Tools::getValue('id_category', 'false') != 1) {
                 * $category->active = 0;
                 * $category->save();
                 * }
                 * }
                 * */

                if ($category->id != NULL && Configuration::get('seor_emptycat') > 0 && $nb_products_in_category <= 0 && Tools::getValue('id_category', 'false') != 2 && Tools::getValue('id_category', 'false') != 1) {

                    $exclusions_exploded = array();
                    $exclusions = Configuration::get('seor_emptycat_exclusions');
                    if ($exclusions != false && $exclusions != null && $exclusions != '') {
                        $exclusions_exploded = explode(",", $exclusions);
                    }

                    if (in_array($category->id, $exclusions_exploded)) {
                        $excluded = true;
                    } else {
                        $excluded = false;
                    }

                    if ($excluded == false) {
                        if (Configuration::get('seor_emptycat_redirect_type') == 0) {
                            $headers = 'HTTP/1.1 301 Moved Permanently';
                        }
                        if (Configuration::get('seor_emptycat_redirect_type') == 1) {
                            $headers = 'HTTP/1.1 302 Moved Temporarily';
                        }
                        if (Configuration::get('seor_emptycat_redirect_type') == 2) {
                            $headers = 'HTTP/1.1 303 See Other';
                        }
                        if (Configuration::get('seor_emptycat') == 1) {
                            $category->active = 0;
                            $category->save();
                        }
                        if (Configuration::get('seor_emptycat') == 2) {
                            $linkObject = new Link();
                            $category->active = 0;
                            $category->save();

                            if ($seor_logs == true) {
                                $log = new seoRedirectHistory();
                                $log->url = $website_url;
                                $log->new = $this->context->shop->getBaseURL();
                                $log->id_shop = Context::getContext()->shop->id;
                                $log->date_add = date("Y-m-d H:i:s");
                                $log->add();
                            }

                            header($headers);
                            header('Location: ' . $this->context->shop->getBaseURL());
                            exit;
                        }
                        if (Configuration::get('seor_emptycat') == 3) {
                            $linkObject = new Link();
                            if ($category->id_parent == 2 || $category->id_parent == 1) {
                                $link = $this->context->shop->getBaseURL();
                            } else {
                                $link = $linkObject->getCategoryLink($category->id_parent);
                            }
                            $category->active = 0;
                            $category->save();

                            if ($seor_logs == true) {
                                $log = new seoRedirectHistory();
                                $log->url = $website_url;
                                $log->new = $link;
                                $log->id_shop = Context::getContext()->shop->id;
                                $log->date_add = date("Y-m-d H:i:s");
                                $log->add();
                            }

                            header($headers);
                            header('Location: ' . $link);
                            exit;
                        }
                    }
                }
            }
            if (Tools::getValue('id_product', 'false') != 'false' && Tools::getValue('controller') == "product") {
                $product = new Product(Tools::getValue('id_product'), false);
                $product->loadStockData();
                if ((Configuration::get('seor_pins') || Configuration::get('seor_pos') > 0) && !in_array($product->id, explode(',', Configuration::get('seor_pos_exclude'))) && !in_array($product->id_category_default, explode(',', Configuration::get('seor_pos_exclude_cat')))) {

                    $break_action_product_not_in_category = 0;
                    if (strlen(Configuration::get('seor_oos_cat_include')) > 0) {
                        if (count(explode(',', Configuration::get('seor_oos_cat_include'))) > 0) {
                            if (!in_array($product->id, explode(',', Configuration::get('seor_oos_cat_include'))) && !in_array($product->id_category_default, explode(',', Configuration::get('seor_oos_cat_include')))) {
                                $break_action_product_not_in_category = 1;
                            }
                        }
                    }

                    if ($break_action_product_not_in_category == 0) {
                        if (in_array(Configuration::get('seor_pins'), array(1))) {
                            if ($product->quantity > 0 && $product->id != null) {
                                $categories = Configuration::get('seor_pins_category');
                                if ($categories != false && $product->id_category_default != $categories) {
                                    $product->deleteCategory($categories);
                                    $array_of_categories = array();
                                    foreach ($product->getCategories() AS $kcat => $vcat) {
                                        array_push($array_of_categories, $vcat);
                                    }
                                    if (!in_array($categories, $array_of_categories)) {
                                        $product->addToCategories($categories);
                                        $product->save();
                                    }
                                }
                            }
                        }

                        if (in_array(Configuration::get('seor_pins'), array(2))) {
                            if ($product->quantity > 0 && $product->id != null) {
                                $categories = Configuration::get('seor_pins_category');
                                if ($categories != false && $product->id_category_default != $categories) {
                                    $product->deleteCategory($categories);
                                    $product->save();
                                }
                            }
                        }
                        if (in_array(Configuration::get('seor_pos'), array(
                            1,
                            2,
                            3
                        ))) {
                            if (Configuration::get('seor_pos_redirect_type') == 0) {
                                $headers = 'HTTP/1.1 301 Moved Permanently';
                            }
                            if (Configuration::get('seor_pos_redirect_type') == 1) {
                                $headers = 'HTTP/1.1 302 Moved Temporarily';
                            }
                            if (Configuration::get('seor_pos_redirect_type') == 2) {
                                $headers = 'HTTP/1.1 303 See Other';
                            }
                            if ($product->quantity <= 0 && $product->active == 1 && $product->id != null) {
                                if (configuration::get('seor_dontato') == true) {
                                    if ($product->out_of_stock != 1) {
                                        $product->active = 0;
                                        $product->update();
                                    }
                                } else {
                                    $product->active = 0;
                                    $product->update();
                                }
                            }
                        }

                        if (Configuration::get('seor_pos') == 8) {
                            if ($product->quantity <= 0 && $product->id != null) {
                                $product->available_for_order = false;
                                $product->save();
                            }
                        }

                        if (in_array(Configuration::get('seor_pos'), array(
                            2,
                            3,
                            4,
                            5
                        ))) {
                            if (Configuration::get('seor_pos') == 2) {
                                if ($product->quantity <= 0 && $product->id != null) {
                                    $linkObject = new Link();
                                    $link = $linkObject->getCategoryLink($product->id_category_default);

                                    if ($seor_logs == true) {
                                        $log = new seoRedirectHistory();
                                        $log->url = $website_url;
                                        $log->new = $this->context->shop->getBaseURL();
                                        $log->id_shop = Context::getContext()->shop->id;
                                        $log->date_add = date("Y-m-d H:i:s");
                                        $log->add();
                                    }

                                    header($headers);
                                    header('Location: ' . $this->context->shop->getBaseURL());
                                    exit;
                                }
                            }
                            if (Configuration::get('seor_pos') == 3) {
                                if ($product->quantity <= 0 && $product->id != null) {
                                    $linkObject = new Link();
                                    $link = $linkObject->getCategoryLink($product->id_category_default);
                                    header($headers);

                                    if ($seor_logs == true) {
                                        $log = new seoRedirectHistory();
                                        $log->url = $website_url;
                                        $log->new = $link;
                                        $log->id_shop = Context::getContext()->shop->id;
                                        $log->date_add = date("Y-m-d H:i:s");
                                        $log->add();
                                    }

                                    header('Location: ' . $link);
                                    exit;
                                }
                            }
                            if (Configuration::get('seor_pos') == 4) {
                                if ($product->quantity <= 0 && $product->id != null) {
                                    $categories = Configuration::get('seor_pos_category');
                                    if ($categories != false && $product->id_category_default != $categories) {
                                        $product->deleteCategories();
                                        $product->addToCategories(array($categories));
                                        $product->id_category_default = $categories;
                                        $product->available_for_order = (Configuration::get('seor_unavorder') == 1 ? false : $product->available_for_order);
                                        $product->save();
                                        $linkObject = new Link();
                                        $link = $linkObject->getProductLink($product);

                                        if ($seor_logs == true) {
                                            $log = new seoRedirectHistory();
                                            $log->url = $website_url;
                                            $log->new = $link;
                                            $log->id_shop = Context::getContext()->shop->id;
                                            $log->date_add = date("Y-m-d H:i:s");
                                            $log->add();
                                        }

                                        header($headers);
                                        header('Location: ' . $link);
                                        exit;
                                    }
                                }
                            }
                            if (Configuration::get('seor_pos') == 5) {
                                if ($product->quantity <= 0 && $product->id != null) {
                                    $product->visibility = 'none';
                                    $product->indexed = 0;
                                    $product->save();
                                } else {
                                    if (Configuration::get('seor_re') == 1 && ($product->visibility == 'none' || $product->indexed == 0)) {
                                        if ($product->quantity > 0) {
                                            $product->visibility = 'both';
                                            $product->indexed = 1;
                                            $product->save();
                                        }
                                    }
                                }
                            }
                        }

                        if (Configuration::get('seor_pos') == 4 || Configuration::get('seor_pos') == 6 || Configuration::get('seor_pos') == 7) {
                            $categories = Configuration::get('seor_pos_category');
                            if ($categories != false && $product->id_category_default != $categories) {
                                if (Configuration::get('seor_pos') == 4) {
                                    $product->deleteCategories();
                                }
                                if (Configuration::get('seor_pos') == 7) {
                                    $product->deleteCategory($categories);
                                }
                                if (Configuration::get('seor_pos') == 4 || Configuration::get('seor_pos') == 6) {
                                    $product->deleteCategory($categories);
                                    $array_of_categories = array();
                                    foreach ($product->getCategories() AS $kcat => $vcat) {
                                        array_push($array_of_categories, $vcat);
                                    }
                                    if (!in_array($categories, $array_of_categories)) {
                                        $product->addToCategories($categories);
                                        $product->save();
                                    }
                                }
                                if (Configuration::get('seor_pos') == 4) {
                                    $product->id_category_default = $categories;
                                    $product->available_for_order = false;
                                }
                                $product->save();
                            }
                        }
                    }
                }

                if ($product->id == null) {
                    if (Configuration::get('seor_pd') == 1) {
                        if (Configuration::get('seor_pd_redirect_type') == 0) {
                            $headers = 'HTTP/1.1 301 Moved Permanently';
                        }
                        if (Configuration::get('seor_pd_redirect_type') == 1) {
                            $headers = 'HTTP/1.1 302 Moved Temporarily';
                        }
                        if (Configuration::get('seor_pd_redirect_type') == 2) {
                            $headers = 'HTTP/1.1 303 See Other';
                        }
                        if (Configuration::get('seor_pd') == 1) {
                            $redirectTo = $this->context->shop->getBaseURL();
                        }

                        if ($seor_logs == true) {
                            $log = new seoRedirectHistory();
                            $log->url = $website_url;
                            $log->new = $redirectTo;
                            $log->id_shop = Context::getContext()->shop->id;
                            $log->date_add = date("Y-m-d H:i:s");
                            $log->add();
                        }

                        header($headers);
                        header('Location: ' . $redirectTo);
                        exit;
                    }
                }
                if ($product->active != 1) {
                    if (Configuration::get('seor_re') == 1) {
                        if ($product->quantity > 0) {
                            $product->active = 1;
                            $product->update();
                        }
                    }
                    if (Configuration::get('seor_dp') == 1 || Configuration::get('seor_dp') == 2) {
                        if (Configuration::get('seor_dp_redirect_type') == 0) {
                            $headers = 'HTTP/1.1 301 Moved Permanently';
                        }
                        if (Configuration::get('seor_dp_redirect_type') == 1) {
                            $headers = 'HTTP/1.1 302 Moved Temporarily';
                        }
                        if (Configuration::get('seor_dp_redirect_type') == 2) {
                            $headers = 'HTTP/1.1 303 See Other';
                        }
                        if (Configuration::get('seor_dp') == 1) {
                            $redirectTo = $this->context->shop->getBaseURL();
                        }
                        if (Configuration::get('seor_dp') == 2) {
                            $linkObject = new Link();
                            $redirectTo = $linkObject->getCategoryLink($product->id_category_default);
                        }

                        if ($seor_logs == true) {
                            $log = new seoRedirectHistory();
                            $log->url = $website_url;
                            $log->new = $redirectTo;
                            $log->id_shop = Context::getContext()->shop->id;
                            $log->date_add = date("Y-m-d H:i:s");
                            $log->add();
                        }

                        header($headers);
                        header('Location: ' . $redirectTo);
                        exit;
                    }
                }
            }
        }
    }

    public function getNbProductsRecursive($category)
    {
        $db_subcategories = $category->getSubCategories($this->context->language->id, true);
        $subcategories = array();
        $subcategories[] = -1;
        foreach ($db_subcategories AS $cat) {
            $subcategories[] = $cat['id_category'];
        }
        $nbProductRecursive = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(distinct(cp.id_product))
			FROM  `' . _DB_PREFIX_ . 'category_product` cp
			' . Shop::addSqlAssociation('category', 'cp') . '
			INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product = cp.id_product)
			WHERE (cp.id_category IN (' . (int)$category->id . ', ' . implode(',', $subcategories) . ') AND p.active =1)
		');
        if (!$nbProductRecursive) {
            return -1;
        }

        return $nbProductRecursive;
    }

    public function hookactionDispatcherBefore($params)
    {
        $seor_logs = Configuration::get('seor_logs');
        $this->runSeoRedirect();
        if (Configuration::get('seor_dm') == 1) {
            if (Tools::getValue('id_manufacturer', 'false') != 'false' && Tools::getValue('controller') == 'manufacturer') {
                $manufacturer = new Manufacturer(Tools::getValue('id_manufacturer'));
                if ($manufacturer->active == 0) {
                    if (Configuration::get('seor_dm_redirect_type') == 0) {
                        $headers = 'HTTP/1.1 301 Moved Permanently';
                    }
                    if (Configuration::get('seor_dm_redirect_type') == 1) {
                        $headers = 'HTTP/1.1 302 Moved Temporarily';
                    }
                    if (Configuration::get('seor_dm_redirect_type') == 2) {
                        $headers = 'HTTP/1.1 303 See Other';
                    }

                    $website = $_SERVER['REQUEST_URI'];
                    $website_url = Tools::getShopProtocol() . $_SERVER['HTTP_HOST'] . $website;
                    if ($seor_logs == true) {
                        $log = new seoRedirectHistory();
                        $log->url = $website_url;
                        $log->new = $this->context->shop->getBaseURL();
                        $log->id_shop = Context::getContext()->shop->id;
                        $log->date_add = date("Y-m-d H:i:s");
                        $log->add();
                    }

                    header($headers);
                    header('Location: ' . $this->context->shop->getBaseURL());
                    exit;
                }
            }
        }
    }

    public function hookactionUpdateQuantity($params)
    {
        $this->actionOnStockUpdate($params);
    }

    public function hookactionProductUpdate($params)
    {
        //$this->actionOnStockUpdate($params);
    }

    public function actionOnStockUpdate($params)
    {
        if (isset($params['id_product'])) {
            $product = new Product($params['id_product'], false);
            $product->loadStockData();


            if ((Configuration::get('seor_pins') > 0 || Configuration::get('seor_pos') > 0) &&
                !in_array($product->id, explode(',', Configuration::get('seor_pos_exclude'))) &&
                !in_array($product->id_category_default, explode(',', Configuration::get('seor_pos_exclude_cat')))) {

                if (strlen(Configuration::get('seor_oos_cat_include')) > 0) {
                    if (count(explode(',', Configuration::get('seor_oos_cat_include'))) > 0) {
                        if (!in_array($product->id_category_default, explode(',', Configuration::get('seor_oos_cat_include')))) {
                            return;
                        }
                    }
                }

                //IN STOCK ASSIGN TO CATEGORY
                if (in_array(Configuration::get('seor_pins'), array(1))) {
                    if ($product->quantity > 0 && $product->id != null) {
                        $categories = Configuration::get('seor_pins_category');
                        if ($categories != false && $product->id_category_default != $categories) {
                            $product->deleteCategory($categories);
                            $array_of_categories = array();
                            foreach ($product->getCategories() AS $kcat => $vcat) {
                                array_push($array_of_categories, $vcat);
                            }
                            if (!in_array($categories, $array_of_categories)) {
                                $product->addToCategories($categories);
                                $product->save();
                            }
                        }
                    }
                }

                //IN STOCK UNASSIGN FROM CATEGORY
                if (in_array(Configuration::get('seor_pins'), array(2))) {
                    if ($product->quantity > 0 && $product->id != null) {
                        $categories = Configuration::get('seor_pins_category');
                        if ($categories != false && $product->id_category_default != $categories) {
                            $product->deleteCategory($categories);
                            $product->save();
                        }
                    }
                }

                //OUT OF STOCK
                if (in_array(Configuration::get('seor_pos'), array(1, 2, 3))) {
                    if ($product->quantity <= 0 && $product->active == 1 && $product->id != null) {
                        if (configuration::get('seor_dontato') == true) {
                            if ($product->out_of_stock != 1) {
                                $product->active = 0;
                                $product->update();
                            }
                        } else {
                            $product->active = 0;
                            $product->update();
                        }
                    }
                }

                if (in_array(Configuration::get('seor_pos'), array(4, 5, 6, 7, 8))) {
                    if ($product->quantity <= 0 && $product->id != null) {
                        $categories = Configuration::get('seor_pos_category');
                        if ($categories != false && $product->id_category_default != $categories) {
                            if (Configuration::get('seor_pos') == 4) {
                                $product->deleteCategories();
                                $product->save();
                            }

                            if (Configuration::get('seor_pos') == 7) {
                                $product->deleteCategory($categories);
                                $product->save();
                            }

                            if (Configuration::get('seor_pos') == 4 || Configuration::get('seor_pos') == 6) {
                                $categories = Configuration::get('seor_pos_category');
                                if ($categories != false && $product->id_category_default != $categories) {
                                    $product->deleteCategory($categories);
                                    $array_of_categories = array();
                                    foreach ($product->getCategories() AS $kcat => $vcat) {
                                        array_push($array_of_categories, $vcat);
                                    }
                                    if (!in_array($categories, $array_of_categories)) {
                                        $product->addToCategories($categories);
                                        $product->save();
                                    }
                                }
                            }

                            if (Configuration::get('seor_pos') == 4) {
                                $product->id_category_default = $categories;
                                $product->available_for_order = (Configuration::get('seor_unavorder') == 1 ? false : $product->available_for_order);
                            }

                            if (Configuration::get('seor_pos') == 8) {
                                if ($product->quantity <= 0 && $product->id != null) {
                                    $product->available_for_order = false;
                                    $product->save();
                                }
                            }

                            if (Configuration::get('seor_pos') == 5) {
                                if ($product->quantity <= 0 && $product->id != null) {
                                    $product->visibility = 'none';
                                    $product->indexed = 0;
                                    $product->save();
                                } else {
                                    if (Configuration::get('seor_re') == 1 && ($product->visibility == 'none' || $product->indexed == 0)) {
                                        if ($product->quantity > 0) {
                                            $product->visibility = 'both';
                                            $product->indexed = 1;
                                            $product->save();
                                        }
                                    }
                                }
                            }
                            $product->save();
                        }
                    }
                }
            }


            if (Configuration::get('seor_re') == 1 && $product->active != 1) {
                if ($product->quantity > 0) {
                    $product->active = 1;
                    $product->update();
                }
            }
        }
    }

    public function hookDisplayBackOfficeHeader()
    {

        $this->context->controller->addCSS(($this->_path) . 'views/css/seoredirect-admin.css', 'all');
    }

    public function hookactionValidateOrder($params)
    {
        if (in_array(Configuration::get('seor_pos'), array(1, 2, 3))) {
            foreach ($params['cart']->getProducts() as $product => $value) {
                $cartProduct = new Product($value['id_product'], true);
                if ($cartProduct->quantity <= 0) {
                    $cartProduct->active = 0;
                    $cartProduct->update();
                }
            }
        }
        if (in_array(Configuration::get('seor_pos'), array(4))) {
            foreach ($params['cart']->getProducts() as $product => $value) {
                $cartProduct = new Product($value['id_product'], true);
                if ($cartProduct->quantity <= 0) {
                    $cartProduct->visibility = 'both';
                    $cartProduct->indexed = 1;
                    $cartProduct->update();
                }
            }
        }
    }

    public function cronJob()
    {
        $category = '';
        $categories_counter = array();
        $enabled_categories_counter = array();
        if (Configuration::get('seor_emptycat') > 0) {
            $exclusions_exploded = array();
            $exclusions = Configuration::get('seor_emptycat_exclusions');
            if ($exclusions != false && $exclusions != null && $exclusions != '') {
                $exclusions_exploded = explode(",", $exclusions);
            }

            if (count($exclusions_exploded) > 0) {
                $where_excluded = ' AND pc.id_category NOT IN ('.Configuration::get('seor_emptycat_exclusions').')';
            } else {
                $where_excluded = '';
            }

            $empty_categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT * FROM ' . _DB_PREFIX_ . 'category pc
            INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON pc.id_category = cs.id_category AND cs.id_shop = ' . $this->context->shop->id . '
            WHERE NOT EXISTS (
                SELECT *
                FROM ' . _DB_PREFIX_ . 'category_product cp
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = cp.id_product AND ps.id_shop = ' . $this->context->shop->id . '
                WHERE pc.id_category = cp.id_category
            ) AND pc.id_parent > 1 AND pc.is_root_category <> 1' . $where_excluded);

            if (count($empty_categories) > 0) {
                foreach ($empty_categories AS $category) {
                    $cat = new Category($category['id_category'], $this->context->language->id);
                    if ($cat->active == 1) {

                        if (Configuration::get('seor_emptycat_products') == 1) {
                            $nb_products_in_category = $this->getNbProductsRecursive($cat);
                        } else {
                            $nb_products_in_category = $cat->getWsNbProductsRecursive();
                        }

                        if ($nb_products_in_category < 0) {
                            $cat->active = 0;
                            if ($cat->save()) {
                                $categories_counter[] = '(#' . $cat->id_category . ') ' . $cat->name;
                            }
                        }
                    }
                }
            }

            echo count($categories_counter) . ' ' . $this->getTranslator()->trans('categories disabled', [], 'Modules.Seoredirect.Seoredirect');
            if (count($categories_counter) > 0) {
                echo "<br/>";
                foreach ($categories_counter AS $category) {
                    echo $category . '</br>';
                }
            }


            //RE-ENABLE CATEGORIES
            $not_empty_categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT * FROM ' . _DB_PREFIX_ . 'category pc
            INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs ON pc.id_category = cs.id_category AND cs.id_shop = ' . $this->context->shop->id . '
            WHERE EXISTS (
                SELECT *
                FROM ' . _DB_PREFIX_ . 'category_product cp
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = cp.id_product AND ps.id_shop = ' . $this->context->shop->id . '
                WHERE pc.id_category = cp.id_category
            ) AND pc.id_parent > 1 AND pc.is_root_category <> 1 AND pc.active = 0' . $where_excluded);

            if (count($not_empty_categories) > 0) {
                foreach ($not_empty_categories AS $category) {
                    $cat = new Category($category['id_category'], $this->context->language->id);
                    if (Configuration::get('seor_emptycat_products') == 1) {
                        $nb_products_in_category = $this->getNbProductsRecursive($cat);
                    } else {
                        $nb_products_in_category = $cat->getWsNbProductsRecursive();
                    }
                    if ($nb_products_in_category > 0) {
                        $cat->active = 1;
                        if ($cat->save()) {
                            $enabled_categories_counter[] = '(#' . $cat->id_category . ') ' . $cat->name;
                        }
                    }
                }
            }
            echo "<br/><br/>";
            echo count($enabled_categories_counter) . ' ' . $this->getTranslator()->trans('categories re-enabled', [], 'Modules.Seoredirect.Seoredirect');
            if (count($enabled_categories_counter) > 0) {
                echo "<br/>";
                foreach ($enabled_categories_counter AS $category) {
                    echo $category . '</br>';
                }
            }
        }
        unset($category);

        if (in_array(Configuration::get('seor_pos'), array(
                1,
                2,
                3,
                5
            )) OR Configuration::get('seor_re') == 1
        ) {
            $exclude = '';
            if (strlen(Configuration::get('seor_pos_exclude')) > 0) {
                if (count(explode(',', Configuration::get('seor_pos_exclude'))) > 0) {
                    $exclude = 'AND id_product NOT IN (' . Configuration::get('seor_pos_exclude') . ') ';
                }
            }

            $exclude_cat = '';
            if (strlen(Configuration::get('seor_pos_exclude_cat')) > 0) {
                if (count(explode(',', Configuration::get('seor_pos_exclude_cat'))) > 0) {
                    $exclude_cat = 'AND id_category_default NOT IN (' . Configuration::get('seor_pos_exclude_cat') . ') ';
                }
            }

            if (strlen(Configuration::get('seor_oos_cat_include')) > 0) {
                if (count(explode(',', Configuration::get('seor_oos_cat_include'))) > 0) {
                    $exclude_cat .= 'AND id_category_default IN (' . Configuration::get('seor_oos_cat_include') . ') ';
                }
            }


            if (Configuration::get('seor_dontato') == true) {
                $where = " AND out_of_stock <> 1";
            } else {
                $where = "";
            }


            if (Configuration::get('seor_pos') == 5) {
                $visiblity_on = " indexed = 1, visibility = 'both'";
                $visiblity_off = " indexed = 0, visibility = 'none'";
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'product_shop` SET ' . $visiblity_off . ' WHERE id_shop = ' . $this->context->shop->id . ' ' . $exclude . ' ' . $exclude_cat . 'AND id_product IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'stock_available` WHERE quantity<=0 ' . $where . ')');
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'product_shop` SET ' . $visiblity_on . ' WHERE id_shop = ' . $this->context->shop->id . ' AND id_product IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'stock_available` WHERE quantity>0)');
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'product` SET ' . $visiblity_off . ' WHERE 1 = 1 ' . $exclude . ' ' . $exclude_cat . 'AND id_product IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'stock_available` WHERE quantity<=0 ' . $where . ')');
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'product` SET ' . $visiblity_on . ' WHERE 1 = 1 AND id_product IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'stock_available` WHERE quantity>0)');
            } elseif (Configuration::get('seor_pos') != 5) {
                $visiblity_on = 'active=1 ';
                $visiblity_off = 'active=0 ';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'product_shop` SET ' . $visiblity_off . ' WHERE id_shop = ' . $this->context->shop->id . ' ' . $exclude . ' ' . $exclude_cat . 'AND id_product IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'stock_available` WHERE quantity<=0 ' . $where . ')');
                Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('UPDATE `' . _DB_PREFIX_ . 'product_shop` SET ' . $visiblity_on . ' WHERE id_shop = ' . $this->context->shop->id . ' AND id_product IN (SELECT id_product FROM `' . _DB_PREFIX_ . 'stock_available` WHERE quantity>0)');
            }
        }
    }
}

class seoredirectUpdate extends seoredirect
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

if(file_exists(_PS_MODULE_DIR_ . 'seoredirect/lib/searchTool/searchTool.php')) {
    require_once _PS_MODULE_DIR_ . 'seoredirect/lib/searchTool/searchTool.php';
}