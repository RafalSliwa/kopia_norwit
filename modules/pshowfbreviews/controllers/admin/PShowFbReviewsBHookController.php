<?php

/**
 * File from http://PrestaShow.pl
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @authors     PrestaShow.pl <kontakt@prestashow.pl>
 * @copyright   2016 PrestaShow.pl
 * @license     http://PrestaShow.pl/license
 */

require_once dirname(__FILE__) . "/../../config.php";

use Prestashow\PShowFbReviews\Controller\FbReviewsBHookController;

class PShowFbReviewsBHookController extends FbReviewsBHookController
{

    public $default_action = 'list';
    public $select_menu_tab = 'subtab-PShowFbReviewsBHook';

    public function __construct()
    {
        $this->table = 'pshow_fbreviews_hook';
        $this->className = 'PShowFbReviewsBHook';
        $this->lang = false;

        $this->context = Context::getContext();

        parent::__construct();

        $this->controller_displayName = $this->l('Hooks');
        $this->toolbar_title = $this->l('Hooks');

        $this->specificConfirmDelete = $this->l('Delete this hook?');

        $this->tpl_folder = 'reviewshook/';
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
    }

    public function renderList()
    {
        $this->action_displayName = $this->l('Hooks');

        $this->fields_list = array(
            'id_pshow_fbreviews_hook' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'hook_name' => array(
                'title' => $this->l('Hook name')
            )
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function postProcess()
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            try {
                $hook_name = Tools::getValue('hook_name');

                // if ID exists action == update
                if ($this->id_object)
                    PShowFbReviewsBHook::unregisterHook('pshowfbreviews', $hook_name, $id_shop);

                $presta_id_hook = PShowFbReviewsBHook::registerHook('pshowfbreviews', $hook_name, $id_shop);

                // save $presta_id_hook for process
                $_POST['presta_id_hook'] = (int)$presta_id_hook;

                $object = parent::postProcess();

                $languages = Language::getLanguages(true);
                $obj = $this->loadObject(true);

                $obj->hook_name = $hook_name;
                $obj->presta_id_hook = (int)$presta_id_hook;
                $obj->update();

            } catch (PrestaShopException $e) {
                $e->displayMessage();
                $this->alerts[] = array('warning', $this->l('Hook add error, try again'));
            }
        }

        if ($this->action == 'delete') {
            $id_hook = Tools::getValue('id_pshow_fbreviews_hook');
            $hook = new PShowFbReviewsBHook($id_hook);

            PShowFbReviewsBHook::unregisterHook('pshowfbreviews', $hook->hook_name, $id_shop);

            parent::postProcess();
        }
    }

    public function renderForm()
    {
        $this->action_displayName = $this->l('Edit hook');

        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        $active_input_type = 'switch';

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $active_input_type = 'radio';
        }

        $hooks = Hook::getHooks();
        $hooks = $this->prepareHooksList($hooks);

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Hook')
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Hook'),
                    'name' => 'hook_name',
                    'required' => true,
                    'options' => array(
                        'query' => $hooks,
                        'id' => 'name',
                        'name' => 'name'
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        $this->fields_value = $this->getConfiguration();

        return parent::renderForm();
    }

    public function getConfiguration()
    {
        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;
        $fields = array();

        return $fields;
    }

    public function prepareHooksList($hooks)
    {
        foreach ($hooks as $key => $value) {
            if ((stripos($value['name'], 'action') !== false)
                || (stripos($value['name'], 'admin') !== false)
                || (stripos($value['name'], 'backoffice') !== false)
                || (stripos($value['name'], 'dashboard') !== false)) {
                unset($hooks[$key]);
            }
        }

        return $hooks;
    }

    // Temporary bypass functions
    public function listHelperAction()
    {
    }

    public function addHelperAction()
    {
    }

    public function newHelperAction()
    {
    }

    public function editHelperAction()
    {
    }
}
