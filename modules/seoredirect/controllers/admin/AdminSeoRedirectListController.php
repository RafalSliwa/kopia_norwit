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

class AdminSeoRedirectListController extends ModuleAdminController
{
    protected $position_identifier = 'id_seor';

    public function __construct()
    {
        $this->table = 'seor';
        $this->className = 'seoRedirectList';
        $this->lang = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        parent::__construct();
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->getTranslator()->trans('Delete selected', [], 'Modules.Seoredirect.List'),
                'confirm' => $this->module->getTranslator()->trans('Delete selected items?', [], 'Modules.Seoredirect.List')
            )
        );
        $this->bootstrap = true;
        $this->_orderBy = 'id_seor';
        $this->fields_list = array(
            'id_seor' => array(
                'title' => $this->module->getTranslator()->trans('ID', [], 'Modules.Seoredirect.List'),
                'align' => 'left',
                'orderby' => true,
                'width' => 20
            ),
            'position' => array(
                'title' => $this->module->getTranslator()->trans('Priority', [], 'Modules.Seoredirect.List'),
                'orderby' => true,
            ),
            'old' => array(
                'title' => $this->module->getTranslator()->trans('Old URL', [], 'Modules.Seoredirect.List'),
                'align' => 'left',
                'orderby' => true,
                'callback' => 'checkForDuplicate',
                'width' => 170
            ),
            'new' => array(
                'title' => $this->module->getTranslator()->trans('New URL', [], 'Modules.Seoredirect.List'),
                'align' => 'left',
                'orderby' => true,
                'width' => 170
            ),
            'redirect_type' => array(
                'title' => $this->module->getTranslator()->trans('Type', [], 'Modules.Seoredirect.List'),
                'align' => 'left',
                'orderby' => true,
                'width' => 60
            ),
            'active' => array(
                'title' => $this->module->getTranslator()->trans('Active', [], 'Modules.Seoredirect.List'),
                'width' => 30,
                'orderby' => true,
                'type' => 'bool',
                'active' => 'status',
            ),
            'regexp' => array(
                'title' => $this->module->getTranslator()->trans('Regexp', [], 'Modules.Seoredirect.List'),
                'align' => 'left',
                'orderby' => true,
                'orderby' => false,
                'type' => 'bool',
                'width' => 200
            ),
            'wildcard' => array(
                'title' => $this->module->getTranslator()->trans('Wildcard', [], 'Modules.Seoredirect.List'),
                'align' => 'left',
                'orderby' => true,
                'type' => 'bool',
                'width' => 200
            ),
        );
    }

    public function checkForDuplicate($group, $row)
    {
        return $this->isDuplicated($row['old']).$row['old'];
    }

    public function isDuplicated($old)
    {
        if (count(seoRedirectList::getOld(psql($old)))>1) {
            return '<i class="fa fa-exclamation-triangle">&nbsp;<strong>'.$this->module->getTranslator()->trans('duplicated', [], 'Modules.Seoredirect.List').'</strong></i>  ';
        }
    }


    public function renderList()
    {
        $this->initToolbar();
        return parent::renderList();
    }

    public function init()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive())
        {
            $this->_where = 'AND a.id_shop=' . Context::getContext()->shop->id;
        }
        parent::init();
    }

    public function initToolbar()
    {
        unset($this->toolbar_btn);
        $Link = new Link();
        $this->toolbar_btn['new'] = array(
            'desc' => $this->module->getTranslator()->trans('Add new', [], 'Modules.Seoredirect.List'),
            'href' => $Link->getAdminLink('AdminSeoRedirectList') . '&addseor'
        );
        $this->toolbar_btn['stats'] = array(
            'desc' => $this->module->getTranslator()->trans('Statistics', [], 'Modules.Seoredirect.List'),
            'href' => $Link->getAdminLink('AdminSeoRedirectStats')
        );
        $this->toolbar_btn['import'] = array(
            'desc' => $this->module->getTranslator()->trans('Import redirections', [], 'Modules.Seoredirect.List'),
            'href' => $Link->getAdminLink('AdminSeoRedirectImport')
        );
    }

    public function initFormToolBar()
    {
    }

    public function renderForm()
    {
        if (Configuration::get('seor_wildcards') == 1)
        {
            $wildcards = "<strong>" . $this->module->getTranslator()->trans('Regular expressions', [], 'Modules.Seoredirect.List').'</strong> '.$this->module->getTranslator()->trans('You can use regular expressions to define redirections.', [], 'Modules.Seoredirect.List').'<br />';
            $wildcards .= "<div class='alert alert-info'>".$this->module->getTranslator()->trans('Example of regular expression:')."<br/><br/>
                          <strong>".$this->module->getTranslator()->trans('OLD URL:')."</strong> ".'http\:\/\/domain\.com\/blog/category\/(.*)\/'."<br/>
                          <strong>".$this->module->getTranslator()->trans('NEW URL:')."</strong> http://domain.com/blog/{1}  </div>";
            $wildcards .= "<strong>" . $this->module->getTranslator()->trans('Wildcards', [], 'Modules.Seoredirect.List') . "</strong><br />";
            $wildcards .= "<strong>" . $this->module->getTranslator()->trans('? (question mark)') . "</strong> " . $this->module->getTranslator()->trans('this can represent any single character. If you specified something at the command line like "hd?" - script would look for hda, hdb, hdc and every other letter/number between a-z, 0-9.') . "</br>";
            $wildcards .= "<strong>" . $this->module->getTranslator()->trans('* (asterisk)') . "</strong> " . $this->module->getTranslator()->trans('this can represent any number of characters (including zero, in other words, zero or more characters). If you specified a "cd*" it would use "cda", "cdrom", "cdrecord" and anything that starts with “cd” also including “cd” itself. "m*l" could by mill, mull, ml, and anything that starts with an m and ends with an l.') . "</br>";
            $wildcards .= "<strong>" . $this->module->getTranslator()->trans('[ ] (square brackets)') . "</strong> " . $this->module->getTranslator()->trans('specifies a range. If you did m[a,o,u]m it can become: mam, mum, mom if you did: m[a-d]m it can become anything that starts and ends with m and has any character a to d inbetween. For example, these would work: mam, mbm, mcm, mdm. This kind of wildcard specifies an “or” relationship (you only need one to match).') . "</br>";
            $wildcards .= "<strong>" . $this->module->getTranslator()->trans('{ } (curly brackets)') . "</strong> " . $this->module->getTranslator()->trans('terms are separated by commas and each term must be the name of something or a wildcard. This wildcard will copy anything that matches either wildcard(s), or exact name(s) (an “or” relationship, one or the other). For example, this would be valid: {*printed*,*summer*} - this will check urls for "printed" or "summer" words') . "</br>";
            $wildcards .= "<strong>" . $this->module->getTranslator()->trans('[!] ') . "</strong> " . $this->module->getTranslator()->trans('This construct is similar to the [ ] construct, except rather than matching any characters inside the brackets, it\'ll match any character, as long as it is not listed between the [ and ]. This is a logical NOT.') . "</br>";
        }
        else
        {
            $wildcards = '';
        }
        $module = new seoredirect();
        $this->initFormToolBar();
        if (!$this->loadObject(true))
        {
            return;
        }
        $cover = false;
        $obj = $this->loadObject(true);
        //$obj->old=str_replace("\\","\\\\",$obj->old);
        if (isset($obj->id))
        {
            $this->display = 'edit';
        }
        else
        {
            $this->display = 'add';
        }
        $options = array(
            array(
                'id' => 301,
                'name' => $this->module->getTranslator()->trans('301 Permanent', [], 'Modules.Seoredirect.List')
            ),
            array(
                'id' => 302,
                'name' => $this->module->getTranslator()->trans('302 Temporary', [], 'Modules.Seoredirect.List')
            ),
            array(
                'id' => 303,
                'name' => $this->module->getTranslator()->trans('303 Redirection', [], 'Modules.Seoredirect.List')
            ),
            array(
                'id' => 410,
                'name' => $this->module->getTranslator()->trans('410 Gone', [], 'Modules.Seoredirect.List')
            )
        );

        $options_active = array(
            array(
                'id' => 1,
                'name' => $this->module->getTranslator()->trans('Enabled', [], 'Modules.Seoredirect.List')
            ),
            array(
                'id' => 0,
                'name' => $this->module->getTranslator()->trans('Disabled', [], 'Modules.Seoredirect.List')
            )
        );
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->getTranslator()->trans('New redirection', [], 'Modules.Seoredirect.List'),
            ),
            'input' => array(
                ($module->psversion(1) != 5 ? array(
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Relative path', [], 'Modules.Seoredirect.List'),
                    'name' => 'absolute',
                    'required' => true,
                    'desc' => $this->module->getTranslator()->trans('Use this only if you want to create relative url for old URL. Option removes domain address from url so redirection will work even if you will change the domain of your shop.', [], 'Modules.Seoredirect.List'),
                    'lang' => false,
                    'values' => array(
                        array(
                            'id' => 'ractive_on',
                            'value' => 1,
                            'label' => $this->module->getTranslator()->trans('On', [], 'Modules.Seoredirect.List')
                        ),
                        array(
                            'id' => 'ractive_off',
                            'value' => 0,
                            'label' => $this->module->getTranslator()->trans('Off', [], 'Modules.Seoredirect.List')
                        )
                    ),
                ) : array(
                    'type' => 'select',
                    'label' => $this->module->getTranslator()->trans('Relative path', [], 'Modules.Seoredirect.List'),
                    'name' => 'absolute',
                    'required' => true,
                    'desc' => $this->module->getTranslator()->trans('Use this only if you want to create relative url for old URL. Option removes domain address from url so redirection will work even if you will change the domain of your shop.', [], 'Modules.Seoredirect.List'),
                    'lang' => false,
                    'options' => array(
                        'query' => $options_active,
                        'id' => 'id',
                        'name' => 'name'
                    )
                )),
                array(
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Old URL', [], 'Modules.Seoredirect.List'),
                    'name' => 'old',
                    'desc' => $wildcards,
                    'required' => true,
                    'lang' => false,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('New URL', [], 'Modules.Seoredirect.List'),
                    'name' => 'new',
                    'required' => true,
                    'lang' => false,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->getTranslator()->trans('Type of URL', [], 'Modules.Seoredirect.List'),
                    'name' => 'redirect_type',
                    'required' => true,
                    'lang' => false,
                    'options' => array(
                        'query' => $options,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                ($module->psversion(1) != 5 ? array(
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Active:', [], 'Modules.Seoredirect.List'),
                    'name' => 'active',
                    'required' => true,
                    'lang' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->getTranslator()->trans('On', [], 'Modules.Seoredirect.List')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->getTranslator()->trans('Off', [], 'Modules.Seoredirect.List')
                        )
                    ),
                ) : array(
                    'type' => 'select',
                    'label' => $this->module->getTranslator()->trans('Active', [], 'Modules.Seoredirect.List'),
                    'name' => 'active',
                    'required' => true,
                    'lang' => false,
                    'options' => array(
                        'query' => $options_active,
                        'id' => 'id',
                        'name' => 'name'
                    )
                )),
                ($module->psversion(1) != 5 ? array(
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Regexp', [], 'Modules.Seoredirect.List'),
                    'name' => 'regexp',
                    'required' => true,
                    'desc' => $this->module->getTranslator()->trans('Use this only if you want to create regular expression rediraction and if you know how to use regexp', [], 'Modules.Seoredirect.List') . "<div class='alert alert-warning'>".$this->module->getTranslator()->trans('When you will use this option the "old url" field above must be a regular expression, it is very important to use correct regular expression syntax', [], 'Modules.Seoredirect.List') ."</div>",
                    'lang' => false,
                    'values' => array(
                        array(
                            'id' => 'ractive_on',
                            'value' => 1,
                            'label' => $this->module->getTranslator()->trans('On', [], 'Modules.Seoredirect.List')
                        ),
                        array(
                            'id' => 'ractive_off',
                            'value' => 0,
                            'label' => $this->module->getTranslator()->trans('Off', [], 'Modules.Seoredirect.List')
                        )
                    ),
                ) : array(
                    'type' => 'select',
                    'label' => $this->module->getTranslator()->trans('Regexp', [], 'Modules.Seoredirect.List'),
                    'name' => 'regexp',
                    'required' => true,
                    'desc' => $this->module->getTranslator()->trans('Use this only if you want to create regular expression rediraction and if you know how to use regexp', [], 'Modules.Seoredirect.List'),
                    'lang' => false,
                    'options' => array(
                        'query' => $options_active,
                        'id' => 'id',
                        'name' => 'name'
                    )
                )),
                ($module->psversion(1) != 5 ? array(
                    'type' => 'switch',
                    'label' => $this->module->getTranslator()->trans('Wildcard', [], 'Modules.Seoredirect.List'),
                    'name' => 'wildcard',
                    'required' => true,
                    'desc' => $this->module->getTranslator()->trans('Use this only if you want to create wildcard rediraction and if you know how to use wildcards', [], 'Modules.Seoredirect.List'),
                    'lang' => false,
                    'values' => array(
                        array(
                            'id' => 'ractive_on',
                            'value' => 1,
                            'label' => $this->module->getTranslator()->trans('On', [], 'Modules.Seoredirect.List')
                        ),
                        array(
                            'id' => 'ractive_off',
                            'value' => 0,
                            'label' => $this->module->getTranslator()->trans('Off', [], 'Modules.Seoredirect.List')
                        )
                    ),
                ) : array(
                    'type' => 'select',
                    'label' => $this->module->getTranslator()->trans('Wildcard', [], 'Modules.Seoredirect.List'),
                    'name' => 'wildcard',
                    'required' => true,
                    'desc' => $this->module->getTranslator()->trans('Use this only if you want to create wildcard rediraction and if you know how to use wildcards', [], 'Modules.Seoredirect.List'),
                    'lang' => false,
                    'options' => array(
                        'query' => $options_active,
                        'id' => 'id',
                        'name' => 'name'
                    )
                )),
                array(
                    'type' => 'text',
                    'label' => $this->module->getTranslator()->trans('Execution priority', [], 'Modules.Seoredirect.List'),
                    'name' => 'position',
                    'desc' => $this->module->getTranslator()->trans('Module executes redirections one by one. The higher value you will enter here - the more important redirection is. Type here numbers only.', [], 'Modules.Seoredirect.List'),
                    'lang' => false,
                ),
            ),
            'submit' => array(
                'title' => $this->module->getTranslator()->trans('Save', [], 'Modules.Seoredirect.List')
            )
        );
        return parent::renderForm();
    }

    public function processAdd()
    {
        $object = parent::processAdd();
        return true;
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();
        return true;
    }

    public function postProcess()
    {
        if ((Tools::getValue('new', 'false') != 'false' && Tools::getValue('old', 'false') != 'false') && Tools::getValue('absolute','false') == true)
        {
            $old = trim(str_replace('https://', '', str_replace('http://', '', str_replace($_SERVER['HTTP_HOST'], '', $_POST['old']))));
            $_POST['old'] = $old;
            $_POST['id_shop'] = Context::getContext()->shop->id;
        }
        if ((Tools::getValue('new', 'false') != 'false' && Tools::getValue('old', 'false') != 'false')){
            $_POST['id_shop'] = Context::getContext()->shop->id;
        }
        return parent::postProcess();
    }
}