<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */
require_once _PS_MODULE_DIR_ . 'extratabspro/models/extratabpro.php';
require_once _PS_MODULE_DIR_ . 'extratabspro/models/extratabproextracontents.php';

class extratabspro extends Module
{
    protected static $cache_products;
    public $mypresta_link;
    public $mkey;
    public $nocategoriesfound;
    public $addcategory;
    public $nosuppliersfound;
    public $nomanufacturersfound;

    public function __construct()
    {
        @ini_set('display_errors', 0);
        @error_reporting(0);
        $this->name = 'extratabspro';
        $this->tab = 'front_office_features';
        $this->version = '2.4.6';
        $this->author = 'MyPresta.eu';
        $this->bootstrap = true;
        $this->module_key = 'fec8580f7892981a8a0c15598bc92cb5';
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/product-extra-tabs-pro.html';
        $this->displayName = $this->l('Extra Tabs Pro');
        $this->description = $this->l('Display unlimited number of extra tabs for any product you want');
        parent::__construct();
        $this->checkforupdates();

        $this->nocategoriesfound = $this->l('No categories found');
        $this->nomanufacturersfound = $this->l('No manufacturers found');
        $this->nosuppliersfound = $this->l('No suppliers found');
        $this->addcategory = $this->l('Add');
    }

    public function runStatement($statement)
    {
        if (@!Db:: getInstance()->Execute($statement)) {
            return false;
        }
        return true;
    }

