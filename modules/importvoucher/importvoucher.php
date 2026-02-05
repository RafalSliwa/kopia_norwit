<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @version   of the vouchers engine: 9.8
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class importvoucher extends Module
{
    function __construct()
    {
        $this->name = 'importvoucher';
        $this->tab = 'advertising_marketing';
        $this->version = '2.9.4';
        $this->author = 'MyPresta.eu';
        $this->tab = 'advertising_marketing';
        $this->mypresta_link = 'https://mypresta.eu/modules/advertising-and-marketing/import-voucher-from-csv.html';
        $this->dir = '/modules/importvoucher/';
        $this->bootstrap = 1;
        $this->psver = $this->psversion();
        $this->module_key = 'ac3247a4165f0f931fb216faeb130ea4';
        parent::__construct();
        $this->checkforupdates();

        $this->displayName = $this->l('Import Vouchers from CSV');
        $this->description = $this->l('This module imports voucher codes from CSV files. Addon has advanced vouchers configuration tool.');

        //voucher engine fields to translate - for translation purposes
        $this->l('Tax excluded');
        $this->l('Tax included');
        $this->l('Shipping excluded');
        $this->l('Shipping included');
        $this->l('Enabled');
        $this->l('Disabled');
        $this->l('Percent(%)');
        $this->l('Amount');
        $this->l('None');
        $this->l('Value');
        $this->l('Amount');
        $this->l('Order (without shipping)');
        $this->l('Specific product');
        $this->l('Product ID');
        $this->l('enter product ID number');
        $this->l('how to get product id?');
        $this->l('Select categories from list above, use CTRL+click to select multiple categories, CTRL+A to select all of them');
        $this->l('Select products from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        $this->l('General settings');
        $this->l('Name');
        $this->l('This will be displayed in the cart summary, as well as on the invoice');
        $this->l('Description');
        $this->l('For your eyes only. This will never be displayed to the customer');
        $this->l('Voucher length');
        $this->l('How many characters will be used to generate voucher code');
        $this->l('Enable sufix');
        $this->l('Turn this option on if you want to enable sufix for your voucher code. It will be added AFTER generated code like CODE_sufix.');
        $this->l('Sufix');
        $this->l('Define sufix for your voucher code');
        $this->l('Enable prefix');
        $this->l('Turn this option on if you want to enable prefix for your voucher code. It will be added BEFORE generated code like prefix_CODE.');
        $this->l('Prefix');
        $this->l('Define prefix for your voucher code');
        $this->l('Highlight');
        $this->l('If the voucher is not yet in the cart, it will be displayed in the cart summary.');
        $this->l('Partial use');
        $this->l('Only applicable if the voucher value is greater than the cart total. If you do not allow partial use, the voucher value will be lowered to the total order amount. If you allow partial use, however, a new voucher will be created with the remainder.');
        $this->l('Priority');
        $this->l('Cart rules are applied by priority. A cart rule with a priority of "1" will be processed before a cart rule with a priority of "2".');
        $this->l('Active');
        $this->l('Conditions');
        $this->l('Expiration time');
        $this->l('Define how long (in days) voucher code will be active');
        $this->l('Minimum amount');
        $this->l('You can choose a minimum amount for the cart either with or without the taxes and shipping.');
        $this->l('Total available');
        $this->l('The cart rule will be applied to the first "X" customers only.');
        $this->l('Total available for each user');
        $this->l('A customer will only be able to use the cart rule "X" time(s).');
        $this->l('Add rule concerning categories');
        $this->l('Add rule concerning products');
        $this->l('Actions');
        $this->l('Free shipping');
        $this->l('Apply a discount');
        $this->l('Apply discount to');
        $this->l('Turn this option on if you want dont want to allow to use this code with other voucher codes');
        $this->l('Uncombinable with other codes');
        $this->l('Select manufacturers from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        $this->l('Add rule concerning manufacturers');
        $this->l('Add rule concerning attributes');
        $this->l('Select Attributes from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        $this->l('Cheapest product');
        $this->l('Selected products');
        $this->l('Cumulative with price reductions');
        $this->l('Turn this option on if you want to allow to use this code with price reductions');
        $this->l('Date from');
        $this->l('Date to');
        $this->l('Expiry date, format: YYYY-MM-DD HH:MM:SS');
        $this->l('Start date, format: YYYY-MM-DD HH:MM:SS');
        $this->l('Conditions');
        //2.1
        $this->l('Search for product');
        $this->l('or enter product ID');
        $this->l('product combination ID');
        //2.5
        $this->l('Send free gift');
        //2.7
        $this->l('Add rule concerning carriers');
        $this->l('Select carriers from list above, use CTRL+click to select multiple items CTRL+A to select all of them');
        //3.2
        $this->l('Please fill out each available field - do not leave fields empty. Otherwise module will not generate coupon codes or these codes will not work properly.');
        $this->l('Below you can find links to YouTube videos where you can find more informations about this voucher code configuration tool.');
        $this->l('Please note that settings here are related to one unique voucher code that module will generate.');
        $this->l('Suggested values for fields below: Total available: 1, Total available for each user: 1');
        $this->l('This means that customer that will receive one unique voucher will have possibility to use it during checkout only one time (as long as you will use suggested values)');
        $this->l('Video description of advanced voucher configuration tool');
        $this->l('General settings');
        $this->l('Conditions settings');
        $this->l('Actions settings');
        //3.4
        $this->l('Add rule concerning suppliers');
        $this->l('Select suppliers from list above, use CTRL+click to select multiple products, CTRL+A to select all of them');
        //3.6
        $this->l('Share voucher between shops');
        $this->l('If enabled - voucher will be shared between shops (multistore), if disabled - voucher will be available only in shop where it was generated');
        //4.0
        $this->l('Select groups from list above, use CTRL+click to select multiple items CTRL+A to select all of them');
        $this->l('Add rule concerning groups of customers');
        //4.4
        $this->l('Exclude discounted products');
        $this->l('If enabled, the voucher will not apply to products already on sale.');
        //5.2
        $this->l('Select countries from list above, use CTRL+click to select multiple items CTRL+A to select all of them');
        $this->l('Add rule concerning countries');
        //5.3
        $this->l('How many product(s) matching the following rules (below) cart must contain?');

    }

    public static function inconsistency($return)
    {
        return true;
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 14 //
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
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
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
                        $actual_version = importvoucherUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (importvoucherUpdate::version($this->version) < importvoucherUpdate::version(Configuration::get('updatev_' . $this->name))) {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                }
                if ($display_msg == 1) {
                    if (importvoucherUpdate::version($this->version) < importvoucherUpdate::version(importvoucherUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        // for updates purposes
    }

    function install()
    {
        if (!(parent::install()) or
            !Configuration::updateValue('update_' . $this->name, '0') or
            !Configuration::updateValue('IV_ROW_DELIMITER', '\r') or
            !$this->registerHook('ActionAdminControllerSetMedia') or
            !Configuration::updateValue('IV_COL_DELIMITER', ',')) {
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

    public function customerExists($email, $return_id = false, $ignore_guest = true)
    {
        $email = preg_replace('/[^\@\.\-\_\w\s]+/u', '', $email);

        $result = Db::getInstance()->getValue('
		SELECT id_customer
		FROM ' . _DB_PREFIX_ . 'customer
		WHERE email = \'' . pSQL(trim($email)) . '\'
		' . ($ignore_guest ? ' AND `is_guest` = 0' : ''));
        return ($return_id ? (int)$result : null);
    }

    public function insert_voucher($array_values)
    {
        if (isset($array_values['voucher_code'])) {
            $voucher_code = $array_values['voucher_code'];
        } else {
            $voucher_code = null;
        }

        if (isset($array_values['name'])) {
            $name = $array_values['name'];
        } else {
            $name = null;
        }

        if (isset($array_values['description'])) {
            $description = $array_values['description'];
        } else {
            $description = null;
        }

        if (isset($array_values['value'])) {
            $value = $array_values['value'];
        } else {
            $value = null;
        }

        if (isset($array_values['quantity'])) {
            $quantity = $array_values['quantity'];
        } else {
            $quantity = null;
        }

        if (isset($array_values['quantity_per_user'])) {
            $quantity_per_user = $array_values['quantity_per_user'];
        } else {
            $quantity_per_user = null;
        }

        if (isset($array_values['cumulable'])) {
            if (strtolower($array_values['cumulable']) == "no") {
                $array_values['cumulable'] = 0;
            } elseif (strtolower($array_values['cumulable']) == "yes") {
                $array_values['cumulable'] = 1;
            }
            $cumulable = $array_values['cumulable'];
        } else {
            $cumulable = null;
        }

        if (isset($array_values['date_to'])) {
            $date_to = $array_values['date_to'];
        } else {
            $date_to = null;
        }

        if (isset($array_values['date_from'])) {
            $date_from = $array_values['date_from'];
        } else {
            $date_from = null;
        }

        if (isset($array_values['minimal_basket'])) {
            $minimal_basket = $array_values['minimal_basket'];
        } else {
            $minimal_basket = null;
        }

        if (isset($array_values['currency'])) {
            $currency_id = $array_values['currency'];
        } else {
            $currency_id = null;
        }

        if (isset($array_values['currency_basket'])) {
            $currency_id_basket = $array_values['currency_basket'];
        } else {
            $currency_id_basket = null;
        }

        if (isset($array_values['partial_use'])) {
            if (strtolower($array_values['partial_use']) == "no") {
                $array_values['partial_use'] = 0;
            } elseif (strtolower($array_values['partial_use']) == "yes") {
                $array_values['partial_use'] = 1;
            }
            $partial_use = $array_values['partial_use'];
        } else {
            $partial_use = null;
        }

        if (isset($array_values['id_customer'])) {
            $id_customer = $array_values['id_customer'];
        } else {
            $id_customer = null;
        }

        if (($id_customer == null || $id_customer == '') || Customer::customerIdExistsStatic($id_customer) <= 0) {
            if (isset($array_values['customer_email'])) {
                $id_customer = (Validate::isEmail(trim($array_values['customer_email'])) ? $this->customerExists(trim($array_values['customer_email']), true, false) : null);
            }
        }
        if (isset($array_values['free_shipping'])) {
            if (strtolower($array_values['free_shipping']) == "no") {
                $array_values['free_shipping'] = 0;
            } elseif (strtolower($array_values['free_shipping']) == "yes") {
                $array_values['free_shipping'] = 1;
            }
            $free_shipping = $array_values['free_shipping'];
        } else {
            $free_shipping = null;
        }

        if (isset($array_values['products_condition'])) {
            if (strlen($array_values['products_condition']) > 1) {
                $products_condition = trim($array_values['products_condition']);
                if (is_array($products_condition)) {
                    if (count($products_condition) > 0) {
                        $products_condition = explode(",", $products_condition);
                    } else {
                        $products_condition = false;
                    }
                } else {
                    $products_condition = array($products_condition);
                }
            } else {
                $products_condition = false;
            }
        } else {
            $products_condition = false;
        }

        if (isset($array_values['categories_condition'])) {
            if (strlen($array_values['categories_condition']) > 1) {
                $categories_condition = trim($array_values['categories_condition']);
                if (is_array($categories_condition)) {
                    if (count($categories_condition) > 0) {
                        $categories_condition = explode(",", $categories_condition);
                    } else {
                        $categories_condition = false;
                    }
                } else {
                    $categories_condition = array($categories_condition);
                }
            } else {
                $categories_condition = false;
            }
        } else {
            $categories_condition = false;
        }


        $vn = new importvoucherVoucherEngine('IV');

        if (Tools::getValue('overwrite') == 1) {
            $voucher = $vn->AddVoucherCode('IV', $id_customer, $voucher_code, $name, $description, $value, $quantity, $quantity_per_user, $cumulable, $date_from, $date_to, $minimal_basket, $currency_id_basket, $currency_id, $partial_use, $free_shipping, null, false, $products_condition, $categories_condition);
        } elseif (Tools::getValue('overwrite') == 0) {
            $cartRuleExists = CartRule::getIdByCode($voucher_code);
            if ($cartRuleExists == false) {
                $voucher = $vn->AddVoucherCode('IV', $id_customer, $voucher_code, $name, $description, $value, $quantity, $quantity_per_user, $cumulable, $date_from, $date_to, $minimal_basket, $currency_id_basket, $currency_id, $partial_use, $free_shipping, null, false, $products_condition, $categories_condition);
            } else {
                $voucher = $vn->AddVoucherCode('IV', $id_customer, $voucher_code, $name, $description, $value, $quantity, $quantity_per_user, $cumulable, $date_from, $date_to, $minimal_basket, $currency_id_basket, $currency_id, $partial_use, $free_shipping, null, $cartRuleExists, $products_condition, $categories_condition);
            }
        } else {
            return;
        }


        if ((isset($array_values['customer_email']) && (Tools::getValue('sendmail', 'false') == 1)) || (Tools::getValue('sendmail', 'false') == 2 && $id_customer > 0)) {
            $cartRule = new CartRule(CartRule::getIdByCode($voucher->code));
            $voucher_value = null;
            if ($cartRule->reduction_amount > 0) {
                $voucher_currency = new Currency($cartRule->reduction_currency);
                $voucher_currency_sign = $voucher_currency->sign;
                $voucher_value = $cartRule->reduction_amount . " " . $voucher_currency_sign;
                if ($cartRule->free_shipping == 1) {
                    if ($voucher_value == null) {
                        $voucher_value = $this->l('Free shipping');
                    } else {
                        $voucher_value .= " + " . $this->l('Free shipping');
                    }
                }
            } elseif ($cartRule->reduction_percent > 0) {
                $voucher_value = $cartRule->reduction_percent . "%";
                if ($cartRule->free_shipping == 1) {
                    if ($voucher_value == null) {
                        $voucher_value = $this->l('Free shipping');
                    } else {
                        $voucher_value .= " + " . $this->l('Free shipping');
                    }
                }
            } elseif ($cartRule->free_shipping == 1) {
                if ($voucher_value == null) {
                    $voucher_value = $this->l('Free shipping');
                } else {
                    $voucher_value .= " + " . $this->l('Free shipping');
                }
            }

            $templateVars['{voucher}'] = $voucher->code;
            $templateVars['{voucher_date_from}'] = $cartRule->date_from;
            $templateVars['{voucher_date_to}'] = $cartRule->date_to;
            $templateVars['{voucher_value}'] = $voucher_value;
            $templateVars['{voucher_description}'] = $cartRule->description;

            $id_lang = Context::getContext()->language->id;
            $id_shop = Context::getContext()->shop->id;

            if (Tools::getValue('sendmail', 'false') == 2 && $id_customer > 0) {
                if ($id_customer != null && $id_customer > 0) {
                    $customer = new Customer($id_customer);
                    if (isset($customer->id_lang)) {
                        if ($customer->id_lang > 0) {
                            $id_lang = $customer->id_lang;
                        }
                        $array_values['customer_email'] = $customer->email;
                    }
                }
            }

            if (Validate::isEmail($array_values['customer_email'])) {
                Mail::Send($id_lang, 'voucher-email', Mail::l('Your personalized voucher code', $id_lang), $templateVars, strval($array_values['customer_email']), null, strval(Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop)), strval(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)), null, null, dirname(__FILE__) . '/mails/', false, $id_shop);
            }
        }
    }

    public static function generateVoucherCode($prefix)
    {
        $validCharacters = "ABCDEFGHJKLMNOUPRSTUWQXYZ0123456789";
        $length = Configuration::get($prefix . 'length');
        $last = "";
        $validCharNumber = Tools::strlen($validCharacters);
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $validCharNumber - 1);
            while ($last == $index) {
                $index = mt_rand(0, $validCharNumber - 1);
            }
            $result .= $validCharacters[$index];
            $last = $index;
        }
        return $result;
    }

    public function generateselect($colid)
    {
        $form = '
        <SELECT name="col' . $colid . '" style="font-size:12px; max-width:100px;">
        <option value="skip">' . $this->l('skip column') . '</option>
        <option value="voucher_code">' . $this->l('voucher code') . '</option>
        <option value="name">' . $this->l('voucher name') . '</option>
        <option value="description">' . $this->l('description') . '</option>
        <option value="value">' . $this->l('value of code') . '</option>
        <option value="currency">' . $this->l('currency ID (if voucher pattern is set to amount)') . '</option>
        <option value="quantity">' . $this->l('quantity') . '</option>
        <option value="quantity_per_user">' . $this->l('quantity per user') . '</option>
        <option value="cumulable">' . $this->l('cumulable') . '</option>
        <option value="date_from">' . $this->l('date from') . '</option>
        <option value="date_to">' . $this->l('date to') . '</option>
        <option value="minimal_basket">' . $this->l('minimal basket') . '</option>
        <option value="currency_basket">' . $this->l('currency ID of minimal basket') . '</option>
        <option value="partial_use">' . $this->l('partial use') . '</option>
        <option value="id_customer">' . $this->l('ID Customer') . '</option>
        <option value="customer_email">' . $this->l('Customer E-Mail') . '</option>
        <option value="free_shipping">' . $this->l('Free Shipping') . '</option>
        <option value="products_condition">' . $this->l('Products by ID') . '</option>
        <option value="categories_condition">' . $this->l('Categories by ID') . '</option>
        </SELECT>';
        return $form;
    }

    public function getContent()
    {
        $output = "";

        if (Tools::isSubmit('delete_csv_file')) {
            if (file_exists(".." . $this->dir . $_POST['fcsv'])) {
                unlink(".." . $this->dir . $_POST['fcsv']);
            }
            $output .= " <div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->l('CSV file deleted') . "</div></div>";
        }

        if (Tools::isSubmit('upload_csv')) {
            $plik_tmp = $_FILES['upload_csv']['tmp_name'];
            $plik_nazwa = $_FILES['upload_csv']['name'];
            $plik_rozmiar = $_FILES['upload_csv']['size'];
            if (is_uploaded_file($plik_tmp)) {
                $date = date("Y-m-d-h-i-s");
                if (move_uploaded_file($plik_tmp, '..' . $this->dir . "$date.csv")) {
                }
            }
            $output .= "<div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->l('CSV file uploaded') . "</div></div>";
        }

        if (Tools::isSubmit('delimiters_submit')) {
            Configuration::updateValue('IV_ROW_DELIMITER', "{$_POST['iv_row_delimiter']}");
            Configuration::updateValue('IV_COL_DELIMITER', "{$_POST['iv_col_delimiter']}");
            $output .= "<div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->l('Settings saved') . "</div></div>";
        }

        if (Tools::isSubmit('save_voucher_settings')) {
            importvoucherVoucherEngine::updateVoucher(Tools::getValue('voucherPrefix'), $_POST);
            $output .= "<div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->l('Settings saved') . "</div></div>";
        }

        return $output . $this->displayForm();
    }

    public function getcurrencies()
    {
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $query = "SELECT * FROM `" . _DB_PREFIX_ . "currency`";
        $array = $db->ExecuteS($query);
        return $array;
    }

    public function getCsvFiles()
    {
        $dir = opendir('..' . $this->dir);
        $count = 0;
        while (false !== ($file = readdir($dir))) {
            if (($file == ".") || ($file == "..")) {
            } else {
                if (preg_match('@(.*)\.(csv)@i', $file)) {
                    $filesarray[$count]['name'] = $file;
                    $count++;
                }
            }
        }

        $csvfiles = "";
        if (isset($filesarray)) {
            if (count($filesarray) > 0) {
                foreach ($filesarray as $key => $value) {
                    $this->context->smarty->assign('csv_name', $value['name']);
                    $csvfiles = $csvfiles . $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/csv_file.tpl');
                }
            } else {
                $csvfiles = $this->l('No Files');
            }
        } else {
            $csvfiles = $this->l('No Files');
        }
        return $csvfiles;
    }

    public function displayForm()
    {
        $vn = new importvoucherVoucherEngine('IV');
        $vn->datetype = "date";

        $output = false;
        if (Tools::isSubmit('importcsv')) {
            $output = '';
            $file = file_get_contents(".." . $this->dir . "{$_POST['importfile']}");
            $exp = null;
            if (Configuration::get('IV_ROW_DELIMITER') == '\n') {
                $exp = explode("\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r') {
                $exp = explode("\r", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r\n') {
                $exp = explode("\r\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\n\r') {
                $exp = explode("\r\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == "-") {
                $exp = explode("-", $file);
            }

            $rows = '<table class="table table-bordered" style="width:100%;"><input type="hidden" name="filename" value="' . $_POST['importfile'] . '"/>';
            if (count($exp) > 0) {
                foreach ($exp as $key => $value) {
                    $first = "1";
                    $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                    $rows .= "<tr><td>add</td>";
                    foreach ($exprow as $id => $val) {
                        $rows .= "<td>" . $this->generateselect($id) . "</td>";
                    }
                    $rows .= "</tr>";
                    if ($first == 1) {
                        break;
                    }
                }
            }

            if (count($exp) > 0) {
                foreach ($exp as $key => $value) {
                    if (strlen($value) > 1) {
                        if (Configuration::get('IV_COL_DELIMITER') == "") {
                            $exprow = array();
                            $exprow[] = $exp[$key];
                        } else {
                            $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                        }
                        $rows .= "<tr>";
                        $rows .= "<td><input type=\"checkbox\" checked=\"yes\" value=\"1\" name=\"add$key\"></td>";
                        foreach ($exprow as $id => $val) {
                            $rows .= "<td>$val</td>";
                        }
                        $rows .= "</tr>";
                    }
                }
            }
            $rows .= "</table>";
            $output .= "<div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->l('Loaded to import') . "</div></div>";
            $output .= '
                <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                    <div class="panel clearfix">
                        <h3>' . $this->l('Email with voucher code') . '</h3>
                            <div class="col-md-4">
                            <div class="alert alert-info">
                                ' . $this->l('select this option if you want to send email to customer with information about voucher code.') . ' ' . $this->l('To use this option you have to define "customer E-Mail" field in your CSV file') . ' ' . $this->l(' or ID of customer') . '
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div style="display:block; padding:10px 10px;">
                                <label>' . $this->l('Do you want to send email with voucher?') . '</label>
                                <select name="sendmail">
                                    <option value="0">' . $this->l('Do not send an email') . '</option>
                                    <option value="1">' . $this->l('Send email with code to email (use column with email address in CSV file)') . '</option>
                                    <option value="2">' . $this->l('Send email with code to customer (email of customer based on ID customer from CSV file)') . '</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel clearfix">
                        <h3>' . $this->l('Overwrite settings of existing codes') . '</h3>
                        <div class="col-md-4">
                            <div class="alert alert-info">
                            ' . $this->l('You can decide here what module will do when you will try to import voucher code that already exists in shop database') . '
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div style="display:block; padding:10px 10px;">
                                <label>' . $this->l('What to do?') . '</label>
                                <select name="overwrite">
                                    <option value="0">' . $this->l('Overwrite existing code with new settings') . '</option>
                                    <option value="1">' . $this->l('Create new voucher with the same code') . '</option>
                                    <option value="2">' . $this->l('Do not import code') . '</option>
                                </select>
                            </div>
                        </div>                        
                    </div>
                
                    <div class="alert alert-info">
                        <strong>' . $this->l('Voucher code association with customer - basic rules') . '.<br/></strong>
                        <ul>
                            <li>' . $this->l('If you will define id_customer field in CSV file module will try to associate voucher code with customer account that has defined ID.') . '.</li>
                            <li>' . $this->l('If customer with ID defined in id_customer field will not exist - module will try to associate voucher with customer that uses email addres defined in customer_email field') . '.</li>
                            <li>' . $this->l('If both id_customer and customer_email field will not exist as a customers in your shop it will be unavailable to define such association') . '.</li>
                            <li>' . $this->l('If you expect to send email to customer with voucher code but do not associate imported voucher with customer account registered with usage of email used in CSV file activate option below') . '.</li>
                        </ul>
                        <div style="display:block; padding:10px 10px;">
                            <label>' . $this->l('Do not associate voucher with customer account identified by email') . '</label>
                            <select name="sendmail_do_not_associate" style="max-width:200px;">
                                <option value="0">' . $this->l('No') . '</option>
                                <option value="1">' . $this->l('Yes') . '</option>
                            </select>
                        </div>
                    </div> 
                ';


            $output .= "<div class=\"bootstrap\" style=\"margin-top:20px;\"><div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>" . $this->l('From list of vouchers below please select voucher code field, otherwise module will generate custom code based on default voucher settings you defined.') . "<br/>" . $this->l('If you want (it is not necessary) and if your CSV file contains other informations about code - select them too. If you will not specify other fields here - module will use default settings of code you defined.') . "</div></div>";
            $output .= '<div  class="panel clearfix">
                    <h3>' . $this->l('Preview of CSV file') . '</h3>
                    ' . $rows . '
                    </div>';
            $output .= "            
                    <div class=\"separation\"></div>
                    <div class=\"clearfix\"></div>
                    <div class=\"panel-footer clearfix\">
                        <button class=\"btn btn-default pull-right\" name=\"submit_vouchers\" type=\"submit\">
                            <i class=\"process-icon-save\"></i>" . $this->l('Add vouchers') . "
                        </button>
                    </div>
                    </form>";
        }

        $confirmation = false;
        if (Tools::isSubmit('submit_vouchers')) {
            $file = file_get_contents(".." . $this->dir . "{$_POST['filename']}");
            if (Configuration::get('IV_ROW_DELIMITER') == '\n') {
                $exp = explode("\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r') {
                $exp = explode("\r", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\r\n') {
                $exp = explode("\r\n", $file);
            }
            if (Configuration::get('IV_ROW_DELIMITER') == '\n\r') {
                $exp = explode("\r\n", $file);
            }

            $columns = "";
            foreach ($exp as $key => $value) {
                $first = 1;
                $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                foreach ($exprow as $id => $val) {
                    ${"col" . $id} = $_POST["col" . "$id"];
                    if (!(${"col" . $id} == "skip")) {
                        $columns .= ${"col" . $id} . ",";
                    }
                }
                if ($first == 1) {
                    break;
                }
            }
            $columns = substr($columns, 0, -1);
            foreach ($exp as $key => $value) {
                if (isset($_POST["add" . "$key"])) {
                    $exprow = explode(Configuration::get('IV_COL_DELIMITER'), "$exp[$key]");
                    $values = "";
                    foreach ($exprow as $id => $val) {
                        ${"col" . $id} = $_POST["col" . "$id"];
                        if (!(${"col" . $id} == "skip")) {
                            $values .= "'$val',";
                            $array_values[${"col" . "$id"}] = trim($val);
                        }
                    }
                    $values = substr($values, 0, -1);
                    $this->insert_voucher($array_values);
                }
            }
            $confirmation = true;
        }

        $this->context->smarty->assign(array(
            'output' => $output,
            'confirm' => $confirmation,
            'csvfiles' => $this->getCsvFiles(),
            'voucher_conf' => $vn->generateForm()
        ));

        return $this->display(__FILE__, 'views/backoffice.tpl');
    }


    public function cron()
    {
        $array_values = array();
        $file = file_get_contents('bons.csv');
        $file_lines = explode("\n", $file);
        $count = 0;
        foreach ($file_lines AS $key => $line) {
            $line_exploded = explode(";", $line);
            $count++;
            if ($count < 3) {
                continue;
            }
            $array_values['voucher_code'] = $line_exploded[0];
            $array_values['customer_email'] = $line_exploded[2];
            $array_values['description'] = $line_exploded[3];
            $array_values['value'] = $line_exploded[5];
            $array_values['name'] = $line_exploded[6];




            if (isset($array_values['voucher_code'])) {
                $voucher_code = $array_values['voucher_code'];
            } else {
                $voucher_code = null;
            }

            if (isset($array_values['name'])) {
                $name = $array_values['name'];
            } else {
                $name = null;
            }

            if (isset($array_values['description'])) {
                $description = $array_values['description'];
            } else {
                $description = null;
            }

            if (isset($array_values['value'])) {
                $value = $array_values['value'];
            } else {
                $value = null;
            }

            if (isset($array_values['quantity'])) {
                $quantity = $array_values['quantity'];
            } else {
                $quantity = null;
            }

            if (isset($array_values['quantity_per_user'])) {
                $quantity_per_user = $array_values['quantity_per_user'];
            } else {
                $quantity_per_user = null;
            }

            if (isset($array_values['cumulable'])) {
                if (strtolower($array_values['cumulable']) == "no") {
                    $array_values['cumulable'] = 0;
                } elseif (strtolower($array_values['cumulable']) == "yes") {
                    $array_values['cumulable'] = 1;
                }
                $cumulable = $array_values['cumulable'];
            } else {
                $cumulable = null;
            }

            if (isset($array_values['date_to'])) {
                $date_to = $array_values['date_to'];
            } else {
                $date_to = null;
            }

            if (isset($array_values['date_from'])) {
                $date_from = $array_values['date_from'];
            } else {
                $date_from = null;
            }

            if (isset($array_values['minimal_basket'])) {
                $minimal_basket = $array_values['minimal_basket'];
            } else {
                $minimal_basket = null;
            }

            if (isset($array_values['currency'])) {
                $currency_id = $array_values['currency'];
            } else {
                $currency_id = null;
            }

            if (isset($array_values['currency_basket'])) {
                $currency_id_basket = $array_values['currency_basket'];
            } else {
                $currency_id_basket = null;
            }

            if (isset($array_values['partial_use'])) {
                if (strtolower($array_values['partial_use']) == "no") {
                    $array_values['partial_use'] = 0;
                } elseif (strtolower($array_values['partial_use']) == "yes") {
                    $array_values['partial_use'] = 1;
                }
                $partial_use = $array_values['partial_use'];
            } else {
                $partial_use = null;
            }

            if (isset($array_values['id_customer'])) {
                $id_customer = $array_values['id_customer'];
            } else {
                $id_customer = null;
            }

            if ((($id_customer == null || $id_customer == '') || Customer::customerIdExistsStatic($id_customer) <= 0) && Tools::getValue('sendmail_do_not_associate') == 0) {
                if (isset($array_values['customer_email'])) {
                    $id_customer = (Validate::isEmail(trim($array_values['customer_email'])) ? $this->customerExists(trim($array_values['customer_email']), true, false) : null);
                }
            }
            if (isset($array_values['free_shipping'])) {
                if (strtolower($array_values['free_shipping']) == "no") {
                    $array_values['free_shipping'] = 0;
                } elseif (strtolower($array_values['free_shipping']) == "yes") {
                    $array_values['free_shipping'] = 1;
                }
                $free_shipping = $array_values['free_shipping'];
            } else {
                $free_shipping = null;
            }

            if (isset($array_values['products_condition'])) {
                if (strlen($array_values['products_condition']) > 1) {
                    $products_condition = trim($array_values['products_condition']);
                    if (count($products_condition) > 0) {
                        $products_condition = explode(",", $products_condition);
                    } else {
                        $products_condition = false;
                    }
                } else {
                    $products_condition = false;
                }
            } else {
                $products_condition = false;
            }

            if (isset($array_values['categories_condition'])) {
                if (strlen($array_values['categories_condition']) > 1) {
                    $categories_condition = trim($array_values['categories_condition']);
                    if (count($categories_condition) > 0) {
                        $categories_condition = explode(",", $categories_condition);
                    } else {
                        $categories_condition = false;
                    }
                } else {
                    $categories_condition = false;
                }
            } else {
                $categories_condition = false;
            }


            $vn = new importvoucherVoucherEngine('IV');
            $value = number_format($value, 2, '.', '');


            if (Tools::getValue('overwrite') == 1) {
                $voucher = $vn->AddVoucherCode('IV', $id_customer, $voucher_code, $name, $description, $value, $quantity, $quantity_per_user, $cumulable, $date_from, $date_to, $minimal_basket, $currency_id_basket, $currency_id, $partial_use, $free_shipping, null, false, $products_condition, $categories_condition);
            } elseif (Tools::getValue('overwrite') == 0) {
                $cartRuleExists = CartRule::getIdByCode($voucher_code);
                if ($cartRuleExists == false) {
                    $voucher = $vn->AddVoucherCode('IV', $id_customer, $voucher_code, $name, $description, $value, $quantity, $quantity_per_user, $cumulable, $date_from, $date_to, $minimal_basket, $currency_id_basket, $currency_id, $partial_use, $free_shipping, null, false, $products_condition, $categories_condition);
                    echo $voucher_code . ' added! <br/>';
                } else {
                    $voucher = $vn->AddVoucherCode('IV', $id_customer, $voucher_code, $name, $description, $value, $quantity, $quantity_per_user, $cumulable, $date_from, $date_to, $minimal_basket, $currency_id_basket, $currency_id, $partial_use, $free_shipping, null, $cartRuleExists, $products_condition, $categories_condition);
                }
            } else {
                return;
            }
        }
    }
}

class importvoucherUpdate extends importvoucher
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
        if (isset($actual_version)) {
            Configuration::updateValue("update_" . $module, date("U"));
            Configuration::updateValue("updatev_" . $module, $actual_version);
            return $actual_version;
        }
    }

    public static function inconsistency($ret)
    {
        return parent::inconsistency($ret);
    }
}

if (file_exists(_PS_MODULE_DIR_ . 'importvoucher/lib/voucherengine/engine.php')) {
    require_once _PS_MODULE_DIR_ . 'importvoucher/lib/voucherengine/engine.php';
}

?>