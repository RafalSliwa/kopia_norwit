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
 */
require_once _PS_MODULE_DIR_ . 'seoredirect/seoredirect.php';

if(file_exists(_PS_MODULE_DIR_ . 'seoredirect/lib/searchTool/searchTool.php')) {
    require_once _PS_MODULE_DIR_ . 'seoredirect/lib/searchTool/searchTool.php';
}

class AdminSeoRedirectSettingsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->className = 'Configuration';
        $this->table = 'configuration';
        $this->searchTool = new searchToolseoredirect('seoredirect', 'other');
        parent::__construct();

        $wildcards = "<strong>" . $this->module->getTranslator()->trans('Regular expressions', [], 'Modules.Seoredirect.Settings') . '</strong> ' . $this->module->getTranslator()->trans('You can use regular expressions to define redirections.', [], 'Modules.Seoredirect.Settings') . '</br>';
        $wildcards .= "<strong>" . $this->module->getTranslator()->trans('? (question mark)', [], 'Modules.Seoredirect.Settings') . "</strong> " . $this->module->getTranslator()->trans('this can represent any single character. If you specified something at the command line like "hd?" - script would look for hda, hdb, hdc and every other letter/number between a-z, 0-9.', [], 'Modules.Seoredirect.Settings') . "</br>";
        $wildcards .= "<strong>" . $this->module->getTranslator()->trans('* (asterisk)', [], 'Modules.Seoredirect.Settings') . "</strong> " . $this->module->getTranslator()->trans('this can represent any number of characters (including zero, in other words, zero or more characters). If you specified a "cd*" it would use "cda", "cdrom", "cdrecord" and anything that starts with “cd” also including “cd” itself. "m*l" could by mill, mull, ml, and anything that starts with an m and ends with an l.', [], 'Modules.Seoredirect.Settings') . "</br>";
        $wildcards .= "<strong>" . $this->module->getTranslator()->trans('[ ] (square brackets)', [], 'Modules.Seoredirect.Settings') . "</strong> " . $this->module->getTranslator()->trans('specifies a range. If you did m[a,o,u]m it can become: mam, mum, mom if you did: m[a-d]m it can become anything that starts and ends with m and has any character a to d inbetween. For example, these would work: mam, mbm, mcm, mdm. This kind of wildcard specifies an “or” relationship (you only need one to match).', [], 'Modules.Seoredirect.Settings') . "</br>";
        $wildcards .= "<strong>" . $this->module->getTranslator()->trans('{ } (curly brackets)', [], 'Modules.Seoredirect.Settings') . "</strong> " . $this->module->getTranslator()->trans('terms are separated by commas and each term must be the name of something or a wildcard. This wildcard will copy anything that matches either wildcard(s), or exact name(s) (an “or” relationship, one or the other). For example, this would be valid: {*printed*,*summer*} - this will check urls for "printed" or "summer" words', [], 'Modules.Seoredirect.Settings') . "</br>";
        $wildcards .= "<strong>" . $this->module->getTranslator()->trans('[!] ', [], 'Modules.Seoredirect.Settings') . "</strong> " . $this->module->getTranslator()->trans('This construct is similar to the [ ] construct, except rather than matching any characters inside the brackets, it\'ll match any character, as long as it is not listed between the [ and ]. This is a logical NOT.', [], 'Modules.Seoredirect.Settings') . "</br>";

        if (Tools::getValue('seor_pos_exclude', 'false') != 'false')
        {
            Configuration::updateValue('seor_pos_exclude', implode(',', Tools::getValue('seor_pos_exclude')));
        }

        if (Tools::getValue('categoryBoxDef', 'false') != 'false')
        {
            Configuration::updateValue('seor_unavorder', Tools::getValue('seor_unavorder', 0));
        }

        if (Tools::getValue('seor_unavorder', 'false') != 'false')
        {
            Configuration::updateValue('seor_pos_category', Tools::getValue('categoryBoxDef'));
        }
        
        if (Tools::getValue('categoryBoxDefInStock', 'false') != 'false')
        {
            Configuration::updateValue('seor_pins_category', Tools::getValue('categoryBoxDefInStock'));
        }

        if (Tools::getValue('seor_pos_exclude_cat', 'false') != 'false')
        {
            Configuration::updateValue('seor_pos_exclude_cat', implode(',', Tools::getValue('seor_pos_exclude_cat')));
        }

        if (Tools::getValue('seor_oos_cat_include', 'false') != 'false')
        {
            Configuration::updateValue('seor_oos_cat_include', implode(',', Tools::getValue('seor_oos_cat_include')));
        }

        $fields = array(
            'seor_preview_on' => array(
                'title' => $this->module->getTranslator()->trans('Do not activate module for "preview" mode', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Module can be disabled when you use a "preview" product feature:', [], 'Modules.Seoredirect.Settings') .' <a target="_blank" href="https://i.imgur.com/XTeED1y.png">['.$this->module->getTranslator()->trans('see screenshot', [], 'Modules.Seoredirect.Settings').']</a>',
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('No', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Yes', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_wildcards' => array(
                'title' => $this->module->getTranslator()->trans('Enable wildcards', [], 'Modules.Seoredirect.Settings'),
                'desc' =>$this->module->getTranslator()->trans('Module can work with wildcards, this means that you can generate bulk redirections based on url patterns', [], 'Modules.Seoredirect.Settings') . "<br />" . $wildcards . '<hr/>',
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0'
            ),
            'seor_savestats' => array(
                'title' => $this->module->getTranslator()->trans('Enable statistics', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Enable or disable statistics feature. If enabled - module will store informations about redirected customers.', [], 'Modules.Seoredirect.Settings') . '<hr/>',
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0'
            ),
            'seor_logs' => array(
                'title' => $this->module->getTranslator()->trans('Enable logs', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Option when active will log all redirections initiated by module.', [], 'Modules.Seoredirect.Settings') . '<hr/>',
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0'
            ),
            'seor_emptycat' => array(
                'title' => $this->module->getTranslator()->trans('Empty categories', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select what you want to do with categories that are empty (without products)', [], 'Modules.Seoredirect.Settings') . '<br/>' . $this->cronJobUrlCategories(),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Disable & hide automatically', [], 'Modules.Seoredirect.Settings')
                    ),
                    2 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('Disable and redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    ),
                    3 => array(
                        'value' => 3,
                        'name' => $this->trans('Disable and redirect to parent category (if exists, otherwise redirect to homepage)', [], 'Modules.Seoredirect.Seoredirect')
                    ),
                ),
                'identifier' => 'value'
            ),
            'seor_emptycat_oos' => array(
                'title' => $this->module->getTranslator()->trans('Out of stock categories', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select what you want to do with categories that are out of stock (products are active, but all of these products are out of stock)', [], 'Modules.Seoredirect.Settings') . '<br/>' . $this->cronJobUrlCategories(),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' =>  $this->module->getTranslator()->trans('Disable & hide automatically', [], 'Modules.Seoredirect.Settings'),
                    ),
                    2 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('Disable and redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    ),
                    3 => array(
                        'value' => 3,
                        'name' => $this->module->getTranslator()->trans('Disable and redirect to parent category (if exists, otherwise redirect to homepage)', [], 'Modules.Seoredirect.Settings')
                    ),
                ),
                'identifier' => 'value'
            ),
            'seor_emptycat_redirect_type' => array(
                'title' => $this->module->getTranslator()->trans('Empty categories redirect type', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('If you will enable redirection of empty categories you can define type of redirection', [], 'Modules.Seoredirect.Settings'),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    301 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('301 Permanent redirection', [], 'Modules.Seoredirect.Settings')
                    ),
                    302 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('302 Moved Temporarily', [], 'Modules.Seoredirect.Settings')
                    ),
                    303 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('303 See Other', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_emptycat_products' => array(
                'title' => $this->module->getTranslator()->trans('Exlcude not active products', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('When you want to hide empty categories you can exclude disabled products from function that checks how many products category has', [], 'Modules.Seoredirect.Settings')  . '<hr/>',
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0'
            ),
            'seor_emptycat_exclusions' => array(
                'title' => $this->module->getTranslator()->trans('Exlcude selected categories', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select categories that will be excluded from "Out of stock categories" and "Empty categories" disable/hide features', [], 'Modules.Seoredirect.Settings')  . '<hr/>' . $this->searchTool->searchTool('category', 'seor_emptycat_exclusions', '', true, (Tools::getValue('seor_emptycat_exclusions', 'false') != 'false' ? Tools::getValue('seor_emptycat_exclusions'):Configuration::get('seor_emptycat_exclusions'))) . $this->searchTool->initTool(),
                'type' => 'text',
                'suffix'   => $this->searchTool->searchTool('category', 'seor_emptycat_exclusions', ''),

            ),
            'seor_auto404' => array(
                'title' => $this->module->getTranslator()->trans('Automatic redirect 404 page', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('', [], 'Modules.Seoredirect.Settings') . '<hr/>',
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('-- no --', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    ),
                ),
                'identifier' => 'value'
            ),
            'seor_pins' => array(
                'title' => $this->module->getTranslator()->trans('In stock products', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select what you want to do with products that are in stock', [], 'Modules.Seoredirect.Settings') . '<br/>' . $this->returnCategoriesFormInStock() . '<hr/>',
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Assign to selected category', [], 'Modules.Seoredirect.Settings')
                    ),
                    2 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('Unassign from selected category', [], 'Modules.Seoredirect.Settings')
                    ),
                ),
                'identifier' => 'value'
            ),
                        
            'seor_pos' => array(
                'title' => $this->module->getTranslator()->trans('Out of stock products', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select what you want to do with products that are out of stock', [], 'Modules.Seoredirect.Settings') . '<br/>' . $this->cronJobUrl() . $this->returnCategoriesForm(),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    8 => array(
                        'value' => 8,
                        'name' => $this->module->getTranslator()->trans('Make product unavailable to order', [], 'Modules.Seoredirect.Settings')
                    ),
                    4 => array(
                        'value' => 4,
                        'name' => $this->module->getTranslator()->trans('Unassign from all categories and move to new category', [], 'Modules.Seoredirect.Settings')
                    ),
                    6 => array(
                        'value' => 6,
                        'name' => $this->module->getTranslator()->trans('Assign to selected category', [], 'Modules.Seoredirect.Settings')
                    ),
                    7 => array(
                        'value' => 7,
                        'name' => $this->module->getTranslator()->trans('Unassign from selected category', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->trans('Disable & hide automatically', [], 'Modules.Seoredirect.Seoredirect')
                    ),
                    2 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('Disable and redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    ),
                    3 => array(
                        'value' => 3,
                        'name' => $this->module->getTranslator()->trans('Disable and redirect to main category of product', [], 'Modules.Seoredirect.Settings')
                    ),
                    5 => array(
                        'value' => 5,
                        'name' => $this->trans('Disable visibility in shop\'s catalog', [], 'Modules.Seoredirect.Seoredirect')
                    ),
                ),
                'identifier' => 'value'
            ),
            'seor_oos_cat_include' => array(
                'title' => $this->module->getTranslator()->trans('Limit feature to selected category(ies)', [], 'Modules.Seoredirect.Settings'),
                'type' => 'html',
                'label' => $this->module->getTranslator()->trans('If you want feature to disable out of stock products can work in selected category(ies) only', [], 'Modules.Seoredirect.Settings'),
                'html_content' => $this->displaySearchFieldCategory('seor_oos_cat_include')
            ),
            'seor_pos_exclude' => array(
                'title' => $this->module->getTranslator()->trans('Exclude some products', [], 'Modules.Seoredirect.Settings'),
                'type' => 'html',
                'label' => $this->trans('If you don\'t want to disable / hide or redirect some products you can define exclusions.', [], 'Modules.Seoredirect.Seoredirect'),
                'html_content' => $this->displaySearchFieldProduct('seor_pos_exclude')
            ),
            'seor_pos_exclude_cat' => array(
                'title' => $this->module->getTranslator()->trans('Exclude products from categories', [], 'Modules.Seoredirect.Settings'),
                'type' => 'html',
                'label' => $this->trans('If you don\'t want to disable / hide or redirect products from categories you can define exclusions.', [], 'Modules.Seoredirect.Seoredirect'),
                'html_content' => $this->displaySearchFieldCategory('seor_pos_exclude_cat')
            ),
            'seor_dontato' => array(
                'title' => $this->trans('Do not hide "allowed to order" products', [], 'Modules.Seoredirect.Seoredirect'),
                'desc' => $this->trans('Turn this option if you do not want to hide out of stock products that are marked as "allowed to order when out of stock"', [], 'Modules.Seoredirect.Seoredirect'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'default' => '0'
            ),
            'seor_pos_redirect_type' => array(
                'title' => $this->module->getTranslator()->trans('Out of stock products redirect type', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('If you will enable redirection of out of stock products you can define type of redirection', [], 'Modules.Seoredirect.Settings'),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    301 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('301 Permanent redirection', [], 'Modules.Seoredirect.Settings')
                    ),
                    302 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('302 Moved Temporarily', [], 'Modules.Seoredirect.Settings')
                    ),
                    303 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('303 See Other', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_re' => array(
                'title' => $this->module->getTranslator()->trans('Enable when in stock', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Turn this on if you want to re-enable product if it will be in stock again', [], 'Modules.Seoredirect.Settings') . '<br/>' . '<hr/>',
                'hint' => $this->module->getTranslator()->trans('Option enables product page once someone will try to reach product page.', [], 'Modules.Seoredirect.Settings'),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('No', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Yes', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_dp' => array(
                'title' => $this->module->getTranslator()->trans('Disabled products', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select what you want to do with products that are disabled (not active)', [], 'Modules.Seoredirect.Settings'),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    ),
                    2 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('Redirect to main category of product', [], 'Modules.Seoredirect.Settings')
                    ),
                ),
                'identifier' => 'value'
            ),
            'seor_dp_redirect_type' => array(
                'title' => $this->module->getTranslator()->trans('Disabled products redirect type', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('If you will enable redirection of disabled products pages you can define type of redirection', [], 'Modules.Seoredirect.Settings') . '<hr/>',
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    301 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('301 Permanent redirection', [], 'Modules.Seoredirect.Settings')
                    ),
                    302 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('302 Moved Temporarily', [], 'Modules.Seoredirect.Settings')
                    ),
                    303 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('303 See Other', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_pd' => array(
                'title' => $this->module->getTranslator()->trans('Removed products', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('What to do if someone will try to reach removed product page', [], 'Modules.Seoredirect.Settings'),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_pd_redirect_type' => array(
                'title' => $this->module->getTranslator()->trans('Removed products redirect type', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('If you will enable redirection of removed products pages you can define type of redirection', [], 'Modules.Seoredirect.Settings') . '<br/>' . '<hr/>',
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    301 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('301 Permanent redirection', [], 'Modules.Seoredirect.Settings')
                    ),
                    302 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('302 Moved Temporarily', [], 'Modules.Seoredirect.Settings')
                    ),
                    303 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('303 See Other', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
            'seor_dm' => array(
                'title' => $this->module->getTranslator()->trans('Disabled manufacturers', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('Select what you want to do with manufacturers that are disabled (not active)', [], 'Modules.Seoredirect.Settings'),
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    0 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('Nothing', [], 'Modules.Seoredirect.Settings')
                    ),
                    1 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('Redirect to shop homepage', [], 'Modules.Seoredirect.Settings')
                    ),
                ),
                'identifier' => 'value'
            ),
            'seor_dm_redirect_type' => array(
                'title' => $this->module->getTranslator()->trans('Disabled manufacturer redirect type', [], 'Modules.Seoredirect.Settings'),
                'desc' => $this->module->getTranslator()->trans('If you will enable redirection of disabled manufacturer pages you can define type of redirection', [], 'Modules.Seoredirect.Settings') . '<hr/>',
                'type' => 'select',
                'cast' => 'intval',
                'list' => array(
                    301 => array(
                        'value' => 0,
                        'name' => $this->module->getTranslator()->trans('301 Permanent redirection', [], 'Modules.Seoredirect.Settings')
                    ),
                    302 => array(
                        'value' => 1,
                        'name' => $this->module->getTranslator()->trans('302 Moved Temporarily', [], 'Modules.Seoredirect.Settings')
                    ),
                    303 => array(
                        'value' => 2,
                        'name' => $this->module->getTranslator()->trans('303 See Other', [], 'Modules.Seoredirect.Settings')
                    )
                ),
                'identifier' => 'value'
            ),
        );

        $this->fields_options = array(
            'general' => array(
                'title' => $this->module->getTranslator()->trans('General settings of Seo Redirect module', [], 'Modules.Seoredirect.Settings'),
                'icon' => 'icon-cogs',
                'fields' => $fields,
                'submit' => array('title' => $this->module->getTranslator()->trans('Save', [], 'Modules.Seoredirect.Settings')),
            ),
        );
    }

    public function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1)
        {
            return $exp[1];
        }
        if ($part == 2)
        {
            return $exp[2];
        }
        if ($part == 3)
        {
            return $exp[3];
        }
    }

    public function displaySearchFieldProduct($name)
    {
        $products = false;
        if (Configuration::get('seor_pos_exclude') != false)
        {
            $products_explode = explode(',', Configuration::get('seor_pos_exclude'));
            if (count($products_explode) > 0)
            {
                $products = array();
                foreach ($products_explode AS $product)
                {
                    $new = new Product($product, true, $this->context->language->id);
                    $products[] = $new;
                }
            }
        }
        $this->context->smarty->assign(array(
            'version' => _PS_VERSION_,
            'input_array_name' => $name,
            'id_langg' => $this->context->language->id,
            'linkk' => $this->context->link,
            'products' => $products
        ));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seoredirect/views/admin/adminSearch.tpl');
    }

    public function displaySearchFieldCategory($name)
    {
        $name = trim($name);
        $categories_array = false;
        if (Configuration::get($name) != false)
        {
            $categories_explode = explode(',', Configuration::get($name));
            if (count($categories_explode) > 0)
            {
                $categories_array = array();
                foreach ($categories_explode AS $category)
                {
                    $new = new Category($category, $this->context->language->id);
                    $categories_array[] = $new;
                }
            }
        }
        $this->context->smarty->assign(array(
            'input_array_name' => trim($name),
            'id_langg' => $this->context->language->id,
            'linkk' => $this->context->link,
            'currentToken' => Tools::getAdminToken('AdminSeoRedirectSettings' . (int)Tab::getIdFromClassName('AdminSeoRedirectSettings') . (int)$this->context->employee->id),
            'categories_array' => $categories_array

        ));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seoredirect/views/admin/adminSearchCategory.tpl');
    }

    public function getPathUrIThis()
    {
        $module = Module::getInstanceByName('seoredirect');
        return $module->getPathUri();
    }

    public function setMedia($var = false)
    {

        parent::setMedia($var);
        if ($this->psversion() != 7) {
            $this->context->controller->addJs(__PS_BASE_URI__ . $this->admin_webpath . '/themes/' . $this->bo_theme . '/js/tree.js');
        } else {
            $this->context->controller->addJs(__PS_BASE_URI__ . '/modules/seoredirect/views/admin/tree.js');
        }
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryPlugin(array('autocomplete'));
    }

    public function getSecureKey()
    {
        $module = Module::getInstanceByName('seoredirect');
        return $module->secure_key;
    }

    public function returnCategoriesForm()
    {
        $selected_category = new Category(Configuration::get('seor_pos_category'), $this->context->language->id);
        $this->context->smarty->assign(array(
            'category_to_move' => $selected_category,
            'version' => _PS_VERSION_,
            'id_langg' => $this->context->language->id,
            'linkk' => $this->context->link,
            'currentToken' => Tools::getAdminToken('AdminSeoRedirectSettings' . (int)Tab::getIdFromClassName('AdminSeoRedirectSettings') . (int)$this->context->employee->id)
        ));
        $category_def = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seoredirect/views/admin/adminSearchCategoryToMove.tpl');

        return "<div id='seor_pos_category'><br/><div class='alert alert-info alert-info-unavailable-to-order'><strong>".$this->module->getTranslator()->trans('Make product unavailable to order', [], 'Modules.Seoredirect.Settings')."</strong><br/><select class=\"form-control fixed-width-xxl \" name=\"seor_unavorder\" id=\"seor_unavorder\"><option value=\"1\" ".(Configuration::get('seor_unavorder') == 1 ? 'selected':'').">".$this->module->getTranslator()->trans('Active', [], 'Modules.Seoredirect.Settings')."</option><option value=\"0\" ".(Configuration::get('seor_unavorder') != 1 ? 'selected':'').">".$this->module->getTranslator()->trans('Disable', [], 'Modules.Seoredirect.Settings')."</option></select>" . $this->module->getTranslator()->trans('This feature will mark product as product unavailable to order', [], 'Modules.Seoredirect.Settings') . "</div>" . $category_def . "</div>";
    }
    
    public function returnCategoriesFormInStock()
    {
        $selected_category = new Category(Configuration::get('seor_pins_category'), $this->context->language->id);

        $this->context->smarty->assign(array(
            'category_to_move_instock' => $selected_category,
            'id_langg' => $this->context->language->id,
            'currentToken' => Tools::getAdminToken('AdminSeoRedirectSettings' . (int)Tab::getIdFromClassName('AdminSeoRedirectSettings') . (int)$this->context->employee->id),
        ));
        $category_def = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seoredirect/views/admin/adminSearchCategoryToMoveInStock.tpl');
        return "<div id='seor_pins_category'><br/>" . $category_def . "</div>";
    }
    
    public function cronJobUrl()
    {
        $croonurl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUriThis() . 'cronjob.php?key=' . $this->getSecureKey();
        return '<div class="cronJobDiv" style="' . (Configuration::get('seor_pos') == 4 ? 'display:none;' : 'display:block;') . '"><div class="bootstrap" style="margin-top:10px;"><div class="alert alert-warning">' . $this->trans('Module enables and disables out of stock / in stock products when someone will try to access to product page. You can bulk enable / disable them with cron job. Details below', [], 'Modules.Seoredirect.Seoredirect'). '<br /></div><div class="alert alert-info">' . $this->trans('Add this url to your cron job table to disable and/or enable out of stock products automatically', [], 'Modules.Seoredirect.Seoredirect').'<br />' . $croonurl . '</div></div></div>';
    }

    public function cronJobUrlCategories()
    {
        $croonurl = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUriThis() . 'cronjob.php?key=' . $this->getSecureKey();
        return '<div class="cronJobDivCategories" style="' . (Configuration::get('seor_emptycat') != 1 ? 'display:none;' : 'display:block;') . '"><div class="bootstrap" style="margin-top:10px;"><div class="alert alert-warning">' . $this->module->getTranslator()->trans('Module disables categories when someone will try to open category page that has no products. You can also automatically disable empty categories with cron job task. Details below.', [], 'Modules.Seoredirect.Settings') . '<br /></div><div class="alert alert-info">' . $this->module->getTranslator()->trans('Add this url to your cron job table to disable empty categories automatically', [], 'Modules.Seoredirect.Settings') . '<br />' . $croonurl . '</div></div></div>';
    }


    public function beforeUpdateOptions()
    {
        if (isset($_POST['seor_pos_exclude']))
        {
            if (is_array($_POST['seor_pos_exclude']))
            {
                $_POST['seor_pos_exclude'] = implode(',', Tools::getValue('seor_pos_exclude'));
            }
        }
        if (isset($_POST['seor_pos_exclude_cat']))
        {
            if (is_array($_POST['seor_pos_exclude_cat']))
            {
                $_POST['seor_pos_exclude_cat'] = implode(',', Tools::getValue('seor_pos_exclude_cat'));
            }
        }
        if (isset($_POST['seor_oos_cat_include']))
        {
            if (is_array($_POST['seor_oos_cat_include']))
            {
                $_POST['seor_oos_cat_include'] = implode(',', Tools::getValue('seor_oos_cat_include'));
            }
        }
    }

    public static function jsonEncode($data, $options = 0, $depth = 512)
    {
        return json_encode($data, $options, $depth);
    }

    public function ajaxProcess()
    {
        if (Tools::isSubmit('categoriesFilter'))
        {
            $search_query = trim(Tools::getValue('q'));
            $customers = Db::getInstance()->executeS('SELECT c.`id_category`, cl.`name` 
                FROM `' . _DB_PREFIX_ . 'category` c
                LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`
                LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs ON c.`id_category` = cs.`id_category`
                LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON (s.`id_shop` = cs.`id_shop`)
                WHERE cl.`name` like \'%' . $search_query . '%\' AND s.`id_shop` = \'' . $this->context->shop->id . '\'
                GROUP BY c.`id_category`
                ORDER BY c.`id_category` DESC
                LIMIT 50');
            die(self::jsonEncode($customers));
        }
    }

}