    public function inconsistency($return_report = 1)
    {
        $form = '<style>
        .inconsistency0 {width:5px; background:red; padding:10px; border:1px solid red!important;}
        .inconsistency1 {width:5px;  background:green; padding:10px; border:1px solid green!important;}
        .inconsistency td {padding:5px; border:1px solid #c0c0c0;}
        </style>';
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;
        $table['extratabspro']['block_type']['type'] = "INT";
        $table['extratabspro']['block_type']['length'] = "4";
        $table['extratabspro']['block_type']['default'] = "1";
        $table['extratabspro']['categories']['type'] = "TEXT";
        $table['extratabspro']['categories']['length'] = "X";
        $table['extratabspro']['categories']['default'] = "X";
        $table['extratabspro']['manufacturers']['type'] = "TEXT";
        $table['extratabspro']['manufacturers']['length'] = "X";
        $table['extratabspro']['manufacturers']['default'] = "X";
        $table['extratabspro']['block_type2']['type'] = "INT";
        $table['extratabspro']['block_type2']['length'] = "4";
        $table['extratabspro']['block_type2']['default'] = "0";
        $table['extratabspro']['products']['type'] = "TEXT";
        $table['extratabspro']['products']['length'] = "X";
        $table['extratabspro']['products']['default'] = "X";
        $table['extratabspro']['block_type3']['type'] = "INT";
        $table['extratabspro']['block_type3']['length'] = "4";
        $table['extratabspro']['block_type3']['default'] = "0";
        $table['extratabspro']['cms_body']['type'] = "VARCHAR";
        $table['extratabspro']['cms_body']['length'] = "4";
        $table['extratabspro']['cms_body']['default'] = "0";
        $table['extratabspro']['cms']['type'] = "VARCHAR";
        $table['extratabspro']['cms']['length'] = "4";
        $table['extratabspro']['cms']['default'] = "0";
        $table['extratabspro']['geoip']['type'] = "VARCHAR";
        $table['extratabspro']['geoip']['length'] = "5";
        $table['extratabspro']['geoip']['default'] = '0';
        $table['extratabspro']['selected_geoip']['type'] = "TEXT";
        $table['extratabspro']['selected_geoip']['length'] = "X";
        $table['extratabspro']['selected_geoip']['default'] = "X";
        $table['extratabspro']['everywhere']['type'] = "VARCHAR";
        $table['extratabspro']['everywhere']['length'] = "1";
        $table['extratabspro']['everywhere']['default'] = '0';
        $table['extratabspro_lang']['internal_name']['type'] = "TEXT";
        $table['extratabspro_lang']['internal_name']['length'] = "X";
        $table['extratabspro_lang']['internal_name']['default'] = 'X';
        $table['extratabspro']['suppliers']['type'] = "TEXT";
        $table['extratabspro']['suppliers']['length'] = "X";
        $table['extratabspro']['suppliers']['default'] = "X";
        $table['extratabspro']['block_type4']['type'] = "INT";
        $table['extratabspro']['block_type4']['length'] = "4";
        $table['extratabspro']['block_type4']['default'] = "0";

        $table['extratabspro']['feature']['type'] = "INT";
        $table['extratabspro']['feature']['length'] = "4";
        $table['extratabspro']['feature']['default'] = "0";
        $table['extratabspro']['feature_v']['type'] = "VARCHAR";
        $table['extratabspro']['feature_v']['length'] = "250";
        $table['extratabspro']['feature_v']['default'] = '';

        $table['extratabspro']['id_shop']['type'] = "INT";
        $table['extratabspro']['id_shop']['length'] = "4";
        $table['extratabspro']['id_shop']['default'] = "1";

        $table['extratabspro']['for_groups']['type'] = "INT";
        $table['extratabspro']['for_groups']['length'] = "1";
        $table['extratabspro']['for_groups']['default'] = "0";
        $table['extratabspro']['groups']['type'] = "VARCHAR";
        $table['extratabspro']['groups']['length'] = "250";
        $table['extratabspro']['groups']['default'] = '';

        $table['extratabspro']['df']['type'] = "INT";
        $table['extratabspro']['df']['length'] = "1";
        $table['extratabspro']['df']['default'] = "0";
        $table['extratabspro']['date_from']['type'] = "VARCHAR";
        $table['extratabspro']['date_from']['length'] = "60";
        $table['extratabspro']['date_from']['default'] = '';
        $table['extratabspro']['tf']['type'] = "INT";
        $table['extratabspro']['tf']['length'] = "1";
        $table['extratabspro']['tf']['default'] = "0";
        $table['extratabspro']['time_from']['type'] = "VARCHAR";
        $table['extratabspro']['time_from']['length'] = "60";
        $table['extratabspro']['time_from']['default'] = '';
        $table['extratabspro']['dt']['type'] = "INT";
        $table['extratabspro']['dt']['length'] = "1";
        $table['extratabspro']['dt']['default'] = "0";
        $table['extratabspro']['date_to']['type'] = "VARCHAR";
        $table['extratabspro']['date_to']['length'] = "60";
        $table['extratabspro']['date_to']['default'] = '';
        $table['extratabspro']['tt']['type'] = "INT";
        $table['extratabspro']['tt']['length'] = "1";
        $table['extratabspro']['tt']['default'] = "0";
        $table['extratabspro']['time_to']['type'] = "VARCHAR";
        $table['extratabspro']['time_to']['length'] = "60";
        $table['extratabspro']['time_to']['default'] = '';

        $table['extratabspro']['allConditions']['type'] = "INT";
        $table['extratabspro']['allConditions']['length'] = "1";
        $table['extratabspro']['allConditions']['default'] = '0';

        $table['extratabspro']['allshops']['type'] = "INT";
        $table['extratabspro']['allshops']['length'] = "1";
        $table['extratabspro']['allshops']['default'] = '0';
        $table['extratabspro']['stock']['type'] = "INT";
        $table['extratabspro']['stock']['length'] = "1";
        $table['extratabspro']['stock']['default'] = '0';



        $return = array();

        //ps_extratabspro
        foreach (Db::getInstance()->executeS("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA ='" . _DB_NAME_ . "' AND TABLE_NAME='" . _DB_PREFIX_ . "extratabspro'") as $key => $column) {
            $return[$column['COLUMN_NAME']] = "1";
        }
        foreach ($table['extratabspro'] as $key => $field) {
            if (!isset($return[$key])) {
                $error[$key]['type'] = "0";
                $error[$key]['message'] = $this->l('Database inconsistency, column does not exist');
                if ($field['default'] != "X") {
                    if ($this->runStatement("ALTER TABLE `{$prefix}extratabspro` ADD COLUMN `" . $key . "` " . $field['type'] . "(" . $field['length'] . ") NULL DEFAULT '" . $field['default'] . "'")) {
                        $error[$key]['fixed'] = $this->l('... FIXED!');
                    } else {
                        $error[$key]['fixed'] = $this->l('... ERROR!');
                    }
                } else {
                    if ($this->runStatement("ALTER TABLE `{$prefix}extratabspro` ADD COLUMN `" . $key . "` " . $field['type'])) {
                        $error[$key]['fixed'] = $this->l('... FIXED!');
                    } else {
                        $error[$key]['fixed'] = $this->l('... ERROR!');
                    }
                }
                if (isset($field['config'])) {
                    Configuration::updateValue($field['config'], "1");
                }
            } else {
                $error[$key]['type'] = "1";
                $error[$key]['message'] = $this->l('OK!');
                $error[$key]['fixed'] = '';

                if (isset($field['config'])) {
                    Configuration::updateValue($field['config'], "1");
                }
            }
        }
        //ps_extratabspro_lang
        foreach (Db::getInstance()->executeS("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA ='" . _DB_NAME_ . "' AND TABLE_NAME='" . _DB_PREFIX_ . "extratabspro_lang'") as $key => $column) {
            $return[$column['COLUMN_NAME']] = "1";
        }
        foreach ($table['extratabspro_lang'] as $key => $field) {
            if (!isset($return[$key])) {
                $error[$key]['type'] = "0";
                $error[$key]['message'] = $this->l('Database inconsistency, column does not exist');
                if ($field['default'] != "X") {
                    if ($this->runStatement("ALTER TABLE `{$prefix}extratabspro_lang` ADD COLUMN `" . $key . "` " . $field['type'] . "(" . $field['length'] . ") NULL DEFAULT '" . $field['default'] . "'")) {
                        $error[$key]['fixed'] = $this->l('... FIXED!');
                    } else {
                        $error[$key]['fixed'] = $this->l('... ERROR!');
                    }
                } else {
                    if ($this->runStatement("ALTER TABLE `{$prefix}extratabspro_lang` ADD COLUMN `" . $key . "` " . $field['type'])) {
                        $error[$key]['fixed'] = $this->l('... FIXED!');
                    } else {
                        $error[$key]['fixed'] = $this->l('... ERROR!');
                    }
                }
                if (isset($field['config'])) {
                    Configuration::updateValue($field['config'], "1");
                }
            } else {
                $error[$key]['type'] = "1";
                $error[$key]['message'] = $this->l('OK!');
                $error[$key]['fixed'] = '';
                if (isset($field['config'])) {
                    Configuration::updateValue($field['config'], "1");
                }
            }
        }

        $form .= '<table class="inconsistency"><tr><td colspan="4" style="text-align:center">' . $this->l('Module upgrade consistency') . '</td></tr>';
        foreach ($error as $column => $info) {
            $form .= "<tr><td class='inconsistency" . $info['type'] . "'></td><td>" . $column . "</td><td>" . $info['message'] . "</td><td>" . $info['fixed'] . "</td></tr>";
        }
        $form .= "</table>";
        if ($return_report == 1) {
            //return $form;
        } else {
            return true;
        }
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
            ' . ($this->psversion() == 6 || $this->psversion() == 7 || $this->psversion(0) == 8 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
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
                        $actual_version = extratabsproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (extratabsproUpdate::version($this->version) < extratabsproUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = extratabsproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (extratabsproUpdate::version($this->version) < extratabsproUpdate::version(extratabsproUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function install()
    {
        $sql = array();

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'extratabspro_temp` (
                  `id_tab_template` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY (`id_tab_template`),
                  UNIQUE  `id_tab_unique` ( `id_tab_template`)
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'extratabspro_temp_lang` (
                  `id_tab_template` int(10) unsigned NOT NULL,
                  `id_lang` int(10) unsigned NOT NULL,
                  `body` TEXT NULL,
                  `name` TEXT NULL
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'extratabsproextracontents` (
                  `id_extracontents` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `id_tab` INT( 11 ) UNSIGNED NOT NULL,
                  `id_product` INT( 11 ) UNSIGNED NOT NULL,
                  PRIMARY KEY (`id_extracontents`),
                  UNIQUE  `id_tab_unique` (  `id_extracontents` )
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'extratabsproextracontents_lang` (
                  `id_extracontents` int(10) unsigned NOT NULL,
                  `id_lang` int(10) unsigned NOT NULL,
                  `body` TEXT NULL
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';


        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'extratabspro` (
                  `id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `id_product` INT( 11 ) UNSIGNED NOT NULL,
                  `active` INT( 1 ) UNSIGNED NOT NULL,
                  `position` INT( 3 ) UNSIGNED NOT NULL,
                  PRIMARY KEY (`id_tab`),
                  UNIQUE  `id_tab_unique` (  `id_tab` )
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'extratabspro_lang` (
                  `id_tab` int(10) unsigned NOT NULL,
                  `id_lang` int(10) unsigned NOT NULL,
                  `body` TEXT NULL,
                  `name` TEXT NULL
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';


        if (!parent::install() or !Configuration::updateValue('update_' . $this->name, '0') or
            !$this->registerHook('displayProductExtraContent') or
            !$this->registerHook('productTab') or
            !$this->registerHook('actionProductAdd') or
            !$this->registerHook('productTabContent') or
            !$this->registerHook('displayAdminProductsExtra') or
            !$this->registerHook('displayFooterProduct') or
            !$this->registerHook('actionAdminControllerSetMedia') or
            !$this->registerHook('displayHeader') or
            !Configuration::updateValue('update_' . $this->name, '0') or !$this->runSql($sql)) {
            return false;
        }

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

    public function runSql($sql)
    {
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                //return FALSE;
            }
        }
        //call inconsistency to update module database
        $this->inconsistency(0);

        //and return true to continue installation
        return true;
    }

    public function searchsupplier($search)
    {
        return Db::getInstance()->ExecuteS('SELECT `id_supplier`,`name` FROM `' . _DB_PREFIX_ . 'supplier` WHERE `name` like "%' . pSQL($search) . '%" LIMIT 10');
    }

    public function getSuppliers($id)
    {
        return Db::getInstance()->ExecuteS('SELECT `id_supplier` FROM `' . _DB_PREFIX_ . 'product_supplier` WHERE `id_product`= ' . pSQL($id) . ' GROUP BY id_supplier');
    }

    public function searchfeature($search)
    {
        return Db::getInstance()->ExecuteS('SELECT `id_feature_value`,`value` as name FROM `' . _DB_PREFIX_ . 'feature_value_lang` WHERE `value` like "%' . pSQL($search) . '%" AND id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '" LIMIT 10');
    }

    public function searchcategory($search)
    {
        return Db::getInstance()->ExecuteS('SELECT `id_category`,`name` FROM `' . _DB_PREFIX_ . 'category_lang` WHERE `name` like "%' . pSQL($search) . '%" AND id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '" AND id_shop="' . $this->context->shop->id . '" LIMIT 10');
    }

    public function searchproduct($search)
    {
        return Db::getInstance()->ExecuteS('SELECT `id_product`,`name` FROM `' . _DB_PREFIX_ . 'product_lang` WHERE `name` like "%' . pSQL($search) . '%" AND id_lang="' . Configuration::get('PS_LANG_DEFAULT') . '" AND id_shop="' . $this->context->shop->id . '" LIMIT 10');
    }

    public function searchmanufacturer($search)
    {
        return Db::getInstance()->ExecuteS('SELECT m.`id_manufacturer`,m.`name` FROM `' . _DB_PREFIX_ . 'manufacturer` m LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms ON ms.id_manufacturer = m.id_manufacturer WHERE `name` like "%' . pSQL($search) . '%" AND ms.id_shop="' . $this->context->shop->id . '" LIMIT 10');
    }

    public function getproducttabs()
    {
        $query = "SELECT * FROM `" . _DB_PREFIX_ . "extratabspro` as a LEFT JOIN `" . _DB_PREFIX_ . "extratabspro_lang` as b ON a.id_tab = b.id_tab";
        return Db::getInstance()->ExecuteS($query);
    }

    public function getCustomerGroups()
    {
        $customer_groups = array();
        if (isset($this->context->cart->id_customer)) {
            if ($this->context->cart->id_customer == 0) {
                // VISITOR
                $customer_groups[1] = 1;
            } else {
                // CUSTOMER
                foreach (Customer::getGroupsStatic($this->context->cart->id_customer) as $group) {
                    $customer_groups[$group] = 1;
                }
            }
        } elseif ($this->context->customer->is_guest == 1) {
            $customer_groups[1] = 2;
        } else {
            // VISITOR
            $customer_groups[1] = 1;
        }
        if (count($customer_groups) > 0) {
            return $customer_groups;
        } else {
            return false;
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if (!in_array(Tools::getValue('controller', 'false'), array('IqitElementorEditor','FieldElementorEditor'))) {
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJqueryUI('ui.draggable');
            $this->context->controller->addJqueryUI('ui.droppable');
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->context->controller->addCSS($this->_path . 'views/admin/extratabspro.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/admin/extratabspro.js');
            if (version_compare(substr(_PS_VERSION_, 0, 5), '8.1.0', '>=')) {
                $this->context->controller->addJS($this->_path . 'views/admin/extratabspro8.js');
            } else {
                $this->context->controller->addJS($this->_path . 'views/admin/extratabspro.js');
            }

            //$this->context->controller->addJquery('jquery-ui.will.be.removed.in.1.6');
        }
    }

    public function hookdisplayHeader($params)
    {
        $this->context->controller->addJS($this->_path . 'views/js/extratabspro.js', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/extratabspro.css', 'all');
    }

    public function hookactionProductAdd($params)
    {
        if (Tools::getisset('duplicateproduct')) {
            $tabs = Extratabpro::loadByIdProduct(Tools::getValue('id_product'));
            foreach ($tabs as $tab => $value) {
                if ($value->block_type3 == 1) {
                    $associate_tab_with_product = 0;
                    $update_tab = new Extratabpro($value->id_tab);
                    $array_products = explode(',', $update_tab->products);
                    if (count($array_products) > 0) {
                        foreach ($array_products AS $key => $var) {
                            if ($var == Tools::getValue('id_product')) {
                                $associate_tab_with_product = 1;
                            }
                        }
                        if ($associate_tab_with_product == 1) {
                            $update_tab->products = $update_tab->products .= "," . $params['product']->id;
                            $update_tab->update();
                        }
                    }
                }

                foreach (Extratabproextracontents::getByProductAndTabId(Tools::getValue('id_product'), $value->id_tab) as $extracontents) {
                    $base_extracontents = new Extratabproextracontents($extracontents['id_extracontents']);
                    $new_extracontents = new Extratabproextracontents();
                    $new_extracontents->id_product = $params['product']->id;
                    $new_extracontents->id_tab = $value->id_tab;
                    $new_extracontents->body = $base_extracontents->body;
                    $new_extracontents->add();
                }
            }
            $this->_clearCache('*');
        }
    }

    public function returnUserCountry()
    {
        $record = false;
        if (!in_array($_SERVER['SERVER_NAME'], array(
            'localhost',
            '127.0.0.1'
        ))
        ) {
            /* Check if Maxmind Database exists */
            if (@filemtime(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_)) {
                $reader = new GeoIp2\Database\Reader(_PS_GEOIP_DIR_ . _PS_GEOIP_CITY_FILE_);
                try {
                    $record = $reader->city(Tools::getRemoteAddr());
                } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                    $record = null;
                }

                if (isset($record->country->isoCode)) {
                    return $record->country->isoCode;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function countriesSelection($selected = false)
    {
        if ($selected) {
            $selected_array = explode(',', $selected);
        } else {
            $selected_array = array();
        }
        $form = '<div class="well" style="height: 300px; overflow-y: auto;">
			<table class="table" style="border-spacing : 0; border-collapse : collapse;">
				<thead>
					<tr>
						<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, \'extratabspro_selected_geoip[]\', this.checked)"></th>
						<th>' . $this->l('Name') . '</th>
					</tr>
				</thead>
				<tbody>';

        foreach (Country::getCountries($this->context->language->id) AS $key => $country) {
            $element_selected = "";
            foreach ($selected_array AS $item => $selected) {
                if ($selected == $country['iso_code']) {
                    $element_selected = "checked";
                }
            }
            $form .= '
                    <tr>
                        <td style="width:30px;"><input type="checkbox" name="extratabspro_selected_geoip[]" value="' . $country['iso_code'] . '" ' . $element_selected . '></td>
                        <td>' . $country['name'] . '</td>
                    </tr>';
        }
        $form .= '
			    </tbody>
			</table>
		</div>';

        return $form;
    }

    public function groupsSelection($selected = false)
    {
        if ($selected) {
            $selected_array = explode(',', $selected);
        } else {
            $selected_array = array();
        }
        $form = '<div class="well" style="height: 200px; overflow-y: auto;">
			<table class="table" style="border-spacing : 0; border-collapse : collapse;">
				<thead>
					<tr>
						<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, \'extratabspro_groups[]\', this.checked)"></th>
						<th>' . $this->l('Group Name') . '</th>
					</tr>
				</thead>
				<tbody>';

        foreach (Group::getGroups($this->context->language->id) AS $key => $group) {
            $element_selected = "";
            foreach ($selected_array AS $item => $selected) {
                if ($selected == $group['id_group']) {
                    $element_selected = "checked";
                }
            }
            $form .= '
                    <tr>
                        <td style="width:30px;"><input type="checkbox" name="extratabspro_groups[]" value="' . $group['id_group'] . '" ' . $element_selected . '></td>
                        <td>' . $group['name'] . '</td>
                    </tr>';
        }
        $form .= '
			    </tbody>
			</table>
		</div>';

        return $form;
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $_GET['id_product'] = $params['id_product'];
        $product_extratabs = null;
        if (isset($_POST['action'])) {
            if ($_POST['action'] == "addnew") {
                $extratab = new Extratabpro();
                if (Tools::getValue("without_product", "false") != "false") {
                    $extratab->id_product = Tools::getValue('id_product');
                } else {
                    $extratab->id_product = 0;
                }
                $extratab->allshops = Tools::getValue('extratabspro_allshops');
                $extratab->position = "999";
                $extratab->active = "0";
                $extratab->body = $_POST['mbbody'];
                $extratab->name = $_POST['title'];
                $extratab->internal_name = $_POST['titlein'];
                $extratab->block_type = Tools::getValue('block_type');
                $extratab->categories = Tools::getValue('categories_block');
                $extratab->block_type2 = Tools::getValue('block_type2');
                $extratab->manufacturers = Tools::getValue('manufacturers_block');
                $extratab->block_type3 = Tools::getValue('block_type3');
                $extratab->products = Tools::getValue('products_block');
                $extratab->block_type4 = Tools::getValue('block_type4');
                $extratab->suppliers = Tools::getValue('suppliers_block');
                $extratab->cms = Tools::getValue('extratabspro_cms');
                $extratab->cms_body = Tools::getValue('extratabspro_cms_body');
                $extratab->geoip = Tools::getValue('extratabspro_geoip', 0);
                $extratab->everywhere = Tools::getValue('extratabspro_everywhere', 0);
                $extratab->feature = Tools::getValue('etab_feature', 0);
                $extratab->feature_v = implode(',', Tools::getValue('etab_feature_v', array()));
                $extratab->for_groups = Tools::getValue('extratabspro_for_groups', 0);
                $extratab->groups = implode(',', Tools::getValue('extratabspro_groups', array()));
                $extratab->df = Tools::getValue('extratabspro_df', 0);
                $extratab->tf = Tools::getValue('extratabspro_tf', 0);
                $extratab->dt = Tools::getValue('extratabspro_dt', 0);
                $extratab->tt = Tools::getValue('extratabspro_tt', 0);
                $extratab->date_from = Tools::getValue('extratabspro_datefrom', 0);
                $extratab->date_to = Tools::getValue('extratabspro_dateto', 0);
                $extratab->time_from = Tools::getValue('extratabspro_timefrom', 0);
                $extratab->time_to = Tools::getValue('extratabspro_timeto', 0);
                $extratab->allConditions = Tools::getValue('extratabspro_allconditions', 0);
                $extratab->stock = Tools::getValue('extratabspro_stock', 0);
                if (Tools::getValue('extratabspro_geoip') == 1) {
                    $extratab->selected_geoip = implode(',', Tools::getValue('extratabspro_selected_geoip'));
                }
                $extratab->add();
                $this->_clearCache('*');
            }
        }

        if (isset($_POST['action'])) {
            if ($_POST['action'] == "updateblock") {
                if (Tools::getValue('save_only_for_this_product') == 1) {
                    $return = Extratabproextracontents::getByProductAndTabId(Tools::getValue('id_product'), Tools::getValue('id_tab'));
                    if (isset($return['0']['id_extracontents'])) {
                        $extracontents = new Extratabproextracontents($return['0']['id_extracontents']);
                        $extracontents->body = $_POST['mbbody'];
                        $extracontents->update();
                    } else {
                        $extracontents = new Extratabproextracontents();
                        $extracontents->id_tab = $_POST['id_tab'];
                        $extracontents->id_product = $_POST['id_product'];
                        $extracontents->body = $_POST['mbbody'];
                        $extracontents->add();
                    }
                    $extratab = new Extratabpro($_POST['id_tab']);
                    $extratab->name = $_POST['title'];
                    $extratab->allshops = Tools::getValue('extratabspro_allshops');
                    $extratab->internal_name = $_POST['titlein'];
                    $extratab->block_type = Tools::getValue('block_type');
                    $extratab->categories = Tools::getValue('categories_block');
                    $extratab->block_type2 = Tools::getValue('block_type2');
                    $extratab->manufacturers = Tools::getValue('manufacturers_block');
                    $extratab->block_type3 = Tools::getValue('block_type3');
                    $extratab->products = Tools::getValue('products_block');
                    $extratab->block_type4 = Tools::getValue('block_type4');
                    $extratab->suppliers = Tools::getValue('suppliers_block');
                    $extratab->cms = Tools::getValue('extratabspro_cms');
                    $extratab->cms_body = Tools::getValue('extratabspro_cms_body');
                    $extratab->geoip = Tools::getValue('extratabspro_geoip', 0);
                    $extratab->everywhere = Tools::getValue('extratabspro_everywhere', 0);
                    $extratab->feature = Tools::getValue('etab_feature', 0);
                    $extratab->feature_v = implode(',', Tools::getValue('etab_feature_v', array()));
                    $extratab->for_groups = Tools::getValue('extratabspro_for_groups', 0);
                    $extratab->groups = implode(',', Tools::getValue('extratabspro_groups', array()));
                    $extratab->df = Tools::getValue('extratabspro_df', 0);
                    $extratab->tf = Tools::getValue('extratabspro_tf', 0);
                    $extratab->dt = Tools::getValue('extratabspro_dt', 0);
                    $extratab->tt = Tools::getValue('extratabspro_tt', 0);
                    $extratab->date_from = Tools::getValue('extratabspro_datefrom', 0);
                    $extratab->date_to = Tools::getValue('extratabspro_dateto', 0);
                    $extratab->time_from = Tools::getValue('extratabspro_timefrom', 0);
                    $extratab->time_to = Tools::getValue('extratabspro_timeto', 0);
                    $extratab->allConditions = Tools::getValue('extratabspro_allconditions', 0);
                    $extratab->stock = Tools::getValue('extratabspro_stock', 0);
                    if (Tools::getValue('extratabspro_geoip') == 1) {
                        $extratab->selected_geoip = implode(',', Tools::getValue('extratabspro_selected_geoip'));
                    }
                    $extratab->update();
                    $this->_clearCache('*');

                } else {
                    $return = Extratabproextracontents::getByProductAndTabId(Tools::getValue('id_product'), Tools::getValue('id_tab'));
                    if (isset($return['0']['id_extracontents'])) {
                        $extracontents = new Extratabproextracontents($return['0']['id_extracontents']);
                        $extracontents->delete();
                    }
                    $extratab = new Extratabpro($_POST['id_tab']);
                    $extratab->allshops = Tools::getValue('extratabspro_allshops');
                    $extratab->body = $_POST['mbbody'];
                    $extratab->name = $_POST['title'];
                    $extratab->internal_name = $_POST['titlein'];
                    $extratab->block_type = Tools::getValue('block_type');
                    $extratab->categories = Tools::getValue('categories_block');
                    $extratab->block_type2 = Tools::getValue('block_type2');
                    $extratab->manufacturers = Tools::getValue('manufacturers_block');
                    $extratab->block_type3 = Tools::getValue('block_type3');
                    $extratab->products = Tools::getValue('products_block');
                    $extratab->block_type4 = Tools::getValue('block_type4');
                    $extratab->suppliers = Tools::getValue('suppliers_block');
                    $extratab->cms = Tools::getValue('extratabspro_cms');
                    $extratab->cms_body = Tools::getValue('extratabspro_cms_body');
                    $extratab->geoip = Tools::getValue('extratabspro_geoip', 0);
                    $extratab->everywhere = Tools::getValue('extratabspro_everywhere', 0);
                    $extratab->feature = Tools::getValue('etab_feature', 0);
                    $extratab->feature_v = implode(',', Tools::getValue('etab_feature_v', array()));
                    $extratab->for_groups = Tools::getValue('extratabspro_for_groups', 0);
                    $extratab->groups = implode(',', Tools::getValue('extratabspro_groups', array()));
                    $extratab->df = Tools::getValue('extratabspro_df', 0);
                    $extratab->tf = Tools::getValue('extratabspro_tf', 0);
                    $extratab->dt = Tools::getValue('extratabspro_dt', 0);
                    $extratab->tt = Tools::getValue('extratabspro_tt', 0);
                    $extratab->date_from = Tools::getValue('extratabspro_datefrom', 0);
                    $extratab->date_to = Tools::getValue('extratabspro_dateto', 0);
                    $extratab->time_from = Tools::getValue('extratabspro_timefrom', 0);
                    $extratab->time_to = Tools::getValue('extratabspro_timeto', 0);
                    $extratab->allConditions = Tools::getValue('extratabspro_allconditions', 0);
                    $extratab->stock = Tools::getValue('extratabspro_stock', 0);
                    if (Tools::getValue('extratabspro_geoip') == 1) {
                        $extratab->selected_geoip = implode(',', Tools::getValue('extratabspro_selected_geoip'));
                    }
                    $extratab->update();
                    $this->_clearCache('*');

                }
            }
        }

        if (isset($_GET['editblock'])) {
            $this_extratab = new extratabpro((int)$_GET['editblock']);
            $check_extra_contents = Extratabproextracontents::getByProductAndTabId(Tools::getValue('id_product'), (int)$_GET['editblock']);
            if (isset($check_extra_contents['0']['id_extracontents'])) {
                $extracontents = new Extratabproextracontents($check_extra_contents['0']['id_extracontents']);
                $this_extratab->body = $extracontents->body;
            }
            $this->context->smarty->assign(array(
                'extratabpro' => $this_extratab,
                'employee_idlang' => $this->context->language->id,
                'thismodule' => $this
            ));
        }

        $thisproduct = new Product(Tools::getValue('id_product'));
        $blocks_prepare = extratabpro::loadByIdProduct(Tools::getValue('id_product'));
        $product_suppliers = $this->getSuppliers($thisproduct->id);
        $product_features = Product::getFeaturesStatic($thisproduct->id);
        $product_manufacturer = $thisproduct->id_manufacturer;
        $product_categories = Product::getProductCategories(Tools::getValue('id_product'));
        $i = 0;
        if (is_array($blocks_prepare)) {
            if (count($blocks_prepare) > 0) {
                foreach ($blocks_prepare as $key => $value) {
                    $tab_included = 0;
                    $i = $value->id_tab;

                    //TABS EVERYWHERE
                    if ($value->everywhere == 1) {
                        $product_extratabs[$i] = $value;
                        $tab_included = 1;
                    }

                    //TABS BASED ON CATEGORIES
                    if ($value->block_type == 2 && $tab_included == 0) {
                        foreach (explode(",", $value->categories) as $exk) {
                            foreach ($product_categories as $pk => $pv) {
                                if ($pv == $exk) {
                                    if ($tab_included == 0) {
                                        $product_extratabs[$i] = $value;
                                        $tab_included = 1;
                                    }
                                }
                            }
                        }
                    }

                    //TABS FOR PRODUCTS
                    if ($value->block_type3 == 1 && $tab_included == 0) {
                        foreach (explode(",", $value->products) as $exp) {
                            if (Tools::getValue('id_product') == $exp) {
                                if ($tab_included == 0) {
                                    $product_extratabs[$i] = $value;
                                    $tab_included = 1;
                                }
                            }
                        }
                        if ($tab_included == 0 && Tools::getValue('id_product') == $value->id_product) {
                            $product_extratabs[$i] = $value;
                        }
                    }

                    //TABS FOR MANUFACTURERS
                    if ($value->block_type2 == 1 && $tab_included == 0) {
                        foreach (explode(",", $value->manufacturers) as $exm) {
                            if ($product_manufacturer == $exm) {
                                if ($tab_included == 0) {
                                    $product_extratabs[$i] = $value;
                                    $tab_included = 1;
                                }
                            }
                        }
                        if ($tab_included == 0 && Tools::getValue('id_product') == $value->id_product) {
                            $product_extratabs[$i] = $value;
                        }
                    }

                    //SUPPLIERS
                    if ($value->block_type4 == 1 && $tab_included == 0) {
                        foreach (explode(",", $value->suppliers) as $exm) {
                            foreach ($product_suppliers AS $supplier_key => $supplier) {
                                if ($supplier['id_supplier'] == $exm) {
                                    if ($tab_included == 0) {
                                        $product_extratabs[$i] = $value;
                                        $tab_included = 1;
                                    }
                                }
                            }
                        }
                    }

                    //FEATURES
                    if ($value->feature == 1 && $tab_included == 0) {
                        foreach (explode(",", $value->feature_v) as $exf) {
                            foreach ($product_features AS $feat_key => $feat) {
                                if ($feat['id_feature_value'] == $exf) {
                                    if ($tab_included == 0) {
                                        $product_extratabs[$i] = $value;
                                        $tab_included = 1;
                                    }
                                }
                            }
                        }
                    }

                    if (is_array($product_extratabs)) {
                        if (count($product_extratabs) > 0) {
                            foreach ($product_extratabs as $tab => $params) {
                                $check_extra_contents = Extratabproextracontents::getByProductAndTabId(Tools::getValue('id_product'), $params->id_tab);
                                if (isset($check_extra_contents[0]['id_extracontents'])) {
                                    $extracontents = new Extratabproextracontents($check_extra_contents[0]['id_extracontents']);
                                    $product_extratabs[$tab]->body = $extracontents->body;
                                }
                            }
                        }
                    }

                }
                $this->context->smarty->assign(array(
                    'id_shop' => $this->context->shop->id,
                    'product_extratabs' => $product_extratabs,
                    'employee_idlang' => $this->context->cookie->id_lang,
                    'thismodule' => $this,
                    'languages' => $this->context->controller->getLanguages()
                ));
            } else {
                $this->context->smarty->assign(array(
                    'id_shop' => $this->context->shop->id,
                    'employee_idlang' => $this->context->cookie->id_lang,
                    'thismodule' => $this,
                    'languages' => $this->context->controller->getLanguages()
                ));
            }
        } else {
            $this->context->smarty->assign(array(
                'id_shop' => $this->context->shop->id,
                'employee_idlang' => $this->context->cookie->id_lang,
                'thismodule' => $this,
                'languages' => $this->context->controller->getLanguages()
            ));
        }
        $link = new Link();
        $this->context->smarty->assign('link', $link);
        $this->context->smarty->assign('bolink', strtok($_SERVER["REQUEST_URI"], '?'));
        return $this->display(__FILE__, 'views/admin/tabs17.tpl');
    }

    public static function getSelectedFeaturesDiv($block = null)
    {
        $feature_v = '';
        if ($block != null) {
            foreach (explode(',', $block) AS $key) {
                $feature_value = FeatureValue::getFeatureValueLang($key);
                $feature_v .= "<div><input type='hidden' name='etab_feature_v[]' value=" . $key . "> " . $feature_value[0]['value'] . " <span class=\"remove\" onclick=\"$(this).parent().remove();\"></span> </div>";
            }
            return $feature_v;
        }
        return;
    }

    public function replaceVariables($body)
    {
        $product = new Product(Tools::getValue('id_product'), false, $this->context->cookie->id_lang);
        $features = Product::getFrontFeaturesStatic($this->context->language->id, Tools::getValue('id_product'));
        $features_to_replace = "";
        foreach ($features as $key => $value) {
            $features_to_replace .= "<tr><td>" . $value['name'] . "</td><td>" . $value['value'] . "</td></tr>";
        }

        if ($product->id_supplier > 0) {
            $supplier = new Supplier($product->id_supplier, $this->context->language->id);
            $body = str_replace('{supplier_name}', $supplier->name, $body);
            $body = str_replace('{supplier_description}', $supplier->description, $body);
        }

        if ($product->id_manufacturer > 0) {
            $manufacturer = new Manufacturer($product->id_manufacturer, $this->context->language->id);
            $body = str_replace('{manufacturer_name}', $manufacturer->name, $body);
            $body = str_replace('{manufacturer_description}', $manufacturer->description, $body);
            $body = str_replace('{manufacturer_description_short}', $manufacturer->short_description, $body);
        }

        $category = new Category($product->id_category_default, $this->context->language->id);

        $body = str_replace('{main_category}', $category->name, $body);
        $body = str_replace('{ean}', $product->ean13, $body);
        $body = str_replace('{upc}', $product->upc, $body);
        $body = str_replace('{quantity}', $product->quantity, $body);
        $body = str_replace('{reference}', $product->reference, $body);
        $body = str_replace('{id}', $product->id, $body);

        $body = str_replace('{name}', $product->name, $body);
        $body = str_replace('{description}', $product->description, $body);
        $body = str_replace('{short description}', $product->description_short, $body);
        $body = str_replace('{features}', "<table class='table table-striped'>" . $features_to_replace . "</table>", $body);
        preg_match_all("/\{[^\}]*\}/", $body, $matches);
        foreach ($matches[0] as $match) {
            $string = explode("|", str_replace(array(
                '{',
                '}'
            ), array(
                '',
                ''
            ), $match));
            if (count($string) > 1) {
                $variants = count($string);
                $variants--;
                $body = str_replace($match, $string[rand(0, $variants)], $body);
            }
            unset($string);
        }

        /** HOOK RUN **/
        preg_match_all('/\{HOOK\:[(A-Za-z0-9\_)]+\:[(A-Za-z0-9\_)]+\}/i', $body, $matches);
        foreach ($matches[0] as $index => $match) {
            $explode = explode(":", $match);
            $body = str_replace($match, strtolower(Hook::exec(str_replace("}", "", $explode[1]), array(), Module::getModuleIdByName(str_replace("}", "", $explode[2])))), $body);
        }

        return $body;
    }

    public function returnTabsFromDb()
    {
        $extratabs = new Extratabpro();
        global $cookie;

        $blocks_prepare = extratabpro::loadByIdProductActive(Tools::getValue('id_product'));
        $product_categories = Product::getProductCategories(Tools::getValue('id_product'));
        $product_suppliers = $this->getSuppliers(Tools::getValue('id_product'));
        $product_features = Product::getFeaturesStatic(Tools::getValue('id_product'));
        $thisproduct = new Product(Tools::getValue('id_product'));
        $customer_groups = $this->getCustomerGroups();
        $product_manufacturer = $thisproduct->id_manufacturer;
        if (is_array($blocks_prepare)) {
            if (count($blocks_prepare) > 0) {
                foreach ($blocks_prepare as $key => $value) {
                    $product_has_category = 0;
                    $product_has_product = 0;
                    $product_has_manufacturer = 0;
                    $product_has_supplier = 0;
                    $product_has_feature = 0;

                    $tab_included = 0;
                    $tab_included2 = 0;
                    $i = $value->id_tab;

                    if ($value->cms == 1) {
                        $contents = CMS::getCMSContent($value->cms_body, $this->context->language->id, $this->context->shop->id);
                        //$value->body[$this->context->language->id] = $contents['content'];
                        $value->cms_body_show[$this->context->language->id] = $contents['content'];
                    }


                    /** STOCK CHECK **/
                    $stock_quantity = StockAvailable::getQuantityAvailableByProduct(Tools::getValue('id_product'));

                    if ($value->stock == 1) {
                        if ($stock_quantity > 0) {

                        } else {
                            continue;
                        }
                    } elseif ($value->stock == 2) {
                        if ($stock_quantity <= 0) {

                        } else {
                            continue;
                        }
                    }




                    /** GLOBAL RULES - FILTERS NB 1 **/

                    //TABS EVERYWHERE
                    if ($value->everywhere == 1 && $tab_included == 0) {
                        $product_extratabs[$i] = $value;
                        $tab_included = 1;
                    }

                    //TAB IN PRODUCT
                    //if ($tab_included == 0 && Tools::getValue('id_product') == $value->id_product)
                    //{
                    //    $product_extratabs[$i] = $value;
                    //}


                    //TABS BASED ON CATEGORIES
                    if ($value->block_type == 2 && ($tab_included == 0 || ($value->allConditions == 1))) {
                        $product_has_category = 0;
                        foreach (explode(",", $value->categories) as $exk) {
                            foreach ($product_categories as $pk => $pv) {
                                if ($pv == $exk) {
                                    $product_has_category = 1;
                                    if ($tab_included == 0) {
                                        $product_extratabs[$i] = $value;
                                        $tab_included = 1;
                                    }
                                }
                            }
                        }
                        if ($product_has_category == 0) {
                            if ($value->allConditions == 1) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    }

                    //TABS FOR PRODUCTS
                    if ($value->block_type3 == 1 && ($tab_included == 0 || $value->allConditions == 1)) {
                        $product_has_product = 0;
                        foreach (explode(",", $value->products) as $exp) {
                            if (Tools::getValue('id_product') == $exp) {
                                $product_has_product = 1;
                                if ($tab_included == 0) {
                                    $product_extratabs[$i] = $value;
                                    $tab_included = 1;
                                }
                            }
                        }
                        if ($product_has_product == 0) {
                            if ($value->allConditions == 1) {
                                unset($product_extratabs[$i]);
                            }
                        }
                        if ($tab_included == 0 && Tools::getValue('id_product') == $value->id_product) {
                            $product_extratabs[$i] = $value;
                        }
                    }

                    //TABS FOR MANUFACTURERS
                    if ($value->block_type2 == 1 && ($tab_included == 0 || $value->allConditions == 1)) {
                        $product_has_manufacturer = 0;
                        foreach (explode(",", $value->manufacturers) as $exm) {
                            if ($product_manufacturer == $exm) {
                                $product_has_manufacturer = 1;
                                if ($tab_included == 0) {
                                    $product_extratabs[$i] = $value;
                                    $tab_included = 1;
                                }
                            }
                        }
                        if ($product_has_manufacturer == 0) {
                            if ($value->allConditions == 1) {
                                unset($product_extratabs[$i]);
                            }
                        }
                        if ($tab_included == 0 && Tools::getValue('id_product') == $value->id_product) {
                            $product_extratabs[$i] = $value;
                        }
                    }

                    //SUPPLIERS
                    if ($value->block_type4 == 1 && ($tab_included == 0 || $value->allConditions == 1)) {
                        $product_has_supplier = 0;
                        foreach (explode(",", $value->suppliers) as $exm) {
                            foreach ($product_suppliers AS $supplier_key => $supplier) {
                                if ($supplier['id_supplier'] == $exm) {
                                    $product_has_supplier = 1;
                                    if ($tab_included == 0) {
                                        $product_extratabs[$i] = $value;
                                        $tab_included = 1;
                                    }
                                }
                            }
                        }

                        if ($product_has_supplier == 0) {
                            if ($value->allConditions == 1) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    }

                    //FEATURES
                    if ($value->feature == 1 && ($tab_included == 0 || $value->allConditions == 1)) {
                        $product_has_feature = 0;
                        foreach (explode(",", $value->feature_v) as $exf) {
                            foreach ($product_features AS $feat_key => $feat) {
                                if ($feat['id_feature_value'] == $exf) {
                                    $product_has_feature = 1;
                                    if ($tab_included == 0) {
                                        $product_extratabs[$i] = $value;
                                        $tab_included = 1;
                                    }
                                }
                            }
                        }

                        if ($product_has_feature == 0) {
                            if ($value->allConditions == 1) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    }

                    if (
                        ($value->block_type == 2 && $value->allConditions == 1 && $product_has_category == 0) ||
                        ($value->block_type3 == 1 && $value->allConditions == 1 && $product_has_product == 0) ||
                        ($value->block_type2 == 1 && $value->allConditions == 1 && $product_has_manufacturer == 0) ||
                        ($value->block_type4 == 1 && $value->allConditions == 1 && $product_has_supplier == 0) ||
                        ($value->feature == 1 && $value->allConditions == 1 && $product_has_feature == 0)
                    ) {
                        $tab_included = 0;
                        unset($product_extratabs[$i]);
                    }


                    /** GLOBAL RULES - FILTERS NB 2 **/
                    //GROUPS
                    $tab_included2 = 1;
                    if ($value->for_groups == 1 && $tab_included == 1) {
                        $tab_included2 = 0;
                        foreach (explode(",", $value->groups) as $exg) {
                            foreach ($customer_groups AS $group => $val) {
                                if ($group == $exg) {
                                    $tab_included2 = 1;
                                }
                            }
                        }
                        if ($tab_included2 == 0) {
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        } else {
                            $product_extratabs[$i] = $value;
                        }
                    }

                    //TIME FROM
                    if ($value->tf == 1 && $tab_included == 1) {
                        $time_now = str_replace(":", "", date("H:i:s"));
                        $time_from = str_replace(":", "", $value->time_from);
                        if ((int)$time_from <= (int)$time_now) {
                            $time_from_ver = 1;
                        } else {
                            $time_from_ver = 0;
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    } else {
                        $time_from_ver = 1;
                    }

                    //TIME TO
                    if ($value->tt == 1 && $tab_included == 1) {
                        $time_now = ltrim(str_replace(":", "", date("H:i:s")), 0);
                        $time_to = ltrim(str_replace(":", "", $value->time_to), 0);

                        if ((int)$time_to >= (int)$time_now) {
                            $time_to_ver = 1;
                        } else {
                            $time_to_ver = 0;
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    } else {
                        $time_to_ver = 1;
                    }

                    //DATE FROM
                    if ($value->df == 1 && $tab_included == 1) {
                        $date_now = strtotime(date("Y-m-d"));
                        $date_from = strtotime($value->date_from);
                        if ($date_from <= $date_now) {
                            $date_from_ver = 1;
                        } else {
                            $date_from_ver = 0;
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    } else {
                        $date_from_ver = 1;
                    }

                    //DATE TO
                    if ($value->dt == 1 && $tab_included == 1) {
                        $date_now = strtotime(date("Y-m-d"));
                        $date_to = strtotime($value->date_to);
                        if ($date_to >= $date_now) {
                            $date_to_ver = 1;
                        } else {
                            $date_to_ver = 0;
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    } else {
                        $date_to_ver = 1;
                    }

                    if ((($date_to_ver == 0 && $value->dt == 1) || ($date_from_ver == 0 && $value->df == 1) || ($time_to_ver == 0 && $value->tt == 1) || ($time_from_ver == 0 && $value->tf == 1)) || $tab_included2 == 0) {
                        if (isset($product_extratabs[$i])) {
                            unset($product_extratabs[$i]);
                        }
                    } elseif ($tab_included == 1) {
                        $product_extratabs[$i] = $value;
                    }


                    if ($value->tf == 1 && $value->tt == 1) {
                        if ($time_to_ver == 0 || $time_from_ver == 0) {
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    }

                    if ($value->df == 1 && $value->dt == 1) {
                        if ($date_to_ver == 0 || $date_from_ver == 0) {
                            if (isset($product_extratabs[$i])) {
                                unset($product_extratabs[$i]);
                            }
                        }
                    }
                }

                if (isset($product_extratabs)) {
                    foreach ($product_extratabs as $tab => $params) {
                        $check_extra_contents = Extratabproextracontents::getByProductAndTabId(Tools::getValue('id_product'), $params->id_tab);
                        if (isset($check_extra_contents[0]['id_extracontents'])) {
                            $extracontents = new Extratabproextracontents($check_extra_contents[0]['id_extracontents']);
                            $product_extratabs[$tab]->body = $extracontents->body;
                        }
                    }
                }

                if (isset($product_extratabs)) {
                    foreach ($product_extratabs as $tab => $value) {
                        foreach ($product_extratabs[$tab]->body as $lang => $contents) {
                            $product_extratabs[$tab]->body[$lang] = $this->replaceVariables($contents);
                        }
                    }
                }

                if (isset($product_extratabs)) {
                    foreach ($product_extratabs as $tab => $value) {
                        if ($product_extratabs[$tab]->geoip == 1) {
                            if (!in_array($this->returnUserCountry(), explode(',', $product_extratabs[$tab]->selected_geoip))) {
                                unset($product_extratabs[$tab]);
                            }
                        }
                    }
                }
                if (isset($product_extratabs)) {
                    return $product_extratabs;
                }
            }
        }

        return false;
    }

    public function hookdisplayProductTab($params)
    {
        return $this->hookProductTab($params);
    }

    public function hookdisplayProductTabContent($params)
    {
        return $this->hookProductTabContent($params);
    }

    public function hookdisplayProductExtraContent($params)
    {
        if (Tools::getValue('ajax', 'false') == 'false') {
            if (Configuration::get('tabstype') == "17") {
                if (!Cache::isStored('extratabspro::extratab_' . Tools::getValue('id_product', 0))) {
                    $product_extratabs = $this->returnTabsFromDb();
                    $ps17tabz = [];
                    if (isset($product_extratabs)) {
                        if (is_array($product_extratabs)) {
                            if (count($product_extratabs) > 0) {
                                foreach ($product_extratabs as $tab => $value) {
                                    $ps17tabz[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())->setTitle($value->name[$this->context->language->id])->setContent($value->body[$this->context->language->id] . ($value->cms == 1 ? $value->cms_body_show[$this->context->language->id] : ''));
                                }
                                Cache::store('extratabspro::extratab_' . Tools::getValue('id_product', 0), $ps17tabz);
                                return $ps17tabz;
                            }
                        }
                    }
                } else {
                    return Cache::retrieve('extratabspro::extratab' . Tools::getValue('id_product', 0));
                }
            }
        }
        return array();
    }

    public function hookdisplayFooterProduct($params)
    {
        $id_shop = $this->context->shop->id;
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);

        if (Configuration::get('tabstype') == '00') {
            if (!$this->isCached('views/front/footer.tpl', $this->getCacheId('ext_footer' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer))) {
                $product_extratabs = $this->returnTabsFromDb();
                if (isset($product_extratabs)) {
                    if (is_array($product_extratabs)) {
                        if (count($product_extratabs) > 0) {
                            $this->context->smarty->assign(array(
                                'psversion' => $this->psversion(),
                                'id_lang' => $this->context->language->id,
                                'extratabs' => $product_extratabs
                            ));
                        }
                    }
                }
                if ($product_extratabs != false) {
                    if (isset($product_extratabs)) {
                        if (count($product_extratabs) > 0) {
                            return $this->display(__FILE__, 'views/front/footer.tpl', $this->getCacheId('ext_footer' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer));
                        }
                    }
                }
            } else {
                return $this->display(__FILE__, 'views/front/footer.tpl', $this->getCacheId('ext_footer' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer));
            }
        }
    }

    public function hookProductTab($params)
    {
        $id_shop = $this->context->shop->id;
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);

        if (Configuration::get('tabstype') == '15') {
            if (!$this->isCached('views/front/tab.tpl', $this->getCacheId('ext_tab' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer))) {
                $product_extratabs = $this->returnTabsFromDb();
                if (isset($product_extratabs)) {
                    if (is_array($product_extratabs)) {
                        if (count($product_extratabs) > 0) {
                            $this->context->smarty->assign(array(
                                'psversion' => $this->psversion(),
                                'id_lang' => $this->context->language->id,
                                'extratabs' => $product_extratabs
                            ));
                        }
                    }
                }
                if (isset($product_extratabs)) {
                    if (count($product_extratabs) > 0) {
                        return $this->display(__FILE__, 'views/front/tab.tpl', $this->getCacheId('ext_tab' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer));
                    }
                }
            } else {
                return $this->display(__FILE__, 'views/front/tab.tpl', $this->getCacheId('ext_tab' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer));
            }
        }
    }

    public function hookProductTabContent($params)
    {
        $id_shop = $this->context->shop->id;
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);

        if (Configuration::get('tabstype') == '15') {
            if (!$this->isCached('views/front/tabcontents.tpl', $this->getCacheId('ext_tabc' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer))) {

                $product_extratabs = $this->returnTabsFromDb();
                if (isset($product_extratabs)) {
                    if (is_array($product_extratabs)) {
                        if (count($product_extratabs) > 0) {
                            $this->context->smarty->assign(array(
                                'psversion' => $this->psversion(),
                                'id_lang' => $this->context->language->id,
                                'extratabs' => $product_extratabs
                            ));
                        }
                    }
                }
                if (isset($product_extratabs)) {
                    if (count($product_extratabs) > 0) {
                        return $this->display(__FILE__, 'views/front/tabcontents.tpl', $this->getCacheId('ext_tabc' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer));
                    }
                }
            } else {
                return $this->display(__FILE__, 'views/front/tabcontents.tpl', $this->getCacheId('ext_tabc' . Tools::getValue('id_product', 0) . '-' . $id_shop . '-' . $id_customer));
            }
        }
    }

    public function cccc($f)
    {
        $this->_clearCache($f);
    }


    public function getContent()
    {
        $output = "";
        if (Tools::isSubmit('module_settings')) {
            Configuration::updateValue('tabstype', Tools::getValue('tabstype'));
            $this->cccc('*');
        }
        if (Tools::isSubmit('module_settings_support')) {
            Configuration::updateValue('mypresta_support', Tools::getValue('mypresta_support'));
            $this->cccc('*');
        }
        $output .= "";
        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $options = "<option>" . $this->l('-- SELECT --') . "</option>";
        $idlang = (int)Configuration::get('PS_LANG_DEFAULT');

        $form3 = '<div class="bootstrap" style="margin-top:20px; margin:auto;  margin-top:10px;"><div class="alert alert-info">' . $this->l('Alternatively you can display tabs in ProductFooter position. PrestaShop will display there section with tabs from module ') . '</div></div>';

        $returntotal = $this->checkforupdates(0, true) . '<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <div class="panel">
                <h3><i class="icon-wrench"></i> ' . $this->l('MyPresta Support') . '</h3>
                <label>' . $this->l('Disable support chat') . '</label>
                <div class="margin-form">    
                    <input type="checkbox" name="mypresta_support" value="1" ' . (Configuration::get('mypresta_support') == 1 ? 'checked' : '') . '/>
                    <p class="clear">' . $this->l('If you will select this option - you will disable "help" button to contact with support') . '</p>
                </div>
                <div class="clearfix"></div>
                <div class="panel-footer">
                    <button class="button btn btn-default pull-right"><i class="process-icon-save"></i>' . $this->l('save') . '</button>
	               <input type="hidden" name="module_settings_support" class="button" value="1">
	            </div> 
            </div>           
        </form>';

        return (Configuration::get('mypresta_support') == 1 ? "" : "<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement(\"iframe\");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src=\"javascript:false\",r.title=\"\",r.role=\"presentation\",(r.frameElement||r).style.cssText=\"display: none\",d=document.getElementsByTagName(\"script\"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain=\"'+n+'\";void(0);',o=s}o.open()._l=function(){var o=this.createElement(\"script\");n&&(this.domain=n),o.id=\"js-iframe-async\",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload=\"document._l();\">'),o.close()}(\"//assets.zendesk.com/embeddable_framework/main.js\",\"prestasupport.zendesk.com\");/*]]>*/</script>") . '
		<form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <div class="panel">
                <h3><i class="icon-wrench"></i> ' . $this->l('Select type of tabs') . '</h3>
                <div style="display:block; margin:auto; overflow:hidden; width:100%; vertical-align:top;" class="bootstrap">
                    <style>
                        .tabstype {text-align:center;}
                        .tabstype img {
                            padding:6px!important; background:#FFF; border:1px solid #cecece;
                        }
                    </style>
                    <div class="tabstype">
                        <div class="col-lg-4">
                            <div class="col-lg-10">
                                <img class="img-responsive" style="cursor:pointer; width:100%; height:auto;" src="../modules/extratabspro/img/17.gif" onclick="$(\'.ps17view\').attr(\'checked\',true);"/>
                                ' . '<div class="bootstrap" style="margin-top:20px; margin:auto;  margin-top:10px;"><div class="alert alert-info">' . $this->l('Select this option if you want to use standard method to create tabs in PrestaShop 1.7') . '</div></div>' . '
                                ' . $this->l('Default tabs in PrestaShop 1.7 block') . ' ' . $this->l('or PrestaShop 8.0 block') . '<br/><input type="radio" name="tabstype" class="ps17view" value="17" ' . (Configuration::get('tabstype') == 17 ? 'checked="yes"' : '') . '>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="col-lg-10">
                                <img class="img-responsive" style="cursor:pointer; width:100%; height:auto;"" src="../modules/extratabspro/img/15.gif" onclick="$(\'.ps15view\').attr(\'checked\',true);"/>
                                ' . '<div class="bootstrap" style="margin-top:20px; margin:auto;  margin-top:10px;"><div class="alert alert-info">' . $this->l('Select this method if you want to use old hooks (available in PrestaShop 1.6 / 1.5 / 1.4). Some of themes can still use this method. ') . ' <u><a href="https://mypresta.eu/prestashop-17/product-page-tabs.html" target="_blank">' . $this->l('Read how to use old hooks in theme') . '</a></u></div></div>' . '
                                ' . $this->l('Old tabs (required old hooks)') . '<br/><input type="radio" name="tabstype" class="ps15view" value="15" ' . (Configuration::get('tabstype') == 15 ? 'checked="yes"' : '') . '>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="col-lg-10">
                                <img class="img-responsive" style="cursor:pointer; width:100%; height:auto;"" src="../modules/extratabspro/img/00.gif" onclick="$(\'.ps00view\').attr(\'checked\',true);"/>
                                ' . $form3 . '
                                ' . $this->l('Custom tabs - internal module tabs engine') . ' <br/><input type="radio" name="tabstype" class="ps00view" value="00" ' . (Configuration::get('tabstype') == 00 ? 'checked="yes"' : '') . '></td>
                           </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>                          
                </div>
                <div class="panel-footer">
                    <button class="button btn btn-default pull-right"><i class="process-icon-save"></i>' . $this->l('save') . '</button>
                    <input type="hidden" name="module_settings" class="button" value="1">
                </div>
            </div>
		</form>' . $returntotal . $this->inconsistency();

    }

    public function displayFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
    {
        if (count($languages) == 1) {
            return false;
        }

        $language = new Language($default_language);

        $output = '
        <button type="button" class="btn btn-default dropdown-toggle" onclick="toggleLanguageFlags(this);" alt="" tabindex="-1" data-toggle="dropdown"/>' . $language->iso_code . '<i class="icon-caret-down"></i></button>
        <ul class="dropdown-menu">';
        foreach ($languages as $language) {
            if ($use_vars_instead_of_ids) {
                $output .= '<li><a tabindex="-1" onclick="changeMain($(this),"' . trim($language['iso_code']) . '");" href="javascript:changeLanguage(\'' . $id . '\', ' . $ids . ', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" />' . $language['name'] . '</a></li>';
            } else {
                $output .= '<li><a tabindex="-1" onclick="changeMain($(this),\'' . trim($language['iso_code']) . '\');" href="javascript:changeLanguage(\'' . $id . '\', \'' . $ids . '\', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" />' . $language['name'] . '</a></li>';
            }
        }
        $output .= '</ul>';

        if ($return) {
            return $output;
        }
        echo $output;
    }

    public function returnListOfTabs()
    {
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'extratabspro/views/admin/tabs-list.tpl');
    }

}

class extratabsproUpdate extends extratabspro
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
        $actual_version = '';
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

?>